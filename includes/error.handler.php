<?php
namespace php_error;
use \php_error\FileLinesSet,
    \php_error\ErrorHandler,

    \php_error\JSMin,
    \php_error\JSMinException;

use \Closure,
    \Exception,
    \ErrorException,
    \InvalidArgumentException;

use \ReflectionMethod,
    \ReflectionFunction,
    \ReflectionParameter;

global $_php_error_already_setup, $_php_error_global_handler, $_php_error_is_ini_enabled;

if ( empty( $_php_error_already_setup ) ) {
  $_php_error_already_setup = true;
  $missingIdentifier  = array( 'T_INSTEADOF', 'T_TRAIT', 'T_TRAIT_C', 'T_YIELD', 'T_FINALLY' );

  $counter = 100001;
  foreach ( $missingIdentifier as $id ) {
    if ( !defined( $id ) ) {
      define( $id, $counter++ );
    }
  }
  
  if ( !isset( $_php_error_global_handler ) ) {
    $_php_error_global_handler  = null;
    $_php_error_is_ini_enabled  = ( !@get_cfg_var( "php_error.force_disabled" ) && !@get_cfg_var( "php_error.force_disable"  ) && @ini_get( "display_errors" ) === "1" && PHP_SAPI !== 'cli' );
  }

  function withoutErrors( $callback ) {
    global $_php_error_global_handler;
    if ( $_php_error_global_handler !== null ) {
      return $_php_error_global_handler->withoutErrors( $callback );
    }
    else {
      return $callback();
    }
  }

  function reportErrors( $options = null ) {
    $handler  = new ErrorHandler( $options );
    return $handler->turnOn();
  }

  class ErrorHandler {
    const REGEX_DOCTYPE = '/<( )*!( *)DOCTYPE([^>]+)>/';
    const REGEX_PHP_IDENTIFIER  = '\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
    const REGEX_PHP_CONST_IDENTIFIER  = '/\b[A-Z_\x7f-\xff][A-Z0-9_\x7f-\xff]*/';

    const REGEX_METHOD_OR_FUNCTION_END  = '/(\\{closure\\})|(((\\\\)?(\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*)?\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(::[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)?)\\(\\)$/';
    const REGEX_METHOD_OR_FUNCTION  = '/(\\{closure\\})|(((\\\\)?(\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*)?\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(::[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)?)\\(\\)/';

    const REGEX_VARIABLE  = '/\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/';

    const REGEX_MISSING_SEMI_COLON_FOLLOWING_LINE = '/^ *(return|}|if|while|foreach|for|switch)/';

    const NUM_FILE_LINES  = 13;
    const FILE_TYPE_APPLICATION = 1;
    const FILE_TYPE_IGNORE  = 2;
    const FILE_TYPE_ROOT  = 3;

    const PHP_ERROR_MAGIC_HEADER_KEY  = 'PHP_ERROR_MAGIC_HEADER';
    const PHP_ERROR_MAGIC_HEADER_VALUE  = 'php_stack_error';
    const MAGIC_IS_PRETTY_ERRORS_MARKER = '<!-- __magic_php_error_is_a_stack_trace_constant__ -->';

    const HEADER_SAVE_FILE  = 'PHP_ERROR_SAVE_FILES';

    const POST_FILE_LOCATION  = 'php_error_upload_file';

    const PHP_ERROR_INI_PREFIX  = 'php_error';

    private static $IS_SCALAR_TYPE_HINTING_SUPPORTED  = false;

    private static $SCALAR_TYPES  = array( 'string', 'integer', 'float', 'boolean', 'bool', 'int', 'number' );

    private static $PHP_SYMBOL_MAPPINGS = array(
      '$end'  =>  'end of file',
      'T_ABSTRACT'  =>  'abstract',
      'T_AND_EQUAL' =>  "'&='",
      'T_ARRAY' =>  'array',
      'T_ARRAY_CAST'  =>  'array cast',
      'T_AS'  =>  "'as'",
      'T_BOOLEAN_AND' =>  "'&&'",
      'T_BOOLEAN_OR'  =>  "'||'",
      'T_BOOL_CAST' =>  'boolean cast',
      'T_BREAK' =>  'break',
      'T_CASE'  =>  'case',
      'T_CATCH' =>  'catch',
      'T_CLASS' =>  'class',
      'T_CLASS_C' =>  '__CLASS__',
      'T_CLONE' =>  'clone',
      'T_CLOSE_TAG' =>  'closing PHP tag',
      'T_CONCAT_EQUAL'  =>  "'.='",
      'T_CONST' =>  'const',
      'T_CONSTANT_ENCAPSED_STRING'  =>  'string',
      'T_CONTINUE'  =>  'continue',
      'T_CURLY_OPEN'  =>  '\'{$\'',
      'T_DEC' =>  '-- (decrement)',
      'T_DECLARE' =>  'declare',
      'T_DEFAULT' =>  'default',
      'T_DIR' =>  '__DIR__',
      'T_DIV_EQUAL' =>  "'/='",
      'T_DNUMBER' =>  'number',
      'T_DOLLAR_OPEN_CURLY_BRACES'  =>  '\'${\'',
      'T_DO'  =>  "'do'",
      'T_DOUBLE_ARROW'  =>  "'=>'",
      'T_DOUBLE_CAST' =>  'double cast',
      'T_DOUBLE_COLON'  =>  "'::'",
      'T_ECHO'  =>  'echo',
      'T_ELSE'  =>  'else',
      'T_ELSEIF'  =>  'elseif',
      'T_EMPTY' =>  'empty',
      'T_ENCAPSED_AND_WHITESPACE' =>  'non-terminated string',
      'T_ENDDECLARE'  =>  'enddeclare',
      'T_ENDFOR'  =>  'endfor',
      'T_ENDFOREACH'  =>  'endforeach',
      'T_ENDIF' =>  'endif',
      'T_ENDSWITCH' =>  'endswitch',
      'T_ENDWHILE'  =>  'endwhile',
      'T_EVAL'  =>  'eval',
      'T_EXIT'  =>  'exit call',
      'T_EXTENDS' =>  'extends',
      'T_FILE'  =>  '__FILE__',
      'T_FINAL' =>  'final',
      'T_FINALLY' =>  'finally',
      'T_FOR' =>  'for',
      'T_FOREACH' =>  'foreach',
      'T_FUNCTION'  =>  'function',
      'T_FUNC_C'  =>  '__FUNCTION__',
      'T_GLOBAL'  =>  'global',
      'T_GOTO'  =>  'goto',
      'T_HALT_COMPILER' =>  '__halt_compiler',
      'T_IF'  =>  'if',
      'T_IMPLEMENTS'  =>  'implements',
      'T_INC' =>  '++ (increment)',
      'T_INCLUDE' =>  'include',
      'T_INCLUDE_ONCE'  =>  'include_once',
      'T_INSTANCEOF'  =>  'instanceof',
      'T_INSTEADOF' =>  'insteadof',
      'T_INT_CAST'  =>  'int cast',
      'T_INTERFACE' =>  'interface',
      'T_ISSET' =>  'isset',
      'T_IS_EQUAL'  =>  "'=='",
      'T_IS_GREATER_OR_EQUAL' =>  "'>='",
      'T_IS_IDENTICAL'  =>   "'==='",
      'T_IS_NOT_EQUAL'  =>  "'!=' or '<>'",
      'T_IS_NOT_IDENTICAL'  =>  "'!=='",
      'T_IS_SMALLER_OR_EQUAL' =>  "'<='",
      'T_LINE'  =>  '__LINE__',
      'T_LIST'  =>  'list',
      'T_LNUMBER' =>  'number',
      'T_LOGICAL_AND' =>  "'and'",
      'T_LOGICAL_OR'  =>  "'or'",
      'T_LOGICAL_XOR' =>  "'xor'",
      'T_METHOD_C'  =>  '__METHOD__',
      'T_MINUS_EQUAL' => "'-='",
      'T_MOD_EQUAL' =>  "'%='",
      'T_MUL_EQUAL' =>  "'*='",
      'T_NAMESPACE' =>  'namespace',
      'T_NEW' =>  'new',
      'T_NUM_STRING'  =>  'array index in a string',
      'T_NS_C'  =>  '__NAMESPACE__',
      'T_NS_SEPARATOR'  =>  'namespace seperator',
      'T_OBJECT_CAST' =>  'object cast',
      'T_OBJECT_OPERATOR' =>  "'->'",
      'T_OLD_FUNCTION'  =>  'old_function',
      'T_OPEN_TAG'  =>  "'<?php' or '<?'",
      'T_OPEN_TAG_WITH_ECHO'  =>  "'<?php echo '",
      'T_OR_EQUAL'  =>  "'|='",
      'T_PAAMAYIM_NEKUDOTAYIM'  =>  "'::'",
      'T_PLUS_EQUAL'  =>  "'+='",
      'T_PRINT' =>  'print',
      'T_PRIVATE' =>  'private',
      'T_PUBLIC'  =>  'public',
      'T_PROTECTED' =>  'protected',
      'T_REQUIRE' =>  'require',
      'T_REQUIRE_ONCE'  =>  'require_once',
      'T_RETURN'  =>  'return',
      'T_SL'  =>  "'<<'",
      'T_SL_EQUAL'  =>  "'<<='",
      'T_SR'  =>  "'>>'",
      'T_SR_EQUAL'  =>  "'>>='",
      'T_START_HEREDOC' =>  "'<<<'",
      'T_STATIC'  =>  'static',
      'T_STRING'  =>  'string',
      'T_STRING_CAST' =>  'string cast',
      'T_SWITCH'  =>  'switch',
      'T_THROW' =>  'throw',
      'T_TRY' =>  'try',
      'T_TRAIT' =>  'trait',
      'T_TRAIT_C' =>  '__trait__',
      'T_UNSET' =>  'unset',
      'T_UNSET_CAST'  =>  'unset cast',
      'T_USE' =>  'use',
      'T_VAR' =>  'var',
      'T_VARIABLE'  =>  'variable',
      'T_WHILE' =>  'while',
      'T_WHITESPACE'  =>  'whitespace',
      'T_XOR_EQUAL' =>  "'^='",
      'T_YIELD' =>  'yield'
    );

    private static $syntaxMap = array(
      'const' =>  'syntax-literal',
      'reference_ampersand' =>  'syntax-function',

      T_COMMENT =>  'syntax-comment',
      T_DOC_COMMENT =>  'syntax-comment',

      T_ABSTRACT  =>  'syntax-keyword',
      T_AS  =>  'syntax-keyword',
      T_BREAK =>  'syntax-keyword',
      T_CASE  =>  'syntax-keyword',
      T_CATCH =>  'syntax-keyword',
      T_CLASS =>  'syntax-keyword',

      T_CONST =>  'syntax-keyword',

      T_CONTINUE  =>  'syntax-keyword',
      T_DECLARE =>  'syntax-keyword',
      T_DEFAULT =>  'syntax-keyword',
      T_DO  =>  'syntax-keyword',

      T_ELSE  =>  'syntax-keyword',
      T_ELSEIF  =>  'syntax-keyword',
      T_ENDDECLARE  =>  'syntax-keyword',
      T_ENDFOR  =>  'syntax-keyword',
      T_ENDFOREACH  =>  'syntax-keyword',
      T_ENDIF =>  'syntax-keyword',
      T_ENDSWITCH =>  'syntax-keyword',
      T_ENDWHILE  =>  'syntax-keyword',
      T_EXTENDS =>  'syntax-keyword',

      T_FINAL =>  'syntax-keyword',
      T_FINALLY =>  'syntax-keyword',
      T_FOR =>  'syntax-keyword',
      T_FOREACH =>  'syntax-keyword',
      T_FUNCTION  =>  'syntax-keyword',
      T_GLOBAL  =>  'syntax-keyword',
      T_GOTO  =>  'syntax-keyword',

      T_IF  =>  'syntax-keyword',
      T_IMPLEMENTS  =>  'syntax-keyword',
      T_INSTANCEOF  =>  'syntax-keyword',
      T_INSTEADOF =>  'syntax-keyword',
      T_INTERFACE =>  'syntax-keyword',

      T_LOGICAL_AND =>  'syntax-keyword',
      T_LOGICAL_OR  =>  'syntax-keyword',
      T_LOGICAL_XOR =>  'syntax-keyword',
      T_NAMESPACE =>  'syntax-keyword',
      T_NEW =>  'syntax-keyword',
      T_PRIVATE => 'syntax-keyword',
      T_PUBLIC  =>  'syntax-keyword',
      T_PROTECTED =>  'syntax-keyword',
      T_RETURN  =>  'syntax-keyword',
      T_STATIC  =>  'syntax-keyword',
      T_SWITCH  =>  'syntax-keyword',
      T_THROW =>  'syntax-keyword',
      T_TRAIT =>  'syntax-keyword',
      T_TRY =>  'syntax-keyword',
      T_USE =>  'syntax-keyword',
      T_VAR =>  'syntax-keyword',
      T_WHILE =>  'syntax-keyword',
      T_YIELD =>  'syntax-keyword',

      T_CLASS_C =>  'syntax-literal',
      T_DIR =>  'syntax-literal',
      T_FILE  =>  'syntax-literal',
      T_FUNC_C  =>  'syntax-literal',
      T_LINE  =>  'syntax-literal',
      T_METHOD_C  =>  'syntax-literal',
      T_NS_C  =>  'syntax-literal',
      T_TRAIT_C =>  'syntax-literal',

      T_DNUMBER =>  'syntax-literal',
      T_LNUMBER =>  'syntax-literal',

      T_CONSTANT_ENCAPSED_STRING  =>  'syntax-string',
      T_VARIABLE  =>  'syntax-variable',

      T_STRING        => 'syntax-function',

      T_ARRAY =>  'syntax-function',
      T_CLONE =>  'syntax-function',
      T_ECHO  =>  'syntax-function',
      T_EMPTY =>  'syntax-function',
      T_EVAL  =>  'syntax-function',
      T_EXIT  =>  'syntax-function',
      T_HALT_COMPILER =>  'syntax-function',
      T_INCLUDE =>  'syntax-function',
      T_INCLUDE_ONCE  =>  'syntax-function',
      T_ISSET =>  'syntax-function',
      T_LIST  =>  'syntax-function',
      T_REQUIRE_ONCE  =>  'syntax-function',
      T_PRINT =>  'syntax-function',
      T_REQUIRE =>  'syntax-function',
      T_UNSET =>  'syntax-function'
    );

    private static $SAFE_AUTOLOADER_FUNCTIONS = array( 'class_exists', 'interface_exists', 'method_exists', 'property_exists', 'is_subclass_of' );
    private static $ALLOWED_RETURN_MIME_TYPES = array( 'text/html', 'application/xhtml+xml' );

    private static function isIIS() {
      return ( isset( $_SERVER['SERVER_SOFTWARE'] ) && strpos( $_SERVER['SERVER_SOFTWARE'], 'IIS/' ) !== false ) || ( isset( $_SERVER['_FCGI_X_PIPE_'] ) && strpos( $_SERVER['_FCGI_X_PIPE_'], 'IISFCGI') !== false );
    }

    private static function isBinaryRequest() {
      $response = ErrorHandler::getResponseHeaders();
      foreach( $response as $key => $value ) {
        if ( strtolower( $key ) === 'content-transfer-encoding' ) {
          return ( strtolower( $value ) === 'binary' );
        }
      }
    }

    private static function isNonPHPRequest() {
      $response = ErrorHandler::getResponseHeaders();
      foreach( $response as $key => $value ) {
        if ( strtolower( $key ) === 'content-type' ) {
          foreach ( ErrorHandler::$ALLOWED_RETURN_MIME_TYPES as $type ) {
            if ( stripos( $value, $type ) !== false ) {
              return false;
            }
          }
          return true;
        }
      }
      return false;
    }

    private static function phpSymbolToDescription( $symbol ) {
      if ( isset( ErrorHandler::$PHP_SYMBOL_MAPPINGS[$symbol] ) ) {
        return ErrorHandler::$PHP_SYMBOL_MAPPINGS[$symbol];
      }
      else {
        return "'$symbol'";
      }
    }

    public static function syntaxHighlight( $code ) {
      $syntaxMap  = ErrorHandler::$syntaxMap;
      $tokens = @token_get_all( "<?php ".$code." ?>" );
      $html = array();
      $len  = count( $tokens ) - 1;
      $inString = false;
      $stringBuff = null;
      $skip = false;

      for( $i = 1; $i < $len; $i++ ) {
        $token  = $tokens[$i];
        if ( is_array( $token ) ) {
          $type = $token[0];
          $code = $token[1];
        }
        else {
          $type = null;
          $code = $token;
        }
        if ( strpos( $code, "\n" ) !== false && trim( $code ) === '' ) {
          if ( $inString ) {
            $html[] = "<span class=\"syntax-string\">".implode( "", $stringBuff );
            $stringBuff = array();
          }
        }
        elseif ( $code === '&' ) {
          if ( $i < $len ) {
            $next = $tokens[$i+1];
            if ( is_array( $next ) && $next[0] === T_VARIABLE ) {
              $type = "reference_ampersand";
            }
          }
        }
        elseif ( $code === '"' || $code === "'" ) {
          if ( $inString ) {
            $html[] = "<span class=\"syntax-string\">".implode( "", $stringBuff ).htmlspecialchars( $code )."</span>";
            $stringBuff = null;
            $skip = true;
          }
          else {
            $stringBuff = array();
          }
          $inString = !$inString;
        }
        elseif ( $type === T_STRING ) {
          $matches  = array();
          preg_match( ErrorHandler::REGEX_PHP_CONST_IDENTIFIER, $code, $matches );
          if ( $matches && strlen( $matches[0] ) === strlen( $code ) ) {
            $type = "const";
          }
        }

        if ( $skip ) {
          $skip = false;
        }
        else {
          $code = htmlspecialchars( $code );
          if ( $type !== null && isset( $syntaxMap[$type] ) ) {
            $class  = $syntaxMap[$type];
            if ( $type === T_CONSTANT_ENCAPSED_STRING && strpos( $code, "\n" ) !== false ) {
              $append = "<span class='$class'>".implode( "</span>\n<span class='$class'>", explode( "\n", $code ) )."</span>";
            }
            elseif ( strrpos( $code, "\n" ) === strlen( $code )- 1 ) {
              $append = "<span class='$class'>".substr( $code, 0, strlen( $code ) - 1 )."</span>\n";
            }
            else {
              $append = "<span class='$class'>$code</span>";
            }
          }
          elseif ( $inString && $code !== '"' ) {
            $append = "<span class=\"syntax-string\">$code</span>";
          }
          else {
            $append = $code;
          }
          if ( $inString ) {
            $stringBuff[] = $append;
          }
          else {
            $html[] = $append;
          }
        }
      }
      if ( $stringBuff !== null ) {
        $html[] = "<span class=\"syntax-string\">".implode( "", $stringBuff )."</span>";
        $stringBuff = null;
      }
      return implode( "", $html );
    }

    private static function splitFunction( $name ) {
      $name = preg_replace( '/\\(\\)$/', '', $name );
      if ( strpos( $name, '::' ) !== false ) {
        $parts  = explode( '::', $name );
        $className  = $parts[0];
        $type = '::';
        $functionName = $parts[1];
      }
      elseif ( strpos( $name, '->' ) !== false ) {
        $parts  = explode( '->', $name );
        $className  = $parts[0];
        $type = '->';
        $functionName = $parts[1];
      }
      else {
        $className  = null;
        $type = null;
        $functionName = $name;
      }
      return array( $className, $type, $functionName );
    }

    private static function newArgument( $name, $type = false, $isPassedByReference = false, $isOptional = false, $optionalValue = null, $highlight = false ) {
      if ( $name instanceof ReflectionParameter ) {
        $highlight  = ( func_num_args() > 1 ) ? $highlight = $type : false;

        $klass  = $name->getDeclaringClass();
        $functionName = $name->getDeclaringFunction()->name;
        if ( $klass !== null ) {
          $klass  = $klass->name;
        }

        $export = ReflectionParameter::export( ( ( $klass ) ? array( "\\$klass", $functionName ) : $functionName ), $name->name, true );
        $paramType  = preg_replace( '/.*?(\w+)\s+\$'.$name->name.'.*/', '\\1', $export );
        if ( strpos( $paramType, '[' ) !== false || strlen( $paramType ) === 0 ) {
          $paramType  = null;
        }
        return ErrorHandler::newArgument( $name->name, $paramType, $name->isPassedByReference(), $name->isDefaultValueAvailable(), ( ( $name->isDefaultValueAvailable() ) ? var_export( $name->getDefaultValue(), true ) : null ), ( ( func_num_args() > 1 ) ? $type : false ) );
      }
      else {
        return array( 'name' => $name, 'has_type' => ( $type !== false ), 'type' => $type, 'is_reference' => $isPassedByReference, 'has_default' => $isOptional, 'default_val' => $optionalValue, 'is_highlighted' => $highlight );
      }
    }

    private static function syntaxHighlightFunctionMatch( $match, &$stackTrace, $highlightArg = null, &$numHighlighted = 0 ) {
      list( $className, $type, $functionName )  = ErrorHandler::splitFunction( $match );
      if ( $className !== null ) {
        $reflectFun = new ReflectionMethod( $className, $functionName );
      }
      elseif ( $functionName === '{closure}' ) {
        return '<span class="syntax-variable">$closure</span>';
      }
      else {
        $reflectFun = new ReflectionFunction( $functionName );
      }
      if ( $reflectFun ) {
        $params = $reflectFun->getParameters();
        if ( $params ) {
          $args = array();
          $min = 0;
          foreach( $params as $i => $param ) {
            $arg  = ErrorHandler::newArgument( $param );
            if ( !$arg["has_default"] ) {
              $min  = $i;
            }
            $args[] = $arg;
          }
          if ( $highlightArg !== null ) {
            for( $i = $highlightArg; $i <= $min; $i++ ) {
              $args[$i]["is_highlighted"] = true;
            }
            $numHighlighted = $min-$highlightArg;
          }
          if ( $className !== null ) {
            if ( $stackTrace && isset( $stackTrace[1] ) && isset( $stackTrace[1]['type'] ) ) {
              $type = htmlspecialchars( $stackTrace[1]['type'] );
            }
          }
          else {
            $type = null;
          }
          return ErrorHandler::syntaxHighlightFunction( $className, $type, $functionName, $args );
        }
      }
      return null;
    }

    private static function syntaxHighlightFunction( $class, $type, $fun, &$args = null ) {
      $info = array();
      if ( isset( $class ) && $class && isset( $type ) && $type ) {
        if ( $type === '->' ) {
          $type = '-&gt;';
        }
        $info[] = "<span class='syntax-class'>$class</span>$type";
      }
      if ( isset( $fun ) && $fun ) {
        $info[] = "<span class='syntax-function'>$fun</span>";
      }
      if ( $args ) {
        $info[] = '( ';
        foreach( $args as $i => $arg ) {
          if ( $i > 0 ) {
            $info[] = ', ';
          }
          if ( is_string( $arg ) ) {
            $info[] = $arg;
          }
          else {
            $highlight  = $arg["is_highlighted"];
            $name = $arg["name"];
            if ( $highlight ) {
              $info[] = '<span class="syntax-higlight-variable">';
            }
            if ( $name === '_' ) {
              $info[] = '<span class="syntax-variable-not-important">';
            }
            if ( $arg["has_type"] ) {
              $info[] = "<span class='syntax-class'>";
              $info[] = $arg["type"];
              $info[] = "</span> ";
            }
            if ( $arg["is_reference"] ) {
              $info[] = '<span class="syntax-function">&amp;</span>';
            }
            $info[] = "<span class='syntax-variable'>\$$name</span>";
            if ( $arg["has_default"] ) {
              $info[] = '=<span class="syntax-literal">'.$arg['default_val'].'</span>';
            }
            if ( $name === '_' ) {
              $info[] = '</span>';
            }
            if ( $highlight ) {
              $info[] = '</span>';
            }
          }
        }
        $info[] = ' )';
      }
      else {
        $info[] = '()';
      }
      return implode( "", $info );
    }

    private static function optionsPop( &$options, $key, $alt = null ) {
      if ( $options && isset( $options[$key] ) ) {
        $val  = $options[$key];
        unset( $options[$key] );
        return $val;
      }
      else {
        $iniAlt = @get_cfg_var( ErrorHandler::PHP_ERROR_INI_PREFIX.'.'.$key );
        if ( $iniAlt !== false ) {
          return $iniAlt;
        }
        else {
          return $alt;
        }
      }
    }

    private static function folderTypeToCSS( $type ) {
      if ( $type === ErrorHandler::FILE_TYPE_ROOT ) {
        return 'file-root';
      }
      elseif ( $type === ErrorHandler::FILE_TYPE_IGNORE ) {
        return 'file-ignore';
      }
      elseif ( $type === ErrorHandler::FILE_TYPE_APPLICATION ) {
        return 'file-app';
      }
      else {
        return 'file-common';
      }
    }

    private static function isFolderType( &$folders, $longest, $file ) {
      $parts  = explode( '/', $file );
      $len    = min( count( $parts ), $longest );
      for( $i = $len; $i > 0; $i-- ) {
        if ( isset( $folders[$i] ) ) {
          $folderParts  = &$folders[ $i ];
          $success  = false;
          for( $j = 0; $j < count( $folderParts ); $j++ ) {
            $folderNames  = $folderParts[$j];
            for( $k = 0; $k < count( $folderNames ); $k++ ) {
              if ( $folderNames[$k] === $parts[$k] ) {
                $success  = true;
              }
              else {
                $success  = false;
                break;
              }
            }
          }
          if ( $success ) {
            return true;
          }
        }
      }
      return false;
    }

    private static function setFolders( &$origFolders, &$longest, $folders ) {
      $newFolders = array();
      $newLongest = 0;
      if ( $folders ) {
        if ( is_array( $folders ) ) {
          foreach( $folders as $folder ) {
            ErrorHandler::setFoldersInner( $newFolders, $newLongest, $folder );
          }
        }
        elseif ( is_string( $folders ) ) {
          ErrorHandler::setFoldersInner( $newFolders, $newLongest, $folders );
        }
        else {
          throw new Exception( "Unknown value given for folder: ".$folders );
        }
      }
      $origFolders  = $newFolders;
      $longest      = $newLongest;
    }

    private static function setFoldersInner( &$newFolders, &$newLongest, $folder ) {
      $folder = str_replace( '\\', '/', $folder );
      $folder = preg_replace( '/(^\\/+)|(\\/+$)/', '', $folder );
      $parts  = explode( '/', $folder );
      $count  = count( $parts );
      $newLongest = max( $newLongest, $count );
      if ( isset( $newFolders[$count] ) ) {
        $folds  = &$newFolders[$count];
        $folds[]  = $parts;
      }
      else {
        $newFolders[$count] = array( $parts );
      }
    }

    private static function getRequestHeaders() {
      if ( function_exists('getallheaders') ) {
        return getallheaders();
      }
      else {
        $headers  = array();
        foreach( $_SERVER as $key => $value ) {
          if ( strpos( $key, 'HTTP_' ) === 0 ) {
            $key  = str_replace( " ", "-", ucwords( strtolower( str_replace( "_", " ", substr( $key, 5 ) ) ) ) );
            $headers[$key]  = $value;
          }
        }
        return $headers;
      }
    }

    private static function getResponseHeaders() {
      $headers  = ( function_exists( 'apache_response_headers' ) ) ? apache_response_headers() : array();
      if ( function_exists( 'headers_list' ) ) {
        $hList  = headers_list();
        foreach( $hList as $header ) {
          $header =   explode( ":", $header );
          $headers[array_shift($header)]  = trim( implode( ":", $header ) );
        }
      }
      return $headers;
    }

    public static function identifyTypeHTML( $arg, $recurseLevels = 1 ) {
      if ( !is_array( $arg ) && !is_object( $arg ) ) {
        if ( is_string( $arg ) ) {
          return "<span class='syntax-string'>&quot;".ellipses( htmlentities( $arg ), 40 )."&quot;</span>";
        }
        else {
          return "<span class='syntax-literal'>".var_export( $arg, true ).'</span>';
        }
      }
      elseif ( is_array( $arg ) ) {
        if ( count( $arg ) === 0 ) {
          return "[]";
        }
        elseif ( $recurseLevels > 0 ) {
          $argArr = array();
          foreach( $arg as $ag ) {
            $argArr[] = ErrorHandler::identifyTypeHTML( $ag, $recurseLevels - 1 );
          }
          if ( ( $recurseLevels % 2 ) === 0 ) {
            return "[".implode( ', ', $argArr )."]";
          }
          else {
            return "[ ".implode( ', ', $argArr )." ]";
          }
        }
        else {
          return "[...]";
        }
      }
      elseif ( get_class( $arg ) === 'Closure' ) {
        return '<span class="syntax-variable">$Closure</span>()';
      }
      else {
        $argKlass = get_class( $arg );
        if ( preg_match( ErrorHandler::REGEX_PHP_CONST_IDENTIFIER, $argKlass ) ) {
          return '<span class="syntax-literal">$'.$argKlass.'</span>';
        }
        else {
          return '<span class="syntax-variable">$'.$argKlass.'</span>';
        }
      }
    }

    private $saveUrl;
    private $isSavingEnabled;

    private $cachedFiles;

    private $isShutdownRegistered;
    private $isOn;

    private $ignoreFolders = array();
    private $ignoreFoldersLongest = 0;

    private $applicationFolders = array();
    private $applicationFoldersLongest  = 0;

    private $defaultErrorReportingOn;
    private $defaultErrorReportingOff;
    private $applicationRoot;
    private $serverName;

    private $catchClassNotFound;
    private $catchSurpressedErrors;
    private $catchAjaxErrors;

    private $backgroundText;
    private $numLines;

    private $displayLineNumber;
    private $htmlOnly;

    private $isBufferSetup;
    private $bufferOutputStr;
    private $bufferOutput;

    private $isAjax;

    private $lastGlobalErrorHandler;

    private $classNotFoundException;

    public function __construct( $options = null ) {
      global $_php_error_global_handler;
      if ( $_php_error_global_handler !== null ) {
        $this->lastGlobalErrorHandler = $_php_error_global_handler;
      }
      else {
        $this->lastGlobalErrorHandler = null;
      }
      $_php_error_global_handler  = $this;
      $this->cachedFiles  = array();
      $this->isShutdownRegistered = false;
      $this->isOn = false;

      $ignoreFolders  = ErrorHandler::optionsPop( $options, "ignore_folders", null );
      $appFolders     = ErrorHandler::optionsPop( $options, "application_folders", null );

      if ( $ignoreFolders !== null ) {
        ErrorHandler::setFolders( $this->ignoreFolders, $this->ignoreFoldersLongest, $ignoreFolders );
      }
      if ( $appFolders !== null ) {
        ErrorHandler::setFolders( $this->applicationFolders, $this->applicationFoldersLongest, $appFolders );
      }

      $this->saveUrl  = ErrorHandler::optionsPop( $options, 'save_url', $_SERVER['REQUEST_URI'] );
      $this->isSavingEnabled  = ErrorHandler::optionsPop( $options, 'enable_saving', true );

      $this->defaultErrorReportingOn  = ErrorHandler::optionsPop( $options, 'error_reporting_on', -1 );
      $this->defaultErrorReportingOff = ErrorHandler::optionsPop( $options, 'error_reporting_off', error_reporting() );

      $this->applicationRoot  = ErrorHandler::optionsPop( $options, 'application_root', $_SERVER['DOCUMENT_ROOT'] );
      $this->serverName = ErrorHandler::optionsPop( $options, 'server_name', $_SERVER['SERVER_NAME'] );

      $dir  = @realpath( $this->applicationRoot );
      if ( !is_string( $dir ) ) {
        throw new Exception( "Document root not found: ".$this->applicationRoot );
      }
      else {
        $this->applicationRoot  = str_replace( DIRECTORY_SEPARATOR, "/", $dir );
      }
      $this->catchClassNotFound = !!ErrorHandler::optionsPop( $options, 'catch_class_not_found', true );
      $this->catchSurpressedErrors  = !!ErrorHandler::optionsPop( $options, 'catch_supressed_errors', false );
      $this->catchAjaxErrors  = !!ErrorHandler::optionsPop( $options, 'catch_ajax_errors', true  );

      $this->backgroundText = ErrorHandler::optionsPop( $options, 'background_text', '' );
      $this->numLines = ErrorHandler::optionsPop( $options, 'snippet_num_lines', ErrorHandler::NUM_FILE_LINES );
      $this->displayLineNumber  = ErrorHandler::optionsPop( $options, 'display_line_numbers', true );

      $this->htmlOnly = !!ErrorHandler::optionsPop( $options, 'html_only', true );

      $this->classNotFoundException = null;

      $wordpress  = ErrorHandler::optionsPop( $options, 'wordpress', false );
      if ( $wordpress ) {
        $this->defaultErrorReportingOn  = E_ERROR | E_WARNING | E_PARSE | E_USER_DEPRECATED & ~E_DEPRECATED & ~E_STRICT;
      }
      $concrete5  = ErrorHandler::optionsPop( $options, 'concrete5', false );
      if ( $concrete5 ) {
        $this->defaultErrorReportingOn = E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED;
      }
      if ( $options ) {
        foreach ( $options as $key => $val ) {
          throw new InvalidArgumentException( "Unknown option given $key" );
        }
      }
      $this->isAjax = ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && ( $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' ) ) || ( isset( $_REQUEST['php_error_is_ajax'] ) );
      $this->isBufferSetup  =  false;
      $this->bufferOutputStr  = '';
      $this->bufferOutput = false;
      $this->startBuffer();
    }
 
    public function isOn() {
      return $this->isOn;
    }

    public function isOff() {
      return !$this->isOn;
    }

    public function turnOn() {
      $this->propagateTurnOff();
      $this->setEnabled( true );

      global $_php_error_is_ini_enabled;
      if ( $_php_error_is_ini_enabled ) {
        if ( $this->isSavingEnabled ) {
          $headers  = ErrorHandler::getRequestHeaders();
          if ( isset( $headers[ErrorHandler::HEADER_SAVE_FILE] ) ) {
            if ( isset( $_POST ) && isset( $_POST[ErrorHandler::POST_FILE_LOCATION] ) ) {
              $files  = $_POST[ErrorHandler::POST_FILE_LOCATION];
              foreach( $files as $file => $content ) {
                @file_put_contents( $file, $content );
              }
              exit(0);
            }
          }
        }
      }
      return $this;
    }

    public function turnOff() {
      $this->setEnabled( false );
      return $this;
    }

    public function withoutErrors( $callback ) {
      if ( !is_callable( $callback ) ) {
        throw new Exception( "non callable callback given" );
      }
      if ( $this->isOn() ) {
        $this->turnOff();
        $result = $callback();
        $this->turnOn();
        return $result;
      }
      else {
        return $callback();
      }
    }

    public function __onShutdown() {
      global $_php_error_is_ini_enabled;
      if ( $_php_error_is_ini_enabled ) {
        if ( $this->isOn() ) {
          $error  = error_get_last();
          if ( $error && in_array( $error['type'], array( 1, 4, 64 ) ) ) {
            $this->reportError( $error['type'], $error['message'], $error['line'], $error['file'] );
          }
          else {
            $this->endBuffer();
          }
        }
        else {
          $this->endBuffer();
        }
      }
    }

    private function propagateTurnOff() {
      if ( $this->lastGlobalErrorHandler !== null ) {
        $this->lastGlobalErrorHandler->turnOff();
        $this->lastGlobalErrorHandler->propagateTurnOff();
        $this->lastGlobalErrorHandler = null;
      }
    }

    private function startBuffer() {
      global $_php_error_is_ini_enabled;
      if ( $_php_error_is_ini_enabled && !$this->isBufferSetup ) {
        $this->isBufferSetup  = true;
        ini_set( 'implicit_flush', false );
        ob_implicit_flush( false );
        if ( !@ini_get('output_buffering') ) {
          @ini_set( 'output_buffering', 'on' );
        }
        $output = '';
        $bufferOutput = true;
        $this->bufferOutputStr  = &$output;
        $this->bufferOutput = &$bufferOutput;

        ob_start( function( $string ) use ( &$output, &$bufferOutput ) {
          if ( $bufferOutput ) {
            $output .=  $string;
            return '';
          }
          else {
            $temp = $output . $string;
            $output = '';
            return $temp;
          }
        });
        $self = $this;
        register_shutdown_function( function() use ( $self ) {
          $self->__onShutdown();
        });
      }
    }

    private function discardBuffer() {
      $str  = $this->bufferOutputStr;
      $this->bufferOutputStr  = '';
      $this->bufferOutput = false;
      return $str;
    }
 
    private function flushBuffer() {
      $temp = $this->bufferOutputStr;
      $this->bufferOutputStr  = '';
      return $temp;
    }

    public function endBuffer() {
      if ( $this->isBufferSetup ) {
        $content  = ob_get_contents();
        $handlers = ob_list_handlers();
        $wasGZHandler = false;
        $this->bufferOutput = true;
        for( $i = count( $handlers ) - 1; $i >= 0; $i-- ) {
          $handler  = $handlers[$i];
          if ( $handler === 'ob_gzhandler' ) {
            $wasGZHandler = true;
            ob_end_clean();
          }
          elseif ( $handler === 'default output handler' ) {
            ob_end_clean();
          }
          else {
            ob_end_flush();
          }
        }
        $content  = $this->discardBuffer();
        if ( $wasGZHandler ) {
          ob_start( 'ob_gzhandler' );
        }
        else {
          ob_start();
        }
        if ( !$this->isAjax && $this->catchAjaxErrors && ( !$this->htmlOnly || !ErrorHandler::isNonPHPRequest() ) && !ErrorHandler::isBinaryRequest() ) {
          $js = $this->getContent( 'displayJSInjection' );
          $js = JSMin::minify( $js );
          $matches  = array();
          preg_match( ErrorHandler::REGEX_DOCTYPE, $content, $matches );
          if ( $matches ) {
            $doctype  = $matches[0];
            $content  = preg_replace( ErrorHandler::REGEX_DOCTYPE, "$doctype $js", $content );
          }
          else {
            echo $js;
          }
        }
        echo $content;
      }
    }

    private function getContent( $method ) {
      ob_start();
      $this->$method();
      $content  = ob_get_contents();
      ob_end_clean();
      return $content;
    }

    private function isApplicationFolder( $file ) {
      return ErrorHandler::isFolderType( $this->applicationFolders, $this->applicationFoldersLongest, $file );
    }

    private function isIgnoreFolder( $file ) {
      return ErrorHandler::isFolderType( $this->ignoreFolders, $this->ignoreFoldersLongest, $file );
    }

    private function getFolderType( $root, $file ) {
      $testFile = $this->removeRootPath( $root, $file );
      if ( $file === __FILE__ ) {
        $type = ErrorHandler::FILE_TYPE_IGNORE;
      }
      elseif ( strpos( $testFile, '/' ) === false ) {
        $type = ErrorHandler::FILE_TYPE_ROOT;
      }
      elseif ( $this->isApplicationFolder( $testFile ) ) {
        $type = ErrorHandler::FILE_TYPE_APPLICATION;
      }
      elseif ( $this->isIgnoreFolder( $testFile ) ) {
        $type = ErrorHandler::FILE_TYPE_IGNORE;
      }
      else {
        $type = false;
      }
      return array( $type, $testFile );
    }

    private function getFileContents( $path ) {
      if ( isset( $this->cachedFiles[$path] ) ) {
        return $this->cachedFiles[$path];
      }
      else {
        $contents = @file_get_contents( $path );
        if ( $contents ) {
          $contents = explode( "\n", preg_replace( '/(\r\n)|(\n\r)|\r/', "\n", str_replace( "\t", '    ', $contents ) ) );
          $this->cachedFiles[$path] = $contents;
          return $contents;
        }
      }
      return array();
    }

    private function readCodeFile( $errFile, $errLine ) {
      try {
        $lines  = $this->getFileContents( $errFile );
        if ( $lines ) {
          $numLines = $this->numLines;
          $searchUp = ceil( $numLines*0.75 );
          $searchDown = $numLines - $searchUp;
          $countLines = count( $lines );

          if ( $errLine + $searchDown > $countLines ) {
            $minLine  = max( 0, $countLines - $numLines );
            $maxLine  = $countLines;
          }
          else {
            $minLine  = max( 0, $errLine - $searchUp );
            $maxLine  = min( $minLine + $numLines, count( $lines ) );
          }
          $fileLines  = array_splice( $lines, $minLine, $maxLine - $minLine );
          $stripSize  = -1;
          foreach( $fileLines as $i => $line ) {
            $newLine  = ltrim( $line, ' ' );
            if ( strlen( $newLine ) > 0 ) {
              $numSpaces  = strlen( $line ) - strlen( $newLine );
              if ( $stripSize === -1 ) {
                $stripSize  = $numSpaces;
              } 
              else {
                $stripSize  = min( $stripSize, $numSpaces );
              }
            }
            else {
              $fileLines[$i]  = $newLine;
            }
          }
          if ( $stripSize > 0 ) {
            if ( $stripSize > 4 ) {
              $stripSize -= 4;
            }
            foreach ( $fileLines as $i => $line ) {
              if ( strlen( $line ) > $stripSize ) {
                $fileLines[$i]  = substr( $line, $stripSize );
              }
            }
          }

          $fileLines  = join( "\n", $fileLines );
          $fileLines  = ErrorHandler::syntaxHighlight( $fileLines );
          $fileLines  = explode( "\n", $fileLines );

          $lines  = array();
          for( $i = 0; $i < count( $fileLines ); $i++ ) {
            $lines[$i+$minLine+1] = $fileLines[$i];
          }
        }
        return $lines;
      }
      catch ( Exception $ex ) {
        return null;
      }
      return null;
    }

    private function removeRootPath( $root, $path ) {
      $filePath = str_replace( DIRECTORY_SEPARATOR, '/', $path );
      if ( strpos( $filePath, $root ) === 0 && strlen( $root ) < strlen( $filePath ) ) {
        return substr( $filePath, strlen( $root )+1 );
      }
      else {
        return $filePath;
      }
    }

    private function improveErrorMessage( $ex, $code, $message, $errLine, $errFile, $root, &$stackTrace ) {
      $srcErrFile = $errFile;
      $srcErrLine = $errLine;
      $altInfo    = null;
      $stackSearchI = 0;

      $skipStackFirst = function( &$stackTrace ) {
        $skipFirst  = true;
        foreach( $stackTrace as $i => $trace ) {
          if ( $skipFirst ) {
            $skipFirst  = false;
          }
          else {
            if ( $trace && isset( $trace['file'] ) && isset( $trace['line'] ) ) {
              return array( $trace['file'], $trace['line'], $i );
            }
          }
        }
        return array( null, null, null );
      };

      if ( $code === 1 ) {
        if ( ( strpos( $message, " undefined method " ) !== false ) || ( strpos( $message, " undefined function " ) !== false ) ) {
          $matches  = array();
          preg_match( '/\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*((->|::)[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)?\\(\\)$/', $message, $matches );

          if ( $matches ) {
            list( $className, $type, $functionName )  = ErrorHandler::splitFunction( $matches[0] );
            if ( $stackTrace && isset( $stackTrace[1] ) && $stackTrace[1]['args'] ) {
              $numArgs  = count( $stackTrace[1]['args'] );
              for( $i = 0; $i < $numArgs; $i++ ) {
                $args[] = ErrorHandler::newArgument( "_" );
              }
            }

            $message = preg_replace( '/\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*((->|::)[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)?\\(\\)$/', ErrorHandler::syntaxHighlightFunction( $className, $type, $functionName, $args ), $message );
          }
        }
        elseif ( $message === 'Using $this when not in object context' ) {
          $message  = 'Using <span class="syntax-variable">$this</span> outside object context';
        }
        elseif ( strpos( $message, "Class " ) !== false && strpos( $message, "not found" ) !== false ) {
          $matches  = array();
          preg_match( '/\'(\\\\)?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*((\\\\)?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+\'/', $message, $matches );

          if ( count( $matches ) > 0 ) {
            $className  = $matches[0];
            $className  = substr( $className, 1, strlen( $className ) - 2 );
            $message    = preg_replace( '/\'(\\\\)?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*((\\\\)?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)+\'/', "<span class='syntax-class'>$className</span>", $message );
          }
        }
      }
      elseif ( $code === 2 ) {
        if ( strpos( $message, "Missing argument " ) === 0 ) {
          $message  = preg_replace( '/, called in .*$/', '', $message );
          $matches  = array();
          preg_match( ErrorHandler::REGEX_METHOD_OR_FUNCTION_END, $message, $matches );
          if ( $matches ) {
            $argumentMathces  = array();
            preg_match( '/^Missing argument ([0-9]+)/', $message, $argumentMathces );
            $highlightArg = ( count( $argumentMathces ) === 2 ) ? ( ( (int)$argumentMathces[1] ) - 1 ) : null ;
            $numHighlighted = 0;
            $altInfo  = ErrorHandler::syntaxHighlightFunctionMatch( $matches[0], $stackTrace, $highlightArg, $numHighlighted );
            if ( $numHighlighted > 0 ) {
              $message  = preg_replace( '/^Missing argument ([0-9]+)/', 'Missing arguments ', $message );
            }
            if ( $altInfo ) {
              $message  = preg_replace( ErrorHandler::REGEX_METHOD_OR_FUNCTION_END, $altInfo, $message );
              list( $srcErrFile, $srcErrLine, $stackSearchI ) = $skipStackFirst( $stackTrace );
            }
          }
        }
        elseif ( strpos( $message, 'require(' ) === 0 || strpos( $message, 'include(' ) === 0 ) {
          $endI = strpos( $message, '):' );
          if ( $endI ) {
            $requireLen = strlen( 'require(' );
            $postMessage  = substr( $message, $endI + 2 );
            $postMessage  = str_replace( "failed to open stream: No ", "no ", $postMessage );
            $message      = substr_replace( $message, $postMessage, $endI + 2 );

            $replaceBit = "failed to open stream: No ";
            if ( strpos( $message, $replaceBit ) === $endI + 2 ) {
              $message  = substr_replace( $message, 'no ', $endI + 2, strlen( $replaceBit ) );
            }
            $match  = substr( $message, $requireLen, $endI-$requireLen );
            $newString  = "<span class='syntax-string'>'$match'</span>),";
            $message    = substr_replace( $message, $newString, $requireLen, ( $endI-$requireLen ) + 2 );
          }
        }
      }
      elseif ( $code === 4 ) {
        if ( $message === "syntax error, unexpected T_ENCAPSED_AND_WHITESPACE" ) {
          $message = "syntax error, string is not closed";
        }
        else {
          $semiColonError = false;
          if ( strpos( $message, 'syntax error,' ) === 0 && $errLine > 2 ) {
            $lines  = ErrorHandler::getFileContents( $errFile );
            $line   = $lines[$errLine-1];
            if ( preg_match( ErrorHandler::REGEX_MISSING_SEMI_COLON_FOLLOWING_LINE, $line ) !== 0 ) {
              $content  = rtrim( implode( "\n", array_slice( $lines, 0, $errLine - 1 ) ) );
              if ( strrpos( $content, ';' ) !== strlen( $content ) - 1 ) {
                $message  = "Missing semi-colon";
                $errLine--;
                $srcErrLine = $errLine;
                $semiColonError = true;
              }
            }
          }
          if ( $semiColonError ) {
            $matches  = array();
            $num  = preg_match( '/\bunexpected ([A-Z_]+|\\$end)\b/', $message, $matches );
            if ( $num > 0 ) {
              $match  = $matches[0];
              $newSymbol  = ErrorHandler::phpSymbolToDescription( str_replace( 'unexpected ', '', $match ) );
              $message    = str_replace( $match, "unexpected $newSymbol", $message );
            }
            $matches  = array();
            $num  = preg_match( '/, expecting ([A-Z_]+|\\$end)( or ([A-Z_]+|\\$end))*/', $message, $matches );
            if ( $num > 0 ) {
              $match  = $matches[0];
              $newMatch = str_replace( ", expecting ", '', $match );
              $symbols  = explode( ' or ', $newMatch );
              foreach( $symbols as $i => $sym ) {
                $symbols[$i]  = ErrorHandler::phpSymbolToDescription( $sym );
              }
              $newMatch = implode( ', or ', $symbols );
              $message  = str_replace( $match, ", expecting $newMatch", $message );
            }
          }
        }
      }
      elseif ( $code === 8 ) {
        if ( strpos( $message, "Undefined variable:" ) !== false ) {
          $matches  = array();
          preg_match( ErrorHandler::REGEX_VARIABLE, $message, $matches );
          if ( count( $matches ) > 0 ) {
            $message  = 'Undefined variable <span class="syntax-variable">$'.$matches[0].'</span>' ;
          }
        }
      }
      elseif ( $code === 4096 ) {
        if ( strpos( $message, 'must be an ' ) ) {
          $message  = preg_replace( '/, called in .*$/', '', $message );
          $matches  = array();
          preg_match( ErrorHandler::REGEX_METHOD_OR_FUNCTION, $message, $matches );
          if ( $matches ) {
            $argumentMathces  = array();
            preg_match( '/^Argument ([0-9]+)/', $message, $argumentMathces );
            $highlightArg = ( count( $argumentMathces ) === 2 ) ? ( ( (int)$argumentMathces[1] ) - 1 ) : null;
            $fun  = ErrorHandler::syntaxHighlightFunctionMatch( $matches[0], $stackTrace, $highlightArg );
            if ( $fun ) {
              $message  = str_replace( 'passed to ', 'calling ', $message );
              $message  = preg_replace( ErrorHandler::REGEX_METHOD_OR_FUNCTION, $fun, $message );
              $prioritizeCaller = true;
              $scalarType = null;
              if ( ! ErrorHandler::$IS_SCALAR_TYPE_HINTING_SUPPORTED ) {
                foreach( ErrorHandler::$SCALAR_TYPES as $scalar ) {
                  if ( stripos( $message, "must be an instance of $scalar," ) !== false ) {
                    $scalarType = $scalar;
                    break;
                  }
                }
              }
              if ( $scalarType !== null ) {
                $message  = preg_replace( '/^Argument [0-9]+ calling /', 'Incorrect type hinting for ', $message );
                $message  = preg_replace( '/ must be an instance of '.ErrorHandler::REGEX_PHP_IDENTIFIER.'\b.*$/', ", ${scalarType} is not supported", $message );
                $prioritizeCaller = false;
              }
              else {
                $message  = preg_replace( '/ must be an (instance of )?'.ErrorHandler::REGEX_PHP_IDENTIFIER.'\b/', '', $message );
                if ( preg_match( '/, none given$/', $message ) ) {
                  $message  = preg_replace( '/^Argument /', 'Missing argument ', $message );
                  $message  = preg_replace( '/, none given$/', '', $message );
                }
                else {
                  $message  = preg_replace( '/^Argument /', 'Incorrect argument ', $message );
                }
              }
              if ( $prioritizeCaller ) {
                list( $srcErrFile, $srcErrLine, $stackSearchI ) = $skipStackFirst( $stackTrace );
              }
            }
          }
        }
      }
      if ( $stackTrace !== null ) {
        $isEmpty  =  ( count( $stackTrace ) === 0 );
        if ( $isEmpty ) {
          array_unshift( $stackTrace, array( 'line' => $errLine, 'file' => $errFile ) );
        }
        elseif ( ( count( $stackTrace ) > 0 ) && ( ( !isset( $stackTrace[0]['line'] ) ) || ( $stackTrace[0]['line'] !== $errLine ) ) ) {
          array_unshift( $stackTrace, array( 'line' => $errLine, 'file' => $errFile ) );
        }
        if ( $stackTrace && !$isEmpty ) {
          $ignoreCommons  =  false;
          $len  = count( $stackTrace );
          for( $i = $stackSearchI; $i < ( $stackSearchI + $len ); $i++ ) {
            $trace  = &$stackTrace[$i%$len];
            if ( isset( $trace['file'] ) && isset( $trace['line'] ) ) {
              list( $type, $_ ) = $this->getFolderType( $root, $trace['file'] );
              if ( $type !== ErrorHandler::FILE_TYPE_IGNORE ) {
                if ( $type === ErrorHandler::FILE_TYPE_APPLICATION ) {
                  $srcErrLine = $trace['line'];
                  $srcErrFile = $trace['file'];
                  break;
                }
                elseif ( !$ignoreCommons ) {
                  $srcErrLine = $trace['line'];
                  $srcErrFile = $trace['file'];
                  $ignoreCommons = true;
                }
              }
            }
          }
        }
      }
      return array( $message, $srcErrFile, $srcErrLine, $altInfo );
    }

    private static function is_dangerous( $string = null ) {
      if ( !is_string( $string ) ) {
        return $string;
      }
      $striped  = strip_tags( $string );
      $strings  = array( "modlogin", MAIN_DB_USER, MAIN_DB_PASS, USER_DB_USER, USER_DB_PASS );
      $mailers  = array( "MAIL_PASSWORD" );

      foreach( $mailers as $mailer ) {
        if ( defined( $mailer ) ) {
          $strings[]  = constant( $mailer );
        }
      }

      $regexps  = array(
                    "/mysql(i)?\:(host|dbname)\=(.+?)/i",
                    "/(require(_once)?|include(_once))(\s+)?\((\s+)?(.*)(\s+)?\)(\s+)?/i",
                    "/access denied for user(.*)using password\: yes\)/i",
                    "/access denied for user(.*)to database/i",
                    '#(\&quot\;)(([a-z0-9_\s%\-.:\/\\\])*).php(\&quot\;)#si'
                  );
      foreach( $strings as $str ) {
        if ( $str && stristr( $striped, $str ) ) {
          $string = "{high sensitive data detected}";
        }
      }
      foreach( $regexps as $regexp ) {
        if ( preg_match( $regexp, $striped ) ) {
          $string = "{high sensitive data detected}";
        }
      }

      return $string;
    }

    private function parseStackTrace( $code, $message, $errLine, $errFile, &$stackTrace, $root, $altInfo = null ) {
      if ( $stackTrace !== null ) {
        $lineLen  = 0;
        $fileLen  = 0;
        foreach( $stackTrace as $i => $trace ) {
          if ( $trace ) {
            if ( isset( $trace['line'] ) ) {
              $lineLen  = max( $lineLen, strlen( $trace['line'] ) );
            }
            else {
              $trace['line']  = '';
            }
            $info = '';
            if ( $i === 0 && $altInfo !== null ) {
              $info = $altInfo;
            }
            elseif( $i > 0 && ( isset( $trace['class'] ) || isset( $trace['type'] ) || isset( $trace['function'] ) ) ) {
              $args = array();
              if ( isset( $trace['args'] ) ) {
                foreach( $trace['args'] as $arg ) {
                  $args[] = ErrorHandler::identifyTypeHTML( $arg, 1 );
                }
              }
              $info = ErrorHandler::syntaxHighlightFunction( ( isset( $trace['class'] ) ) ? $trace["class"] : null, ( isset( $trace['type'] ) ) ? $trace['type'] : null, ( isset( $trace['function'] ) ) ? $trace['function'] : null, $args );
          }
          elseif ( isset( $trace['info'] ) && $trace['info'] !== '' ) {
            $info = ErrorHandler::syntaxHighlight( $trace['info'] );
          }
          else if ( isset( $trace['file'] ) && !isset( $trace['info'] ) ) {
            $contents = $this->getFileContents( $trace['file'] );
            if ( $contents ) {
              $info = ErrorHandler::syntaxHighlight( trim( $contents[$trace['line']-1] ) );
            }
          }
          $trace['info']  = $info;
          if ( isset( $trace['file'] ) ) {
            list( $type, $file )  = $this->getFolderType( $root, $trace['file'] );
            if ( basename( $file ) == "error.handler.php" ) {
              $file = '[error_handler]';
              $trace['file_type'] = '';
              $trace['is_native'] = true;
            }
            else {
              $trace['file_type'] = $type;
              $trace['is_native'] = false;
            }
          }
          else {
            $file = '[error_handler]';
            $trace['file_type'] = '';
            $trace['is_native'] = true;
          }
          $trace['file']  = $file;
          $fileLen  = max( $fileLen, strlen( $file ) );
          $stackTrace[$i] = $trace;
        }
      }
      $highlightI = -1;
      foreach( $stackTrace as $i => $trace ) {
        if ( $trace['line'] === $errLine && $trace['file'] === $errFile ) {
          $highlightI = $i;
          break;
        }
      }
      
      foreach ( $stackTrace as $i => $trace ) {
        if ( $trace ) {
          $line = str_pad( $trace['line'], $lineLen, ' ', STR_PAD_LEFT );
          $file = basename( $trace['file'] );
          $fileKlass  = '';
          if ( $file == "index.php" ) {
            $file = "[internal_file]";
          }
          elseif ( isset( $_GET["page"] ) && $file == $_GET["page"].".php" ) {
            $file = "[internal_file]";
          }
          if ( $trace['is_native'] ) {
            $fileKlass  = 'file-internal-php';
          }
          else {
            $fileKlass  = 'filename '.ErrorHandler::folderTypeToCSS( $trace['file_type'] );
          }
          $file = $file.str_pad( '', $fileLen - strlen( $file ), ' ', STR_PAD_LEFT );
          $info = $trace['info'];
          if ( $info ) {
            $info = str_replace( "\n", '\n', $info );
            $info = str_replace( "\r", '\r', $info );
          }
          else {
            $info = '&nbsp;';
          }
          $file = trim( $file );
          $info = self::is_dangerous( $info );
          $stackStr = "<td class=\"linenumber\" style=\"width:40px;\">".$line."</td>".
                      "<td class=\"".$fileKlass."\" style=\"width:200px;\" title=\"".$file."\">".ellipses( $file, 20 )."</td>".
                      "<td class='lineinfo'>".$info."</td>";
          $cssClass = array();
          if ( $trace['is_native'] ) {
            $cssClass[] = "is-native";
          }
          if ( $highlightI === $i ) {
            $cssClass[] = "highlight";
          }
          elseif ( $highlightI > $i ) {
            $cssClass[] = "pre-highlight";
          }
          $cssClass = implode( " ", $cssClass );
          if ( $i !== 0 && isset( $trace['exception'] ) && $trace['exception'] ) {
            $ex = $trace['exception'];
            $exHtml = '<tr class="error-stack-trace-exception"><td>exception &quot;'.htmlspecialchars( $ex->getMessage() ).'&quot;</td></tr>';
          }
          else {
            $exHtml = '';
          }
          $data = '';
          if ( isset( $trace['file-id'] ) ) {
            $data = ' data-file-id="'.$trace['file-id'].'" data-line="'.$line.'"';
          }
          $stackTrace[$i] = $exHtml."<tr class=\"error-stack-trace-line ".$cssClass."\"".$data.">".$stackStr."</tr>";
        }
      }
      return "<table id=\"error-stack-trace\">".implode( "\n", $stackTrace )."</table>";
    }
    else {
      return null;
    }
  }

            private function logError( $message, $file, $line, $ex=null ) {
                if ( $ex ) {
                    $trace = $ex->getTraceAsString();
                    $parts = explode( "\n", $trace );
                    $trace = "        " . join( "\n        ", $parts );

                    if ( ! ErrorHandler::isIIS() ) {
                        error_log( "$message \n           $file, $line \n$trace" );
                    }
                } else {
                    if ( ! ErrorHandler::isIIS() ) {
                        error_log( "$message \n           $file, $line" );
                    }
                }
            }

            /**
             * Given a class name, which can include a namespace,
             * this will report that it is not found.
             *
             * This will also report it as an exception,
             * so you will get a full stack trace.
             */
            public function reportClassNotFound( $className ) {
                throw new \ErrorException( "Class '$className' not found", E_ERROR, 0, __FILE__, __LINE__ );
            }

            /**
             * Given an exception, this will report it.
             */
            public function reportException( $ex ) {
                $this->reportError(
                        $ex->getCode(),
                        $ex->getMessage(),
                        $ex->getLine(),
                        $ex->getFile(),
                        $ex
                );
            }

            /**
             * The entry point for handling an error.
             *
             * This is the lowest entry point for error reporting,
             * and for that reason it can either take just error info,
             * or a combination of error and exception information.
             *
             * Note that this will still log errors in the error log
             * even when it's disabled with ini. It just does nothing
             * more than that.
             */
            public function reportError( $code, $message, $errLine, $errFile, $ex=null ) {
                $this->discardBuffer();

                if (
                        $ex === null &&
                        $code === 1 &&
                        strpos($message, "Class ") === 0 &&
                        strpos($message, "not found") !== false &&
                        $this->classNotFoundException !== null
                ) {
                    $ex = $this->classNotFoundException;

                    $code       = $ex->getCode();
                    $message    = $ex->getMessage();
                    $errLine    = $ex->getLine();
                    $errFile    = $ex->getFile();
                    $stackTrace = $ex->getTrace();
                }

                $this->logError( $message, $errFile, $errLine, $ex );

                /**
                 * It runs if:
                 *  - it is globally enabled
                 *  - this error handler is enabled
                 *  - we believe it is a regular html request, or ajax
                 */
                global $_php_error_is_ini_enabled;
                if (
                        $_php_error_is_ini_enabled &&
                        $this->isOn() && (
                                $this->isAjax ||
                                !$this->htmlOnly ||
                                !ErrorHandler::isNonPHPRequest()
                        )
                ) {
                    $root = $this->applicationRoot;

                    list( $ex, $stackTrace, $code, $errFile, $errLine ) =
                            $this->getStackTrace( $ex, $code, $errFile, $errLine );

                    list( $message, $srcErrFile, $srcErrLine, $altInfo ) =
                            $this->improveErrorMessage(
                                    $ex,
                                    $code,
                                    $message,
                                    $errLine,
                                    $errFile,
                                    $root,
                                    $stackTrace
                            );

                    $errFile = $srcErrFile;
                    $errLine = $srcErrLine;

                    list( $fileLinesSets, $numFileLines ) = $this->generateFileLineSets( $srcErrFile, $srcErrLine, $stackTrace );

                    list( $type, $errFile ) = $this->getFolderType( $root, $errFile );
                    $errFileType = ErrorHandler::folderTypeToCSS( $type );

                    $stackTrace = $this->parseStackTrace( $code, $message, $errLine, $errFile, $stackTrace, $root, $altInfo );
                    $fileLines  = $this->readCodeFile( $srcErrFile, $srcErrLine );

                    // load the session, if ...
                    //  - there *is* a session cookie to load
                    //  - the session has not yet been started
                    // Do not start the session without he cookie, because there may be no session ever.
                    if ( isset($_COOKIE[session_name()]) && session_id() === '' ) {
                        session_start();
                    }

                    $request  = ErrorHandler::getRequestHeaders();
                    $response = ErrorHandler::getResponseHeaders();

                    $dump = $this->generateDumpHTML(
                            array(
                                    'post'    => ( isset($_POST)    ? $_POST    : array() ),
                                    'get'     => ( isset($_GET)     ? $_GET     : array() ),
                                    'session' => ( isset($_SESSION) ? $_SESSION : array() ),
                                    'cookies' => ( isset($_COOKIE)  ? $_COOKIE  : array() )
                            ),

                            $request,
                            $response,

                            $_SERVER
                    );
                    $this->displayError( $message, $srcErrLine, $errFile, $errFileType, $stackTrace, $fileLinesSets, $numFileLines, $dump );

                    // exit in order to end processing
                    $this->turnOff();
                    exit(0);
                }
            }

            private function getStackTrace( $ex, $code, $errFile, $errLine ) {
                $stackTrace = null;

                if ( $ex !== null ) {
                    $next = $ex;
                    $stackTrace = array();
                    $skipStacks = 0;

                    for (
                            $next = $ex;
                            $next !== null;
                            $next = $next->getPrevious()
                    ) {
                        $ex = $next;

                        $stack = $ex->getTrace();
                        $file  = $ex->getFile();
                        $line  = $ex->getLine();

                        if ( $stackTrace !== null && count($stackTrace) > 0 ) {
                            $stack = array_slice( $stack, 0, count($stack)-count($stackTrace) + 1 );
                        }

                        if ( count($stack) > 0 && (
                            !isset($stack[0]['file']) ||
                            !isset($stack[0]['line']) ||
                            $stack[0]['file'] !== $file ||
                            $stack[0]['line'] !== $line
                        ) ) {
                            array_unshift( $stack, array(
                                    'file' => $file,
                                    'line' => $line
                            ) );
                        }

                        $stackTrace = ( $stackTrace !== null ) ?
                                array_merge( $stack, $stackTrace ) :
                                $stack ;

                        if ( count($stackTrace) > 0 ) {
                            $stackTrace[0]['exception'] = $ex;
                        }
                    }

                    $message = $ex->getMessage();
                    $errFile = $ex->getFile();
                    $errLine = $ex->getLine();

                    $code = $ex->getCode();

                    if ( method_exists($ex, 'getSeverity') ) {
                        $severity = $ex->getSeverity();

                        if ( $code === 0 && $severity !== 0 && $severity !== null ) {
                            $code = $severity;
                        }
                    }
                }

                return array( $ex, $stackTrace, $code, $errFile, $errLine );
            }

            private function generateDumpHTML( $arrays, $request, $response, $server ) {
                $arrToHtml = function( $name, $array, $css='' ) {
                    $max = 0;

                    foreach ( $array as $e => $v ) {
                        $max = max( $max, strlen( $e ) );
                    }

                    $snippet = "<h2 class='error_dump_header'>$name</h2>";

                    foreach ( $array as $e => $v ) {
                        $e = str_pad( $e, $max, ' ', STR_PAD_RIGHT );

                        $e = htmlentities( $e );
                        $v = ErrorHandler::identifyTypeHTML( $v, 3 );

                        $snippet .= "<div class='error_dump_key'>$e</div><div class='error_dump_mapping'>=&gt;</div><div class='error_dump_value'>$v</div>";
                    }

                    return "<div class='error_dump $css'>$snippet</div>";
                };

                $html = '';
                foreach ( $arrays as $key => $value ) {
                    if ( isset($value) && $value ) {
                        $html .= $arrToHtml( $key, $value );
                    } else {
                        unset($arrays[$key]);
                    }
                }

                return "<div class='error-dumps'>" .
                            $html .
                            $arrToHtml( 'request', $request, 'dump_request' ) .
                            $arrToHtml( 'response', $response, 'dump_response' ) .
                            $arrToHtml( 'server', $server, 'dump_server' ) .
                        "</div>";
            }

            private function generateFileLineSets( $srcErrFile, $srcErrLine, &$stackTrace ) {
                $fileLineID = 1;
                $srcErrID = "file-line-$fileLineID";
                $fileLineID++;


                $lines = $this->getFileContents( $srcErrFile );
                $minSize = count( $lines );

                $srcFileSet = new FileLinesSet( $srcErrFile, $srcErrID, $lines );

                $seenFiles = array( $srcErrFile => $srcFileSet );

                if ( $stackTrace ) {
                    foreach ( $stackTrace as $i => &$trace ) {
                        if ( $trace && isset($trace['file']) && isset($trace['line']) ) {
                            $file = $trace['file'];
                            $line = $trace['line'];

                            if ( isset($seenFiles[$file]) ) {
                                $fileSet = $seenFiles[$file];
                            } else {
                                $traceFileID = "file-line-$fileLineID";

                                $lines = $this->getFileContents( $file );
                                $minSize = max( $minSize, count($lines) );
                                $fileSet = new FileLinesSet( $file, $traceFileID, $lines );

                                $seenFiles[ $file ] = $fileSet;

                                $fileLineID++;
                            }

                            $trace['file-id'] = $fileSet->getHTMLID();
                        }
                    }
                }

                return array( array_values($seenFiles), $minSize );
            }

            /*
             * Even if disabled, we still act like reporting is on,
             * if it's turned on.
             *
             * We just don't do anything.
             */
            private function setEnabled( $isOn ) {
                $wasOn = $this->isOn;
                $this->isOn = $isOn;

                global $_php_error_is_ini_enabled;
                if ( $_php_error_is_ini_enabled ) {
                    /*
                     * Only turn off, if we're moving from on to off.
                     *
                     * This is so if it's turned off without turning on,
                     * we don't change anything.
                     */
                    if ( !$isOn ) {
                        if ( $wasOn ) {
                            $this->runDisableErrors();
                        }
                    /*
                     * Always turn it on, even if already on.
                     *
                     * This is incase it was messed up in some way
                     * by the user.
                     */
                    } else if ( $isOn ) {
                        $this->runEnableErrors();
                    }
                }
            }

            private function runDisableErrors() {
                global $_php_error_is_ini_enabled;

                if ( $_php_error_is_ini_enabled ) {
                    error_reporting( $this->defaultErrorReportingOff );

                    @ini_restore( 'html_errors' );

                    if ( ErrorHandler::isIIS() ) {
                        @ini_restore( 'log_errors' );
                    }
                }
            }

            /*
             * Now the actual hooking into PHP's error reporting.
             *
             * We enable _ALL_ errors, and make them all exceptions.
             * We also need to hook into the shutdown function so
             * we can catch fatal and compile time errors.
             */
            private function runEnableErrors() {
                global $_php_error_is_ini_enabled;

                if ( $_php_error_is_ini_enabled ) {
                    $catchSurpressedErrors = &$this->catchSurpressedErrors;
                    $self = $this;

                    // all errors \o/ !
                    error_reporting( $this->defaultErrorReportingOn );
                    @ini_set( 'html_errors', false );

                    if ( ErrorHandler::isIIS() ) {
                        @ini_set( 'log_errors', false );
                    }

                    set_error_handler(
                            function( $code, $message, $file, $line, $context ) use ( $self, &$catchSurpressedErrors ) {
                                /*
                                 * DO NOT! log the error.
                                 *
                                 * Either it's thrown as an exception, and so logged by the exception handler,
                                 * or we return false, and it's logged by PHP.
                                 *
                                 * Also DO NOT! throw an exception, instead report it.
                                 * This is because if an operation raises both a user AND
                                 * fatal error (such as require), then the exception is
                                 * silently ignored.
                                 */
                                if ( $self->isOn() ) {
                                    /*
                                     * When using an @, the error reporting drops to 0.
                                     */
                                    if ( error_reporting() !== 0 || $catchSurpressedErrors ) {
                                        $ex = new \ErrorException( $message, $code, 0, $file, $line );

                                        $self->reportException( $ex );
                                    }
                                } else {
                                    return false;
                                }
                            },
                            $this->defaultErrorReportingOn
                    );

                    set_exception_handler( function($ex) use ( $self ) {
                        if ( $self->isOn() ) {
                            $self->reportException( $ex );
                        } else {
                            return false;
                        }
                    });

                    if ( ! $self->isShutdownRegistered ) {
                        if ( $self->catchClassNotFound ) {
                            $classException = &$self->classNotFoundException;
                            $autoloaderFuns = ErrorHandler::$SAFE_AUTOLOADER_FUNCTIONS;

                            /*
                             * When this is called, the key point is that we don't error!
                             *
                             * Instead we record that an error has occurred,
                             * if we believe one has, and then let PHP error as normal.
                             * The stack trace we record is then used later.
                             *
                             * This is done for two reasons:
                             *  - functions like 'class_exists' will run the autoloader, and we shouldn't error on them
                             *  - on PHP 5.3.0, the class loader registered functions does *not* return closure objects, so we can't do anything clever.
                             *
                             * So we watch, but don't touch.
                             */
                            spl_autoload_register( function($className) use ( $self, &$classException, &$autoloaderFuns ) {
                                if ( $self->isOn() ) {
                                    $classException = null;

                                    // search the stack first, to check if we are running from 'class_exists' before we error
                                    if ( defined('DEBUG_BACKTRACE_IGNORE_ARGS') ) {
                                        $trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS );
                                    } else {
                                        $trace = debug_backtrace();
                                    }
                                    $error = true;

                                    foreach ( $trace as $row ) {
                                        if ( isset($row['function']) ) {
                                            $function = $row['function'];

                                            // they are just checking, so don't error
                                            if ( in_array($function, $autoloaderFuns, true) ) {
                                                $error = false;
                                                break;
                                            // not us, and not the autoloader, so error!
                                            } else if (
                                                    $function !== '__autoload' &&
                                                    $function !== 'spl_autoload_call' &&
                                                    strpos($function, 'php_error\\') === false
                                            ) {
                                                break;
                                            }
                                        }
                                    }

                                    if ( $error ) {
                                        $classException = new \ErrorException( "Class '$className' not found", E_ERROR, 0, __FILE__, __LINE__ );
                                    }
                                }
                            } );
                        }

                        $self->isShutdownRegistered = true;
                    }
                }
            }

  private function displayJSInjection() {
    
  }

  private function displayError( $message, $errLine, $errFile, $errFileType, $stackTrace, &$fileLinesSets, $numFileLines, $dumpInfo ) {
    if ( $this->isAjax ) {
      return false;
    }
    $applicationRoot   = $this->applicationRoot;
    $serverName        = $this->serverName;
    $backgroundText    = $this->backgroundText;
    $displayLineNumber = $this->displayLineNumber;
    $saveUrl           = $this->saveUrl;
    $isSavingEnabled   = $this->isSavingEnabled;

    if ( isset( $_SERVER['QUERY_STRING'] ) ) {
      $requestUrl     = str_replace( $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] );
      $requestUrlLen  = strlen( $requestUrl );

      if ( $requestUrlLen > 0 && substr( $requestUrl, $requestUrlLen-1 ) === '?' ) {
        $requestUrl = substr( $requestUrl, 0, $requestUrlLen-1 );
      }
    }
    else {
      $requestUrl = $_SERVER['REQUEST_URI'];
    }
    header_remove( 'Content-Transfer-Encoding' );
    $this->displayHTML(
      function() use( $message, $errFile, $errLine ) {
        echo "  <!-- $message | $errFile, $errLine -->\n";
      },
      function() use (
        $requestUrl,
        $backgroundText,
        $serverName,
        $applicationRoot,
        $message,
        $errLine,
        $errFile,
        $errFileType,
        $stackTrace,
        &$fileLinesSets,
        $numFileLines,
        $displayLineNumber,
        $dumpInfo,
        $isSavingEnabled
      ) {
        if ( $backgroundText ) {
?>
  <div id="error-wrap">
    <div id="error-back"><?php echo $backgroundText ?></div>
  </div>
<?php
        }
?>
  <h1 id="error-title"><?php echo self::is_dangerous( $message ); ?></h1>
<?php
        if ( $stackTrace !== null ) {
          echo $stackTrace;
        }
        if ( $dumpInfo !== null ) {
          //echo $dumpInfo;
        }
      },
      function() use ( $errFile, $errLine ) {
        return array( $errFile, $errLine );
      }
    );
  }
  function displayHTML( Closure $head, $body = null, $javascript = null, $infos = null ) {
    if ( func_num_args() === 2 ) {
      $body = $head;
      $head = null;
    }
    try {
      @ob_clean();
    }
    catch ( Exception $ex ) {
      
    }
    if ( !$this->htmlOnly && ErrorHandler::isNonPHPRequest() ) {
      @header( "Content-Type: text/html", true );
    }
    @header( ErrorHandler::PHP_ERROR_MAGIC_HEADER_KEY.': '.ErrorHandler::PHP_ERROR_MAGIC_HEADER_VALUE );
?>
<!DOCTYPE html>
<html>
  <head>
<?php
    if ( $head !== null ) {
      $head();
    }
?>
  <!--<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>-->
  <style>
    html,body{margin:0;padding:0;width:100%;height:100%}body{color:#666;tab-size:4}::-moz-selection{background:#fd9f92!important;color:#fff!important;text-shadow:none}::selection{background:#fd9f92!important;color:#fff!important;text-shadow:none}a,.error-stack-trace-line{-webkit-transition:color 120ms linear,background 120ms linear;-moz-transition:color 120ms linear,background 120ms linear;-ms-transition:color 120ms linear,background 120ms linear;-o-transition:color 120ms linear,background 120ms linear;transition:color 120ms linear,background 120ms linear}a,a:visited,a:hover,a:active{color:#fa6844;text-decoration:none}a:hover,a.error-editor-link:hover{color:#ec451b}a.error-editor-link,a.error-editor-link:visited,a.error-editor-link:active{color:inherit}
    h2,.background{font-size:16px;font-family:inconsolata,'Droid Sans Mono',"DejaVu Sans Mono",consolas,monospace}.background{line-height:18px;width:100%;padding:18px 24px;-moz-box-sizing:border-box;box-sizing:border-box;position:fixed;top:0;left:0;right:0;bottom:45px;z-index:100000;overflow:auto}h1,h2{font-family:"Segoe UI Light","Helvetica Neue",'RobotoLight',"Segoe UI","Segoe WP",sans-serif;font-weight:100;line-height:normal}h1{font-size:42px;margin-bottom:0}h2{font-size:28px;margin-top:0}
    #error-title{margin:0;margin-bottom:30px;position:relative;white-space:normal;word-wrap:break-word;color:#e93d12;line-height:1.2em}#error-file-root{color:#666;position:relative}#error-stack-trace,.error-stack-trace-line{border-spacing:0;width:100%}#error-stack-trace{position:relative;line-height:28px;cursor:pointer}.error-stack-trace-exception{color:#b33}.error-stack-trace-exception>td{padding-top:18px}.error-stack-trace-line.highlight{background:#fde5e0}.muted{color:#ccc}.error-stack-trace-line.is-native{background:#e5eef0}.error-stack-trace-line.is-exception{margin-top:18px;border-top:1px solid #422}.error-stack-trace-line:first-of-type>td:first-of-type{border-top-left-radius:2px}.error-stack-trace-line:first-of-type>td:last-of-type{border-top-right-radius:2px}.error-stack-trace-line:last-of-type>td:first-of-type{border-bottom-left-radius:2px}.error-stack-trace-line:last-of-type>td:last-of-type{border-bottom-right-radius:2px}.error-stack-trace-line>td{padding:3px 0;vertical-align:top}.error-stack-trace-line>.linenumber,.error-stack-trace-line>.filename,.error-stack-trace-line>.file-internal-php,.error-stack-trace-line>.lineinfo{padding-left:18px;padding-right:12px}.error-stack-trace-line>.linenumber,.error-stack-trace-line>.file-internal-php,.error-stack-trace-line>.filename{white-space:pre}.error-stack-trace-line>.linenumber{text-align:right}.error-stack-trace-line>.lineinfo{padding-right:18px;padding-left:0;text-indent:0}.error-dumps{position:relative;margin-top:20px;padding-top:20px;width:100%;max-width:100%;overflow:hidden;border-top:1px solid #CCC}.error_dump{float:left;clear:none;-moz-box-sizing:border-box;box-sizing:border-box;padding:0 32px 24px 12px;max-width:100%}.error_dump.dump_request{clear:left;max-width:50%;min-width:600px}.error_dump.dump_response{max-width:50%;min-width:600px}.error_dump.dump_server{width:100%;clear:both}.error_dump_header{color:#eb3800;margin:0;margin-left:-6px}.error_dump_key,.error_dump_mapping,.error_dump_value{white-space:pre;padding:3px 6px 3px 6px;float:left}.error_dump_key{clear:left}.error_dump_mapping{padding:3px 12px}.error_dump_value{clear:right;white-space:normal;max-width:100%}.syntax-class{color:#c07041}.syntax-function{color:#008000}.syntax-literal{color:#8000ff}.syntax-string{color:#f00}.syntax-variable-not-important{opacity:.5}.syntax-higlight-variable{color:#f00;border-bottom:3px dashed #c33}.syntax-variable{color:#06c}.syntax-keyword{color:#c07041}.syntax-comment{color:#5a5a5a}.file-internal-php{color:#555!important}.file-common{color:#ce1b0c}.file-ignore{color:#585}.file-app{color:#66c6d5}.file-root{color:#b69}
    .footer{margin:0;text-align:center;border-top:1px solid #CCC;font-size:18px;font-weight:bold;padding:10px;font-family:'Segoe UI Light','Helvetica Neue','RobotoLight','Segoe UI','Segoe WP',sans-serif;background:#EEE;position:fixed;left:0;bottom:0;right:0;line-height:20px;-webkit-box-shadow:0 0 10px #CCC;-moz-box-shadow:0 0 10px #CCC;-o-box-shadow:0 0 10px #CCC;box-shadow:0 0 10px #CCC}
    .source-line-num {
      float: left;
    }
    .source-line {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      text-shadow: 0 0 5px #333;
      color: #ddd;
    }
    .clearfix{*zoom:1;}.clearfix:before,.clearfix:after{display:table;content:"";line-height:0;}
    hr {
      border: 0 none;
      border-bottom: 1px solid #eee;
    }
    pre.error-line-explored {
      color: #CCC;
      padding: 10px;
      white-space: pre-wrap;
    }
    .source-lines {
      display: block;
      padding: 2px;
    }
    .source-lines.highlighted-lines {
      color: #333;
      background: #fff;
      -webkit-box-shadow: 0 0 10px #AAA;
      box-shadow: 0 0 10px #AAA;
      box-shadow: 0 0 10px #AAA;
      box-shadow: 0 0 10px #AAA;
      border: 1px solid #AAA;
    }
    .source-lines.highlighted-lines .source-line {
      color: #666;
      text-shadow: 0 0 0 transparent !important;
    }
  </style>
  </head>
  <body>
    <div class="background">
      <?php
        $body();
        $file = $this->cachedFiles;
        if ( is_array( $file ) ) {
          reset( $file );
          $key  = key( $file );
          $lines  = $file[$key];
          $stack  = $javascript();
          if ( !empty( $lines ) && !empty( $stack ) && isset( $stack[1] ) && !empty( $stack[1] ) ) {
            $total_lines  = count( $lines );
            $error_line   = $stack[1];
            $start_index  = ( $error_line - 5 );
            $start_index  = ( $start_index < 0 ) ? 0 : $start_index;
            $stop_index   = ( $error_line + 5 );
            $stop_index   = ( $stop_index > $total_lines ) ? $total_lines : $stop_index;
            $stop_index   = ( $stop_index - $start_index );
            $finalized    = array();
            $lines        = array_splice( $lines, $start_index, $stop_index );
            if ( !empty( $lines ) ) {
              $start_index++;
              foreach( $lines as $line ) {
                $line_num = ( strlen( $error_line ) > strlen( $start_index ) ) ? sprintf( "%0".( strlen( $error_line ) )."s", $start_index ) : $start_index;
                if ( $error_line === $start_index ) {
                  $syntax = ErrorHandler::syntaxHighlight( $line );
                  $space_before = ( strlen( $line ) - strlen( ltrim( $line ) ) - 1 );
                  $line   = str_repeat( "&nbsp;", max( 1, $space_before ) ).str_ireplace( trim( $line ), $syntax, $line );
                  $finalized[]  = '<div class="source-lines highlighted-lines clearfix"><div class="source-line-num">'.$line_num.'</div><div class="source-line">'.$line.'</div></div>';
                }
                else {
                  $space_before = ( strlen( $line ) - strlen( ltrim( $line ) ) - 1 );
                  $line = htmlentities( $line );
                  $line = str_replace( ' ', '&nbsp;', $line );
                  $line = str_repeat( "&nbsp;", max( 1, $space_before ) ).$line;
                  $finalized[]  = '<div class="source-lines clearfix"><div class="source-line-num">'.$line_num.'</div><div class="source-line">'.$line.'</div></div>';
                }
                $start_index++;
              }
            }
            $finalized  = implode( "", $finalized );
            echo '<hr /><h3 style="color:#E93D12;">Error Trace <span style="color:#666;">('.basename( $stack[0] ).':'.$error_line.')</span></h3>';
            echo '<pre class="error-line-explored clearfix">'.$finalized.'</pre>';
          }
        }
      ?>
      <div class="footer">
        <a href="javascript:window.history.back();" style="vertical-align: middle;">Go Back</a>
        <span class="muted" style="vertical-align: middle;">|</span>
        <a href="javascript:window.location.reload();" style="vertical-align: middle;">Reload</a>
      </div>
    </div>
  </body>
</html>
<?php
    }
  }

        /**
         * Code is outputted multiple times, for each file involved.
         * This allows us to wrap up a single set of code.
         */
        class FileLinesSet
        {
            private $src;
            private $id;
            private $lines;

            public function __construct( $src, $id, array $lines ) {
                $this->src   = $src;
                $this->id    = $id;
                $this->lines = $lines;
            }

            public function getSrc() {
                return $this->src;
            }

            public function getHTMLID() {
                return $this->id;
            }

            public function getLines() {
                return $this->lines;
            }

            public function getContent() {
                return implode( "\n", $this->lines );
            }
        }

        /**
         * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
         *
         * This is pretty much a direct port of jsmin.c to PHP with just a few
         * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
         * outputs to stdout, this library accepts a string as input and returns another
         * string as output.
         *
         * PHP 5 or higher is required.
         *
         * Permission is hereby granted to use this version of the library under the
         * same terms as jsmin.c, which has the following license:
         *
         * --
         * Copyright (c) 2002 Douglas Crockford (www.crockford.com)
         *
         * Permission is hereby granted, free of charge, to any person obtaining a copy of
         * this software and associated documentation files (the "Software"), to deal in
         * the Software without restriction, including without limitation the rights to
         * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
         * of the Software, and to permit persons to whom the Software is furnished to do
         * so, subject to the following conditions:
         *
         * The above copyright notice and this permission notice shall be included in all
         * copies or substantial portions of the Software.
         *
         * The Software shall be used for Good, not Evil.
         *
         * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
         * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
         * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
         * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
         * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
         * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
         * SOFTWARE.
         * --
         *
         * @package JSMin
         * @author Ryan Grove <ryan@wonko.com>
         * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
         * @copyright 2008 Ryan Grove <ryan@wonko.com> (PHP port)
         * @copyright 2012 Adam Goforth <aag@adamgoforth.com> (Updates)
         * @license http://opensource.org/licenses/mit-license.php MIT License
         * @version 1.1.2 (2012-05-01)
         * @link https://github.com/rgrove/jsmin-php
         */
        class JSMin
        {
            const ORD_LF = 10;
            const ORD_SPACE = 32;
            const ACTION_KEEP_A = 1;
            const ACTION_DELETE_A = 2;
            const ACTION_DELETE_A_B = 3;

            protected $a = '';
            protected $b = '';
            protected $input = '';
            protected $inputIndex = 0;
            protected $inputLength = 0;
            protected $lookAhead = null;
            protected $output = '';

            // -- Public Static Methods --------------------------------------------------

            /**
             * Minify Javascript
             *
             * @uses __construct()
             * @uses min()
             * @param string $js Javascript to be minified
             * @return string
             */
            public static function minify($js) {
                $jsmin = new JSMin($js);
                return $jsmin->min();
            }

            // -- Public Instance Methods ------------------------------------------------

            /**
             * Constructor
             *
             * @param string $input Javascript to be minified
             */
            public function __construct($input) {
                $this->input = str_replace("\r\n", "\n", $input);
                $this->inputLength = strlen($this->input);
            }

            // -- Protected Instance Methods ---------------------------------------------

            /**
             * Action -- do something! What to do is determined by the $command argument.
             *
             * action treats a string as a single character. Wow!
             * action recognizes a regular expression if it is preceded by ( or , or =.
             *
             * @uses next()
             * @uses get()
             * @throws JSMinException If parser errors are found:
             * - Unterminated string literal
             * - Unterminated regular expression set in regex literal
             * - Unterminated regular expression literal
             * @param int $command One of class constants:
             * ACTION_KEEP_A Output A. Copy B to A. Get the next B.
             * ACTION_DELETE_A Copy B to A. Get the next B. (Delete A).
             * ACTION_DELETE_A_B Get the next B. (Delete B).
             */
            protected function action($command) {
                switch($command) {
                    case self::ACTION_KEEP_A:
                        $this->output .= $this->a;

                    case self::ACTION_DELETE_A:
                        $this->a = $this->b;

                        if ($this->a === "'" || $this->a === '"') {
                            for (;;) {
                                $this->output .= $this->a;
                                $this->a = $this->get();

                                if ($this->a === $this->b) {
                                    break;
                                }

                                if (ord($this->a) <= self::ORD_LF) {
                                    throw new JSMinException('Unterminated string literal.');
                                }

                                if ($this->a === '\\') {
                                    $this->output .= $this->a;
                                    $this->a = $this->get();
                                }
                            }
                        }

                    case self::ACTION_DELETE_A_B:
                        $this->b = $this->next();

                        if ($this->b === '/' && (
                                $this->a === '(' || $this->a === ',' || $this->a === '=' ||
                                $this->a === ':' || $this->a === '[' || $this->a === '!' ||
                                $this->a === '&' || $this->a === '|' || $this->a === '?' ||
                                $this->a === '{' || $this->a === '}' || $this->a === ';' ||
                                $this->a === "\n" )) {

                            $this->output .= $this->a . $this->b;

                            for (;;) {
                                $this->a = $this->get();

                                if ($this->a === '[') {
                                    /*
        inside a regex [...] set, which MAY contain a '/' itself. Example: mootools Form.Validator near line 460:
        return Form.Validator.getValidator('IsEmpty').test(element) || (/^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test(element.get('value'));
             */
                                    for (;;) {
                                        $this->output .= $this->a;
                                        $this->a = $this->get();

                                        if ($this->a === ']') {
                                                break;
                                        } elseif ($this->a === '\\') {
                                            $this->output .= $this->a;
                                            $this->a = $this->get();
                                        } elseif (ord($this->a) <= self::ORD_LF) {
                                            throw new JSMinException('Unterminated regular expression set in regex literal.');
                                        }
                                    }
                                } elseif ($this->a === '/') {
                                    break;
                                } elseif ($this->a === '\\') {
                                    $this->output .= $this->a;
                                    $this->a = $this->get();
                                } elseif (ord($this->a) <= self::ORD_LF) {
                                    throw new JSMinException('Unterminated regular expression literal.');
                                }

                                $this->output .= $this->a;
                            }

                            $this->b = $this->next();
                        }
                }
            }

            /**
             * Get next char. Convert ctrl char to space.
             *
             * @return string|null
             */
            protected function get() {
                $c = $this->lookAhead;
                $this->lookAhead = null;

                if ($c === null) {
                    if ($this->inputIndex < $this->inputLength) {
                        $c = substr($this->input, $this->inputIndex, 1);
                        $this->inputIndex += 1;
                    } else {
                        $c = null;
                    }
                }

                if ($c === "\r") {
                    return "\n";
                }

                if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
                    return $c;
                }

                return ' ';
            }

            /**
             * Is $c a letter, digit, underscore, dollar sign, or non-ASCII character.
             *
             * @return bool
             */
            protected function isAlphaNum($c) {
                return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
            }

            /**
             * Perform minification, return result
             *
             * @uses action()
             * @uses isAlphaNum()
             * @uses get()
             * @uses peek()
             * @return string
             */
            protected function min() {
                if (0 == strncmp($this->peek(), "\xef", 1)) {
                        $this->get();
                        $this->get();
                        $this->get();
                }

                $this->a = "\n";
                $this->action(self::ACTION_DELETE_A_B);

                while ($this->a !== null) {
                    switch ($this->a) {
                        case ' ':
                            if ($this->isAlphaNum($this->b)) {
                                $this->action(self::ACTION_KEEP_A);
                            } else {
                                $this->action(self::ACTION_DELETE_A);
                            }
                            break;

                        case "\n":
                            switch ($this->b) {
                                case '{':
                                case '[':
                                case '(':
                                case '+':
                                case '-':
                                case '!':
                                case '~':
                                    $this->action(self::ACTION_KEEP_A);
                                    break;

                                case ' ':
                                    $this->action(self::ACTION_DELETE_A_B);
                                    break;

                                default:
                                    if ($this->isAlphaNum($this->b)) {
                                        $this->action(self::ACTION_KEEP_A);
                                    }
                                    else {
                                        $this->action(self::ACTION_DELETE_A);
                                    }
                            }
                            break;

                        default:
                            switch ($this->b) {
                                case ' ':
                                    if ($this->isAlphaNum($this->a)) {
                                        $this->action(self::ACTION_KEEP_A);
                                        break;
                                    }

                                    $this->action(self::ACTION_DELETE_A_B);
                                    break;

                                case "\n":
                                    switch ($this->a) {
                                        case '}':
                                        case ']':
                                        case ')':
                                        case '+':
                                        case '-':
                                        case '"':
                                        case "'":
                                            $this->action(self::ACTION_KEEP_A);
                                            break;

                                        default:
                                            if ($this->isAlphaNum($this->a)) {
                                                $this->action(self::ACTION_KEEP_A);
                                            }
                                            else {
                                                $this->action(self::ACTION_DELETE_A_B);
                                            }
                                    }
                                    break;

                                default:
                                    $this->action(self::ACTION_KEEP_A);
                                    break;
                            }
                    }
                }

                return $this->output;
            }

            /**
             * Get the next character, skipping over comments. peek() is used to see
             * if a '/' is followed by a '/' or '*'.
             *
             * @uses get()
             * @uses peek()
             * @throws JSMinException On unterminated comment.
             * @return string
             */
            protected function next() {
                $c = $this->get();

                if ($c === '/') {
                    switch($this->peek()) {
                        case '/':
                            for (;;) {
                                $c = $this->get();

                                if (ord($c) <= self::ORD_LF) {
                                    return $c;
                                }
                            }

                        case '*':
                            $this->get();

                            for (;;) {
                                switch($this->get()) {
                                    case '*':
                                        if ($this->peek() === '/') {
                                            $this->get();
                                            return ' ';
                                        }
                                        break;

                                    case null:
                                        throw new JSMinException('Unterminated comment.');
                                }
                            }

                        default:
                            return $c;
                    }
                }

                return $c;
            }

            /**
             * Get next char. If is ctrl character, translate to a space or newline.
             *
             * @uses get()
             * @return string|null
             */
            protected function peek() {
                $this->lookAhead = $this->get();
                return $this->lookAhead;
            }
        }

        // -- Exceptions ---------------------------------------------------------------
        class JSMinException extends Exception {}

        if (
                $_php_error_is_ini_enabled &&
                $_php_error_global_handler === null &&
                @get_cfg_var('php_error.autorun')
        ) {
            reportErrors();
        }
    }

function ellipses( $string = null, $maxlen = 10 ) {
  $maxlen = ( $maxlen < 10 ) ? 10 : $maxlen;
  $string = trim( htmlspecialchars( $string ) );
  if ( !$string ) {
    return $string;
  }
  $length = strlen( $string );
  if ( $length > $maxlen ) {
    $maxlen = ( $maxlen - 2 );
    $middle = round( $length / 2 );
    $str1   = substr( $string, 0, ( $maxlen / 2 ) );
    $str2   = substr( $string, "-".( $maxlen / 2 ) );
    return $str1."...".$str2;
  }
  return $string;
}
?>
<?php

/*!
  Copyright 2013 The Impact Plus. All rights reserved.

  YOU ARE PERMITTED TO:
  * Transfer the Software and license to another party if the other party agrees to accept the terms and conditions of this License Agreement. The license holder is responsible for a transfer fee of $50.95 USD. The license must be at least 90 days old or not transferred within the last 90 days;
  * Modify source codes of the software and add new functionality that does not violate the terms of the current license;
  * Customize the Software's design and operation to suit the internal needs of your web site except to the extent not permitted under this Agreement;
  * Create, sell and distribute applications/modules/plugins which interface (not derivative works) with the operation of the Software provided the said applications/modules/plugins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement;
  * Create, sell and distribute by any means any templates and/or designs/skins which allow you or other users of the Software to customize the appearance of Impact Plus provided the said templates and or designs/skins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement.

  YOU ARE "NOT" PERMITTED TO:
  * Use the Software in violation of any US/India or international law or regulation.
  * Permit other individuals to use the Software except under the terms listed above;
  * Reverse-engineer and/or disassemble the Software for distribution or usage outside your domain if it is not an unlimited licence version;
  * Use the Software in such as way as to condone or encourage terrorism, promote or provide pirated Software, or any other form of illegal or damaging activity;
  * Distribute individual copies of proprietary files, libraries, or other programming material in the Software package.
  * Distribute or modify proprietary graphics, HTML, or CSS packaged with the Software for use in applications other than the Software;
  * Use the Software in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Modify the software and/or create applications and modules which allow the Software to function in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Copy the Software and install that single program for simultaneous use on multiple machines without prior written consent from IMPACT PLUS;
*/

if ( !class_exists( "ipCurl" ) ) {
  require_once( dirname( dirname( __FILE__ ) )."/curl/curl.class.php" );
}
if ( !class_exists( "JavaScriptPacker" ) ) {
  require_once( dirname( __FILE__ )."/jspacker.class.php" );
}
if ( !function_exists( "str_get_html" ) ) {
  require_once( dirname( __FILE__ )."/htmlparser.class.php" );
}
if ( !class_exists( "ipPasswordGen" ) ) {
  //require_once( dirname( __FILE__ )."/hasher.class.php" );
}
if ( !class_exists( "JSMinPlus" ) ) {
  require_once( dirname( __FILE__ )."/jsminplus.class.php" );
}

class ipWiseLoop {
  private static $url = "http://www.wiseloop.com/wl.cms/storage/user/wiseloop/php-javascript-obfuscator/bin/jso.php";
  private static $ref = "http://www.wiseloop.com/demo/php-javascript-obfuscator";
  private static $par = "js=%s&doDecoy=%s&doMinify=%s&doLockDomain=%s&doScrambleVars=%s&encryptionLevel=%d";

  public static function obfuscate( $js = "", $decoy = false, $minify = false, $lock = false, $scramble = true, $level = 1 ) {
    if ( !trim( $js ) ) {
      return null;
    }
    $par  = self::post_params( $js, $decoy, $minify, $lock, $scramble, $level );
    $curl = new ipCurl( self::$url );
    $curl->setReferer( self::$ref );
    $curl->setPost( $par );
    $curl->createCurl();

    if ( $curl->getError() === 0 ) {
      if ( $response = trim( $curl->getResponse() ) ) {
        return $response;
      }
    }
    return null;
  }

  private static function post_params( $js = "", $decoy = false, $minify = false, $lock = false, $scramble = true, $level = 1 ) {
    return sprintf( self::$par, urlencode( $js ), self::bool( $decoy ), self::bool( $minify ), self::bool( $lock ), self::bool( $scramble ), (int)$level );
  }

  private static function bool( $bool = true ) {
    return ( $bool ) ? "true" : "false";
  }
}

class ipUglifyJS {
  private static $url = "http://marijnhaverbeke.nl/uglifyjs";
  private static $ref = "http://marijnhaverbeke.nl/uglifyjs";

  public static function obfuscate( $js = "", $utf8 = true, $source_map = false, $download = false ) {
    if ( !trim( $js ) ) {
      return null;
    }
    $par  = self::post_params( $js, $utf8, $source_map, $download );
    $curl = new ipCurl( self::$url );
    $curl->setReferer( self::$ref );
    $curl->setPost( $par );
    $curl->createCurl();

    if ( $curl->getError() === 0 ) {
      if ( $response = trim( $curl->getResponse() ) ) {
        return $response;
      }
    }
    return null;
  }

  private static function post_params( $js = "", $utf8 = false, $source_map = false, $download = false ) {
    $par  = array();
    if ( parse_url( $js, PHP_URL_SCHEME ) ) {
      $par["code_url"]  = $js;
    }
    else {
      $par["js_code"] = $js;
    }
    if ( $utf8 ) {
      $par["utf8"]  = "true";
    }
    if ( $source_map ) {
      $par["source_map"]  = "true";
    }
    if ( $download ) {
      $par["download"]  = $download;
    }
    return http_build_query( $par );
  }
}

class ipJavascriptObfuscator {
  private static $url = "http://www.javascriptobfuscator.com/";
  private static $ref = "http://www.javascriptobfuscator.com/";
  private static $vie = '/wEPDwUKLTI0MDAwODAzNmQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgYFCGNiTGluZUJSBQhjYkluZGVudAULY2JFbmNvZGVTdHIFDmNiRW5jb2RlTnVtYmVyBQljYk1vdmVTdHIFDmNiUmVwbGFjZU5hbWVzkhu7ipx09t7ORqMSiqXAZjxixpvev5qGduB5U7lxKAA=';
  private static $eve = '/wEdAAtVzcjdONxmP98Zn2VeKgWWESCFkFW/RuhzY1oLb/NUVB2nXP6dhZn6mKtmTGNHd3PN+DvxnwFeFeJ9MIBWR693/0+kJGcigziRf+JnyYP3ngWOnPKUhxuCfOKb0tlvVuly5juiFHJSf6q9cXRA/+LsCzkidEk0Y8qCyJLcOKXNoEywswNt0lfddYqrIj/HYv1fNaBSlQ4gCFEJtbofwBY37hv76BH8vu7iM4tkb8en1RGDlH5soHS6hWUl4JVZYtSZ51XOVy0Wuo6R2616LTDx';
  private static $pars = array(
    "__VIEWSTATE" =>  '',
    "__EVENTVALIDATION" =>  '',
    "TextBox1"  =>  "",
    "TextBox2"  =>  "",
    "Button1"   =>  "Obfuscate",
    "cbEncodeStr" =>  "on",
    "cbEncodeNumber"  =>  "on",
    "cbMoveStr" =>  "on",
    "cbReplaceNames"  =>  "on",
    "TextBox3"  =>  "\n^_get_\n^_set_\n^_mtd_\n"
  );
  private static $par = "TextBox1=%s&TextBox2=&Button1=Obfuscate&cbEncodeStr=%s&cbEncodeNumber=%s&cbMoveStr=%s&cbReplaceNames=%s&TextBox3=%s&__VIEWSTATE=%s&__EVENTVALIDATION=%s";

  public static function obfuscate( $js = "", $str = true, $num = true, $move = true, $rep = true, $txt = "\n^_get_\n^_set_\n^_mtd_\n" ) {
    if ( !trim( $js ) ) {
      return null;
    }
    $par  = self::post_params( $js, $str, $num, $move, $rep, $txt );
    $curl = new ipCurl( self::$url );
    $curl->setReferer( self::$ref );
    $curl->setPost( $par );
    $curl->setTimout( 0 );
    $curl->createCurl();

    if ( $curl->getError() === 0 ) {
      if ( $response = trim( $curl->getResponse() ) ) {
        return self::parse( $response );
      }
    }
    return null; 
  }

  private static function parse( $html = null ) {
    if ( !$html ) {
      return null;
    }
    if ( preg_match( "/JScriptCodeDom/", $html ) ) {
      return '/**
 * alright dude ! You are screwed :P
**/';
    }
    if ( $html = str_get_html( $html ) ) {
      if ( $code = $html->find( "textarea#TextBox2", 0 ) ) {
        $res  = trim( htmlspecialchars_decode( $code->innertext ) );
        return $res;
      }
    }
    return null;
  }

  private static function post_params( $j = "", $s = true, $n = true, $m = true, $r = true, $t = "\n^_get_\n^_set_\n^_mtd_\n" ) {
    $v  = urlencode( self::$vie );
    $e  = urlencode( self::$eve );
    $u  = sprintf( self::$par, urlencode( $j ), self::str( $s ), self::str( $n ), self::str( $m ), self::str( $r ), urlencode( $t ), $v, $e );
    return $u;
  }

  private static function str( $str = true ) {
    return ( $str ) ? "on" : "off";
  }
}

class ipPackerJS {
  public static function obfuscate( $js = "", $encoding = 62, $fast = false, $chars = false ) {
    if ( !trim( $js ) ) {
      return null;
    }
    $packer = new JavaScriptPacker( $js, $encoding, $fast, $chars );
    return $packer->pack();
  }
}

class ipObfuscator {
  public static function init( $js = "", $obfuscate = true ) {
    if ( !$js ) {
      return '';
    }

    if ( $packer = ipPackerJS::obfuscate( $js ) ) {
      if ( $obfuscate ) {
        if ( $packer = ipJavascriptObfuscator::obfuscate( $packer ) ) {
          return ipPackerJS::obfuscate( $packer );
        }
        return '';
      }
      else {
        return $packer;
      }
    }

    return '';
  }
}

class ipObfuscatorUnpacker {
  private static $hex_regex   = '/((\\\x)(\d\w))+/';
  private static $g_regex     = '/_0x([a-z\d]+)/';
  private static $gkey_regex  = '/^var(\s+)?_0x([a-z\d]+)(\s+)?=/';
  private static $gline_regex = '/^var(\s+)?(_0x[a-z\d]+)(\s+)?\=(\s+)?\["(.*?)"\];/';
  private static $replace_obj = '/\[%s\[(\d+)\]\]/';
  private static $replace_str = '/%s\[((\d+))\]/';

  private static $source  = null;
  private static $parsed  = null;

  private static $base_id = null;
  private static $strings = array();
  private static $gbl_var = array();

  private static $counter = 1;

  public static function run( $js = null ) {
    self::$source = $js;

    if ( !self::detect() ) {
      return self::$source;
    }

    self::unpack();
    self::sanitize();
    self::finalize();

    return self::$parsed;
  }

  private static function detect() {
    if ( preg_match( self::$gkey_regex, self::$source ) ) {
      return true;
    }
    return false;
  }

  private static function unpack() {
    preg_match( self::$gline_regex, self::$source, $strings );
    if ( $strings ) {
      self::$base_id  = trim( $strings[2] );
      self::$replace_obj  = sprintf( self::$replace_obj, self::$base_id  );
      self::$replace_str  = sprintf( self::$replace_str, self::$base_id  );
      self::parse_strings( explode( '","', trim( $strings[5] ) ) );
    }
  }

  private static function parse_strings( $strings = array() ) {
    if ( empty( $strings ) ) {
      return false;
    }
    foreach( $strings as &$word ) {
      $word = self::preserve_lb( self::parse_char( $word, "" ) );
    }
    self::$strings  = $strings;
    unset( $strings );
  }

  private static function parse_char( $hex, $glue = false ) {
    $chars  = explode( " ", trim( str_ireplace( '\x', ' ', $hex ) ) );
    foreach( $chars as &$char ) {
      $char = ( !empty( $char ) ) ? self::hexchar( $char ) : $char;
      //preg_match( '("|\')(.*)([\n\r]+)(.*)("|\')', $char );
    }
    if ( $glue !== false ) {
      return implode( $glue, $chars );
    }
    return $chars;
  }

  private static function preserve_lb( $string = null ) {
    if ( !$string || nl2br( $string ) === $string ) {
      return $string;
    }
    $string = str_ireplace( array( "\n", "\r", "\r\n", "\n\r" ), array( '\n', '\r', '\r\n', '\n\r' ), $string );
    return $string;
  }

  private static function hexchar( $hex ) {
    return chr( hexdec( $hex ) );
  }

  private static function charhex( $char ) {
    $hex  = '';
    for( $i = 0; $i < strlen( $char ); $i++ ) {
      $hex  .=  '\x'.dechex( ord( $char[$i] ) );
    }
    return $hex;
  }

  private static function sanitize() {
    self::$parsed = preg_replace( self::$gline_regex, "", self::$source );
  }

  private static function finalize() {
    self::replace_obj();
    self::replace_str();
    self::replace_var();
    self::replace_hex();
  }

  private static function replace_obj() {
    if ( !self::$base_id ) {
      return false;
    }
    self::$parsed = preg_replace_callback( self::$replace_obj, function( &$matches ) {
      $key  = (int)$matches[1];
      if ( isset( self::$strings[$key] ) ) {
        return ".".self::$strings[$key];
      }
    }, self::$parsed );
  }

  private static function replace_str() {
    if ( !self::$base_id ) {
      return false;
    }
    self::$parsed = preg_replace_callback( self::$replace_str, function( &$matches ) {
      $key  = (int)$matches[1];
      if ( isset( self::$strings[$key] ) ) {
        $q  = self::safe_quotes( self::$strings[$key] );
        return $q.self::$strings[$key].$q;
      }
    }, self::$parsed );
  }

  private static function safe_quotes( $str = null ) {
    if ( !$str ) {
      return '"';
    }
    return ( stristr( $str, '"' ) !== false ) ? "'" : '"';
  }

  private static function replace_var() {
    self::$parsed = preg_replace_callback( self::$g_regex, function( &$matches ) {
      $var  = trim( $matches[0] );
      if ( !isset( self::$gbl_var[$var] ) ) {
        self::generate_var( $var );
      }
      return self::$gbl_var[$var];
    }, self::$parsed );
  }

  private static function replace_hex() {
    self::$parsed = preg_replace_callback( self::$hex_regex, function( &$matches ) {
      $word = implode( "", self::parse_char( $matches[0] ) );
      return $word;
    }, self::$parsed );
  }

  private static function generate_var( $var = null ) {
    if ( !$var ) {
      return false;
    }
    self::$gbl_var[$var]  = "var".self::$counter;
    self::$counter++;
  }

  public static function get() {
    return self::$parsed;
  }

  public static function source() {
    return self::$source;
  }
}

class ipPackerUnpacker {
  private static $packed  = null;
  private static $unpacked  = null;

  private static $func_regex  = '/eval\(function\(([,\w\d]+)\)(\s+)?\{(.*)return (\w)\}\((.*)\)/';
  private static $args_regex  = '/^(.*),([\d]+),([\d]+)\,(.*)\.split(.*)/';

  private static $function  = false;
  private static $arguments = false;
  private static $args_str  = false;

  private static $start = 0;
  private static $end   = 0;

  public static function run( $js = null ) {
    self::$start  = microtime( true );
    self::$packed = $js;
    if ( !self::$packed ) {
      return self::$packed;
    }
    if ( !self::detect() ) {
      return self::$packed;
    }
    self::parse();
    self::$end  = microtime( true );
    return self::$unpacked;
  }

  private static function detect() {
    preg_match( self::$func_regex, self::$packed, $matches );

    if ( $matches ) {
      self::$function   = trim( $matches[3] );
      self::$args_str   = self::parse_args_str( trim( $matches[1] ) );
      self::$arguments  = self::parse_args( trim( $matches[5] ) );

      return true;
    }
    return false;
  }

  private static function parse_args( $str = null ) {
    preg_match( self::$args_regex, $str, $matches );

    if ( $matches ) {
      $arr  = array();
      $arr[self::$args_str[0]]  = trim( $matches[1] );
      $arr[self::$args_str[1]]  = (int)trim( $matches[2] );
      $arr[self::$args_str[2]]  = (int)trim( $matches[3] );
      $arr[self::$args_str[3]]  = trim( $matches[4] );
      return $arr;
    }
    return false;
  }

  private static function parse_args_str( $str = null ) {
    $str  = explode( ",", trim( $str ) );
    foreach( $str as &$s ) {
      $s  = trim( $s );
    }
    return $str;
  }

  private static function arg( $i = 0 ) {
    if ( isset( self::$args_str[$i] ) && isset( self::$arguments[self::$args_str[$i]] ) ) {
      return self::$arguments[self::$args_str[$i]];
    }
    return false;
  }

  public static function get_time() {
    return number_format( self::$end - self::$start, 2 );
  }

  public static function get_start_time() {
    return self::$start;
  }

  public static function get_end_time() {
    return self::$end;
  }

  public static function get_size( $packed = false ) {
    return ( $packed ) ? strlen( self::$packed ) : strlen( self::$unpacked );
  }

  private static function e( $a, $b ) {
    $c  = '';
    if ( $b > $a ) {
      $c  .=  self::e( $a, (int)( $b / $a ) );
    }
    $b = ( $b % $a );
    if ( $b > 35 ) {
      $c  .=  self::chr( $b + 29 );
    }
    else {
      $c  .=  self::dechex( $b, 36 );
    }
    return $c;
  }

  private static function parse() {
    if ( !self::$arguments || !self::$args_str ) {
      return false;
    }
    $p  = self::script( self::arg( 0 ) );
    $a  = self::arg( 1 );
    $c  = self::arg( 2 );
    $k  = self::split( self::arg( 3 ) );
    $e  = $p;
    $d  = self::get_words( $p );

    $s  = array();

    while( $c-- ) {
      if ( isset( $k[$c] ) ) {
        $i  = self::e( $a, $c );
        $s[$i]  = $k[$c];
      }
    }

    foreach( $d as $f ) {
      if ( isset( $s[$f] ) && !empty( $s[$f] ) ) {
        $char = $s[$f];
        $e  = preg_replace( '/\b'.$f.'\b/', $char, $e );
      }
    }
    self::$unpacked = $e;
  }

  private static function get_words( $str = null ) {
    preg_match_all( '/\b(\w+)\b/', $str, $d );
    if ( isset( $d[1] ) ) {
      $d  = $d[1];
    }
    else {
      $d  = array();
    }
    return array_unique( $d );
  }

  private static function script( $str = null ) {
    preg_match( '/^("|\')(.*)("|\')$/', $str, $matches );
    if ( $matches && isset( $matches[2] ) ) {
      return trim( stripslashes( $matches[2] ) );
    }
    return $str;
  }

  private static function split( $str = null ) {
    preg_match( '/("|\')(.*)("|\')/', $str, $matches );
    if ( $matches ) {
      return explode( "|", trim( $matches[2] ) );
    }
    return $str;
  }
  private static function dechex( $a = 0, $b = 16 ) {
    return base_convert( $a, 10, $b );
  }

  private static function chr( $a = 0 ) {
    return chr( $a );
  }

  private static function debug( $str = null ) {
    print_r( $str );
    echo "\n\n";
  }
}

class ipMinifyPhp {
  private $source = null;
  private $output = null;
  private $marked = null;
  private $did    = false;

  private $options  = null;

  private $start_time = 0;
  private $end_time   = 0;
  private $start_mem  = 0;
  private $end_mem    = 0;
  private $time_limit = 0;
  private $memory_limit = 0;

  private $tokens_list  = null;
  private $last_token   = null;
  private $tokens = null;
  private $tokens_parsed  = array();
  private $token  = null;
  private $index  = 0;

  private $has_namespace  = false;

  private $namespaces = array();
  private $classes    = array();
  private $functions  = array();
  private $variables  = array();
  private $strings    = array();

  private $exclude_classes  = array( "Exception", "Directory", "self" );
  private $exclude_funcs    = array(
                                '__autoload', '__construct', '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup', '__toString', '__invoke', '__set_state', '__clone',
                                'if', 'else', 'elseif', 'else if', 'while', 'do', 'for', 'foreach', 'break', 'continue', 'switch', 'declare', 'return', 'require', 'include', 'require_once', 'include_once', 'goto',
                                'echo', 'money_format', 'print', 'function', 'self', 'this', 'method_exists', 'array', 'isset', 'empty', 'get_cfg_var', 'ini_set', 'ini_get'
                              );
  private $exclude_vars     = array( 'this', '_POST', '_GET', '_SERVER', '_SESSION', '_REQUEST', 'GLOBALS', '_FILES', '_ENV', '_COOKIE', 'php_errormsg', 'HTTP_RAW_POST_DATA', 'http_response_header', 'argc', 'argv' );
  private $string_funcs     = array(
                                'if', 'else', 'elseif', 'else if', 'while', 'do', 'for', 'foreach', 'break', 'continue', 'switch', 'declare', 'return', 'require', 'include', 'require_once', 'include_once', 'goto',
                                '__autoload', '__construct', '__destruct', '__call', '__callStatic', '__get', '__set', '__isset', '__unset', '__sleep', '__wakeup', '__toString', '__invoke', '__set_state', '__clone',
                                'self', 'this', 'method_exists', 'array', 'isset', 'empty', 'get_cfg_var', 'ini_set', 'ini_get'
                              );
  private $class_closures   = array(
                                'class_exists', 'interface_exists', 'method_exists', 'property_exists', 'is_subclass_of', 'class_parents', 'class_implements',
                                'class_uses'
                              );
  private $func_closures1   = array(
                                'method_exists', 'call_user_func', 'call_user_func_array', 'is_callable'
                              );
  private $func_closures2   = array(
                                'function_exists'
                              );
  private $global_ns_methods  = array();
  private $did_arg_first  = false;

  private $in_namespace = false;
  private $in_use       = false;
  private $in_classname = false;
  private $class_count  = 0;
  private $in_funcname  = false;
  private $func_count   = 0;
  private $in_for = false;
  private $for_in = 0;

  private $prev_cache   = array();
  private $next_cache   = array();

  private $in_tag_element = array();
  private $in_tag_added   = array();
  private $tag_count_open = false;

  private $splitter_char  = ',';

  public function __construct( $source = null, $options = array() ) {
    $this->set_content( $source );
    $this->set_options( $options );
  }

  public function set_content( $source = null ) {
    $source = trim( $source );
    if ( function_exists( "mb_convert_encoding" ) ) {
      if ( strtolower( @mb_detect_encoding( $source ) !== 'utf-8' ) ) {
        $source = @mb_convert_encoding( $source, "UTF-8", @mb_detect_encoding( $source ) );
      }
    }
    if ( !empty( $source ) ) {
      $this->source = $source;
      return true;
    }
    return false;
  }

  public function set_options( $options = array() ) {
    if ( is_array( $options ) && !empty( $options ) ) {
      $this->options  = array_merge( $this->get_default_options(), $options );
      $this->exclude_classes  = array_merge( $this->exclude_classes, $this->options["exclude_classes"] );
      $this->exclude_funcs  = array_merge( $this->exclude_funcs, $this->options["exclude_functions"] );
      $this->exclude_vars = array_merge( $this->exclude_vars, $this->options["exclude_variables"] );
      return true;
    }
    return false;
  }

  public function minify( $source = null, $options = array() ) {
    $this->set_start_time();

    $this->set_content( $source );
    $this->set_options( $options );

    if ( $this->parse_tokens() ) {
      $len  = count( $this->tokens );
      for( $i = 0; $i < $len; $i++ ) {
        $token  = $this->to_token_arr( $this->tokens[$i] );

        $this->token  = $token;
        $this->index  = $i;

        if ( $this->timeout_exceeded() ) {
          throw new Exception( "Process Timout".$this->debug( array( "time" ) ) );
        }

        if ( $this->memory_usage_exceeded() ) {
          throw new Exception( "Memory Usage Limit".$this->debug( array( "memory" ) ) );
        }

        $this->parse_strings();

        if ( $this->option( "minify_script" ) ) {
          $this->do_minify();
        }
        else {
          $this->do_join();
        }
      }
    }

    $this->set_end_time();
  }

  private function do_minify() {
    if ( $this->token[0] !== T_ENCAPSED_AND_WHITESPACE ) {
      $this->token[1] = trim( $this->token[1] );
    }

    if ( !$this->option( "preserve_comments" ) ) {
      if ( in_array( $this->token[0], array( T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT ) ) ) {
        $this->token[1] = '';
      }
    }

    if ( $this->token[0] === T_WHITESPACE ) {
      $this->token[1] = '';
    }

    $this->add_spaces_before( $this->token[1] );
    $this->add_spaces_after( $this->token[1] );
    $this->highlight();
  }

  private function add_spaces_before( &$token ) {
    $tname  = $this->token[0];
    switch( $tname ) {
      case T_COMMENT:
      case T_ML_COMMENT:
      case T_DOC_COMMENT:
      break;

      case T_END_HEREDOC:
        $token  = PHP_EOL.$token;
      break;

      case T_LOGICAL_OR:
      case T_LOGICAL_AND:
      case T_LOGICAL_XOR:

      case T_EXTENDS:
      case T_AS:
      case T_IMPLEMENTS:
      case T_INSTANCEOF:
      case T_ENCAPSED_AND_WHITESPACE:
        if ( $tname === T_AS ) {
          if ( $this->prev_token( 1 ) === ")" ) {
            break;
          }
        }
        if ( $tname === T_ENCAPSED_AND_WHITESPACE ) {
          break;
        }
        $token  = ' '.$token;
      break;

      case T_NEW:
        if ( !$this->is_whitespace( $this->prev_token( 1, 1, true ) ) ) {
          $token  = ' '.$token;
        }
      break;

      default:
        if ( $tname === T_VARIABLE ) {
          if ( $this->in( "eval" ) ) {
            break;
          }
        }
      break;
    }
  }
  private function add_spaces_after( &$token ) {
    $tname  = $this->token[0];
    switch( $tname ) {
      case T_COMMENT:
      case T_ML_COMMENT:
      case T_DOC_COMMENT:
        if ( in_array( substr( trim( $token ), 0, 1 ), array( '/', '#' ) ) ) {
          $token  = $token.PHP_EOL;
        }
      break;

      case T_START_HEREDOC:
      case T_END_HEREDOC:
        $token  = $token.PHP_EOL;
      break;

      case T_LOGICAL_OR:
      case T_LOGICAL_AND:
      case T_LOGICAL_XOR:

      case T_OPEN_TAG:
      case T_OPEN_TAG_WITH_ECHO:
      case T_CLASS:
      case T_ABSTRACT:
      case T_PROTECTED:
      case T_PRIVATE:
      case T_PUBLIC:
      case T_FUNCTION:
      case T_EXTENDS:
      case T_NEW:
      case T_CONST:
      case T_FINAL:
      case T_NAMESPACE:
      case T_INTERFACE:
      case T_IMPLEMENTS:
      case T_STATIC;
      case T_THROW:
      case T_INSTANCEOF:
      case T_DO:
      case T_GOTO:
      case T_ENCAPSED_AND_WHITESPACE:
        if ( $tname === T_FUNCTION ) {
          if ( $this->next_token( 1 ) === "(" ) {
            break;
          }
        }
        if ( $tname === T_ENCAPSED_AND_WHITESPACE ) {
          break;
        }
        if ( $tname === T_DO ) {
          if ( $this->next_token( 1 ) === "{" ) {
            break;
          }
        }
        if ( $tname === T_INSTANCEOF ) {
          if ( $this->next_token( 0 ) === T_VARIABLE ) {
            break;
          }
        }
        $token  = $token." ";
      break;

      case T_ELSE:
        if ( !in_array( $this->next_token( 1 ), array( ":", "{" ) ) ) {
          $token  = $token." ";
        }
      break;

      case T_ECHO:
      case T_RETURN:
      case T_PRINT:
      case T_REQUIRE:
      case T_REQUIRE_ONCE:
      case T_INCLUDE:
      case T_INCLUDE_ONCE:
      case T_GLOBAL:
      case T_CASE:
        if ( !in_array( $this->next_token( 0 ), array( T_VARIABLE, T_CONSTANT_ENCAPSED_STRING, 'UNKNOWN' ) ) ) {
          $token  = $token." ";
        }
      break;

      case T_FOR:
      case T_ENDFOR:
      case ( $token === '(' ):
      case ( $token === ')' ):
        if ( $token === 'for' ) {
          $this->in_for = true;
          $this->for_in = 0;
        }
        if ( ( $token === '(' || ( $token === ':' ) ) && $this->in_for ) {
          $this->for_in++;
        }
        if ( ( $token === ')' || $tname === T_ENDFOR ) && $this->in_for ) {
          if ( --$this->for_in <= 0 ) {
            $this->in_for = false;
          }
        }
      default:
        if ( $tname === T_VARIABLE ) {
          if ( $this->in( "eval" ) ) {
            break;
          }
        }
        if ( $token === ';' ) {
          
        }
        if ( $token === ';' && !$this->in_for && ( substr( trim( $this->output ), -1 ) === ';' || substr( trim( $this->output ), -1 ) === '}' ) ) {
          continue;
        }
      break;
    }
  }

  private function is_whitespace( $str = null ) {
    if ( trim( $str ) !== $str ) {
      return true;
    }
    return false;
  }

  private function do_join() {
    if ( !$this->option( "preserve_comments" ) ) {
      if ( in_array( $this->token[0], array( T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT ) ) ) {
        $this->token[1] = '';
      }
    }
    $this->highlight();
  }

  private function md5( $str ) {
    $str  = $this->unquote( $str );
    return md5( $str );
  }

  private function highlight() {
    $current  = htmlspecialchars( $this->token[1] );
    $this->output .=  $current;
    $label  = $this->get_label( $this->token[1], $this->token[3] );
    
    if ( $label['label'] ) {
      $this->marked .=  '<span id="'.$this->md5( $this->token[1] ).'" data-label="'.$label["label"].'" data-line="'.$this->token[2].'" data-token="'.$label["token"].'" class="'.$this->token[3].'">'.$current.'</span>';
    }
    else {
      $this->marked .=  '<span class="'.$this->token[3].'">'.$current.'</span>';
    }
  }

  private function get_label( $token = null, $class = null ) {
    $quote  = substr( $token, 0, 1 );
    $token  = $this->unquote( $token );
    $label  = array( "label" => null, "token" => null );
    switch( $class ) {
      case ( strpos( $class, "namespace-string" ) !== false ):
        $label["label"] = "namespace";
        $label["token"] = htmlspecialchars( array_search( $token, $this->namespaces ) );
      break;
      case ( strpos( $class, "class-string" ) !== false ):
        $label["label"] = "class";
        $label["token"] = htmlspecialchars( array_search( $token, $this->classes ) );
      break;
      case ( strpos( $class, "function-string" ) !== false ):
        $label["label"] = "function";
        $label["token"] = htmlspecialchars( array_search( $token, $this->functions ) );
      break;
      case ( strpos( $class, "variable-string" ) !== false ):
        $label["label"] = "variable";
        $token  = str_replace( '$', '', $token );
        $label["token"] = '$'.htmlspecialchars( array_search( $token, $this->variables ) );
      break;
      case ( strpos( $class, "string-string" ) !== false ):
        $string = trim( $this->hexchar( $token ) );
        if ( !empty( $string ) ) {
          $label["label"] = "string";
          $label["token"] = $this->ellipse( $string, 50 );
        }
      break;
      case ( strpos( $class, "html-string" ) !== false ):
        $label["label"] = "html";
        $label["token"] = $this->ellipse( $this->unquote( $token ), 50 );
      break;
    }
    return $label;
  }

  private function ellipse( $string = null, $maxlen = 10 ) {
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

  public function output() {
    if ( !$this->did ) {
      $this->did  = true;
      if ( $this->option( "mass_encoding" ) ) {
        $this->mass_encode();
      }
      else {
        $this->do_comments();
      }
    }
    return ( $this->output ) ? $this->output : htmlspecialchars( $this->source );
  }

  private function do_comments() {
    if ( $this->option( "add_copyright" ) === true ) {
      $output = trim( htmlspecialchars_decode( $this->output ) );
      if ( substr( $output, 0, 5 ) === '<?php' ) {
        $output = '<?php'.PHP_EOL.$this->alt_comments().PHP_EOL.substr( $output, 5, strlen( $output ) );
      }
      else {
        $output = '<?php'.PHP_EOL.$this->alt_comments().PHP_EOL.'?>'.PHP_EOL.$output;
      }
      $this->output = htmlspecialchars( $output );
    }
  }

  public function marked() {
    return ( $this->marked ) ? $this->marked : htmlspecialchars( $this->source );
  }

  private function alt_comments() {
    $comment  = trim( (string)$this->option( "copyright_message" ) );
    if ( empty( $comment ) ) {
      $comment  = '/*
 * Impact Plus 3.2
 *
 * Copyright (c) 2013-2014 Impact Plus
 *
 * This script is protected.
 * Any attempt to reverse engineer, debug or decode this file or its dependent files is strictly prohibited.
 */';
    }
    return $comment;
  }

  private function preg_stub( $count = 5 ) {
    $output  =  '';
    $output .=  'preg_replace("'.$this->charhex( "/.*/e" ).'","';
    $output .=  $this->charhex( 'eval(gzinflate(base64_decode("' );
    $output .=  base64_encode( gzdeflate( $this->output, 9 ) );
    $output .=  $this->charhex( '")));' );
    $output .=  '","");';
    $this->output = $output;
  }

  private function mass_encode() {
    $data = gzdeflate( $this->do_high_encode(), 9 );
    $data = base64_encode( $data );

    $uucode = $this->mixed_combine( array( "base64_decode", "gzinflate", "call_user_func" ), true );
    $variables  = $uucode["variable"];
    $base64_dec = $uucode["strings"]["base64_decode"];
    $gzinflate  = $uucode["strings"]["gzinflate"];
    $calluserf  = $uucode["strings"]["call_user_func"];

    $this->output = $variables.'eval('.$calluserf.'('.$gzinflate.','.$calluserf.'('.$base64_dec.',"'.$data.'")));';
    //$this->preg_stub();

    if ( $this->option( "add-processor" ) ) {
      $this->output = base64_encode( gzdeflate( $this->output, 9 ) );

      $uucode = $this->mixed_combine( array( "base64_decode", "preg_replace", "trim", "gzinflate", "call_user_func", "stream_get_contents", "fopen", "fseek" ), true );
      $variables  = $uucode["variable"];
      $fp = '$'.$this->ucode( "fp" );
      $base64_dec = $uucode["strings"]["base64_decode"];
      $gzinflate  = $uucode["strings"]["gzinflate"];
      $calluserf  = $uucode["strings"]["call_user_func"];
      $sgcontents = $uucode["strings"]["stream_get_contents"];
      $fseek      = $uucode["strings"]["fseek"];
      $fopen      = $uucode["strings"]["fopen"];

      $processor    = $fp.'='.$calluserf.'('.$fopen.','.$uucode["strings"]["trim"].'('.$uucode["strings"]["preg_replace"].'( "/(.+?)\(([0-9]+)\)\s*\:\s*eval\(\)\\\'d\s*code\s*/","$1",__FILE__)),"'.$this->charhex( "r" ).'");'.$calluserf.'('.$fseek.','.$fp.',__COMPILER_HALT_OFFSET__);';
      $creator      = $calluserf.'('.$sgcontents.','.$fp.')';
      $this->output = $variables.$processor.'eval('.$calluserf.'('.$gzinflate.','.$calluserf.'('.$base64_dec.','.$creator.')));__halt_compiler();'.$data;

      if ( $this->option( "add_copyright" ) === true ) {
        $comment  = $this->alt_comments();
        $this->output = '<?php'.PHP_EOL.$comment.PHP_EOL.$this->output;
      }
      else {
        $this->output = '<?php '.$this->output;
      }
    }
    else {
      if ( $this->option( "add_copyright" ) === true ) {
        $comment  = $this->alt_comments();
        $this->output = '<?php'.PHP_EOL.$comment.PHP_EOL.$this->output.PHP_EOL.'?>';
      }
      else {
        $this->output = '<?php '.$this->output.PHP_EOL.'?>';
      }
    }

    $this->output = htmlspecialchars( $this->output );
  }

  private function do_high_encode() {
    $this->output = htmlspecialchars_decode( $this->output );

    $encoder  = new ipMassEncoder;
    $encoder->set_source( $this->output )->set_level( $this->option( "encoding_level" ) )->encode();
    $this->output = $encoder->decode();

    if ( $this->option( "higher_encoding" ) === true ) {
      $uucode = $this->mixed_combine( array( "call_user_func" ), true );
      $variables  = $uucode["variable"];
      $calluserf  = $uucode["strings"]["call_user_func"];
      $gzdefl = gzdeflate( $this->output, 9 );
      $base64 = base64_encode( $gzdefl );
      $this->output = $this->get_decode_function( $base64 );
    }

    return $this->output;
  }

  private function split_to_chunks( $base64 = null ) {
    $data = array();
    for( $i = 0; $i < strlen( $base64 ); $i++ ) {
      $data[] = dechex( ord( $base64[$i] ) );
    }
    $data   = implode( "|", $data );
    return $data;
  }

  private function decode_function( $chunks = null ) {
    $uucode = $this->mixed_combine( array( "base64_decode", "create_function", "explode", "implode", "chr", "hexdec", "gzinflate", "call_user_func" ), true );
    $variable = $uucode["variable"];
    $strings  = $uucode["strings"];

    $a  = '$'.ipMassEncoder::uucode( "a" );
    $b  = '$'.ipMassEncoder::uucode( "b" );
    $c  = '$'.ipMassEncoder::uucode( "c" );

    $string     = '$'.ipMassEncoder::uucode( "string" );
    $base64_dec = $strings["base64_decode"];
    $explode    = $strings["explode"];
    $implode    = $strings["implode"];
    $chr        = $strings["chr"];
    $hexdec     = $strings["hexdec"];
    $gzinflate  = $strings["gzinflate"];
    $calluserf  = $strings["call_user_func"];
    $create_fun = $strings["create_function"];

    $code_execution = 'return'.$calluserf.'('.$gzinflate.','.$calluserf.'('.$base64_dec.','.$calluserf.'('.$implode.',"",'.$b.')));';
    $function = $variable.$string.'='.$create_fun.'(\''.$a.'\',\'global'.implode( ",", $strings ).';'.$a.'='.$explode.'("|",'.$a.');'.$b.'=array();foreach('.$a.' as '.$c.'):'.$b.'[]='.$chr.'('.$hexdec.'('.$c.'));endforeach;'.$code_execution.'\');eval('.$calluserf.'('.$string.',"'.$chunks.'"));';
    return $function;
  }

  private function get_decode_function( $base64 = null ) {
    $chunks = $this->split_to_chunks( $base64 );
    $decode = $this->decode_function( $chunks );

    $data   = base64_encode( $decode );

    $uucode = $this->mixed_combine( array( "base64_decode", "call_user_func" ), true );
    $base64 = $uucode["strings"]["base64_decode"];
    $calluf = $uucode["strings"]["call_user_func"];
    $callbk = $uucode["variable"].'eval('.$calluf.'('.$base64.',"'.$data.'"));';
    return $callbk;
  }

  public function mixed_combine( $strings = array(), $variable = false, $callback = false ) {
    if ( !$callback || !is_callable( $callback ) ) {
      $callback = array( "ipMassEncoder", "uucode" );
    }
    $encoded_arr  = array();
    $encoded_str  = array();
    $encoded_mlt  = array();

    foreach( $strings as $string ) {
      $chars  = str_split( $string );
      $uucode = call_user_func( $callback, $string );

      $encoded_str[$string] = $uucode;
      foreach( $chars as $char ) {
        $char = $this->charhex( $char );
        $encoded_arr[]  = '$'.$uucode.'.="'.$char.'";';
        $encoded_mlt[$string][] = '$'.$uucode.'.="'.$char.'";';
      }
      unset( $uucode );
    }

    $prepend_null = array();
    foreach( $encoded_str as $uucode ) {
      $prepend_null[] = '$'.$uucode.'="";';
    }

    if ( $variable === true ) {
      array_walk( $encoded_str, function( &$m ) {
        $m  = '$'.$m;
      });
    }

    shuffle( $prepend_null );
    shuffle( $prepend_null );
    $prepend_null = implode( "", $prepend_null );

    shuffle( $encoded_mlt );

    $total_values = count( $encoded_arr );
    $total_index  = range( 0, count( $encoded_mlt ) - 1 );
    $repeatation  = array();
    $final_array  = array_fill( 0, $total_values, null );

    for( $i = 0; $i < $total_values; $i++ ) {
      $key1 = $this->array_rand( $total_index );
      $key2 = ( !isset( $repeatation[$key1] ) ) ? 0 : ( $repeatation[$key1] + 1 );
      $repeatation[$key1] = $key2;
      if ( $key2 >= ( count( $encoded_mlt[$key1] ) - 1 ) ) {
        unset( $total_index[$key1] );
      }
      if ( isset( $encoded_mlt[$key1] ) ) {
        if ( isset( $encoded_mlt[$key1][$key2] ) ) {
          $final_array[$i]  = $encoded_mlt[$key1][$key2];
        }
      }
    }

    $encoded_arr  = $prepend_null.implode( "", $final_array );
    unset( $final_array, $encoded_mlt, $total_values, $total_index, $repeatation, $prepend_null );

    return array( "variable" => $encoded_arr, "strings" => $encoded_str );
  }

  private function array_rand( $arr, $num = 1 ) {
    shuffle( $arr );
    shuffle( $arr );

    $r  = array();
    for( $i = 0; $i < $num; $i++ ) {
      $r[]  = $arr[$i];
    }
    return ( $num == 1 ) ? $r[0] : $r;
  }

  public function ucode() {
    $alpha  = array_merge( range( 'z', 'a' ), range( 'A', 'Z' ) );
    $alpha  = $alpha[array_rand( $alpha )];
    $uucode = ipPasswordGen::generate_key( null, mt_rand( 10, 20 ), true );
    if ( is_numeric( substr( $uucode, 0, 1 ) ) ) {
      $uucode = $alpha.$uucode;
    }
    $uucode = strtolower( $uucode );
    return $uucode;
    //return ipPasswordGen::syllables();
    //return ipPasswordGen::keygen( 'i', 20, false );
  }

  /** Private Functions **/
  private function set_start_time() {
    $this->start_time = microtime();
    $this->start_mem  = memory_get_usage( true );
  }
  private function set_end_time() {
    $this->end_time   = microtime();
    $this->end_mem    = memory_get_usage( true );
  }

  private function parse_tokens() {
    if ( function_exists( "token_get_all" ) ) {
      if ( $tokens = token_get_all( $this->source ) ) {
        $this->tokens = $tokens;
        unset( $tokens );

        $this->define_tokens();
        $this->exclude_strings();

        return true;
      }
    }

    return false;
  }

  private function define_tokens() {
    $this->tokens_list  = $this->get_php_token_list();
    foreach( $this->tokens_list as $token ) {
      if ( !defined( $token ) ) {
        define( $token, -1 );
      }
    }
  }

  private function exclude_strings() {
    $defined_functions  = get_defined_functions();
		$defined_functions  = $defined_functions["internal"];
    foreach( $defined_functions as $def_func ) {
      if ( !in_array( $def_func, $this->exclude_funcs ) ) {
        $this->exclude_funcs[]  = $def_func;
      }
    }
    sort( $this->exclude_funcs );

    $defined_classes  = get_declared_classes();
    foreach( $defined_classes as $def_class ) {
      if ( !in_array( $def_class, $this->exclude_classes ) ) {
        $this->exclude_classes[]  = $def_class;
      }
    }
    sort( $this->exclude_classes );

    unset( $defined_functions, $defined_classes );
  }

  private function parse_strings() {
    if ( in_array( $this->token[0], array( T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT, T_WHITESPACE ) ) ) {
      $this->token[3] = "php-string ".$this->get_php_token_class( $this->token[0] );
      return false;
    }

    if ( $this->option( "encode_namespace" ) ) {
      if ( $this->token[0] === T_NAMESPACE ) {
        $this->in_namespace   = true;
        $this->has_namespace  = true;
        preg_match_all( "/namespace(\s+)((.+?))(\;|\{)/", $this->source, $namespaces );
        if ( isset( $namespaces[3] ) && !empty( $namespaces[3] ) ) {
          foreach( $namespaces[3] as $namespaces_1 ) {
            $namespaces_1 = explode( '\\', trim( $namespaces_1 ) );
            foreach( $namespaces_1 as $namespace ) {
              $namespace  = trim( $namespace );
              $this->namespaces[$namespace] = $this->uniqid_case( $namespace );
            }
          }
          unset( $namespace, $namespaces_1 );
        }
      }
      unset( $namespaces );
    }
    if ( $this->token[0] === T_USE ) {
      $this->in_use = true;
    }
    if ( $this->in_use ) {
      if ( $this->token[1] === ";" || $this->token[1] === ")" ) {
        $this->in_use = false;
      }
    }
    if ( $this->has_namespace && $this->in_namespace ) {
      if ( $this->token[1] === ";" || $this->token[1] === "{" ) {
        $this->in_namespace = false;
      }
    }

    if ( $this->next_token( 1 ) === "(" ) {
      $this->add_wrapping_tag();
    }
    if ( $this->prev_token( 1 ) === ")" ) {
      $this->remove_wrapping_tag();
    }

    if ( $this->option( "encode_class" ) ) {
      $this->parse_defined_classes();
    }
    if ( $this->option( "encode_functions" ) ) {
      $this->parse_defined_functions();
    }
    if ( $this->option( "encode_variables" ) ) {
      $this->parse_defined_variables();
    }
    if ( $this->option( "encode_strings" ) ) {
      $this->parse_defined_strings();
    }
    if ( $this->option( "minify_inline_html" ) ) {
      $this->parse_inline_html();
    }
    if ( $this->option( "obfuscate_inline_js" ) ) {
      $this->parse_inline_js();
    }

    if ( $this->has_namespace ) {
      if ( $this->prev_token( 0 ) === T_NAMESPACE || $this->prev_token( 1 ) === '\\' ) {
        if ( isset( $this->namespaces[$this->token[1]] ) ) {
          $this->add_to_array( $this->token, 1, $this->namespaces[$this->token[1]], true, false );
          $this->add_css_class( 'namespace-string' );
        }
      }
    }

    if ( !isset( $this->token[3] ) ) {
      $this->token[3] = "php-string ".$this->get_php_token_class( $this->token[0] );
    }
  }

  private function is_not_a_class( $class = null ) {
    return ( isset( $this->classes[$class] ) );
  }
  private function is_not_a_function( $func = null ) {
    return ( isset( $this->functions[$func] ) );
  }
  private function is_not_a_variable( $var = null ) {
    return ( isset( $this->variables[$var] ) );
  }

  private function add_wrapping_tag() {
    $this->in_tag_element[$this->token[1]] = $this->token[0];
    $this->in_tag_added[] = $this->token[1];
    $this->tag_count_open[$this->token[1]] = ( !isset( $this->tag_count_open[$this->token[1]] ) ) ? 0 : $this->tag_count_open[$this->token[1]]+1;
  }
  private function remove_wrapping_tag() {
    end( $this->in_tag_added );
    $key  = key( $this->in_tag_added );
    unset( $this->in_tag_added[$key] );
    /*$last_tag = end( $this->in_tag_added );
    if ( !isset( $this->tag_count_open[$last_tag] ) ) {
      $this->tag_count_open[$last_tag] = false;
    }
    if ( !isset( $this->tag_count_open[$last_tag] ) || !$this->tag_count_open[$last_tag] ) {
      if ( isset( $this->in_tag_element[$last_tag] ) ) {
        unset( $this->in_tag_element[$last_tag], $this->tag_count_open[$last_tag], $this->in_tag_added[count( $this->in_tag_added ) - 1] );
      }
    }
    else {
      $this->tag_count_open[$last_tag]--;
    }*/
  }
  private function in( $element = null, $in = 0 ) {
    $last = null;
    if ( $in === 0 ) {
      $last = substr( end( $this->in_tag_added ), 0, strlen( $element ) );
    }
    else {
      if ( isset( $this->in_tag_added[count( $this->in_tag_added ) - $in] ) ) {
        $last = substr( $this->in_tag_added[count( $this->in_tag_added ) - $in], 0, strlen( $element ) );
      }
    }
    return ( $last == $element );
  }
  private function in_arr( $elements = array() ) {
    foreach( $elements as $element ) {
      if ( in_array( $element, $this->in_tag_added ) ) {
        return true;
      }
    }
  }
  private function last_opened( $i = 0 ) {
    $i  = ( count( $this->in_tag_added ) - $i );
    if ( isset( $this->in_tag_added[$i] ) ) {
      return $this->in_tag_added[$i];
    }
    return false;
  }

  private function is_namespaced( $string = null ) {
    if ( stristr( $string, '\\' ) ) {
      return true;
    }
    return false;
  }

  private function parse_namespace( $string = null ) {
    $string = trim( $string );
    $string = str_replace( '\\\\', '\\', $string );
    $global = ( strpos( $string, '\\' ) === 0 );
    $string = explode( '\\', $string );
    $output = array();
    $i  = 0;
    foreach( $string as $a => $b ) {
      $b  = trim( $b );
      if ( empty( $b ) ) {
        unset( $string[$a] );
        continue;
      }
      if ( !isset( $this->namespaces[$b] ) ) {
        if ( $i === 0 && $this->prev_token( 1, 1 ) !== "." && $this->prev_token( 1, 2 ) !== "__NAMESPACE__" ) {
          $output[] = $b;
        }
        elseif ( $this->is_an_allowed_class( $b ) ) {
          $output[] = $this->uniqid_case( $b );
        }
      }
      else {
        $output[] = $this->namespaces[$b];
      }
      $i++;
    }
    return ( ( $global ) ? '\\\\' : '' ).implode( '\\\\', $output );
  }

  private function parse_defined_classes() {
    $token  = $this->token[1];
    $prev_token = $this->prev_token();
    $next_token = $this->next_token();

    /** Check inside Class -- Start **/
    if ( $this->token[0] === T_CLASS ) {
      $this->in_classname = true;
    }
    if ( $token === "{" ) {
      if ( $this->in_classname ) {
        $this->class_count++;
      }
    }
    if ( $token === "}" ) {
      if ( $this->in_classname ) {
        $this->class_count--;
      }
      if ( $this->class_count == 0 ) {
        $this->in_classname = false;
      }
    }
    /** Check inside Class -- END **/

    if ( $this->token[0] === T_STRING ) {
      /** get defiend, extended, interface classes **/
      if ( in_array( $prev_token[0], array( T_CLASS, T_EXTENDS, T_INTERFACE, T_IMPLEMENTS, T_NEW, T_INSTANCEOF, T_TRAIT ) ) ) {
        if ( in_array( $prev_token[0], array( T_EXTENDS, T_NEW, T_INSTANCEOF ) ) ) {
          if ( in_array( $token, $this->global_ns_methods ) ) {
            return false;
          }
        }
        if ( $this->is_an_allowed_class( $token ) ) {
          $id = $this->add_to_array( $this->token, 1, $token, true );
          $this->add_to_class_list( $token, $id );
          $this->add_css_class( "class-string" );
          return true;
        }
      }

      if ( $next_token[0] === T_DOUBLE_COLON ) {
        if ( $this->is_an_allowed_class( $token ) ) {
          $id = $this->add_to_array( $this->token, 1, $token, true );
          $this->add_to_class_list( $token, $id );
          $this->add_css_class( "class-string" );
          return true;
        }
      }

      if ( $this->option( "encode_namespace" ) && $this->option( "encode_external" ) ) {
        if ( $this->in_use ) {
          if ( $prev_token[0] === T_USE || $prev_token[1] === "," ) {
            $id = $this->add_to_array( $this->token, 1, $token, true );
            $this->add_to_namespace_list( $token, $id );
            $this->add_css_class( "namespace-string" );
            return true;
          }
          else {
            if ( !isset( $this->namespaces[$token] ) ) {
              if ( $prev_token[0] === T_NS_SEPARATOR ) {
                if ( $this->prev_token( 0, 2 ) === T_USE || $this->prev_token( 1, 2 ) === "," ) {
                  // Global method
                  $this->global_ns_methods[]  = $token;
                  return false;
                }
              }
              if ( $this->is_an_allowed_class( $token ) ) {
                $id = $this->add_to_array( $this->token, 1, $token, true );
                $this->add_to_class_list( $token, $id );
                $this->add_css_class( "class-string" );
                return true;
              }
            }
            else {
              $id = $this->add_to_array( $this->token, 1, $token );
              $this->add_to_namespace_list( $token, $id );
              $this->add_css_class( "namespace-string" );
              return true;
            }
          }
        }
        elseif ( $prev_token[0] !== T_NS_SEPARATOR && $next_token[0] === T_NS_SEPARATOR ) {
          if ( $this->is_an_allowed_class( $token ) ) {
            $id = $this->add_to_array( $this->token, 1, $token, true );
            $this->add_to_class_list( $token, $id );
            $this->add_css_class( "namespace-string" );
            return true;
          }
        }
        elseif ( $prev_token[0] === T_NS_SEPARATOR ) {
          if ( $next_token[1] === "(" ) {
            if ( $this->is_an_allowed_function( $token ) ) {
              $id = $this->add_to_array( $this->token, 1, $token, true );
              $this->add_to_func_list( $token, $id );
              $this->add_css_class( "function-string" );
              return true;
            }
          }
          elseif ( $this->is_an_allowed_class( $token ) ) {
            $id = $this->add_to_array( $this->token, 1, $token, true );
            $this->add_to_class_list( $token, $id );
            $this->add_css_class( "class-string" );
            return true;
          }
        }
      }
    }
    elseif ( $this->token[0] === T_CONSTANT_ENCAPSED_STRING ) {
      if ( $this->in( "is_subclass_of" ) && $prev_token[1] === "," ) {
        $string = $this->unquote( $token );
        if ( $this->is_namespaced( $string ) ) {
          $this->did_arg_first  = $string;
          $string = $this->parse_namespace( $string );
          $id = $this->add_to_array( $this->token, 1, $string, true, false, "'", "'" );
          $this->add_to_class_list( $token, $id );
          $this->add_css_class( "class-string" );
        }
        else {
          if ( $this->is_an_allowed_class( $string ) ) {
            $this->did_arg_first  = $string;
            $id = $this->add_to_array( $this->token, 1, $string, true, true, "'", "'" );
            $this->add_to_class_list( $string, $id );
            $this->add_css_class( "class-string" );
          }
        }
        return true;
      }
      foreach( $this->class_closures as $closure ) {
        if ( $this->in( $closure ) && $prev_token[1] !== "," ) {
          $string = $this->unquote( $token );
          if ( $this->is_namespaced( $string ) ) {
            $this->did_arg_first  = $string;
            $string = $this->parse_namespace( $string );
            $id = $this->add_to_array( $this->token, 1, $string, true, false, "'", "'" );
            $this->add_to_class_list( $token, $id );
            $this->add_css_class( "class-string" );
          }
          else {
            if ( $this->is_an_allowed_class( $string ) ) {
              $this->did_arg_first  = $string;
              $id = $this->add_to_array( $this->token, 1, $string, true, true, "'", "'" );
              $this->add_to_class_list( $string, $id );
              $this->add_css_class( "class-string" );
            }
          }
          return true;
        }
        elseif ( $this->in( "array" ) ) {
          if ( in_array( $this->prev_token( 1, 6 ), $this->func_closures1 ) || in_array( $this->prev_token( 1, 6 ), $this->func_closures2 ) ) {
            $string = $this->unquote( $token );
            if ( $this->is_namespaced( $string ) ) {
              $this->did_arg_first  = $string;
              $string = $this->parse_namespace( $string );
              $id = $this->add_to_array( $this->token, 1, $string, true, false, "'", "'" );
              $this->add_to_class_list( $token, $id );
              $this->add_css_class( "class-string" );
            }
            else {
              if ( $this->is_an_allowed_class( $string ) ) {
                $this->did_arg_first  = $string;
                $id = $this->add_to_array( $this->token, 1, $string, true, true, "'", "'" );
                $this->add_to_class_list( $string, $id );
                $this->add_css_class( "class-string" );
              }
            }
            return true;
          }
        }
      }
    }
    return false;
  }

  private function parse_defined_functions() {
    $token  = $this->token[1];
    $prev_token = $this->prev_token();
    $next_token = $this->next_token();

    if ( !in_array( $this->token[0], array( T_STRING, T_CONSTANT_ENCAPSED_STRING, "T_STRING", "UNKNOWN" ) ) ) {
      return false;
    }

    if ( $this->token[0] === T_VARIABLE ) {
      return false;
    }

    /** Check inside Function -- START **/
    if ( $this->token[0] === T_FUNCTION ) {
      $this->in_funcname  = true;
    }
    if ( $token === "{" ) {
      if ( $this->in_funcname ) {
        $this->func_count++;
      }
    }
    if ( $token === "}" ) {
      if ( $this->in_funcname ) {
        $this->func_count--;
      }
      if ( $this->func_count == 0 ) {
        $this->in_funcname = false;
      }
    }
    /** Check inside Function -- END **/


    if ( $this->in_classname && !$this->option( "encode_class_functions" ) ) {
      return false;
    }

    if ( $prev_token[0] === T_FUNCTION ) {
      if ( $this->token[0] === T_STRING ) {
        if ( $this->in_classname && !$this->option( "encode_class_functions" ) ) {
          return false;
        }
        if ( $this->is_an_allowed_function( $token ) ) {
          $id = $this->add_to_array( $this->token, 1, $token, true );
          $this->add_to_func_list( $token, $id );
          $this->add_css_class( "function-string" );
          return true;
        }
      }
    }

    if ( $next_token[1] === "(" ) {
      if ( $prev_token[0] === T_OBJECT_OPERATOR || $prev_token[0] === T_DOUBLE_COLON ) {
        if ( $this->in_classname && !$this->option( "encode_class_functions" ) ) {
          return false;
        }
        if ( $this->is_an_allowed_function( $token ) ) {
          $id = $this->add_to_array( $this->token, 1, $token, true );
          $this->add_to_func_list( $token, $id );
          $this->add_css_class( "function-string" );
          return true;
        }
      }
      else {
        if ( $this->token[0] === T_STRING ) {
          if ( $prev_token[0] === T_NEW || $this->prev_token( 0, 2 ) === T_NEW ) {
            return false;
          }
          if ( !isset( $this->token[3] ) ) {
            if ( $this->is_an_allowed_function( $token ) ) {
              $id = $this->add_to_array( $this->token, 1, $token, true );
              $this->add_to_func_list( $token, $id );
              $this->add_css_class( "function-string" );
              return true;
            }
          }
        }
      }
    }

    if ( $this->token[0] === T_CONSTANT_ENCAPSED_STRING ) {
      foreach( $this->func_closures1 as $closure ) {
        if ( $this->in( $closure ) && $this->prev_token( 1 ) === "," ) {
          $string = $this->unquote( $token );
          if ( !$this->option( "encode_external" ) && !$this->function_is_defined( $string ) ) {
            return false;
          }
          $id = $this->add_to_array( $this->token, 1, $string, true, true, '"', '"' );
          $this->add_to_func_list( $string, $id );
          $this->add_css_class( "function-string" );
          return true;
        }
        elseif ( $this->in( "array" ) ) {
          if ( in_array( $this->prev_token( 1, 9 ), $this->func_closures1 ) || in_array( $this->prev_token( 1, 9 ), $this->func_closures2 ) ) {
            $string = $this->unquote( $token );
            if ( !$this->option( "encode_external" ) && !$this->function_is_defined( $string ) ) {
              return false;
            }
            $id = $this->add_to_array( $this->token, 1, $string, true, true, '"', '"' );
            $this->add_to_func_list( $string, $id );
            $this->add_css_class( "function-string" );
            return true;
          }
          elseif ( in_array( $this->prev_token( 1, 7 ), $this->func_closures1 ) || in_array( $this->prev_token( 1, 7 ), $this->func_closures2 ) ) {
            $string = $this->unquote( $token );
            if ( !$this->option( "encode_external" ) && !$this->function_is_defined( $string ) ) {
              return false;
            }
            $id = $this->add_to_array( $this->token, 1, $string, true, true, '"', '"' );
            $this->add_to_func_list( $string, $id );
            $this->add_css_class( "function-string" );
            return true;
          }
        }
        elseif (
             ( in_array( $this->prev_token( 1, 3 ), $this->func_closures1 ) && $this->prev_token( 1, 3 ) !== "method_exists" )
          || ( in_array( $this->prev_token( 1, 2 ), $this->func_closures1 ) && $this->prev_token( 1, 2 ) !== "method_exists" )
          || ( in_array( $this->prev_token( 1, 3 ), $this->func_closures2 ) )
          || ( in_array( $this->prev_token( 1, 2 ), $this->func_closures2 ) )
        ) {
          $string = $this->unquote( $token );
          if ( !$this->option( "encode_external" ) && !$this->function_is_defined( $string ) ) {
            return false;
          }
          $id = $this->add_to_array( $this->token, 1, $string, true, true, '"', '"' );
          $this->add_to_func_list( $string, $id );
          $this->add_css_class( "function-string" );
          return true;
        }
        else {
          $string = $this->unquote( $token );
          if ( substr( $string, 0, 2 ) === "::" ) {
            $string = substr( $string, 2, strlen( $string ) );
            if ( !$this->option( "encode_external" ) && !$this->function_is_defined( $string ) ) {
              return false;
            }
            $id = $this->add_to_array( $this->token, 1, $string, true, true, '"::', '"' );
            $this->add_to_func_list( $string, $id );
            $this->add_css_class( "function-string" );
            return true;
          }
        }
      }
    }
    return false;
  }

  private function parse_defined_variables() {
    $token  = $this->token[1];
    $prev_token = $this->prev_token();
    $next_token = $this->next_token();

    if ( $this->token[0] === T_VARIABLE ) {
      if ( $this->in_classname && !$this->option( "encode_class_variables" ) ) {
        return false;
      }
      $string = substr( trim( $token ), 1, strlen( $token ) );
      if ( $this->is_an_allowed_variable( $string ) ) {
        $id = $this->add_to_array( $this->token, 1, $string, true, true, '$' );
        $this->add_to_var_list( $string, $id );
        $this->add_css_class( "variable-string" );
        return true;
      }
    }
    elseif ( $this->token[0] === T_STRING ) {
      if ( $prev_token[0] === T_OBJECT_OPERATOR && $next_token[1] !== "(" ) {
        if ( $this->in_classname && !$this->option( "encode_class_variables" ) ) {
          return false;
        }
        if ( $this->is_an_allowed_variable( $token ) ) {
          $id = $this->add_to_array( $this->token, 1, $token, true );
          $this->add_to_var_list( $token, $id );
          $this->add_css_class( "variable-string" );
          return true;
        }
      }
    }
    elseif ( $this->in( "property_exists" ) && $this->prev_token( 1 ) === "," ) {
      $string = $this->unquote( $token );
      if ( $this->is_an_allowed_variable( $string ) ) {
        $id = $this->add_to_array( $this->token, 1, $string, true, true, '"', '"' );
        $this->add_to_var_list( $string, $id );
        $this->add_css_class( "variable-string" );
        return true;
      }
    }
    return false;
  }

  private function parse_defined_strings() {
    if ( $this->token[0] === T_CONSTANT_ENCAPSED_STRING ) {
      if ( !$this->is_markup( $this->token[1] ) && !isset( $this->token[3] ) && !$this->is_regex( $this->token[1] ) ) {
        $string = $this->charhex( $this->token[1] );
        $this->strings[$this->unquote( $this->token[1] )] = $string;
        $this->add_to_array( $this->token, 1, $string, true, false, '"', '"' );
        $this->add_css_class( "string-string" );
        return true;
      }        
    }
    return false;
  }

  private function is_constant( $const = null ) {
    if ( preg_match( '/^([A-Z0-9_]+)$/', $const ) ) {
      if ( preg_match( '/define\((\s+)?(\'|")'.preg_quote( $const ).'(\'|")/', $this->source ) ) {
        return true;
      }
      if ( preg_match( '/const(\s+)?'.preg_quote( $const ).'/', $this->source ) ) {
        return true;
      }
    }
    return false;
  }

  public function charhex( $char = null ) {
    $char = $this->unquote( $char );
    $hex  = '';
    for( $i = 0; $i < strlen( $char ); $i++ ) {
      $hex  .=  '\x'.dechex( ord( $char[$i] ) );
    }
    return $hex;
  }

  private function hexchar( $hex = null ) {
    $hex  = $this->unquote( $hex );
    if ( is_numeric( $hex ) || stripos( $hex, '\x' ) === false ) {
      return null;
    }
    $hex  = explode( ' ', trim( str_replace( '\x', ' ', $hex ) ) );
    $chrs = '';
    foreach( $hex as $chr ) {
      $chrs .=  chr( hexdec( $chr ) );
    }
    return $chrs;
  }

  private function parse_inline_html() {
    if ( $this->token[0] === T_INLINE_HTML ) {
      $html = $this->minify_html( $this->token[1] );
      $html = trim( $html );
      $this->add_to_array( $this->token, 1, $html, true, false );
      $this->add_css_class( "html-string" );
    }
    elseif ( $this->token[0] === T_CONSTANT_ENCAPSED_STRING ) {
      if ( $this->is_markup( $this->token[1] ) ) {
        $html   = $this->minify_html( $this->token[1] );
        $quote  = substr( trim( $html ), 0, 1 );
        $html   = trim( $this->unquote( $html ) );
        $html   = $quote.$html.$quote;
        $this->add_to_array( $this->token, 1, $html, true, false );
        $this->add_css_class( "html-string" );
      }
    }
  }

  private function jsEscape( $str = null ) {
    return addcslashes( $str, "\\\'\"&\n\r<>" );
  }

  private function parse_inline_js() {
    if ( $this->token[0] === T_INLINE_HTML ) {
      $script = $this->encode_javascript( $this->token[1] );
      if ( $script !== false ) {
        $this->add_to_array( $this->token, 1, $script, true, false );
        if ( !isset( $this->token[3] ) ) {
          $this->add_css_class( "script-string" );
        }
      }
    }
    elseif ( $this->token[0] === T_CONSTANT_ENCAPSED_STRING ) {
      if ( $this->is_markup( $this->token[1] ) ) {
        $script = $this->encode_javascript( $this->token[1] );
        if ( $script !== false ) {
          $this->add_to_array( $this->token, 1, $script, true, false );
          if ( !isset( $this->token[3] ) ) {
            $this->add_css_class( "script-string" );
          }
        }
      }
    }
  }

  private function is_markup( $string = null ) {
    $string = $this->unquote( $string );
    if ( preg_match( '/<[^>]*>/', $string ) ) {
      if ( !$this->is_regex( $string ) ) {
        return true;
      }
    }
    return false;
  }

  private function unquote( $string = null ) {
    if ( substr( $string, 0, 1 ) == substr( $string, -1 ) ) {
      if ( in_array( substr( $string, 0, 1 ), array( '"', "'" ) ) ) {
        $string = trim( $string );
        $string = substr( $string, 1, strlen( $string ) - 2 );
        return $string;
      }
    }
    return $string;
  }

  private function is_regex( $expr = null ) {
    $expr = $this->unquote( $expr );
    if ( preg_match('/^(.{3,}?)([imsxuADU]*)$/', $expr, $m ) ) {
      $start  = substr( $m[1], 0, 1 );
      $end    = substr( $m[1], -1 );
      if ( ( $start === $end && !preg_match( '/[*?[:alnum:] \\\\]/', $start ) ) || ( $start === '{' && $end === '}' ) ) {
        return true;
      }
    }
    return false;
  }

  private function minify_html( $html = null ) {
    $html = preg_replace_callback( "/<style((?:(?!src=).)*?)>(.*?)<\/style>/smix", function( &$match ) {
      $style  = trim( $match[2] );
      $args   = ( trim( $match[1] ) != '' ) ? ' '.trim( $match[1] ) : '';
      if ( !empty( $style ) ) {
        $style  = ipCssMinifiy::minify( $style );
      }
      return "<style".$args.">".$style."</style>";
    }, $html );

    $html = ipHtmlMinify::minify( $html );

    return $html;
  }

  private function encode_javascript( $string = null ) {
    preg_match_all( "/<script((?:(?!src=).)*?)>(.*?)<\/script>/smix", $string, $scripts );
    if ( !empty( $scripts ) && !empty( $scripts[0] ) ) {
      $string = preg_replace_callback( "/<script((?:(?!src=).)*?)>(.*?)<\/script>/smix", function( &$match ) {
        $script = trim( $match[2] );
        $args   = ( trim( $match[1] ) != '' ) ? ' '.trim( $match[1] ) : '';
        if ( !empty( $script ) ) {
          $encoded  = $this->jsEscape( trim( ipPackerJS::obfuscate( $script, 36 ) ) );
          if ( $this->option( "force_encoding" ) === false ) {
            $script = ( strlen( $encoded ) > strlen( $script ) ) ? '/*<![CDATA[*/'.JSMinPlus::minify( $script ).'/*]]>*/' : '/*<![CDATA[*/'.$encoded.'/*]]>*/';
          }
          else {
            $script = '/*<![CDATA[*/'.$encoded.'/*]]>*/';
          }
        }
        return "<script".$args.">".$script."</script>";
      }, $string );
      return $string;
    }
    return false;
  }

  private function add_to_class_list( $name = null, $secure = null ) {
    if ( !isset( $this->classes[$name] ) ) {
      $this->classes[$name] = $secure;
    }
  }
  private function add_to_namespace_list( $name = null, $secure = null ) {
    if ( !isset( $this->namespaces[$name] ) ) {
      $this->namespaces[$name]  = $secure;
    }
  }
  private function add_to_func_list( $name = null, $secure = null ) {
    if ( !isset( $this->functions[$name] ) ) {
      $this->functions[$name] = $secure;
    }
  }
  private function add_to_var_list( $name = null, $secure = null ) {
    if ( !isset( $this->variables[$name] ) ) {
      $this->variables[$name] = $secure;
    }
  }

  private function class_is_defined( $class = null ) {
    if ( $class === "self" ) {
      return false;
    }
    if ( preg_match( '/(class|interface|trait)(\s+)'.preg_quote( $class ).'/', $this->source ) ) {
      return true;
    }
    return false;
  }
  private function function_is_defined( $function = null ) {
    if ( preg_match( '/function(\s+)'.preg_quote( $function ).'(\s+)?\(/', $this->source ) ) {
      return true;
    }
    return false;
  }
  private function variable_is_defined( $variable = null ) {
    if ( preg_match( '/'.preg_quote( $variable ).'(\s+)(=|;)(.+?)/', $this->source ) ) {
      return true;
    }
    if ( preg_match( '/as(\s+)(\&)?\$'.preg_quote( $variable ).'/', $this->source ) ) {
      return true;
    }
    if ( preg_match( '/for(.+?)\=\>(\s+)(\&)?\$'.preg_quote( $variable ).'/', $this->source ) ) {
      return true;
    }
    return false;
  }

  private function is_an_allowed_class( $class = null ) {
    if ( in_array( $class, array( "self", "parent" ) ) ) {
      return false;
    }
    if ( $this->option( "encode_external" ) ) {
      if ( !$this->has_namespace ) {
        if ( !$this->option( 'encode_namespace' ) ) {
          if ( preg_match( '/\\\\'.preg_quote( $class ).'/', $this->source ) ) {
            return false;
          }
          if ( preg_match( '/as(\s+)'.preg_quote( $class ).'/', $this->source ) ) {
            return false;
          }
          if ( preg_match( '/use(\s+)'.preg_quote( $class ).'/', $this->source ) ) {
            return false;
          }
          if ( preg_match( '/'.preg_quote( $class ).'\\\\/', $this->source ) ) {
            return false;
          }
        }
        if ( !in_array( $class, $this->exclude_classes ) ) {
          return true;
        }
      }
      else {
        return true;
      }
    }
    else {
      if ( $this->class_is_defined( $class ) ) {
        return true;
      }
    }
    return false;
  }
  private function is_an_allowed_function( $function = null ) {
    if ( $this->option( "encode_external" ) ) {
      if ( !$this->has_namespace ) {
        if ( !in_array( $function, $this->exclude_funcs ) ) {
          return true;
        }
      }
      else {
        if ( $this->prev_token( 0 ) === T_FUNCTION ) {
          if ( !in_array( $function, $this->string_funcs ) ) {
            return true;
          }
          return false;
        }
        if ( ( $this->prev_token( 0 ) === T_OBJECT_OPERATOR || $this->prev_token( 0 ) === T_DOUBLE_COLON ) && $this->next_token( 1 ) === "(" ) {
          if ( !in_array( $function, $this->string_funcs ) ) {
            return true;
          }
          return false;
        }        
        if ( $this->prev_token( 0, 1 ) !== T_NS_SEPARATOR ) {
          return false;
        }
        if ( $this->prev_token( 0, 2 ) === T_STRING ) {
          return true;
        }
        else {
          if ( !in_array( $function, $this->exclude_funcs ) ) {
            return true;
          }
          return false;
        }
        if ( !in_array( $function, $this->string_funcs ) ) {
          return true;
        }
      }
    }
    else {
      if ( !in_array( $function, $this->exclude_funcs ) && $this->function_is_defined( $function ) ) {
        return true;
      }
    }
    return false;
  }
  private function is_an_allowed_variable( $variable = null ) {
    if ( $this->option( "encode_external" ) ) {
      if ( !in_array( $variable, $this->exclude_vars ) ) {
        return true;
      }
    }
    else {
      if ( $this->variable_is_defined( $variable ) ) {
        if ( !in_array( $variable, $this->exclude_vars ) ) {
          return true;
        }
      }
    }
    return false;
  }

  private function add_to_array( &$arr = array(), $index = null, $value = null, $force = false, $encode = true, $p = false, $a = false ) {
    if ( $value === null ) {
      $value  = $index;
    }

    $token_id = $value;

    if ( !isset( $arr[$index] ) || $force === true ) {
      $token_id = ( $encode === true ) ? $this->uniqid_case( $value ) : $value;

      $tid  = $token_id;
      if ( $p !== false ) {
        $tid  = $p.$tid;
      }
      if ( $a !== false ) {
        $tid  = $tid.$a;
      }

      $arr[$index]  = $tid;
    }

    return $token_id;
  }

  private function add_css_class( $cssname = "variable-string" ) {
    $this->add_to_array( $this->token, 3, "php-string encoded-string ".$cssname, true, false );
  }

  public function get_size_left( $array = false ) {
    $size = ( strlen( $this->source ) - strlen( $this->output ) );
    $aret = array(
              "bytes" =>  sprintf( "%.0f bytes", $size ),
              "KB"    =>  sprintf( "%.2f KB", $size / 1024.0 )
            );
    if ( $array === true ) {
      return $aret;
    }
    if ( $size >= 1024 ) {
      return $aret["KB"];
    }
    else {
      return $aret["bytes"];
    }
  }

  public function get_source_size( $array = false ) {
    $size = strlen( $this->source );
    $aret = array(
              "bytes" =>  sprintf( "%.0f bytes", $size ),
              "KB"    =>  sprintf( "%.2f KB", $size / 1024.0 )
            );
    if ( $array === true ) {
      return $aret;
    }
    if ( $size >= 1024 ) {
      return $aret["KB"];
    }
    else {
      return $aret["bytes"];
    }
  }

  public function get_minified_size( $array = false ) {
    $size = strlen( $this->output );
    $aret = array(
              "bytes" =>  sprintf( "%.0f bytes", $size ),
              "KB"    =>  sprintf( "%.2f KB", $size / 1024.0 )
            );
    if ( $array === true ) {
      return $aret;
    }
    if ( $size >= 1024 ) {
      return $aret["KB"];
    }
    else {
      return $aret["bytes"];
    }
  }

  public function get_ratio() {
    $ratio  = round( 100 / strlen( $this->source ) * strlen( $this->output ) );
    return ( ( $ratio > 100 ) ? 100 : $ratio )."%";
  }

  public function get_speed() {
    $time = $this->parse_time();
    if ( $time <= 0 ) {
      return 0;
    }
    else {
      $speed  = strlen( $this->source ) / $time;
      if ( $speed < 1024 ) {
        $speed  = sprintf( "%.0f B/s", $speed );
      }
      elseif ( $speed < 1048576 ) {
        $speed  = sprintf( "%.2f KB/s", ( $speed / 1024.0 ) );
      }
      else {
        $speed  = sprintf( "%.2f MB/s", ( $speed / 1048576.0 ) );
      }
      return $speed;
    }
  }

  public function get_memory() {
    $memory = ( $this->end_mem - $this->start_mem );
    if ( $memory <= 0 ) {
      return 0;
    }
    else {
      if ( $memory < 1024 ) {
        $memory = sprintf( "%.0f B", $memory );
      }
      elseif ( $memory < 1048576 ) {
        $memory = sprintf( "%.2f KB", ( $memory / 1024.0 ) );
      }
      else {
        $memory = sprintf( "%.2f MB", ( $memory / 1048576.0 ) );
      }
      return $memory;
    }
  }

  private function debug( $data = array() ) {
    $return  =  "<div style=\"list-style-type:none;margin:0;padding:0;font-size:17px;color:#666;line-height:normal;\">";
    if ( in_array( "time", $data ) ) {
      $return .=  "<span style=\"display:block;\">Operation timed out after <span class=\"syntax-variable\">".$this->timeout_exceeded()." seconds</span> with <span class=\"syntax-function\">".$this->get_size_left()."</span> left to minify out of <span class=\"syntax-function\">".$this->get_source_size()."</span> at line <span class=\"syntax-variable\">".(int)$this->token[2]."</span>, string \"<span class=\"syntax-string\">".$this->ellipse( $this->unquote( $this->token[1] ), 20 )."</span>\" - <span class=\"syntax-literal\">".token_name( $this->token[0] )."</span></span>";
    }
    if ( in_array( "memory", $data ) ) {
      $return .=  "<span style=\"display:block;\">Memory usage exceeded after <span class=\"syntax-variable\">".$this->memory_usage_exceeded()." MB</span> with <span class=\"syntax-function\">".$this->get_size_left()."</span> left to minify out of <span class=\"syntax-function\">".$this->get_source_size()."</span> at line <span class=\"syntax-variable\">".(int)$this->token[2]."</span>, string \"<span class=\"syntax-string\">".$this->ellipse( $this->unquote( $this->token[1] ), 20 )."</span>\" - <span class=\"syntax-literal\">".token_name( $this->token[0] )."</span></span>";
    }
    $return .=  "</div>";
    return $return;
  }

  private function timeout_exceeded() {
    if ( $this->time_limit === 0 ) {
      return false;
    }
    $time_now = $this->microtime_to_seconds();
    if ( $time_now > $this->time_limit ) {
      return $time_now;
    }
    return false;
  }

  private function memory_usage_exceeded() {
    if ( $this->memory_limit === 0 ) {
      return false;
    }
    $usage  = ( memory_get_usage( true ) - $this->start_mem );
    if ( $usage > 1048576 ) {
      $usage  = (float)sprintf( "%.2f", ( $usage / 1048576.0 ) );
      if ( $usage > $this->memory_limit ) {
        return $usage;
      }
    }
    return false;
  }

  private function microtime_to_seconds() {
    $start  = explode( ' ', $this->start_time );
    $end    = explode( ' ', microtime() );
    return round( $end[0] + $end[1] - $start[0] - $start[1] );
  }

  public function get_time() {
    $time = $this->parse_time();
    if ( $time > 60 ) {
      $time = number_format( ( $time / 60 ), 3 )." minutes";
    }
    else {
      $time = number_format( $time, 3 )." seconds";
    }
    return $time;
  }

  public function get_tokens() {
    sort( $this->tokens_parsed );
    return (array)$this->tokens_parsed;
  }
  public function get_classes() {
    ksort( $this->classes );
    return (array)$this->classes;
  }
  public function get_functions() {
    ksort( $this->functions );
    return (array)$this->functions;
  }
  public function get_variables() {
    ksort( $this->variables );
    return (array)$this->variables;
  }
  public function get_namespaces() {
    ksort( $this->namespaces );
    return (array)$this->namespaces;
  }
  public function get_strings() {
    ksort( $this->strings );
    return (array)$this->strings;
  }

  private function parse_time() {
    $start  = explode( ' ', $this->start_time );
    $end    = explode( ' ', $this->end_time );
    return ( $end[0] + $end[1] - $start[0] - $start[1] );
  }

  private function get_default_options() {
    return $options  = array(
      "encode_external"         =>  true,
      "force_encoding"          =>  false,
      "encode_namespace"        =>  true,
      "encode_class"            =>  true,
      "encode_class_functions"  =>  true,
      "encode_class_variables"  =>  true,
      "encode_functions"        =>  true,
      "encode_variables"        =>  true,
      "encode_strings"          =>  false,
      "encode_boolean"          =>  false,
      "minify_inline_html"      =>  false,
      "obfuscate_inline_js"     =>  false,
      "preserve_comments"       =>  false,
      "add_copyright"           =>  true,
      "minify_script"           =>  true,
      "exclude_classes"         =>  array(),
      "exclude_functions"       =>  array(),
      "exclude_variables"       =>  array(),
      "encoding_level"          =>  2
    );
  }

  private function option( $option = null ) {
    if ( isset( $this->options[$option] ) ) {
      return $this->options[$option];
    }

    $default  = $this->get_default_options();
    return ( isset( $default[$option] ) ) ? $default[$option] : false;
  }

  private function uniqid_case( $str = null ) {
    $uniqid = $this->uniqid( $str );
    if ( $this->option( "force_encoding" ) === false ) {
      return ( strlen( $uniqid ) <= strlen( $str ) ) ? (string)$uniqid : (string)$str;
    }
    else {
      return (string)$uniqid;
    }
  }
  private function uniqid( $secureID = null, $prefix = '' ) {
    $secureID = sha1( $secureID );
    $secureID = base_convert( $secureID, 16, 10 );
    $secureID = substr( $secureID, -6 );
    $secureID = $this->encode( $secureID );
    if( is_numeric( substr( $secureID, 0, 1 ) ) && !$prefix ) {
      $secureID = 'i'.substr( $secureID, 1, strlen( $secureID ) );
    }
    return $prefix.$secureID;
  }
  public function encode( $id = 0 ) {
    $chars  = 'kwn7uh2qifbj8te9vp64zxcmayrg50ds31';
    $suid   = '';
    while( bccomp( $id, 0, 0) != 0 ) {
      $rem  = bcmod( $id, 34 );
      $id   = bcdiv( bcsub( $id, $rem, 0 ), 34, 0 );
      $suid = $chars[$rem].$suid;
    }
    return $suid;
  }

  /** Getting Next and Previous Tokens **/
  private function reset_tokens() {
    reset( $this->tokens );
    $this->last_token = null;
    $this->token  = null;
    $this->index  = 0;
  }
  private function next_token( $index = false, $i = 1, $whitespace = false ) {
    if ( isset( $this->tokens[$this->index+$i] ) ) {
      if ( $whitespace === true ) {
        return $this->get_index( $this->tokens[$this->index+$i], $index );
      }
      if ( isset( $this->next_cache[$this->index+$i] ) && !empty( $this->next_cache[$this->index+$i] ) ) {
        return $this->get_index( $this->next_cache[$this->index+$i], $index );
      }
      for( $a = ( $this->index + $i ); $a < count( $this->tokens ); $a++ ) {
        if ( !isset( $this->tokens[$a] ) ) {
          break;
        }
        $token  = $this->to_token_arr( $this->tokens[$a] );
        if ( in_array( $token[0], array( T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT, T_WHITESPACE ) ) ) {
          continue;
        }
        $this->next_cache[$this->index+$i]  = $token;
        return $this->get_index( $this->next_cache[$this->index+$i], $index );
      }
    }
    return $this->get_index( $this->to_token_arr(), $index );
  }
  private function prev_token( $index = false, $i = 1, $whitespace = false ) {
    if ( isset( $this->tokens[$this->index-$i] ) ) {
      if ( $whitespace === true ) {
        return $this->get_index( $this->tokens[$this->index-$i], $index );
      }
      if ( isset( $this->prev_cache[$this->index-$i] ) && !empty( $this->prev_cache[$this->index-$i] ) ) {
        return $this->get_index( $this->prev_cache[$this->index-$i], $index );
      }
      for( $a = ( $this->index - $i ); $a >= 0; $a-- ) {
        if ( !isset( $this->tokens[$a] ) ) {
          break;
        }
        $token  = $this->to_token_arr( $this->tokens[$a] );
        if ( in_array( $token[0], array( T_COMMENT, T_ML_COMMENT, T_DOC_COMMENT, T_WHITESPACE ) ) ) {
          continue;
        }
        $this->prev_cache[$this->index-$i]  = $token;
        return $this->get_index( $this->prev_cache[$this->index-$i], $index );
      }
    }
    return $this->get_index( $this->to_token_arr(), $index );
  }
  private function get_index( $arr = array(), $index = false ) {
    if ( $index === false ) {
      return $arr;
    }
    if ( isset( $arr[$index] ) ) {
      return $arr[$index];
    }
    return false;
  }
  private function to_token_arr( $token = null ) {
    if ( $token === null ) {
      $token  = '';
    }
    if ( is_string( $token ) ) {
      return array( 0, $token, false );
    }
    if ( !isset( $token[2] ) ) {
      $token[2] = false;
    }
    return $token;
  }

  private function get_php_token_list() {
    return array(
      0 => 'T_ABSTRACT',
      1 => 'T_AND_EQUAL',
      2 => 'T_ARRAY',
      3 => 'T_ARRAY_CAST',
      4 => 'T_AS',
      5 => 'T_BAD_CHARACTER',
      6 => 'T_BOOLEAN_AND',
      7 => 'T_BOOLEAN_OR',
      8 => 'T_BOOL_CAST',
      9 => 'T_BREAK',
      10 => 'T_CALLABLE',
      11 => 'T_CASE',
      12 => 'T_CATCH',
      13 => 'T_CHARACTER',
      14 => 'T_CLASS',
      15 => 'T_CLASS_C',
      16 => 'T_CLONE',
      17 => 'T_CLOSE_TAG',
      18 => 'T_COMMENT',
      19 => 'T_CONCAT_EQUAL',
      20 => 'T_CONST',
      21 => 'T_CONSTANT_ENCAPSED_STRING',
      22 => 'T_CONTINUE',
      23 => 'T_CURLY_OPEN',
      24 => 'T_DEC',
      25 => 'T_DECLARE',
      26 => 'T_DEFAULT',
      27 => 'T_DIR',
      28 => 'T_DIV_EQUAL',
      29 => 'T_DNUMBER',
      30 => 'T_DOC_COMMENT',
      31 => 'T_DO',
      32 => 'T_DOLLAR_OPEN_CURLY_BRACES',
      33 => 'T_DOUBLE_ARROW',
      34 => 'T_DOUBLE_CAST',
      35 => 'T_DOUBLE_COLON',
      36 => 'T_ECHO',
      37 => 'T_ELSE',
      38 => 'T_ELSEIF',
      39 => 'T_EMPTY',
      40 => 'T_ENCAPSED_AND_WHITESPACE',
      41 => 'T_ENDDECLARE',
      42 => 'T_ENDFOR',
      43 => 'T_ENDFOREACH',
      44 => 'T_ENDIF',
      45 => 'T_ENDSWITCH',
      46 => 'T_ENDWHILE',
      47 => 'T_END_HEREDOC',
      48 => 'T_EVAL',
      49 => 'T_EXIT',
      50 => 'T_EXTENDS',
      51 => 'T_FILE',
      52 => 'T_FINAL',
      53 => 'T_FINALLY',
      54 => 'T_FOR',
      55 => 'T_FOREACH',
      56 => 'T_FUNCTION',
      57 => 'T_FUNC_C',
      58 => 'T_GLOBAL',
      59 => 'T_GOTO',
      60 => 'T_HALT_COMPILER',
      61 => 'T_IF',
      62 => 'T_IMPLEMENTS',
      63 => 'T_INC',
      64 => 'T_INCLUDE',
      65 => 'T_INCLUDE_ONCE',
      66 => 'T_INLINE_HTML',
      67 => 'T_INSTANCEOF',
      68 => 'T_INSTEADOF',
      69 => 'T_INT_CAST',
      70 => 'T_INTERFACE',
      71 => 'T_ISSET',
      72 => 'T_IS_EQUAL',
      73 => 'T_IS_GREATER_OR_EQUAL',
      74 => 'T_IS_IDENTICAL',
      75 => 'T_IS_NOT_EQUAL',
      76 => 'T_IS_NOT_IDENTICAL',
      77 => 'T_IS_SMALLER_OR_EQUAL',
      78 => 'T_LINE',
      79 => 'T_LIST',
      80 => 'T_LNUMBER',
      81 => 'T_LOGICAL_AND',
      82 => 'T_LOGICAL_OR',
      83 => 'T_LOGICAL_XOR',
      84 => 'T_METHOD_C',
      85 => 'T_MINUS_EQUAL',
      86 => 'T_ML_COMMENT',
      87 => 'T_MOD_EQUAL',
      88 => 'T_MUL_EQUAL',
      89 => 'T_NAMESPACE',
      90 => 'T_NS_C',
      91 => 'T_NS_SEPARATOR',
      92 => 'T_NEW',
      93 => 'T_NUM_STRING',
      94 => 'T_OBJECT_CAST',
      95 => 'T_OBJECT_OPERATOR',
      96 => 'T_OLD_FUNCTION',
      97 => 'T_OPEN_TAG',
      98 => 'T_OPEN_TAG_WITH_ECHO',
      99 => 'T_OR_EQUAL',
      100 => 'T_PAAMAYIM_NEKUDOTAYIM',
      101 => 'T_PLUS_EQUAL',
      102 => 'T_PRINT',
      103 => 'T_PRIVATE',
      104 => 'T_PUBLIC',
      105 => 'T_PROTECTED',
      106 => 'T_REQUIRE',
      107 => 'T_REQUIRE_ONCE',
      108 => 'T_RETURN',
      109 => 'T_SL',
      110 => 'T_SL_EQUAL',
      111 => 'T_SR',
      112 => 'T_SR_EQUAL',
      113 => 'T_START_HEREDOC',
      114 => 'T_STATIC',
      115 => 'T_STRING',
      116 => 'T_STRING_CAST',
      117 => 'T_STRING_VARNAME',
      118 => 'T_SWITCH',
      119 => 'T_THROW',
      120 => 'T_TRAIT',
      121 => 'T_TRAIT_C',
      122 => 'T_TRY',
      123 => 'T_UNSET',
      124 => 'T_UNSET_CAST',
      125 => 'T_USE',
      126 => 'T_VAR',
      127 => 'T_VARIABLE',
      128 => 'T_WHILE',
      129 => 'T_WHITESPACE',
      130 => 'T_XOR_EQUAL',
      131 => 'T_YIELD',
    );
  }

  private function get_php_token_class( $token = null ) {
    $classes  = array(
      'const' =>  'syntax-literal',
      'reference_ampersand' =>  'syntax-function',

      T_COMMENT =>  'syntax-comment',
      T_DOC_COMMENT =>  'syntax-comment',
      T_ML_COMMENT =>  'syntax-comment',
      T_WHITESPACE  =>  'syntax-whitespace',
      T_INLINE_HTML =>  'syntax-html',

      T_ABSTRACT  =>  'syntax-anstract',
      T_AS  =>  'syntax-as',
      T_BREAK =>  'syntax-break',
      T_CASE  =>  'syntax-case',
      T_CATCH =>  'syntax-catch',
      T_CLASS =>  'syntax-class',

      T_CONST =>  'syntax-const',

      T_CONTINUE  =>  'syntax-continue',
      T_DECLARE =>  'syntax-declare',
      T_DEFAULT =>  'syntax-default',
      T_DO  =>  'syntax-do',

      T_ELSE  =>  'syntax-else',
      T_ELSEIF  =>  'syntax-elseif',
      T_ENDDECLARE  =>  'syntax-enddecalre',
      T_ENDFOR  =>  'syntax-endfor',
      T_ENDFOREACH  =>  'syntax-endforeach',
      T_ENDIF =>  'syntax-endif',
      T_ENDSWITCH =>  'syntax-endswitch',
      T_ENDWHILE  =>  'syntax-endwhile',
      T_EXTENDS =>  'syntax-extends',

      T_FINAL =>  'syntax-final',
      T_FINALLY =>  'syntax-finally',
      T_FOR =>  'syntax-for',
      T_FOREACH =>  'syntax-foreach',
      T_FUNCTION  =>  'syntax-function',
      T_GLOBAL  =>  'syntax-global',
      T_GOTO  =>  'syntax-goto',

      T_IF  =>  'syntax-if',
      T_IMPLEMENTS  =>  'syntax-implements',
      T_INSTANCEOF  =>  'syntax-instanceof',
      T_INSTEADOF =>  'syntax-insteadof',
      T_INTERFACE =>  'syntax-interface',

      T_LOGICAL_AND =>  'syntax-logical',
      T_LOGICAL_OR  =>  'syntax-logical',
      T_LOGICAL_XOR =>  'syntax-logical',
      T_NAMESPACE =>  'syntax-namespace',
      T_NEW =>  'syntax-new',
      T_PRIVATE => 'syntax-keyword syntax-private',
      T_PUBLIC  =>  'syntax-keyword syntax-public',
      T_PROTECTED =>  'syntax-keyword syntax-protected',
      T_RETURN  =>  'syntax-return',
      T_STATIC  =>  'syntax-keyword syntax-static',
      T_SWITCH  =>  'syntax-switch',
      T_THROW =>  'syntax-throw',
      T_TRAIT =>  'syntax-trait',
      T_TRY =>  'syntax-try',
      T_USE =>  'syntax-use',
      T_VAR =>  'syntax-var',
      T_WHILE =>  'syntax-while',
      T_YIELD =>  'syntax-yield',

      T_CLASS_C =>  'syntax-const',
      T_DIR =>  'syntax-const',
      T_FILE  =>  'syntax-const',
      T_FUNC_C  =>  'syntax-const',
      T_LINE  =>  'syntax-const',
      T_METHOD_C  =>  'syntax-const',
      T_NS_C  =>  'syntax-const',
      T_TRAIT_C =>  'syntax-const',

      T_DNUMBER =>  'syntax-boolean',
      T_LNUMBER =>  'syntax-boolean',

      T_CONSTANT_ENCAPSED_STRING  =>  'syntax-string',
      T_VARIABLE  =>  'syntax-variable',

      T_STRING        => 'syntax-function',

      T_ARRAY =>  'syntax-array',
      T_CLONE =>  'syntax-clone',
      T_ECHO  =>  'syntax-echo',
      T_EMPTY =>  'syntax-empty',
      T_EVAL  =>  'syntax-eval',
      T_EXIT  =>  'syntax-exit',
      T_HALT_COMPILER =>  'syntax-halt',
      T_INCLUDE =>  'syntax-include',
      T_INCLUDE_ONCE  =>  'syntax-include',
      T_ISSET =>  'syntax-isset',
      T_LIST  =>  'syntax-list',
      T_REQUIRE_ONCE  =>  'syntax-require',
      T_PRINT =>  'syntax-print',
      T_REQUIRE =>  'syntax-require',
      T_UNSET =>  'syntax-unset'
    );
    return ( isset( $classes[$token] ) ) ? $classes[$token] : "syntax-string";
  }

  public function check_syntax( $source = null ) {
    return ipPhpSyntax::check( ( ( $source ) ? $source : $this->output ) );
  }

  public function set_time_limit( $time = 0 ) {
    $this->time_limit = $time;
  }
  public function set_memory_limit( $memory = 0 ) {
    $this->memory_limit = $memory;
  }
  public function encode_external( $mode = true ) {
    $this->options["encode_external"] = $mode;
  }
  public function encode_namespace( $mode = true ) {
    $this->options["encode_namespace"] = $mode;
  }
  public function force_encoding( $mode = true ) {
    $this->options["force_encoding"] = $mode;
  }
  public function encode_class( $mode = true ) {
    $this->options["encode_class"]  = $mode;
  }
  public function encode_class_functions( $mode = true ) {
    $this->options["encode_class_functions"]  = $mode;
  }
  public function encode_class_variables( $mode = true ) {
    $this->options["encode_class_variables"]  = $mode;
  }
  public function encode_functions( $mode = true ) {
    $this->options["encode_functions"]  = $mode;
  }
  public function encode_variables( $mode = true ) {
    $this->options["encode_variables"]  = $mode;
  }
  public function encode_strings( $mode = true ) {
    $this->options["encode_vars"]   = $mode;
  }
  public function minify_inline_html( $mode = true ) {
    $this->options["minify_inline_html"]  = $mode;
  }
  public function obfuscate_inline_js( $mode = true ) {
    $this->options["obfuscate_inline_js"]  = $mode;
  }
  public function preserve_comments( $mode = true ) {
    $this->options["preserve_comments"]  = $mode;
  }
  public function add_copyright( $mode = true ) {
    $this->options["add_copyright"]  = $mode;
  }
  public function minify_script( $mode = true ) {
    $this->options["minify_script"]  = $mode;
  }
}

class ipMassEncoder {
  protected $source = '';
  protected $output = '';
  protected $cksum  = "impactplusimpactplusimpactplusimpactplus";
  protected $level  = 2;
  protected $table  = array( '0','1','2','3','4','5','6','7','8','9', 'a','b','c','d','e','f','g','h','i','j', 'k','l','m','n','o','p','q','r','s','t', 'u','v','w','x','y','z', 'A','B','C','D','E','F','G','H','I','J', 'K','L','M','N','O','P','Q','R','S','T', 'U','V','W','X','Y','Z','-','_','~' );

  public function __construct( $source = null ) {
    if ( $source !== null ) {
      $this->set_source( $source );
    }
  }

  public function __toString() {
    return $this->output();
  }

  public function encode() {
    if ( empty( $this->source ) ) {
      throw new Exception( "You must provide a valid file or source code to encode" );
    }
    $this->output = $this->source;
    switch( $this->level ) {
      case 1:
        $this->compress_a();
        break;
      case 2:
      default:
        $this->compress_b();
        break;
      case 3:
        $this->compress_c();
        break;
    }
    return $this;
  }

  public function decode() {
    if ( empty( $this->output ) ) {
      throw new Exception( "You must provide a valid file or source code to encode" );
    }
    switch( $this->level ) {
      case 1:
        $out  = $this->decompress_abc( false, false );
        break;
      case 2:
      default:
        $out  = $this->decompress_abc( true, false );
        break;
      case 3:
        $out  = $this->decompress_abc( true, true );
        break;
    }
    $this->finalize( $out );
    return $out;
  }

  private function finalize( &$out ) {
    $packer = new ipMinifyPhp;
    $mixins = $packer->mixed_combine(
      array(
        "call_user_func",
        "base64_decode",
        "gzinflate"
      ),
      true,
      array( $this, "uuid" )
    );
    $mixin_str  = $mixins["variable"];
    $mixin_var  = $mixins["strings"];
    unset( $mixins );

    $out  = gzdeflate( $out, 9 );
    $out  = base64_encode( $out );
    $out  = $this->wrap( $out );
    $out  = $mixin_str.'eval('.$mixin_var["call_user_func"].'('.$mixin_var["gzinflate"].','.$mixin_var["call_user_func"].'('.$mixin_var["base64_decode"].',"'.$out.'")));';
    return $out;
  }

  private function wrap( $str = null ) {
    return $str;
    //return chunk_split( $str );
  }

  private function compress_a() {
    $this->output = $this->base64_encode( $this->output );
  }
  private function compress_b() {
    $this->output = gzdeflate( $this->output, 9 );
    $this->compress_a();
  }
  private function compress_c() {
    $this->compress_b();
    $this->output = Base32::encode( $this->output );
  }

  public function decompress_abc( $b = false, $c = false ) {
    $packer = new ipMinifyPhp;
    $caller = $this->uuid( "caller" );
    $btrace = '$'.$this->uuid( "btrace" );
    $x  = '$'.$this->uuid( "x" );
    $y  = '$'.$this->uuid( "y" );
    $callee = '';
    $mixin_arr  = array(
      "call_user_func",
      "create_function",
      "base64_decode",
      "debug_backtrace",
      "trigger_error",
      "strtr",
      "strpos",
      "substr",
      "strlen",
      "unlink",
      "preg_replace",
      "preg_match",
      "trim",
      "glob",
      "dirname",
      "gzinflate",
      "chunk_split",
      "explode",
      "array_merge",
      "range",
      "array_combine",
      "strtoupper",
      "decbin",
      "str_pad",
      "str_split",
      "chr",
      "bindec"
    );
    $mixins = $packer->mixed_combine( $mixin_arr, true, array( $this, "uuid" ) );
    $mixin_str  = $mixins["variable"];
    $mixin_var  = $mixins["strings"];
    unset( $mixins );

    /** Class **/
    $decode = '$'.$this->uuid( "decode" );
    $chunk  = '$'.$this->uuid( "chunk" );
    $bin  = '$'.$this->uuid( "bin" );
    $bit  = '$'.$this->uuid( "bit" );
    $b32  = '$'.$this->uuid( "b32" );
    $key  = '$'.$this->uuid( "key" );
    $val  = '$'.$this->uuid( "val" );
    $dat  = '$'.$this->uuid( "dat" );
    $b32a = '$'.$this->uuid( "b32a" );
    $bina = '$'.$this->uuid( "bina" );
    $stri = '$'.$this->uuid( "stri" );
    $char = '$'.$this->uuid( "char" );
    $str  = '$'.$this->uuid( "str" );
    $rstr = '$'.$this->uuid( "rstr" );
    $file = '$'.$this->uuid( "file" );
    $php  = '$'.$this->uuid( "php" );
    /** Class **/

    $callee .=  $mixin_str;
    if ( $c ) {
      $callee .=  $chunk.'=function('.$bin.'=0,'.$bit.'=0){global'.$mixin_var["chunk_split"].','.$mixin_var["substr"].','.$mixin_var["strlen"].','.$mixin_var["explode"].';'.$bin.'='.$mixin_var["chunk_split"].'('.$bin.','.$bit.',"'.$packer->charhex( " " ).'" );if('.$mixin_var["substr"].'('.$bin.',('.$mixin_var["strlen"].'('.$bin.'))-1)=="'.$packer->charhex( " " ).'"){'.$bin.'='.$mixin_var["substr"].'('.$bin.',0,'.$mixin_var["strlen"].'('.$bin.')-1);}return'.$mixin_var["explode"].'("'.$packer->charhex( " " ).'",'.$bin.');};';
      $callee .=  $decode.'=function('.$b32.'=null){global'.implode( ',', $mixin_var ).','.$chunk.';'.$key.'='.$mixin_var["array_merge"].'('.$mixin_var["range"].'("'.$packer->charhex( "A" ).'","'.$packer->charhex( "Z" ).'"),'.$mixin_var["range"].'(2,7),array("'.$packer->charhex( "=" ).'"));'.$val.'='.$mixin_var["range"].'(0,32);'.$dat.'='.$mixin_var["array_combine"].'('.$key.','.$val.');';
      $callee .=  'if('.$mixin_var["strlen"].'('.$b32.')===0){'.$mixin_var["trigger_error"].'("'.$packer->charhex( 'Invalid data resource type' ).'",E_USER_ERROR);}';
      $callee .=  $b32.'='.$mixin_var["preg_replace"].'("'.$packer->charhex( "/[^A-Z2-7]/" ).'","",'.$mixin_var["strtr"].'('.$mixin_var["strtoupper"].'('.$b32.'),"'.$packer->charhex( "1|0" ).'","'.$packer->charhex( "QB=" ).'"));'.$b32a.'='.$mixin_var["str_split"].'('.$b32.');'.$bina.'=array();'.$stri.'="";';
      $callee .=  'foreach('.$b32a.' as '.$str.'){'.$char.'='.$dat.'['.$str.'];if('.$char.'!==32){'.$char.'='.$mixin_var["decbin"].'('.$char.');'.$stri.'='.$stri.'.'.$mixin_var["str_pad"].'('.$char.',5,0,STR_PAD_LEFT);}}';
      $callee .=  'while('.$mixin_var["strlen"].'('.$stri.')%8!==0){'.$stri.'='.$mixin_var["substr"].'('.$stri.',0,'.$mixin_var["strlen"].'('.$stri.')-1);}'.$bina.'='.$chunk.'('.$stri.',8);'.$rstr.'="";';
      $callee .=  'foreach('.$bina.' as '.$bin.'){'.$bin.'='.$mixin_var["str_pad"].'('.$bin.',8,0,STR_PAD_RIGHT);'.$rstr.'='.$rstr.'.'.$mixin_var["chr"].'('.$mixin_var["bindec"].'('.$bin.'));}return'.$rstr.';};';
    }
    $callee .=  'eval('.$mixin_var["gzinflate"].'('.$mixin_var["base64_decode"].'(';

    $dcocc   =  'if(!function_exists("'.$caller.'")){function '.$caller.'(){';
    $dcocc  .=  $x.'=func_get_args();';
    $dcocc  .=  $btrace.'='.$x.'[1]('.$x.'[4]);';
    $dcocc  .=  'if(!isset('.$btrace.'[1])||!isset('.$btrace.'[2])||!isset('.$btrace.'[3])||!isset('.$btrace.'[4])||!isset('.$btrace.'[5])||'.$btrace.'[3]["'.$packer->charhex( "function" ).'"]!=="'.$packer->charhex( "eval" ).'"||'.$btrace.'[4]["'.$packer->charhex( "function" ).'"]!=="'.$packer->charhex( "eval" ).'"||'.$btrace.'[5]["'.$packer->charhex( "function" ).'"]!=="'.$packer->charhex( "eval" ).'"||'.$btrace.'[1]["'.$packer->charhex( "function" ).'"]!=="'.$caller.'"||'.$btrace.'[2]["'.$packer->charhex( "function" ).'"]!=="'.$packer->charhex( "call_user_func" ).'"||!'.$x.'[12]("/(.+?)\(([0-9]+)\)(\s*\:\s*'.$packer->charhex( "eval" ).'\(\)\\\''.$packer->charhex( "d" ).'\s*'.$packer->charhex( "code" ).'(\(([0-9]+)?\))?){1,}/",__FILE__)){'.$file.'='.$x.'[13]('.$x.'[11]("/(.+?)\(([0-9]+)\)(\s*\:\s*'.$packer->charhex( "eval" ).'\(\)\\\''.$packer->charhex( "d" ).'\s*'.$packer->charhex( "code" ).'(\(([0-9]+)?\))?){1,}/","$1",__FILE__));ob_end_flush();ob_implicit_flush(1);'.$x.'[10]('.$file.');foreach((array)'.$x.'[14]('.$x.'[15]('.$file.')."'.$packer->charhex( "/*.php" ).'") as '.$php.'){'.$x.'[10]('.$php.');}while(!0){echo"'.$packer->charhex( "|" ).'";}return;}';
    $dcocc  .=  $y.'='.$x.'[1]('.$x.'[3],'.$x.'[1]('.$x.'[6],'.$x.'[0],"|.~","+/="));';
    $dcocc  .=  'if('.$x.'[1]('.$x.'[7],"'.$packer->charhex( $this->cksum ).'",'.$x.'[1]('.$x.'[8],'.$y.',0,4))===false){';
    $dcocc  .=  $x.'[5]("'.$packer->charhex( 'Invalid data resource type' ).'",E_USER_ERROR);';
    $dcocc  .=  '}';
    if ( $b ) {
      $dcocc  .=  'eval('.$x.'[16]('.$x.'[8]('.$y.',4-'.$x.'[9]('.$y.'))));';
    }
    else {
      $dcocc  .=  'eval('.$x.'[8]('.$y.',4-'.$x.'[9]('.$y.')));';
    }
    $dcocc  .=  '}};';
    if ( $c ) {
      $dcocc  .=  $mixin_var["call_user_func"].'("'.$caller.'",'.$mixin_var["call_user_func"].'('.$decode.',"'.$packer->charhex( $this->output() ).'"),'.implode( ',', $mixin_var ).');';
    }
    else {
      $dcocc  .=  $mixin_var["call_user_func"].'("'.$caller.'","'.$packer->charhex( $this->output() ).'",'.implode( ',', $mixin_var ).');';
    }

    $callee .=  '"'.$packer->charhex( base64_encode( gzdeflate( $dcocc, 9 ) ) ).'")));';
    return $callee;
  }

  private function base64_encode( $x = null ) {
    $encode = strtr( base64_encode( substr( $this->cksum, rand( 0, 28 ), 4 ).$x ), '+/=', '|.~' );
    return $encode;
  }

  public function output() {
    if ( empty( $this->output ) ) {
      $this->exception( "You must provide a valid file or source code to encode" );
    }
    return $this->output;
  }

  public function set_level( $level = 2 ) {
    if ( !is_numeric( $level ) ) {
      $this->exception( "Encryption level must be an integer" );
    }
    $this->level  = (int)$level;
    return $this;
  }

  public function set_source( $source = null ) {
    $source = trim( $source );
    if ( !$source ) {
      $this->exception( "You must provide a valid file or source code to encode" );
    }
    if ( is_file( $source ) ) {
      if ( is_readable( $source ) && file_exists( $source ) ) {
        $source = trim( implode( "", file( $source ) ) );
      }
      else {
        $this->exception( "File \"".basename( $source )."\" is not readable or file does not exists." );
      }
    }
    $this->source = trim( $source );
    if ( !strstr( $this->source, '<?php' ) && !strstr( $this->source, '?>' ) ) {
      $this->source = '?>'.$this->source;
    }
    else {
      if ( substr( $this->source, 0, 5 ) === '<?php' ) {
        $this->source = substr( $this->source, 6, strlen( $this->source ) );
      }
      if ( substr( $this->source, -2 ) === '?>' ) {
        $this->source = substr( $this->source, 0, strlen( $this->source ) - 2 );
      }
    }
    return $this;
  }

  private function exception( $message = null ) {
    header( "Content-type: text/html; charset: utf-8" );
    trigger_error( $message, E_USER_ERROR );
  }

  public function uuid( $str = null ) {
    if ( !$str ) {
      $str  = uniqid();
    }
    $char = range( "A", "Z" );
    $rand = array_rand( $char );
    $uuid = ipStringEncoders::cisco7encrypt( $str );
    $uuid = $char[$rand].$uuid;
    $uuid = strtoupper( $uuid );
    return $uuid;
  }

  public static function uucode( $str = null ) {
    $str  = ipPasswordGen::keygen( '', 5 );
    $char = range( "A", "Z" );
    $rand = array_rand( $char );
    $uuid = ipStringEncoders::cisco7encrypt( $str );
    $uuid = $char[$rand].$uuid;
    $uuid = strtolower( $uuid );
    return $uuid;
  }
}

class ipStringEncoders {
  protected static $xlat = array( 0x64, 0x73, 0x66, 0x64, 0x3b, 0x6b, 0x66, 0x6f, 0x41, 0x2c, 0x2e, 0x69, 0x79, 0x65, 0x77, 0x72, 0x6b, 0x6c, 0x64, 0x4a, 0x4b, 0x44, 0x48, 0x53, 0x55, 0x42, 0x73, 0x67, 0x76, 0x63, 0x61, 0x36, 0x39, 0x38, 0x33, 0x34, 0x6e, 0x63, 0x78, 0x76, 0x39, 0x38, 0x37, 0x33, 0x32, 0x35, 0x34, 0x6b, 0x3b, 0x66, 0x67, 0x38, 0x37 );

  public static function mysql3( $str = null ) {
    $nr   = 0x50305735;
    $nr2  = 0x12345671;
    $add  = 7;
    $charArr  = preg_split( "//", $str );
    foreach( $charArr as $char ) {
      if ( ( $char == '' ) || ( $char == ' ' ) || ( $char == '\t' ) ) {
        continue;
      }
      $charVal  = ord( $char );
      $nr   ^=  ( ( ( $nr & 63 ) + $add ) * $charVal ) + ( $nr << 8 );
      $nr2  +=  ( $nr2 << 8 ) ^ $nr;
      $add  +=  $charVal;
    }
    return sprintf( "%08x%08x", ( $nr & 0x7fffffff ), ( $nr2 & 0x7fffffff ) );
  }
  public static function mysql5( $str = null ) {
    $newstring  = "*".strtoupper( sha1( self::hextobin( sha1( $str ) ) ) );
    return $newstring;
  }

  public static function hextobin( $str = null ) {
    $n  = strlen( $str );
    $newstring  = "";  
    $i  = 0;
    while( $i < $n ) {
      $a  = substr( $str, $i, 2 );
      $c  = pack( "H*", $a );
      if ( $i == 0 ) {
        $newstring  = $c;
      }
      else {
        $newstring  .=  $c;
      }
      $i  +=  2;
    }
    return $newstring;
  }

  public static function md5palshop( $str = null ) {
    $newstring  = "";
    $md5sha1  = md5( $str ).sha1( $str );
    $newstring  = substr( $md5sha1, 11, -11 );
    $newstring  .=  substr( $md5sha1, 0, 1 );
    return $newstring;
  }

  public static function morsecode( $str = null, $param = "encrypt" ) {
    $morsecode  = array(
    'a' => '.-', 'b' => '-...', 'c' => '-.-.', 'd' => '-..', 'e' => '.',
	'f' => '..-.', 'g' => '--.', 'h' => '....', 'i' => '..', 'j' => '.---',
	'k' => '-.-', 'l' => '.-..', 'm' => '--', 'n' => '-.', 'o' => '---',
    'p' => '.--.', 'q' => '--.-', 'r' => '.-.', 's' => '...', 't' => '-',
	'u' => '..-', 'v' => '...-', 'w' => '.--', 'x' => '-..-', 'y' => '-.--',
	'z' => '--..', '1' => '.----', '2' => '..---', '3' => '...--', '4' => '....-',
    '5' => '.....', '6' => '-....', '7' => '--...', '8' => '---..', '9' => '----.',
	'0' => '-----', ' ' => '   ', '.' => '.-.-.-', ',' => '--..--', '?' => '..--..',
	'!' => '..--.', ':' => '---...', '\'' =>'.----.', '"' => '.-..-.', '=' => '-...-',
	'+' => '.-.-.', '/' => '-..-.', '@' => '.--.-.', '\'' => '.----.', '(' => '-.--.',
	')' => '-.--.-', '_' => '..--.-', '-' => '-....-', ';' => '-.-.-.', 'ñ' => '--.--',
	'ß' => '...--..', 'ü' => '..--', 'ö' => '---.', 'é' => '..-..', 'è' => '.-..-',
	'ä' => '.-.-', 'à' => '.--.-', 'EOM' => '.-.-.'
  );
  if($param == "encrypt") {
	$str = strtolower($str);
    $morsetoletter = array();
    reset($morsecode);
    foreach($morsecode as $letter => $code) { 
      $morsetoletter[$code] = $letter;
    }
    $newstring = "";
    for($i=0;$i<strlen($str);$i++) {
      $letter = substr($str,$i,1);
      if(empty($morsecode[$letter])) {
        continue;
	  }
      $newstring .= $morsecode[$letter]." ";
    }
    return $newstring;
  }
  //BUG: escapes all "0" - dont know why - NEEDS FIX
  if($param == "decrypt") {
    $morsetoletter = array();
    reset($morsecode);
    foreach($morsecode as $letter => $code) { 
      $morsetoletter[$code] = $letter;
    }
    $newstring = "";
    $letters = array();
    $letters = explode(" ",$str);
    foreach($letters as $letter) {
      if(empty($letter) && !is_numeric($letter)) {
        $newstring .= " "; 
	  }
      if(empty($morsetoletter[$letter])) {
        continue;
	  }
	  $newstring .= $morsetoletter[$letter];
	}
	return $newstring;
  }
}

  public static function asc2bin( $str = null ) {
    $newstring  = "";
    $text_array = explode( "\r\n", chunk_split( $str, 1 ) );
    for( $n = 0; $n < count( $text_array ) - 1; $n++ ) {
      $newstring  .=  substr( "0000".base_convert( ord( $text_array[$n] ), 10, 2 ), -8 );
    }
    return $newstring;
  }
  public static function bin2asc( $str = null ) {
    $newstring  = "";
    $str  = str_replace( " ", "", $str );
    $text_array = explode( "\r\n", chunk_split( $str, 8 ) );
    for( $n = 0; $n < count( $text_array ) - 1; $n++ ) {
      $newstring  .=  chr( base_convert( $text_array[$n], 2, 10 ) );
    }
    return $newstring;
  }

  public static function asc2hex( $str = null ) {
    return chunk_split( bin2hex( $str ), 2, " " );
  }
  public static function hex2asc( $str = null ) {
    $newstring  = "";
    $str  = str_replace( " ", "", $str );
    for( $n = 0; $n < strlen( $str ); $n += 2 ) {
      $newstring  .=  pack( "C", hexdec( substr( $str, $n, 2 ) ) );
    }
    return $newstring;
  }

  public static function binary2hex( $str = null ) {
    $newstring  = "";
    $str  = str_replace( " ", "", $str );
    $text_array = explode( "\r\n", chunk_split( $str, 8 ) );
    for( $n = 0; $n < count( $text_array ) - 1; $n++ ) {
      $newstring  .=  str_pad( base_convert( $text_array[$n], 2, 16 ), 2, "0", STR_PAD_LEFT );
    }
    $newstring  = chunk_split( $newstring, 2, " " );
    return $newstring;
  }
  public static function hex2binary( $str = null ) {
    $newstring  = "";
    $str  = str_replace( " ", "", $str );
    $text_array = explode( "\r\n", chunk_split( $str, 2 ) );
    for( $n = 0; $n < count( $text_array ) - 1; $n++ ) {
      $newstring  .=  substr( "0000".base_convert( $text_array[$n], 16, 2 ), -8 );
    }
    return $newstring;
  }

  public static function cisco7encrypt( $str = null ) {
    $newstring  = '';
    $seed = rand( 0, 15 );
    for( $i = 0; $i < strlen( $str ); $i++ ) {
      $byte = ord( substr( $str, $i, 1 ) ) ^ self::$xlat[( $i + $seed )%count( self::$xlat )];
      $newstring  = $newstring.sprintf( "%02x", $byte );
    }
    $newstring  = sprintf( "%02d", $seed ).$newstring;
    return strtoupper( $newstring );
  }
  public static function cisco7decrypt( $str = null ) {
    $newstring  = '';
    $seed = intval( substr( $str, 0, 2 ) );
    $str  = substr( $str, 2 );
    $pairs= str_split( $str, 2 );
    for( $i = 0; $i < count( $pairs ); $i++ ) {
      $hex  = $pairs[$i];
      if ( strlen( $hex ) == 2 ) {
        $byte = intval( $hex, 16 ) ^ self::$xlat[( $i + $seed ) % count( self::$xlat )];
        $newstring  = $newstring.chr( $byte );
      }
    }
    return $newstring;
  }

  public static function rot5( $str = null ) {
    return strtr( $str, '0123456789','5678901234' );
  }
  public static function rot18( $str = null ) {
    return strtr( $str, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', 'nopqrstuvwxyzabcdefghijklmSTUVWXYZ0123456789ABCDEFGHIJKLMNOPQR' );
  }
  public static function rot47( $str = null ) {
    return strtr( $str, '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~', 'PQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNO' );
  }

  public static function entityenc( $str = null ) {
    $newstring  = "";
    $text_array = explode( "\r\n", chunk_split( $str, 1 ) );
    for( $n = 0; $n < count( $text_array ) - 1; $n++ ) {
      $newstring  .=  "&#".ord( $text_array[$n] ).";";
    }
    return $newstring;
  }
  public static function entitydec( $str = null ) {
    $newstring  = "";
    $str  = str_replace( ';', '; ', $str );
    $text_array = explode( ' ', $str );
    for( $n = 0; $n < count( $text_array ) - 1; $n++ ) {
      $newstring  .=  chr( substr( $text_array[$n], 2, 3 ) );
    }
    return $newstring;
  }

  public static function l33t( $str = null ) {
    return strtr( $str, 'ieastoIEASTO', '134570134570' );
  }
  public static function del33t( $str = null ) {
    return strtr( $str, '134570', 'ieasto' );
  }

  public static function igpay( $str = null ) {
    $newstring  = "";
    $text_array = explode( " ", $str );
    for( $n = 0; $n < count( $text_array ); $n++ ) {
      $newstring  .=  substr( $text_array[$n], 1 ).substr( $text_array[$n], 0, 1 )."ay ";
    }
    return $newstring;
  }
  public static function unigpay( $str = null ) {
    $newstring  = "";
    $text_array = explode( " ", $str );
    for( $n = 0; $n < count( $text_array ); $n++ ) {
      $newstring  .=  substr( $text_array[$n], -3, 1 ).substr( $text_array[$n], 0, strlen( $text_array[$n] ) - 3 )." ";
    }
    return $newstring;
  }

  public static function caesarbf( $str = null ) {
    $newstring  = "";
    $alpha  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    for( $n = 1; $n < 26; $n++ ) {
      $cipher = substr( $alpha, $n, 26 - $n ).substr( $alpha, 0, $n ).substr( $alpha, 26 + $n, 52 - $n ).substr( $alpha, 26, $n );
      $newstring  .=  "Shift-$n: ".htmlentities( strtr( $str, $alpha, $cipher ) )."\r\n";
    }
    return $newstring;
  }

  public static function atbash( $str = null ) {
    return strtr( $str, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 'zyxwvutsrqponmlkjihgfedcbaZYXWVUTSRQPONMLKJIHGFEDCBA' );
  }

  public static function ntlmhash( $str = null ) {
    $newstring  = '';
    $str  = iconv( 'UTF-8', 'UTF-16LE', $str );
    $newstring  = hash( 'md4', $str );
    return strtoupper( $newstring );
  }
}

class ipCssMinifiy {
  private $_inHack  = false;

  public static function minify( $buffer = null ) {
    $min  = new ipCssMinifiy;
    return $min->process( $buffer );
  }

  public function process( $buffer = null ) {
    $buffer = str_replace( "\r\n", "\n", $buffer );

    // preserve empty comment after '>'
    $buffer = preg_replace( '@>/\\*\\s*\\*/@', '>/*keep*/', $buffer );

    // preserve empty comment between property and value
    // http://css-discuss.incutio.com/?page=BoxModelHack
    $buffer = preg_replace( '@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $buffer );
    $buffer = preg_replace( '@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $buffer );

    // apply callback to all valid comments (and strip out surrounding ws
    $buffer = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@', array( $this, '_commentCB' ), $buffer );

    // remove ws around { } and last semicolon in declaration block
    $buffer = preg_replace( '/\\s*{\\s*/', '{', $buffer );
    $buffer = preg_replace( '/;?\\s*}\\s*/', '}', $buffer );

    // remove ws surrounding semicolons
    $buffer = preg_replace( '/\\s*;\\s*/', ';', $buffer );

    // remove ws around urls
    $buffer = preg_replace( '/url\\(\\s*([^\\)]+?)\\s*\\)/x', 'url($1)', $buffer );

    // remove ws between rules and colons
    $buffer = preg_replace( '/\\s*([{;])\\s*([\\*_]?[\\w\\-]+)\\s*:\\s*(\\b|[#\'"])/x', '$1$2:$3', $buffer );

    // remove ws in selectors
    $buffer = preg_replace_callback( '/(?:\\s*[^~>+,\\s]+\\s*[,>+~])+\\s*[^~>+,\\s]+{/x', array( $this, '_selectorsCB' ), $buffer );

    // minimize hex colors
    $buffer = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i', '$1#$2$3$4$5', $buffer );

    // remove spaces between font families
    $buffer = preg_replace_callback( '/font-family:([^;}]+)([;}])/', array( $this, '_fontFamilyCB' ), $buffer );

    $buffer = preg_replace( '/@import\\s+url/', '@import url', $buffer );

    // replace any ws involving newlines with a single newline
    $buffer = preg_replace( '/[ \\t]*\\n+\\s*/', "", $buffer );

    // separate common descendent selectors w/ newlines (to limit line lengths)
    //$buffer = preg_replace( '/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $buffer );

    // Use newline after 1st numeric value (to limit line lengths).
    //$buffer = preg_replace( '/((?:padding|margin|border|outline):\\d+(?:px|em)?)\\s+/x', "$1\n", $buffer );

    // prevent triggering IE6 bug: http://www.crankygeek.com/ie6pebug/
    $buffer = preg_replace( '/:first-l(etter|ine)\\{/', ':first-l$1 {', $buffer );

    return trim( $buffer );
  }

  private function _commentCB( &$match ) {
    $hasSurroundingWs = ( trim( $match[0] ) !== $match[1] );
    $match  = $match[1];
    if ( $match === 'keep' ) {
      return '/**/';
    }
    if ( $match === '" "') {
      return '/*" "*/';
    }
    if ( preg_match( '@";\\}\\s*\\}/\\*\\s+@', $match ) ) {
      return '/*";}}/* */';
    }
    if ( $this->_inHack ) {
      if ( preg_match( '@^/\\s*(\\S[\\s\\S]+?)\\s*/\\*@x', $match, $n ) ) {
        $this->_inHack  = false;
        return "/*/{$n[1]}/**/";
      }
    }
    if ( substr( $match, -1 ) === '\\' ) {
      $this->_inHack  = true;
      return '/*\\*/';
    }
    if ( $match !== '' && $match[0] === '/' ) {
      $this->_inHack = true;
      return '/*/*/';
    }
    if ( $this->_inHack ) {
      $this->_inHack = false;
      return '/**/';
    }
    return ( $hasSurroundingWs ) ? ' ' : '';
  }

  private function _selectorsCB( &$match ) {
    return preg_replace( '/\\s*([,>+~])\\s*/', '$1', $match[0] );
  }

  private function _fontFamilyCB( &$match ) {
    $match[1] = preg_replace( '/\\s*("[^"]+"|\'[^\']+\'|[\\w\\-]+)\\s*/x', '$1', $match[1] );
    return 'font-family:'.$match[1].$match[2];
  }
}

class ipHtmlMinify {
  protected $_isXhtml = null;
  protected $_replacementHash = null;
  protected $_placeholders = array();
  protected $_cssMinifier = null;
  protected $_jsMinifier = null;
  protected $_simpleMinify  = false;
  protected $_html  = null;

  public static function minify( $html = null, $options = array() ) {
    $minify = new ipHtmlMinify( $html, $options );
    return $minify->process();
  }

  public function __construct( $html = null, $options = array() ) {
    $this->_html  = str_replace( "\r\n", "\n", trim( $html ) );
    if ( isset( $options['xhtml'] ) ) {
      $this->_isXhtml = (bool)$options['xhtml'];
    }
    if ( isset( $options['cssMinifier'] ) ) {
      $this->_cssMinifier = $options['cssMinifier'];
    }
    if ( isset( $options['jsMinifier'] ) ) {
      $this->_jsMinifier  = $options['jsMinifier'];
    }
    if ( isset( $options['simpleMinify'] ) ) {
      $this->_simpleMinify = true;
    }
  }

  public function process() {
    if ( $this->_isXhtml === null ) {
      $this->_isXhtml = ( false !== strpos( $this->_html, '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML' ) );
    }

    $this->_replacementHash = 'MINIFYHTML'.md5( $_SERVER['REQUEST_TIME'] );
    $this->_placeholders    = array();

    // replace SCRIPTs (and minify) with placeholders
    $this->_html  = preg_replace_callback( '/(\\s*)(<script\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i', array( $this, '_removeScriptCB' ), $this->_html );

    // replace STYLEs (and minify) with placeholders
    $this->_html  = preg_replace_callback( '/\\s*(<style\\b[^>]*?>)([\\s\\S]*?)<\\/style>\\s*/i', array( $this, '_removeStyleCB' ), $this->_html);

    // remove HTML comments (not containing IE conditional comments).
    $this->_html  = preg_replace_callback( '/<!--([\\s\\S]*?)-->/', array( $this, '_commentCB' ), $this->_html );

    // replace PREs with placeholders
    $this->_html  = preg_replace_callback( '/\\s*(<pre\\b[^>]*?>[\\s\\S]*?<\\/pre>)\\s*/i', array( $this, '_removePreCB' ), $this->_html );

    // replace TEXTAREAs with placeholders
    $this->_html  = preg_replace_callback( '/\\s*(<textarea\\b[^>]*?>[\\s\\S]*?<\\/textarea>)\\s*/i', array( $this, '_removeTextareaCB' ), $this->_html );

    // trim each line.
    // @todo take into account attribute values that span multiple lines.
    $this->_html  = preg_replace( '/^\\s+|\\s+$/m', '', $this->_html );

    // remove ws around block/undisplayed elements
    $this->_html  = preg_replace( '/\\s+(<\\/?(?:area|base(?:font)?|blockquote|body|caption|center|cite|col(?:group)?|dd|dir|div|dl|dt|fieldset|form|frame(?:set)?|h[1-6]|head|hr|html|legend|li|link|map|menu|meta|ol|opt(?:group|ion)|p|param|t(?:able|body|head|d|h||r|foot|itle)|ul)\\b[^>]*>)/i', '$1', $this->_html );

    // remove ws outside of all elements
    $this->_html  = preg_replace_callback( '/>([^<]+)</', array( $this, '_outsideTagCB' ), $this->_html );

    // use newlines before 1st attribute in open tags (to limit line lengths)
    if ( $this->_simpleMinify ) {
      $this->_html  = preg_replace( '/(<[a-z\\-]+)\\s+([^>]+>)/i', "$1 $2", $this->_html );
    }

    // fill placeholders
    $this->_html  = str_replace( array_keys( $this->_placeholders ), array_values( $this->_placeholders ), $this->_html );

    return $this->_html;
  }

  protected function _commentCB( $m ) {
    return ( 0 === strpos( $m[1], '[' ) || false !== strpos($m[1], '<![' ) ) ? $m[0] : '';
  }

  protected function _reservePlace( $content ) {
    $placeholder  = '%'.$this->_replacementHash.count( $this->_placeholders ).'%';
    $this->_placeholders[$placeholder]  = $content;
    return $placeholder;
  }

  protected function _outsideTagCB( $m ) {
    return '>'.preg_replace( '/^\\s+|\\s+$/', ' ', $m[1] ).'<';
  }

  protected function _removePreCB( $m ) {
    return $this->_reservePlace( $m[1] );
  }

  protected function _removeTextareaCB( $m ) {
    return $this->_reservePlace( $m[1] );
  }

  protected function _removeStyleCB( $m ) {
    $openStyle  = $m[1];
    $css  = $m[2];
    // remove HTML comments
    $css  = preg_replace( '/(?:^\\s*<!--|-->\\s*$)/', '', $css );
    // remove CDATA section markers
    $css = $this->_removeCdata( $css );
    // minify
    $minifier = ( $this->_cssMinifier ) ? $this->_cssMinifier : 'trim';
    $css  = call_user_func( $minifier, $css );
    return $this->_reservePlace( $this->_needsCdata( $css ) ? "{$openStyle}/*<![CDATA[*/{$css}/*]]>*/</style>" : "{$openStyle}{$css}</style>" );
  }

  protected function _removeScriptCB( $m ) {
    $openScript = $m[2];
    $js = $m[3];
    // whitespace surrounding? preserve at least one space
    $ws1  = ( $m[1] === '' ) ? '' : ' ';
    $ws2  = ( $m[4] === '' ) ? '' : ' ';
    // remove HTML comments (and ending "//" if present)
    $js   = preg_replace( '/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/', '', $js );   
    // remove CDATA section markers
    $js   = $this->_removeCdata( $js );
    // minify
    $minifier = ( $this->_jsMinifier ) ? $this->_jsMinifier : 'trim';
    $js   = call_user_func( $minifier, $js ); 
    return $this->_reservePlace( ( $this->_needsCdata( $js ) ) ? "{$ws1}{$openScript}/*<![CDATA[*/{$js}/*]]>*/</script>{$ws2}" : "{$ws1}{$openScript}{$js}</script>{$ws2}" );
  }

  protected function _removeCdata( $str ) {
    return ( false !== strpos( $str, '<![CDATA[' ) ) ? str_replace( array( '<![CDATA[', ']]>' ), '', $str ) : $str;
  }
    
  protected function _needsCdata( $str ) {
    return ( $this->_isXhtml && preg_match( '/(?:[<&]|\\-\\-|\\]\\]>)/', $str ) );
  }
}

class ipPhpSyntax {
  private static $source  = null;

  private static $maxsize = 500000; // 0 means unlimited size
  private static $tmpdir  = null;
  private static $php_cmd = false; // path to command line version of PHP on this system

  private static $dos2unix  = "/usr/bin/dos2unix"; // CRLF => LF
  private static $mac2unix  = "/usr/bin/mac2unix"; // CR => LF

  private static $ok_line_start         = 'No syntax errors detected in';
  private static $on_line_pattern       = '/on line (<b>)?([0-9]*)(<\\/b>)?/';
  private static $on_line_pattern_index = 2;

  private static $highlight_start_pat;
  private static $highlight_end_1_pat;
  private static $highlight_end_2_pat;
  private static $highlight_end_pat;

  private static $version;
  private static $message = "Source code may contain dangerous php functions";
  private static $title   = "Error";

  private static $error   = true;
  private static $errros  = array();
  private static $errline = array();

  private static function reset( $options = array() ) {
    if ( isset( $options["tmpdir"] ) ) {
      self::$tmpdir   = $options["tmpdir"];
    }
    if ( isset( $options["tmpdir"] ) ) {
      self::$php_cmd  = $options["php_cmd"];
    }
    else {
      self::$php_cmd  = "php.exe";
    }
    if ( isset( $options["dos2unix"] ) ) {
      self::$dos2unix = $options["dos2unix"];
    }
    if ( isset( $options["mac2unix"] ) ) {
      self::$mac2unix = $options["mac2unix"];
    }

    if ( !self::$tmpdir ) {
      self::$tmpdir   = sys_get_temp_dir();
    }
    self::$version  = self::version();
  }

  private static function version( $level = 0 ) {
    $full   = phpversion();
    $parts  = explode( ".", $full );
    if ( count( $parts ) == 0 ) {
      return "unknown";
    }
    $version  = $parts[0];
    for( $i = 1; $i <= $level; $i++ ) {
      $version  .=  ".".$parts[$i];
    }
    return $version;
  }

  private static function source( $source = null ) {
    self::$source = $source;
  }

  private static function exec() {
    $tmp_name = tempnam( self::$tmpdir, "syncheck" );

    file_put_contents( $tmp_name, self::$source );
    $tmp_name = preg_replace( "/\s/", " ", $tmp_name );

    $cmd  = "( \"".self::$php_cmd."\" -l -d display_errors=on -d log_errors=off -f \"".$tmp_name."\" 2>&1 )";
    exec( $cmd, $output, $result );

    @unlink( $tmp_name );
    $len  = count( $output );
    if ( !$len ) {
      self::$message  = "Sorry! internal error";
      return false;
    }

    if ( substr( $output[0], 0, strlen( self::$ok_line_start ) ) == self::$ok_line_start ) {
      self::$error    = false;
      self::$message  = false;
    }
    else {
      self::$error  = true;
      if ( $len > 0 && rtrim( $output[0] ) == "<br />" ) {
        array_shift( $output );
        $len--;
      }
      if ( $len > 0 && rtrim( $output[$len-1] ) == "Errors parsing ".$tmp_name )  {
        $len--;   // N.B. skip last line
      }
      for( $i=0; $i < $len; $i++ ) {
        $line = $output[$i];
        if ( stristr( $line, "is not recognized as an internal or external command" ) ) {
          $line = "'php.exe' is not recognized as an internal or external command";
        }
        elseif ( stristr( $line, "Could not open input file" ) ) {
          $line = "temporarily missing source code";
        }

        if ( preg_match( '/^(.*):(.*)in (.*) on line (.*)$/', $line, $errors ) ) {
          self::$title  = ucwords( trim( $errors[1] ) );
          $line = trim( $errors[2] )." in line ".trim( $errors[4] );
        }

        if ( preg_match( self::$on_line_pattern, $line, $matches ) ) {
          $line = "(line:".$matches[self::$on_line_pattern_index].") ".$line;
        }

        self::$errros[] = str_replace( $tmp_name, $tmp_name, $line );
      }

      self::$message  = implode( " ", self::$errros );
    }
  }

  public static function check( $source = null, $options = array() ) {
    self::reset( $options );
    self::source( $source );

    if ( !self::restricted() ) {
      self::exec();
    }
    else {
      self::$message  = "Cannot test Syntax in safe mode";
    }

    return array( "error" => self::$error, "message" => self::$message, "title" => self::$title );
  }

  private static function restricted() {
    if ( ini_get( "safe_mode" ) && strtolower( ini_get( "safe_mode" ) ) != 'off' ) {
      return true;
    }
    if ( ini_get( 'open_basedir' ) != '' ) {
      return true;
    }
    if ( !function_exists( "exec" ) ) {
      return true;
    }
    if ( $tmp = tempnam( self::$tmpdir, "syncheck" ) ) {
      @unlink( $tmp );
      return false;
    }
    return true;
  }
}

class ipJSBeautify {
  public static function beautify( $js = null, $options = array() ) {
    $js = ipObfuscatorUnpacker::run( $js );
    //return $js;
    return js_beautify( $js, $options );
  }
}

class ipPhpTidy {
  private static $tokens  = array();
  private static $source  = null;
  private static $output  = null;

  private static function fix_token( $token ) {
    return ( is_array( $token ) ) ? $token : array( 0, $token );
  }

  public static function tidy( $source = null ) {
    self::$source = self::$output = $source;

    return self::$output;
  }
}

class ipRegex {
  private static $domain  = '/^(http|https|ftp)://([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/?/i';

  public static function validate_domain( $domain = null ) {
    return ( empty( $domain ) ) ? false : preg_match( self::$domain, $domain );
  }

  public static function clean_duplicate_words( $text = null ) {
    return preg_replace( "/s(w+s)1/i", "$1", $text );
  }

  public static function clean_duplicate_punct( $text = null ) {
    return preg_replace( "/.+/i", ".", $text );
  }

  public static function get_element_by_tag( $tag = null, $xml = null ) {
    $tag  = preg_quote( $tag );
    preg_match_all( '{<'.$tag.'[^>]*>(.*?)</'.$tag.'>}', $xml, $matches, PREG_PATTERN_ORDER );
    return ( isset( $matches[1] ) && !empty( $matches[1] ) ) ? $matches[1] : array();
  }

  public static function get_element_by_attr( $xml = null, $tag = null, $attr = null, $value = null ) {
    $tag  = ( is_null( $tag ) ) ? '\w+' : preg_quote( $tag );
    $attr = preg_quote( $attr );
    $value  = preg_quote( $value );

    $tag_regex  = "/<(".$tag.")[^>]*$attr\s*=\s*(['\"])$value\\2[^>]*>(.*?)<\/\\1>/";
    preg_match_all( $tag_regex, $xml, $matches, PREG_PATTERN_ORDER );
    return ( isset( $matches[3] ) && !empty( $matches[3] ) ) ? $matches[3] : array();
  }

  public static function is_hex_color( $color = null ) {
    return ( empty( $color ) ) ? false : preg_match('/^#(?:(?:[a-fd]{3}){1,2})$/i', $color );
  }

  public static function smart_quotes( $text = null ) {
    /*$typo = new ipTypography( true );
    $typo->set_punctuation_spacing( false );
    $typo->set_style_numbers( false );
    $typo->set_style_ampersands( false );
    $typo->set_style_caps( false );
    $typo->set_style_initial_quotes( false );
    $text = $typo->process( htmlspecialchars( $text ) );*/
    return htmlspecialchars( $text );
    //return preg_replace( '/"([^"x84x93x94rn]+)"/', '”$1”', $text );
  }

  public static function is_valid_password( $text = null ) {
    // This regular expression will tests if the input consists of 6 or more letters, digits, underscores and hyphens.
    // The input must contain at least one upper case letter, one lower case letter and one digit.
    return preg_match( 'A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])[-_a-zA-Z0-9]{6,}z', $text );
  }

  public static function get_client_language( $availableLanguages = array(), $default = 'en' ) {
    if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
      $langs  = explode( ',',$_SERVER['HTTP_ACCEPT_LANGUAGE'] );
      foreach( $langs as $value ) {
        $choice = trim( substr( $value, 0, 2 ) );
        if ( in_array( $choice, $availableLanguages ) ) {
          return $choice;
        }
      }
    } 
    return $default;
  }

  public static function ordinal( $cdnl = 0 ) {
    $test_c = abs( $cdnl ) % 10;
    $ext    = ( ( abs( $cdnl ) % 100 < 21 && abs( $cdnl ) %100 > 4 ) ? "th" : ( ( $test_c < 4 ) ? ( $test_c < 3 ) ? ( $test_c < 2 ) ? ( $test_c < 1 ) ? 'th' : 'st' : 'nd' : 'rd' : 'th' ) );
    return $cdnl.$ext;
  }

  public static function convert_currency( $from_currency = "", $to_currency = "", $amount = 0 ) {
    $amount = urlencode( $amount );
    $from_currency  = urlencode( $from_currency );
    $to_currency    = urlencode( $to_currency );
    $url  = "http://www.google.com/ig/calculator?hl=en&q=".$amount.$from_currency."=?".$to_currency;
    $ch   = curl_init();
    $timeout  = 0;
    $curl = new ipCurl( $url );
    $curl->setTimout( 0 );
    $curl->createCurl();
    $rawdata  = trim( $curl->getResponse() );

    if ( $curl->getError() === 0 && $rawdata ) {
      $data = explode( '"', $rawdata );
      if ( isset( $data[3] ) ) {
        $data = explode( " ", $data[3] );
        if ( isset( $data[0] ) ) {
          return round( trim( $data[0] ), 2 );
        }
      }
    }
    return false;
  }

  public static function time_difference( $time = 0 ) {
    if( is_numeric( $time ) ) {
      $value  = array( "years" => 0, "days" => 0, "hours" => 0, "minutes" => 0, "seconds" => 0 );
      if ( $time >= 31556926 ) {
        $value["years"] = floor( $time / 31556926 );
        $time = ( $time % 31556926 );
      }
      if ( $time >= 86400 ) {
        $value["days"]  = floor( $time / 86400 );
        $time = ( $time % 86400 );
      }
      if ( $time >= 3600 ) {
        $value["hours"] = floor( $time / 3600 );
        $time = ( $time % 3600 );
      }
      if ( $time >= 60 ) {
        $value["minutes"] = floor( $time / 60 );
        $time = ( $time % 60 );
      }
      $value["seconds"] = floor( $time );
      return (array)$value;
    }
    else {
      return $time;
    }
  }
}

class Base32 {
  private static $encode  = array();
  private static $decode  = array();
  private static function chunk( $bin = 0, $bit = 0 ) {
		$bin = chunk_split( $bin, $bit, ' ' );
		if ( substr( $bin, ( strlen( $bin ) ) - 1)  == ' ' ) {
      $bin = substr( $bin, 0, strlen( $bin ) - 1 );
		}
		return explode( ' ', $bin );
	}
  public static function encode( $str = null ) {
    $key  = array_merge( range( "A" , "Z" ), range( 2, 7 ), array( "=" ) );
    self::$encode = $key;

    if ( strlen( $str ) === 0 ) {
      return "";
    }
    $bs = "";
		foreach( str_split( $str ) as $s ) {
		  $s  = decbin( ord( $s ) );
      $bs .=  str_pad( $s, 8, 0, STR_PAD_LEFT );
		}
    $ba = self::chunk( $bs, 5 );
    while( count( $ba ) % 8 !== 0 ) {
			$ba[] = null;
		}
		$b32 =  "";
		foreach( $ba as $bin ) {
      $char = 32;
      if ( !is_null( $bin ) ) {
        $bin  = str_pad( $bin, 5, 0, STR_PAD_RIGHT );
				$char = bindec( $bin );
			}
      $b32  .=  self::$encode[$char];
		}
		return strtr( $b32, "QB=", "1|0" );
	}
  public static function decode( $b32 = null ) {
    $key  = array_merge( range( "A" , "Z" ), range( 2, 7 ), array( "=" ) );
    $val  = range( 0, 32 );
    self::$decode = array_combine( $key, $val );
		if ( strlen( $b32 ) === 0 ) {
		  return "";
		}
		$b32  = preg_replace("/[^A-Z2-7]/", "", strtr( strtoupper( $b32 ), "1|0", "QB=" ) );
		$b32a = str_split( $b32 );
		$bina = array();
		$stri = "";
		foreach( $b32a as $str ) {
      $char = self::$decode[$str];
      if ( $char !== 32 ) {
        $char = decbin( $char );
				$stri = $stri.str_pad( $char, 5, 0, STR_PAD_LEFT );
			}
		}
		while( strlen( $stri ) %8 !== 0 ) {
		  $stri = substr( $stri, 0, strlen( $stri ) - 1 );
		}
		$bina = self::chunk( $stri, 8 );
		$rstr = "";
		foreach( $bina as $bin ) {
		  $bin  = str_pad( $bin, 8, 0, STR_PAD_RIGHT );
      $rstr = $rstr.chr( bindec( $bin ) );
		}
		return $rstr;
	}
}
?>
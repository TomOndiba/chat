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

class ipCSSParser {
  private $source = null;
  private $base   = null;
  private $links  = array();
  private $cache  = array();

  private $urlreg = "/^((http|ftp|https):)?\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?$/i";
  //private $cssreg = '#url\(\s*(\'|")?((ht|f)tps?:)?([a-z0-9\._/~%\-\+&\#\?!=\(\)@]+)(\.(jpe?g|png|bmp|gif|svg))(\'|")?\s*\)#i';
  private $cssreg = '#(url|data\-uri)\(\s*(\'|")?((ht|f)tps?:)?([a-z0-9\._/~%\-\+&\#\?!=\(\)@]+)(\.([a-z]{3,}))([\#\?](.+?))?(\'|")?\s*\)#i';

  public static function parse( $source = null, $base = null, $url_only = false ) {
    $parse  = new ipCSSParser( $source, $base );
    return $parse->run( $url_only );
  }

  public static function minify( $source = null ) {
    $source = CSSConstants::run( $source );
    $source = CSSVendorPrefix::run( $source );
    $source = CSSCondenser::run( $source );
    return $source;
  }

  public function __construct( $source = null, $base = null ) {
    $this->setSource( $source );
    $this->setBase( $base );
  }

  public function setSource( $source = null ) {
    $this->source = trim( $source );
  }

  public function setBase( $base = null ) {
    $this->base = $base;
  }

  public function run( $url_only = false ) {
    /** Change relative url's to absoolute **/
    $this->source = preg_replace_callback( $this->cssreg, array( $this, 'parseLinks' ), $this->source );
    if ( !$url_only ) {
      //$source = CSSConstants::run( $source );
      /** Vendor Prefixed CSS Properties **/
      $this->source = CSSVendorPrefix::run( $this->source );
      /** Vendor Based Gradient properties **/
      $this->source = CSSVendorGradient::run( $this->source );
      /** Remove Whitespace and Comments **/
      //$source = CSSCondenser::run( $source );
    }
    return $this->source;
  }

  private function parseLinks( &$m ) {
    $line = $m[0];
    $img  = $m[4].$m[5];
    if ( preg_match( $this->urlreg, $img ) ) {
      $this->add_links( $img );
      return $line;
    }
    $url  = sourceGrabber::abs_path( $img, $this->base );
    $line = str_replace( $img, $url, $line );
    $this->add_links( $url );
    return $line;
  }

  private function add_links( $link = null ) {
    if ( !in_array( $link, $this->links ) ) {
      $this->links[]  = trim( $link );
    }
  }
}

class sourceGrabber {
  private static $links = array();

  public static function abs_path( $link = null, $base = null ) {
    $base = rtrim( $base, "/" )."/";

    if ( isset( self::$links[$link] ) ) {
      return self::$links[$link];
    }
    if ( substr( $link, 0, 1 ) === "/" ) {
      return $link;
    }
    $link = ( substr( $link, 0, 2 ) === "./" ) ? substr( $link, 2, strlen( $link ) ) : $link;
    $path = substr_count( $link, "../" );
    $link = str_ireplace( "../", "", $link );
    for( $i = 1; $i <= $path; $i++ ) {
      $base = dirname( $base )."/";
    }
    self::$links[$link] = $base.$link;
    return self::$links[$link];
  }
}

class CSSConstants {
  public static function run( $css = null ) {
    $constants  = array();
    if ( preg_match_all( '#@constants\s*\{\s*([^\}]+)\s*\}\s*#i', $css, $matches ) ) {
      foreach( $matches[0] as $i => $constant ) {
        $css  = str_replace( $constant, '', $css );
        if ( preg_match_all( '#([_a-z0-9]+)\s*:\s*([^;]+);#i', $matches[1][$i], $vars ) ) {
				  foreach( $vars[1] as $var => $name ) {
            $constants["const($name)"]  = $vars[2][$var];
          }
        }
      }
    }
    if ( !empty( $constants ) ) {
      $css  = str_replace( array_keys( $constants ), array_values( $constants ), $css );
    }
    return $css;
	}
}
class CSSVendorPrefix {
  public static function generate() {
    if ( isset( $_SESSION["cssVendorPrefixes"] ) ) {
      return $_SESSION["cssVendorPrefixes"];
    }

    $file = dirname( __FILE__ )."/cssPrefixes.json";
    $data = array();

    if ( file_exists( $file ) ) {
      $mtime  = filemtime( $file );
      $ntime  = time();
      $data   = $_SESSION["cssVendorPrefixes"]  = (array)json_decode( file_get_contents( $file ), true );

      if ( ( $ntime - $mtime ) <= ( 3600 * 24 ) ) {
        return $data;
      }
    }

    require_once( dirname( __FILE__ )."/htmlparser.class.php" );

    $prefixes = array();
    $indexes  = array( "-moz-", "-webkit-", "-o-", "-ms-" );

    $html = @file_get_contents( "http://peter.sh/experiments/vendor-prefixed-css-property-overview/" );
    $html = str_get_html( $html );

    if ( !$html || !( $rows = $html->find( ".overview-table tbody tr" ) ) ) {
      return $data;
    }

    foreach( $rows as $row ) {
      $vendors  = array();
      $default  = null;
      $index    = 0;
  
      foreach( $row->find( "td" ) as $col ) {
        if ( $index === 4 ) {
          break;
        }
        $vendor = explode( " ", strtolower( trim( strip_tags( $col->innertext ) ) ) );
        $vendor = ( !empty( $vendor ) ) ? trim( $vendor[0] ) : null;
        $hasPre = $col->find( "i", 0 );
        if ( !$default && $hasPre ) {
          $default  = $vendor;
        }
        if ( $vendor && $vendor !== $default ) {
          $vendors[$indexes[$index]]  = $vendor;
        }
        $index++;
      }
      $vendors  = array_filter( array_unique( $vendors ) );
      if ( $default && !empty( $vendors ) ) {
        $prefixes[$default] = $vendors;
      }
    }

    $prefixes = self::selfPrefixes( $prefixes );
    $_SESSION["cssVendorPrefixes"]  = $prefixes;
    file_put_contents( $file, json_encode( $prefixes ) );

    return $prefixes;
  }

  private static function selfPrefixes( $data = array() ) {
    $props    = array( "appearance", "box-flex" );
    $default  = array();
    $prefixes = array( "-moz-", "-webkit-", "-o-", "-ms-" );
    foreach( $props as $prop ) {
      foreach( $prefixes as $prefix ) {
        $default[$prop][$prefix]  = $prefix.$prop;
      }
    }
    $data = array_merge( $data, $default );
    ksort( $data );
    //header("Content-Type: text/plain");
    //print_r( $data );
    //exit();
    return $data;
  }

  public static function run( $css = null ) {
    $bases  = array();
    $vendors  = array( "webkit" => "-webkit-", "chrome" => "-webkit-", "safari" => "-webkit-", "opera" => "-o-", "firefox" => "-moz-", "msie" => "-ms-" );
    $prefixed = self::generate();

    $browser  = getBrowser();
    if ( !$browser || !isset( $vendors[$browser] ) ) {
      return $css;
    }

    $prefix   = $vendors[$browser];
    $replaces = array();

    //header( "Content-Type: text/css" );
    foreach( $prefixed as $attribute => $prefixes ) {
      if ( !isset( $prefixes[$prefix] ) ) {
        continue;
      }
      $hasRules = preg_match_all( '#([{;]+)(\s*)(\b'.$attribute.'\b)\s*\:\s*((?!before|after|(first|last)\-(child|line)|hover|focus|active|visited)+)([^;}]+)(\;*)#i', $css, $cssRules, PREG_OFFSET_CAPTURE|PREG_SET_ORDER );
      if ( $hasRules > 0 ) {
        foreach( $cssRules as $cssRule ) {
          $line = $cssRule[0][0];
          $prop = $attribute;
          $val  = trim( $cssRule[7][0] );
          $sep  = $cssRule[1][0];
          $wsp  = $cssRule[2][0];

          $rule = $sep.$wsp.$prop.": ".$val.";".$wsp.$prefixes[$prefix].": ".$val.";";
          //print_r( $cssRule );
          //echo PHP_EOL;
          //continue 1;
          $replaces[$line]  = $rule;
        }
      }
    }
    //exit();
    if ( !empty( $replaces ) ) {
      $css  = str_replace( array_keys( $replaces ), array_values( $replaces ), $css );
    }
    return $css;
	}
}
class CSSVendorGradient {
  public static function run( $source = null ) {
    $items  = array();
    $regExpLib  = self::generateRegExp();
    $rGradientEnclosedInBrackets  = "/background(\-image)?\s*:\s*(linear|radial)\-gradient\s*\(((?:\([^\)]*\)|[^\)\(]*)*)\)\s*(;?)/"; // Captures inside brackets - max one additional inner set.
    if ( preg_match_all( $rGradientEnclosedInBrackets, $source, $matches ) ) {
      foreach( $matches[0] as $key => $match ) {
        $gradient = self::parseGradient( $regExpLib, $matches[3][$key] );
        if ( $gradient ) {
          $items[]  = $gradient;
          $gradient = self::generate( $gradient, $matches[1][$key], $matches[2][$key] );
          if ( $gradient ) {
            $source = str_replace( $matches[0][$key], $gradient.$matches[4][$key], $source );
          }
        }
        else {
          $source = str_replace( $matches[0][$key], self::generate( $matches[1][$key], $matches[2][$key], $matches[3][$key] ).$matches[4][$key], $source );
        }
      }
    }
    return $source;
  }

  private static function generate( $gradient = null, $written_as = "", $direction = "linear" ) {
    $iargs  = func_num_args();
    $args   = func_get_args();
    $source = null;
    if ( is_string( $gradient ) ) {
      if ( $iargs === 3 ) {
        $source = self::generateRules( "background".$args[0], $args[1]."-gradient(".$args[2].")" );
      }
    }
    else {
      $directions = array(
        "left"  =>  "right",
        "right" =>  "left",
        "bottom"  =>  "top",
        "top"   =>  "bottom"
      );

      if ( empty( $gradient->colorStopList ) ) {
        return false;
      }

      $key  = "background".$written_as;
      $val  = array();
      $dir  = array();
      $col  = array();

      array_push( $val, $direction."-gradient(" );

      /** Generate Directon **/
      if ( isset( $gradient->angle ) ) {
        array_push( $dir, $gradient->angle );
      }
      elseif ( isset( $gradient->side ) ) {
        $sides  = explode( " ", $gradient->side );
        foreach( $sides as $side ) {
          if ( isBrowser( "webkit" ) ) {
            array_push( $dir, $directions[$side] );
          }
          else {
            array_push( $dir, $side );
          }
        }
      }
      $dir  = trim( implode( " ", $dir ) );
      if ( !empty( $dir ) ) {
        array_push( $col, $dir );
      }

      /** Generate Colours **/
      foreach( $gradient->colorStopList as $color ) {
        if ( !isset( $color->position ) ) {
          array_push( $col, $color->color );
          continue;
        }
        array_push( $col, $color->color." ".$color->position );
      }
      $col  = implode( ", ", $col );

      /** Finalize Gradient **/
      array_push( $val, $col, ")" );
      $val  = implode( "", $val );

      $source = self::generateRules( $key, $val, false );
      $source = "background".$args[1].": ".$args[2]."-gradient(".$gradient->original.");".PHP_EOL.$source;
    }
    return $source;
  }

  private static function generateRules( $key = null, $value = null, $default = true ) {
    $lines    = ( $default ) ? array( $key.": ".$value ) : array();
    $value    = str_ireplace( "to ", "", $value );
    $prefixes = array( "webkit" => "-webkit-", "opera" => "-o-", "firefox" => "-moz-", "msie" => "-ms-" );
    foreach( $prefixes as $vendor => $prefix ) {
      if ( isBrowser( $vendor ) ) {
        $lines[]  = $key.": ".$prefix.$value;
        break;
      }
    }
    return implode( ";".PHP_EOL, $lines );
  }

  private static function regExp( $regexp = null ) {
    $regexp = (object)array(
      "source"  =>  $regexp,
      "regexp"  =>  "/".$regexp."/",
    );
    return $regexp;
  }

  private static function generateRegExp() {
    // Note any variables with "Capture" in name include capturing bracket set(s).
    $searchFlags      = "i"; // ignore case for angles, "rgb" etc
    $rAngle           = self::regExp( "(?:[+-]?\d*\.?\d+)(?:deg|grad|rad|turn)" ); // Angle +ive, -ive and angle types
    $rSideCornerCapture = self::regExp( "to\s+((?:(?:left|right|bottom|top|\s)+))" ); // optional 2nd part
    $rComma           = self::regExp( "\s*,\s*" ); // Allow space around comma.
    $rColorHex        = self::regExp( "\#(?:[A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})" ); // 3 or 6 character form
    $rDigits3         = self::regExp( "\(\s*(?:[0-9]{1,3}\s*,\s*){2}[0-9]{1,3}\s*\)" ); // "(1, 2, 3)"
    $rDigits4         = self::regExp( "\(\s*(?:[0-9]{1,3}\s*,\s*){3}[0-9]*\.*[0-9]{1,3}\s*\)" ); // "(1, 2, 3, 4)"
    $rValue           = self::regExp( "(?:[+-]?\d*\.?\d+)(?:%|[a-z]+)?" ); // ".9", "-5px", "100%".
    $rKeyword         = self::regExp( "[_A-Za-z-][_A-Za-z0-9-]*" ); // "red", "transparent", "border-collapse".
    $rColor           = self::combineRegExp(
                          array(
                            '(?:', $rColorHex, '|', '(?:rgb|hsl)', $rDigits3, '|', '(?:rgba|hsla)', $rDigits4, /*'|', $rKeyword,*/ ')'
                          )
                        );
    $rColorStop       = self::combineRegExp( array( $rColor, '(?:\\s+', $rValue, ')?' ) ); // Single Color Stop, optional value.
    $rColorStopList   = self::combineRegExp( array( '(?:', $rColorStop, $rComma, ')*', $rColorStop ) );// List of color stops min 1.
    $rLineCapture     = self::combineRegExp( array( '(?:(', $rAngle, ')|', $rSideCornerCapture, ')' ) );// Angle or SideCorner
    $rGradientSearch  = self::combineRegExp(
                          array(
                            '(', '(', $rLineCapture, ')', $rComma, ')?', '(', $rColorStopList, ')'
                          ),
                          $searchFlags
                        ); // Capture 1:"line", 2:"angle" (optional), 3:"side corner" (optional) and 4:"stop list".
    $rColorStopSearch = self::combineRegExp(
                          array(
                            '\\s*(', $rColor, ')', '(?:\\s+', '(', $rValue, '))?', '(?:', $rComma, '\\s*)?'
                          ),
                          $searchFlags
                        ); // Capture 1:"color" and 2:"position" (optional).

    return (object)array(
      "gradientSearch"  =>  $rGradientSearch,
      "colorStopSearch" =>  $rColorStopSearch
    );
  }

  private static function combineRegExp( $regexpList = array(), $flags = null ) {
    $source = null;
    foreach( $regexpList as $item ) {
      if ( is_object( $item ) ) {
        $regexp = $item->source;
      }
      elseif ( is_array( $item ) ) {
        $regexp = $item["source"];
      }
      else {
        $regexp = $item;
      }
      $source .=  $regexp;
    }
    $source = ( $flags ) ? "/".$source."/".$flags : $source;
    return $source;
  }

  private static function parseGradient( $regexpList = array(), $input = null ) {
    $result = null;
    $hasMatch = preg_match( $regexpList->gradientSearch, $input, $matchGradient );

    if ( $hasMatch ) {
      $result = (object)array(
        "original"  =>  $matchGradient[0],
        "colorStopList" => array()
      );

      // Line (Angle or Side-Corner).
      if ( !!$matchGradient[2] ) {
        $result->line = $matchGradient[2];
      }
      // Angle or undefined if side-corner.
      if ( !!$matchGradient[3] ) {
        $result->angle  = $matchGradient[3];
      }
      // Side-corner or undefined if angle.
      if ( !!$matchGradient[4] ) {
        $result->side = $matchGradient[4];
      }

      // Loop though all the color-stops.
      if ( preg_match_all( $regexpList->colorStopSearch, $matchGradient[5], $matchColorStop ) ) {
        foreach( $matchColorStop[0] as $key => $value ) {
          $stopResult = array(
            "color" =>  $matchColorStop[1][$key]
          );
          if ( !!$matchColorStop[2][$key] ) {
            $stopResult["position"] = $matchColorStop[2][$key];
          }
          array_push( $result->colorStopList, (object)$stopResult );
        }
      }
    }
    return $result;
  }
}
class CSSCondenser {
  public static function run( $css = null ) {
    $css  = trim( preg_replace('#/\*[^*]*\*+([^/*][^*]*\*+)*/#', '', $css ) ); // comments
    $css  = preg_replace( '#\s+(\{|\})#', "$1", $css ); // before
    $css  = preg_replace( '#(\{|\}|:|,|;)\s+#', "$1", $css ); // after
    return $css;
  }
}

class ipCSSCache {
  private $cache_dir  = null;
  public function __construct( $cache_dir = null ) {
    if ( !file_exists( $cache_dir ) ) {
      mkdir( $cache_dir, 0755, true );
    }
    $this->cache_dir  = realpath( $cache_dir ).DIRECTORY_SEPARATOR;
  }
  public function get( $file = null, $browser = "none" ) {
    //return false;
    if ( !file_exists( $file ) ) {
      return false;
    }
    $cache  = realpath( $this->cache_dir.md5( $file.$browser ) );
    if ( !$cache ) {
      return false;
    }
    $ctime  = filemtime( $cache );
    $ftime  = filemtime( $file );
    if ( $ftime > $ctime ) {
      @unlink( $cache );
      return false;
    }
    return file_get_contents( $cache );
  }
  public function add( $file = null, $data = null, $browser = "none" ) {
    //return;
    if ( !file_exists( $file ) || !$data ) {
      return false;
    }
    $cache  = $this->cache_dir.md5( $file.$browser );
    file_put_contents( $cache, $data );
  }
}
function isBrowser( $codename = null, $version = null ) {
  if ( !$codename ) {
    return false;
  }
  return ( isset( $_GET[$codename] ) && (int)trim( $_GET[$codename] ) === 1 );
}
function getBrowser() {
  $browsers = array( "webkit", "chrome", "safari", "opera", "firefox", "msie" );
  foreach( $browsers as $browser ) {
    if ( isset( $_GET[$browser] ) ) {
      return $browser;
    }
  }
  return false;
}

function doLessParse( $text = null, $file = null ) {
  try {
    $options  = array( "compress"  =>  false );
    $parser = new Less_Parser( $options );
    $parser->parse( $text );
    $text = $parser->getCss();
  }
  catch( Exception $e ) {
    $text = ipCSSParser::minify( $text );
    $text = "/*".PHP_EOL.basename( $file ).": ".$e->getMessage().PHP_EOL."*/".PHP_EOL.$text;
  }
  return $text;
}

/** https://github.com/agar/css-cache **/
?>
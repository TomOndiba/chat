<?php

/**
 * @author bystwn22
 * @copyright 2013
 */

error_reporting( 0 );
set_time_limit( 0 );

$loadPlugins  = false;
require_once( dirname( __FILE__ )."/includes/plugins.class.php" );
require_once( dirname( __FILE__ )."/includes/gzip.class.php" );
require_once( dirname( __FILE__ )."/includes/packers/css.parser.class.php" );
require_once( dirname( __FILE__ )."/includes/packers/less.php/Less.php" );

global $ipPlugins;
$cssCache = new ipCSSCache( dirname( __FILE__ )."/css/cache/" );

$doless = ( !isset( $_GET["noless"] ) );
$based  = dirname( __FILE__ );
$files  = ( isset( $_GET["l"] ) ) ? array_map( "trim", explode( ",", trim( (string)$_GET["l"], ", " ) ) ) : array();
$direc  = ( isset( $_GET["d"] ) ) ? trim( (string)$_GET["d"] ) : array();
$format = parse_module_format( $files );
$files  = parse_module_list( $files );
$direcr = ( $direc != "lpsm" ) ? $format.( ( $direc ) ? DIRECTORY_SEPARATOR.$direc : "" ) : "plugins";
if ( $direc == "lpsm" ) {
  $direc  = realpath( $based.DIRECTORY_SEPARATOR."plugins" ).DIRECTORY_SEPARATOR;
  $is_plugin  = true;
}
else {
  $direc  = realpath( $based.DIRECTORY_SEPARATOR.$format.( ( $direc ) ? DIRECTORY_SEPARATOR.$direc : "" ) ).DIRECTORY_SEPARATOR;
  $is_plugin  = false;
}
$data   = null;
$modif  = 0;

foreach( $files as $file ) {
  $base     = dirname( dirname( $_SERVER["PHP_SELF"] )."/".$direcr."/".$file.".".$format );
  $extnsion = pathinfo( $file, PATHINFO_EXTENSION );
  $has_extn = ( $extnsion && !in_array( $extnsion, array( "min", "io" ) ) ) ? true : false;

  if ( $is_plugin ) {
    $ipPlugins->setPluginFormat( $format );
    $file = $ipPlugins->findPluginFile( $file );
  }
  else {
    $file = realpath( sprintf( "%s%s%s", $direc, $file, ( !$has_extn ? ".".$format : null ) ) );
  }

  if ( !$file || !file_exists( $file ) || !is_readable( $file ) ) {
    $data = false;
    break;
  }
  $mtime  = filemtime( $file );
  $modif  = max( $modif, $mtime );
  $text   = trim( implode( "", file( $file ) ) );
  if ( !$text ) {
    $data = false;
    break;
  }
  if ( $format === "css" ) {
    if ( $cachedCSS = $cssCache->get( $file, getBrowser() ) ) {
      $text = $cachedCSS;
    }
    else {
      $text = ipCSSParser::parse( $text, $base );

      if ( $doless ) {
        $has_import = preg_match_all( '!@import\s+url\(([\'"])(.+?)\\1\)\;?!', $text, $imports );

        if ( $has_import > 0 ) {
          header( "Content-Type: text/plain" );
          foreach( $imports[2] as $iKey => $iLink ) {
            if ( stristr( $iLink, "http" ) !== false || strstr( $iLink, "ipChat" ) === false ) {
              continue 1;
            }
            $iLink  = substr( $iLink, strpos( $iLink, "ipChat" ) + 7 );
            $iFile  = realpath( dirname( __FILE__ )."/".$iLink );
            $iData  = null;
            if ( $iFile ) {
              $iData  = trim( file_get_contents( $iFile ) );
              if ( $iData ) {
                $iBase  = dirname( dirname( $_SERVER["PHP_SELF"] )."/".$iLink );
                $iData  = ipCSSParser::parse( $iData, $iBase );
              }
            }
            $text = str_replace( $imports[0][$iKey], $iData, $text );
          }
        }
        $text = doLessParse( $text, $file );
      }
      $cssCache->add( $file, $text, getBrowser() );
    }
  }
  $data = $data.$text.PHP_EOL;
}

$data = trim( $data );

if ( !$data ) {
  $header = new ipHeader;
  $header::_204();
  $header::content_mime( mime::get( $format ) );
  $header::content_length( 0 );
  $header::powered_by( "Impact Plus" );
  $header::no_cache();
  $header::connection( "close" );
  exit();
}
$gzip = new ipGzip;
$gzip::etag( md5( $data ), $modif );
if ( isset( $_GET["nv"] ) ) {
  $gzip::no_validate();
  $gzip::set_expire_date( 50000000 );
}
else {
  $gzip::set_expire_date( 0 );
}
$gzip::set_last_modified( $modif );
$gzip::set_content_type( mime::get( $format ) );
$gzip::set_content( $data );
$gzip::do_output();

function parse_module_format( $arr = array() ) {
  if ( !is_array( $arr ) ) {
    return;
  }
  return ( isset( $arr[0] ) ) ? $arr[0] : false;
}
function parse_module_list( $arr = array() ) {
  if ( !is_array( $arr ) ) {
    return;
  }
  if( isset( $arr[0] ) ) {
    unset( $arr[0] );
  }
  return $arr;
}
?>
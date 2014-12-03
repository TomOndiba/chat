<?php

/**
 * @author bystwn22
 * @copyright 2013
 */

require_once( dirname( dirname( __FILE__ ) )."/includes/gzip.class.php" );
require_once( dirname( dirname( __FILE__ ) )."/includes/packers/css.parser.class.php" );

$based  = dirname( __FILE__ );
$files  = ( isset( $_GET["l"] ) ) ? array_map( "trim", explode( ",", trim( (string)$_GET["l"], ", " ) ) ) : array();
$direc  = ( isset( $_GET["d"] ) ) ? trim( (string)$_GET["d"] ) : array();
$format = parse_module_format( $files );
$files  = parse_module_list( $files );
$direcr = $format.( ( $direc ) ? "/".$direc : "" );
$direc  = realpath( $based."/".$format.( ( $direc ) ? "/".$direc : "" ) ).DIRECTORY_SEPARATOR;
$data   = null;
$modif  = 0;

foreach( $files as $file ) {
  $extnsion = pathinfo( $file, PATHINFO_EXTENSION );
  $extnsion = explode( ".", $extnsion );
  end( $extnsion );
  $extnsion = current( $extnsion );

  $has_extn = ( $extnsion && in_array( $extnsion, array( "less", "css", "scss", "js", "php" ) ) ) ? true : false;

  $base = dirname( dirname( $_SERVER["PHP_SELF"] )."/".$direcr."/".$file.".".$format );
  $file = realpath( sprintf( "%s%s%s", $direc, $file, ( !$has_extn ? ".".$format : null ) ) );

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
    $text = ipCSSParser::parse( $text, $base, true );
  }
  $data = $data.$text.PHP_EOL.PHP_EOL;
}

$data = trim( $data );

if ( !$data ) {
  $header = new ipHeader;
  $header::_404();
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
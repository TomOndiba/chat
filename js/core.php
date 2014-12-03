<?php

/**
 * @author bystwn22
 * @copyright 2013
 */

error_reporting( 0 );
set_time_limit( 0 );

$loadPlugins  = false;
require_once( dirname( dirname( __FILE__ ) )."/includes/gzip.class.php" );

$files  = array( 0 => "core", 2 => "chat" );
$data   = null;
$modif  = 0;

if ( isset( $_GET["mobile"] ) ) {
  $files[1] = "mobile-core";
}

foreach( $files as $file ) {
  $file = realpath( dirname( __FILE__ )."/modules/".$file.".js" );
  if ( !$file || !file_exists( $file ) || !is_readable( $file ) ) {
    $data = null;
    break;
  }
  $mtime  = filemtime( $file );
  $modif  = max( $modif, $mtime );
  $text   = trim( implode( "", file( $file ) ) );
  if ( empty( $text ) ) {
    $data = null;
    break;
  }
  $text = $text.PHP_EOL;
  $data = $data.$text;
}

$data = trim( $data );

if ( !$data ) {
  $header = new ipHeader;
  $header::_404();
  $header::content_mime( "application/x-javascript" );
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
$gzip::set_expire_date( 0 );
$gzip::set_last_modified( $modif );
$gzip::set_content_type( "application/x-javascript" );
$gzip::set_content( $data );
$gzip::do_output();
?>
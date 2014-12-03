<?php

/**
 * @author bystwn22
 * @copyright 2013
 */

require_once( dirname( __FILE__ )."/includes/conn/open.php" );
loadClass( "ipHeader", "header/header.class.php" );

$a  = ( isset( $_GET["a"] ) ) ? $_GET["a"] : 0;
$b  = ( isset( $_GET["b"] ) ) ? $_GET["b"] : false;
$c  = ( isset( $_GET["c"] ) ) ? $_GET["c"] : false;
$d  = ( isset( $_GET["d"] ) ) ? $_GET["d"] : false;
$e  = ( isset( $_GET["e"] ) ) ? $_GET["e"] : false;
$f  = ( isset( $_GET["f"] ) ) ? (int)$_GET["f"] : 0;

if ( !$a || !$b || !$d || !$e ) {
  if ( $e ) {
    ipHeader::content_mime( mime::get( $e, true ) );
  }
  ipHeader::_404();
  ipHeader::powered_by( "Impact Plus" );
  ipHeader::no_cache();
  ipHeader::connection( "close" );
  exit();
}

$id = substr( $a, 0, 2 );
$im = substr( $a, 2, 2 );
$iy = substr( $a, -4 );

if ( !$f ) {
  sleep(1);
}

$base = ROOT_DIR."ipChat/uploads/";
$name = ( ( $f ) ? "thumb_" : "" ).( ( $c ) ? $c."_" : "" ).$d."_".$b."_".$a.".".$e;
$link = realpath( $base."/".$iy."/".$im."/".$id."/".$name );

if ( $link ) {
  loadClass( "ipGzip", "gzip.class.php" );
  $time = filemtime( $link );
  $data = implode( "", file( $link ) );
  $mime = mime::get( $link, false, false, true );

  $gzip = new ipGzip;
  $gzip::etag( md5( $data ), $time );
  $gzip::no_validate();
  $gzip::set_expire_date( 50000 );
  $gzip::set_last_modified( $time );
  $gzip::set_content_type( $mime );
  $gzip::set_content( $data );
  $gzip::do_output();
  exit();
}

ipHeader::_404();
ipHeader::content_mime( mime::get( $e, true ) );
ipHeader::powered_by( "Impact Plus" );
ipHeader::no_cache();
ipHeader::connection( "close" );
exit;
?>
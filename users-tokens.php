<?php

/**
 * @author bystwn22
 * @copyright 2014
 */

header( "Content-Type: application/json" );
set_time_limit( 0 );
error_reporting( 0 );
ignore_user_abort( false );

require_once( dirname( __FILE__ )."/includes/conn/open.php" );
loadClass( "ipHooks", "required/hooks/hooks.class.php" );
loadClass( "ipGzip", "gzip.class.php" );
loadClass( "ipPlugins", "plugins.class.php" );
loadClass( "ipLanguage", "lang.class.php" );
loadClass( "ipUsers", "users.class.php" );

$ipLang = new ipLanguage( ilanguage() );
$ipGzip = new ipGzip;
$ipGzip::set_expire_date( 0 );
$ipGzip::set_last_modified( time() );
$ipGzip::set_content_type( "application/json" );
$hasGzip  = false;

$userID = get_user_id( "cm" );
$search = trim( strtolower( (string)getPostArr( "search" ) ) );
$reqid  = trim( strtolower( getPostArr( "id" ) ) );

global $ipPlugins;
$exclud = getPostArr( "exclude", array() );
if ( $reqid ) {
  $users  = ipUsers::get_users( $userID, $reqid, "ID", null, null, false, false );
}
else {
  $users  = ipUsers::fetch_users( $userID, $search, $exclud, 20 );
}
exportResponse( $users );
?>
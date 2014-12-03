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

error_reporting(0);
if ( !session_id() ) {
  session_start();
}
set_time_limit( 0 );
ignore_user_abort( false );

$dirbaseuri = dirname( __FILE__ );
$corefiles  = array(
  $dirbaseuri."/conf.php",
  dirname( $dirbaseuri )."/error.handler.php",
  $dirbaseuri."/ezsql.php",
  $dirbaseuri."/mysqli.php",
  dirname( $dirbaseuri )."/required/password.class.php",
  dirname( $dirbaseuri )."/required/funcs.class.php",
  dirname( $dirbaseuri )."/required/runtime.cache.class.php",
  dirname( $dirbaseuri )."/required/ganon.class.php"
);

foreach( $corefiles as $corefile ) {
  $file = realpath( $corefile );
  if ( !$file || filesize( $file ) <= 0 ) {
    $corefile = basename( dirname( $corefile ) ).DIRECTORY_SEPARATOR.basename( $corefile );
    throw_error( sprintf( '404: Module "%s" not found or is empty', $corefile ) );
  }
  require_once( $file );
  if ( basename( $file ) === "error.handler.php" ) {
    //\php_error\reportErrors();
  }
}

$ipdb = new ezSQL_mysqli( MAIN_DB_USER, MAIN_DB_PASS, MAIN_DB_NAME, MAIN_DB_HOST );
$ipdb->query( "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'" );
$GLOBALS["ipdb"]  = $ipdb;

if ( USER_DB_INIT == false ) {
  define( "USER_IN_IP_DB", true );
  $GLOBALS["ipudb"] = $ipudb  = $ipdb;
}
else {
  define( "USER_IN_IP_DB", false );
  $GLOBALS["ipudb"] = $ipudb  = new ezSQL_mysqli( USER_DB_USER, USER_DB_PASS, USER_DB_NAME, USER_DB_HOST );
  $ipudb->query( "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'" );
}
function is_ajax_request() {
  if ( isset( $_SERVER["HTTP_X_REQUESTED_WITH"] ) && strtolower( $_SERVER["HTTP_X_REQUESTED_WITH"] ) === "xmlhttprequest" ) {
    return true;
  }
  return false;
}
function throw_error( $message = null ) {
  if ( is_ajax_request() ) {
    echo json_encode( array( "error" => 1, "message" => $message ) );
    exit();
  }
  ob_get_clean();
  echo '<style type="text/css">*{font-family:monospace!important;}</style>';
  throw new Exception( $message );
}

class TimeSpender {
  private static $start = false;
  private static $stop  = false;

  public static function start() {
    self::$start  = microtime( true );
  }
  public static function stop() {
    self::$stop   = microtime( true );
    return number_format( self::$stop - self::$start, 2 );
  }
  public static function took() {
    return number_format( self::$stop - self::$start, 2 );
  }
}



function tempdir( $dir ) {
  if ( !file_exists( $dir ) ) {
    mkdir( $dir, 0755, true );
  }
  return $dir;
}

function abspathToRoot( $append = null ) {
  $base = "//".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
  $pos  = strpos( $base, "ipChat" );
  if ( $pos !== false ) {
    $base = substr( $base, 0, $pos );
  }
  return $base.$append;
}

function getPostArr( $index, $default = false, $trim = true, $empty = true ) {
  if ( !isset( $_POST[$index] ) ) {
    return $default;
  }
  if ( $trim && !is_array( $_POST[$index] ) ) {
    $_POST[$index]  = trim( $_POST[$index] );
  }
  if ( $empty ) {
    if ( empty( $_POST[$index] ) ) {
      return $default;
    }
  }
  return $_POST[$index];
}
function getGetArr( $index, $default = false, $trim = true, $empty = true ) {
  if ( !isset( $_GET[$index] ) ) {
    return $default;
  }
  if ( $trim && !is_array( $_GET[$index] ) ) {
    $_GET[$index]  = trim( $_GET[$index] );
  }
  if ( $empty ) {
    if ( empty( $_GET[$index] ) ) {
      return $default;
    }
  }
  return $_GET[$index];
}
function toSingleFile( $files = array() ) {
  if ( !is_array( $files ) || !isset( $files["tmp_name"] ) || empty( $files["tmp_name"] ) ) {
    return false;
  }
  if ( !is_array( $files["tmp_name"] ) ) {
    return ( is_uploaded_file( $files["tmp_name"] ) ) ? $files : false;
  }
  if ( !is_uploaded_file( $files["tmp_name"][0] ) ) {
    return false;
  }
  $file = array(
    "name"  =>  $files["name"][0],
    "type"  =>  $files["type"][0],
    "size"  =>  $files["size"][0],
    "error"  =>  $files["error"][0],
    "tmp_name"  =>  $files["tmp_name"][0]
  );
  return $file;
}
function hasConvRead( $idx = null ) {
  $userID = get_user_id();
  if ( !$userID ) {
    return false;
  }
  global $ipdb;
  $idx  = $ipdb->escape( trim( $idx ) );
  if ( !$idx ) {
    return false;
  }
  return ( $ipdb->get_var( "SELECT COUNT(*) FROM $ipdb->groups_rel WHERE groupID = '{$idx}' AND userID = '{$userID}'" ) ) ? true : false;
}
function hasConvWrite( $idx = null ) {
  $userID = get_user_id();
  if ( !$userID ) {
    return false;
  }
  global $ipdb;
  $idx  = $ipdb->escape( trim( $idx ) );
  if ( !$idx ) {
    return false;
  }
  return ( $ipdb->get_var( "SELECT status FROM $ipdb->groups_rel WHERE groupID = '{$idx}' AND userID = '{$userID}'" ) === "active" ) ? true : false;
}
function convLeftId( $idx = null ) {
  $userID = get_user_id();
  if ( !$userID ) {
    return false;
  }
  global $ipdb;
  $idx  = $ipdb->escape( trim( $idx ) );
  if ( !$idx ) {
    return false;
  }
  return $ipdb->get_var( "SELECT ID FROM $ipdb->messages WHERE groupID = '{$idx}' AND sent_from = '{$userID}' AND userID = '{$userID}' AND notice_section = 'left' AND is_notice = 1 ORDER BY ID DESC" );
}

function srm( $index = null ) {
  if ( strtolower( $_SERVER["REQUEST_METHOD"] ) === "post" ) {
    return getPostArr( $index );
  }
  else {
    return getGetArr( $index );
  }
}

function connectionExists( $host = null, $port = 0 ) {
  return true;
  $timeout  = 30;
  try {
    $socket = fsockopen( $host, $port, $error_num, $error_str, $timeout ) ;
    if ( $socket && is_resource( $socket ) ) {
      fclose( $socket );
      return true;
    }
  }
  catch( Exception $e ) {}
  return false;
}
function executeWebSocket( $host = null, $port = 0 ) {
  if ( connectionExists( $host, $port ) ) {
    writeLogFile( "sockets", "socket connection exists at $host:$port" );
    return;
  }

  writeLogFile( "sockets", "socket connection does not exists. starting server at $host:$port" );
  global $dirbaseuri;

  try {
    $file = escapeshellarg( dirname( $dirbaseuri )."/socket/server.php" );
    if ( strtolower( substr( php_uname(), 0, 7 ) ) == "windows" ) {
      pclose( popen( "start /B php -q ".$file, "r" ) );
    }
    else {
      exec( "php -q ".$file." > /dev/null &" );
    }
    return true;
  }
  catch( Exception $e ) {}
  return false;
}

function writeLogFile( $file = null, $text = null ) {
  global $dirbaseuri;

  $file = dirname( $dirbaseuri )."/logs/".trim( $file ).".txt";
  $text = trim( $text );

  if ( !file_exists( dirname( $file ) ) ) {
    mkdir( dirname( $file ), 0755, true );
  }

  $text = date( "d/m/y H:i:s" )." ".$text.PHP_EOL;
  try {
    $file = fopen( $file, "a+" );
    if ( $file && is_resource( $file ) ) {
      fwrite( $file, $text );
      fclose( $file );
    }
    return true;
  }
  catch( Exception $e ) {}
  return false;
}

/*function exceptionErrorHandler( $code, $message, $file, $line, $context ) {
  throw new Exception( $message.' ['.$file.':'.$line.']', $code );
}

set_error_handler( 'exceptionErrorHandler' );*/
?>
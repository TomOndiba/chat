<?php

/**
 * @author bystwn22
 * @copyright 2014
 */

error_reporting( 0 );
set_time_limit( 0 );
ignore_user_abort( false );

header( "Content-Type: text/event-stream" );
header( "Cache-Control: no-cache" ); // recommended to prevent caching of event data.

require_once( dirname( __FILE__ )."/includes/conn/open.php" );
loadClass( "ipHooks", "required/hooks/hooks.class.php" );
loadClass( "ipPlugins", "plugins.class.php" );
loadClass( "ipLanguage", "lang.class.php" );
loadClass( "ipUsers", "users.class.php" );
loadClass( "ipPing", "ping.class.php" );

global $ipPlugins, $ipdb;
$userID = get_user_id();

$sleepEvent   = true;
$hasInstance  = false;

$eventSource  = getGetArr( "eventSource" );
$pingEvent    = getGetArr( "pingEvent" );
$ping_id      = getGetArr( "ping_id" );
$tabsList     = getGetArr( "tabs" );
$mobileOnly   = getGetArr( "mobile_only" );
$page         = getGetArr( "page" );
$nodeConn     = getGetArr( "node" );

$lastRetryTimer = ( isset( $_SESSION["lastRetryTimer"] ) ) ? (int)$_SESSION["lastRetryTimer"] : 0;
$usersLastList  = ( isset( $_SESSION["usersLastList"] ) ) ? (int)$_SESSION["usersLastList"] : 0;
$notifLastList  = ( isset( $_SESSION["notifLastList"] ) ) ? (int)$_SESSION["notifLastList"] : 0;

$userInstance1  = new ipUsers( $userID );
$userInstance2  = new ipUsers( $userID );
$userInstance3  = new ipUsers( $userID );
$pingInstance   = new ipPing( $userID, $ping_id );

$userInstance1->refresh_status();

$user   = $userInstance2->get_user( $userID );

if ( $eventSource ) {
  echo ":".str_repeat( " ", 2048 ).PHP_EOL.PHP_EOL;
}

if ( !$mobileOnly ) {
  if ( !$usersLastList || ( time() - $usersLastList ) > 59 ) {
    $users  = $userInstance3->list_users( 30 );
    $_SESSION["usersLastList"]  = time();
  
    if ( $users ) {
      $usersCurrentList = json_encode( $users );
      $hasInstance  = true;
      if ( $eventSource ) {
        echo "event: users".PHP_EOL;
        echo "data: ".$usersCurrentList.PHP_EOL.PHP_EOL;
      }
    }
  }
  
  if ( !$notifLastList || ( time() - $notifLastList ) > 59 ) {
    $notif  = $pingInstance->process( "notif", $tabsList );
    $_SESSION["notifLastList"]  = time();
  
    if ( $notif ) {
      $hasInstance  = true;
      if ( $eventSource ) {
        echo "event: notifications".PHP_EOL;
        echo "data: ".json_encode( $notif ).PHP_EOL.PHP_EOL;
      }
    }
  }
}

if ( $pingEvent ) {
  if ( ( $mobileOnly && in_array( $page, array( "messages", "messenger" ) ) ) || !$mobileOnly ) {
    $messages = $pingInstance->process( "chat", $tabsList );
    if ( $messages ) {
      $hasInstance  = true;
      if ( $eventSource ) {
        echo "event: messages".PHP_EOL;
        echo "data: ".json_encode( $messages ).PHP_EOL.PHP_EOL;
      }
    }

    if ( ( $mobileOnly && $page === "messenger" ) || !$mobileOnly ) {
      $seen = $pingInstance->process( "seen", $tabsList );
      if ( $seen && ( isset( $seen["s"] ) && !empty( $seen["s"] ) ) && ( isset( $seen["t"] ) && !empty( $seen["t"] ) ) ) {
        $hasInstance  = true;
        if ( $eventSource ) {
          echo "event: seen".PHP_EOL;
          echo "data: ".json_encode( $seen ).PHP_EOL.PHP_EOL;
        }
      }
    }
  }

  if ( ( $mobileOnly && $page === "buddylist" ) || !$mobileOnly ) {
    $status = $pingInstance->process( "status", $tabsList );
    if ( $status ) {
      if ( $eventSource ) {
        if ( isset( $status["t"] ) && $status["t"] === "continue" ) {
          
        }
        else {
          $hasInstance  = true;
          echo "event: status".PHP_EOL;
          echo "data: ".json_encode( $status ).PHP_EOL.PHP_EOL;
        }
      }
    }
  }
}

if ( $eventSource ) {
  if ( !$hasInstance ) {
    if ( $_SESSION["lastRetryTimer"] < 60000 ) {
      $_SESSION["lastRetryTimer"] = $lastRetryTimer = ( $lastRetryTimer + 2000 );
    }
    echo "retry: ".$lastRetryTimer.PHP_EOL;
  }
  else {
    $_SESSION["lastRetryTimer"] = $lastRetryTimer = 2000;
    echo "retry: ".$lastRetryTimer.PHP_EOL;
  }
  ob_flush();
  flush();
}
?>
<?php
  require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) )."/includes/required/admin.class.php" );
  global $ipdb;

  $is_post_request  = strtolower( $_SERVER["REQUEST_METHOD"] ) === "post";
  $is_ajax_request  = is_ajax_request();

  if ( !$is_post_request ) {
    exit();
  }

  if ( !ModLogin::isLogged() || ModLogin::isExpired() ) {
    exportError( "You don't have enough permission to make changes", 1 );
  }

  $items  = getPostArr( "items", array(), false );
  $delete = getPostArr( "delete", false, false, false );
  $clear  = getPostArr( "clear", false, false, false );

  if ( $clear ) {
    do_action( "onclearnotifs" );
    if ( $ipdb->query( "DELETE FROM `$ipdb->notif`" ) ) {
      $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `structure` = 'notifReaded'" );
      exportError( "All notifications were successfully cleared", 0 );
    }
  }
  elseif ( $delete ) {
    $items  = array_map( "trim", array_map( array( $ipdb, "escape" ), $items ) );
    $items  = array_unique( array_filter( $items ) );

    do_action( "ondeletenotifs", false, $items );
    $items  = trim( implode( ",", $items ) );

    if ( !empty( $items ) ) {
      if ( $ipdb->query( "DELETE FROM `$ipdb->notif` WHERE `ID` IN ({$items})" ) ) {
        $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `targetID` IN ({$items}) AND `structure` = 'notifReaded'" );
        exportError( "Selected notifications were successfully deleted", 0 );
      }
    }
  }

  if ( !$is_ajax_request ) {
    header( "Location: ".$_SERVER["HTTP_REFERER"] );
  }
  else {
    exportError( "Unidentified request or request cannot be processed at this time", 1 );
  }
?>
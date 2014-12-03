<?php
  require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) )."/includes/required/admin.class.php" );
  global $ipdb, $ipudb;

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
  $isblock    = getPostArr( "block", false, false, false );
  $isunblock  = getPostArr( "unblock", false, false, false );

  if ( $clear ) {
    do_action( "onclearusers" );
    if ( $ipudb->query( "DELETE FROM `{$ipudb->users}`" ) ) {
      $ipdb->query( "DELETE FROM `$ipdb->attachments`" );
      $ipdb->query( "DELETE FROM `$ipdb->groups`" );
      $ipdb->query( "DELETE FROM `$ipdb->groups_rel`" );
      $ipdb->query( "DELETE FROM `$ipdb->messages`" );
      $ipdb->query( "DELETE FROM `$ipdb->notif`" );
      $ipdb->query( "DELETE FROM `$ipdb->online`" );
      $ipdb->query( "DELETE FROM `$ipdb->online_rel`" );
      $ipdb->query( "DELETE FROM `$ipdb->relation`" );
      

      if ( $uploads = realpath( root_dir()."ipChat/uploads/" ) ) {
        rrmdir( $uploads );
      }

      exportError( "Chat history and Users cleared successfully.", 0 );
    }
  }
  elseif ( $delete ) {
    $items  = array_map( "trim", array_map( array( $ipdb, "escape" ), $items ) );
    $items  = array_unique( array_filter( $items ) );

    do_action( "ondeleteusers", false, $items );
    $items  = trim( implode( ",", $items ) );

    if ( !empty( $items ) ) {
      $fieldID  = $ipudb->escape( USER_COL_ID );
      $users    = $ipudb->get_results( "SELECT * FROM `{$ipudb->users}` WHERE `{$fieldID}` IN ({$items})" );

      if ( !empty( $items ) ) {
        $attachments  = $ipdb->get_results( "SELECT `target`, `thumbnail` FROM `$ipdb->attachments` WHERE `userID` IN ({$items})" );
      }

      if ( $ipudb->query( "DELETE FROM `{$ipudb->users}` WHERE `{$fieldID}` IN ({$items})") ) {
        $ipdb->query( "DELETE FROM `$ipdb->messages` WHERE ( `targetID` IN ({$items}) OR `userID` IN ({$items}) ) AND `groupID` = 0" );
        $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE ( `mainID` IN ({$items}) OR `targetID` IN ({$items}) ) AND `targetIG` = 'user'" );
        $ipdb->query( "DELETE FROM `$ipdb->online_rel` WHERE `user` IN ({$items}) OR `target` IN ({$items})" );
        $ipdb->query( "DELETE FROM `$ipdb->groups_rel` WHERE `userID` IN ({$items})" );
        $ipdb->query( "DELETE FROM `$ipdb->notif` WHERE `sender` IN ({$items}) OR `reciever` IN ({$items})" );
        if ( isset( $attachments ) && $attachments ) {
          foreach( $attachments as $attachment ) {
            $attachment->target     = trim( $attachment->target );
            $attachment->thumbnail  = trim( $attachment->thumbnail );

            $target = ( $attachment->target ) ? realpath( root_dir().$attachment->target ) : false;
            $thumb  = ( $attachment->thumbnail ) ? realpath( root_dir().$attachment->thumbnail ) : false;
  
            if ( $target ) {
              @unlink( $target );
            }
            if ( $thumb ) {
              @unlink( $thumb );
            }
          }
          $ipdb->query( "DELETE FROM `$ipdb->attachments` WHERE `userID` IN ({$items})" );
        }
        exportError( "Selected users were successfully removed", 0 );
      }
    }
  }
  elseif ( $isblock ) {
    $time   = $ipdb->escape( time() );
    $items  = array_map( "trim", array_map( array( $ipdb, "escape" ), $items ) );
    $items  = array_unique( array_filter( $items ) );

    do_action( "onblockusers", false, $items );

    if ( !empty( $items ) ) {
      foreach( $items as $item ) {
        $ipdb->query( "
          INSERT INTO `$ipdb->relation`
            (`mainID`, `targetID`, `targetIG`, `structure`, `time`)
          VALUES
            ('0', '{$item}', 'user', 'blockedList', '{$time}')
          ON DUPLICATE KEY UPDATE
            `time` = '{$time}'
        " );
      }
      exportError( "Selected users were successfully blocked", 0 );
    }
  }
  elseif ( $isunblock ) {
    $items  = array_map( "trim", array_map( array( $ipdb, "escape" ), $items ) );
    $items  = array_unique( array_filter( $items ) );

    do_action( "onunblockusers", false, $items );
    $items  = trim( implode( ",", $items ) );

    if ( !empty( $items ) ) {
      $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `mainID` = 0 AND `targetID` IN ({$items}) AND `targetIG` = 'user' AND `structure` = 'blockedList'" );
      exportError( "Selected users were successfully unblocked", 0 );
    }
  }


  if ( !$is_ajax_request ) {
    header( "Location: ".$_SERVER["HTTP_REFERER"] );
  }
  else {
    exportError( "Unidentified request or request cannot be processed at this time", 1 );
  }
?>
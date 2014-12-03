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
    do_action( "oncleargroups" );
    if ( $ipdb->query( "DELETE FROM `$ipdb->groups`" ) ) {
      $ipdb->query( "DELETE FROM `$ipdb->groups_rel`" );
      $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `structure` != 'blockedList' AND `targetIG` = 'group'" );
  
      $relations  = $ipdb->get_results( "SELECT `relationID` FROM `$ipdb->messages` WHERE `groupID` != '0' GROUP BY `relationID`" );
      $message_id = array();
  
      if ( $relations ) {
        foreach( $relations as $relation ) {
          $message_id[] = $relation->relationID;
        }
        $quick_san  = function( $v ) {
          global $ipdb;
          return json_encode( $ipdb->escape( trim( $v ) ) );
        };
        $message_id = array_map( $quick_san, array_filter( $message_id ) );
        $message_id = implode( ",", $message_id );
      }
  
      if ( !empty( $message_id ) ) {
        $attachments  = $ipdb->get_results( "SELECT `target`, `thumbnail` FROM `$ipdb->attachments` WHERE `relationID` IN ({$message_id})" );
      }
  
      if ( $ipdb->query( "DELETE FROM `$ipdb->messages` WHERE `relationID` IN ({$message_id})" ) ) {
        $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `identifier` IN ({$message_id})" );
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
          $ipdb->query( "DELETE FROM `$ipdb->attachments` WHERE `relationID` IN ({$message_id})" );
        }
      }

      exportError( "All groups and data associated with the groups removed", 0 );
    }
  }
  elseif ( $delete ) {
    $items  = array_map( "trim", array_map( array( $ipdb, "escape" ), $items ) );
    $items  = array_unique( array_filter( $items ) );

    do_action( "ondeletegroups", false, $items );
    $items  = trim( implode( ",", $items ) );

    if ( !empty( $items ) ) {
      if ( $ipdb->query( "DELETE FROM `$ipdb->groups` WHERE `ID` IN ({$items})" ) ) {
        $ipdb->query( "DELETE FROM `$ipdb->groups_rel` WHERE `groupID` IN ({$items})" );
        $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `structure` != 'blockedList' AND `targetIG` = 'group' AND `targetID` IN ({$items})" );
    
        $relations  = $ipdb->get_results( "SELECT `relationID` FROM `$ipdb->messages` WHERE `groupID` IN ({$items}) GROUP BY `relationID`" );
        $message_id = array();
    
        if ( $relations ) {
          foreach( $relations as $relation ) {
            $message_id[] = $relation->relationID;
          }
          $quick_san  = function( $v ) {
            global $ipdb;
            return json_encode( $ipdb->escape( trim( $v ) ) );
          };
          $message_id = array_map( $quick_san, array_filter( $message_id ) );
          $message_id = implode( ",", $message_id );
        }
    
        if ( !empty( $message_id ) ) {
          $attachments  = $ipdb->get_results( "SELECT `target`, `thumbnail` FROM `$ipdb->attachments` WHERE `relationID` IN ({$message_id})" );
        }
    
        if ( $ipdb->query( "DELETE FROM `$ipdb->messages` WHERE `relationID` IN ({$message_id})" ) ) {
          $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `identifier` IN ({$message_id})" );
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
            $ipdb->query( "DELETE FROM `$ipdb->attachments` WHERE `relationID` IN ({$message_id})" );
          }
        }
  
        exportError( "Selected groups and data associated with the groups removed", 0 );
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
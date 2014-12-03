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

  $add_extn = getPostArr( "add-extn", false, false, false );
  $add_mime = getPostArr( "add-mime", false, false, false );
  $del_extn = getPostArr( "del-extn", false, false, false );
  $del_mime = getPostArr( "del-mime", false, false, false );
  $add_both = getPostArr( "add-both", false, false, false );
  $del_both = getPostArr( "del-both", false, false, false );

  $quick_san  = function( $v ) {
    global $ipdb;
    return json_encode( $ipdb->escape( trim( $v ) ) );
  };

  if ( $clear ) {
    do_action( "onclearattachments" );
    $links  = $ipdb->get_results( "SELECT `relationID` FROM `$ipdb->attachments` GROUP BY `relationID`" );
    $rels   = array();
    if ( $links ) {
      foreach( $links as $link ) {
        if ( !in_array( $link->relationID, $rels ) ) {
          array_push( $rels, $link->relationID );
        }
      }
      $rels = array_map( $quick_san, array_filter( $rels ) );
      $rels = implode( ",", $rels );

      if ( !empty( $rels ) ) {
        if ( $ipdb->query( "DELETE FROM `$ipdb->messages` WHERE ( ( `notice_section` = 'attachment' AND `is_notice` = '1' ) OR ( `message` = '' ) ) AND `relationID` IN ({$rels})" ) ) {
          $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `identifier` IN ({$rels})" );
        }
        $ipdb->query( "UPDATE `$ipdb->messages` SET `has_attachment` = '0' WHERE `has_attachment` = '1' AND `relationID` IN ({$rels})" );
      }

      $ipdb->query( "DELETE FROM `$ipdb->attachments`" );
    }

    if ( $uploads = realpath( root_dir()."ipChat/uploads/" ) ) {
      rrmdir( $uploads );
    }

    exportError( "All file(s) and messages associated with its cleared", 0 );
  }
  elseif ( $delete ) {
    $items  = array_map( array( $ipdb, "escape" ), array_filter( array_map( "trim", (array)$items ) ) );
    do_action( "ondeleteattachments", false, $items );
    $items  = trim( implode( ",", $items ) );
    if ( !empty( $items ) ) {
      $links  = $ipdb->get_results( "SELECT `target`, `thumbnail`, `relationID` FROM `$ipdb->attachments` WHERE `ID` IN ({$items})" );
      if ( $ipdb->query( "DELETE FROM `$ipdb->attachments` WHERE `ID` IN ({$items})" ) ) {
        if ( $links ) {
          $relations  = array();
          foreach( $links as $link ) {
            $link->target     = trim( $link->target );
            $link->thumbnail  = trim( $link->thumbnail );

            $target = ( $link->target ) ? realpath( root_dir().$link->target ) : false;
            $thumb  = ( $link->thumbnail ) ? realpath( root_dir().$link->thumbnail ) : false;
  
            if ( $target ) {
              @unlink( $target );
            }
            if ( $thumb ) {
              @unlink( $thumb );
            }

            if ( !in_array( $link->relationID, $relations ) ) {
              array_push( $relations, $link->relationID );
            }
          }
          $relations  = array_map( $quick_san, array_filter( $relations ) );
          $relations  = implode( ",", $relations );

          if ( !empty( $relations ) ) {
            if ( !$ipdb->get_var( "SELECT COUNT(*) FROM `$ipdb->attachments` WHERE `relationID` IN ({$relations})" ) ) {
              if ( $ipdb->query( "DELETE FROM `$ipdb->messages` WHERE ( ( `notice_section` = 'attachment' AND `is_notice` = '1' ) OR ( `message` = '' ) ) AND `relationID` IN ({$relations})" ) ) {
                $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `identifier` IN ({$relations})" );
              }
              $ipdb->query( "UPDATE `$ipdb->messages` SET `has_attachment` = '0' WHERE `has_attachment` = '1' AND `relationID` IN ({$relations})" );
            }
          }
        }

        exportError( "Selected file(s) were successfully deleted", 0 );
      }
    }
  }
  elseif ( $add_extn || $add_mime || $del_extn || $del_mime ) {
    $items  = array_filter( array_map( "trim", (array)$items ) );
    if ( !empty( $items ) ) {
      $list   = getWhiteBlackList();
      $index  = ( $add_extn || $del_extn ) ? "extn" : "mime";

      if ( $add_extn || $add_mime ) {
        $list->{$index} = array_filter( array_unique( array_merge( $list->{$index}, $items ) ) );
      }
      elseif ( $del_extn || $del_mime ) {
        $list->{$index} = array_diff( $list->{$index}, $items );
      }

      if ( updateWhiteBlackList( $list ) ) {
        exportError( ucfirst( ipgo( "blocked_files_mode" ) )." successfully updated", 0 );
      }
    }
  }
  elseif ( $add_both || $del_both ) {
    $items  = array_filter( (array)$items );
    if ( !empty( $items ) ) {
      $list = getWhiteBlackList();

      if ( $del_both ) {
        if ( isset( $items["extn"] ) ) {
          $items["extn"]  = array_filter( array_map( "trim", (array)$items["extn"] ) );
          $list->extn     = array_diff( $list->extn, $items["extn"] );
        }
        if ( isset( $items["mime"] ) ) {
          $items["mime"]  = array_filter( array_map( "trim", (array)$items["mime"] ) );
          $list->mime     = array_diff( $list->mime, $items["mime"] );
        }
      }
      else {
        if ( isset( $items["extn"] ) ) {
          $items["extn"]  = array_filter( array_map( "trim", (array)$items["extn"] ) );
          $list->extn     = array_filter( array_unique( array_merge( $list->extn, $items["extn"] ) ) );
        }
        if ( isset( $items["mime"] ) ) {
          $items["mime"]  = array_filter( array_map( "trim", (array)$items["mime"] ) );
          $list->mime     = array_filter( array_unique( array_merge( $list->mime, $items["mime"] ) ) );
        }
      }

      if ( updateWhiteBlackList( $list ) ) {
        exportError( ucfirst( ipgo( "blocked_files_mode" ) )." successfully updated", 0 );
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
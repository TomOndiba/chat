<?php

/**
 * @author bystwn22
 * @copyright 2013
 */

header( "Content-Type: application/json" );

require_once( dirname( __FILE__ )."/includes/conn/open.php" );
loadClass( "ipHooks", "required/hooks/hooks.class.php" );
loadClass( "ipGzip", "gzip.class.php" );
loadClass( "ipPlugins", "plugins.class.php" );
loadClass( "ipLanguage", "lang.class.php" );

$ipLang = new ipLanguage( ilanguage() );
$ipGzip = new ipGzip;
$ipGzip::set_expire_date( 0 );
$ipGzip::set_last_modified( time() );
$ipGzip::set_content_type( "application/json" );
$hasGzip  = true;

$userID   = get_user_id();
$channel  = trim( strtolower( (string)srm( "channel" ) ) );
$process  = trim( strtolower( srm( "process" ) ) );
$action   = trim( strtolower( srm( "action" ) ) );

global $ipPlugins;

loadClass( "ipUsers", "users.class.php" );
if ( isset( $_POST["ping"] ) && $_POST["ping"] === "status" ) {
  $sping  = new ipUsers( $userID );
  $drop   = getPostArr( "drop" );
  $sping->refresh_status( $drop );
  exportError( "success", 0 );
}
else {
  $sping  = new ipUsers( $userID );
  $sping->refresh_status();
}

switch( $channel ) {
  case "settings":
    switch( $process ) {
      case "emoticons":
        $item = getPostArr( "item" );
        exportResponse( ipSmilies::load( $item ) );
      break;
      case "chat":
        loadClass( "ipRelation", "relation.class.php" );
        $rel  = new ipRelation( $userID );
        if ( $action === "update" ) {
          $tokens = getPostArr( "tokens" );
          $data   = getPostArr( "data" );
          exportResponse( $rel->update_chat_settings( $tokens, $data ) );
        }
        elseif ( $action === "quick" ) {
          $idx  = getPostArr( "idx" );
          $idn  = getPostArr( "idn" );
          if ( $rel->presence_settings( $idx, $idn ) ) {
            exportError( "success", 0 );
          }
          exportError( "error" );
        }
        else {
          exportResponse( $rel->online_settings() );
        }
      break;
      case "update":
        $idx  = getPostArr( "idx" );
        $idn  = getPostArr( "idn" );
        if ( ipso( $idx, $idn ) ) {
          exportError( "success", 0 );
        }
        exportError( "error", 1 );
      break;
      default:
        $socketEnabled  = ( (int)ipgo( "enable_socket" ) === 1 && (int)getPostArr( "sockets" ) === 1 );
        if ( $socketEnabled ) {
          $socketHost = ipgo( "socket_host" );
          $socketPort = (int)ipgo( "socket_port" );
        }

        $response = array();
        $response["settings"] = parse_settings();
        $response["language"] = $ipLang->list_language();
        $response["languages"]  = array(
                                    "write" =>  $ipLang->writing_languages(),
                                    "read"  =>  $ipLang->reading_languages(),
                                    "codes" =>  array(
                                                  "r" =>  $ipLang->reading_language(),
                                                  "w" =>  $ipLang->writing_language()
                                                )
                                  );

        $ipPlugins->setPluginFormat( "js" );
        $response["plugins"]  = $ipPlugins->listPlugins( true, false );
        $response["themes"]   = ipThemes::themes( dirname( __FILE__ )."/css/themes/" );

        $ipPlugins->setPluginFolder( dirname( __FILE__ )."/css/external/" );
        $ipPlugins->setPluginFormat( "css" );
        $response["styles"]   = $ipPlugins->listPlugins( false, false );
        $response["mentor"]   = IMPACTPLUS_SERVER;
        $response["emoticon"] = ipSmilies::load( false, true );
        if ( $socketEnabled ) {
          $response["socket"] = array( $socketHost, $socketPort );
        }
        else {
          $response["socket"] = false;
        }
        exportResponse( $response );
      break;
    }
  break;
  case "tabs":
    switch( $process ) {
      case "state":
        loadClass( "ipRelation", "relation.class.php" );
        $id   = getPostArr( "id" );
        $type = getPostArr( "type" );
        if ( $action === "open" ) {
          $rel  = new ipRelation( $userID );
          $rel  = $rel->openTab( $id, $type );
          exportError( ( $rel ) ? "success" : "error", ( $rel ) ? 0 : 1 );
        }
        elseif ( $action === "close" ) {
          $rel  = new ipRelation( $userID );
          $rel  = $rel->closeTab( $id, $type );
          exportError( ( $rel ) ? "success" : "error", ( $rel ) ? 0 : 1 );
        }
      break;
    }
  break;
  case "users":
    loadClass( "ipUsers", "users.class.php" );
    if ( $action === "load" ) {
      $users  = new ipUsers( $userID );
      $exclud = getPostArr( "exclude", array() );
      $init   = getPostArr( "init" );
      $limit  = getPostArr( "limit" );
      if ( !$init ) {
        $users->set_excluded_ids( $exclud );
        $users  = $users->list_users( $limit );
        exportResponse( $users );
      }
      else {
        $user   = $users->get_user( $userID );
        if ( !$user ) {
          exportResponse( array( "user" => false, "users" => false ) );
        }
        loadClass( "ipRelation", "relation.class.php" );
        $users  = $users->list_users( 30 );
        $relat  = new ipRelation( $userID );
        exportResponse( array( "user" => $user, "users" => $users, "tabs" => $relat->getTabs() ) );
      }
    }
    elseif ( $action === "search" ) {
      $search = getPostArr( "search" );
      $exclud = getPostArr( "exclude", array() );
      $users  = ipUsers::fetch_users( $userID, $search, $exclud );
      exportResponse( $users );
    }
    elseif ( $action === "get" ) {
      $user = new ipUsers( $userID );
      if ( $process === "blocked" ) {
        if ( $user = $user->list_blocked( false, false, true ) ) {
          exportResponse( $user );
        }
        exportError( "We were unable to get this user information." );
      }
      else {
        $id   = getPostArr( "id" );
        $user = $user->get_user( $id );
        if ( !$user ) {
          exportError( "We were unable to get this user information." );
        }
        exportResponse( $user );
      }
    }
    elseif ( $action === "batch" ) {
      $id = getPostArr( "id", array() );
      $users  = ipUsers::get_users( $userID, $id );
      if ( !$users ) {
        exportError( "We were unable to get this users information." );
      }
      exportResponse( $users );
    }
    elseif ( $action === "block" ) {
      $id = getPostArr( "id", array() );
      $users  = new ipUsers( $userID );
      if ( !$users->block_users( $id ) ) {
        exportError( "We were unable to block this user(s)." );
      }
      exportError( "success", 0 );
    }
    elseif ( $action === "unblock" ) {
      $id = getPostArr( "id", array() );
      $users  = new ipUsers( $userID );
      if ( !( $users = $users->unblock_users( $id ) ) ) {
        exportError( "We were unable to unblock this user(s)." );
      }
      exportResponse( $users );
    }
  break;
  case "ping":
    loadClass( "ipPing", "ping.class.php" );
    $tabs = srm( "tabs" );
    $pnid = srm( "ping_id" );
    $ping = new ipPing( $userID, $pnid );
    if ( $response = $ping->process( $process, $tabs ) ) {
      exportResponse( $response );
    }
    exportError( $ping->get_error() );
  break;
  case "socket":
    switch( $process ) {
      case "seen":
        $idx = getPostArr( "idx" );
        $idn = getPostArr( "idn" );
      break;
    }
  break;
  case "notifications":
    $notif    = ( isset( $_POST["notif"] ) ) ? $ipdb->escape( trim( (string)$_POST["notif"] ) ) : null;
    switch( $process ) {
      case "read":
        loadClass( "relation" );
        $relation = new ipRelation( $userID );
        if ( !$relation->relation_exists( $userID, $notif, "user", "notifReaded", null, "count" ) ) {
          if ( $relation->insert_relation( $userID, $notif, "user", "notifReaded", time() ) ) {
            exportError( "success", 0 );
          }
        }
        exportError( "Could not move notification to readed group" );
      break;
      case "get":
      default:
        exportError( "Request could not be processed (Malformed)" );
      break;
    }
  break;
  case "languages":
    switch( $process ) {
      case "translate":
        $search = getPostArr( "search", null );
        $tlang  = getPostArr( "to", "en" );
        if ( $translation = $ipLang->translate_ime( $search, $tlang ) ) {
          echo json_encode( $translation );
          exit();
        }
        exportError( "Translation failed with undefined error" );
      break;
    }
  break;
  case "messages":
    switch( $process ) {
      case "stream":
        loadClass( "ipStreamParser", "stream.class.php" );
        $url    = getPostArr( "url", false );
        $crawl  = new ipStreamParser( $url );
        if ( $stream = $crawl->crawl() ) {
          loadClass( "ipMessages", "chat.class.php" );
          $messages = new ipMessages( $userID );
          if ( $response = $messages->uploadSream( $stream ) ) {
            $response['stream'] = $stream;
            exportResponse( $response );
          }
        }
        exportError( "We were unable to crawl this link." );
      break;
      case "typing":
        loadClass( "ipRelation", "relation.class.php" );
        $rel  = new ipRelation( $userID );
        switch( $action ) {
          case "add":
            $idx  = getPostArr( "idx", false );
            $idn  = getPostArr( "idn", false );
            $idj  = (int)getPostArr( "idj", time() );
            if ( $rel->do_typing( $idx, $idn, $idj ) ) {
              exportError( "success", 0 );
            }
            exportError( "We were unable to update typing notification." );
          break;
          default:
          break;
        }
      break;
      case "history":
        $older  = (int)getPostArr( "older" );
        loadClass( "ipMessages", "chat.class.php" );
        $message  = new ipMessages( $userID );
        if ( $response = $message->loadHistory( $older ) ) {
          exportResponse( $response );
        }
        exportError( $ipLang->translate( "MSG_FETCH_FAILED" ) );
      break;
      case "group":
        if ( $action === "add" ) {
          $users  = ( isset( $_POST["users"] ) && is_array( $_POST["users"] ) && !empty( $_POST["users"] ) ) ? $_POST["users"] : false;
          if ( $users === false ) {
            exportError( "You must select at least 2 peoples to start a group conversation" );
          }
          global $ipdb;
          $con  = $ipdb->escape( time() );
          $cby  = $ipdb->escape( $userID );
          $query  = $ipdb->query( "INSERT INTO $ipdb->groups(created_on, created_by) VALUES('{$con}','{$cby}')" );
          if ( $query ) {
            $users[]  = (int)$userID;
            $groupID  = $ipdb->insert_id;
            foreach( $users as &$user ) {
              $user = (int)$user;
              $ipdb->query( "INSERT INTO $ipdb->groups_rel(groupID, userID) VALUES('{$groupID}','{$user}')" );
            }
            exportResponse(
              array(
                "users" =>  $users,
                "ID"    =>  $groupID,
                "time"  =>  $con,
                "owner" =>  $userID,
                "name"  =>  null,
                "write" =>  true,
                "avail" =>  $users
              )
            );
          }
          exportError( "Unexpected end point error" );
        }
        elseif ( $action === "get" ) {
          loadClass( "ipRelation", "relation.class.php" );
          $idx  = getPostArr( "id" );
          if ( !hasConvRead( $idx ) ) {
            exportError( "We were unable to get this group information." );
          }
          $rel  = new ipRelation( $userID );
          if ( $group = $rel->getGroupInfo( $idx ) ) {
            exportResponse( $group );
          }
          exportError( "We were unable to get this group information." );
        }
        elseif ( $action === "leave" ) {
          $idx  = getPostArr( "id" );
          if ( !hasConvRead( $idx ) ) {
            exportError( "You dont have enough permission to manage this group." );
          }
          elseif ( !hasConvWrite( $idx ) ) {
            exportError( "You already left this group" );
          }
          else {
            loadClass( "ipMessages", "chat.class.php" );
            $message  = new ipMessages( $userID );
            if ( $response = $message->leaveGroup( $idx ) ) {
              exportResponse( $response );
            }
          }
          exportError( "We were unable to get this group information." );
        }
        elseif ( $action === "naming" ) {
          $idx  = getPostArr( "id" );
          $name = getPostArr( "name" );
          if ( !hasConvRead( $idx ) ) {
            exportError( "You dont have enough permission to name this group." );
          }
          elseif ( !hasConvWrite( $idx ) ) {
            exportError( "You already left this group" );
          }
          else {
            loadClass( "ipMessages", "chat.class.php" );
            $message  = new ipMessages( $userID );
            if ( $response = $message->nameGroup( $idx, $name ) ) {
              exportResponse( $response );
            }
          }
          exportError( "We were unable to get this group information." );
        }
        elseif ( $action === "update" ) {
          $idx    = getPostArr( "id" );
          $users  = getPostArr( "users" );
          if ( !hasConvRead( $idx ) ) {
            exportError( "You dont have enough permission to add peoples to this group." );
          }
          elseif ( !hasConvWrite( $idx ) ) {
            exportError( "You already left this group" );
          }
          else {
            loadClass( "ipMessages", "chat.class.php" );
            $message  = new ipMessages( $userID );
            if ( $response = $message->updateGroup( $idx, $users ) ) {
              exportResponse( $response );
            }
          }
          exportError( "We were unable to get this group information." );
        }
      break;
      case "message":
        if ( $action === "send" ) {
          loadClass( "ipMessages", "chat.class.php" );
          $message  = getPostArr( "message", array(), false );
          $chat     = new ipMessages( $userID );
          if ( $response  = $chat->send( $message ) ) {
            exportResponse( $response );
          }
          exportError( "Unable to connect to Chat. This message failed to send." );
        }
        elseif ( $action === "load" ) {
          loadClass( "ipMessages", "chat.class.php" );
          $id     = getPostArr( "id" );
          $type   = getPostArr( "type" );
          $older  = getPostArr( "older" );
          if ( $chat = ipMessages::load( $userID, $id, $type, $older ) ) {
            exportResponse( $chat );
          }
          exportError( "We were unable to fetch previous messages in this conversation." );
        }
        elseif ( $action === "addseen" ) {
          loadClass( "ipMessages", "relation.class.php" );
          $id     = getPostArr( "idx" );
          $type   = getPostArr( "idn" );
          $chat   = new ipRelation( $userID );
          $chat->add_seen_time( $userID, $id, $type );
          exportResponse( array( "uid" => $userID, "idx" => $id, "idn" => $type ) );
        }
        elseif ( $action === "getseen" ) {
          loadClass( "ipMessages", "relation.class.php" );
          $id     = getPostArr( "idx" );
          $type   = getPostArr( "idn" );
          $chat   = new ipRelation( $userID );
          exportResponse( array( "seen" => $chat->get_seen_time( $id, $type ) ) );
        }
      break;
    }
  break;
  case "attachments":
    $relation_id  = getPostArr( "relation_id" );
    $target_id    = getPostArr( "nubuid" );
    $target_type  = getPostArr( "nubmod" );
    $source       = getPostArr( "source" );
    $attachment   = toSingleFile( ( isset( $_FILES["attachment"] ) ) ? $_FILES["attachment"] : array() );
    $has_error    = true;

    $_ksht  = (int)getPostArr( "_ksht" );
    $_kshc  = (int)getPostArr( "_kshc" );

    if ( $process === "message" ) {
      if ( !$relation_id || !$target_id || !$target_type || !$attachment ) {
        exportError( "We were unable to upload this image." );
      }
      loadClass( "ipMessages", "chat.class.php" );
      $messages = new ipMessages( $userID );
      if ( $upload = $messages->uploadFile( $attachment, $relation_id ) ) {
        $has_error  = false;
        if ( $_kshc !== $_ksht ) {
          exportResponse( $upload );
        }
      }
      if ( $_kshc === $_ksht ) {
        if ( !$message = $messages->doRelationMessage( $relation_id, $target_id, $target_type ) ) {
          $has_error  = true;
          exportError( "We were unable to upload this image." );
        }
        $upload["message"]  = $message;
        exportResponse( $upload );
      }
      exportError( "We were unable to upload this image." );
    }
    elseif ( $process === "attachment" ) {
      if ( $action === "delete" ) {
        $ids  = getPostArr( "idx" );
        if ( !$ids || empty( $ids ) ) {
          exportError( "We were unable to delete this attachment." );
        }
        loadClass( "ipMessages", "chat.class.php" );
        $messages = new ipMessages( $userID );
        if ( $delete = $messages->deleteFile( $ids ) ) {
          exportError( "Attachment successfully deleted.", 0 );
        }
        exportError( "We were unable to delete this attachment." );
      }
      else {
        if ( !$relation_id || !$target_id || !$target_type || !$attachment ) {
          exportError( "We were unable to upload this attachment." );
        }
        loadClass( "ipMessages", "chat.class.php" );
        $messages = new ipMessages( $userID );
        if ( $upload = $messages->uploadFile( $attachment, $relation_id ) ) {
          exportResponse( $upload );
        }
      }
    }
    elseif ( $process === "profile" ) {
      if ( !$attachment ) {
        exportError( "We were unable to upload this image." );
      }
      loadClass( "ipMessages", "chat.class.php" );
      $message  = new ipMessages( $userID );
      $upload   = $message->uploadProcess( $attachment, $relation_id, "/ipChat/images/users/", "/^image\//", "avatar_" );
      if ( $upload ) {
        @unlink( $upload["flnka"] );
        if ( $upload["tlnka"] && file_exists( $upload["tlnka"] ) ) {
          $process_avatar = do_action( "user_avatar", true, $userID, $upload["tlnkr"] );
          if ( $process_avatar ) {
            exportResponse( array(
              "image" =>  $process_avatar,
              "error" =>  0,
              "message" =>  "success"
            ) );
          }
          @unlink( $upload["tlnka"] );          
        }
      }
      exportError( "We were unable to upload this image." );
    }
    elseif ( $process === "info" ) {
      $extension  = getPostArr( "extn" );
      $mimetype   = getPostArr( "mime" );
      $mimegroup  = getPostArr( "mgrp" );

      $finfo  = false;
      $file   = realpath( dirname( __FILE__ )."/includes/file_info.json" );
      if ( $file && is_readable( $file ) ) {
        $finfo  = json_decode( implode( "", file( $file ) ), true );
        if ( $finfo && isset( $finfo[$mimegroup] ) ) {
          exportResponse( $finfo[$mimegroup] );
        }
      }

      exportError( "We were unable to fetch this file information." );
    }
    else {
      exportError( "We were unable to upload attachment." );
    }
  break;
  case "authentification":
    switch( $process ) {
      case "signup":
        if ( is_logged_in() ) {
          exportError( "You are already logged in, you cannot signup again", 0 );
        }

        $user   = getPostArr( "username" );
        $email  = getPostArr( "email" );
        $pass   = getPostArr( "password" );
        $rpass  = getPostArr( "password-alt" );
        $data   = do_action( "user_signup", true, $user, $email, $pass, $rpass );

        if ( !is_array( $data ) || empty( $data ) ) {
          exportError( "Signup failed with an unknown error" );
        }
        else {
          exportError( $data["message"], $data["error"] );
        }
      break;
      case "login":
        if ( is_logged_in() ) {
          exportError( "You are already logged in, you cannot signup again", 0 );
        }
        $user   = getPostArr( "username" );
        $pass   = getPostArr( "password" );
        $data   = do_action( "user_login", true, $user, $pass );

        if ( !is_array( $data ) || empty( $data ) ) {
          exportError( "Login failed with an unknown error" );
        }
        else {
          exportError( $data["message"], $data["error"] );
        }
      break;
      case "reset_pass":
        $user = getPostArr( "username" );
        $data = do_action( "user_reset_pass", true, $user );

        if ( !is_array( $data ) || empty( $data ) ) {
          exportError( "Password reset failed with an unknown error" );
        }
        else {
          exportError( $data["message"], $data["error"] );
        }
      break;
      case "logout":
        if ( !is_logged_in() ) {
          exportError( "You are already logged out", 0 );
        }
        $data = do_action( "user_logout", true );
        if ( !$data ) {
          exportError( "You are already logged out" );
        }
        exportError( "success", 0 );
      break;
    }
  break;
  default:
    exportError( "unidentified request", 1 );
  break;
}
?>
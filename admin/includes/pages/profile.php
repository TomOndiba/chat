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

  $params = array();
  $admin  = ModLogin::getInfo();
  $username = $ipdb->escape( $admin->username );

  $avatar = ( isset( $_FILES["admin-avatar"] ) && !empty( $_FILES["admin-avatar"]["tmp_name"] ) ) ? $_FILES["admin-avatar"] : false;
  if ( $avatar ) {
    $old_avatar = $new_avatar = ( $admin->avatar && realpath( ROOT_DIR."ipChat/admin/".$admin->avatar ) ) ? realpath( ROOT_DIR."ipChat/admin/".$admin->avatar ) : false;
    $upload     = uploadProcess( $avatar, null, "/ipChat/admin/images/", "/^image\//", "avatar_" );
    if ( $upload && is_array( $upload ) ) {
      if ( $old_avatar ) {
        @unlink( $old_avatar );
      }
      $new_avatar = $upload["flnkr"];
      if ( $upload["tlnkr"] ) {
        $new_avatar = $upload["tlnkr"];
        @unlink( $upload["flnka"] );
      }
      $new_avatar = substr( $new_avatar, ( strpos( $new_avatar, "admin/" ) + 6 ) );
    }
    if ( $new_avatar ) {
      $avatar = $ipdb->escape( $new_avatar );
      $ipdb->query( "UPDATE `$ipdb->admin` SET `avatar` = '{$avatar}' WHERE `username` = '{$username}'" );
      call_script( "change_avatar", $new_avatar );
    }
    exit();
  }

  $name   = $ipdb->escape( getPostArr( "admin-name" ) );
  $email  = $ipdb->escape( filter_var( getPostArr( "admin-email" ), FILTER_VALIDATE_EMAIL ) );

  $password1  = $ipdb->escape( getPostArr( "admin-old-pass" ) );
  $password2  = getPostArr( "admin-new-pass" );
  $password3  = getPostArr( "admin-retype-pass" );

  if ( !$name || strlen( $name ) < 3 ) {
    exportError( "Please enter your Full name", 1 );
  }
  $params[] = "`name` = '{$name}'";

  if ( !$email ) {
    exportError( "Please enter your valid E-mail address", 1 );
  }
  $params[] = "`email` = '{$email}'";

  if ( $password1 ) {
    if ( !PassHash::compare_hash( $admin->password, $password1 ) ) {
      exportError( "Incorrect old password, please try again.", 1 );
    }
    if ( !$password2 || strlen( $password2 ) < 5  ) {
      exportError( "Please enter your new password (minimum 5 chars) or leave old password field empty", 1 );
    }
    if ( !$password3 ) {
      exportError( "Please verify your new password", 1 );
    }
    if ( $password2 !== $password3 ) {
      exportError( "Passwords do not match", 1 );
    }
    $password = $ipdb->escape( PassHash::hash( $password2 ) );
    $params[] = "`password` = '{$password}'";
  }

  $params = trim( implode( ", ", $params ) );
  if ( !empty( $params ) ) {
    if ( $ipdb->query( "UPDATE `$ipdb->admin` SET {$params} WHERE `username` = '{$username}'" ) ) {
      exportError( "Your profile successfully updated", 0 );
    }
  }

  if ( !$is_ajax_request ) {
    header( "Location: ".$_SERVER["HTTP_REFERER"] );
  }
  else {
    exportError( "Unidentified request or request cannot be processed at this time", 1 );
  }
?>
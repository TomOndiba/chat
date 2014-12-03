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

  $n_domains  = getPostArr( "allowed_domains" );
  $n_themes   = $ipdb->escape( getPostArr( "active_theme" ) );
  $n_language = $ipdb->escape( getPostArr( "language" ) );
  $n_home_uri = $ipdb->escape( getPostArr( "home_uri" ) );
  $allow_mode = $ipdb->escape( getPostArr( "allow_mode" ) );

  $enable_socket = (int)getPostArr( "enable_socket", 0 );
  $socket_host = getPostArr( "socket_host", 0 );
  $socket_port = (int)getPostArr( "socket_port", 0 );

  $api_key  = getPostArr( "api_key", null );

  $n_blocked  = array_map( "trim", getPostArr( "blocked_files", array() ) );
  $n_notifs   = getPostArr( "notif", array() );
  $n_errors   = array();

  if ( $n_domains ) {
    $n_domains  = array_filter( array_unique( array_map( "trim", explode( ",", $n_domains ) ) ) );
    $n_domains  = apply_filters( "onupdatealloweddomains", $n_domains );
  }
  if ( $n_blocked ) {
    if ( isset( $n_blocked["extn"] ) && !empty( $n_blocked["extn"] ) ) {
      $n_blocked["extn"]  = array_filter( array_unique( array_map( "trim", explode( ",", $n_blocked["extn"] ) ) ) );
    }
    if ( isset( $n_blocked["mime"] ) && !empty( $n_blocked["mime"] ) ) {
      $n_blocked["mime"]  = array_filter( array_unique( array_map( "trim", explode( ",", $n_blocked["mime"] ) ) ) );
    }
    $n_blocked["extn"]  = array_filter( (array)$n_blocked["extn"] );
    $n_blocked["mime"]  = array_filter( (array)$n_blocked["mime"] );

    $n_blocked  = apply_filters( "onupdateblockedfiles", $n_blocked );
  }
  $n_blocked  = $ipdb->escape( ( $n_blocked ) ? json_encode( $n_blocked ) : json_encode( array() ) );

  if ( empty( $n_home_uri ) ) {
    $n_errors[] = "You must provide your site's full URL";
  }
  if ( $n_domains && is_array( $n_domains ) ) {
    $invalid_domains  = array();
    foreach( $n_domains as &$n_domain ) {
      if ( !valid_domain( $n_domain ) ) {
        $invalid_domains[]  = $n_domain;
        $n_domain = null;
      }
    }
    if ( !empty( $invalid_domains ) ) {
      $n_errors[] = "Some domain names are invalid and are removed (".implode( ", ", $invalid_domains ).")";
    }
    $n_domains  = array_filter( $n_domains );
  }
  else {
    $n_domains  = array();
  }
  $n_domains  = trim( $ipdb->escape( json_encode( $n_domains ) ) );

  $n_notif  = array();
  if ( !empty( $n_notifs ) ) {
    foreach( $n_notifs as $notif ) {
      $notif_id = ( isset( $notif["id"] ) ) ? trim( strip_tags( strtolower( $notif["id"] ) ) ) : null;
      $subject  = ( isset( $notif["subject"] ) ) ? trim( strip_tags( $notif["subject"] ) ) : null;
      $message  = ( isset( $notif["message"] ) ) ? trim( $notif["message"] ) : null;
      if ( !$notif_id || !$subject || !$message ) {
        continue;
      }
      $n_notif[$notif_id] = array(
                              "subject" =>  $subject,
                              "message" =>  $message
                            );
    }
  }
  $n_notif  = $ipdb->escape( serialize( $n_notif ) );

  if ( !empty( $n_errors ) ) {
    exportError( '<p>Oops, there seems to be something wrong.</p><br /><ol><li>'.implode( '</li><li>', $n_errors ).'</li></ol>' );
  }

  $by_update  = 0;
  $to_update  = 2;

  $query  = array(
    "UPDATE `$ipdb->settings` SET `value` = '{$n_domains}' WHERE `name` = 'allowed_domains'",
    "UPDATE `$ipdb->settings` SET `value` = '{$allow_mode}' WHERE `name` = 'blocked_files_mode'",
    "UPDATE `$ipdb->settings` SET `value` = '{$n_blocked}' WHERE `name` = 'blocked_files'",
    "UPDATE `$ipdb->settings` SET `value` = '{$n_notif}' WHERE `name` = 'notification_layout'",
    "UPDATE `$ipdb->settings` SET `value` = '{$enable_socket}' WHERE `name` = 'enable_socket'",
    "UPDATE `$ipdb->settings` SET `value` = '{$socket_host}' WHERE `name` = 'socket_host'",
    "UPDATE `$ipdb->settings` SET `value` = '{$socket_port}' WHERE `name` = 'socket_port'",
    "UPDATE `$ipdb->settings` SET `value` = '{$api_key}' WHERE `name` = 'api_key'"
  );

  if ( $n_themes ) {
    do_action( "onupdatedefaulttheme", false, $n_themes );
    $query[]  = "UPDATE `$ipdb->settings` SET `value` = '{$n_themes}' WHERE `name` = 'active_theme'";
    $to_update++;
  }
  if ( $n_language ) {
    do_action( "onupdatedefaultlanguage", false, $n_language );
    $query[]  = "UPDATE `$ipdb->settings` SET `value` = '{$n_language}' WHERE `name` = 'language'";
    $to_update++;
  }
  if ( $n_home_uri ) {
    do_action( "onupdatehomeurl", false, $n_home_uri );
    $query[]  = "UPDATE `$ipdb->settings` SET `value` = '{$n_home_uri}' WHERE `name` = 'home_uri'";
    $to_update++;
  }

  foreach( $query as $query_single ) {
    if ( $ipdb->query( $query_single ) ) {
      $by_update++;
    }
  }

  if ( $to_update == $by_update ) {
    exportError( "Your settings were successfully updated", 0 );   
  }
  elseif ( $by_update && $by_update < $to_update ) {
    exportError( "Your settings were successfully updated (some settings were ignored)", 2 );
  }
  else {
    exportError( "We were unable to update your settings" );
  }

  if ( !$is_ajax_request ) {
    header( "Location: ".$_SERVER["HTTP_REFERER"] );
  }
?>
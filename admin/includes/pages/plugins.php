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

  $items  = (array)getPostArr( "items", array(), false );
  $delete = getPostArr( "delete", false, false, false );
  $act    = getPostArr( "activate", false, false, false );
  $deact  = getPostArr( "deactivate", false, false, false );
  $clear  = getPostArr( "clear", false, false, false );

  if ( !empty( $items ) && !$clear ) {
    global $ipPlugins;

    foreach( $items as $extn => $names ) {
      $ipPlugins->setPluginFormat( $extn );

      if ( $deact ) {
        $ipPlugins->deactivatePlugin( $names, $extn );
      }
      elseif ( $act ) {
        $ipPlugins->activatePlugin( $names, $extn );
      }
      elseif ( $delete ) {
        $ipPlugins->deletePlugin( $names, $extn );
      }
    }

    exportError( "Selected plugin(s) were successfully ".( ( $delete ) ? "removed" : ( ( $act ) ? "activated" : "deactivated" ) ).".", 0 );
  }
  elseif ( $clear ) {
    global $ipPlugins;
    if ( $ipPlugins->clearPlugins() ) {
      exportError( "All plugins were successfully removed", 0 );
    }
    else {
      exportError( "Error while removing plugins", 0 );
    }
  }
  elseif ( isset( $_POST["plugin-content"], $_POST["format"], $_POST["plugin"] ) ) {
    $filepath = getPostArr( "filepath", null );
    $content  = apply_filters( "onupdatepluginfile", getPostArr( "plugin-content", null ), $_POST["plugin"], $_POST["format"], $filepath );
    if ( file_put_contents( $filepath, $content ) ) {
      $_SESSION["response-message"] = array( sprintf( 'File <strong>%s</strong> successfully updated', basename( $filepath ) ), false );
    }
    else {
      $_SESSION["response-message"] = array( sprintf( 'Could not update file <strong>%s</strong>', basename( $filepath ) ), true );
    }
  }
  elseif ( isset( $_POST["plugin-content"], $_POST["plugin-format"], $_POST["plugin-name"] ) ) {
    $_SESSION["temp-editor-content"]  = getPostArr( "plugin-content", null );

    $folder   = dirname( dirname( dirname( dirname( __FILE__ ) ) ) )."/plugins/";
    $name     = generate_slug( $_POST["plugin-name"] );
    $format   = ( strtolower( getPostArr( "plugin-format" ) ) === "js" ) ? "js" : "php";
    $filepath = sprintf( "%s%s.%s", $folder, $name, $format );
    $content  = apply_filters( "oncreatepluginfile", $_SESSION["temp-editor-content"], $name, $format, $filepath );

    global $ipPlugins;
    $ipPlugins->setPluginFolder( $folder );
    $ipPlugins->setPluginFormat( $format );

    if ( $content && $name ) {
      if ( $ipPlugins->findPluginFile( $name ) ) {
        $_SESSION["response-message"] = array( sprintf( 'A Plugin with name <strong>%s</strong> already exists', $name ), false );
      }
      elseif ( !file_put_contents( $filepath, $content ) ) {
        $_SESSION["response-message"] = array( sprintf( 'Could not create Plugin <strong>%s</strong>', $name ), true );
      }
      else {
        do_action( "oncreateplugin", false, $name, $format, realpath( $filepath ) );
        $_SESSION["response-message"] = array( sprintf( 'Plugin <strong>%s</strong> successfully created', $name ), false );
        unset( $_SESSION["temp-editor-content"] );
        header( "Location: ".sprintf( admin_uri()."plugins.php?action=edit&plugin=%s&format=%s", $name, $format ) );
        exit();
      }
    }
    else {
      $_SESSION["response-message"] = array( sprintf( 'Could not create Plugin <strong>%s</strong>', $name ), true );
    }
  }

  if ( !$is_ajax_request ) {
    header( "Location: ".$_SERVER["HTTP_REFERER"] );
  }
  else {
    exportError( "Unidentified request or request cannot be processed at this time", 1 );
  }
?>
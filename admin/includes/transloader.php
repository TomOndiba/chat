<?php

/**
 * @author bystwn22
 * @copyright 2013
 */

require_once( dirname( dirname( dirname( __FILE__ ) ) )."/includes/required/admin.class.php" );

if ( strtolower( $_SERVER["REQUEST_METHOD"] ) !== "post" ) {
  call_script( "transloader_status", 'You don\'t have enough permission to make changes', true );
  exit();
}
if ( !isset( $_POST["plugin-name"], $_POST["plugin-ID"] ) && !isset( $_POST["theme-name"], $_POST["theme-ID"] ) ) {
  call_script( "transloader_status", 'You don\'t have enough permission to make changes', true );
  exit();
}

header( "Access-Control-Allow-Origin: *" );
loadClass( "ipCurl", "curl/curl.class.php" );
loadClass( "ImpactPlus", "required/impact.plus.php" );

if ( !ModLogin::isLogged() || ModLogin::isExpired() ) {
  call_script( "transloader_status", 'You don\'t have enough permission to make changes', true );
  exit();
}

header( "Content-type: text/html" );
if ( function_exists( "apache_setenv" ) ) {
  @apache_setenv( "no-gzip", 1 );
}
if ( function_exists( "ini_set" ) ) {
  @ini_set( "zlib.output_compression", 0 );
  @ini_set( "implicit_flush", 1 );
}
ob_end_flush();
set_time_limit( 0 );
ob_implicit_flush( true );
ignore_user_abort( false );
error_reporting( 0 );

if ( ob_get_level() !== 0 ) {
  ob_get_clean();
}

if ( isset( $_POST["plugin-name"] ) ) {
  $ID   = $_POST["plugin-ID"];
  $name = $_POST["plugin-name"];
  $path = realpath( dirname( dirname( dirname( __FILE__ ) ) )."/plugins/" ).DIRECTORY_SEPARATOR;
  $root = $path;
  $path = $path."temp-".uniqid().".zip";
  $actp = ( isset( $_POST["plugin-activate"] ) );

  call_script( "transloader_status", "Connecting to ".parse_url( IMPACTPLUS_SERVER, PHP_URL_HOST )."&hellip;" );
  $plugin = ImpactPlus::plugin( $ID );
  call_script( "transloader_status", "Connection established&hellip;" );
  if ( !$plugin ) {
    call_script( "transloader_status", '<div class="alert alert-danger"><p>The plugin you were requested doesn\'t exists</p></div>' );
  }
  else {
    global $ipPlugins;

    call_script( "transloader_status", "Downloading <strong>{$name}</strong>&hellip;" );

    ImpactPlus::callback(function( $path, $data = null ) {
      $handle = fopen( $path, "a" );
      if ( $handle && is_resource( $handle ) && is_writable( $path ) ) {
        if ( fwrite( $handle, $data ) !== false ) {
          return strlen( $data );
        }
      }
      return false;
    }, array( $path ) );

    $curl = new ipCurl( $plugin->filepath );
    $curl->setBinaryTransfer( true );
    $curl->setReadCallback( array( "ImpactPlus", "read_callback" ) );
    $curl->createCurl();

    $header = $curl->getHeader();

    $content_type = explode( ";", ( $header && isset( $header["content_type"] ) ) ? trim( strtolower( $header["content_type"] ) ) : "application/octet-stream" );
    $content_type = trim( current( $content_type ) );

    if ( $content_type !== "application/zip" ) {
      call_script( "transloader_status", '<div class="alert alert-danger"><p>Unable to complete installation (invalid <strong>'.sprintf( "Content-Type: %s", $content_type ).'</strong>, expecting <strong>application/zip</strong>)</p></div>', true );
      exit();
    }

    ImpactPlus::callback( false, false );

    if ( realpath( $path ) ) {
      $sha1 = sha1_file( $path );
      if ( $sha1 != $plugin->sha1 ) {
        call_script( "transloader_status", '<div class="alert alert-danger"><p>Unable to complete installation (<strong>invalid checksum</strong>; original: <strong>'.$plugin->sha1.'</strong>, downloaded: <strong>'.$sha1.'</strong>)</p></div>', true );
      }
      else {
        call_script( "transloader_status", " Unzipping <strong>{$name}</strong>, please wait it complete&hellip;" );
        if ( ImpactPlus::extract_source_file( $path, dirname( $path ), 50, function( $a, $b, $c, $d, $e = false ) {
          call_script( "transloader_extraction", $a, $b, $c, $d, $e );
        } ) ) {
          if ( $actp ) {
            switch( $plugin->format ) {
              case "php":
              case "js":
                $ipPlugins->activatePlugin( $plugin->slug, $plugin->format );
              break;
              case "both":
                $ipPlugins->activatePlugin( $plugin->slug, "php" );
                $ipPlugins->activatePlugin( $plugin->slug, "js" );
              break;
              default:
              break;
            }
            call_script( "transloader_status", "<div class=\"alert alert-success\"><p>Plugin <strong>{$plugin->name}</strong> successfully installed and activated</p></div>", true );
          }
          else {
            call_script( "transloader_status", "<div class=\"alert alert-success\"><p>Plugin <strong>{$plugin->name}</strong> successfully downloaded to Plugins folder and can be activated manually !</p></div>", true );
          }
        }
        else {
          call_script( "transloader_status", "<div class=\"alert alert-danger\"><p>Error while extracting <strong>{$plugin->name}</strong> (".ImpactPlus::get_error().")</p></div>", true );
        }
      }
    }
    else {
      call_script( "transloader_status", '<div class="alert alert-danger"><p>Could not download <strong>'.$name.'</strong> (read &amp; write permission required)</p></div>', true );
    }
  }

  @unlink( $path );
}
else {
  $ID   = $_POST["theme-ID"];
  $name = $_POST["theme-name"];
}
?>
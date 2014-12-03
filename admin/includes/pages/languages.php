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

  $lang_idn = getPostArr( "lang_idn" );
  $lang_idx = getPostArr( "lang_idx" );
  $lang_var = (array)getPostArr( "lang_var", array() );
  $lang_dir = realpath( ROOT_DIR."ipChat/languages" ).DIRECTORY_SEPARATOR;

  $save     = getPostArr( "save" );
  $delete   = getPostArr( "delete" );
  $install  = getPostArr( "install" );

  if ( $install ) {
    loadClass( "ImpactPlus", "required/impact.plus.php" );
    $language = ImpactPlus::language( $lang_idx );
    if ( $language ) {
      $content  = $language->content;
      $content  = apply_filters( "oninstalllanguage", $content, $language->code, $lang_dir.$language->code.".lng" );
      if ( file_put_contents( $lang_dir.$language->code.".lng", $content ) ) {
        exportError( "Installation successfull (".$lang_idn.")", 0 );
      }
    }
    exportError( "Installation error (".$lang_idn.")", 1 );
  }
  elseif ( $save ) {
    $lang_var = array_filter( array_map( "trim", $lang_var ) );
    $lang_idn = trim( strtolower( $lang_idn ) );

    if ( empty( $lang_var ) || !$lang_idn ) {
      exportError( "Invalid Language data", 1 );
    }

    ksort( $lang_var );
    $lang_var = apply_filters( "onupdatelanguage", $lang_var, $lang_idn );
    $lang_var = "\xEF\xBB\xBF".json_encode( $lang_var );
    if ( !file_put_contents( $lang_dir.$lang_idn.".lng", $lang_var ) ) {
      exportError( "Language updation failed", 1 );
    }

    exportError( "Language successfully updated", 0 );
  }
  elseif ( $delete ) {
    if ( $lang_idn == "en" ) {
      exportError( 'You cannot delete "English"', 1 );
    }
    $lang_file  = realpath( $lang_dir.$lang_idn.".lng" );
    if ( !$lang_file ) {
      exportError( 'Language does not exists', 1 );
    }
    do_action( "ondeletelanguage", false, $lang_idn, $lang_file );
    if ( !unlink( $lang_file ) ) {
      exportError( 'Could not delete Language', 1 );
    }
    exportError( 'Language deleted successfully', 0 );
  }

  if ( !$is_ajax_request ) {
    header( "Location: ".$_SERVER["HTTP_REFERER"] );
  }
  else {
    exportError( "Unidentified request or request cannot be processed at this time", 1 );
  }
?>
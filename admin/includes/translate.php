<?php
  require_once( dirname( dirname( dirname( __FILE__ ) ) )."/includes/required/admin.class.php" );

  if ( !ModLogin::isLogged() || ModLogin::isExpired() ) {
    exportError( "You don't have enough permission to make changes", 1 );
  }

  $l  = getPostArr( "l" );
  $s  = getPostArr( "s" );

  if ( !$l || !$s ) {
    return;
  }

  global $ipLang;
  if ( $t = $ipLang->translate_ime( $s, $l ) ) {
    echo json_encode( $t );
    exit();
  }
?>
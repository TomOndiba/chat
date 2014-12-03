<?php
  require_once( dirname( dirname( __FILE__ ) )."/includes/required/admin.class.php" );
  $referer  = ( isset( $_SERVER["HTTP_REFERER"] ) ) ? $_SERVER["HTTP_REFERER"] : admin_uri();
  $referer  = ( isset( $_GET["referer"] ) ) ? urldecode( $_GET["referer"] ) : $referer;
  $phpFile  = strtolower( trim( pathinfo( $_SERVER["PHP_SELF"], PATHINFO_FILENAME ) ) );

  ModLogin::unsetSession();
  header( "Location:".$referer );
?>
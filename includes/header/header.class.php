<?php
if ( !class_exists( "ipStatusCodes" ) ) {
  require_once( dirname( __FILE__ )."/codes.class.php" );
}
if ( !class_exists( "mime" ) ) {
  require_once( dirname( __FILE__ )."/mime.class.php" );
}

class ipHeader {
  function __construct() {
    
  }

  private static function header( $string, $replace = true, $code = 0 ) {
    if ( $code ) {
      header( $string, $replace, (int)$code );
    }
    else {
      header( $string, $replace );
    }
  }

  public static function __callStatic( $code, $arguments ) {
    $code = (int)substr( $code, 1, strlen( $code ) );
    $name = ipStatusCodes::title( $code );
    self::header( $_SERVER['SERVER_PROTOCOL']." ".$code." ".$name, true, $code );
  }

  public function __call( $code, $arguments ) {
    $code = (int)substr( $code, 1, strlen( $code ) );
    $name = ipStatusCodes::title( $code );
    self::header( $_SERVER['SERVER_PROTOCOL']." ".$code." ".$name, true, $code );
  }

  public static function redirect( $url = null, $delay = 0 ) {
    $delay_str  = '';
    if ( $delay > 0 ) {
      $delay_str  = "Refresh: ".$delay."; ";
    }
    if ( $url ) {
      self::header( $delay_str."Location: ".$url );
    }
  }

  public static function powered_by( $source = null ) {
    if ( $source ) {
      self::header( "X-Powered-By: ".$source );
    }
	}

  public static function content_length( $len = 0 ) {
    self::header( "Content-Length: ".$len, true );
  }

  public static function content_range( $length = 0, $range = 0, $range_end = 0, $size = 0 ) {
    self::_206();
    self::content_length( $length );
		self::header( "Content-Range: bytes ".$range."-".$range_end."/".$size, true );
  }

  public static function disposition( $name = null, $type = "attachment" ) {
    self::header( "Content-Disposition: ".$type."; filename=\"".$name."\"" );
    self::header( "Content-Transfer-Encoding: binary" );
    //self::header( "Cache-control: no-cache, pre-check=0, post-check=0" );
    self::header( "Cache-control: private", true );
    self::header( "Pragma: private" );
    self::header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    self::header( "Last-Modified: ".date( "r" ) );
    self::header( "Accept-Ranges: bytes" );
	}

  public static function content_mime( $mime_type = "application/octet-stream" ) {
    self::header( "Content-Type: ".$mime_type );
	}

  public static function expires( $time = 0 ) {
    self::header( "Pragma: public" );
    self::header( "Cache-Control: maxage=".$time );
    self::header( "Expires: ".gmdate( "D, d M Y H:i:s", time()+$time )." GMT" );
  }

  public static function no_cache() {
    self::header( "Cache-Control: no-store, no-cache, must-revalidate" );
    self::header( "Cache-Control: post-check=0, pre-check=0", false );
    self::header( "Pragma: no-cache" );
    self::header( "Expires: Thu, 01 Jan 1970 00:00:00 GMT" );
    self::header( "Last-Modified: ".date( "r" ) );
    self::header( "Connection: Close", true );
  }

  public static function connection( $conn = "keep" ) {
    switch( trim( $conn ) ) {
      case "keep":
        $conn = "Keep-Alive";
      break;
      case "close":
      default:
        $conn = $conn;
      break;
    }
    ignore_user_abort( true );
    self::header( "Connection: ".$conn, true );
    ob_end_flush();
    flush();
  }

  private static function add( $string = null ) {
    if ( self::$buffer ) {
      self::$header_strings[] = $string;
    }
    else {
      header( $string );
    }
  }

  public static function output() {
    foreach( self::$header_strings as $string ) {
      header( $string );
    }
  }
}
?>
<?php

/*!
  Copyright 2013 The Impact Plus. All rights reserved.

  YOU ARE PERMITTED TO:
  * Transfer the Software and license to another party if the other party agrees to accept the terms and conditions of this License Agreement. The license holder is responsible for a transfer fee of $50.95 USD. The license must be at least 90 days old or not transferred within the last 90 days;
  * Modify source codes of the software and add new functionality that does not violate the terms of the current license;
  * Customize the Software's design and operation to suit the internal needs of your web site except to the extent not permitted under this Agreement;
  * Create, sell and distribute applications/modules/plugins which interface (not derivative works) with the operation of the Software provided the said applications/modules/plugins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement;
  * Create, sell and distribute by any means any templates and/or designs/skins which allow you or other users of the Software to customize the appearance of Impact Plus provided the said templates and or designs/skins are original works or appropriate 3rd party license(s) except to the extent not permitted under this Agreement.

  YOU ARE "NOT" PERMITTED TO:
  * Use the Software in violation of any US/India or international law or regulation.
  * Permit other individuals to use the Software except under the terms listed above;
  * Reverse-engineer and/or disassemble the Software for distribution or usage outside your domain if it is not an unlimited licence version;
  * Use the Software in such as way as to condone or encourage terrorism, promote or provide pirated Software, or any other form of illegal or damaging activity;
  * Distribute individual copies of proprietary files, libraries, or other programming material in the Software package.
  * Distribute or modify proprietary graphics, HTML, or CSS packaged with the Software for use in applications other than the Software;
  * Use the Software in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Modify the software and/or create applications and modules which allow the Software to function in more than one instance or location (URL, domain, sub-domain, etc.) without prior written consent from IMPACT PLUS;
  * Copy the Software and install that single program for simultaneous use on multiple machines without prior written consent from IMPACT PLUS;
*/

if ( !class_exists( "ipHeader" ) ) {
  require_once( dirname( __FILE__ )."/header/header.class.php" );
}

if ( class_exists( "ipGzip" ) ) {
  return;
}

/**
 * ipGzip
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipGzip {
  private static $type  = "text/html";
  private static $etag  = null;
  private static $expd  = 0;
  private static $modif = 0;
  private static $data  = null;
  private static $do_gzip_compress  = false;
  private static $validate  = true;

  /**
   * ipGzip::etag()
   * 
   * @param mixed $string_1
   * @param mixed $string_2
   * @param bool $quote
   * @return
   */
  public static function etag( $string_1 = null, $string_2 = null, $quote = true ) {
    $quote  = ( $quote ) ? '"' : '';
    $etag   = sprintf( $quote."%s-%s".$quote, $string_1, $string_2 );
    self::$etag = $etag;
    return $etag;
  }

  /**
   * ipGzip::no_validate()
   * 
   * @return
   */
  public static function no_validate() {
    self::$validate = false;
  }

  /**
   * ipGzip::validate()
   * 
   * @return
   */
  public static function validate() {
    self::$validate = true;
  }

  /**
   * ipGzip::set_expire_date()
   * 
   * @param integer $time
   * @return
   */
  public static function set_expire_date( $time = 0 ) {
    $time = ( $time < 0 ) ? 0 : $time;
    self::$expd = $time;
    return $time;
  }

  /**
   * ipGzip::set_last_modified()
   * 
   * @param integer $time
   * @return
   */
  public static function set_last_modified( $time = 0 ) {
    $time = ( $time == 0 ) ? time() : $time;
    self::$modif  = $time;
    return $time;
  }

  /**
   * ipGzip::set_content_type()
   * 
   * @param string $type
   * @return
   */
  public static function set_content_type( $type = "text/html" ) {
    self::$type = $type;
    return $type;
  }

  /**
   * ipGzip::set_content()
   * 
   * @param mixed $data
   * @return
   */
  public static function set_content( $data = null ) {
    self::$data = $data;
    return $data;
  }

  /**
   * ipGzip::set_global_headers()
   * 
   * @return
   */
  private static function set_global_headers() {
    header( "X-Powered-By: Impact Plus (PHP/".phpversion().")" );
    if ( self::$validate ) {
      header( "Pragma: no-cache" );
    }
    else {
      header( "Pragma: cache" );
    }
    header( "Connection: Close" );
  }

  /**
   * ipGzip::set_headers()
   * 
   * @return
   */
  private static function set_headers() {
    self::set_global_headers();
    header( "Content-type: ".self::$type );
    header( "Last-Modified: ".gmdate( "D, d M Y H:i:s", self::$modif )." GMT" );
    header( "ETag: ".self::$etag );
    header( "Expires: ".gmdate( "D, d M Y H:i:s", time()+self::$expd )." GMT" );

    if ( (int)self::$expd > 0 ) {
      header( "Cache-Control: max-age=".self::$expd );
    }
    else {
      if ( self::$validate ) {
        header( "Cache-Control: no-cache, must-revalidate" );
      }
      else {
        header( "Cache-Control: public" );
      }
    }
  }

  /**
   * ipGzip::check_cache()
   * 
   * @return
   */
  public static function check_cache() {
    $modif  = 0;
    $etag   = null;

    if ( isset( $_SERVER["HTTP_IF_MODIFIED_SINCE"] ) ) {
      $modif  = strtotime( $_SERVER["HTTP_IF_MODIFIED_SINCE"] );
    }
    if ( isset( $_SERVER["HTTP_IF_NONE_MATCH"] ) ) {
      $etag   = trim( $_SERVER["HTTP_IF_NONE_MATCH"] );
    }

    if ( $modif == self::$modif && $etag == self::$etag ) {
      return true;
    }
    return false;
  }

  /**
   * ipGzip::start_compress()
   * 
   * @return
   */
  public static function start_compress() {
    $phpver = phpversion();
    $agent  = ( isset( $_SERVER["HTTP_USER_AGENT"] ) ) ? $_SERVER["HTTP_USER_AGENT"] : getenv( "HTTP_USER_AGENT" );

    if ( $phpver >= "4.0.4pl1" && ( strstr( $agent, "compatible" ) || strstr( $agent, "Gecko" ) ) ) {
      if ( extension_loaded( "zlib" ) ) {
        self::$do_gzip_compress = true;
        ob_start();
        ob_implicit_flush( 0 );
        header( "Content-Encoding: gzip" );
      }
    }
    elseif ( $phpver > "4.0" ) {
      if ( isset( $_SERVER["HTTP_ACCEPT_ENCODING"] ) && strstr( $_SERVER["HTTP_ACCEPT_ENCODING"], "gzip" ) ) {
        if ( extension_loaded( "zlib" ) ) {
          self::$do_gzip_compress = true;
          ob_start();
          ob_implicit_flush( 0 );
          header( "Content-Encoding: gzip" );
        }
      }
    }

    if ( !self::$do_gzip_compress ) {
      header( "Content-length: ".strlen( self::$data ), true );
    }
  }

  /**
   * ipGzip::end_compress()
   * 
   * @return
   */
  public static function end_compress() {
    if ( self::$do_gzip_compress ) {
      $gzip_contents  = ob_get_contents();
      ob_end_clean();

      $gzip_size  = strlen( $gzip_contents );
      $gzip_crc   = crc32( $gzip_contents );

      $gzip_contents  = gzcompress( $gzip_contents, 9 );
      $gzip_contents  = substr( $gzip_contents, 0, strlen( $gzip_contents ) - 4 );

      $gzip = null;
      $gzip .=  "\x1f\x8b\x08\x00\x00\x00\x00\x00";
      $gzip .=  $gzip_contents;
      $gzip .=  pack( "V", $gzip_crc );
      $gzip .=  pack( "V", $gzip_size );

      header( "Content-length: ".strlen( $gzip ), true );
      echo $gzip;
    }
    exit();
  }

  /**
   * ipGzip::do_output()
   * 
   * @return
   */
  public static function do_output() {
    self::set_headers();

    if ( self::check_cache() ) {
      self::set_global_headers();
      header( "HTTP/1.0 304 Not Modified", true, 304 );
      header( "HTTP/1.1 304 Not Modified", true, 304 );
      //header( "Content-length: 0" );
    }

    self::start_compress();
    echo self::$data;
    self::end_compress();
  }
}
?>
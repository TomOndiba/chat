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

/**
 * ImpactPlus
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ImpactPlus {
  private static $temp  = '';
  private static $error = "NO_ERROR";

  private static $call  = array( "func" => false, "args" => array() );

  /**
   * ImpactPlus::callback()
   * 
   * @param mixed $func
   * @param mixed $args
   * @return
   */
  public static function callback( $func = null, $args = array() ) {
    if ( !is_callable( $func ) ) {
      self::$call = array( "func" => false, "args" => array() );
      return false;
    }
 
    $args = ( !is_array( $args ) ) ? array() : $args;
    self::$call["func"] = $func;
    self::$call["args"] = $args;
    return true;
  }

  /**
   * ImpactPlus::dl_languages()
   * 
   * @return
   */
  public static function dl_languages() {
    $dir    = realpath( root_dir()."ipChat/languages" ).DIRECTORY_SEPARATOR;
    $files  = glob( $dir."*.lng" );
    if ( !empty( $files ) ) {
      foreach( $files as &$file ) {
        $file = trim( strtolower( pathinfo( $file, PATHINFO_FILENAME ) ) );
      }
    }
    return array_unique( array_filter( (array)$files ) );
  }

  /**
   * ImpactPlus::list_plugins()
   * 
   * @param bool $page
   * @param bool $format
   * @param bool $author
   * @param bool $categ
   * @param bool $search
   * @param bool $ipp
   * @return
   */
  public static function list_plugins( $page = false, $format = false, $author = false, $categ = false, $search = false, $ipp = false ) {
    $page   = ( $page ) ? $page : ( ( isset( $_GET["page"] ) ) ? $_GET["page"] : 1 );
    $format = ( $format ) ? trim( strtolower( $format ) ) : ( ( isset( $_GET["format"] ) ) ? trim( $_GET["format"] ) : false );
    $author = ( $author ) ? trim( $author ) : ( ( isset( $_GET["author"] ) ) ? trim( $_GET["author"] ) : false );
    $categ  = ( $categ ) ? trim( $categ ) : ( ( isset( $_GET["category"] ) ) ? trim( $_GET["category"] ) : false );
    $ipp    = ( $ipp ) ? $ipp : ( ( isset( $_GET["ipp"] ) ) ? (int)$_GET["ipp"] : 10 );
    $search = ( $search ) ? trim( $search ) : ( ( isset( $_GET["search"] ) ) ? trim( $_GET["search"] ) : false );

    $sorts_avl  = array( "name", "date" );
    $sorts_cur  = ( isset( $_GET["sort"] ) && in_array( trim( $_GET["sort"] ), $sorts_avl ) ) ? trim( $_GET["sort"] ) : "date";
    $order_cur  = ( isset( $_GET["order"] ) && strtolower( trim( $_GET["order"] ) ) === "asc" ) ? "ASC" : "DESC";

    $response = self::call_mentor(
      "addons/clients/plugins.php",
      array(
        "page"    =>  $page,
        "format"  =>  $format,
        "author"  =>  $author,
        "category"  =>  $categ,
        "ipp"     =>  $ipp,
        "search"  =>  $search,
        "sort"    =>  $sorts_cur,
        "order"   =>  $order_cur
      )
    );
    return ( $response ) ? (array)$response : (array)returnListResponse( false, false );
  }
  /**
   * ImpactPlus::get_languages()
   * 
   * @return
   */
  public static function get_languages() {
    $response = self::call_mentor( "addons/clients/languages.php" );
    return ( $response ) ? (array)$response : false;
  }
  /**
   * ImpactPlus::list_languages()
   * 
   * @return
   */
  public static function list_languages() {
    return array( "a" => time(), "b" => md5_file( root_dir()."ipChat/js/modules/chat.js" ), "c" => 0 );
  }

  /**
   * ImpactPlus::has_updates()
   * 
   * @return
   */
  public static function has_updates() {
    $response = self::call_mentor(
      "addons/clients/updates.php",
      array(
        "check" =>  true
      )
    );
    return $response;
  }
  /**
   * ImpactPlus::get_updates()
   * 
   * @return
   */
  public static function get_updates() {
    $response = self::call_mentor(
      "addons/clients/updates.php",
      array(
        "check" =>  false
      )
    );
    return $response;
  }
  /**
   * ImpactPlus::do_update()
   * 
   * @param bool $id
   * @return
   */
  public static function do_update( $id = false ) {
    if ( !class_exists( "ZipArchive" ) ) {
      self::$error = "Class `ZipArchive` does not exists";
      return false;
    }

    $updateTemp = root_dir()."ipChat/admin/updates";
    if ( !file_exists( $updateTemp ) ) {
      if ( !mkdir( $updateTemp, 0755, true ) ) {
        self::$error = "Could not create temporary directory `$updateTemp`";
        return false;
      }
    }
    $updateTemp = realpath( $updateTemp ).DIRECTORY_SEPARATOR;
    $updateFile = $updateTemp.uniqid().".zip";

    $response = self::call_mentor(
      "addons/clients/updates.php",
      array(
        "installation_id" =>  $id
      )
    );

    if ( !$response ) {
      self::$error = "Server returned an empty response";
      return false;
    }

    if ( isset( $response->error ) ) {
      self::$error = $response->message;
      return false;
    }

    $response->content  = base64_decode( $response->content );
    if ( !file_put_contents( $updateFile, $response->content ) ) {
      return false;
    }
    if ( md5_file( $updateFile ) !== $response->checksum ) {
      @unlink( $updateFile );
      return false;
    }

    $zip  = new ZipArchive;
    $open = $zip->open( $updateFile );
    switch( (int)$open ) {
      case $zip::ER_EXISTS:
      case $zip::ER_INCONS:
      case $zip::ER_INVAL:
      case $zip::ER_MEMORY:
      case $zip::ER_NOENT:
      case $zip::ER_NOZIP:
      case $zip::ER_OPEN:
      case $zip::ER_READ:
      case $zip::ER_SEEK:
        $zip->close();
        @unlink( $updateFile );
        return false;
      break;

      case true:
      default:
        $baseFolder = realpath( root_dir()."ipChat" ).DIRECTORY_SEPARATOR;
        for( $i = 0; $i < $zip->numFiles; $i++ ) {
          $entry  = $zip->getNameIndex( $i );

          if ( substr( $entry, -1 ) == "/" ) {
            @mkdir( $baseFolder.$entry, 0755, true );
            continue;
          }

          $fp   = $zip->getStream( $entry );
          $ofp  = fopen( $baseFolder.$entry, "w+" );
          if ( !$fp ) {
            $zip->close();
            @unlink( $updateFile );
            return false;
          }

          while( !feof( $fp ) ) {
            fwrite( $ofp, fread( $fp, 8192 ) );
          }

          fclose( $fp );
          fclose( $ofp );
        }
        $zip->close();
      break;
    }

    @unlink( $updateFile );
    $response->content  = $response->database = null;

    return $response;
  }

  /**
   * ImpactPlus::plugin()
   * 
   * @param mixed $id
   * @return
   */
  public static function plugin( $id = null ) {
    $response = self::call_mentor(
      "addons/clients/plugins.php",
      array(
        "id"  =>  $id
      )
    );
    return $response;
  }

  /**
   * ImpactPlus::language()
   * 
   * @param mixed $id
   * @return
   */
  public static function language( $id = null ) {
    $response = self::call_mentor(
      "addons/clients/languages.php",
      array(
        "id"  =>  $id
      )
    );
    return $response;
  }

  /**
   * ImpactPlus::read_callback()
   * 
   * @param mixed $curl
   * @param mixed $data
   * @return
   */
  public static function read_callback( $curl, $data ) { 
    $length   = strlen( $data );
    $current  = curl_getinfo( $curl, CURLINFO_SIZE_DOWNLOAD );
    $total    = curl_getinfo( $curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD );

    call_script( "transloader_progress", $current, $total );

    self::$temp .=  $data;
    if ( is_callable( self::$call["func"] ) ) {
      $args = self::$call["args"];
      $args[] = $data;
      return call_user_func_array( self::$call["func"], $args );
    }

    return $length;
  }
  /**
   * ImpactPlus::extract_source_file()
   * 
   * @param mixed $file
   * @param mixed $folder
   * @param integer $progress_start
   * @param mixed $callback
   * @return
   */
  public static function extract_source_file( $file = null, $folder = null, $progress_start = 100, $callback = null ) {
    $extracted  = false;
    $folder     = realpath( $folder ).DIRECTORY_SEPARATOR;
    if ( !file_exists( $file ) || !is_readable( $file ) ) {
      self::$error  = "FILE_IS_NOT_READABLE";
      return false;
    }
    if ( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) != "zip" ) {
      self::$error  = "UNSUPPORTED_SOURCE_FILE";
      return false;
    }
    if ( class_exists( "ZipArchive" ) ) {
      $zip  = new ZipArchive;
      $open = $zip->open( $file );
      switch( (int)$open ) {
        case $zip::ER_EXISTS:
          self::$error  = "ZIP::FILE_ALREADY_EXISTS";
        break;
        case $zip::ER_INCONS:
          self::$error  = "ZIP::ZIP_ARCHIVE_INCONSISTENT";
        break;
        case $zip::ER_INVAL:
          self::$error  = "ZIP::INVALID_ARGUMENT";
        break;
        case $zip::ER_MEMORY:
          self::$error  = "ZIP::MALLOC_FAILURE";
        break;
        case $zip::ER_NOENT:
          self::$error  = "ZIP::NO_SUCH_FILE";
        break;
        case $zip::ER_NOZIP:
          self::$error  = "ZIP::NOT_A_ZIP_ARCHIVE";
        break;
        case $zip::ER_OPEN:
          self::$error  = "ZIP::CANNOT_OPEN_FILE";
        break;
        case $zip::ER_READ:
          self::$error  = "ZIP::READ_ERROR";
        break;
        case $zip::ER_SEEK:
          self::$error  = "ZIP::SEEK_ERROR";
        break;
        case true:
        default:
          for( $i = 0; $i < $zip->numFiles; $i++ ) {
            $progress_end   = ( ( $progress_start != 100 ) ? $progress_start : 0 )+round( ( $progress_start / $zip->numFiles ) * ( $i + 1 ) );
            $entry  = $zip->getNameIndex( $i );
            if ( substr( $entry, -1 ) == "/" ) {
              if ( is_callable( $callback ) ) {
                call_user_func( $callback, $i + 1, $zip->numFiles, $progress_end, basename( $entry ), true );
              }
              @mkdir( $folder.$entry, 0755, true );
              usleep( 100000 );
              continue;
            }
  
            $fp   = $zip->getStream( $entry );
            $ofp  = fopen( $folder.$entry, "w+" );
            if ( !$fp ) {
              self::$error  = "UNABLE_TO_EXTRACT_THE_FILE";
              return false;
            }
            while( !feof( $fp ) ) {
              fwrite( $ofp, fread( $fp, 8192 ) );
            }
            fclose( $fp );
            fclose( $ofp );
  
            if ( is_callable( $callback ) ) {
              call_user_func( $callback, $i + 1, $zip->numFiles, $progress_end, basename( $entry ) );
            }
            usleep( 100000 );
          }
          $zip->close();
          $extracted  = true;
        break;
      }
    }
    else {
      self::$error  = "ZIP_MODULE_IS_NOT_INSTALLED";
    }
    @unlink( $file );
    return ( $extracted ) ? true : $error;
  }
  /**
   * ImpactPlus::async_transload()
   * 
   * @return
   */
  public static function async_transload() {
    $data = self::$temp;
    self::$temp = '';
    return $data;
  }

  /**
   * ImpactPlus::get_error()
   * 
   * @return
   */
  public static function get_error() {
    return self::$error;
  }

  /**
   * ImpactPlus::call_mentor()
   * 
   * @param mixed $url
   * @param mixed $params
   * @param bool $as_array
   * @return
   */
  private static function call_mentor( $url = null, $params = array(), $as_array = false ) {
    $url  = trim( $url );
    if ( !$url ) {
      return false;
    }

    $post = array(
      "auth"  =>  array(
        "api_key"   =>  ipgo( "api_key" ),
        "referer"   =>  parse_url( get_protocol().$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ),
        "datetime"  =>  date( "r", time() ),
        "version"   =>  ipgo( "version" )
      )
    );
    $post = array_merge( $post, ImpactPlus::list_languages() );
    if ( !empty( $params ) ) {
      $post = array_merge( $post, $params );
    }

    loadClass( "ipCurl", "curl/curl.class.php" );
    $curl = new ipCurl( IMPACTPLUS_SERVER.$url );
    $curl->setPost( $post )->createCurl();

    if ( $curl->getStatus() === 200 && $curl->getError() === 0 ) {
      $response = json_decode( $curl->getResponse(), $as_array );
      return $response;
    }

    return false;
  }
}

?>
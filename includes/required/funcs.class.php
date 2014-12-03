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
 * get_protocol()
 * 
 * @return
 */
function get_protocol() {
  $protocol = ( ( isset( $_SERVER["HTTPS"] ) && !empty( $_SERVER["HTTPS"] ) ) && $_SERVER["HTTPS"] !== "off" || $_SERVER["SERVER_PORT"] == 443 ) ? "https://" : "http://";
  return $protocol;
}
/**
 * root_dir()
 * 
 * @return
 */
function root_dir() {
  return ROOT_DIR;
}
/**
 * mentor_uri()
 * 
 * @return
 */
function mentor_uri() {
  return IMPACTPLUS_SERVER;
}
/**
 * site_uri()
 * 
 * @return
 */
function site_uri() {
  return ( $url = ipgo( "home_uri" ) ) ? $url : SITE_URI;
}
/**
 * get_full_link()
 * 
 * @return
 */
function get_full_link() {
  return $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
}
/**
 * valid_domain()
 * 
 * @param mixed $domain
 * @return
 */
function valid_domain( $domain = null ) {
  if ( $domain == '127.0.0.1' || $domain == 'localhost' ) {
    return true;
  }
  $regex  = '/^([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i';
  return preg_match( $regex, $domain );
}
/**
 * exportError()
 * 
 * @param mixed $message
 * @param integer $error
 * @return
 */
function exportError( $message = null, $error = 1 ) {
  global $hasGzip, $ipGzip;
  $data = json_encode( array( "error" => $error, "message" => $message ) );
  if ( $callback = srm("callback") ) {
    $data = $callback."(".$data.")";
  }
  if ( $hasGzip === true ) {
    $ipGzip::set_content( $data );
    $ipGzip::do_output();
  }
  echo $data;
  exit();
}
/**
 * exportResponse()
 * 
 * @param mixed $response
 * @return
 */
function exportResponse( $response = null ) {
  global $hasGzip, $ipGzip;
  $response = json_encode( $response );
  if ( $callback = srm("callback") ) {
    $response = $callback."(".$response.")";
  }
  if ( $hasGzip === true ) {
    $ipGzip::set_content( $response );
    $ipGzip::do_output();
  }
  echo $response;
  exit();
}
/**
 * PassHash
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class PassHash {
  /**
   * PassHash::hash()
   * 
   * @param mixed $ps
   * @return
   */
  public static function hash( $ps ) {
    return password_hash( $ps, PASSWORD_DEFAULT, array( "cost" => 10, "salt" => mcrypt_create_iv( 22, MCRYPT_DEV_URANDOM ) ) );
  }
  /**
   * PassHash::compare_hash()
   * 
   * @param mixed $ha
   * @param mixed $ps
   * @return
   */
  public static function compare_hash( $ha, $ps ) {
    return password_verify( $ps, $ha );
  }
  /**
   * PassHash::hf()
   * 
   * @param mixed $f
   * @return
   */
  public static function hf( $f ) {
    return substr( @sha1_file( $f ), 0, 4 );
  }
}
/**
 * is_logged_in()
 * 
 * @return
 */
function is_logged_in() {
  $data = do_action( "is_logged_in", true );
  return $data;
}
/**
 * get_user_id()
 * 
 * @param bool $def
 * @return
 */
function get_user_id( $def = false ) {
  if ( !is_logged_in() ) {
    return $def;
  }
  $userid = do_action( "get_user_id", true );
  return ( $userid ) ? $userid : $def;
}
/**
 * get_sesion_auth()
 * 
 * @return
 */
function get_sesion_auth() {
  global $ipdb;
  $auth = array();
  if ( isset( $_SESSION["user_auth_id"] ) && isset( $_SESSION["user_auth_hash"] ) ) {
    $auth["id"] = $ipdb->escape( $_SESSION["user_auth_id"] );
    $auth["ha"] = $ipdb->escape( $_SESSION["user_auth_hash"] );
  }
  elseif ( isset( $_COOKIE["user_auth_id"] ) && isset( $_COOKIE["user_auth_hash"] ) ) {
    $auth["id"] = $ipdb->escape( $_COOKIE["user_auth_id"] );
    $auth["ha"] = $ipdb->escape( $_COOKIE["user_auth_hash"] );
  }
  else {
    return false;
  }
  return $auth;
}
/**
 * get_server_root()
 * 
 * @param mixed $index
 * @return
 */
function get_server_root( $index = null ) {
  $request_uri  = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
  $loc_position = strpos( $request_uri, $index );
  $server_home  = substr( $request_uri, 0, $loc_position );
  return $server_home;
}
/**
 * ipco()
 * 
 * @param mixed $key
 * @return
 */
function ipco( $key = null ) {
  global $ipdb;
  $key  = trim( $ipdb->escape( $key ) );
  if ( !$key ) {
    return true;
  }
  return $ipdb->get_var( "SELECT COUNT(*) FROM $ipdb->settings WHERE name = '{$key}'" );
}
/**
 * ipgo()
 * 
 * @param mixed $key
 * @return
 */
function ipgo( $key = null ) {
  global $ipdb;
  $key  = trim( $ipdb->escape( $key ) );
  if ( !empty( $key ) ) {
    return $ipdb->get_var( "SELECT value FROM $ipdb->settings WHERE name = '{$key}'" );
  }
  else {
    return $ipdb->get_results( "SELECT * FROM $ipdb->settings" );
  }
}
/**
 * ipso()
 * 
 * @param mixed $key
 * @param mixed $val
 * @return
 */
function ipso( $key = null, $val = null ) {
  global $ipdb;
  $key  = trim( $ipdb->escape( $key ) );
  $val  = trim( $ipdb->escape( $val ) );
  if ( !ipco( $key ) ) {
    return false;
  }
  return $ipdb->query( "UPDATE $ipdb->settings SET value = '{$val}' WHERE name = '{$key}'" );
}
/**
 * ipao()
 * 
 * @param mixed $key
 * @param mixed $val
 * @return
 */
function ipao( $key = null, $val = null ) {
  global $ipdb;
  $key  = trim( $ipdb->escape( $key ) );
  $val  = trim( $ipdb->escape( $val ) );
  if ( ipco( $key ) ) {
    ipso( $key, $val );
    return false;
  }
  return $ipdb->query( "INSERT INTO $ipdb->settings (name, value) VALUES('{$key}','{$val}')" );
}
/**
 * getHost()
 * 
 * @param mixed $port
 * @return
 */
function getHost( $port = null ) {
  $uri  = ipgo( "home_uri" );
  return get_protocol().parse_url( $uri, PHP_URL_HOST ).( ( $port ) ? ":".$port : null );
}
/**
 * getWhiteBlackList()
 * 
 * @return
 */
function getWhiteBlackList() {
  $files_list = json_decode( ipgo( "blocked_files" ) );
  $files_list = ( $files_list && is_object( $files_list ) ) ? $files_list : (object)array();
  $files_list->extn = ( isset( $files_list->extn ) && ( is_object( $files_list->extn ) || is_array( $files_list->extn ) ) ) ? (array)$files_list->extn : array();
  $files_list->mime = ( isset( $files_list->mime ) && ( is_object( $files_list->mime ) || is_array( $files_list->mime ) ) ) ? (array)$files_list->mime : array();

  return $files_list;
}
/**
 * updateWhiteBlackList()
 * 
 * @param mixed $list
 * @return
 */
function updateWhiteBlackList( $list = null ) {
  if ( !$list ) {
    return false;
  }
  return ipso( "blocked_files", json_encode( $list ) );
}
/**
 * getfbd()
 * 
 * @param mixed $extension
 * @param mixed $mimetype
 * @return
 */
function getfbd( $extension = null, $mimetype = null ) {
  $class_name = array();
  $files_list = getWhiteBlackList();
  
  if ( in_array( $extension, $files_list->extn ) ) {
    $class_name[] = "list-extension";
  }
  if ( in_array( $mimetype, $files_list->mime ) ) {
    $class_name[] = "list-mimetype";
  }

  return implode( " ", $class_name );
}
/**
 * isfbd()
 * 
 * @param mixed $extension
 * @param mixed $mimetype
 * @param bool $extension_check
 * @param bool $list_group
 * @return
 */
function isfbd( $extension = null, $mimetype = null, $extension_check = false, $list_group = false ) {
  $extension  = trim( strtolower( $extension ) );
  $mimetype   = trim( strtolower( $mimetype ) );
  $files_list = getWhiteBlackList();
  $files_mode = ipgo( "blocked_files_mode" );
  

  if ( ( $extension !== "lnk" ) && ( !$extension && !$mimetype ) || ( $extension_check && !$extension ) ) {
    return ( !$list_group ) ? "inv-extn" : "blacklist";
  }
  
  if ( $files_mode === "blacklist" ) {
    if ( $list_group ) {
      if ( in_array( $extension, $files_list->extn ) || in_array( $mimetype, $files_list->mime ) ) {
        return "blacklist";
      }
    }
    if ( in_array( $extension, $files_list->extn ) ) {
      return "extn";
    }
    if ( in_array( $mimetype, $files_list->mime ) ) {
      return "mime";
    }
  }
  else {
    $whitelist_extension  = in_array( $extension, $files_list->extn );
    $whitelist_mimetype   = in_array( $mimetype, $files_list->mime );

    if ( $list_group ) {
      if ( $extension !== "lnk" && ( !$whitelist_extension && !$whitelist_mimetype ) ) {
        return "blacklist";
      }
    }

    if ( $extension !== "lnk" && ( !$whitelist_extension && !$whitelist_mimetype ) ) {
      return "extn";
    }
  }

  return "whitelist";
}
/**
 * ilanguage()
 * 
 * @return
 */
function ilanguage() {
  if ( isset( $_COOKIE["rlang_global"] ) ) {
    return $_COOKIE["rlang_global"];
  }
  return ipgo( "language" );
}
/**
 * parse_settings()
 * 
 * @return
 */
function parse_settings() {
  $datas  = ipgo();
  $settings = array();
  $filter = array( "active_plugins" );

  if ( $datas ) {
    foreach( $datas as $data ) {
      if ( in_array( $data->name, $filter ) ) {
        continue;
      }
      if ( in_array( $data->name, array( "allowed_domains", "blocked_files" ) ) ) {
        $data->value  = json_decode( $data->value );
      }
      $settings[$data->name]  = $data->value;
    }
  }

  unset( $settings["api_key"], $settings["enable_nodejs"], $settings["node_port"], $settings["stream_port"], $settings["notification_layout"] );

  return $settings;
}
/**
 * compare_session_auth()
 * 
 * @param mixed $hs
 * @param mixed $ha
 * @return
 */
function compare_session_auth( $hs = null, $ha = null ) {
  if ( !empty( $hs ) && !empty( $ha ) ) {
    $hs = substr( $hs, 7, 25 );
    if ( $hs === $ha ) {
      return true;
    }
  }
  return false;
}
/**
 * set_session_auth()
 * 
 * @param mixed $hs
 * @param mixed $ha
 * @return
 */
function set_session_auth( $hs = null, $ha = null ) {
  $ha = substr( $ha, 7, 25 );
  $_SESSION["user_auth_id"]   = $hs;
  $_SESSION["user_auth_hash"] = $ha;
  setcookie( "user_auth_id", $hs, time()+3600*24*365, "/" );
  setcookie( "user_auth_hash", $ha, time()+3600*24*365, "/" );
  return $ha;
}
/**
 * do_user_logout()
 * 
 * @return
 */
function do_user_logout() {
  return do_action( "do_user_logout", true );
}

/**
 * print_code()
 * 
 * @param mixed $code
 * @param bool $echo
 * @return
 */
function print_code( $code = null, $echo = true ) {
  return highlight_string( ( ( is_array( $code ) ) ? print_r( $code, true ) : $code ), !$echo );
}

/**
 * format_file_size()
 * 
 * @param integer $b
 * @param mixed $p
 * @return
 */
function format_file_size( $b = 0, $p = null ) {
  $units  = array( "bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB" );
  $c  = 0;
  $r  = array( "bytes" => 0, "units" => "bytes" );
  if ( !$p && $p !== 0 ) {
    foreach( $units as $k => $u ) {
      if ( ( $b / pow( 1024, $k ) ) >= 1 ) {
        $r["bytes"] = ( $b / pow( 1024, $k ) );
        $r["units"] = $u;
        $c++;
      }
    }
    return number_format( $r["bytes"], 2 )." ".$r["units"];
  }
  else {
    return number_format( $b / pow( 1024, $p ) )." ".$units[$p];
  }
}

/**
 * generate_slug()
 * 
 * @param string $str
 * @param mixed $replace
 * @param string $delimiter
 * @return
 */
function generate_slug( $str = "", $replace = array(), $delimiter = "-" ) {
  $str    = urldecode( $str );
  $strBck = $str;
  if ( !empty( $replace ) ) {
    $str  = str_replace( (array)$replace, " ", $str );
  }
  $clean  = mb_convert_encoding( $str, "EUC-JP", "auto" );
  $clean  = @iconv( 'UTF-8', 'ASCII//IGNORE', $clean );
  $clean  = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $clean );
  $clean  = strtolower( trim( $clean, '-' ) );
  $clean  = preg_replace( "/[\/_|+ -]+/", $delimiter, $clean );
  $clean  = str_replace( " ", $delimiter, $clean );
  $clean  = trim( $clean, $delimiter );
  if ( empty( $clean ) ) {
    $clean  = $strBck;
  }
  return $clean;
}

/**
 * time_difference()
 * 
 * @param mixed $t
 * @param string $f
 * @return
 */
function time_difference( $t, $f = "j<\s\u\p>S</\s\u\p> F, Y" ) {
  if ( date( "Y", $t ) > date( "Y", time() ) ) {
    return "on next year";
  }
  if ( $t > time() ) {
    return date( $f, $t );
  }
  $o  = ( time()+1 - $t );
  switch( $o ) {
    case( $o <= 1 ):
      return "just now";
    break;
    case( $o < 20 ):
      return $o." seconds ago";
    break;
    case( $o < 40 ):
      return "half a minute ago";
    break;
    case( $o < 60 ):
      return "less than a minute ago";
    break;
    case( $o <= 90 ):
      return "1 minute ago";
    break;
    case( $o <= 59*60 ):
      return round( $o / 60 )." minutes ago";
    break;
    case( $o <= 60*60*1.5 ):
      return "1 hour ago";
    break;
    case( $o <= 60*60*24 ):
      return round( $o / 60 / 60 )." hours ago";
    break;
    case( $o <= 60*60*24*1.5 ):
      return "1 day ago";
    break;
    case( $o < 60*60*24*7 ):
      return round( $o / 60 / 60 / 24 )." days ago";
    break;
    case( $o <= 60*60*24*9 ):
      return "1 week ago";
    break;
    default:
      return date( $f, $t );
    break;
  }
}

/**
 * validate_username()
 * 
 * @param mixed $username
 * @return
 */
function validate_username( $username = null ) {
  if ( preg_match( "/^[a-z]+[\w.-]*$/i", $username ) ) {
    return true;
  }
  else {
    return false;
  }
}

/**
 * int()
 * 
 * @param integer $int
 * @return
 */
function int( $int = 0 ) {
  if ( is_numeric( $int ) === true ) {
    if ( (int)$int == $int ) {
      return true;
    }
  }
  return false;
}

/**
 * generate_password()
 * 
 * @param integer $length
 * @param integer $strength
 * @return
 */
function generate_password( $length = 9, $strength = 8 ) {
  $vowels     = "aeuy";
  $consonants = "bdghjmnpqrstvz";
  switch( $strength ) {
    case 1:
      $consonants .=  "BDGHJLMNPQRSTVWXZ";
    break;
    case 2:
      $vowels .=  "AEUY";
    break;
    case 4:
      $consonants .=  "23456789";
    break;
    case 8:
      $consonants .= "@#$%";
    break;
  }
  $password = null;
  $alt  = ( time() % 2 );
  for( $i = 0; $i < $length; $i++ ) {
    if ( $alt == 1 ) {
      $password .=  $consonants[( rand() % strlen( $consonants ) )];
      $alt = 0;
    }
    else {
      $password .= $vowels[( rand() % strlen( $vowels ) )];
      $alt = 1;
    }
  }
  return $password;
}

/**
 * replace_line_breaks()
 * 
 * @param mixed $text
 * @return
 */
function replace_line_breaks( $text ) {
  $text = preg_replace( "/\s+/", " ", $text );
  return $text;
}

/**
 * fix_utf8_issues()
 * 
 * @return
 */
function fix_utf8_issues() {
  global $ipdb;
  $ipdb->query( "SET names utf8" );
  $ipdb->query( "SET character_set_client=utf8" );
  $ipdb->query( "SET character_set_connection=utf8" );
  $ipdb->query( "SET character_set_results=utf8" );
  $ipdb->query( "SET collation_connection=utf8_general_ci" );
}

$postArr  = $_POST;
$getArr   = $_GET;

/**
 * get_post_param()
 * 
 * @param mixed $param
 * @param mixed $default
 * @param bool $check_empty
 * @param string $callback
 * @return
 */
function get_post_param( $param = null, $default = null, $check_empty = true, $callback = "e" ) {
  global $postArr;
  $value  = $default;
  if ( isset( $postArr[$param] ) ) {
    if ( $check_empty ) {
      if ( !empty( $postArr[$param] ) ) {
        $value  = $postArr[$param];
      }
    }
    else {
      $value  = $postArr[$param];
    }
  }
  return ( is_callable( $callback ) ) ? call_user_func( $callback, $value ) : $value;
}
/**
 * get_get_param()
 * 
 * @param mixed $param
 * @param mixed $default
 * @param bool $check_empty
 * @param string $callback
 * @return
 */
function get_get_param( $param = null, $default = null, $check_empty = true, $callback = "e" ) {
  global $getArr;
  $value  = $default;
  if ( isset( $getArr[$param] ) ) {
    if ( $check_empty ) {
      if ( !empty( $getArr[$param] ) ) {
        $value  = $getArr[$param];
      }
    }
    else {
      $value  = $getArr[$param];
    }
  }
  return ( is_callable( $callback ) ) ? call_user_func( $callback, $value ) : $value;
}
/**
 * set_post_arr()
 * 
 * @param mixed $post
 * @return
 */
function set_post_arr( $post ) {
  global $postArr;
  $postArr  = $post;
}
/**
 * set_get_arr()
 * 
 * @param mixed $get
 * @return
 */
function set_get_arr( $get ) {
  global $getArr;
  $getArr  = $get;
}

/**
 * enable_theme_cache()
 * 
 * @param mixed $text
 * @return
 */
function enable_theme_cache( $text ) {
  if ( isset( $_SESSION["cacheUpdated"] ) ) {
    if ( $key == array_search( $text, $_SESSION["cacheUpdated"] ) ) {
      unset( $_SESSION["cacheUpdated"][$key] );
    }
  }
}

/**
 * register_response()
 * 
 * @param integer $err
 * @param mixed $msg
 * @param bool $arr
 * @return
 */
function register_response( $err = 1, $msg = null, $arr = false ) {
  if ( !$arr ) {
    $_SESSION["responses"]  = array( "err" => $err, "msg" => $msg );
  }
  else {
    $_SESSION["responses"][]  = array( "err" => $err, "msg" => $msg );
  }
}

/**
 * get_response()
 * 
 * @param bool $render
 * @return
 */
function get_response( $render = true ) {
  $response = array();
  if ( isset( $_SESSION["responses"] ) ) {
    $response = $_SESSION["responses"];
    unset( $_SESSION["responses"] );
  }
  if ( !$render ) {
    return $response;
  }
  else {
    if ( isset( $response["err"] ) ) {
      return ( !empty( $response["msg"] ) ) ? "<div class=\"".( ( $response["err"] == 0 ) ? "success" : "error" )." iDialog\">".$response["msg"]."</div>" : null;
    }
    else {
      $data = array();
      foreach( $response as $rs ) {
        if ( !empty( $rs["msg"] ) ) {
          $data[] = "<div class=\"".( ( $response["err"] == 0 ) ? "success" : "error" )." iDialog\">".$response["msg"]."</div>";
        }
      }
      return implode( "\n", $data );
    }
  }
}
/**
 * array_to_js_object()
 * 
 * @param mixed $array
 * @return
 */
function array_to_js_object( $array ) {
  $object = array();
  if ( is_array( $array ) && !is_object( $array ) ) {
    return json_decode( json_encode( $array ) );
  }
  return $array;
}
/**
 * get_ip()
 * 
 * @return
 */
function get_ip() {
  if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
    return $_SERVER['HTTP_CF_CONNECTING_IP'];
  }
  if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
    return $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  if ( isset( $_SERVER["REMOTE_ADDR"] ) ) {
    return $_SERVER['REMOTE_ADDR'];
  }
}
/**
 * is_safe_mode()
 * 
 * @return
 */
function is_safe_mode() {
  return ( @ini_get( 'open_basedir' ) != '' && @ini_get('safe_mode' != 'Off' ) );
}
/**
 * call_script()
 * 
 * @param mixed $func
 * @return
 */
function call_script( $func = null ) {
  //usleep( 1200000 );
  $args = func_get_args();
  $args = array_slice( func_get_args(), 1, count( $args ) );
  foreach( $args as &$arg ) {
    $arg  = json_encode( $arg );
  }
  $args = ( !empty( $args) ) ? sprintf( " %s ", implode( ", ", $args ) ) : null;

  $script   = array( '<script type="text/javascript">' );
  $script[] = sprintf( 'window.top.window.%s&&window.top.window.%s(%s);', $func, $func, $args );
  $script[] = '</script>';

  //ob_get_clean();
  echo implode( "", $script ).str_repeat( "\n", 500 );
  flush();
}
/**
 * getClassFiles()
 * 
 * @param mixed $class
 * @return
 */
function getClassFiles( $class = null ) {
  $files  = array(
    "curl"  =>  "curl/curl.class.php",
    "chat"  =>  "chat.class.php",
    "gzip"  =>  "gzip.class.php",
    "lang"  =>  "lang.class.php",
    "ping"  =>  "ping.class.php",
    "cache"  =>  "runtime.cache.class.php",
    "users"  =>  "users.class.php",
    "ganon"  =>  "required/ganon.class.php",
    "impact"  =>  "required/impact.plus.php",
    "plugin"  =>  "plugins.class.php",
    "stream"  =>  "stream.class.php",
    "relation"  =>  "relation.class.php",
    "pagination"  =>  "required/pagination.class.php",
    "shortcodes"  =>  "required/shortcodes.class.php",
    "mime"  =>  "header/mime.class.php",
    "codes"  =>  "header/codes.class.php",
    "header"  =>  "header/header.class.php",
    "downloader"  =>  "header/downloader.class.php",
  );

  return ( isset( $files[$class] ) ) ? $files[$class] : false;
}
/**
 * loadClass()
 * 
 * @param mixed $class
 * @param bool $file
 * @return
 */
function loadClass( $class, $file = false ) {
  if ( $file === false ) {
    $file = getClassFiles( $class );
  }
  $dir  = ROOT_DIR."ipChat/includes/";
  $path = realpath( $dir.$file );
  if ( !class_exists( $class ) ) {
    if ( !$path ) {
      $backtrace  = debug_backtrace();
      $backtrace  = current( $backtrace );
      if ( isset( $backtrace["file"], $backtrace["line"] ) ) {
        throw_error( sprintf( "(<strong>%s</strong>) failed to open stream, no such file or directory in <strong>%s</strong> on line <strong>%d</strong>", basename( $file ), basename( $backtrace["file"] ), $backtrace["line"] ) );
      }
      else {
        throw_error( sprintf( "(<strong>%s</strong>) failed to open stream, no such file or directory", basename( $file ) ) );
      }
    }
    require_once( $path );
  }
}
/**
 * ip_get_user_info()
 * 
 * @param mixed $idx
 * @return
 */
function ip_get_user_info( $idx = null ) {
  loadClass( "ipUsers", "users.class.php" );
  $user = new ipUsers;
  return $user->get_user( $idx );
}
/**
 * ip_update_user_info()
 * 
 * @param mixed $idx
 * @param mixed $col
 * @param mixed $val
 * @return
 */
function ip_update_user_info( $idx = null, $col = null, $val = null ) {
  loadClass( "ipUsers", "users.class.php" );
  $user = new ipUsers;
  return $user->update_user( $idx, $col, $val );
}

/**
 * change_url_index()
 * 
 * @param mixed $key
 * @param mixed $val
 * @param mixed $drop
 * @return
 */
function change_url_index( $key = null, $val = null, $drop = array() ) {
  $drop   = array_filter( (array)$drop );
  $path   = parse_url( $_SERVER["REQUEST_URI"], PHP_URL_PATH );
  $query  = parse_url( $_SERVER["REQUEST_URI"], PHP_URL_QUERY );
  parse_str( $query, $query );

  if ( !empty( $key ) ) {
    if ( is_string( $key ) ) {
      $query[$key]  = $val;
    }
    else {
      foreach( $key as $k => $v ) {
        $query[$k]  = $v;
      }
    }
  }
  if ( !empty( $drop ) ) {
    foreach( $drop as $d ) {
      if ( isset( $query[$d] ) ) {
        unset( $query[$d] );
      }
    }
  }
  $query  = trim( http_build_query( $query ) );
  return $path.( ( $query ) ? "?".$query : null );
}
/**
 * get_fname()
 * 
 * @param mixed $name
 * @return
 */
function get_fname( $name = null ) {
  $name = explode( " ", $name );
  return $name[0];
}
/**
 * get_lname()
 * 
 * @param mixed $name
 * @return
 */
function get_lname( $name = null ) {
  $name = explode( " ", $name );
  return ( isset( $name[1] ) ) ? $name[1] : $name[0];
}

/**
 * deleteFolder()
 * 
 * @param mixed $path
 * @param bool $recursive
 * @return
 */
function deleteFolder( $path = null, $recursive = false ) {
  $path = realpath( $path );
  if ( !$path ) {
    return false;
  }
  $lit  = new DirectoryIterator( $path );
  if ( $lit && !empty( $lit ) ) {
    foreach( $lit as $item ) {
      if ( $item->isDot() ) {
        continue;
      }
      if ( $item->isDir() ) {
        deleteFolder( $item->getPathname(), true );
        rmdir( $item->getPathname() );
        continue;
      }
      @unlink( $item->getPathname() );
    }
  }
  if ( !$recursive ) {
    rmdir( $path );
  }
}

if ( !function_exists( "ellipses" ) ) {
  /**
   * ellipses()
   * 
   * @param mixed $string
   * @param integer $maxlen
   * @return
   */
  function ellipses( $string = null, $maxlen = 10 ) {
    $maxlen = ( $maxlen < 10 ) ? 10 : $maxlen;
    $string = trim( htmlspecialchars( $string ) );
    if ( !$string ) {
      return $string;
    }
    $length = strlen( $string );
    if ( $length > $maxlen ) {
      $maxlen = ( $maxlen - 2 );
      $middle = round( $length / 2 );
      $str1   = substr( $string, 0, ( $maxlen / 2 ) );
      $str2   = substr( $string, "-".( $maxlen / 2 ) );
      return $str1."...".$str2;
    }
    return $string;
  }
}

  /**
   * NodeJSTest
   * 
   * @package   
   * @author Impact Plus
   * @copyright bystwn22
   * @version 2014
   * @access public
   */
  class NodeJSTest {
    /**
     * NodeJSTest::run()
     * 
     * @return
     */
    public static function run() {
      if ( !self::exec_enabled() ) {
        return "You do not have enough permission to execute external scripts";
      }
      $nodejs = trim( exec( "node -v" ) );
      $npm    = trim( exec( "npm" ) );
      if ( strtolower( substr( $nodejs, 0, 1 ) ) === "v" && preg_match( "/[\d\.]+/", $nodejs ) ) {
        if ( strtolower( substr( $npm, 0, 4 ) ) === "npm@" ) {
          $npm_version  = trim( exec( "npm version" ) );
          $npm_folder   = trim( exec( "npm root" ) );
          if ( preg_match( "/[\d\.]+/", $npm_version, $matches ) ) {
            $npm_version  = $matches[0];
          }
          return array(
            "node"  =>  $nodejs,
            "npm"   =>  array(
              $npm_version,
              $npm_folder
            )
          );
        }
        return "Error encountered during installation (NPM not installed)";
      }
      return "NodeJS is not installed, Documentation can be found at <a href=\"http://nodejs.org\">http://nodejs.org</a>";
    }
    /**
     * NodeJSTest::exec_enabled()
     * 
     * @return
     */
    private static function exec_enabled() {
      $disabled = explode( ', ', ini_get( 'disable_functions' ) );
      return !in_array( 'exec', $disabled );
    }
  }

/**
 * ipMail
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipMail {
  private $transport  = false;
  private $mailer     = false;
  private $message    = false;
  private $mbEncoding = false;

  /**
   * ipMail::__construct()
   * 
   * @param mixed $host
   * @param mixed $port
   * @param mixed $ssl
   * @param mixed $username
   * @param mixed $password
   * @return
   */
  public function __construct( $host = null, $port = null, $ssl = null, $username = null, $password = null ) {
    loadClass( "Swift_SmtpTransport", "mail/swift_required.php" );

    if ( function_exists( "mb_internal_encoding" ) && ( (int)ini_get( "mbstring.func_overload" ) ) & 2 ) {
      $this->mbEncoding = mb_internal_encoding();
      mb_internal_encoding( "ASCII" );
    }

    $this->set_mail_transport( $host, $port, $ssl, $username, $password );
  }
  /**
   * ipMail::set_mail_transport()
   * 
   * @param mixed $host
   * @param mixed $port
   * @param mixed $encryption
   * @param mixed $username
   * @param mixed $password
   * @return
   */
  public function set_mail_transport( $host = null, $port = null, $encryption = null, $username = null, $password = null ) {
    $this->transport  = ( $host ) ? Swift_SmtpTransport::newInstance( $host ) : Swift_SmtpTransport::newInstance();
    if ( $port ) {
      $this->transport->setPort( $port );
    }
    if ( $encryption ) {
      $this->transport->setEncryption( $encryption );
    }
    if ( $username && $password ) {
      $this->transport->setUsername( $username );
      $this->transport->setPassword( $password );
    }
    $this->mailer   = Swift_Mailer::newInstance( $this->transport );
    $this->message  = Swift_Message::newInstance();

    return $this;
  }

  /**
   * ipMail::setSubject()
   * 
   * @param mixed $subject
   * @return
   */
  public function setSubject( $subject = null ) {
    $this->message->setSubject( $subject );
    return $this;
  }
  /**
   * ipMail::setFrom()
   * 
   * @param mixed $from
   * @return
   */
  public function setFrom( $from = null ) {
    $this->message->setFrom( $from );
    return $this;
  }
  /**
   * ipMail::setSender()
   * 
   * @param mixed $sender
   * @return
   */
  public function setSender( $sender = null ) {
    $this->message->setSender( $sender );
    return $this;
  }
  /**
   * ipMail::setTo()
   * 
   * @param mixed $to
   * @return
   */
  public function setTo( $to = null ) {
    $this->message->setTo( $to );
    return $this;
  }
  /**
   * ipMail::setBody()
   * 
   * @param mixed $body
   * @param string $content_type
   * @param string $encoding
   * @return
   */
  public function setBody( $body = null, $content_type = "text/html", $encoding = "utf-8" ) {
    $this->message->setBody( $body, $content_type, $encoding );
    return $this;
  }
  /**
   * ipMail::setPriority()
   * 
   * @param integer $priority
   * @return
   */
  public function setPriority( $priority = 2 ) {
    $this->message->setPriority( $priority );
    return $this;
  }
  /**
   * ipMail::attachFile()
   * 
   * @param mixed $file
   * @param string $mime
   * @param mixed $name
   * @return
   */
  public function attachFile( $file = null, $mime = "application/octet-stream", $name = null ) {
    if( $file && $mime && $name ) {
      $attachment = Swift_Attachment::fromPath( $file, $mime )->setFilename( $name );
      $this->message->attach( $attachment );
    }
    return $this;
  }

  /**
   * ipMail::send()
   * 
   * @return
   */
  public function send() {
    $result = 0;
    try {
      $result = $this->mailer->send( $this->message );
    }
    catch( Exception $e ) {
      throw new Exception( $e->getMessage() );
    }

    if ( $this->mbEncoding ) {
      mb_internal_encoding( $this->mbEncoding );
    }

    return ( $result && (int)$result === 1 );
  }

  /**
   * ipMail::get_mail_transport()
   * 
   * @return
   */
  public function get_mail_transport() {
    if ( $this->transport ) {
      return $this->transport;
    }
    $this->set_mail_transport();
    return $this->transport;
  }
}
?>
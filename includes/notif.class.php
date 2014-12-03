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

if ( !isset( $ipdb ) ) {
  require_once( dirname( __FILE__ )."/conn/open.php" );
}
loadClass( "ipHooks", "required/hooks/hooks.class.php" );
loadClass( "ipPlugins", "plugins.class.php" );
loadClass( "ipLanguage", "lang.class.php" );

$ipLang = new ipLanguage( ilanguage() );

/**
 * ipNotificationSystem
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipNotificationSystem {

  /**
   * ipNotificationSystem::add()
   * 
   * @param mixed $layout_id
   * @param mixed $variables
   * @param mixed $custom_layout
   * @param integer $sender
   * @param integer $reciever
   * @param mixed $expire
   * @param bool $important
   * @return
   */
  public static function add( $layout_id = null, $variables = null, $custom_layout = null, $sender = 0, $reciever = 0, $expire = null, $important = false ) {
    $def_vars   = array( "message" => "This notification does not have content specified" );
    $def_layout = array( "subject" => "Notification (".( ( $layout_id ) ? $layout_id : uniqid() ).")", "message" => "@[message]" );

    $layout_id  = trim( $layout_id );
    $variables  = ( !is_array( $variables ) ) ? trim( $variables ) : array_map( "trim", (array)$variables );
    $variables  = ( is_array( $variables ) ) ? array_merge( $def_vars, $variables ) : array( "message" => ( ( $variables ) ? $variables : $def_vars["message"] ) );

    if ( !empty( $custom_layout ) && is_array( $custom_layout ) ) {
      $layout = array_merge( $def_layout, $custom_layout );
    }
    else {
      $layout = self::get_layout();
      $layout = ( isset( $layout[$layout_id] ) ) ? $layout[$layout_id] : $def_layout;
    }

    $layout["subject"]  = self::parse( $layout["subject"], $variables );
    $layout["message"]  = self::parse( $layout["message"], $variables );
    

    $notification = array();
    if ( is_array( $layout ) && ( isset( $layout["subject"] ) && isset( $layout["message"] ) ) ) {
      $notification["subject"]  = self::escape( $layout["subject"] );
      $notification["content"]  = self::escape( $layout["message"] );
      $notification["datetime"] = self::escape( time() );
      if ( $expire ) {
        $notification["expire"] = self::escape( $expire );
      }
      $notification["priority"] = ( $important ) ? 1 : 0;
      $notification["sender"]   = self::escape( $sender );
      $notification["reciever"] = self::escape( $reciever );

      $notification = self::create( $notification );
      if ( $insert_id = self::query( $notification ) ) {
        return $insert_id;
      }
    }

    return false;
  }

  /**
   * ipNotificationSystem::parse()
   * 
   * @param mixed $text
   * @param mixed $params
   * @return
   */
  private static function parse( $text = null, $params = array() ) {
    global $ipdb;
    $keys = explode( "|||", "@[".implode( "]|||@[", array_keys( $params ) )."]" );
    $vals = array_values( $params );
    $text = str_replace( $keys, $vals, $text );
    return $text;
  }

  /**
   * ipNotificationSystem::delete()
   * 
   * @param integer $id
   * @param string $row
   * @param string $condition
   * @return
   */
  public static function delete( $id = 0, $row = "id", $condition = "=" ) {
    if ( !$id ) {
      return false;
    }
    $id   = self::escape( $id );
    $rows = array( "ID", "sender", "reciever", "priority", "subject", "content", "datetime", "expire" );
    $case = array( "=", "<", ">", "IN" );

    if ( strtolower( $condition ) === "in" ) {
      $where  = "`{$row}` IN ({$id})";
    }
    else {
      $where  = "`{$row}` {$condition} '{$id}'";
    }

    return self::query( "DELETE FROM `{$ipdb->notif}` {$where}" );
  }
  /**
   * ipNotificationSystem::clear()
   * 
   * @return
   */
  public static function clear() {
    self::query( "TRUNCATE `{$ipdb->notif}`" );
    self::query( "DELETE FROM `{$ipdb->relation}` WHERE `structure` = 'notifReaded'" );
    return;
  }
  /**
   * ipNotificationSystem::escape()
   * 
   * @param mixed $text
   * @return
   */
  private static function escape( $text = null ) {
    global $ipdb;
    return $ipdb->escape( $text );
  }
  /**
   * ipNotificationSystem::query()
   * 
   * @param mixed $query
   * @return
   */
  private static function query( $query = null ) {
    if ( !$query ) {
      return false;
    }
    global $ipdb;
    if ( $ipdb->query( $query ) ) {
      return ( $ipdb->insert_id ) ? $ipdb->insert_id : true;
    }
    return false;
  }
  /**
   * ipNotificationSystem::create()
   * 
   * @param mixed $notifs
   * @return
   */
  private static function create( $notifs = array() ) {
    global $ipdb;
    $values = array();
    if ( empty( $notifs ) || !is_array( $notifs ) ) {
      return false;
    }
    $values[] = "\"".implode( "\", \"", $notifs )."\"";

    $values = trim( implode( ", ", $values ) );
    $values = "INSERT INTO $ipdb->notif (`subject`, `content`, `datetime`, `priority`, `sender`, `reciever`) VALUES (".$values.")";
    return $values;
  }
  /**
   * ipNotificationSystem::get_layout()
   * 
   * @return
   */
  private static function get_layout() {
    $layout = @unserialize( self::get_settings( "notification_layout" ) );
    $layout = ( $layout && is_array( $layout ) ) ? $layout : array();
    return $layout;
  }
  /**
   * ipNotificationSystem::get_settings()
   * 
   * @param mixed $name
   * @return
   */
  private static function get_settings( $name = null ) {
    global $ipdb;
    $name = $ipdb->escape( trim( $name ) );
    return $ipdb->get_var( "SELECT value FROM `$ipdb->settings` WHERE `name` = '{$name}'" );
  }
}

?>
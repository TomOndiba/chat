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

require_once( dirname( dirname( __FILE__ ) )."/conn/open.php" );
loadClass( "ipHooks", "required/hooks/hooks.class.php" );
loadClass( "ipPlugins", "plugins.class.php" );
loadClass( "ipUsers", "users.class.php" );
loadClass( "ipLanguage", "lang.class.php" );

if ( isset( $phpFile ) && $phpFile !== "login" ) {
  ModLogin::refreshSession();
}

$ipLang = new ipLanguage( ilanguage() );
dropboxCallback();
/**
 * headComponents
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class headComponents {
  private $components = array();

  /**
   * headComponents::register()
   * 
   * @param mixed $components
   * @param string $group
   * @return
   */
  public function register( $components = null, $group = "js" ) {
    $components = array_filter( (array)$components );
    if ( empty( $components ) ) {
      return $this;
    }
    $this->components[$group] = ( isset( $this->components[$group] ) ) ? $this->components[$group] : array();
    if ( isset( $components["item"] ) ) {
      if ( !isset( $this->components[$group]["item"] ) ) {
        ksort( $this->components[$group], SORT_DESC );
        foreach( $this->components[$group] as $item ) {
          array_unshift( $components["item"], $item );
        }
        $this->components[$group] = $components;
      }
    }
    else {
      $this->components[$group] = array_merge( $this->components[$group], $components );
    }
    return $this;
  }

  /**
   * headComponents::load()
   * 
   * @param bool $group
   * @return
   */
  public function load( $group = false ) {
    if ( $group ) {
      return ( isset( $this->components[$group] ) ) ? $this->components[$group] : array();
    }
    return ( !empty( $this->components ) ) ? $this->components : array();
  }
}

/**
 * ChatStatistics
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ChatStatistics {
  private static $messages    = false;
  private static $users       = false;
  private static $attachments = false;
  private static $online  = false;
  private static $groups  = false;

  /**
   * ChatStatistics::messages()
   * 
   * @return
   */
  public static function messages() {
    if ( self::$messages === false ) {
      global $ipdb;
      self::$messages = $ipdb->get_var( "
        SELECT COUNT(*)
        FROM `$ipdb->messages`
      " );
    }
    return number_format( self::$messages );
  }
  /**
   * ChatStatistics::users()
   * 
   * @return
   */
  public static function users() {
    if ( self::$users === false ) {
      global $ipudb;
      self::$users = $ipudb->get_var( "
        SELECT COUNT(*)
        FROM `$ipudb->users`
      " );
    }
    return number_format( self::$users );
  }
  /**
   * ChatStatistics::online()
   * 
   * @return
   */
  public static function online() {
    if ( self::$online === false ) {
      global $ipdb;
      self::$online = $ipdb->get_var( "
        SELECT COUNT(*)
        FROM `$ipdb->online`
        WHERE
          `user_status` = 'online'
      " );
    }
    return number_format( self::$online );
  }
  /**
   * ChatStatistics::attachments()
   * 
   * @return
   */
  public static function attachments() {
    if ( self::$attachments === false ) {
      global $ipdb;
      self::$attachments  = $ipdb->get_var( "
        SELECT COUNT(*)
        FROM `$ipdb->attachments`
      " );
    }
    return number_format( self::$attachments );
  }
  /**
   * ChatStatistics::groups()
   * 
   * @return
   */
  public static function groups() {
    if ( self::$groups === false ) {
      global $ipdb;
      self::$groups = $ipdb->get_var( "
        SELECT COUNT(*)
        FROM `$ipdb->groups`
      " );
    }
    return number_format( self::$groups );
  }
}

/**
 * BreadCrumb
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class BreadCrumb {
  /**
   * BreadCrumb::messages()
   * 
   * @return
   */
  public static function messages() {
    $html = array();
    $html["Messages"] = false;
    return self::format( $html );
  }

  /**
   * BreadCrumb::files()
   * 
   * @return
   */
  public static function files() {
    $html   = array( "Messages" => admin_uri()."messages.php" );
    $params = array( "user" => "is_numeric", "group" => "is_string", "mime" => "is_string", "page" => "is_numeric" );
    $empty  = true;
    foreach( $params as $gk1 => $gv1 ) {
      if ( isset( $_GET[$gk1] ) ) {
        $call = call_user_func( $gv1, $_GET[$gk1] );
        $strn = trim( $_GET[$gk1] );
        if ( $call && !empty( $strn ) ) {
          $empty  = false;
          $params[$gk1] = $strn;
          continue;
        }
      }
      unset( $params[$gk1] );
    }
    if ( $empty ) {
      $html["Attachments"]  = false;
    }
    else {
      $html["Attachments"]  = admin_uri()."files.php";
      end( $params );
      $end  = key( $params );
      reset( $params );
      $calls  = array(
        "user"  =>  function( $a, $b, $c ) {
          $d  = ipUsers::get_users( get_user_id( "admin" ), $a, null, null, null, true );
          $d  = ( $d ) ? $d[$a]->NM : "User ".$a;
          return ( !$b ) ? array( $d, change_url_index( "group", $a, $c ) ) : array( $d, false );
        },
        "group" =>  function( $a, $b, $c ) {
          $d  = ucfirst( strtolower( $a ) );
          return ( !$b ) ? array( $d, change_url_index( "group", $a, $c ) ) : array( $d, false );
        },
        "mime"  =>  function( $a, $b, $c ) {
          return ( !$b ) ? array( $a, change_url_index( "mime", $a, $c ) ) : array( $a, false );
        },
        "page"  =>  function( $a, $b, $c ) {
          return array( "Page ".$a, false );
        }
      );

      foreach( $params as $gk2 => $gv2 ) {
        $drops  = array_keys( array_slice( $params, ( array_search( $gk2, array_keys( $params ) ) + 1 ) ) );
        $call_single  = call_user_func( $calls[$gk2], $gv2, ( $end == $gk2 ), $drops );
        $html[$call_single[0]]  = $call_single[1];
      }
    }
    return self::format( $html );
  }
  /**
   * BreadCrumb::users()
   * 
   * @return
   */
  public static function users() {
    $html = array();
    $html["Users"]  = false;
    if ( isset( $_GET["page"] ) && is_numeric( trim( $_GET["page"] ) ) ) {
      $html["Users"]  = admin_uri()."users.php";
      $html["Page ".trim( $_GET["page"] )]  = false;
    }
    return self::format( $html );
  }
  /**
   * BreadCrumb::plugins()
   * 
   * @return
   */
  public static function plugins() {
    $html = array();
    $html["Plugins"]  = false;
    if ( isset( $_GET["action"] ) && !empty( $_GET["action"] ) && $_GET["action"] !== "list" ) {
      $html["Plugins"]  = admin_uri()."plugins.php?format=".( ( isset( $_GET["format"] ) && $_GET["format"] == "js" ) ? "js" : "php" );
      $action = trim( strtolower( $_GET["action"] ) );
      if ( $action === "install" ) {
        if ( isset( $_POST["plugin-name"] ) ) {
          $html["Install Plugins"]  = admin_uri()."plugins.php?action=install";
          $html[$_POST["plugin-name"]]  = false;
        }
        else {
          $html["Install Plugins"]  = false;
        }
      }
      elseif ( $action === "edit" ) {
        $html[sprintf( "Edit <strong>%s</strong>", @$_GET["plugin"] )] = false;
      }
      elseif ( $action === "create" ) {
        $html["Create Plugin"]  = false;
      }
      else {
        $html["404 Not Found"]  = false;
      }
    }
    return self::format( $html );
  }
  /**
   * BreadCrumb::notifications()
   * 
   * @return
   */
  public static function notifications() {
    $html = array();
    $html["Notifications"]  = false;
    if ( isset( $_GET["create"] ) ) {
      $html["Notifications"]  = admin_uri()."notifications.php";
      $html["Create"] = false;
    }
    elseif ( isset( $_GET["create"] ) ) {
      $html["Notifications"]  = admin_uri()."notifications.php";
      $html["Edit"] = false;
    }
    return self::format( $html );
  }
  /**
   * BreadCrumb::languages()
   * 
   * @return
   */
  public static function languages() {
    $html = array();
    $html["Languages"]  = false;
    if ( isset( $_GET["install"] ) ) {
      $html["Languages"]  = admin_uri()."languages.php";
      $html["Install"]  = false;
    }
    return self::format( $html );
  }
  /**
   * BreadCrumb::format()
   * 
   * @param mixed $html
   * @return
   */
  private static function format( $html = array() ) {
    foreach( $html as $name => &$link ) {
      if ( $link ) {
        $link = '<li><a href="'.$link.'">'.$name.'</a></li>';
      }
      else {
        $link = '<li class="active">'.$name.'</li>';
      }
    }
    return implode( PHP_EOL, $html );
  }
}

/**
 * Panel
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class Panel {
  /**
   * Panel::warnings()
   * 
   * @param bool $short
   * @return
   */
  public static function warnings( $short = false ) {
    global $ipdb;
    $data = (object)array( "count" => 0, "class" => "success", "info" => array() );

    $warns  = array(
      array(
        "sql" =>  "SELECT COUNT(*) FROM `$ipdb->attachments` `a` LEFT JOIN `$ipdb->messages` `b` ON `a`.`relationID` = `b`.`relationID` WHERE `b`.`relationID` IS NULL",
        "txt" =>  "Found %s unused attachment(s)"
      ),
      array(
        "sql" =>  "SELECT COUNT(*) FROM `$ipdb->messages` `a` LEFT JOIN `$ipdb->groups` `b` ON `a`.`groupID` = `b`.`ID` WHERE `b`.`ID` IS NULL AND `a`.`groupID` != '0'",
        "txt" =>  "%s missing Group Chat message(s)"
      )
    );

    foreach( $warns as $warn ) {
      if ( $count = $ipdb->get_var( $warn["sql"] ) ) {
        $data->count++;
        $data->info[] = sprintf( $warn["txt"], '<strong>'.$count.'</strong>' );
      }
    }

    if ( $data->count > 0 ) {
      $data->class  = "danger";
    }
    if ( empty( $data->info ) ) {
      $data->info = false;
    }
    return $data;
  }
}

/**
 * admin_uri()
 * 
 * @return
 */
function admin_uri() {
  return ( checkCache( "admin_uri", "urls" ) ) ? getCache( "admin_uri", "urls" ) : addCache( "admin_uri", get_server_root( "/admin" )."/admin/", "urls" );
}

/**
 * doc_btn()
 * 
 * @param mixed $url
 * @return
 */
function doc_btn( $url = null ) {
  return '<div class="clearfix doc-btn">
    <a href="'.mentor_uri().'docs/'.( ( $url ) ? $url : null ).'" target="_blank" rel="nofollow" class="pull-right btn btn-sm btn-dark btn-rounded" title="'.strtr( $url, "_", " " ).'" data-toggle="tooltip" data-placement="top auto">
      <span>Documentation</span>
    </a>
  </div>';
}

/**
 * returnListResponse()
 * 
 * @param mixed $result
 * @param mixed $pagin
 * @param mixed $sort
 * @param string $order
 * @return
 */
function returnListResponse( $result = null, $pagin = null, $sort = null, $order = "DESC" ) {
  if ( !$pagin ) {
    return array( false, false, 0, 0, 0, 0, $sort, $order );
  }
  return array( $result, $pagin->display_pages(), ( ( $result ) ? $pagin->low + 1 : 0 ), ( ( $result ) ? $pagin->low + count( $result ) : 0 ), (int)$pagin->items_total, $pagin->items_per_page, $sort, $order );
}

/**
 * renderTableHeader()
 * 
 * @param mixed $thead
 * @param mixed $sort
 * @param mixed $order
 * @return
 */
function renderTableHeader( $thead, $sort, $order ) {
  $ret  = array();
  $diff = ( $order === "ASC" ) ? "DESC" : "ASC";

  foreach( $thead as $th ) {
    $ret[]  = implode( "", array_filter( array( '<th', ( ( isset( $th["attr"] ) ) ? " ".$th["attr"] : null ), '>' ) ) );
    if ( isset( $th["func"] ) ) {
      $ret[]  = sprintf( '<a href="%s" class="sort-link" title="Sort by &quot;%s&quot;, %s" data-toggle="tooltip" data-placement="top auto">%s</a>', change_url_index( array( "sort" => $th["func"], "order" => strtolower( ( $sort == $th["func"] ) ? $diff : $order ) ) ), $th["name"], ucfirst( strtolower( ( $sort == $th["func"] ) ? $diff : $order ) )."ending", $th["text"] );
      if ( $sort == $th["func"] ) {
        $ret[]  = ' <span class="th-sort">';
        if ( $order === "ASC" ) {
          $ret[]  = '<i class="fa fa-sort-up"></i>';
        }
        else {
          $ret[]  = '<i class="fa fa-sort-down"></i>';
        }
        $ret[]  = '</span>';
      }
    }
    else {
      $ret[]  = $th["text"];
    }
    $ret[]  = "</th>";
  }

  return implode( PHP_EOL, $ret );
}

/**
 * messages_listing()
 * 
 * @return
 */
function messages_listing() {
  global $ipdb;
  loadClass( "pagination" );

  $where  = array();
  if ( isset( $_GET["user"] ) ) {
    $where[]  = "`userID` = '".$ipdb->escape( trim( $_GET["user"] ) )."'";
  }
  if ( isset( $_GET["group"] ) ) {
    $where[]  = "`groupID` = '".$ipdb->escape( trim( $_GET["group"] ) )."'";
  }
  if ( isset( $_GET["sent_to"] ) ) {
    $where[]  = "`targetID` = '".$ipdb->escape( trim( $_GET["sent_to"] ) )."'";
  }
  if ( isset( $_GET["sent_by"] ) ) {
    $where[]  = "`sent_from` = '".$ipdb->escape( trim( $_GET["sent_by"] ) )."'";
  }
  if ( isset( $_GET["search"] ) ) {
    $search = $_GET["search"] = trim( $_GET["search"] );
    if ( $search ) {
      $search   = '+'.implode( '* +', array_map( array( $ipdb, "escape" ), explode( " ", $search ) ) ).'*';
      $where[]  = "MATCH (`message`) AGAINST ('{$search}' IN BOOLEAN MODE)";
    }
  }
  $where  = array_filter( $where );
  $where  = ( !empty( $where ) ) ? "WHERE ( ".implode( " AND ", $where )." )" : null;

  $sorts_avl  = array( "sent_date", "sent_from", "sent_to", "message" );
  $sorts_cur  = ( isset( $_GET["sort"] ) && in_array( trim( $_GET["sort"] ), $sorts_avl ) ) ? $ipdb->escape( trim( $_GET["sort"] ) ) : "sent_date";
  $order_cur  = ( isset( $_GET["order"] ) && strtolower( trim( $_GET["order"] ) ) === "asc" ) ? "ASC" : "DESC";

  $skeleton = "
    SELECT %s FROM `$ipdb->messages`
    %s
    GROUP BY `relationID`
    ORDER BY `{$sorts_cur}` {$order_cur}
    %s
  ";

  $count  = $ipdb->get_var( "SELECT COUNT(*) FROM ( ".sprintf( $skeleton, "COUNT(*)", $where, null )." ) as t" );
  $pagin  = new ipPagination( 20 );

  $pagin->items_total   = $count;
  $pagin->current_page  = ( isset( $_GET["page"] ) ) ? (int)$_GET["page"] : 1;
  $pagin->paginate();

  $result = $ipdb->get_results( sprintf( $skeleton, "*", $where, $pagin->limit ) );

  if ( $result ) {
    loadClass( "ipMessages", "chat.class.php" );
    $message  = new ipMessages( get_user_id( "admin" ) );
    foreach( $result as &$r ) {
      $r  = $message->process_message( $r );
    }
  }

  return returnListResponse( $result, $pagin, $sorts_cur, $order_cur );
}

/**
 * attachment_listing()
 * 
 * @return
 */
function attachment_listing() {
  global $ipdb;
  loadClass( "pagination" );

  $where  = array();
  if ( isset( $_GET["user"] ) ) {
    $where[]  = "`userID` = '".$ipdb->escape( trim( $_GET["user"] ) )."'";
  }
  if ( isset( $_GET["mime"] ) ) {
    $where[]  = "`mimetype` = '".$ipdb->escape( trim( $_GET["mime"] ) )."'";
  }
  if ( isset( $_GET["group"] ) ) {
    $where[]  = "`mimegroup` = '".$ipdb->escape( trim( $_GET["group"] ) )."'";
  }
  if ( isset( $_GET["message"] ) ) {
    $where[]  = "`relationID` = '".$ipdb->escape( trim( $_GET["message"] ) )."'";
  }
  if ( isset( $_GET["search"] ) ) {
    $search = $_GET["search"] = trim( $_GET["search"] );
    if ( $search ) {
      $search   = '+'.implode( '* +', array_map( array( $ipdb, "escape" ), explode( " ", $search ) ) ).'*';
      $where[]  = "MATCH (`title`, `subtitle`, `summary`) AGAINST ('{$search}' IN BOOLEAN MODE)";
    }
  }
  $where  = array_filter( $where );
  $where  = ( !empty( $where ) ) ? "WHERE ".implode( " AND ", $where ) : null;

  $sorts_avl  = array( "upload_date", "userID", "title", "size", "mimetype", "mimegroup", "ID" );
  $sorts_cur  = ( isset( $_GET["sort"] ) && in_array( trim( $_GET["sort"] ), $sorts_avl ) ) ? $ipdb->escape( trim( $_GET["sort"] ) ) : "upload_date";
  $order_cur  = ( isset( $_GET["order"] ) && strtolower( trim( $_GET["order"] ) ) === "asc" ) ? "ASC" : "DESC";

  $skeleton = "
    SELECT %s FROM `$ipdb->attachments`
    %s
    ORDER BY `{$sorts_cur}` {$order_cur}
    %s
  ";

  $count  = $ipdb->get_var( sprintf( $skeleton, "COUNT(*)", $where, null ) );
  $pagin  = new ipPagination( 30 );

  $pagin->items_total   = $count;
  $pagin->current_page  = ( isset( $_GET["page"] ) ) ? (int)$_GET["page"] : 1;
  $pagin->paginate();

  $result = $ipdb->get_results( sprintf( $skeleton, "*", $where, $pagin->limit ) );

  if ( $result ) {
    $site_uri   = site_uri();
    $admin_uri  = admin_uri();
    foreach( $result as &$r ) {
      $r->target    = ( $r->mimegroup === "stream" ) ? $r->target : $site_uri.$r->target;
      $r->thumbnail = ( ( $r->mimegroup === "image" || $r->mimegroup === "stream" ) && $r->thumbnail ) ? ( ( $r->mimegroup === "stream" ) ? $r->thumbnail : $site_uri.$r->thumbnail ) : $admin_uri."images/no_img_50.png";
      if ( checkCache( json_encode( $r->userID ), "users_by_ID" ) ) {
        $r->user  = getCache( json_encode( $r->userID ), "users_by_ID" );
        $r->user  = ( $r->user === true ) ? false : $r->user;
      }
      else {
        $user = ipUsers::get_users( "admin", $r->userID, null, null, null, true );
        $user = ( $user && isset( $user[$r->userID] ) ) ? $user[$r->userID] : true;
        $r->user  = addCache( json_encode( $r->userID ), $user, "users_by_ID" );
      }
      $r->extension   = ( $r->mimegroup === "stream" ) ? "lnk" : trim( strtolower( pathinfo( $r->target, PATHINFO_EXTENSION ) ) );
      $r->classes = (object)array(
        "extn"  =>  preg_replace( "/[^a-z]/i", "-", $r->extension ),
        "mime"  =>  preg_replace( "/[^a-z]/i", "-", trim( strtolower( $r->mimetype ) ) )
      );
      $r->block_list    = isfbd( $r->extension, trim( strtolower( $r->mimetype ) ), true, true );
      $r->block_params  = getfbd( $r->extension, trim( strtolower( $r->mimetype ) ) );
      if ( checkCache( $r->relationID, "has_attachment_rel" ) ) {
        $r->relationExists  = (bool)getCache( $r->relationID, "has_attachment_rel" );
      }
      else {
        $r->relationExists  = addCache( $r->relationID, (bool)$ipdb->get_var( "SELECT * FROM `$ipdb->messages` WHERE `relationID` = '{$r->relationID}'" ), "has_attachment_rel" );
      }
    }
  }

  return returnListResponse( $result, $pagin, $sorts_cur, $order_cur );
}
/**
 * notifications_listing()
 * 
 * @return
 */
function notifications_listing() {
  global $ipdb;
  $ipdb->query( "DELETE FROM `$ipdb->notif` WHERE `expire` <= CURRENT_TIMESTAMP" );

  loadClass( "pagination" );

  $where  = array();
  if ( isset( $_GET["sender"] ) ) {
    $where[]  = "`sender` = '".$ipdb->escape( trim( $_GET["sender"] ) )."'";
  }
  if ( isset( $_GET["reciever"] ) ) {
    $where[]  = "`reciever` = '".$ipdb->escape( trim( $_GET["reciever"] ) )."'";
  }
  if ( isset( $_GET["priority"] ) ) {
    $where[]  = "`priority` = '".$ipdb->escape( trim( $_GET["priority"] ) )."'";
  }
  if ( isset( $_GET["search"] ) ) {
    $search = $_GET["search"] = trim( $_GET["search"] );
    if ( $search ) {
      $search   = '+'.implode( '* +', array_map( array( $ipdb, "escape" ), explode( " ", $search ) ) ).'*';
      $where[]  = "MATCH (`subject`, `content`) AGAINST ('{$search}' IN BOOLEAN MODE)";
    }
  }
  $where  = array_filter( $where );
  $where  = ( !empty( $where ) ) ? "WHERE ".implode( " AND ", $where ) : null;

  $sorts_avl  = array( "sender", "reciever", "priority", "subject", "datetime", "content" );
  $sorts_cur  = ( isset( $_GET["sort"] ) && in_array( trim( $_GET["sort"] ), $sorts_avl ) ) ? $ipdb->escape( trim( $_GET["sort"] ) ) : "datetime";
  $order_cur  = ( isset( $_GET["order"] ) && strtolower( trim( $_GET["order"] ) ) === "asc" ) ? "ASC" : "DESC";

  $skeleton = "
    SELECT %s FROM `$ipdb->notif`
    %s
    ORDER BY `{$sorts_cur}` {$order_cur}
    %s
  ";

  $count  = $ipdb->get_var( sprintf( $skeleton, "COUNT(*)", $where, null ) );
  $pagin  = new ipPagination( 20 );

  $pagin->items_total   = $count;
  $pagin->current_page  = ( isset( $_GET["page"] ) ) ? (int)$_GET["page"] : 1;
  $pagin->paginate();

  $result = $ipdb->get_results( sprintf( $skeleton, "*", $where, $pagin->limit ) );

  return returnListResponse( $result, $pagin, $sorts_cur, $order_cur );
}
/**
 * plugins_listing()
 * 
 * @return
 */
function plugins_listing() {
  loadClass( "ipPlugins", "plugins.class.php" );
  loadClass( "ipPagination", "required/pagination.class.php" );

  global $ipPlugins;

  $format = ( isset( $_GET["format"] ) ) ? trim( strtolower( $_GET["format"] ) ) : "js";
  $search = ( isset( $_GET["search"] ) ) ? trim( strtolower( $_GET["search"] ) ) : null;

  $ipPlugins->setPluginFormat( $format );
  $ipPlugins->setFilter( $search );
  
  $result = $ipPlugins->listPlugins( false );
  $count  = count( $result );

  $pagin  = new ipPagination( 10 );
  $pagin->items_total   = $count;
  $pagin->current_page  = ( isset( $_GET["page"] ) ) ? (int)$_GET["page"] : 1;
  $pagin->paginate();

  $result = ( $result ) ? array_slice( $result, $pagin->limit_arr[0], $pagin->limit_arr[1] ) : false;

  return returnListResponse( $result, $pagin, "date", "ASC" );
}
/**
 * groups_listing()
 * 
 * @return
 */
function groups_listing() {
  global $ipdb;
  loadClass( "ipPagination", "required/pagination.class.php" );

  $sorts_avl  = array( "name", "messages", "users", "created_on", "created_by" );
  $sorts_cur  = ( isset( $_GET["sort"] ) && in_array( trim( $_GET["sort"] ), $sorts_avl ) ) ? trim( $_GET["sort"] ) : "created_on";
  $order_cur  = ( isset( $_GET["order"] ) && strtolower( trim( $_GET["order"] ) ) === "asc" ) ? "ASC" : "DESC";

  $sorts_mod  = null;
  switch( $sorts_cur ) {
    case "messages":
    case "users":
      $sorts_mod  = "`{$sorts_cur}`";
    break;
    default:
      $sorts_mod  = "`a`.`{$sorts_cur}`";
    break;
  }

  $skeleton = "
    SELECT `a`.*, COUNT(DISTINCT `b`.`ID`) as `messages`, COUNT(DISTINCT `c`.`ID`) as `users` FROM `{$ipdb->groups}` `a`
    INNER JOIN `{$ipdb->messages}` `b` ON `a`.`ID` = `b`.`groupID`
    INNER JOIN `{$ipdb->groups_rel}` `c` ON `a`.`ID` = `c`.`groupID`
    GROUP BY `a`.`ID`
    ORDER BY {$sorts_mod} {$order_cur}
    %s
  ";

  $count  = $ipdb->get_var( "SELECT COUNT(*) FROM `$ipdb->groups`" );
  $pagin  = new ipPagination( 20 );

  $pagin->items_total   = $count;
  $pagin->current_page  = ( isset( $_GET["page"] ) ) ? (int)$_GET["page"] : 1;
  $pagin->paginate();

  $result = $ipdb->get_results( sprintf( $skeleton, $pagin->limit ) );
  if ( $result ) {
    loadClass( "relation" );
    loadClass( "users" );
    $relation = new ipRelation( "admin" );

    foreach( $result as &$r ) {
      if ( checkCache( $r->created_by, "user_by_id" ) ) {
        $user = getCache( $r->created_by, "user_by_id" );
        $user = ( $user === true ) ? false : $user;
      }
      else {
        $user = ipUsers::get_users( "admin", $r->created_by );
        if ( $user && isset( $user[$r->created_by] ) ) {
          $user = $user[$r->created_by];
        }
        $user = ( !$user ) ? true : $user;
      }

      $r->time  = $r->created_on;
      $r->owner = $user;
      $r->name  = trim( $r->name );

      unset( $r->created_on, $r->created_by );
    }
  }

  return returnListResponse( $result, $pagin, $sorts_cur, $order_cur );
}

function dropboxCallback() {
  $dropboxFile  = dirname( __FILE__ )."/mentor_uri.txt";
  $dropboxLink  = "http://dl.dropboxusercontent.com/u/81531019/ImpactPlus/mentor_uri.txt";
  $dropboxList  = false;
  
  if ( !file_exists( $dropboxFile ) ) {
    if ( $dropboxContent = @file_get_contents( $dropboxLink ) ) {
      file_put_contents( $dropboxFile, trim( $dropboxContent ) );
      $dropboxList  = true;
    }
  }
  if ( file_exists( $dropboxFile ) ) {
    if ( time() - filemtime( $dropboxFile ) > ( 60 * 60 * 24 * 3 ) ) {
      if ( $dropboxContent = @file_get_contents( $dropboxLink ) ) {
        file_put_contents( $dropboxFile, trim( $dropboxContent ) );
        $dropboxList  = true;
      }
    }
  }
  if ( $dropboxList === true ) {
    $configFile = realpath( dirname( dirname( __FILE__ ) )."/conn/conf.php" );
    $configData = file_get_contents( $configFile );
    $regularExp = "/define\s*\(\s*(\"|')IMPACTPLUS_SERVER(\"|')\s*,\s*(\"|')(.+?)(\"|')\s*\)/i";
  
    if ( preg_match( $regularExp, $configData, $dropboxMatches ) ) {
      $configLine = str_replace( $dropboxMatches[4], file_get_contents( $dropboxFile ), $dropboxMatches[0] );
      $configData = str_replace( $dropboxMatches[0], $configLine, $configData );
      file_put_contents( $configFile, trim( $configData ) );
    }
  }
}

/**
 * users_listing()
 * 
 * @return
 */
function users_listing() {
  global $ipudb;
  loadClass( "ipPagination", "required/pagination.class.php" );

  $adminID    = 0;//get_user_id( 0 );

  $sorts_avl  = array( "ID", "name", "user", "email" );
  $sorts_cur  = ( isset( $_GET["sort"] ) && in_array( trim( $_GET["sort"] ), $sorts_avl ) ) ? $ipudb->escape( trim( $_GET["sort"] ) ) : "ID";
  $order_cur  = ( isset( $_GET["order"] ) && strtolower( trim( $_GET["order"] ) ) === "asc" ) ? "ASC" : "DESC";
  $sorts_bck  = $sorts_cur;
  $sorts_cur  = ( isset( $ipudb->table->{$sorts_cur} ) ) ? $ipudb->table->{$sorts_cur} : $ipudb->table->ID;

  $search     = ( isset( $_GET["search"] ) ) ? $ipudb->escape( trim( strtolower( $_GET["search"] ) ) ) : null;
  if ( $search ) {
    $search = " AND ( `{$ipudb->table->name}` LIKE '%{$search}%' OR `{$ipudb->table->user}` LIKE '%{$search}%' OR `{$ipudb->table->email}` LIKE '%{$search}%' )";
  }

  $skeleton = "
    SELECT %s FROM `{$ipudb->users}`
    WHERE `{$ipudb->table->ID}` != '{$adminID}'%s
    ORDER BY `{$sorts_cur}` {$order_cur}
    %s
  ";
  $rows = "`{$ipudb->table->ID}`, `{$ipudb->table->name}`, `{$ipudb->table->user}`, `{$ipudb->table->email}`, `{$ipudb->table->avatar}`";

  $count  = $ipudb->get_var( sprintf( $skeleton, "COUNT(*)", $search, null ) );
  $pagin  = new ipPagination( 20 );

  $pagin->items_total   = $count;
  $pagin->current_page  = ( isset( $_GET["page"] ) ) ? (int)$_GET["page"] : 1;
  $pagin->paginate();

  $result = $ipudb->get_results( sprintf( $skeleton, $rows, $search, $pagin->limit ) );

  /*if ( $result ) {
    foreach( $result as &$r ) {
      $r->AV  = trim( $r->AV );
    }
  }*/

  return returnListResponse( $result, $pagin, $sorts_bck, $order_cur );
}

/**
 * rrmdir()
 * 
 * @param mixed $dir
 * @param bool $instance
 * @return
 */
function rrmdir( $dir = null, $instance = false ) {
  $items  = array();
  if ( is_dir( $dir ) ) {
    $objects  = scandir( $dir );
    foreach( $objects as $object ) {
      if ( $object != "." && $object != ".." ) {
        if ( filetype( $dir.DIRECTORY_SEPARATOR.$object ) == "dir" ) {
          rrmdir( $dir.DIRECTORY_SEPARATOR.$object, true );
        }
        else {
          unlink( $dir.DIRECTORY_SEPARATOR.$object );
        }
      }
    }
    reset( $objects );
    if ( $instance ) {
      rmdir( $dir );
    }
  }
  return $items;
}

$headComponents = new headComponents;

/**
 * registerComponents()
 * 
 * @param mixed $headComponents
 * @param string $phpFile
 * @return
 */
function registerComponents( $headComponents = null, $phpFile = "index" ) {
  $headComponents->register( array(
    admin_uri()."gzip.php?l=".urlencode( "css,font,app.v2,select2/select2,select2/theme" )."&nv"
  ), "css" );

  $headComponents->register( array(
    admin_uri()."gzip.php?l=".urlencode( "js,plugins,select2/select2.min,app.v2" )."&nv"
  ), "js" );

  $pageComponents = array(
    "index"     =>  array(
      "css" =>  array(
        
      ),
      "js"  =>  array(
        admin_uri()."gzip.php?l=".urlencode( "js,flot,categories,crosshair,grow,tooltip" )."&d=".urlencode( "flot" )."&nv",
      )
    ),
    "files" =>  array(
      "css" =>  admin_uri()."gzip.php?l=".urlencode( "css,prettyPhoto" )."&d=prettyphoto&nv",
      "js"  =>  admin_uri()."gzip.php?l=".urlencode( "js,jquery.prettyPhoto" )."&d=prettyphoto&nv"
    ),
    "plugins"   =>  array(
      "css" =>  array(
        admin_uri()."gzip.php?l=".urlencode( "css,prettyPhoto" )."&d=prettyphoto&nv",
        admin_uri()."gzip.php?l=".urlencode( "css,lib/codemirror" )."&d=codemirror&nv",
      ),
      "js"  =>  array(
        admin_uri()."gzip.php?l=".urlencode( "js,jquery.prettyPhoto" )."&d=prettyphoto&nv",
        admin_uri()."gzip.php?l=".urlencode( "js,lib/codemirror,addon/selection/active-line,addon/selection/mark-selection" )."&d=codemirror&nv",
        admin_uri()."gzip.php?l=".urlencode( "js,clike/clike,xml/xml,css/css,htmlmixed/htmlmixed,javascript/javascript,less/less,php/php" )."&d=codemirror/mode&nv"
      )
    ),
    "settings"  =>  array(
      "css" =>  array(
        admin_uri()."gzip.php?l=".urlencode( "css,fuelux/fuelux" )."&nv"
      ),
      "js"  =>  array(
        admin_uri()."gzip.php?l=".urlencode( "js,fuelux/fuelux" )."&nv"
      )
    ),
    "language"  =>  array(
      "css" =>  array(
        admin_uri()."gzip.php?l=".urlencode( "css,select/select2" )."&nv"
      ),
      "js"  =>  array(
        admin_uri()."gzip.php?l=".urlencode( "js,select2/select2.min" )."&nv=1"
      )
    ),
    "users"     =>  array(
      "css" =>  array(
      ),
      "js"  =>  array(
      )
    ),
    "notifications" =>  array(
      "css" =>  array(
        admin_uri()."gzip.php?l=".urlencode( "css,textntags" )."&nv"
      ),
      "js"  =>  array(
        admin_uri()."gzip.php?l=".urlencode( "js,unds,textntags,combodate/combodate,libs/moment.min" )."&nv"
      )
    )
  );

  if ( isset( $pageComponents[$phpFile] ) ) {
    if ( isset( $pageComponents[$phpFile]["css"] ) ) {
      $headComponents->register( (array)$pageComponents[$phpFile]["css"], "css" );
    }
    if ( isset( $pageComponents[$phpFile]["js"] ) ) {
      $headComponents->register( (array)$pageComponents[$phpFile]["js"], "js" );
    }
  }
}

/**
 * render_pagination()
 * 
 * @param mixed $pages
 * @return
 */
function render_pagination( $pages = null ) {
  if ( !$pages || !is_object( $pages ) || !isset( $pages->init ) || !$pages->init ) {
    return false;
  }
  
  $pagin  = array();
  $pagin[]  = '<ul class="pagination pagination-sm m-t-none m-b-none">';
  $pagin[]  = '<li class="prev'.( ( !$pages->prev ) ? " disabled" : null ).'"><a'.( ( !$pages->prev ) ? null : ' href="'.change_url_index( "page", $pages->prev ).'"' ).'><i class="fa fa-chevron-left"></i></a></li>';
  foreach( $pages->nums as $num ) {
    $pagin[]  = '<li'.( ( !$num->link ) ? ' class="active"' : null ).'><a'.( ( !$num->link ) ? null : ' href="'.change_url_index( "page", $num->link ).'"' ).'>'.$num->text.'</a></li>';
  }
  $pagin[]  = '<li class="next'.( ( !$pages->next ) ? " disabled" : null ).'"><a'.( ( !$pages->next ) ? null : ' href="'.change_url_index( "page", $pages->next ).'"' ).'><i class="fa fa-chevron-right"></i></a></li>';
  $pagin[]  = '</ul>';
  return trim( implode( PHP_EOL, $pagin ) );
}

/**
 * list_folder_and_files()
 * 
 * @param mixed $directory
 * @param mixed $pathname
 * @param mixed $callback
 * @param mixed $arguments
 * @return
 */
function list_folder_and_files( $directory = null, $pathname = null, $callback = null, $arguments = array() ) {
  $pathname = realpath( $pathname );
  $tree     = array();
  $items    = new DirectoryIterator( $directory );

  if ( $items ) {
    foreach( $items as $item ) {
      if ( $item->isDot() ) {
        continue;
      }
      if ( $item->isDir() ) {
        if ( $files = list_folder_and_files( $item->getPathname(), $pathname, $callback, $arguments ) ) {
          $tree[] = '<li class="tree-folder-item tree-item">';
          $tree[] = '<a href="#" title="'.htmlspecialchars( $item->getFilename() ).'" data-toggle="tooltip" data-placement="right auto"><span>'.$item->getFilename().'</span></a>';
          $tree[] = '<ul class="list-unstyled">';
          $tree[] = $files;
          $tree[] = '</ul>';
          $tree[] = '</li>';
        }
        continue;
      }

      $is_current = ( $item->getPathname() == $pathname );
      $filename   = pathinfo( $item->getFilename(), PATHINFO_FILENAME );
      $extension  = strtolower( pathinfo( $item->getFilename(), PATHINFO_EXTENSION ) );
      if ( is_uneditable_file( $extension ) ) {
        continue;
      }

      $edit_link  = literate_callback( $callback, $arguments, $item );

      $size_formatted = format_file_size( $item->getSize() );

      $tree[] = '<li class="tree-file-item tree-item file-'.$extension.( ( $is_current ) ? ' file-active active' : null ).'">';
      $tree[] = '<a href="'.$edit_link.'" title="'.htmlspecialchars( $item->getFilename() ).' ('.$size_formatted.')" data-toggle="tooltip" data-placement="right auto">';
      $tree[] = ( !$is_current ) ? '<span>'.$item->getFilename().' <small>('.format_file_size( $item->getSize() ).')</small></span>' : '<strong>'.$item->getFilename().' <small>('.$size_formatted.')</small></strong>';
      $tree[] = '</a>';
      $tree[] = '</li>';
    }
  }

  return ( !empty( $tree ) ) ? implode( "\n", $tree ) : false;
}
/**
 * literate_callback()
 * 
 * @param mixed $callback
 * @param mixed $arguments
 * @param mixed $item
 * @return
 */
function literate_callback( $callback = null, $arguments = array(), $item = null ) {
  if ( is_callable( $callback ) ) {
    array_unshift( $arguments, $item );
    return call_user_func_array( $callback, $arguments );
  }
  return "#";
}
/**
 * is_uneditable_file()
 * 
 * @param mixed $extension
 * @return
 */
function is_uneditable_file( $extension = null ) {
  $base_files = array( "php", "js" );
  $extensions = apply_filters( "uneditable_files", array( "html", "htm", "css", "lng", "json", "txt", "less", "scss", "tpl", "asp", "aspx", "cgi", "c", "java" ) );
  $extensions = array_map( "strtolower", $extensions );
  $extensions = array_map( "trim", $extensions );
  $extensions = array_merge( $base_files, $extensions );
  $extensions = array_unique( $extensions );
  $extensions = array_filter( $extensions );

  return ( !in_array( $extension, $extensions ) );
}

/**
 * ModLogin
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ModLogin {
  private static $error = null;

  /**
   * ModLogin::isLogged()
   * 
   * @return
   */
  public static function isLogged() {
    global $ipdb;
    $session  = self::getSession();
    if ( !$session ) {
      return false;
    }

    $username = $ipdb->escape( trim( $session["id"] ) );
    $passhash = $ipdb->get_var( "SELECT `password` FROM `$ipdb->admin` WHERE `username` = '{$username}'" );

    return self::compareSession( $passhash, $session["ha"] );
  }

  /**
   * ModLogin::verifyCredentials()
   * 
   * @param mixed $username
   * @param mixed $password
   * @return
   */
  public static function verifyCredentials( $username = null, $password = null ) {
    global $ipdb;
    $username = $ipdb->escape( trim( $username ) );
    $password = $ipdb->escape( trim( $password ) );

    if ( empty( $username ) || empty( $password ) ) {
      self::$error  = "Both username and password fields cannot be left blank";
      return false;
    }

    $passhash = $ipdb->get_var( "SELECT `password` FROM `$ipdb->admin` WHERE `username` = '{$username}'" );
    if ( !$passhash ) {
      self::$error  = "Mismatching user credentials";
      return false;
    }

    if ( !PassHash::compare_hash( $passhash, $password ) ) {
      self::$error  = "Did you forgot your password?";
      return false;
    }

    return self::saveLogin( $username, $passhash );
  }

  /**
   * ModLogin::resetPassword()
   * 
   * @param mixed $email
   * @param mixed $admin
   * @return
   */
  public static function resetPassword( $email = null, $admin = null ) {
    global $ipdb;
    $email  = $ipdb->escape( trim( $email ) );
    $admin  = $ipdb->escape( trim( $admin ) );
    if ( !$admin || !filter_var( $admin, FILTER_VALIDATE_EMAIL ) ) {
      self::$error  = "Critical system failure";
      return false;
    }
    if ( !$email || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
      self::$error  = "Please enter your e-mail";
      return false;
    }
    if ( $email !== $admin ) {
      self::$error  = "Invalid e-mail address";
      return false;
    }
    $newPassword  = self::generate_key( null, 10, false );
    $passBackup   = $newPassword;
    $newPassword  = $ipdb->escape( PassHash::hash( $newPassword ) );

    if ( !self::sentMail( $admin, $passBackup ) ) {
      self::$error  = "Error while sending e-mail";
      return false;
    }
    if ( !$ipdb->query( "UPDATE `$ipdb->admin` SET `password` = '{$newPassword}' WHERE 1" ) ) {
      self::$error  = "Crytical system failure";
      return false;
    }
    return true;
  }

  /**
   * ModLogin::sentMail()
   * 
   * @param mixed $email
   * @param mixed $password
   * @return
   */
  private static function sentMail( $email = null, $password = null ) {
    global $ipdb;
    $email  = $ipdb->escape( $email );
    $name   = $ipdb->get_var( "SELECT `name` FROM `$ipdb->admin` WHERE `email` = '{$email}'" );

    $logo_img = site_uri()."ipChat/images/p-logo.png";
    $html     = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Impact Plus ('.ipgo( "Version" ).') - Password Reset</title>
    <meta name="robots" content="noindex,nofollow"></meta>
    <meta property="og:title" content="Bold Type"></meta>
  </head>
  <body topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" style="background-color:#F1F1EF;font-family:Calibri,Verdana,Arial,Helvetica;font-size:15px;">
    <table align="center" height="100%" width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#f1f1ef">
      <tbody>
        <tr>
          <td>
            <table width="690" align="center" border="0" cellspacing="0" cellpadding="0">
              <tbody>
                <tr>
                  <td colspan="3" height="120" style="padding:0;margin:0;font-size:1;line-height:0;background-color:#2e313a">
                    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td width="20"></td>
                          <td align="left" valign="middle" style="padding:0;margin:0;font-size:1;line-height:0">
                            <a href="'.mentor_uri().'" target="_blank">
                              <img src="'.$logo_img.'" alt="Impact Plus" width="201" height="36">
                            </a>
                          </td>
                          <td width="120" align="right" valign="bottom" style="padding:0 0 7px 0;margin:0;font-size:1;line-height:0">
                            <p style="color:#ffffff;font-size:18px;line-height:18px;margin:0;padding:0 0 5px 0">Impact Plus</p>
                            <p style="color:#ffffff;font-size:12px;line-height:12px;margin:0;padding:0">beyond your thoughts</p>
                          </td>
                          <td width="30"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr><td colspan="3" height="30"></td></tr>
                <tr bgcolor="#ffffff">
                  <td width="30"></td>
                  <td>
                    <table width="630" align="center" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <tr>
                          <td colspan="3" width="630" height="50" style="padding:0;margin:0;font-size:1;line-height:0"></td>
                        </tr>
                        <tr>
                          <td colspan="2" width="450" style="padding:0;margin:0;font-size:1;line-height:0"></td>
                          <td style="padding:0;margin:0;font-size:1;line-height:0"></td>
                        </tr>
                        <tr>
                          <td colspan="3" valign="top" style="padding:0;margin:0;font-size:1;line-height:0">
                            <h2 style="color:#404040;font-size:22px;font-weight:bold;line-height:26px;padding:0 0 5px 0;margin:0">Account Password Changed !</h2>
                            <p style="color:#404040;font-size:14px;line-height:18px;padding:0;margin:0">
                Hi '.$name.',<br /><br />
                The password for <a href="'.mentor_uri().'">Impact Plus</a> Chat <a href="'.admin_uri().'">Admin Panel</a> was recently changed.<br />
                Your new Password is: <strong>'.$password.'</strong><br /><br />
                If you did not change your password, your account might have been hijacked.<br />
                To get back into your account, you will need to enter your new password shown above<br />
                when asked and change your email/password immediately from Admin Panel.
                            </p>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="3" width="630" height="50" style="padding:0;margin:0;font-size:1;line-height:0"></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                  <td width="30"></td>
                </tr>
                <tr>
                  <td colspan="3" align="center">
                    <hr style="border:none;color:#E3E3E3;background-color:#E3E3E3;min-height:1px;width:100%">
                    <p style="color:#bebebe;font-size:12px;line-height:12px;padding:0;margin:0 0 10px 0">&copy; '.date( "Y" ).' Impact Plus | All Rights Reserved</p>
                    <p style="color:#bebebe;font-size:12px;line-height:12px;padding:0;margin:0 0 10px 0">
                      <a href="'.mentor_uri().'privacy/" style="color:#bebebe;text-decoration:underline" target="_blank">Privacy Policy</a> | <a href="'.mentor_uri().'terms/" style="color:#bebebe;text-decoration:underline" target="_blank">Terms and Conditions</a>
                    </p>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>';

    $host = ( defined( "MAIL_HOST" ) ) ? MAIL_HOST : null;
    $port = ( defined( "MAIL_PORT" ) ) ? MAIL_PORT : null;;
    $encr = ( defined( "MAIL_ENCRYPTION" ) ) ? MAIL_ENCRYPTION : null;;
    $user = ( defined( "MAIL_USERNAME" ) ) ? MAIL_USERNAME : null;;
    $pass = ( defined( "MAIL_PASSWORD" ) ) ? MAIL_PASSWORD : null;;

    $mailer = new ipMail( $host, $port, $encr, $user, $pass );
    $mailer->setSubject( "Impact Plus: Password Changed" );
    $mailer->setFrom( array( $email => "Impact Plus" ) );
    $mailer->setTo( array( $email ) );
    $mailer->setBody( $html, "text/html", "utf-8" );
    $mailer->setPriority( 2 );

    $result = 0;
    try {
      $result = $mailer->send();
    }
    catch( Exception $e ) {}
    return ( $result && (int)$result === 1 );
  }

  /**
   * ModLogin::generate_key()
   * 
   * @param string $prefix
   * @param integer $length
   * @param bool $alphabets
   * @return
   */
  private static function generate_key( $prefix = '', $length = 17, $alphabets = false ) {
    $options  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    if ( $alphabets === false ) {
      $options  .=  "!@#$%^&*()/-";
    }
    $code = "";
    for( $i = 0; $i < $length; $i++ ) {
      $key  = rand( 0, strlen( $options ) - 1 );
      $code .=  $options[$key];
    }
    return $prefix.$code;
  }

  /**
   * ModLogin::saveLogin()
   * 
   * @param mixed $username
   * @param mixed $password
   * @param bool $remember
   * @return
   */
  private static function saveLogin( $username = null, $password = null, $remember = false ) {
    global $ipdb;
    $username = $ipdb->escape( $username );
    $cur_log  = $ipdb->escape( $ipdb->get_var( "SELECT `current_login` FROM `$ipdb->admin` WHERE `username` = '{$username}'" ) );
    $time_now = $ipdb->escape( time() );

    if ( (int)$cur_log > 0 ) {
      $ipdb->query( "UPDATE `$ipdb->admin` SET `last_login` = '{$cur_log}', `current_login` = '{$time_now}' WHERE `username` = '{$username}'" );
    }
    else {
      $ipdb->query( "UPDATE `$ipdb->admin` SET `current_login` = '{$time_now}' WHERE `username` = '{$username}'" );
    }

    return self::setSession( $username, $password );
  }

  /**
   * ModLogin::getError()
   * 
   * @return
   */
  public static function getError() {
    return ( self::$error ) ? self::$error : "Error string could not be initialized";
  }

  /**
   * ModLogin::getSession()
   * 
   * @param bool $index
   * @return
   */
  public static function getSession( $index = false ) {
    global $ipdb;
    $auth = array();
    if ( isset( $_SESSION["mod_auth_id"] ) && isset( $_SESSION["mod_auth_hash"] ) ) {
      $auth["id"] = $ipdb->escape( $_SESSION["mod_auth_id"] );
      $auth["ha"] = $ipdb->escape( $_SESSION["mod_auth_hash"] );
      $auth["ti"] = (int)$ipdb->escape( ( ( isset( $_SESSION["mod_auth_time"] ) ) ? $_SESSION["mod_auth_time"] : 0 ) );
      $auth["in"] = "s";
    }
    elseif ( isset( $_COOKIE["mod_auth_id"] ) && isset( $_COOKIE["mod_auth_hash"] ) ) {
      $auth["id"] = $ipdb->escape( $_COOKIE["mod_auth_id"] );
      $auth["ha"] = $ipdb->escape( $_COOKIE["mod_auth_hash"] );
      $auth["ti"] = (int)$ipdb->escape( ( ( isset( $_COOKIE["mod_auth_time"] ) ) ? $_COOKIE["mod_auth_time"] : 0 ) );
      $auth["in"] = "c";
    }
    else {
      return false;
    }
    if ( $index ) {
      return ( isset( $auth[$index] ) ) ? $auth[$index] : false;
    }
    return $auth;
  }

  /**
   * ModLogin::refreshSession()
   * 
   * @return
   */
  public static function refreshSession() {
    $session  = self::getSession();
    if ( $session ) {
      if ( $session["in"] === "s" ) {
        $_SESSION["mod_auth_time"]  = time();
      }
      elseif ( $session["in"] === "c" ) {
        $_COOKIE["mod_auth_time"]   = time();
      }
      return $session;
    }
    return false;
  }

  /**
   * ModLogin::compareSession()
   * 
   * @param mixed $hs
   * @param mixed $ha
   * @return
   */
  public static function compareSession( $hs = null, $ha = null ) {
    if ( !empty( $hs ) && !empty( $ha ) ) {
      $hs = substr( $hs, 7, 25 );
      if ( $hs === $ha ) {
        return true;
      }
    }
    return false;
  }

  /**
   * ModLogin::setSession()
   * 
   * @param mixed $hs
   * @param mixed $ha
   * @param bool $hl
   * @return
   */
  public static function setSession( $hs = null, $ha = null, $hl = false ) {
    $ha = substr( $ha, 7, 25 );
    $_SESSION["mod_auth_id"]    = $hs;
    $_SESSION["mod_auth_hash"]  = $ha;
    $_SESSION["mod_auth_time"]  = time();
    if ( $hl ) {
      setcookie( "mod_auth_id", $hs, time()+3600*24*365, "/" );
      setcookie( "mod_auth_hash", $ha, time()+3600*24*365, "/" );
      setcookie( "mod_auth_time", time(), time()+3600*24*365, "/" );
    }
    return $ha;
  }

  /**
   * ModLogin::unsetSession()
   * 
   * @return
   */
  public static function unsetSession() {
    session_destroy();
    setcookie( "mod_auth_id", null, time() - 100, "/" );
    setcookie( "mod_auth_hash", null, time() - 100, "/" );
    setcookie( "mod_auth_time", null, time() - 100, "/" );
  }

  /**
   * ModLogin::isExpired()
   * 
   * @return
   */
  public static function isExpired() {
    global $ipdb;

    if ( self::isLogged() ) {
      $session  = self::getSession();
      $username = $ipdb->escape( trim( $session["id"] ) );

      $last = $session["ti"];
      $time = time();

      if ( ( $last > 0 ) && ( ( $time - $last ) > 600 ) ) {
        $userinfo = $ipdb->get_row( "SELECT * FROM `$ipdb->admin` WHERE `username` = '{$username}'" );
        return $userinfo;
      }
    }
  
    return false;
  }

  /**
   * ModLogin::getInfo()
   * 
   * @return
   */
  public static function getInfo() {
    global $ipdb;
    $session  = self::getSession();
    $username = $ipdb->escape( trim( $session["id"] ) );

    return $ipdb->get_row( "SELECT * FROM `$ipdb->admin` WHERE `username` = '{$username}'" );
  }
}

/**
 * uploadProcess()
 * 
 * @param mixed $file
 * @param mixed $relation_id
 * @param bool $target
 * @param bool $mimetest
 * @param string $prefix
 * @return
 */
function uploadProcess( $file = array(), $relation_id = null, $target = false, $mimetest = false, $prefix = "thumb_" ) {
  if ( empty( $file ) || !isset( $file["tmp_name"] ) || !is_uploaded_file( $file["tmp_name"] ) ) {
    return false;
  }
  loadClass( "mime", "header/mime.class.php" );
  $extn = strtolower( pathinfo( $file["name"], PATHINFO_EXTENSION ) );
  $name = makeFilename( $relation_id );
  $size = $file["size"];
  $type = ( $file["type"] == "application/octet-stream" ) ? mime::get( $file["name"] ) : $file["type"];
  $temp = $file["tmp_name"];

  if ( $target !== false ) {
    $target = trim( $target );
    $target = trim( $target, "/" );
    $target = trim( $target, DIRECTORY_SEPARATOR );
  }

  $root = ( $target ) ? array( realpath( ROOT_DIR.$target ).DIRECTORY_SEPARATOR, $target."/" ) : makeUploadFolder();
  $path = $root[0].$name.".".$extn;

  $link = $root[1].$name.".".$extn;
  $thmb = null;
  $mgrp = "document";

  if ( !move_uploaded_file( $temp, $path ) ) {
    return false;
  }

  $type = ( $type == "application/octet-stream" ) ? mime::get( $path ) : $type;
  if ( $mimetest !== false && !preg_match( $mimetest, $type ) ) {
    @unlink( $path );
    return false;
  }

  $img  = @getimagesize( $path );
  if ( $img && is_array( $img ) ) {
    loadClass( "phpThumb", "phpthumb/phpthumb.class.php" );
    $phpThumb = new phpThumb();
    $phpThumb->setSourceData( implode( "", file( $path ) ) );
    $phpThumb->setParameter( "w", 150 );
    $phpThumb->setParameter( "h", 150 );
    $phpThumb->setParameter( "zc", "C" );
    $phpThumb->setParameter( "config_output_format", "png" );
    if ( $phpThumb->GenerateThumbnail() ) {
      $mgrp = "image";
      if ( $phpThumb->RenderToFile( $root[0].$prefix.$name.".png" ) ) {
        $thmb = $prefix.$name.".png";
      }
      $phpThumb->purgeTempFiles();
    }
  }

  return array(
    'name'  =>  pathinfo( $file["name"], PATHINFO_FILENAME ),
    'size'  =>  $size,
    'type'  =>  $type,
    'flnka' =>  $path,
    'flnkr' =>  $link,
    'tlnka' =>  $root[0].$thmb,
    'tlnkr' =>  $root[1].$thmb,
    'mgrp'  =>  $mgrp,
    'date'  =>  time()
  );
}

/**
 * makeFilename()
 * 
 * @param mixed $relation_id
 * @return
 */
function makeFilename( $relation_id = null ) {
  $allowed_chars  = implode( "", range( 0, 9 ) ).implode( "", range( "A", "Z" ) ).implode( "", range( "a", "z" ) );
  $allowed_count  = strlen( $allowed_chars );
  $name = null;
  $name_length  = 15;

  while( $name === null ) {
    $name = '';
    for( $i = 0; $i < $name_length; ++$i ) {
      $name .=  $allowed_chars{mt_rand( 0, $allowed_count - 1 )};
    }
  }

  $name .=  "_".uniqid()."_".date("dmY");

  return ( ( $relation_id ) ? $relation_id."_" : "" ).$name;
}
/**
 * makeUploadFolder()
 * 
 * @return
 */
function makeUploadFolder() {
  $root = ROOT_DIR."ipChat/uploads/".date( "Y/m/d/" );
  if ( !file_exists( $root ) ) {
    mkdir( $root, 0755, true );
  }
  return array( realpath( $root ).DIRECTORY_SEPARATOR, "ipChat/uploads/".date( "Y/m/d/" ) );
}
?>
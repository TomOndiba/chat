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
 * ipPing
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipPing {
  private $userID   = false;
  private $ping_id  = false;
  private $error    = false;

  /**
   * ipPing::__construct()
   * 
   * @param bool $user_id
   * @param bool $ping_id
   * @return
   */
  public function __construct( $user_id = false, $ping_id = false ) {
    $this->set_user_id( $user_id );
    $this->set_ping_id( $ping_id );
    $this->clear_out_session();
  }

  /**
   * ipPing::set_user_id()
   * 
   * @param integer $user_id
   * @return
   */
  public function set_user_id( $user_id = 0 ) {
    $this->userID = (int)trim( $user_id );
  }

  /**
   * ipPing::set_ping_id()
   * 
   * @param integer $ping_id
   * @return
   */
  public function set_ping_id( $ping_id = 0 ) {
    $this->ping_id  = trim( $ping_id );
  }

  /**
   * ipPing::get_error()
   * 
   * @return
   */
  public function get_error() {
    return $this->error;
  }

  /**
   * ipPing::process()
   * 
   * @param string $section
   * @param mixed $tabs
   * @return
   */
  public function process( $section = "chat", $tabs = array() ) {
    if ( !$this->userID || !$this->ping_id ) {
      $this->error = "authentification required";
      return false;
    }
    $data = false;
    switch( $section ) {
      case "chat":
        $data = false;
        for( $i = 0; $i <= 1; $i++ ) {
          usleep( 800000 );
          $resp = $this->ping_chat( $tabs );
          if ( $resp === "empty" ) {
            break;
          }
          $data = $resp;
          if ( $data ) {
            break;
          }
        }
      break;
      case "seen":
        $data = $this->ping_seen( $tabs );
      break;
      case "status":
        $data = $this->ping_status();
      break;
      case "users":
        $data = $this->ping_users();
      break;
      case "notif":
        $data = $this->ping_notif();
      break;
      default:
        $data = false;
      break;
    }
    return $data;
  }

  /**
   * ipPing::ping_seen()
   * 
   * @param mixed $tabs
   * @return
   */
  private function ping_seen( $tabs = null ) {
    if ( !is_array( $tabs ) || empty( $tabs ) || !$this->userID ) {
      return false;
    }
    /*if ( !$this->cmp_seen_req_last( $tabs ) ) {
      return false;
    }
    $this->add_seen_req_last( $tabs );*/
    $seen = array();
    loadClass( "ipRelation", "relation.class.php" );
    $relation = new ipRelation( $this->userID );

    foreach( $tabs as $tab ) {
      $idx  = ( isset( $tab[0] ) ) ? (int)$tab[0] : false;
      $idn  = ( isset( $tab[1] ) ) ? $tab[1] : false;
      $ido  = ( isset( $tab[2] ) && (int)$tab[2] === 1 ) ? true : false;
      $idu  = ( isset( $tab[3] ) ) ? (int)$tab[3] : false;
      $idm  = ( isset( $tab[4] ) ) ? (int)$tab[4] : false;

      if ( !$idx || !$idn ) {
        // missing tab id and type
        continue;
      }
      if ( !$idu || !$idm ) {
        // missing tab last message user id and last message id
        continue;
      }
      if ( $idu !== $this->userID ) {
        if ( $ido === true ) {
          $relation->add_seen_time( $this->userID, $idx, $idn, $idm );
        }
        continue;
      }
      //$relation->drop_seen_time( $idx, $this->userID, $idn );
      $seen[] = array(
        $idn,
        $idx,
        $relation->get_seen_time( $idx, $idn )
      );
    }

    if ( !$this->cmp_seen_shown_last( $seen ) ) {
      return false;
    }
    $this->add_seen_shown_last( $seen );

    $typing = $relation->get_typing();

    return array( "s" => $seen, "t" => $typing );
  }
  /**
   * ipPing::ping_notif()
   * 
   * @return
   */
  private function ping_notif() {
    global $ipdb;
    $ipdb->query( "DELETE FROM `$ipdb->notif` WHERE `expire` <= CURRENT_TIMESTAMP" );

    $notif  = array();
    $result = $ipdb->get_results( "
      SELECT `a`.*
      FROM `$ipdb->notif` `a`
      LEFT JOIN `$ipdb->relation` `b`
      ON `a`.`ID` = `b`.`targetID`
      WHERE
        ( `a`.`reciever` = '0' OR `a`.`reciever` = '{$this->userID}' )
        AND `b`.`targetID` IS NULL
    " );
    if ( $result ) {
      $senders  = array();
      $regexp   = "/(@)\[\[(\d+):([\w\s@\.,-\/#!$%\^&\*;:{}=\-_`~()]+)\]\]/i";
      foreach( $result as $item ) {
        if ( (int)$item->sender !== 0 ) {
          $senders[]  = $item->sender;
        }

        if ( preg_match( $regexp, $item->content, $matches ) ) {
          $item->content  = preg_replace( $regexp, '<a class="user-tag" href="#" data-chat="$2">$3</a>', $item->content );
        }

        $notif[$item->ID] = array(
          "ID"      =>  $item->ID,
          "subject" =>  $item->subject,
          "content" =>  $item->content,
          "sender"  =>  $item->sender,
          "time"    =>  $item->datetime,
          "important" =>  ( (int)$item->priority === 1 )
        );
      }

      $users  = array();
      if ( !empty( $senders ) ) {
        loadClass( "users" );
        $users  = ipUsers::get_users( $this->userID, $senders, "ID", null, "name" );
        if ( $users ) {
          $users  = $users["name"];
        }
        else {
          $users  = array();
        }
      }
      
      array_walk( $notif, function( &$value, $index, $users ) {
        $value["sender"]  = (int)$value["sender"];
        if ( $value["sender"] === 0 ) {
          $value["sender"]  = "Global";
          return;
        }
        if ( isset( $users[$value["sender"]] ) ) {
          $value["sender"]  = $users[$value["sender"]];
          return;
        }
        $value["sender"]  = "undefined";
      }, $users );
    }
    else {
      $this->error = "no notification available";
    }

    return ( !empty( $notif ) ) ? $notif : false;
  }
  /**
   * ipPing::ping_status()
   * 
   * @return
   */
  private function ping_status() {
    global $ipdb;
    $time_now   = (int)$ipdb->escape( time() );
    $time_check = $ipdb->escape( $time_now - 100 );
    $type_check = $ipdb->escape( $time_now - 1 );

    $ipdb->query( "
      DELETE FROM `$ipdb->online`
      WHERE
        `last_seen` < $time_check
        AND `userID` != {$this->userID}
    " );
    $ipdb->query( "
      INSERT INTO `$ipdb->online`
        (`userID`, `user_status`, `last_seen`)
      VALUES
        ('{$this->userID}','online','{$time_now}')
      ON DUPLICATE KEY UPDATE
        `last_seen` = '{$time_now}'
    " );
    /*$ipdb->query( "
      REPLACE INTO `$ipdb->online`
        (`userID`, `user_status`, `last_seen`)
      VALUES
        ('{$this->userID}','online','{$time_now}')
    " );*/

    $online = $ipdb->get_results( "
      SELECT `userID`
      FROM `$ipdb->online`
      WHERE
        `userID` != {$this->userID}
        AND
        `user_status` = 'online'
    " );

    if ( $online ) {
      loadClass( "ipUsers", "users.class.php" );
      $status = array();
      $users  = new ipUsers( $this->userID );
      foreach( $online as $user ) {
        $status[$user->userID]  = array(
          "ID"  =>  (int)$user->userID,
          "ST"  =>  $users->user_status( $user->userID ),
          "SA"  =>  $users->user_status_mask( $user->userID ),
          "LS"  =>  (int)$users->last_seen_time( $user->userID )
        );
      }
      if ( !$this->cmp_status_shown_last( $status ) ) {
        return false;
      }
      $this->add_status_shown_last( $status );
      return $status;
    }
    return array( "t" => "continue" );
  }
  /**
   * ipPing::ping_users()
   * 
   * @return
   */
  private function ping_users() {
    loadClass( "ipUsers", "users.class.php" );
    $users  = new ipUsers( $this->userID );
    $users  = $users->list_users( 30 );
    if ( !$this->cmp_users_shown_last( $users ) ) {
      return false;
    }
    $this->add_users_shown_last( $users );
    return $users;
  }
  /**
   * ipPing::ping_chat()
   * 
   * @param mixed $tabs
   * @return
   */
  private function ping_chat( $tabs = null ) {
    global $ipdb;
    if ( (int)$ipdb->get_var( "SELECT COUNT(*) FROM `$ipdb->messages`" ) === 0 ) {
      return "empty";
    }
    $shown_id   = $this->get_message_shown();
    $relations  = "
      SELECT *
      FROM `$ipdb->relation`
      WHERE
        `mainID` = '{$this->userID}'
        AND
        `structure` = 'hasMessage'
        %s
      ORDER BY `time` DESC
    ";
    if ( $shown_id && !empty( $shown_id ) ) {
      $relations  = sprintf( $relations, "AND identifier NOT IN ('".implode( "','", $shown_id )."')" );
    }
    else {
      $relations  = sprintf( $relations, null );
    }
    $relations  = $ipdb->get_results( $relations );
    $messages   = false;

    if ( $relations ) {
      $batch_qr = array();
      foreach( $relations as $relation ) {
        $this->add_message_shown( $relation->identifier );

        $message_id = $ipdb->escape( $relation->time );
        $target_id  = $ipdb->escape( $relation->targetID );
        $is_group   = ( $relation->targetIG === "group" ) ? true : false;
        $query      = "(
          SELECT * FROM `$ipdb->messages`
          WHERE
            ( `ID` = '{$message_id}' OR `ID` > '{$message_id}' )
            %s
        )";
        if ( !$is_group ) {
          $query  = sprintf( $query, "AND `sent_from` = '{$target_id}' AND `userID` = '{$this->userID}'" );
        }
        else {
          $query  = sprintf( $query, "AND `sent_to` = '{$target_id}'" );
        }
        $batch_qr[] = $query;
      }
      if ( !empty( $batch_qr ) ) {
        $batch_qr = implode( " UNION ALL ", $batch_qr );
        $results  = $ipdb->get_results( $batch_qr );
        if ( $results ) {
          $messages = array();
          loadClass( "ipMessages", "chat.class.php" );
          loadClass( "ipRelation", "relation.class.php" );
          $message  = new ipMessages( $this->userID );
          $relation = new ipRelation( $this->userID );
          foreach( $results as $result ) {
            $user_id  = ( (int)$result->groupID === 0 ) ? $result->sent_from : $result->groupID;
            $user_ix  = ( (int)$result->groupID === 0 ) ? "user" : "group";
            if ( $this->tab_is_opened( $tabs, $user_id, $user_ix ) ) {
              if ( (int)$result->sent_from !== (int)$this->userID ) {
                if ( (int)$result->is_notice === 0 || ( (int)$result->is_notice === 1 && (int)$result->has_attachment === 1 ) ) {
                  $relation->add_seen_time( $this->userID, $user_id, $user_ix );
                }
              }
            }
            else {
              
            }
            $messages[$result->ID]  = $message->process_message( $result );
          }
        }
        else {
          $this->error  = "query could not handle as expected";
        }
      }
      else {
        $this->error  = "sequence missing";
      }
    }
    else {
      $this->error  = "partition empty";
    }
    return $messages;
  }

  /**
   * ipPing::tab_is_opened()
   * 
   * @param mixed $tabs
   * @param mixed $idx
   * @param mixed $idn
   * @return
   */
  private function tab_is_opened( $tabs = null, $idx, $idn ) {
    if ( !is_array( $tabs ) || empty( $tabs ) ) {
      return false;
    }
    foreach( $tabs as $tab ) {
      if ( (int)$tab[0] === (int)$idx && $tab[1] === $idn && (int)$tab[2] === 1 ) {
        return true;
      }
    }
    return false;
  }

  /**
   * ipPing::get_message_shown()
   * 
   * @return
   */
  private function get_message_shown() {
    $this->init_session_index( "new_messages", array() );
    return $this->get_session_index( "new_messages" );
  }
  /**
   * ipPing::add_message_shown()
   * 
   * @param bool $id
   * @return
   */
  private function add_message_shown( $id = false ) {
    if ( !$id ) {
      return false;
    }
    $this->init_session_index( "new_messages", array() );
    return $this->put_session_index( "new_messages", $id );
  }
  /**
   * ipPing::del_message_shown()
   * 
   * @param bool $id
   * @return
   */
  private function del_message_shown( $id = false ) {
    if ( !$id ) {
      return false;
    }
    if ( in_array( $id, $_SESSION["has_message_shown"][$this->ping_id] ) ) {
      $key  = array_search( $id, $_SESSION["has_message_shown"][$this->ping_id] );
      if ( $key && isset( $_SESSION["has_message_shown"][$this->ping_id][$key] ) ) {
        unset( $_SESSION["has_message_shown"][$this->ping_id][$key] );
      }
    }
    return;
  }

  /**
   * ipPing::array_recursive_diff()
   * 
   * @param mixed $aArray1
   * @param mixed $aArray2
   * @param mixed $except
   * @return
   */
  private function array_recursive_diff( $aArray1, $aArray2, $except = array() ) { 
    $aReturn  = array();
    foreach( $aArray1 as $mKey => $mValue ) {
      if ( array_key_exists( $mKey, $aArray2 ) ) {
        if ( is_array( $mValue ) ) {
          $aRecursiveDiff = $this->array_recursive_diff( $mValue, $aArray2[$mKey], $except );
          if ( count( $aRecursiveDiff ) ) {
            $aReturn[$mKey] = $aRecursiveDiff;
          }
        }
        else {
          if ( $mValue != $aArray2[$mKey] ) {
            if ( !is_array( $mValue ) && !is_object( $mValue ) ) {
              if ( !in_array( $mKey, $except ) ) {
                $aReturn[$mKey] = $mValue;
              }
            }
            else {
              $diff = array_diff( (array)$mValue, (array)$aArray2[$mKey] );
              $diff = array_diff( array_keys( $diff ), array_values( $except ) );
              if ( !empty( $diff ) ) {
                $aReturn[$mKey] = $mValue;
              }
            }
          }
        }
      }
      else {
        $aReturn[$mKey] = $mValue;
      } 
    }
    return $aReturn;
  }

  /**
   * ipPing::get_users_shown_last()
   * 
   * @return
   */
  private function get_users_shown_last() {
    $this->init_session_index( "users_list", array() );
    return $this->get_session_index( "users_list" );
  }
  /**
   * ipPing::add_users_shown_last()
   * 
   * @param bool $users
   * @return
   */
  private function add_users_shown_last( $users = false ) {
    $this->init_session_index( "users_list", array() );
    return $this->set_session_index( "users_list", $users );
  }
  /**
   * ipPing::cmp_users_shown_last()
   * 
   * @param bool $users
   * @return
   */
  private function cmp_users_shown_last( $users = false ) {
    $last = $this->get_users_shown_last();
    if ( empty( $last ) || empty( $users ) ) {
      return true;
    }
    if ( count( $last ) !== count( $users ) ) {
      return true;
    }
    $diff = $this->array_recursive_diff( $users, $last, array( "LS" ) );
    return ( !empty( $diff ) );
  }

  /**
   * ipPing::get_seen_shown_last()
   * 
   * @return
   */
  private function get_seen_shown_last() {
    $this->init_session_index( "seen_list", array() );
    return $this->get_session_index( "seen_list" );
  }
  /**
   * ipPing::add_seen_shown_last()
   * 
   * @param bool $seen
   * @return
   */
  private function add_seen_shown_last( $seen = false ) {
    $this->init_session_index( "seen_list", array() );
    return $this->set_session_index( "seen_list", $seen );
  }
  /**
   * ipPing::cmp_seen_shown_last()
   * 
   * @param bool $seen
   * @return
   */
  private function cmp_seen_shown_last( $seen = false ) {
    $last = $this->get_seen_shown_last();
    if ( empty( $last ) || empty( $seen ) ) {
      return true;
    }
    if ( count( $last ) !== count( $seen ) ) {
      return true;
    }
    $diff = $this->array_recursive_diff( $seen, $last );
    return ( !empty( $diff ) );
  }

  /**
   * ipPing::get_seen_req_last()
   * 
   * @return
   */
  private function get_seen_req_last() {
    $this->init_session_index( "seen_req_list", array() );
    return $this->get_session_index( "seen_req_list" );
  }
  /**
   * ipPing::add_seen_req_last()
   * 
   * @param bool $req
   * @return
   */
  private function add_seen_req_last( $req = false ) {
    $this->init_session_index( "seen_req_list", array() );
    return $this->set_session_index( "seen_req_list", $req );
  }
  /**
   * ipPing::cmp_seen_req_last()
   * 
   * @param bool $req
   * @return
   */
  private function cmp_seen_req_last( $req = false ) {
    $last = $this->get_seen_req_last();
    if ( empty( $last ) || empty( $req ) ) {
      return true;
    }
    if ( count( $last ) !== count( $req ) ) {
      return true;
    }
    $diff = $this->array_recursive_diff( $req, $last );
    return ( !empty( $diff ) );
  }

  /**
   * ipPing::get_status_shown_last()
   * 
   * @return
   */
  private function get_status_shown_last() {
    $this->init_session_index( "status_list", array() );
    return $this->get_session_index( "status_list" );
  }
  /**
   * ipPing::add_status_shown_last()
   * 
   * @param bool $status
   * @return
   */
  private function add_status_shown_last( $status = false ) {
    $this->init_session_index( "status_list", array() );
    return $this->set_session_index( "status_list", $status );
  }
  /**
   * ipPing::cmp_status_shown_last()
   * 
   * @param bool $status
   * @return
   */
  private function cmp_status_shown_last( $status = false ) {
    $last = $this->get_status_shown_last();
    if ( empty( $last ) || empty( $status ) ) {
      return true;
    }
    if ( count( $last ) !== count( $status ) ) {
      return true;
    }
    $diff = $this->array_recursive_diff( $status, $last, array( "LS" ) );
    return ( !empty( $diff ) );
  }

  /**
   * ipPing::set_dynamic_session()
   * 
   * @param bool $index
   * @param mixed $value
   * @return
   */
  private function set_dynamic_session( $index = false, $value = null ) {
    if ( !$index ) {
      return false;
    }
    $_SESSION["ip_ping"]  = array();
    $_SESSION["ip_ping"][$index]  = $value;
  }

  /**
   * ipPing::get_dynamic_session()
   * 
   * @param bool $index
   * @return
   */
  private function get_dynamic_session( $index = false ) {
    if ( !$index ) {
      return array();
    }
    if ( isset( $_SESSION["ip_ping"] ) && isset( $_SESSION["ip_ping"][$index] ) ) {
      return $_SESSION["ip_ping"][$index];
    }
    return array();
  }

  /**
   * ipPing::init_session_index()
   * 
   * @param bool $index
   * @param mixed $value
   * @return
   */
  private function init_session_index( $index = false, $value = null ) {
    if ( !isset( $_SESSION["ip_ping"][$this->ping_id][$index] ) ) {
      $_SESSION["ip_ping"][$this->ping_id][$index]  = $value;
    }
  }
  /**
   * ipPing::get_session_index()
   * 
   * @param bool $index
   * @return
   */
  private function get_session_index( $index = false ) {
    return ( isset( $_SESSION["ip_ping"][$this->ping_id][$index] ) ) ? $_SESSION["ip_ping"][$this->ping_id][$index] : false;
  }
  /**
   * ipPing::set_session_index()
   * 
   * @param bool $index
   * @param mixed $value
   * @return
   */
  private function set_session_index( $index = false, $value = null ) {
    if ( isset( $_SESSION["ip_ping"][$this->ping_id][$index] ) ) {
      $_SESSION["ip_ping"][$this->ping_id][$index]  = $value;
    }
  }
  /**
   * ipPing::put_session_index()
   * 
   * @param bool $index
   * @param mixed $value
   * @return
   */
  private function put_session_index( $index = false, $value = null ) {
    if ( isset( $_SESSION["ip_ping"][$this->ping_id][$index] ) && is_array( $_SESSION["ip_ping"][$this->ping_id][$index] ) ) {
      if ( !in_array( $value, $_SESSION["ip_ping"][$this->ping_id][$index] ) ) {
        array_push( $_SESSION["ip_ping"][$this->ping_id][$index], $value );
      }
    }
  }

  /**
   * ipPing::clear_out_session()
   * 
   * @return
   */
  private function clear_out_session() {
    $this->set_dynamic_session( $this->ping_id, $this->get_dynamic_session( $this->ping_id ) );
  }
}

?>
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
 * ipRelation
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipRelation {
  private $userID = 0;

  /**
   * ipRelation::__construct()
   * 
   * @param mixed $userID
   * @return
   */
  public function __construct( $userID ) {
    $this->set_user_id( $userID );
  }

  /**
   * ipRelation::set_user_id()
   * 
   * @param integer $userID
   * @return
   */
  public function set_user_id( $userID = 0 ) {
    $this->userID = ( $userID === "admin" ) ? $userID : (int)trim( $userID );
  }

  /**
   * ipRelation::openTab()
   * 
   * @param bool $idx
   * @param bool $idn
   * @return
   */
  public function openTab( $idx = false, $idn = false ) {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );
    $idt  = trim( $ipdb->escape( time() ) );

    if ( !$this->userID || !$idx || !$idn ) {
      return false;
    }

    if ( !$this->relation_exists( $this->userID, $idx, $idn, "tabFocused" ) ) {
      return $this->insert_relation( $this->userID, $idx, $idn, "tabFocused", $idt );
    }
    return true;
  }

  /**
   * ipRelation::closeTab()
   * 
   * @param bool $idx
   * @param bool $idn
   * @return
   */
  public function closeTab( $idx = false, $idn = false ) {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );

    if ( !$this->userID || !$idx || !$idn ) {
      return false;
    }

    if ( $relationID = $this->relation_exists( $this->userID, $idx, $idn, "tabFocused" ) ) {
      return $this->remove_relation( $relationID );
    }
    return false;
  }

  /**
   * ipRelation::set_chat_status()
   * 
   * @param string $status
   * @return
   */
  public function set_chat_status( $status = "online" ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    if ( $status === "online" ) {
      return $ipdb->query( "DELETE FROM `$ipdb->online_rel` WHERE `user` = '{$this->userID}' AND `target` = '0'" );
    }
    $time = (int)time();
    return $ipdb->query( "
      INSERT INTO `$ipdb->online_rel`
        (`user`, `target`, `status`, `seen`)
      VALUES
        ('{$this->userID}', '0', 'offline', '{$time}')
      ON DUPLICATE KEY UPDATE
        `status` = 'offline',
        `seen` =  '{$time}'
    " );
  }

  /**
   * ipRelation::update_chat_settings()
   * 
   * @param bool $users
   * @param string $mode
   * @return
   */
  public function update_chat_settings( $users = false, $mode = "online" ) {
    if ( !$this->userID ) {
      return false;
    }
    $response = array( "error" => 1, "message" => "Could not change chat settings" );
    $time     = (int)time();
    global $ipdb;
    if ( $mode === "online" ) {
      $query  = $ipdb->query( "DELETE FROM `$ipdb->online_rel` WHERE `user` = '{$this->userID}'" );
      if ( $query ) {
        $response["error"]    = 0;
        $response["message"]  = "success";
      }
    }
    else {
      if ( $users && !empty( $users ) && is_array( $users ) && $mode !== "offline" ) {
        $users  = array_map( array( $ipdb, "escape" ), $users );
        $users  = array_map( "trim", $users );
        $user_ids = implode( ",", $users ).",0";
      }
      if ( ( !isset( $user_ids ) || empty( $user_ids ) ) && $mode !== "offline" ) {
        return $response;
      }
      $time = (int)time();
      switch( $mode ) {
        case "blacklist":
          $ipdb->query( "
            DELETE FROM `$ipdb->online_rel`
            WHERE
              `user` = '{$this->userID}'
              AND
              `target` NOT IN ($user_ids)
          " );
          $user_vals  = array(
            "('{$this->userID}', 0, 'online', '{$time}')"
          );
          foreach( $users as $user ) {
            $user_vals[]  = "('{$this->userID}', '{$user}', 'offline', '{$time}')";
          }
          $user_vals  = implode( ",\n", $user_vals );
          $ipdb->query( "
            INSERT INTO `$ipdb->online_rel`
              (`user`, `target`, `status`, `seen`)
            VALUES
              {$user_vals}
            ON DUPLICATE KEY UPDATE
              `status` = IF( `target` = '0', 'online', 'offline'),
              `seen` =  '{$time}'
          " );
        break;
        case "whitelist":
          $ipdb->query( "
            DELETE FROM `$ipdb->online_rel`
            WHERE
              `user` = '{$this->userID}'
              AND
              target NOT IN ($user_ids)
          " );
          $user_vals  = array(
            "('{$this->userID}', 0, 'offline', '{$time}')"
          );
          foreach( $users as $user ) {
            $user_vals[]  = "('{$this->userID}', '{$user}', 'online', '{$time}')";
          }
          $user_vals  = implode( ",\n", $user_vals );
          $ipdb->query( "
            INSERT INTO `$ipdb->online_rel`
              (`user`, `target`, `status`, `seen`)
            VALUES
              {$user_vals}
            ON DUPLICATE KEY UPDATE
              `status` = IF( `target` = '0', 'offline', 'online'),
              `seen` = '{$time}'
          " );
        break;
        case "offline":
          $ipdb->query( "
            DELETE FROM `$ipdb->online_rel`
            WHERE
              `user` = '{$this->userID}'
              AND
              target != '0'
          " );
          $ipdb->query( "
            INSERT INTO `$ipdb->online_rel`
              (`user`, `target`, `status`, `seen`)
            VALUES
              ('{$this->userID}', 0, 'offline', '{$time}')
            ON DUPLICATE KEY UPDATE
              `status` = 'offline',
              `seen` = '{$time}'
          " );
        break;
      }
      $response["error"]    = 0;
      $response["message"]  = "success";
    }
    return $response;
  }

  /**
   * ipRelation::do_typing()
   * 
   * @param mixed $idx
   * @param mixed $idn
   * @param mixed $idj
   * @return
   */
  public function do_typing( $idx = null, $idn = null, $idj = null ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );
    $idj  = trim( $ipdb->escape( $idj ) );

    if ( $idx && $idn ) {
      $typing = $ipdb->query( "
        INSERT INTO `$ipdb->relation`
          (`mainID`, `targetID`, `targetIG`, `structure`, `time`)
        VALUES
          ('{$this->userID}','{$idx}','{$idn}','tabTyping','{$idj}')
        ON DUPLICATE KEY UPDATE
          `time` = '{$idj}'
      " );
      return $typing;
    }

    return false;
  }

  /**
   * ipRelation::get_typing()
   * 
   * @return
   */
  public function get_typing() {
    global $ipdb;
    $where  = "WHERE `targetID` = '{$this->userID}' AND `targetIG` = 'user' AND `structure` = 'tabTyping'";
    $typing = $ipdb->get_results( " SELECT * FROM `$ipdb->relation` {$where}" );
    $result = array();

    if ( $typing ) {
      $ipdb->query( "DELETE FROM `$ipdb->relation` {$where}" );
      foreach( $typing as $typ ) {
        $result[$typ->mainID] = $typ->time;
      }
    }
    return $result;
  }

  /**
   * ipRelation::presence_settings()
   * 
   * @param bool $idx
   * @param string $idn
   * @return
   */
  public function presence_settings( $idx = false, $idn = "offline" ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idn  = trim( $ipdb->escape( $idn ) );
    $time = (int)time();

    if ( !$idx || !is_array( $idx ) || empty( $idx ) ) {
      return $ipdb->query( "
        INSERT INTO `$ipdb->online_rel`
          (`user`, `target`, `status`, `seen`)
        VALUES
          ('{$this->userID}', 0, '{$idn}', '{$time}')
        ON DUPLICATE KEY UPDATE
          `status` = '{$idn}',
          `seen` = '{$time}'
      " );
    }
    else {
      $vals = array();
      foreach( $idx as $id ) {
        if ( (int)$id === 0 ) {
          continue;
        }
        $id = $ipdb->escape( trim( $id ) );
        $vals[] = "('{$this->userID}', '{$id}', '{$idn}', '{$time}')";
      }
      $vals = implode( ",\n", $vals );
      if ( empty( $idx ) ) {
        return;
      }
      return $ipdb->query( "
        INSERT INTO `$ipdb->online_rel`
          (`user`, `target`, `status`, `seen`)
        VALUES
          {$vals}
        ON DUPLICATE KEY UPDATE
          `status` = '{$idn}',
          `seen` = '{$time}'
      " );
    }
  }

  /**
   * ipRelation::online_settings()
   * 
   * @return
   */
  public function online_settings() {
    if ( !$this->userID ) {
      return false;
    }
    $data = array(
      "tokens"  =>  array(
        "blacklist" =>  false,
        "whitelist" =>  false
      ),
      "status"  =>  "offline"
    );

    global $ipdb;
    $online_rel = $ipdb->get_results( "SELECT * FROM `$ipdb->online_rel` WHERE user = '{$this->userID}'" );
    $global_rel = "online";

    if ( $online_rel ) {
      foreach( $online_rel as $rel ) {
        if ( (int)$rel->target !== 0 ) {
          if ( $rel->status !== "online" ) {
            $data["tokens"]["blacklist"][]  = $rel->target;
          }
          else {
            $data["tokens"]["whitelist"][]  = $rel->target;
          }
        }
        else {
          $global_rel = $rel->status;
        }
      }
    }
    switch( true ) {
      case ( $global_rel === "online" ):
      default:
        $global_rel = "blacklist";
      break;
      case ( !$data["tokens"]["whitelist"] && !$data["tokens"]["blacklist"] ):
        $global_rel = "offline";
      break;
      case ( $global_rel !== "online" && $data["tokens"]["whitelist"] ):
        $global_rel = "whitelist";
      break;
    }

    $data["status"] = $global_rel;

    return $data;
  }

  /**
   * ipRelation::getTabs()
   * 
   * @param mixed $idx
   * @return
   */
  public function getTabs( $idx = null ) {
    global $ipdb;
    $idx  = ( !$idx ) ? $this->userID : $ipdb->escape( trim( $idx ) );
    if ( !$idx ) {
      return false;
    }
    $tabs   = false;
    $result = $ipdb->get_results( "SELECT * FROM $ipdb->relation WHERE structure = 'tabFocused' AND mainID = '{$idx}'" );
    if ( $result ) {
      $tabs = array();
      foreach( $result as $tab ) {
        $tabs[$tab->targetIG.$tab->targetID] = array(
          "idx" =>  $tab->targetID,
          "idn" =>  $tab->targetIG
        );
      }
    }
    return $tabs;
  }

  /**
   * ipRelation::add_seen_time()
   * 
   * @param mixed $row1
   * @param mixed $row2
   * @param string $row3
   * @param mixed $row4
   * @return
   */
  public function add_seen_time( $row1 = null, $row2 = null, $row3 = "user", $row4 = null ) {
    global $ipdb;
    $row1 = trim( $ipdb->escape( $row1 ) );
    $row2 = trim( $ipdb->escape( $row2 ) );
    $row3 = trim( $ipdb->escape( $row3 ) );
    $row4 = trim( $ipdb->escape( $row4 ) );
    $row5 = trim( $ipdb->escape( time() ) );

    $query_base = "
      DELETE FROM `$ipdb->relation`
      WHERE
        (
          `mainID` = '{$row1}'
          AND `targetID` = '{$row2}'
          AND `targetIG` = '{$row3}'
          AND `structure` = 'hasMessage'
        )
        %s
    ";
    if ( $row3 === "group" ) {
      $query_base = sprintf(
        $query_base,
        "OR
        (
          `targetID` = '{$row2}'
          AND `targetIG` = '{$row3}'
          AND `structure` = 'messageSeen'
          AND `identifier` != '{$row4}'
        )"
      );
    }
    else {
      $query_base = sprintf( $query_base, null );
    }
    $ipdb->query( $query_base );

    return $ipdb->query( "
      INSERT IGNORE INTO `$ipdb->relation`
        (`mainID`, `targetID`, `targetIG`, `structure`, `time`, `identifier`)
      VALUES
        ('{$row1}','{$row2}','{$row3}','messageSeen','{$row5}','{$row4}')
    " );
  }
  /**
   * ipRelation::get_seen_time()
   * 
   * @param mixed $idx
   * @param mixed $idn
   * @return
   */
  public function get_seen_time( $idx = null, $idn = null ) {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );
 
    if ( $idn === "user" ) {
      if ( $seen = $this->relation_exists( $idx, $this->userID, $idn, "messageSeen", false, "time" ) ) {
        return $this->time_difference( (int)$seen );
      }
    }
    else {
      $seen = false;
      $users_saw  = $ipdb->get_results( "SELECT SQL_CALC_FOUND_ROWS mainID FROM `$ipdb->relation` WHERE `targetID` = '{$idx}' AND `targetIG` = 'group' AND `structure` = 'messageSeen' AND `mainID` != '{$this->userID}' GROUP BY mainID LIMIT 0, 3" );
      if ( $users_saw ) {
        loadClass( "ipUsers", "users.class.php" );
        $seen = array();
        $seen_users = (int)$ipdb->get_var( "SELECT FOUND_ROWS()" );
        $users_saw  = ipUsers::get_users( $this->userID, $users_saw, "ID", "mainID", "name" );
        if ( $users_saw ) {
          $users_saw  = $users_saw["name"];
          if ( count( $users_saw ) === 1 ) {
            return "by ".implode( "", $users_saw );
          }
          if ( count( $users_saw ) === 2 ) {
            return "by ".implode( " and ", $users_saw );
          }
          if ( count( $users_saw ) === 3 && $seen_users === 3 ) {
            sort( $users_saw );
            return "by ".$users_saw[0].", ".$users_saw[1]." and ".$users_saw[2];
          }
          $left_seen  = (int)( $seen_users - count( $users_saw ) );
          return "by ".implode( ", ", $users_saw ).", ".$left_seen." more";
        }
        else {
          return "by ".( $seen_users === 1 ) ? "1 user" : $seen_users." users";
        }
      }
      return $seen;
    }
    return false;
  }
  /**
   * ipRelation::drop_seen_time()
   * 
   * @param mixed $mainID
   * @param mixed $targetID
   * @param string $targetIG
   * @return
   */
  public function drop_seen_time( $mainID = null, $targetID = null, $targetIG = "user" ) {
    global $ipdb;
    $mainID   = trim( $ipdb->escape( $mainID ) );
    $targetID = trim( $ipdb->escape( $targetID ) );
    $targetIG = trim( $ipdb->escape( $targetIG ) );

    $query_base = "DELETE FROM `$ipdb->relation` WHERE %s `targetID` = '{$targetID}' AND `targetIG` = '{$targetIG}' AND `structure` = 'messageSeen'";
    $query_base = ( $targetIG === "group" ) ? sprintf( $query_base, null ) : sprintf( $query_base, "`mainID` = '{$mainID}' AND" );

    return $ipdb->query( $query_base );
  }

  /**
   * ipRelation::add_message_retrieved()
   * 
   * @param bool $user
   * @param bool $from
   * @param bool $is_group
   * @param bool $messageID
   * @return
   */
  public function add_message_retrieved( $user = false, $from = false, $is_group = false, $messageID = false ) {
    global $ipdb;
    $userID     = $ipdb->escape( trim( $user ) );
    $targetID   = $ipdb->escape( trim( $from ) );
    $targetIG   = ( $is_group ) ? "group" : "user";
    $structur   = $ipdb->escape( "hasMessage" );
    $time       = $ipdb->escape( $messageID );
    $identifier = $ipdb->escape( uniqid() );

    if ( !$userID || !$targetID || !$time ) {
      return;
    }

    if ( !$is_group ) {
      $ipdb->query( "
        INSERT INTO `$ipdb->relation`
          (`mainID`, `targetID`, `targetIG`, `structure`, `time`, `identifier`)
        VALUES
          ('{$userID}','{$targetID}','user','{$structur}','{$time}','{$identifier}')
        ON DUPLICATE KEY UPDATE
          `identifier` = '{$identifier}'
      " );
    }
    else {
      $group_users  = $ipdb->get_results( "
        SELECT `userID` FROM `$ipdb->groups_rel`
        WHERE
          `groupID` = '{$targetID}'
          AND `status` = 'active'
      " );
      $users_id = array();
      if ( $group_users ) {
        foreach( $group_users as $user_id ) {
          if ( (int)$user_id->userID !== (int)$this->userID ) {
            $users_id[] = $ipdb->escape( $user_id->userID );
          }
        }
        $append   = "('";
        $prepend  = "','{$targetID}','group','{$structur}','{$time}','{$identifier}')";

        $users_id = $append.implode( $prepend.",".$append, $users_id ).$prepend;
  
        $ipdb->query( "
          INSERT INTO `$ipdb->relation`
            (`mainID`, `targetID`, `targetIG`, `structure`, `time`, `identifier`)
          VALUES
            {$users_id}
          ON DUPLICATE KEY UPDATE
            `identifier` = '{$identifier}'
        " );
      }
    }
  }
  /**
   * ipRelation::drop_message_retrieved()
   * 
   * @param mixed $row1
   * @param mixed $row2
   * @param string $row3
   * @return
   */
  public function drop_message_retrieved( $row1 = null, $row2 = null, $row3 = "user" ) {
    global $ipdb;
    $row1 = trim( $ipdb->escape( $row1 ) );
    $row2 = trim( $ipdb->escape( $row2 ) );
    $row3 = trim( $ipdb->escape( $row3 ) );

    return $ipdb->query( "
      DELETE FROM `$ipdb->relation`
      WHERE
        `mainID` = '$row1'
        AND `targetID` = '$row2'
        AND `targetIG` = '$row3'
        AND `structure` = 'hasMessage'
    " );
  }

  /**
   * ipRelation::messageSeen()
   * 
   * @param mixed $idw
   * @param mixed $idx
   * @param mixed $idn
   * @param bool $seen
   * @return
   */
  public function messageSeen( $idw = null, $idx = null, $idn = null, $seen = false ) {
    global $ipdb;
    $idw  = trim( $ipdb->escape( $idw ) );
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );
    $idt  = trim( $ipdb->escape( time() ) );

    if ( $seen === true ) {
      if ( $relationID = $this->relation_exists( $idw, $idx, $idn, "messageSeen" ) ) {
        return;// $this->remove_relation( $relationID );
      }
      $ipdb->query( "
        DELETE FROM $ipdb->relation
        WHERE
          ( `mainID` = '{$idw}' AND `targetID` = '{$idx}' AND `targetIG` = '{$idn}' AND `structure` = 'hasMessage' )
          OR
          ( `mainID` = '{$idx}' AND `targetID` = '{$idw}' AND `targetIG` = 'user' AND `structure` = 'messageSeen' )
      " );
      return $this->insert_relation( $idw, $idx, $idn, "messageSeen", $idt );
    }
    else {
      if ( $idn === "group" ) {
        if ( $relationID = $this->relation_exists( false, $idx, $idn, "messageSeen" ) ) {
          return $ipdb->query( "DELETE FROM $ipdb->relation WHERE `targetID` = '{$idx}' AND `targetIG` = 'group' AND `structure` = 'messageSeen'" );
        }
        return;
      }
      if ( $relationID = $this->relation_exists( $idw, $idx, $idn, "messageSeen" ) ) {
        return $this->remove_relation( $relationID );
      }
    }
  }

  /**
   * ipRelation::getGroupInfo()
   * 
   * @param mixed $idx
   * @return
   */
  public function getGroupInfo( $idx = null ) {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    if ( !$idx ) {
      return false;
    }
    if ( checkCache( $idx, "groups" ) ) {
      return getCache( $idx, "groups" );
    }
    $group  = $ipdb->get_row( "SELECT * FROM $ipdb->groups WHERE ID = '{$idx}'" );
    if ( $group ) {
      $users  = $ipdb->get_results( "SELECT userID, status FROM $ipdb->groups_rel WHERE groupID = '{$idx}'" );
      if ( $users ) {
        $result = array(
          "users" =>  array(),
          "ID"    =>  $group->ID,
          "time"  =>  $group->created_on,
          "owner" =>  $group->created_by,
          "name"  =>  trim( $group->name ),
          "write" =>  ( $this->userID === "admin" ) ? true : hasConvWrite( $group->ID ),
          "avail" =>  array()
        );
        foreach( $users as $user ) {
          $result["users"][]  = (int)$user->userID;
          if ( $user->status === "active" ) {
            $result["avail"][]  = (int)$user->userID;
          }
        }
        if ( !$result["name"] ) {
          $result["name"] = $this->groupNameFromUsers( $result["users"] );
        }
        return addCache( $idx, (object)$result, "groups" );
      }
    }
    return false;
  }

  /**
   * ipRelation::groupNameFromUsers()
   * 
   * @param mixed $users
   * @return
   */
  public function groupNameFromUsers( $users = array() ) {
    $users  = array_filter( array_unique( array_map( "trim", $users ) ) );
    if ( !empty( $users ) ) {
      loadClass( "users" );
      $length = count( $users );
      $fusers = ipUsers::get_users( "admin", array_slice( $users, 0, 3 ) );
      $name   = array();
      if ( $fusers ) {
        $flength  = count( $fusers );
        foreach( $fusers as $fuser ) {
          $name[] = get_lname( $fuser->NM);
        }
        if ( $length != $flength ) {
          $name = array_pad( $name, ( ( $length > 3 ) ? 3 : $length ), "undefined" );
        }
        if ( $length === 1 ) {
          $name = sprintf( "%s", $name[0] );
        }
        elseif ( $length === 2 ) {
          $name = sprintf( "%s and %s", $name[0], $name[1] );
        }
        elseif ( $length === 3 ) {
          $name = sprintf( "%s, %s and %s", $name[0], $name[1], $name[2] );
        }
        else {
          $name = sprintf( "%s, %s, %s and %s more", $name[0], $name[1], $name[2], ( $length - 3 ) );
        }
      }
      if ( empty( $name ) ) {
        $name = $length.( ( $length == 1 ) ? " person" : " peoples" );
      }
      return $name;
    }
    return "no name";
  }

  /**
   * ipRelation::hasMessage()
   * 
   * @param mixed $idx
   * @param mixed $idn
   * @return
   */
  public function hasMessage( $idx = null, $idn = null ) {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );
    $idt  = trim( $ipdb->escape( time() ) );

    if ( !$this->userID || !$idx || !$idn ) {
      return false;
    }

    if ( !$this->relation_exists( $this->userID, $idx, $idn, "hasMessage" ) ) {
      return $this->insert_relation( $this->userID, $idx, $idn, "hasMessage", $idt );
    }
    return true;
  }

  /**
   * ipRelation::notHasMessage()
   * 
   * @return
   */
  public function notHasMessage() {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );
    $idn  = trim( $ipdb->escape( $idn ) );

    if ( !$this->userID || !$idx || !$idn ) {
      return false;
    }

    if ( $relationID = $this->relation_exists( $this->userID, $idx, $idn, "hasMessage" ) ) {
      return $this->remove_relation( $relationID );
    }
    return false;
  }

  /**
   * ipRelation::remove_relation()
   * 
   * @param mixed $idx
   * @return
   */
  public function remove_relation( $idx = null ) {
    global $ipdb;
    $idx  = trim( $ipdb->escape( $idx ) );

    if ( !$idx ) {
      return false;
    }

    return $ipdb->query( "DELETE FROM $ipdb->relation WHERE ID = '{$idx}'" );
  }

  /**
   * ipRelation::insert_relation()
   * 
   * @param mixed $idu
   * @param mixed $idx
   * @param mixed $idn
   * @param mixed $ids
   * @param mixed $idt
   * @return
   */
  public function insert_relation( $idu = null, $idx = null, $idn = null, $ids = null, $idt = null ) {
    global $ipdb;
    $query  = "INSERT INTO $ipdb->relation ";
    $keys   = array();
    $vals   = array();
    if ( $idu ) {
      $keys[] = "`mainID`";
      $vals[] = "'".$ipdb->escape( $idu )."'";
    }
    if ( $idx ) {
      $keys[] = "`targetID`";
      $vals[] = "'".$ipdb->escape( $idx )."'";
    }
    if ( $idn ) {
      $keys[] = "`targetIG`";
      $vals[] = "'".$ipdb->escape( $idn )."'";
    }
    if ( $ids ) {
      $keys[] = "`structure`";
      $vals[] = "'".$ipdb->escape( $ids )."'";
    }
    if ( $idt ) {
      $keys[] = "`time`";
      $vals[] = "'".$ipdb->escape( $idt )."'";
    }
    return $ipdb->query( "INSERT INTO $ipdb->relation(".implode( ", ", $keys ).") VALUES(".implode( ", ", $vals ).")" );
  }

  /**
   * ipRelation::relation_exists()
   * 
   * @param mixed $idu
   * @param mixed $idx
   * @param mixed $idn
   * @param mixed $ids
   * @param mixed $idt
   * @param string $index
   * @param bool $arr
   * @return
   */
  public function relation_exists( $idu = null, $idx = null, $idn = null, $ids = null, $idt = null, $index = "ID", $arr = false ) {
    global $ipdb;
    $index  = ( is_array( $index ) ) ? "`".$ipdb->escape( implode( "`, `", $index ) )."`" : ( ( $index == "count" ) ? "COUNT(*)" : "`".$ipdb->escape( $index )."`" );
    $query  = "SELECT {$index} FROM `$ipdb->relation` WHERE";
    $where  = array();
    if ( $idu ) {
      $where[]  = "`mainID`".$this->where_clause( $idu );
    }
    if ( $idx ) {
      $where[]  = "`targetID`".$this->where_clause( $idx );
    }
    if ( $idn ) {
      $where[]  = "`targetIG`".$this->where_clause( $idn );
    }
    if ( $ids ) {
      $where[]  = "`structure`".$this->where_clause( $ids );
    }
    if ( $idt ) {
      $where[]  = "`time`".$this->where_clause( $idt );
    }
    if ( empty( $where ) ) {
      return false;
    }
    $where  = implode( " AND ", $where );

    return ( $arr === true ) ? $ipdb->get_results( $query." ".$where ) : $ipdb->get_var( $query." ".$where );
  }

  /**
   * ipRelation::where_clause()
   * 
   * @param mixed $idx
   * @return
   */
  private function where_clause( $idx = null ) {
    global $ipdb;
    if ( is_array( $idx ) ) {
      return " IN (".implode( ", ", $idx ).")";
    }
    return " = '".$ipdb->escape( $idx )."'";
  }

  /**
   * ipRelation::time_difference()
   * 
   * @param integer $timestamp
   * @return
   */
  private function time_difference( $timestamp = 0 ) {
    $text = date( "F jS", $timestamp );
    if ( (int)date( "Y", $timestamp ) === (int)date( "Y", time() ) ) {
      if ( (int)date( "m", $timestamp ) === (int)date( "m", time() ) ) {
        if ( (int)date( "d", $timestamp ) === (int)date( "d", time() ) ) {
          for( $h = 0; $h < 25; $h++ ) {
            if ( (int)date( "H", $timestamp ) === $h ) {
              $text = date( "h:i A", $timestamp );
            }
          }
        }
        elseif ( (int)date( 'd', $timestamp ) === ( (int)date( 'd', time() ) - 1 ) ) {
          $text = ( (int)date( 'd', time() ) - 1 );
        }
        else {
          $sent_day = (int)date( 'd', $timestamp );
          for( $d = 2; $d <= 5; $d++ ) {
            $week_day = ( (int)date( 'd', time() ) - $d );
            if ( $sent_day === $week_day ) {
              $text = date( "l", $timestamp );
            }
          }
        }
      }
    }
    else {
      $text = date( 'F jS, Y', $timestamp );
    }
    return $text;
  }
}

?>
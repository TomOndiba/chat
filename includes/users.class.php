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
 * ipUsers
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipUsers {
  private $exclude  = array();
  private $search   = null;
  private $options  = array();
  private $userID   = false;
  private $limit    = 30;

  private $urlregex = "/^(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?$/i";

  private $chat_db  = false;
  private $user_db  = false;
  private $user_tbl = false;

  /**
   * ipUsers::fetch_users()
   * 
   * @param mixed $userID
   * @param mixed $search
   * @param mixed $exclude
   * @param bool $limit
   * @return
   */
  public static function fetch_users( $userID, $search = null, $exclude = array(), $limit = false ) {
    $users  = new ipUsers( $userID );
    if ( $search ) {
      $users->set_search_string( $search );
    }
    $users->set_excluded_ids( $exclude );
    return $users->list_users( $limit );
  }
  /**
   * ipUsers::get_users()
   * 
   * @param mixed $userID
   * @param mixed $idx
   * @param string $idn
   * @param mixed $ids
   * @param mixed $idw
   * @param bool $idf
   * @param bool $arr
   * @return
   */
  public static function get_users( $userID, $idx = null, $idn = "ID", $ids = null, $idw = null, $idf = false, $arr = true ) {
    $users  = new ipUsers( $userID );
    return $users->get_users_info( $idx, $idn, $ids, $idw, $idf, $arr );
  }
  /**
   * ipUsers::user_tb()
   * 
   * @return
   */
  public static function user_tb() {
    $users  = new ipUsers;
    return $users->get_user_table();
  }
  /**
   * ipUsers::user_db()
   * 
   * @return
   */
  public static function user_db() {
    $users  = new ipUsers;
    return $users->get_user_db();
  }
  /**
   * ipUsers::logged_user()
   * 
   * @param mixed $userID
   * @return
   */
  public static function logged_user( $userID = null ) {
    $users  = new ipUsers( $userID );
    return $users->logged_in_user();
  }

  /**
   * ipUsers::__construct()
   * 
   * @param mixed $userID
   * @return
   */
  public function __construct( $userID = null ) {
    $this->set_user_id( $userID );
    $this->chat_db  = $this->escape( MAIN_DB_NAME );
    $this->user_db  = $this->escape( USER_DB_NAME );
    $this->user_tbl = (object)array(
      "ID"  =>  $this->escape( USER_COL_ID ),
      "name"  =>  $this->escape( USER_COL_NAME ),
      "user"  =>  $this->escape( USER_COL_USERNAME ),
      "pass"  =>  $this->escape( USER_COL_PASSWORD ),
      "email" =>  $this->escape( USER_COL_EMAIL ),
      "avatar"  =>  $this->escape( USER_COL_AVATAR )
    );
  }

  /**
   * ipUsers::refresh_status()
   * 
   * @param bool $drop
   * @return
   */
  public function refresh_status( $drop = false ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $time   = $ipdb->escape( time() );
    $query  = false;

    if ( !$drop ) {
      $query  = $ipdb->query( "
        INSERT INTO `$ipdb->online`
          (`userID`, `user_status`, `last_seen`)
        VALUES
          ('{$this->userID}', 'online', '{$time}')
        ON DUPLICATE KEY UPDATE
          `user_status` = 'online', `last_seen` = '{$time}'"
      );
    }
    else {
      $query  = $ipdb->query( "DELETE FROM `$ipdb->online` WHERE `userID` = '{$this->userID}'" );
    }

    $idle     = $ipdb->escape( $time - 20 );
    $offline  = $ipdb->escape( $time - 100 );

    $ipdb->query( "
      UPDATE `$ipdb->online` SET `user_status` = 'offline' WHERE `last_seen` < $offline AND `userID` != '{$this->userID}';
    " );
    //$ipdb->query( "UPDATE `$ipdb->online` SET `user_status` = 'idle' WHERE `last_seen` < $idle" );
    //$ipdb->query( "UPDATE `$ipdb->online` SET `user_status` = 'offline' WHERE `last_seen` < $offline" );

    return $query;
  }

  /**
   * ipUsers::get_user_table()
   * 
   * @return
   */
  public function get_user_table() {
    return $this->user_tbl;
  }

  /**
   * ipUsers::get_user_db()
   * 
   * @return
   */
  public function get_user_db() {
    return $this->user_db;
  }

  /**
   * ipUsers::set_user_id()
   * 
   * @param mixed $userID
   * @return
   */
  public function set_user_id( $userID ) {
    $this->userID = ( is_int( $userID ) ) ? (int)$userID : $this->escape( $userID );
  }

  /**
   * ipUsers::set_search_string()
   * 
   * @param mixed $search
   * @return
   */
  public function set_search_string( $search = null ) {
    $this->search = $this->escape( trim( (string)$search ) );
  }

  /**
   * ipUsers::set_excluded_ids()
   * 
   * @param mixed $exclude
   * @return
   */
  public function set_excluded_ids( $exclude = null ) {
    $this->exclude  = (array)$exclude;
  }

  /**
   * ipUsers::get_user()
   * 
   * @param mixed $userid
   * @param string $col
   * @param bool $index
   * @return
   */
  public function get_user( $userid = null, $col = "ID", $index = false ) {
    global $ipudb;
    $userid = $this->escape( trim( $userid ) );
    $result = array();
    $table  = $this->user_tbl;
    $ID     = $table->ID;
    $blockd = (array)$this->list_blocked( true );
    $blockd = ( !empty( $blockd ) ) ? " AND `{$ID}` NOT IN (".$ipudb->escape( implode( ",", $blockd ) ).")" : "";
    $col    = ( isset( $table->{$col} ) ) ? $table->{$col} : $table->ID;
    $user   = $ipudb->get_row( "SELECT * FROM `$ipudb->users` WHERE `{$col}` = '{$userid}'{$blockd}" );
    $user   = $this->process_user( $user );

    if ( $index ) {
      if ( $user ) {
        switch( strtolower( $index ) ) {
          case "name":
            return $user->NM;
          break;
        }
      }
      return false;
    }
    else {
      return $user;
    }
  }

  /**
   * ipUsers::update_user()
   * 
   * @param mixed $idx
   * @param mixed $col
   * @param mixed $val
   * @return
   */
  public function update_user( $idx, $col, $val ) {
    global $ipudb;
    $idx  = $this->escape( trim( $idx ) );
    $col  = $this->escape( trim( $col ) );
    $val  = $this->escape( trim( $val ) );
    $tbl  = $this->user_tbl;

    if ( !$idx || !$col || !$val ) {
      return false;
    }

    if ( !isset( $tbl->{$col} ) ) {
      return false;
    }
    $col  = $tbl->{$col};
    $ID   = $tbl->ID;

    return $ipudb->query( "UPDATE `$ipudb->users` SET `{$col}` = '{$val}' WHERE `{$ID}` = '{$idx}'" );
  }

  /**
   * ipUsers::list_users()
   * 
   * @param bool $limit
   * @return
   */
  public function list_users( $limit = false ) {
    if ( !$this->userID ) {
      return false;
    }
 
    global $ipdb, $ipudb;
    $results  = array();

    $exclude  = (array)$this->exclude;
    $blocked  = (array)$this->list_blocked( true );

    $exclude  = array_unique( array_merge( $exclude, $blocked ) );
    sort( $exclude );

    $exclude  = $this->escape( trim( implode( ",", $exclude ) ) );
    

    $extract  = array();
    $userdb   = "`".$this->user_db."`.`".$ipudb->users."`";
    $chatdb   = "`".$this->chat_db."`.`".$ipdb->messages."`";
    $onlinedb = "`".$this->chat_db."`.`".$ipdb->online."`";
    $usertbl  = $this->user_tbl;

    if ( !empty( $exclude ) ) {
      $extract[]  = "AND `u`.`{$usertbl->ID}` NOT IN ($exclude)";
    }
    if ( !empty( $this->search ) ) {
      $extract[]  = "AND `u`.`{$usertbl->name}` LIKE '%s'";
    }
    $extract  = trim( implode( " ", $extract ) );

    $actual = "`u`.*";
    $count  = "COUNT(".$actual.")";
    $limit  = ( $limit ) ? "LIMIT ".$limit : null;
    $execute  = "
    (
      SELECT %s
      FROM {$userdb} `u`
      LEFT OUTER JOIN {$chatdb} `c`
      ON `c`.`targetID` = `u`.`{$usertbl->ID}`
      LEFT OUTER JOIN {$onlinedb} `o`
      ON `o`.`userID` = `u`.`{$usertbl->ID}`
      WHERE
        `c`.`groupID` = 0
        AND `o`.`user_status` != 'offline'
        AND `u`.`{$usertbl->ID}` != '{$this->userID}' {$extract}
      ORDER BY `o`.`last_seen` DESC
      %s
    )
    UNION
    (
      SELECT %s
      FROM {$userdb} `u`
      LEFT OUTER JOIN {$chatdb} `c`
      ON `c`.`targetID` = `u`.`{$usertbl->ID}`
      WHERE
        `c`.`groupID` = 0
        AND `u`.`{$usertbl->ID}` != '{$this->userID}' {$extract}
      ORDER BY COUNT(`c`.`ID`) DESC
      %s
    )
    UNION
    (
      SELECT %s
      FROM {$userdb} `u`
      LEFT OUTER JOIN {$onlinedb} `o`
      ON `o`.`userID` = `u`.`{$usertbl->ID}`
      WHERE `u`.`{$usertbl->ID}` != '{$this->userID}' {$extract}
      ORDER BY `o`.`last_seen` DESC
      %s
    )
    UNION
    (
      SELECT %s
      FROM {$userdb} `u`
      WHERE `u`.`{$usertbl->ID}` != '{$this->userID}' {$extract}
      ORDER BY `u`.`{$usertbl->ID}` ASC
      %s
    )
    ";

    $execute  = trim( preg_replace( '/  {2,}/', ' ', $execute ) );

    if ( !empty( $this->search ) ) {
      $extract  = ( !empty( $this->search ) ) ? "%{$this->search}%" : null;
      $execute  = sprintf( $execute, $actual, $extract, $limit, $actual, $extract, $limit, $actual, $extract, $limit, $actual, $extract, $limit );
    }
    else {
      $execute  = sprintf( $execute, $actual, $limit, $actual, $limit, $actual, $limit, $actual, $limit );
    }

    $execute  = $ipdb->get_results( $execute ) ;

    if ( $execute ) {
      foreach( $execute as $user ) {
        if ( empty( $user->{$usertbl->ID} ) ) {
          continue;
        }
        $results[$user->{$usertbl->ID}] = $this->process_user( $user );
      }
      unset( $user, $execute );
    }
    return $results;
  }

  /**
   * ipUsers::block_users()
   * 
   * @param mixed $idx
   * @return
   */
  public function block_users( $idx = null ) {
    if ( !$this->userID ) {
      return false;
    }
    $idx  = array_filter( (array)$idx );
    $idn  = $this->list_blocked( true );
    $idv  = array();

    $idx  = array_diff( $idx, $idn );

    if ( empty( $idx ) ) {
      return false;
    }

    global $ipdb;
    $idx  = array_map( array( $ipdb, "escape" ), $idx );

    $idm  = "
      INSERT INTO `$ipdb->relation`
        (`mainID`, `targetID`, `targetIG`, `structure`, `time`)
      VALUES
        %s
    ";

    foreach( $idx as $id ) {
      $time   = (int)time();
      $idv[]  = "('{$this->userID}', '{$id}', 'user', 'blockedList', '{$time}')";
    }
    $idv  = implode( ",\n", $idv );
    $idm  = sprintf( $idm, $idv );

    return $ipdb->query( $idm );
  }

  /**
   * ipUsers::unblock_users()
   * 
   * @param mixed $idx
   * @return
   */
  public function unblock_users( $idx = null ) {
    if ( !$this->userID ) {
      return false;
    }
    $idx  = array_filter( (array)$idx );

    if ( empty( $idx ) ) {
      return false;
    }

    global $ipdb;
    $idx  = array_map( array( $ipdb, "escape" ), $idx );
    $idn  = $idx;
    $idx  = implode( ",", $idx );

    $idq  = $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `mainID` = '{$this->userID}' AND `targetID` IN ({$idx}) AND `structure` = 'blockedList'" );
    return ( $idq ) ? $this->get_users_info( $idn ) : false;
  }

  /**
   * ipUsers::process_user()
   * 
   * @param mixed $user
   * @return
   */
  public function process_user( $user = null ) {
    if ( !$user ) {
      return false;
    }
    $table  = $this->user_tbl;
    $result = array(
      "ID"  =>  (int)$user->{$table->ID},
      "UN"  =>  $user->{$table->user},
      "EL"  =>  $user->{$table->email},
      "NM"  =>  $user->{$table->name},
      "AV"  =>  $this->fix_avatar( (int)$user->{$table->ID}, $user->{$table->avatar} ),
      "ST"  =>  $this->user_status( $user->{$table->ID} ),
      "SA"  =>  $this->user_status_mask( $user->{$table->ID} ),
      "SD"  =>  $this->user_presence_mask( $user->{$table->ID} ),
      "LS"  =>  $this->last_seen_time( $user->{$table->ID} )
    );
    return (object)$result;
  }

  /**
   * ipUsers::last_seen_time()
   * 
   * @param mixed $user_id
   * @return
   */
  public function last_seen_time( $user_id = null ) {
    if ( !$this->userID ) {
      return false;
    }
    if ( (int)$this->userID === (int)$user_id ) {
      return time();
    }
    $this->user_status( $user_id );
    if ( checkCache( $user_id, "user_status" ) ) {
      $user_status  = getCache( $user_id, "user_status" );
      if ( isset( $user_status->seen ) && !empty( $user_status->seen ) ) {
        if ( isset( $user_status->user_status ) && !empty( $user_status->user_status ) ) {
          $st = $user_status->user_status;
        }
        else {
          $st = $user_status->status;
        }
        return ( $st === "online" ) ? (int)$user_status->last_seen : (int)$user_status->seen;
      }
      else {
        return (int)$user_status->last_seen;
      }
    }
  }
  /**
   * ipUsers::user_status()
   * 
   * @param mixed $user_id
   * @return
   */
  public function user_status( $user_id = null ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $a  = $ipdb->online;
    $b  = $ipdb->online_rel;
    $user_status  = false;
    if ( checkCache( $user_id, "user_status" ) ) {
      $user_status  = getCache( $user_id, "user_status" );
    }
    else {
      if ( (int)$this->userID === (int)$user_id ) {
        $user_status  = $ipdb->get_row( "SELECT * FROM `$b` LEFT JOIN `$a` ON `$a`.`userID` = `$b`.`user` WHERE `$b`.`user` = '{$user_id}' AND `$b`.`target` = '0'" );
      }
      else {
        $count  = "COUNT(*)";
        $actual = "`o`.`user_status`, `o`.`last_seen`, `r`.`status`, `r`.`seen`";
        $group  = "GROUP BY ".$actual;
        $query  = "
          ( SELECT %s FROM `$ipdb->online` `o` LEFT OUTER JOIN `$ipdb->online_rel` `r` ON `o`.`userID` = `r`.`user` WHERE ( `o`.`userID` = '{$user_id}' OR `r`.`user` = '{$user_id}' ) AND `r`.`target` = '{$this->userID}' %s )
          UNION
          ( SELECT %s FROM `$ipdb->online` `o` LEFT OUTER JOIN `$ipdb->online_rel` `r` ON `o`.`userID` = `r`.`user` WHERE ( `o`.`userID` = '{$user_id}' OR `r`.`user` = '{$user_id}' ) AND `r`.`target` = '0' %s )
          UNION
          ( SELECT %s FROM `$ipdb->online` `o` LEFT OUTER JOIN `$ipdb->online_rel` `r` ON `o`.`userID` = `r`.`user` WHERE `o`.`userID` = '{$user_id}' %s )
        ";
        if ( $ipdb->get_var( sprintf( $query, $count, $group, $count, $group, $count, $group ) ) ) {
          $user_status  = $ipdb->get_row( sprintf( $query, $actual, null, $actual, null, $actual, null ) );
        }
      }
    }

    if ( $user_status ) {
      addCache( $user_id, $user_status, "user_status" );
      if ( isset( $user_status->status ) && !empty( $user_status->status ) ) {
        return $user_status->status;
      }
      else {
        return $user_status->user_status;
      }
    }
    else {
      $st = ( (int)$this->userID === (int)$user_id ) ? "online" : "offline";
      addCache( $user_id, (object)array( "user_status" => $st, "last_seen" => 0 ), "user_status" );
      return $st;
    }
  }
  /**
   * ipUsers::user_status_mask()
   * 
   * @param mixed $user_id
   * @return
   */
  public function user_status_mask( $user_id = null ) {
    if ( !$this->userID ) {
      return false;
    }
    if ( (int)$this->userID === (int)$user_id ) {
      return false;
    }
    $this->user_status( $user_id );
    if ( checkCache( $user_id, "user_status" ) ) {
      $user_status  = getCache( $user_id, "user_status" );
      return ( ( isset( $user_status->status ) && !empty( $user_status->status ) ) ? $user_status->status : false );
    }
    global $ipdb;
  }
  /**
   * ipUsers::user_presence_mask()
   * 
   * @param mixed $user_id
   * @return
   */
  public function user_presence_mask( $user_id = null ) {
    if ( !$this->userID ) {
      return false;
    }
    if ( (int)$this->userID === (int)$user_id ) {
      return false;
    }
    global $ipdb;
    $status = $ipdb->get_var( "SELECT `status` FROM $ipdb->online_rel WHERE `user` = '{$this->userID}' AND `target` = '{$user_id}'" );
    if ( $status ) {
      return $status;
    }
    return false;
  }

  /**
   * ipUsers::get_users_info()
   * 
   * @param mixed $idx
   * @param string $idn
   * @param mixed $ids
   * @param mixed $idw
   * @param bool $force
   * @param bool $arr
   * @return
   */
  public function get_users_info( $idx = null, $idn = "ID", $ids = null, $idw = null, $force = false, $arr = true ) {
    if ( !$this->userID && $force !== true ) {
      return false;
    }
    global $ipudb;
    if ( $ids ) {
      $tmp  = $idx;
      $idx  = array();
      foreach( $tmp as $idz ) {
        if ( isset( $idz->{$ids} ) ) {
          $idx[]  = $this->escape( $idz->{$ids} );
        }
      }
    }
    if ( !$idx || empty( $idx ) ) {
      return false;
    }
    $idn  = trim( $this->escape( $idn ) );
    $idw  = trim( $this->escape( $idw ) );
    $idwb = $idw;

    $results  = false;

    $userdb   = $this->user_db.".".$ipudb->users;
    $usertbl  = $this->user_tbl;
    $blocked  = $this->escape( implode( ",", $this->list_blocked( true ) ) );
    $blocked  = ( $blocked ) ? " AND `{$usertbl->ID}` NOT IN ($blocked)" : "";

    $idw  = ( $idw && isset( $usertbl->{$idw} ) ) ? "`".$this->escape( $usertbl->ID )."`, `".$this->escape( $usertbl->{$idw} )."`" : "*";
    $idn  = ( $idn && isset( $usertbl->{$idn} ) ) ? "`".$this->escape( $usertbl->{$idn} )."`" : "`".$usertbl->ID."`";
    $idx  = ( is_array( $idx ) ) ? "IN (".$this->escape( implode( ",", $idx ) ).")" : "= '".$this->escape( $idx )."'";

    $users  = $ipudb->get_results( "SELECT $idw FROM `{$ipudb->users}` WHERE {$idn} {$idx}{$blocked}" );

    if ( $users ) {
      $results  = array();
      foreach( $users as $user ) {
        if ( $idwb && isset( $usertbl->{$idwb} ) ) {
          $results[$idwb][$user->{$usertbl->ID}]  = $user->{$idwb};
        }
        else {
          $row  = $this->process_user( $user );
          $results[$user->{$usertbl->ID}] = $row;
        }
      }
      reset( $results );
    }

    return ( !$arr && is_array( $results ) && ( count( $results ) === 1 ) ) ? current( $results ) : $results;
  }

  /**
   * ipUsers::get_user_status()
   * 
   * @param mixed $uID
   * @param mixed $return
   * @return
   */
  private function get_user_status( $uID, $return = null ) {
    global $ipdb;
    if ( !$uID ) {
      return false;
    }
    $uID  = $this->escape( $uID );

    $data1  = $ipdb->get_row( "SELECT * FROM `$ipdb->online` WHERE `userID` = '{$uID}'" );
    $data2  = $ipdb->get_row( "SELECT `status`, `time` FROM `$ipdb->status_mode` WHERE `targetID` = '{$this->userID}' AND `userID` = '{$uID}' AND `status` != 'dynamic'" );

    if ( $data2 ) {
      if ( $data2->status == 'online' && $data2->time > ( time() - 10 ) ) {
        //
      }
      else {
        if ( !$data1 ) {
          $data1  = (object)array();
        }
        $data1->user_status = $data2->status;
        $data1->set_status  = ( isset( $data1->set_status ) ) ? $data1->set_status : null;
        $data1->last_seen   = $data2->time;
      }
    }

    if ( $data1 ) {
      if ( $return && ( isset( $data1->{$return} ) ) ) {
        return $data1->{$return};
      }
    }

    return $data1;
  }

  /**
   * ipUsers::fix_avatar()
   * 
   * @param mixed $avatar
   * @return
   */
  private function fix_avatar( $user_id, $avatar = null ) {
    $avatar = apply_filters_ref_array( "user_avatar_url", array( $avatar, $user_id ) );
    if ( empty( $avatar ) ) {
      return site_uri()."ipChat/images/users/default.jpg";
    }
    if ( preg_match( $this->urlregex, $avatar ) ) {
      return $avatar;
    }
    if ( stripos( $avatar, "ipchat" ) !== 0 ) {
      return $avatar;
    }
    $base = $_SERVER["REQUEST_URI"];
    $pos  = strpos( $base, "ipChat" );
    if ( $pos !== false ) {
      $base = substr( $base, 0, $pos );
      return $base.$avatar;
    }
    return $avatar;
  }

  /**
   * ipUsers::list_blocked()
   * 
   * @param bool $array
   * @param mixed $users
   * @param bool $detailed
   * @return
   */
  public function list_blocked( $array = false, $users = null, $detailed = false ) {
    if ( !$this->userID || $this->userID == "admin" ) {
      return array();
    }
    global $ipdb;

    $blocked_obj  = array();
    $blocked_arr  = array();

    if ( !empty( $users ) || !is_object( $users ) ) {
      if ( checkCache( $this->userID.( ( $detailed ) ? "a" : "" ), "list_blocked" ) ) {
        $users  = getCache( $this->userID.( ( $detailed ) ? "a" : "" ), "list_blocked" );
      }
      else {
        $sprintf  = ( $detailed ) ? "( `mainID` = '{$this->userID}' OR `mainID` = '0' )" : "( ( `mainID` = '{$this->userID}' OR `targetID` = '{$this->userID}' ) OR `mainID` = '0' )";
        $users  = $ipdb->get_results( "SELECT `mainID`, `targetID` FROM $ipdb->relation WHERE {$sprintf} AND `structure` = 'blockedList'" );
        addCache( $this->userID.( ( $detailed ) ? "a" : "" ), $users, "list_blocked" );
      }
    }

    if ( $users && count( $users ) > 0 ) {
      foreach( $users as $user ) {
        if ( !$array && (int)$user->mainID === 0 ) {
          continue;
        }
        $uid  = ( $user->mainID == $this->userID || (int)$user->mainID === 0 ) ? $user->targetID : $user->mainID;
        $usr  = ( $user->mainID == $this->userID || (int)$user->mainID === 0 ) ? "selfblocked" : "userblocked";
        if ( !in_array( $uid, $blocked_arr ) ) {
          $blocked_obj[$uid]  = $usr;
          $blocked_arr[]  = $uid;
        }
      }
    }

    if ( $detailed ) {
      if ( empty( $blocked_arr ) ) {
        return array( "t" => "continue" );
      }
      global $ipudb;
      $id_imploded  = implode( ",", $blocked_arr );
      $usertbl  = $this->user_tbl;
      $results  = $ipudb->get_results( "SELECT `{$usertbl->ID}`, `{$usertbl->name}`, `{$usertbl->avatar}` FROM `{$ipudb->users}` WHERE `{$usertbl->ID}` IN ({$id_imploded})" );
      if ( $results ) {
        $return = array();
        foreach( $results as $result ) {
          $row  = array();
          $row["ID"]  = (int)$result->{$usertbl->ID};
          $row["NM"]  = $result->{$usertbl->name};
          $row["AV"]  = $this->fix_avatar( (int)$result->{$usertbl->ID},  $result->{$usertbl->avatar} );
          $return[$result->{$usertbl->ID}]  = $row;
        }
        return $return;
      }
      return;
    }

    return ( $array === true ) ? $blocked_arr : $blocked_obj;
  }

  /**
   * ipUsers::escape()
   * 
   * @param mixed $str
   * @return
   */
  private function escape( $str = null ) {
    global $ipdb;
    return $ipdb->escape( trim( $str ) );
  }
}
?>
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
 * ipMessages
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipMessages {
  private $fb_code  = '/\s+\[\[([\w\d\.\_]+)\]\]/i';
  private $ext_url  = '/((http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?)/i';
  private $emoj_reg = '/^\[emoji\]([\w\d_-]+)\.([\w\d]+)\[\/emoji\]$/';
  private $userID   = false;
  private $message  = array(
    'userID'    =>  0,
    'targetID'  =>  0,
    'groupID'   =>  0,
    'relationID'  =>  0,
    'attachments' =>  array(),
    'message'   =>  '',
    'message_o' =>  '',
    'message_n' =>  '',
    'sent_date' =>  0,
    'sent_from' =>  0,
    'sent_to'   =>  0,
    'is_readed' =>  0,
    'is_opened' =>  0,
    'read_date' =>  0,
    'read_datetime'   =>  '0000-00-00',
    'read_timestamp'  =>  '0000-00-00 00:00:00',
    'has_attachment'  =>  0,
    'is_notice'       =>  0,
    'notice_section'  =>  'left',
    'datetime'  =>  '0000-00-00',
    'timestamp' =>  '0000-00-00 00:00:00',
    'source_code' =>  'chat',
    'source_name' =>  'Chat'
  );

  /**
   * ipMessages::load()
   * 
   * @param bool $userID
   * @param bool $id
   * @param bool $type
   * @param bool $older
   * @return
   */
  public static function load( $userID = false, $id = false, $type = false, $older = false ) {
    $chat = new ipMessages( $userID );
    return $chat->load_chat( $id, $type, $older );
  }

  /**
   * ipMessages::__construct()
   * 
   * @param bool $userID
   * @return
   */
  public function __construct( $userID = false ) {
    $this->set_user_id( $userID );
  }

  /**
   * ipMessages::set_user_id()
   * 
   * @param bool $userID
   * @return
   */
  public function set_user_id( $userID = false ) {
    $this->userID = ( $userID == "admin") ? $userID : (int)$userID;
  }

  /**
   * ipMessages::send()
   * 
   * @param mixed $message
   * @param bool $force
   * @return
   */
  public function send( $message = array(), $force = false ) {
    if ( !$this->userID || !is_array( $message ) || empty( $message ) ) {
      return false;
    }

    $skeleton = $this->message_skeleton();
    $message  = array_merge( $skeleton, $message );

    $keys = array();
    $vals = array();

    if ( ( $this->userID !== (int)$message["userID"] ) || ( $this->userID !== (int)$message["sent_from"] ) ) {
      return false;
    }
    if ( 0 === (int)$message["groupID"] && 0 === (int)$message["sent_to"] ) {
      return false;
    }

    $is_group = ( (int)$message["groupID"] !== 0 );
    if ( $is_group && !hasConvWrite( $message["groupID"] ) && !$force ) {
      return false;
    }
    if ( $is_group ) {
      $message["sent_to"] = $message["groupID"];
    }

    $message["message"] = $message["message_o"];
    $attachments  = $message["attachments"];

    global $ipdb;
    unset( $message["message_o"], $message["message_n"], $message["ID"], $message["unID"], $message["attachments"] );

    $userID   = $message["userID"];
    $targetID = $message["targetID"];

    $sent_idx = ( $is_group ) ? $message["groupID"] : $message["targetID"];
    $sent_idn = ( $is_group ) ? "group" : "user";

    foreach( $message as $key => &$val ) {
      if ( !isset( $skeleton[$key] ) ) {
        unset( $message[$key] );
      }
      else {
        if ( !is_object( $val ) && !is_array( $val ) ) {
          $keys[$key] = "`".$ipdb->escape( $key )."`";
          $vals[$key] = "'".$ipdb->escape( $val )."'";
        }
      }
    }

    $query1 = $ipdb->query( "
      INSERT INTO $ipdb->messages
        (".implode( ", ", $keys ).")
      VALUES
        (".implode( ", ", $vals ).")
    " );

    if ( $query1 ) {
      $relationID = $ipdb->escape( $message["relationID"] );
      if ( !empty( $attachments ) && is_array( $attachments ) ) {
        $message["has_attachment"]  = "1";

        $attachments  = $ipdb->escape( trim( implode( ",", $attachments ) ) );
        $ipdb->query( "
          UPDATE `$ipdb->attachments`
          SET
            `relationID` = '{$relationID}'
          WHERE
            `ID` IN ($attachments)
        " );
      }

      loadClass( "ipRelation", "relation.class.php" );
      $relation = new ipRelation( $this->userID );
      $relation->drop_seen_time( $sent_idx, $this->userID, $sent_idn );
      $relation->drop_seen_time( $this->userID, $sent_idx, $sent_idn );
      $message["ID"]  = $ipdb->insert_id;

      $ret1 = $message["sent_to"];
      $ret2 = ( ( $is_group ) ? $message["groupID"] : $message["sent_from"] );
      $ret3 = $is_group;
      $ret4 = $message["ID"];
 
      if ( !$is_group ) {

        $vals["userID"]   = "'".$ipdb->escape( $targetID )."'";
        $vals["targetID"] = "'".$ipdb->escape( $userID )."'";
        
        $query2 = $ipdb->query( "
          INSERT INTO $ipdb->messages
            (".implode( ", ", $keys ).")
          VALUES
            (".implode( ", ", $vals ).")
        " );
        if ( $query2 ) {
          $ret4 = $ipdb->insert_id;
        }
      }

      $relation->add_message_retrieved( $ret1, $ret2, $ret3, $ret4 );

      return ( $query1 ) ? $this->process_message( (object)$message ) : false;
    }

    return false;
  }

  /**
   * ipMessages::load_attachments()
   * 
   * @param mixed $relation_id
   * @return
   */
  public function load_attachments( $relation_id = null ) {
    if ( !$this->userID ) {
      return false;
    }

    global $ipdb;
    $relation_id  = $ipdb->escape( trim( $relation_id ) );

    if ( !$relation_id ) {
      return false;
    }

    if ( checkCache( $relation_id, "load_attachments" ) ) {
      return json_decode( getCache( $relation_id, "load_attachments" ) );
    }

    $results  = $ipdb->get_results( "
      SELECT *
      FROM `$ipdb->attachments`
      WHERE
        relationID = '{$relation_id}'
    " );
    $results  = ( $results ) ? $this->process_attachments( $results ) : false;

    return json_decode( addCache( $relation_id, json_encode( $results ), "load_attachments" ) );
  }

  /**
   * ipMessages::process_attachments()
   * 
   * @param mixed $files
   * @return
   */
  private function process_attachments( $files = null ) {
    if ( !$files || empty( $files ) ) {
      return false;
    }
    if ( isset( $files->mimegroup ) ) {
      $files->extension = trim( strtolower( pathinfo( $files->target, PATHINFO_EXTENSION ) ) );
      $files->readable  = format_file_size( $files->size );
      if ( (int)$files->stream === 0 ) {
        $files->target    = $this->attachment_hash( $files->target );
        $files->thumbnail = $this->attachment_hash( $files->thumbnail, true );
      }
    }
    else {
      foreach( $files as &$file ) {
        $file->extension  = trim( strtolower( pathinfo( $file->target, PATHINFO_EXTENSION ) ) );
        $file->readable   = format_file_size( $file->size );
        if ( (int)$file->stream === 1 ) {
          continue;
        }
        $file->target     = $this->attachment_hash( $file->target );
        $file->thumbnail  = $this->attachment_hash( $file->thumbnail, true );
      }
    }
    return $files;
  }

  /**
   * ipMessages::attachment_hash()
   * 
   * @param mixed $link
   * @return
   */
  private function attachment_hash( $link = null ) {
    $link_base  = md5( $link );
    if ( checkCache( $link, "attachment_hash" ) ) {
      return getCache( $link, "attachment_hash" );
    }
    $thumbnail  = false;
    $filename   = pathinfo( $link, PATHINFO_FILENAME );
    $extension  = pathinfo( $link, PATHINFO_EXTENSION );
    if ( strpos( $filename, "thumb_" ) === 0 ) {
      $thumbnail  = true;
      $filename   = str_replace( "thumb_", "", $filename );
    }
    $parts    = explode( "_", $filename );
    $length   = ( count( $parts ) - 1 );
    if ( $length >= 2 ) {
      $date = $parts[$length];
      $d  = substr( $date, 0, 2 );
      $m  = substr( $date, 2, 2 );
      $y  = substr( $date, -4 );
      if ( $d && $m && $y ) {
        $user   = $parts[$length-1];
        $hash   = $parts[$length-2];
        $reln   = ( isset( $parts[$length-3] ) ) ? $parts[$length-3] : false;
        $query  = array(
          "a" =>  $date,
          "b" =>  $user,
          "c" =>  $reln,
          "d" =>  $hash,
          "e" =>  $extension,
          "f" =>  $thumbnail
        );
        $link = "ipChat/attachment.php?".http_build_query( $query );
      }
    }
    return addCache( $link_base, $link, "attachment_hash" );
  }

  /**
   * ipMessages::load_chat()
   * 
   * @param bool $id
   * @param bool $type
   * @param bool $older
   * @return
   */
  public function load_chat( $id = false, $type = false, $older = false ) {
    if ( !$this->userID ) {
      return false;
    }

    global $ipdb;
    $id   = $ipdb->escape( trim( $id ) );
    $type = $ipdb->escape( trim( $type ) );
    if ( $type === "group" ) {
      $read   = hasConvRead( $id );
      $write  = hasConvWrite( $id );
    }

    if ( !$id || !$type ) {
      return false;
    }
    if ( $type === "group" && !$read ) {
      return false;
    }

    $where    = ( $type === "group" ) ? "groupID = '{$id}'" : "targetID = '{$id}' AND userID = '{$this->userID}'";
    if ( ( $type === "group" && !$write ) && !$older ) {
      $leftID = $ipdb->escape( convLeftId( $id ) );
      if ( !$leftID ) {
        return false;
      }
      $where  .=  " AND ID <= $leftID";
    }
    if ( $older ) {
      $where  .=  " AND ID < '{$older}'";
    }
    $results  = $ipdb->get_results( "
      SELECT *
      FROM (
        SELECT *
        FROM `$ipdb->messages`
        WHERE
          $where
        ORDER BY ID DESC
        LIMIT 20
      ) t
      ORDER BY ID ASC
    " );

    $messages = false;

    if ( $results ) {
      $messages = array( "messages" => array(), "seen" => false );
      loadClass( "ipRelation", "relation.class.php" );
      $relation = new ipRelation( $this->userID );

      if ( !$older ) {
        $ibp  = $results;
        end( $ibp );
        $ibp  = current( $ibp );
        if ( (int)$ibp->sent_from !== (int)$this->userID ) {
          /*if ( (int)$ibp->is_notice === 0 || ( (int)$ibp->is_notice === 1 && (int)$ibp->has_attachment === 1 ) ) {
            $relation->add_seen_time( $this->userID, $id, $type, $ibp->ID );
          }*/
          if ( $type === "group" ) {
            $messages["seen"] = $relation->get_seen_time( $id, $type );
          }
        }
        else {
          $messages["seen"] = $relation->get_seen_time( $id, $type );
        }
        unset( $ibp );
      }
      $relation->drop_message_retrieved( $this->userID, $id, $type );

      foreach( $results as $message ) {
        $messages["messages"][$message->ID] = $this->process_message( $message );
      }
    }

    return $messages;
  }

  /**
   * ipMessages::clearSeen()
   * 
   * @param mixed $id
   * @param mixed $type
   * @return
   */
  public function clearSeen( $id = null, $type = null ) {
    loadClass( "ipRelation", "relation.class.php" );
    $relation = new ipRelation( $this->userID );
    $relation->messageSeen( $this->userID, $id, $type, true );
    return $relation->get_seen_time( $id, $type );
  }

  /**
   * ipMessages::loadHistory()
   * 
   * @param bool $idx
   * @return
   */
  public function loadHistory( $idx = false ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idx    = (int)$ipdb->escape( trim( $idx ) );
    $grps_a = implode( ",", $this->get_user_groups_id( $this->userID ) );
    $grps_i = implode( ",", $this->get_user_groups_id( $this->userID, "inactive" ) );

    $query  = "
    SELECT * FROM (
      SELECT *
      FROM $ipdb->messages
      WHERE
      (
        ( userID = '{$this->userID}' AND groupID = 0 )
        %s
        %s
      )
      ORDER BY ID DESC
    ) t
    GROUP BY groupID, targetID
    ORDER BY ID DESC
    LIMIT $idx, 10";
    $a  = ( !empty( $grps_a ) ) ? "OR ( groupID IN ($grps_a) )" : "";
    $b  = ( !empty( $grps_i ) ) ? "OR ( groupID IN ($grps_i) AND sent_from = '{$this->userID}' )" : "";

    $result = trim( sprintf( $query, $a, $b ) );
    $result = $ipdb->get_results( $result );

    if ( $result ) {
      //$result   = array_reverse( $result );
      $response = array( "messages" => array(), "older" => $idx + 10 );
      loadClass( "ipRelation", "relation.class.php" );
      $rel  = new ipRelation( $this->userID );
      foreach( $result as $message ) {
        $response["messages"][] = $this->process_message( (object)$message, $rel );
      }
      return $response;
    }
    return false;
  }

  /**
   * ipMessages::deleteFile()
   * 
   * @param mixed $idx
   * @return
   */
  public function deleteFile( $idx = null ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idx  = ( is_array( $idx ) ) ? $idx : $ipdb->escape( trim( $idx ) );
    if ( empty( $idx ) ) {
      return false;
    }
    $idx  = ( is_array( $idx ) ) ? "IN (".$ipdb->escape( trim( implode( ",", $idx ) ) ).")" : "= '{$idx}'";
    return $ipdb->query( "DELETE FROM `$ipdb->attachments` WHERE `ID` {$idx}" );
  }

  /**
   * ipMessages::uploadProcess()
   * 
   * @param mixed $file
   * @param mixed $relation_id
   * @param bool $target
   * @param bool $mimetest
   * @param string $prefix
   * @return
   */
  public function uploadProcess( $file = array(), $relation_id = null, $target = false, $mimetest = false, $prefix = "thumb_" ) {
    if ( empty( $file ) || !isset( $file["tmp_name"] ) || !is_uploaded_file( $file["tmp_name"] ) ) {
      return false;
    }
    loadClass( "mime", "header/mime.class.php" );
    $extn = strtolower( pathinfo( $file["name"], PATHINFO_EXTENSION ) );
    $name = $this->makeFilename( $relation_id );
    $size = $file["size"];
    $type = ( $file["type"] == "application/octet-stream" ) ? mime::get( $file["name"] ) : $file["type"];
    $temp = $file["tmp_name"];

    if ( $target !== false ) {
      $target = trim( $target );
      $target = trim( $target, "/" );
      $target = trim( $target, DIRECTORY_SEPARATOR );
    }

    $root = ( $target ) ? array( realpath( ROOT_DIR.$target ).DIRECTORY_SEPARATOR, $target."/" ) : $this->makeUploadFolder();
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
   * ipMessages::uploadFile()
   * 
   * @param mixed $file
   * @param mixed $relation_id
   * @return
   */
  public function uploadFile( $file = array(), $relation_id = null ) {
    if ( !$this->userID ) {
      return false;
    }
    if ( empty( $file ) || !isset( $file["tmp_name"] ) || !is_uploaded_file( $file["tmp_name"] ) ) {
      return false;
    }
    global $ipdb;
    $relation_id  = $ipdb->escape( trim( $relation_id ) );

    loadClass( "mime", "header/mime.class.php" );
    $extn = strtolower( pathinfo( $file["name"], PATHINFO_EXTENSION ) );
    $name = $this->makeFilename( $relation_id );
    $size = $file["size"];
    $type = ( $file["type"] == "application/octet-stream" ) ? mime::get( $file["name"] ) : $file["type"];
    $temp = $file["tmp_name"];

    $root = $this->makeUploadFolder();
    $path = $root[0].$name.".".$extn;

    $link = $root[1].$name.".".$extn;
    $thmb = null;
    $mgrp = "unknown";

    if ( !move_uploaded_file( $temp, $path ) ) {
      return false;
    }

    $type = ( $type == "application/octet-stream" ) ? mime::get( $path ) : $type;

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
        if ( $phpThumb->RenderToFile( $root[0]."thumb_".$name.".png" ) ) {
          $thmb = $root[1]."thumb_".$name.".png";
        }
        $phpThumb->purgeTempFiles();
      }
    }
    else {
      $mgrp = $this->getFileMimeGroup( $extn );
    }

    $name = $ipdb->escape( pathinfo( $file["name"], PATHINFO_FILENAME ) );
    $size = $ipdb->escape( $size );
    $type = $ipdb->escape( $type );
    $link = $ipdb->escape( $link );
    $thmb = $ipdb->escape( $thmb );
    $mgrp = $ipdb->escape( $mgrp );
    $date = $ipdb->escape( time() );

    $query  = $ipdb->query( "INSERT INTO `$ipdb->attachments`(`userID`, `relationID`, `title`, `thumbnail`, `size`, `upload_date`, `target`, `mimetype`, `mimegroup`) VALUES ('{$this->userID}','{$relation_id}','{$name}','{$thmb}','{$size}','{$date}','{$link}','{$type}','{$mgrp}')" );
    if ( $query ) {
      $insert = $ipdb->escape( $ipdb->insert_id );
      $data = array(
        "message"     =>  $this->get_message_by_col( array( "relationID" => $relation_id, "sent_from" => $this->userID ) ),
        "attachment"  =>  $this->process_attachments( $this->get_file_by_col( "ID", $insert ) )
      );
      return $data;
    }

    return false;
  }

  /**
   * ipMessages::getFileMimeGroup()
   * 
   * @param mixed $extn
   * @return
   */
  public function getFileMimeGroup( $extn = null ) {
    $extn = trim( $extn );
    switch( $extn ) {
      case ( in_array( $extn, array( "mp3", "wma", "amr", "wav" ) ) ):
        return "audio";
      break;
      case ( in_array( $extn, array( "zip", "rar", "7z", "tar", "gz" ) ) ):
        return "archive";
      break;
      case ( in_array( $extn, array( "doc", "docx", "pdf", "rtf", "ppt", "xls" ) ) ):
        return "document";
      break;
      case ( in_array( $extn, array( "js", "php", "txt", "htm", "html", "cgi" ) ) ):
        return "text";
      break;
      case ( in_array( $extn, array( "fxg", "ai", "cdr", "eps", "ait", "svg" ) ) ):
        return "vector";
      break;
      default:
        return "text";
      break;
    }
  }

  /**
   * ipMessages::uploadSream()
   * 
   * @param mixed $stream
   * @return
   */
  public function uploadSream( $stream = array() ) {
    if ( !$this->userID ) {
      return false;
    }
    $stream = (object)$stream;
    if ( empty( $stream ) || !isset( $stream->title ) ) {
      return false;
    }
    global $ipdb;

    $title      = $ipdb->escape( trim( strip_tags( $stream->title ) ) );
    $thumbnail  = ( isset( $stream->thumb ) && !empty( $stream->thumb ) ) ? $ipdb->escape( trim( $stream->thumb ) ) : null;
    $uploaded   = $ipdb->escape( time() );
    $target     = $ipdb->escape( $stream->target );
    $mimetype   = "application/octet-stream";
    $mimegroup  = "stream";
    $subtitle   = $ipdb->escape( trim( strip_tags( $stream->subtitle ) ) );
    $summary    = $ipdb->escape( trim( strip_tags( $stream->summary ) ) );

    $query  = $ipdb->query( "
      INSERT INTO `$ipdb->attachments`
        (`userID`, `relationID`, `title`, `subtitle`, `summary`, `thumbnail`, `size`, `upload_date`, `target`, `mimetype`, `mimegroup`, `stream`)
      VALUES
        ('{$this->userID}','','{$title}','{$subtitle}','{$summary}','{$thumbnail}','0','{$uploaded}','{$target}','{$mimetype}','{$mimegroup}','1')
    " );
    if ( $query ) {
      $insert = $ipdb->escape( $ipdb->insert_id );
      $data   = array(
        "message"     =>  false,
        "attachment"  =>  $this->process_attachments( $this->get_file_by_col( "ID", $insert ) )
      );
      return $data;
    }

    return false;
  }

  /**
   * ipMessages::get_message_by_col()
   * 
   * @param mixed $idx
   * @param mixed $idn
   * @return
   */
  private function get_message_by_col( $idx = null, $idn = null ) {
    global $ipdb;
    $idm  = null;
    if ( is_array( $idx ) ) {
      $idm  = array();
      foreach( $idx as $k => $v ) {
        $idm[]  = "`".$ipdb->escape( trim( $k ) )."` = '".$ipdb->escape( trim( $v ) )."'";
      }
      $idm  = implode( " AND ", $idm );
    }
    else {
      $idm  = "`".$ipdb->escape( trim( $idx ) )."` = '".$ipdb->escape( trim( $idn ) )."'";
    }

    $message  = $ipdb->get_row( "SELECT * FROM `$ipdb->messages` WHERE $idm" );
    if ( $message ) {
      return $this->process_message( $message );
    }
    return false;
  }
  /**
   * ipMessages::get_file_by_col()
   * 
   * @param string $idx
   * @param mixed $idn
   * @return
   */
  private function get_file_by_col( $idx = "ID", $idn = null ) {
    global $ipdb;
    $idx  = $ipdb->escape( trim( $idx ) );
    $idn  = $ipdb->escape( trim( $idn ) );
    return $ipdb->get_row( "SELECT * FROM `$ipdb->attachments` WHERE `$idx` = '{$idn}'" );
  }

  /**
   * ipMessages::makeUploadFolder()
   * 
   * @return
   */
  private function makeUploadFolder() {
    $root = ROOT_DIR."ipChat/uploads/".date( "Y/m/d/" );
    if ( !file_exists( $root ) ) {
      mkdir( $root, 0755, true );
    }
    return array( realpath( $root ).DIRECTORY_SEPARATOR, "ipChat/uploads/".date( "Y/m/d/" ) );
  }
  /**
   * ipMessages::makeFilename()
   * 
   * @param mixed $relation_id
   * @return
   */
  private function makeFilename( $relation_id = null ) {
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

    $name .=  "_".$this->userID."_".date("dmY");

    return ( ( $relation_id ) ? $relation_id."_" : "" ).$name;
  }

  /**
   * ipMessages::doRelationMessage()
   * 
   * @param mixed $relation_id
   * @param mixed $idx
   * @param mixed $idn
   * @return
   */
  public function doRelationMessage( $relation_id = null, $idx = null, $idn = null ) {
    if ( !$this->userID ) {
      return false;
    }

    global $ipdb;
    $relation_id  = $ipdb->escape( trim( $relation_id ) );
    $idx  = $ipdb->escape( trim( $idx ) );
    $idn  = $ipdb->escape( trim( $idn ) );

    if ( !$relation_id || !$idx || !$idn ) {
      return false;
    }

    $exists = $ipdb->get_var( "SELECT COUNT(*) FROM $ipdb->messages WHERE relationID = '{$relation_id}'" );
    if ( !$exists ) {
      $message  = $this->message_skeleton();
      if ( $idn === "group" ) {
        $message["groupID"]   = $message["sent_to"] = $idx;
      }
      else {
        $message["targetID"]  = $message["sent_to"] = $idx;
      }
      $message["relationID"]      = $relation_id;
      $message["has_attachment"]  = '1';
      $message["is_notice"] = '1';
      $message["notice_section"]  = 'attachment';

      return $this->send( $message );
    }
    return true;
  }

  /**
   * ipMessages::updateGroup()
   * 
   * @param bool $idx
   * @param mixed $idn
   * @return
   */
  public function updateGroup( $idx = false, $idn = array() ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idx  = $ipdb->escape( trim( $idx ) );
    if ( !$idx || empty( $idn ) ) {
      return false;
    }
    $added  = array();
    foreach( $idn as &$user ) {
      $user = (int)$user;
      $idqx = $ipdb->query( "INSERT IGNORE INTO $ipdb->groups_rel(groupID, userID) VALUES('{$idx}','{$user}')" );
      if ( $idqx ) {
        $added[]  = $user;
      }
    }
    if ( empty( $added ) ) {
      return;
    }
    loadClass( "ipRelation", "relation.class.php" );
    $relation = new ipRelation( $this->userID );
    if ( $group = $relation->getGroupInfo( $idx, true ) ) {
      $message  = $this->message_skeleton();
      $message["groupID"]   = $idx;
      $message["is_notice"] = '1';
      $message["notice_section"]  = 'added';
      if ( count( $added ) === 1 ) {
        $message["message_o"] = '[[[%'.$this->userID.'%]]] added [[[%'.$added[0].'%]]].';
      }
      elseif ( count( $added ) === 2 ) {
        $message["message_o"] = '[[[%'.$this->userID.'%]]] added [[[%'.$added[0].'%]]] and [[[%'.$added[1].'%]]].';
      }
      else {
        $message["message_o"] = '[[[%'.$this->userID.'%]]] added [[[%'.$added[0].'%]]], [[[%'.$added[1].'%]]] and ***'.( count( $added ) - 2 ).'*** more.';
      }
  
      $send = $this->send( $message, true );
      if ( $send ) {
        $relation->drop_seen_time( $this->userID, $idx, "group" );
        return array( "message" => $send, "group" => $group );
      }
    }
  }

  /**
   * ipMessages::get_user_groups_id()
   * 
   * @param bool $idx
   * @param string $idn
   * @return
   */
  public function get_user_groups_id( $idx = false, $idn = "active" ) {
    global $ipdb;
    $idx  = $ipdb->escape( trim( $idx ) );
    $idn  = $ipdb->escape( trim( $idn ) );
    if ( !$idx || !$idn ) {
      return array();
    }
    $groups = $ipdb->get_results( "SELECT groupID FROM $ipdb->groups_rel WHERE userID = '{$idx}' AND status = '{$idn}'" );
    if ( $groups ) {
      $groups_id  = array();
      foreach( $groups as $id ) {
        $groups_id[]  = $id->groupID;
      }
      return $groups_id;
    }
    return array();
  }

  /**
   * ipMessages::nameGroup()
   * 
   * @param bool $idx
   * @param mixed $idn
   * @return
   */
  public function nameGroup( $idx = false, $idn = null ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idx  = $ipdb->escape( trim( $idx ) );
    $idn  = $ipdb->escape( trim( $idn ) );
    if ( !$idx || !$idn ) {
      return false;
    }
    $rename = $ipdb->query( "UPDATE $ipdb->groups SET name = '{$idn}' WHERE ID = '{$idx}'" );
    if ( $rename ) {
      loadClass( "ipRelation", "relation.class.php" );
      $relation = new ipRelation( $this->userID );
      if ( $group = $relation->getGroupInfo( $idx, true ) ) {
        $message  = $this->message_skeleton();
        $message["groupID"]   = $idx;
        $message["is_notice"] = '1';
        $message["notice_section"]  = 'naming';
        $message["message_o"] = '[[[%'.$this->userID.'%]]] named the conversation: ***'.$idn.'***.';
  
        $send = $this->send( $message, true );
        if ( $send ) {
          $relation->drop_seen_time( $this->userID, $idx, "group" );
          return array( "message" => $send, "group" => $group );
        }
      }
    }
    return false;
  }

  /**
   * ipMessages::leaveGroup()
   * 
   * @param bool $idx
   * @return
   */
  public function leaveGroup( $idx = false ) {
    if ( !$this->userID ) {
      return false;
    }
    global $ipdb;
    $idx  = $ipdb->escape( trim( $idx ) );
    if ( !$idx ) {
      return false;
    }
    $leave  = $ipdb->query( "UPDATE $ipdb->groups_rel SET status = 'inactive' WHERE userID = '{$this->userID}' AND groupID = '{$idx}'" );
    if ( $leave ) {
      loadClass( "ipRelation", "relation.class.php" );
      $relation = new ipRelation( $this->userID );
      if ( $group = $relation->getGroupInfo( $idx ) ) {
        $message  = $this->message_skeleton();
        $message["groupID"]   = $idx;
        $message["is_notice"] = '1';
        $message["notice_section"]  = 'left';
        $message["message_o"] = '[[[%'.$this->userID.'%]]] left the conversation';
  
        $send = $this->send( $message, true );
        if ( $send ) {
          $relation->drop_seen_time( $this->userID, $idx, "group" );
          return array( "message" => $send, "group" => $group );
        }
      }
    }
    return false;
  }

  /**
   * ipMessages::load_attachment()
   * 
   * @param bool $id
   * @return
   */
  public function load_attachment( $id = false ) {
    if ( !$this->userID ) {
      return false;
    }

    global $ipdb;
    $id = $ipdb->escape( trim( $id ) );
    if ( !$id ) {
      return false;
    }
  }

  /**
   * ipMessages::process_message()
   * 
   * @param bool $message
   * @param bool $seen
   * @return
   */
  public function process_message( $message = false, $seen = false ) {
    global $ipLang;
    $message->has_attachment  = (int)$message->has_attachment;
    $message->is_notice = (int)$message->is_notice;
    $message->sent_to   = (int)$message->sent_to;
    $message->sent_from = (int)$message->sent_from;
    $message->userID = (int)$message->userID;
    $message->targetID = (int)$message->targetID;
    $message->groupID = (int)$message->groupID;
    $message->sent_date = (int)$message->sent_date;
    if ( $seen !== false ) {
      $idx  = ( $message->sent_from === $this->userID ) ? $message->sent_to : $message->sent_from;
      $idn  = ( $message->groupID === 0 ) ? "user" : "group";
      $message->seen = $seen->get_seen_time( $idx, $idn );
    }
    $message->is_sticker  = $this->is_sticker( $message->message );
    
    if ( $message->has_attachment === 1 ) {
      $message->attachments = $this->load_attachments( $message->relationID );
    }
    $message->message = $this->format_message( $message->message );

    loadClass( "ipUsers", "users.class.php" );
    if ( (int)$message->sent_from !== $this->userID ) {
      $user = new ipUsers( $this->userID );
      $user = $user->get_user( $message->sent_from, "ID", "name" );
      $user = ( !$user ) ? "undefined" : $user;
    }
    else{
      $user = $ipLang->translate( "YOU" );
    }
    $message->message = $this->replace_usernames( $message->message );
    //$message->message = preg_replace( "/\{\{\{\%name\%\}\}\}/", "<strong>".$user."</strong>", $message->message );
    //$message->message = preg_replace( "/\*\*\*(.+?)\*\*\*/", "<strong>$1</strong>", $message->message );

    return $message;
  }

  /**
   * ipMessages::replace_usernames()
   * 
   * @param mixed $message
   * @return
   */
  private function replace_usernames( $message = null ) {
    global $ipLang;
    $found    = preg_match_all( "/\[\[\[\%(\d+)\%\]\]\]/", $message, $matches );
    $users_id = array();
    if ( $found ) {
      if ( srm( "request_sd" ) !== "en" ) {
        if ( stristr( $message, "named the conversation:" ) !== false ) {
          $message  = explode( " named the conversation: ", $message );
          $message  = sprintf( $ipLang->translate( "CONV_NAMED_TO" ), $message[0], $message[1] );
        }
        elseif ( preg_match( '/^(\[\[\[\%(\d+)\%\]\]\] added \[\[\[\%(\d+)\%\]\]\]\, \[\[\[\%(\d+)\%\]\]\] and \*\*\*(\d+)\*\*\* more\.)$/', $message ) ) {
          preg_match( '/\*\*\*(\d+)\*\*\*/', $message, $more_count );
          $message  = sprintf( $ipLang->translate( "GROUP_USER_ADD3" ), $matches[0][0], $matches[0][1], $matches[0][2], $more_count[0] );
          unset( $more_count );
        }
        elseif ( preg_match( '/^(\[\[\[\%(\d+)\%\]\]\] added \[\[\[\%(\d+)\%\]\]\] and \[\[\[\%(\d+)\%\]\]\]\.)$/', $message ) ) {
          $message  = sprintf( $ipLang->translate( "GROUP_USER_ADD2" ), $matches[0][0], $matches[0][1], $matches[0][2] );
        }
        elseif ( preg_match( '/^(\[\[\[\%(\d+)\%\]\]\] added \[\[\[\%(\d+)\%\]\]\]\.)$/', $message ) ) {
          $message  = sprintf( $ipLang->translate( "GROUP_USER_ADD1" ), $matches[0][0], $matches[0][1] );
        }
        elseif ( preg_match( '/^(\[\[\[\%(\d+)\%\]\]\] left the conversation)$/', $message ) ) {
          $message  = sprintf( $ipLang->translate( "LEFT_CONV" ), $matches[0][0] );
        }
      }
      foreach( $matches[1] as $match ) {
        $users_id[] = $match;
      }
      loadClass( "ipUsers", "users.class.php" );
      $users_name = ipUsers::get_users( $this->userID, $users_id, "ID", false, "name" );
      if ( $users_name ) {
        foreach( $users_name["name"] as $user_id => $user_name ) {
          $user_name  = ( $this->userID === (int)$user_id ) ? $ipLang->translate( "YOU" ) : $user_name;
          $message    = str_replace( "[[[%".$user_id."%]]]", "<a data-chat=\"{$user_id}\"><strong>".$user_name."</strong></a>", $message );
        }
      }
      $message  = preg_replace( "/\[\[\[\%(\d+)\%\]\]\]/", "<strong>undefined</strong>", $message );
    }
    $message  = preg_replace( "/\*\*\*(.+?)\*\*\*/", "<strong>$1</strong>", $message );
    return $message;
  }

  /**
   * ipMessages::format_message()
   * 
   * @param string $message
   * @return
   */
  public function format_message( $message = '' ) {
    $message  = htmlspecialchars( trim( $message ) );
    $message  = ' '.$this->emoticons( $message );

    if ( preg_match( $this->ext_url, $message ) ) {
      $message  = preg_replace( $this->ext_url, '<a href="$1" alt="External Link">$1</a>', $message );
    }

    $message  = ' '.$this->emoji( $message );

    if ( preg_match( $this->fb_code, $message ) ) {
      $message  = preg_replace( $this->fb_code, ' <img src="https://graph.facebook.com/$1/picture" height="16" alt="External Image">', $message );
    }

    $message  = nl2br( $message );
    return trim( $message );
  }

  /**
   * ipMessages::emoticons()
   * 
   * @param string $message
   * @return
   */
  public function emoticons( $message = '' ) {
    if ( empty( $message ) ) {
      return $message;
    }
    $emoticons  = ipSmilies::load();
    $message    = ' '.trim( $message );
    foreach( $emoticons as $id => $emoticon ) {
      if ( $emoticon["emoji"] === true ) {
        continue;
      }
      $codes  = $emoticon["data"];
      foreach( $codes as $code => $map ) {
        $search = array_map(function( $str ) {
          return ' '.$str;
        }, $map);
        $replace  = ' <span class="emoticon '.$id.' '.$id.'_'.$code.'"></span>';
        $message  = str_replace( $search, $replace, $message );
      }
    }
    return trim( $message );
  }

  /**
   * ipMessages::is_sticker()
   * 
   * @param mixed $text
   * @return
   */
  private function is_sticker( $text = null ) {
    return preg_match( $this->emoj_reg, $text );
  }
  /**
   * ipMessages::emoji()
   * 
   * @param string $message
   * @return
   */
  public function emoji( $message = '' ) {
    $message  = trim( $message );
    if ( empty( $message ) ) {
      return $message;
    }
    $sticker  = ipSmilies::load();
    if ( preg_match( $this->emoj_reg, $message, $match ) ) {
      $idx  = trim( $match[1] );
      $ind  = trim( $match[2] );
      if ( isset( $sticker[$idx] ) && isset( $sticker[$idx]["data"] ) && isset( $sticker[$idx]["data"][$ind] ) ) {
        $message  = '<div class="_55r0"><div><img class="mvs sticker sticker_'.$idx.' sticker_'.$idx.'_'.$ind.'" src="'.$sticker[$idx]["data"][$ind].'" width="'.$sticker[$idx]["imgw"].'" height="'.$sticker[$idx]["imgh"].'" alt="'.( ( is_numeric( $ind ) ) ? $idx : $ind ).'"></div></div>';
      }
      else {
        $message  = '<div class="_55r00"><span>Sticker missing</span></div>';
      }
    }
    return $message;
  }

  /**
   * ipMessages::message_skeleton()
   * 
   * @return
   */
  private function message_skeleton() {
    $message  = $this->message;
    $message["userID"]      = $message["sent_from"] = $this->userID;
    $message["sent_date"]   = time();
    $message["datetime"]    = date( "Y-m-d", time() );
    $message["timestamp"]   = date( "Y-m-d H:i:s", time() );
    $message["relationID"]  = uniqid( "rel_" );
    return $message;
  }
}

?>
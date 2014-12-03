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
  require_once( dirname( __FILE__ )."/header.class.php" );
}
if ( !class_exists( "mime" ) ) {
  require_once( dirname( __FILE__ )."/mime.class.php" );
}

class ipDownloader {
  private $file = false;
  private $name = false;
  private $mime = false;
  private $size = 0;

  private $f_single = false;
  private $mt_range = 0; 

  private $header = false;

  function __construct( $file = false, $name = false, $mime = "application/octet-stream", $f_single = false ) {
    $this->header = new ipHeader;

    if ( $file !== false ) {
      $this->set_file( $file );
    }
    $this->set_name( $name );
    $this->set_mime( $mime );
    $this->f_single = $f_single;
	}

  public function set_file( $file = false ) {
    $file = realpath( $file );

    if ( !$file || !file_exists( $file ) ) {
      $this->header->content_mime( ( $this->mime ) ? $this->mime : mime::get( $file ) );
      $this->header->_404();
      exit();
    }

    if ( !is_readable( $file ) ) {
      $this->header->content_mime( ( $this->mime ) ? $this->mime : mime::get( $file ) );
      $this->header->_500();
      exit();
    }

    $this->file = $file;
    $this->size = filesize( $file );

    /*if ( ini_get( "safe_mode" ) ) {
      throw new Exception(
        "ipDownloader is not be able to handle large files while safe mode is enabled"
      );
    }*/

    return $this;
  }

  public function set_name( $name = false ) {
    $name = trim( $name );

    if ( !empty( $name ) ) {
      $this->name = $name;
      return $this;
    }

    $this->name = basename( $this->file );
    return $this;
  }

  public function set_mime( $mime = "application/octet-stream" ) {
    $mime = trim( $mime );

    if ( $mime === "detect" ) {
      $this->mime = mime::get( $this->file );
      return $this;
    }

    $this->mime = $mime;
    return $this;
  }

  private function prepare() {
    if ( ini_get( "zlib.output_compression" ) ) {
      ini_set( "zlib.output_compression", "Off" );
    }

    $this->header->content_mime( $this->mime );
    $this->header->disposition( $this->name, "attachment" );

    if ( isset( $_SERVER["HTTP_RANGE"] ) && !$this->f_single ) {
      list( $a, $range )  = explode( "=", $_SERVER["HTTP_RANGE"], 2 );
      list( $range )      = explode( ",", $range, 2 );

      list( $range, $range_end )  = explode( "-", $range );

      $range  = intval( $range );

      if ( !$range_end ) {
        $range_end  = ( $this->size - 1 );
      }
      else {
        $range_end  = intval( $range_end );
      }

      $new_length = ( $range_end - $range + 1 );

      $this->header->content_range( $new_length, $range, $range_end, $size );
      $this->mt_range = $range;
    }
    else {
      $new_length = $this->size;
      $this->header->content_length( $this->size );
    }

    return $new_length;			
  }

  public function download( $file = false, $name = false, $mime = false ) {
    if ( $file !== false ) {
      $this->set_file( $file );
    }
    if ( $name !== false ) {
      $this->set_name( $file );
    }
    if ( $mime !== false ) {
      $this->set_mime( $file );
    }

    set_time_limit( 0 );

    $block_size = $this->prepare();

    $chunksize  = ( 1 * ( 1024 * 1024 ) );
    $bytes_send = 0;

    if ( $file = fopen( $this->file, "r" ) ) {
      if ( isset( $_SERVER["HTTP_RANGE"] ) && !$this->f_single ) {
        fseek( $file, $this->mt_range );
      }

      while( !feof( $file ) && !connection_aborted() && ( $bytes_send < $block_size ) ) {
        $buffer = fread( $file, $chunksize );
        echo $buffer;
        flush();
        $bytes_send +=  strlen( $buffer );
      }

      fclose( $file );
    } 
    else {
      $this->header->content_mime( ( $this->mime ) ? $this->mime : mime::get( $file ) );
      $this->header->_500();
    }
    exit();
  }
}
?>
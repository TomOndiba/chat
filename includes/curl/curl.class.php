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

class ipCurl { 
  private $ch   = null;
  private $url  = null;

  private $_error   = 0;
  private $_errmsg  = null;
  private $_header  = null;
  private $_webpage = null;
  private $_status  = 0;

  public function __construct( $url = null ) {
    $this->url  = $url;

    if ( !function_exists( "curl_init" ) ) {
      throw new Exception( "Fatal Error: Module 'Curl' is not installed properly" );
    }

    $this->ch = curl_init();

    curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $this->ch, CURLOPT_CAINFO, dirname(__FILE__)."/cacert.pem" );

    $this->freshConnect();
    $this->forbidReuse();
    $this->setTimout( 40 );
    $this->setConnTimout( 30 );
    $this->followLocation();
    $this->setMaxRedirects( 4 );
    $this->excludeHeader();
    $this->includeBody();
    $this->verifySSL( true );
    $this->setBinaryTransfer();
    $this->setReferer( $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"] );
    $this->setUserAgent();

    return $this;
  }

  public function freshConnect( $option = false ) {
    curl_setopt( $this->ch, CURLOPT_FRESH_CONNECT, $option );
    return $this;
  }

  public function forbidReuse( $option = false ) {
    curl_setopt( $this->ch, CURLOPT_FORBID_REUSE, $option );
    return $this;
  }

  public function __destruct() {
    curl_close( $this->ch );
    $this->ch = null;
	}

  public function setReadCallback( $callback = null ) {
    curl_setopt( $this->ch, CURLOPT_WRITEFUNCTION, $callback );
    return $this;
  }

  public function setProgressCallback( $callback = null, $buffer = 128 ) {
    curl_setopt( $this->ch, CURLOPT_NOPROGRESS, false );
    curl_setopt( $this->ch, CURLOPT_PROGRESSFUNCTION, $callback );
    curl_setopt( $this->ch, CURLOPT_BUFFERSIZE, $buffer );
    return $this;
  }

  public function includeHeader() {
    curl_setopt( $this->ch, CURLOPT_HEADER, true );
    return $this;
  }
  public function excludeHeader() {
    curl_setopt( $this->ch, CURLOPT_HEADER, false );
    return $this;
  }

  public function includeBody() {
    curl_setopt( $this->ch, CURLOPT_NOBODY, false );
    return $this;
  }
  public function excludeBody() {
    curl_setopt( $this->ch, CURLOPT_NOBODY, true );
    return $this;
  }

  public function setMaxRedirects( $redirects = 4 ) {
    if ( $this->is_safe_mode() ) {
      return $this;
    }
    curl_setopt( $this->ch, CURLOPT_MAXREDIRS, $redirects );
    return $this;
  }

  public function followLocation() {
    if ( $this->is_safe_mode() ) {
      return $this->unfollowLocation();
    }
    curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, true );
    return $this;
  }
  public function unfollowLocation() {
    curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, false );
    return $this;
  }

  public function setReferer( $referer = null ) {
    curl_setopt( $this->ch, CURLOPT_REFERER, $referer );
    return $this;
  }

  public function setBinaryTransfer( $binary = false ) {
    curl_setopt( $this->ch, CURLOPT_BINARYTRANSFER, $binary );
    return $this;
  }

  public function setTimout( $timeout ) {
    curl_setopt( $this->ch, CURLOPT_TIMEOUT, $timeout );
    return $this;
  }

  public function setConnTimout( $timeout ) {
    curl_setopt( $this->ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    return $this;
  }

  public function setUserAgent( $userAgent = null ) {
    $userAgent  = ( !$userAgent ) ? "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31" : $userAgent;
    curl_setopt( $this->ch, CURLOPT_USERAGENT, $userAgent );
    return $this;
  }

  public function setProxy( $url = null, $port = 0, $username = null, $password = null ) {
    curl_setopt( $this->ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC );
    curl_setopt( $this->ch, CURLOPT_PROXY, $url.( ( $port ) > 0 ? ":".$port : null ) );

    if ( $port > 0 ) {
      curl_setopt( $this->ch, CURLOPT_PROXYPORT, $port );
    }

    if ( $username ) {
      curl_setopt( $this->ch, CURLOPT_PROXYUSERPWD, $username.":".$password );
    }

    return $this;
	}

  public function setAuth( $username = null, $password = null ) {
    curl_setopt( $this->ch, CURLOPT_USERPWD, $username.':'.$password );
    curl_setopt( $this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
    return $this;
	}

  public function setCookiFile( $file = "cookie.txt" ) {
    if ( !$file ) {
      return $this;
    }
    curl_setopt( $this->ch, CURLOPT_COOKIEJAR, $file );
    curl_setopt( $this->ch, CURLOPT_COOKIEFILE, $file );
    return $this;
  }

  public function verifySSL( $ssl = false ) {
    if ( !$ssl ) {
      curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, false );
      curl_setopt( $this->ch, CURLOPT_SSL_VERIFYHOST, 2 );
    }
    else {
      curl_setopt( $this->ch, CURLOPT_SSL_VERIFYPEER, true );
    }
    return $this;
  }

  public function setPost( $postFields = null, $keep_array = false ) {
    if ( is_array( $postFields ) && !$keep_array ) {
      $postFields = http_build_query( $postFields );
    }

    curl_setopt( $this->ch, CURLOPT_POST, true );
    curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $postFields );

    return $this;
  }

  public function setFile( $file = null ) {
    if ( $file !== null ) {
      $file = realpath( $file );
    }
    if ( $file && is_readable( $file ) ) {
      $fp = fopen( $file, "w" );
      curl_setopt( $this->ch, CURLOPT_FILE, $fp );
    }

    return $this;
  }

  public function setHeader( $header = array( "Expect:" ) ) {
    curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $header );
    return $this;
  }

  public function createCurl( $url = null ) {
    $url  = ( $url ) ? trim( $url ) : trim( $this->url );

    if ( !$url ) {
      throw new Exception( "Fatal Error: you must provide a valid url before calling 'createCurl'" );
    }
    curl_setopt( $this->ch, CURLOPT_URL, $url );

    $this->_webpage = curl_exec( $this->ch );
    $this->_status  = (int)curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );
    $this->_error   = (int)curl_errno( $this->ch );
    $this->_errmsg  = curl_error( $this->ch );
    $this->_header  = curl_getinfo( $this->ch );

    if ( !$this->_errmsg ) {
      $this->_errmsg  = $this->parse_http_code( $this->_status );
    }

    return $this;
  }

  private function parse_http_code( $code = 404 ) {
    $code = (int)$code;
    if ( !class_exists( "ipStatusCodes" ) ) {
      return null;
    }
    return ipStatusCodes::info( $code );
  }

  private function is_safe_mode() {
    return ( @ini_get( 'open_basedir' ) != '' && @ini_get( 'safe_mode' ) != 'Off' );
  }

  public function getStatus() {
    return $this->_status;
  }

  public function getResponse() {
    return $this->_webpage;
  }

  public function getHeader() {
    return $this->_header;
  }

  public function getError() {
    return $this->_error;
  }

  public function getErrorMessage() {
    return $this->_errmsg;
  }
}
?>
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
 * ipLanguage
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipLanguage {
  private $lang_idx;
  private $lang_idn;
  private $lang_idl;
  private $lang_cache;
  

  /**
   * ipLanguage::__construct()
   * 
   * @param string $lang
   * @return
   */
  public function __construct( $lang = "en" ) {
    $this->lang_idn = realpath( dirname( dirname( __FILE__ ) )."/languages" ).DIRECTORY_SEPARATOR;
    $this->read_language( $lang );
  }

  /**
   * ipLanguage::set_language()
   * 
   * @param string $lang
   * @return
   */
  public function set_language( $lang = "en" ) {
    $this->lang_idx = $lang;
  }

  /**
   * ipLanguage::read_language()
   * 
   * @param mixed $lang
   * @return
   */
  public function read_language( $lang = null ) {
    if ( $lang ) {
      $this->set_language( $lang );
    }
    if ( !$this->lang_idx ) {
      $this->lang_idx = "en";
    }
    $lang_idx = realpath( $this->lang_idn.$this->lang_idx.".lng" );
    if ( $lang_idx && is_readable( $lang_idx ) ) {
      $content  = implode( "", file( $lang_idx ) );
      $content  = str_replace( "\xEF\xBB\xBF", "", $content );
      $content  = json_decode( $content );
      if ( $content && is_object( $content ) ) {
        $this->lang_idl = $content;
      }
    }
    unset( $lang_idx, $content );
  }

  /**
   * ipLanguage::write_language()
   * 
   * @return
   */
  public function write_language() {

  }

  /**
   * ipLanguage::reading_languages()
   * 
   * @return
   */
  public function reading_languages() {
    $languages  = (array)glob( $this->lang_idn."*.lng" );
    $languages  = array_map( function( $language ) {
      return ( pathinfo( $language, PATHINFO_EXTENSION ) === "lng" ) ? pathinfo( $language, PATHINFO_FILENAME ) : null;
    }, $languages );
    $languages  = $this->manipulate( $languages );
    return $languages;
  }
  /**
   * ipLanguage::writing_languages()
   * 
   * @return
   */
  public function writing_languages() {
    $languages  = array_keys( $this->ime_list() );
    $languages  = $this->manipulate( $languages );
    return $languages;
  }

  /**
   * ipLanguage::manipulate()
   * 
   * @param mixed $instance
   * @return
   */
  private function manipulate( $instance = array() ) {
    $instance   = array_filter( $instance );
    $languages  = array();

    foreach( $instance as $language ) {
      $languages[$language] = $this->lang_code_to_name( $language );
    }

    return $languages;
  }

  /**
   * ipLanguage::lang_code_to_name()
   * 
   * @param mixed $lang
   * @return
   */
  public function lang_code_to_name( $lang = null ) {
    $lang   = trim( strtolower( $lang ) );
    if ( !$this->lang_cache ) {
      $lfile  = realpath( dirname( __FILE__ ).DIRECTORY_SEPARATOR."lang_names.json" );
      if ( !$lfile || !is_readable( $lfile ) ) {
        return strtoupper( $lang );
      }
      $names  = $this->lang_cache = json_decode( implode( "", file( $lfile ) ), true );
    }
    else {
      $names  = $this->lang_cache;
    }

    /*$names["am"]  = "Amharic";
    $names["or"]  = "Oriya";
    $names["pa"]  = "Punjabi";
    $names["sa"]  = "Sanskrit";
    $names["si"]  = "Sinhala";
    $names["ti"]  = "Tigrinya";

    if ( isset( $lfile ) ) {
      ksort( $names );
      file_put_contents( $lfile, json_encode( $names ) );
    }*/

    if ( !$names ) {
      return strtoupper( $lang );
    }
    
    return ( isset( $names[$lang] ) ) ? $names[$lang] : strtoupper( $lang );
  }

  /**
   * ipLanguage::ime_list()
   * 
   * @param bool $language
   * @param bool $index
   * @return
   */
  public function ime_list( $language = false, $index = false ) {
    $language   = trim( strtolower( $language ) );
    $languages  = array(
    	'am' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'ar' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'bn' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'zh' =>  array( 'text' => null, 'ime' => 'pinyin', 'num' => 10, 'cp' => 0, 'cs' => 0, 'ie' => 'utf-8','oe' => 'utf-8','app' => 'jsapi'),
    	'el' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'gu' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'hi' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'kn' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'ml' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'mr' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'or' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'fa' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'pa' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'ru' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'sa' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'sr' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'si' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'ta' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'te' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'ti' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi'),
    	'ur' =>  array( 'text' => null, 'ime' => 'transliteration_en_%s', 'num' => 5,'cp' => 0, 'cs' => 0, 'ie' => 'utf-8', 'oe' => 'utf-8', 'app' => 'jsapi')
    );

    if ( $language && isset( $languages[$language] ) ) {
      if ( $index ) {
        return ( isset( $languages[$language][$index] ) ) ? $languages[$language][$index] : false;
      }
      else {
        return $languages[$language];
      }
    }

    if ( func_num_args() === 0 ) {
      $languages["en"]  = null;
      return $languages;
    }

    return false;
  }

  /**
   * ipLanguage::list_language()
   * 
   * @param bool $lang
   * @return
   */
  public function list_language( $lang = false ) {
    if ( !$this->lang_idl ) {
      $this->read_language( $lang );
    }
    return (object)$this->lang_idl;
  }

  /**
   * ipLanguage::translate()
   * 
   * @param mixed $key
   * @return
   */
  public function translate( $key = null ) {
    return ( $this->lang_idl && isset( $this->lang_idl->{$key} ) ) ? $this->lang_idl->{$key} : false;
  }
  /**
   * ipLanguage::translate_ime()
   * 
   * @param mixed $string
   * @param string $tlang
   * @return
   */
  public function translate_ime( $string = null, $tlang = "en" ) {
    $string = trim( $string );
    $tlang  = trim( strtolower( $tlang ) );
    if ( !$string || !$tlang || $tlang == "en" ) {
      return false;
    }
    $language = $this->ime_list( $tlang );
    if ( !$language ) {
      return false;
    }
    $language["text"] = $string;
    $language["ime"]  = sprintf( $language["ime"], $tlang );
    $language = "http://www.google.com/inputtools/request?".http_build_query( $language );

    $_SESSION["lang_ime_trans"] = ( isset( $_SESSION["lang_ime_trans"] ) && is_array( $_SESSION["lang_ime_trans"] ) ) ? $_SESSION["lang_ime_trans"] : array();
    if ( isset( $_SESSION["lang_ime_trans"][$tlang][$string] ) ) {
      $response = $_SESSION["lang_ime_trans"][$tlang][$string];
    }
    else {
      loadClass( "ipCurl", "curl/curl.class.php" );
      $curl = new ipCurl();
      $curl->createCurl( $language );
      $response = json_decode( $curl->getResponse() );
    }

    if ( $response ) {
      if ( !isset( $_SESSION["lang_ime_trans"][$tlang][$string] ) ) {
        $_SESSION["lang_ime_trans"][$tlang][$string]  = $response;
      }
      if ( isset( $response[1][0][1][0] ) ) {
        //$this->__cache_translation( $text, $lang, array( 'text' => $response[1][0][1][0], 'revisions' => $response[1][0][1] ) );
        return array(
          "text"      =>  $response[1][0][1][0],
          "revisions" =>  $response[1][0][1]
        );
      }
    }

    return false;
  }

  /**
   * ipLanguage::reading_language()
   * 
   * @return
   */
  public function reading_language() {
    return ( $this->lang_idx ) ? $this->lang_idx : "en";
  }
  /**
   * ipLanguage::writing_language()
   * 
   * @return
   */
  public function writing_language() {
    return ( isset( $_COOKIE["wlang_global"] ) ) ? trim( strtolower( $_COOKIE["wlang_global"] ) ) : "en";
  }
}
?>
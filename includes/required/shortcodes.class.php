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

class ipShortcodes {
  private $shortcodes = array();

  /**
   * ipShortcodes::registerShortcode()
   * 
   * @param mixed $code
   * @param mixed $function
   * @return void
  */
  public function registerShortcode( $code, $function ) {
    global $shortcodes;
    if ( is_callable( $function ) ) {
      $this->shortcodes[$code]  = $function;
    }
  }

  /**
   * ipShortcodes::replaceShortcodes()
   * 
   * @param mixed $content
   * @return content
   */
  public function replaceShortcodes( $content ) {
    if ( empty( $this->shortcodes ) || !is_array( $this->shortcodes ) ) {
      return $content;
    }
    $pattern  = $this->getShortcodeRegex();
    return preg_replace_callback( "/$pattern/s", array( $this, "replaceShortcodesTag" ), $content );
  }

  /**
   * ipShortcodes::replaceShortcodesTag()
   * 
   * @param mixed $m
   * @return content
   */
  private function replaceShortcodesTag( $m = array() ) {
    if ( $m[1] == "[" && $m[6] == "]" ) {
      return substr( $m[0], 1, -1 );
    }
    $tag  = $m[2];
    $attr = $this->parseSCattrb( $m[3] );
    if ( isset( $m[5] ) ) {
      return $m[1].call_user_func( $this->shortcodes[$tag], $attr, $m[5], $tag ).$m[6];
    } else {
      return $m[1].call_user_func( $this->shortcodes[$tag], $attr, null,  $tag ).$m[6];
    }
  }

  /**
   * ipShortcodes::getShortcodeRegex()
   * 
   * @return regex
   */
  private function getShortcodeRegex() {
    $tagnames   = array_keys( $this->shortcodes );
    $tagregexp  = implode( "|", array_map( "preg_quote", $tagnames ) );
    return '\\[(\\[?)('.$tagregexp.')\\b([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
  }

  /**
   * ipShortcodes::parseSCattrb()
   * 
   * @param mixed $text
   * @return content
   */
  private function parseSCattrb( $text ) {
    $atts     = array();
    $pattern  = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
    $text     = preg_replace( "/[\x{00a0}\x{200b}]+/u", " ", $text );
    if ( preg_match_all( $pattern, $text, $match, PREG_SET_ORDER ) ) {
      foreach ( $match as $m ) {
        if ( !empty( $m[1] ) ) {
          $atts[strtolower( $m[1] )]  = stripcslashes( $m[2] );
        }
        elseif ( !empty( $m[3] )) {
          $atts[strtolower( $m[3] )]  = stripcslashes( $m[4] );
        }
        elseif ( !empty( $m[5] ) ) {
          $atts[strtolower( $m[5] )]  = stripcslashes( $m[6] );
        }
        elseif ( isset( $m[7] ) && strlen( $m[7] ) ) {
          $atts[] = stripcslashes( $m[7] );
        }
        elseif (isset($m[8])) {
          $atts[] = stripcslashes( $m[8] );
        }
      }
    } else {
      $atts = ltrim( $text );
    }
    return $atts;
  }

  /**
   * ipShortcodes::shortcodeAttrb()
   * 
   * @param mixed $pairs
   * @param mixed $atts
   * @return attribs
   */
  private function shortcodeAttrb( $pairs, $atts ) {
    $atts = (array)$atts;
    $out  = array();
    foreach( $pairs as $name => $default ) {
      if ( array_key_exists( $name, $atts ) ) {
        $out[$name] = $atts[$name];
      }
      else {
        $out[$name] = $default;
      }
    }
    return $out;
  }

  /**
   * ipShortcodes::removeShortcodes()
   * 
   * @param mixed $content
   * @return content
   */
  public function removeShortcodes( $content = null ) {
    if ( empty( $this->shortcodes ) || !is_array( $this->shortcodes ) ) {
      return $content;
    }
    $pattern  = $this->getShortcodeRegex();
    return preg_replace_callback( "/$pattern/s", array( $this, "removeShortcodesTag" ), $content );
  }

  /**
   * ipShortcodes::removeShortcodesTag()
   * 
   * @param mixed $m
   * @return content
   */
  private function removeShortcodesTag( $m ) {
    if ( $m[1] == "[" && $m[6] == "]" ) {
      return substr( $m[0], 1, -1 );
    }
    return $m[1].$m[6];
  }
}

$shortcode  = new ipShortcodes;

/**
 * registerShortcode()
 * 
 * @param mixed $code
 * @param mixed $func
 * @return void
 */
function registerShortcode( $code, $func ) {
  global $shortcodes;
  return $shortcodes->registerShortcode( $code, $func );
}

/**
 * replaceShortcodes()
 * 
 * @param mixed $content
 * @return content
 */
function replaceShortcodes( $content ) {
  global $shortcodes;
  return $shortcodes->replaceShortcodes( $content );
}

/**
 * removeShortcodes()
 * 
 * @param mixed $content
 * @return content
 */
function removeShortcodes( $content ) {
  global $shortcodes;
  return $shortcodes->removeShortcodes( $content );
}
?>
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
 * ipRuntimeCache
 * 
 * @package Cache
 * @author bystwn22
 * @copyright 2012
 * @version 2.3
 * @access public
*/
class ipRuntimeCache {
  public $cached;

  /**
   * ipRuntimeCache::addCache()
   * 
   * @param mixed $name
   * @param mixed $data
   * @param mixed $group
   * @return void
  */
  function addCache( $name, $data, $group ) {
    $this->cached[$group][$name]  = $data;
    return $data;
  }

  /**
   * ipRuntimeCache::checkCache()
   * 
   * @param string $name
   * @param string $group
   * @return true on success, false on failure
  */
  function checkCache( $name = null, $group = null ) {
    return ( isset( $this->cached[$group] ) && isset( $this->cached[$group][$name] ) );
  }

  /**
   * ipRuntimeCache::getCacheArray()
   * 
   * @return array
  */
  function getCacheArray() {
    return $this->cached;
  }

  /**
   * ipRuntimeCache::getCache()
   * 
   * @param string $name
   * @param string $group
   * @return cached item
  */
  function getCache( $name = null, $group = null ) {
    return ( isset( $this->cached[$group] ) && isset( $this->cached[$group][$name] ) ) ? $this->cached[$group][$name] : false;
  }

  /**
   * ipRuntimeCache::resetGroup()
   * 
   * @param string $group
   * @param string $name
   * @return void
  */
  function resetGroup( $group = null, $name = null ) {
    if ( isset( $this->cached[$group] ) ) {
      if ( !empty( $name ) ) {
        if ( isset( $this->cached[$group][$name] ) ) {
          unset( $this->cached[$group][$name] );
        }
      }
      else {
        unset( $this->cached[$group] );
      }
    }
  }

  /**
   * ipRuntimeCache::clearCache()
   * 
   * @return void
  */
  function clearCache() {
    $this->cached = array();
  }
}

$ipCache  = new ipRuntimeCache;

/**
 * checkCache()
 * 
 * @param string $name
 * @param string $group
 * @return true on success, false on failure
 */
function checkCache( $name = null, $group = null ) {
  global $ipCache;
  return $ipCache->checkCache( $name, $group );
}

/**
 * getCache()
 * 
 * @param string $name
 * @param string $group
 * @return cached data
 */
function getCache( $name = null, $group = null ) {
  global $ipCache;
  return $ipCache->getCache( $name, $group );
}

/**
 * addCache()
 * 
 * @param string $name
 * @param string $data
 * @param string $group
 * @return void
 */
function addCache( $name = null, $data = null, $group = null ) {
  global $ipCache;
  return $ipCache->addCache( $name, $data, $group );
}

/**
 * getCacheArray()
 * 
 * @return cache array
 */
function getCacheArray() {
  global $ipCache;
  return $ipCache->getCacheArray();
}
?>
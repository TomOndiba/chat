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

require_once( dirname( __FILE__ )."/hooks.class.php" );

$ipHooks  = new ipHooks;
$ipHooks->setActivePlugins( array( "plugin1.php", "plugin3.php", "withargs.php", "plugin2.php" ) );
$ipHooks->loadPlugins();
$GLOBALS["ipHooks"] = $ipHooks;

/**
 * ipHooks::add_hook()
 * 
 * @param string $tag (The name of the hook)
 * @param string $function (The function you wish to be called.)
 * @param integer $priority (optional) (Used to specify the order in which the functions associated with a particular action are executed.(range 0~20, 0 first call, 20 last call))
 * @return void
*/
function add_hook( $tag = null, $function = null, $priority = 10 ) {
	global $ipHooks;
  if ( !hook_exists( $tag ) ) {
    set_hook( $tag );
  }
  $ipHooks->addHook( $tag, $function, $priority );
}

/**
 * ipHooks::registerPlugin()
 * Register plugin data in $this->plugin
 * @param string $pluginID (The name of the plugin.)
 * @param array $data (optional) (The data the plugin accessorial(default none))
 * @return void
*/
function register_plugin( $pluginID = null, $data = array() ) {
	global $ipHooks;
  $ipHooks->registerCallingPlugin( debug_backtrace() );
	$ipHooks->registerPlugin( $pluginID, $data );
}

/**
 * executeHook()
 * Execute all functions which are attached to hook, you can provide argument (or arguments via array)
 * @param string $tag (The name of the hook.)
 * @param mixed $args (optional) (The arguments the function accept (default none))
 * @return optional
*/
function execute_hook( $tag = null, $args = null ) {
  global $ipHooks;
  if ( !hook_exists( $tag ) ) {
    set_hook( $tag );
  }
  return $ipHooks->executeHook( $tag, $args );
}

/**
 * filterHook()
 * Filter $args and after modify, return it. (or arguments via array)
 * @param string $tag (The name of the hook)
 * @param mixed $args (optional) (The arguments the function accept to filter(default none))
 * @return array (The $args filter result.)
*/
function filter_hook( $tag = null, $args = null ) {
  global $ipHooks;
  if ( !hook_exists( $tag ) ) {
    set_hook( $tag );
  }
  return $ipHooks->filterHook( $tag, $args );
}

/**
 * hookExist()
 * Check whether any function is attached to hook
 * @param string $tag (The name of the hook)
 * @return true on success, false on failure
*/
function hook_exists( $tag = null ) {
  global $ipHooks;
  return $ipHooks->hookExist( $tag );
}

/**
 * setHook()
 * Register hook name/tag, so plugin developers can attach functions to hooks
 * @param string $tag (The name of the hook)
 * @return true on success, false on failure
*/
function set_hook( $tag = null ) {
  global $ipHooks;
  return $ipHooks->setHook( $tag );
}

/**
 * setHooks()
 * Register multiple hooks name/tag
 * @param array $tags (The name of the hooks)
 * @return void
*/
function set_hooks( $tags = array() ) {
  global $ipHooks;
  return $ipHooks->setHooks( $tags );
}

/**
 * unsetHook()
 * Write hook off
 * @param string $tag (The name of the hook)
 * @return void
*/
function unset_hook( $tag = null ) {
  global $ipHooks;
  return $ipHooks->unsetHook( $tag );
}

/**
 * ipHooks::unsetHooks()
 * Write multiple hooks off
 * @param array $tags (The name of the hooks)
 * @return void
*/
function unset_hooks( $tags = array() ) {
  global $ipHooks;
  return $ipHooks->unsetHooks( $tags );
}

require_once( dirname( __FILE__ )."/hooks.default.php" );
?>
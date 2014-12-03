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
 * ipThemes
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipThemes {
  private $folder;

  /**
   * ipThemes::themes()
   * 
   * @param mixed $folder
   * @return
   */
  public static function themes( $folder = null ) {
    $themes = new ipThemes( $folder );
    return $themes->listThemes();
  }

  /**
   * ipThemes::__construct()
   * 
   * @param mixed $folder
   * @return
   */
  public function __construct( $folder = null ) {
    $this->setThemeFolder( $folder );
  }

  /**
   * ipThemes::setThemeFolder()
   * 
   * @param mixed $folder
   * @return
   */
  public function setThemeFolder( $folder = null ) {
    if ( $folder && file_exists( $folder ) ) {
      $this->folder = realpath( $folder ).DIRECTORY_SEPARATOR;
    }
  }

  /**
   * ipThemes::listThemes()
   * 
   * @return
   */
  public function listThemes() {
    $files  = glob( $this->folder."*.css" );
    $themes = array();
    if ( is_array( $files ) && !empty( $files ) ) {
      foreach( $files as &$file ) {
        $fname  = pathinfo( $file, PATHINFO_FILENAME );
        $tdata  = new ThemeCommentsRead( $file );
        $tdata->read_comments();
        $tdata  = array(
          "theme_idx"   =>  $fname,
          "name"        =>  $tdata->get_var_by_tag( "Theme Name", $fname ),
          "author"      =>  $tdata->get_var_by_tag( "Author", "Unknown" ),
          "version"     =>  $tdata->get_var_by_tag( "Version", "1.0" ),
          "author_uri"  =>  $tdata->get_var_by_tag( "Author URI", false ),
          "theme_uri"   =>  $tdata->get_var_by_tag( "Theme URI", false ),
          "description" =>  $tdata->get_var_by_tag( "Description", "none" ),
          "screenshot"  =>  ipgo( "home_uri" )."ipChat/css/themes/".$tdata->get_var_by_tag( "Screenshot", "no-image.jpg" ),
        );
        $themes[$fname] = $tdata;
      }
    }
    return $themes;
  }
}

/**
 * ThemeCommentsRead
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ThemeCommentsRead {
  public $stylesheet;
  public $variables;

  /**
   * ThemeCommentsRead::__construct()
   * 
   * @param mixed $stylesheet
   * @return
   */
  function __construct( $stylesheet ) {
    $this->stylesheet = $stylesheet;
    $this->variables  = array();
  }

  /**
   * ThemeCommentsRead::read_comments()
   * 
   * @return
   */
  function read_comments() {
    $fp           = fopen( $this->stylesheet, 'r' );
    $theme_data   = fread( $fp, 1500 );
    $theme_data   = explode( "*/", $theme_data );
    $theme_data   = trim( $theme_data[0] );
    fclose( $fp );

    preg_match_all( '/(.+?):(.*)$/mi', $theme_data, $matches );

    if ( $matches && !empty( $matches ) && isset( $matches[1] ) && !empty( $matches[1] ) ) {
      foreach( $matches[1] as $key => $idx ) {
        $this->variables[trim( $idx )]  = trim( $matches[2][$key] );
      }
    }
  }

  /**
   * ThemeCommentsRead::get_var_by_tag()
   * 
   * @param mixed $tag
   * @param mixed $default
   * @return
   */
  function get_var_by_tag( $tag, $default = null ) {
    if ( isset( $this->variables[$tag] ) ) {
      return ( $this->variables[$tag] ) ? $this->variables[$tag] : $default;
    }
    return $default;
  }
 
  /**
   * ThemeCommentsRead::get_all_vars()
   * 
   * @return
   */
  function get_all_vars() {
    return $this->variables;
  }
}

/**
 * ipPlugins
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipPlugins {
  private $format;
  private $search;
  private $folder;

  /**
   * ipPlugins::__construct()
   * 
   * @param mixed $folder
   * @param string $format
   * @return
   */
  public function __construct( $folder = null, $format = "php" ) {
    $this->setPluginFolder( $folder );
    $this->setPluginFormat( $format );
  }

  /**
   * ipPlugins::setPluginFormat()
   * 
   * @param mixed $format
   * @return
   */
  public function setPluginFormat( $format = null ) {
    $this->format = strtolower( trim( $format ) );
  }
  
  /**
   * ipPlugins::setFilter()
   * 
   * @param mixed $search
   * @return
   */
  public function setFilter( $search = null ) {
    $this->search = strtolower( trim( $search ) );
  }

  /**
   * ipPlugins::setPluginFolder()
   * 
   * @param mixed $folder
   * @return
   */
  public function setPluginFolder( $folder = null ) {
    if ( $folder && file_exists( $folder ) ) {
      $this->folder = realpath( $folder ).DIRECTORY_SEPARATOR;
    }
  }

  /**
   * ipPlugins::findPluginFile()
   * 
   * @param mixed $plugin
   * @param mixed $subdir
   * @param mixed $subfile
   * @return
   */
  public function findPluginFile( $plugin = null, $subdir = null, $subfile = null ) {
    $subdir = trim( trim( $subdir ), "/" );
    $subdir = ( $subdir ) ? $subdir."/" : null;
    $folder = sprintf( "%s%s%s%s", $this->folder, $plugin, "/", $subdir );
    if ( file_exists( $folder ) ) {
      if ( $subfile ) {
        return ( file_exists( $folder.$subfile ) ) ? realpath( $folder.$subfile ) : false;
      }
      return realpath( sprintf( "%s%s.%s", $folder, $plugin, $this->format ) );
    }
    return realpath( sprintf( "%s%s.%s", $this->folder, $plugin, $this->format ) );
  }

  /**
   * ipPlugins::isPluginInFolder()
   * 
   * @param mixed $plugin
   * @return
   */
  public function isPluginInFolder( $plugin = null ) {
    $file = sprintf( "%s%s%s%s.%s", $this->folder, $plugin, DIRECTORY_SEPARATOR, $plugin, $this->format );
    if ( file_exists( $file ) ) {
      return realpath( dirname( $file ) );
    }
    return false;
  }

  /**
   * ipPlugins::listPlugins()
   * 
   * @param bool $active_only
   * @param bool $parse
   * @param bool $fullpath
   * @return
   */
  public function listPlugins( $active_only = true, $parse = true, $fullpath = false ) {
    $active   = ( $active_only ) ? $this->activePlugins( $this->format ) : false;
    $plugins  = array();
    $targets  = new DirectoryIterator( $this->folder );
    if ( $targets ) {
      foreach( $targets as $target ) {
        $file = $target->getPathname();
        if ( $target->isDot() ) {
          continue;
        }
        if ( $target->isDir() ) {
          $file = realpath( sprintf( "%s%s%s.%s", $target->getPathname(), DIRECTORY_SEPARATOR, $target->getFilename(), $this->format ) );
        }
        if ( !$file ) {
          continue;
        }
        if ( strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) !== $this->format ) {
          continue;
        }

        $filename = pathinfo( $file, PATHINFO_FILENAME );

        if ( !$active_only ) {
          if ( $fullpath ) {
            $plugins[]  = $file;
            continue;
          }
          if ( $parse ) {
            $reader = new ThemeCommentsRead( $file );
            $reader->read_comments();
            $data = array(
              "name"  =>  $filename,
              "extn"  =>  pathinfo( $file, PATHINFO_EXTENSION ),
              "data"  =>  $reader->get_all_vars(),
              "date"  =>  filemtime( $file )
            );
            if ( $this->search ) {
              if ( stristr( $filename, $this->search ) ) {
                $plugins[]  = $data;
              }
              else {
                foreach( $data["data"] as $var ) {
                  if ( stristr( $var, $this->search ) ) {
                    $plugins[]  = $data;
                    break 1;
                  }
                }
              }
            }
            else {
              $plugins[]  = $data;
            }
          }
          else {
            $plugins[]  = $filename;
          }
          continue;
        }
        if ( in_array( $filename, $active ) ) {
          if ( $fullpath ) {
            $plugins[]  = $file;
            continue;
          }
          $plugins[]  = $filename;
        }
      }
    }
    return $plugins;
  }

  /**
   * ipPlugins::loadPlugins()
   * 
   * @param bool $active_only
   * @return
   */
  public function loadPlugins( $active_only = true ) {
    $this->format = "php";
    $plugins  = $this->listPlugins( $active_only, false, true );
    if ( !empty( $plugins ) ) {
      $plugins  = apply_filters( "onloadplugins", $plugins );
      foreach( $plugins as $plugin ) {
        do_action( "onloadplugin", false, pathinfo( $plugin, PATHINFO_FILENAME ), "php", $plugin );
        require_once( $plugin );
      }
    }
  }

  /**
   * ipPlugins::activePlugins()
   * 
   * @param string $ext
   * @return
   */
  public function activePlugins( $ext = "php" ) {
    global $ipdb;
    
    $plugins  = ipgo( "active_plugins" );
    $plugins  = @unserialize( $plugins );
    $plugins  = array_filter( (array)$plugins );
    $plugins["php"] = ( isset( $plugins["php"] ) && is_array( $plugins["php"] ) ) ? $plugins["php"] : array();
    $plugins["js"]  = ( isset( $plugins["js"] ) && is_array( $plugins["js"] ) ) ? $plugins["js"] : array();
    if ( $ext ) {
      $plugins[$ext]  = ( isset( $plugins[$ext] ) && is_array( $plugins[$ext] ) ) ? $plugins[$ext] : array();
      return $plugins[$ext];
    }
    return $plugins;
  }

  /**
   * ipPlugins::activatePlugin()
   * 
   * @param mixed $plugins
   * @param string $extension
   * @return
   */
  public function activatePlugin( $plugins = null, $extension = "php" ) {
    $extension  = strtolower( trim( $extension ) );
    $plugins    = array_filter( array_unique( array_map( "trim", (array)$plugins ) ) );
    if ( empty( $plugins ) ) {
      return false;
    }

    $active = $this->activePlugins( false );
    $added  = false;
    foreach( $plugins as $plugin ) {
      if ( !in_array( $plugin, $active[$extension] ) ) {
        do_action( "onactivateplugin", false, $plugin, $extension );
        $added  = true;
        $active[$extension][] = $plugin;
      }
    }

    if ( $added ) {
      return ipso( "active_plugins", serialize( $active ) );
    }
    return false;
  }

  /**
   * ipPlugins::deactivatePlugin()
   * 
   * @param mixed $plugins
   * @param string $extension
   * @return
   */
  public function deactivatePlugin( $plugins = null, $extension = "php" ) {
    $extension  = strtolower( trim( $extension ) );
    $plugins    = array_filter( array_unique( array_map( "trim", (array)$plugins ) ) );
    if ( empty( $plugins ) ) {
      return false;
    }

    $active = $this->activePlugins( false );
    $remove = false;
    foreach( $plugins as $plugin ) {
      if ( in_array( $plugin, $active[$extension] ) ) {
        $key  = array_search( $plugin, $active[$extension] );
        if ( $key !== false && isset( $active[$extension][$key] ) ) {
          do_action( "ondeactivateplugin", false, $plugin, $extension );
          $remove = true;
          unset( $active[$extension][$key] );
        }
      }
    }

    if ( $remove ) {
      return ipso( "active_plugins", serialize( $active ) );
    }
    return false;
  }

  /**
   * ipPlugins::deletePlugin()
   * 
   * @param mixed $plugins
   * @param string $extension
   * @return
   */
  public function deletePlugin( $plugins = null, $extension = "php" ) {
    $extension  = strtolower( trim( $extension ) );
    $plugins    = array_filter( array_unique( array_map( "trim", (array)$plugins ) ) );
    if ( empty( $plugins ) ) {
      return false;
    }

    $this->deactivatePlugin( $plugins, $extension );
    $this->setPluginFormat( $extension );

    foreach( $plugins as $plugin ) {
      if ( $pathname = $this->findPluginFile( $plugin ) ) {
        if ( $dirname = $this->isPluginInFolder( $plugin ) ) {
          do_action( "ondeleteplugin", false, $plugin, $extension, $dirname, true );
          $this->deletePluginFolder( $dirname );
        }
        else {
          do_action( "ondeleteplugin", false, $plugin, $extension, $pathname, false );
          unlink( $pathname );
        }
      }
    }
    
    return true;
  }

  /**
   * ipPlugins::clearPlugins()
   * 
   * @return
   */
  public function clearPlugins() {
    if ( $plugins = realpath( root_dir()."ipChat/plugins/" ) ) {
      do_action( "onclearplugins" );
      ipso( "active_plugins", serialize( array() ) );
      rrmdir( $plugins );
      return true;
    }
    return false;
  }

  /**
   * ipPlugins::deletePluginFolder()
   * 
   * @param mixed $pathname
   * @param bool $recursive
   * @return
   */
  public function deletePluginFolder( $pathname = null, $recursive = false ) {
    $pathname = realpath( $pathname );
    if ( !$pathname ) {
      return false;
    }

    $iterator = new DirectoryIterator( $pathname );
    if ( $iterator && !empty( $iterator ) ) {
      foreach( $iterator as $filename ) {
        if ( $filename->isDot() ) {
          continue;
        }
        if ( $filename->isDir() ) {
          $this->deletePluginFolder( $filename->getPathname(), true );
          rmdir( $filename->getPathname() );
          continue;
        }
        unlink( $filename->getPathname() );
      }
    }
    if ( !$recursive ) {
      rmdir( $pathname );
    }
  }
}

/**
 * ipSmilies
 * 
 * @package   
 * @author Impact Plus
 * @copyright bystwn22
 * @version 2014
 * @access public
 */
class ipSmilies {
  private $smilies  = array(
    "smilies" =>  array(
      "name"  =>  "Emoticons",
      "data"  =>  array(
        "biggrin" => array( "=D",":-D",":D" ),
        "confused" => array( ":-/", ":/", ":-\\", ":\\" ),
        "cool" => array( "B)", "B-)", "B|" ),
        "eek" => array( "o_O", "o.O", "O_o", "O.o" ),
        "mad" => array( ":@", "&gt;:(", "&gt;:o" ),
        "neutral" => array( ":|" ),
        "sad" => array( ":(", ":-(" ),
        "smile" => array( ":)", ":-)" ),
        "surprised" => array( ":O", ":-O" ),
        "wink" => array( ";)", ";-)" ),
        "glasses" => array( "8)", "8-)" ),
        "razz" => array( ":P", ":-P" )
      ),
      "icon"  =>  array(
                    "u" =>  "ipChat/images/smiley-unselected.png",
                    "s" =>  "ipChat/images/smiley-selected.png"
                  ),
      "imgw"  =>  false,
      "imgh"  =>  false,
      "emoji" =>  false
    )
  );

  /**
   * ipSmilies::load()
   * 
   * @param bool $item
   * @param bool $headers
   * @return
   */
  public static function load( $item = false, $headers = false ) {
    $smilies  = new ipSmilies;
    return $smilies->getList( $item, $headers );
  }

  /**
   * ipSmilies::__construct()
   * 
   * @return
   */
  public function __construct() {
    $this->smilies  = do_action( "smilies", false, $this->smilies );
    foreach( $this->smilies as &$smiley ) {
      $smiley = array_merge( array( "name"=>null,"emoji"=>false,"imgw"=>50,"imgh"=>50,"data"=>null,"extn"=>null,"icon"=>array("u"=>"ipChat/images/ipl-16.gif","s"=>"ipChat/images/ipl-16.gif") ), $smiley );
      $smiley["icon"]["s"]  = site_uri().$smiley["icon"]["s"];
      $smiley["icon"]["u"]  = site_uri().$smiley["icon"]["u"];
    }
  }

  /**
   * ipSmilies::process()
   * 
   * @param bool $item
   * @return
   */
  private function process( $item = false ) {
    if ( $item ) {
      if ( isset( $this->smilies[$item] ) ) {
        $this->format( $this->smilies[$item], $item );
        return $this->smilies[$item];
      }
    }
    else {
      foreach( $this->smilies as $key => &$smiley ) {
        $this->format( $smiley, $key );
      }
      return $this->smilies;
    }
    return false;
  }

  /**
   * ipSmilies::format()
   * 
   * @param mixed $smiley
   * @param mixed $key
   * @return
   */
  private function format( &$smiley, $key ) {
    if ( !is_array( $smiley["data"] ) ) {
      $path = ROOT_DIR.$smiley["data"];
      if ( file_exists( $path ) ) {
        $smiley["data"] = array_map( function( $item ) {
          $name = trim( pathinfo( $item, PATHINFO_FILENAME ) );
          return ( !in_array( $name, array( "icon", "icon-selected" ) ) ) ? str_replace( ROOT_DIR, site_uri(), $item ) : null;
        }, (array)glob( $path."*" ) );
        $smiley["data"] = array_filter( $smiley["data"] );
        if ( empty( $smiley["data"] ) ) {
          unset( $this->smilies[$key] );
        }
      }
      else {
        unset( $this->smilies[$key] );
      }
    }
  }

  /**
   * ipSmilies::getList()
   * 
   * @param bool $item
   * @param bool $headers
   * @return
   */
  public function getList( $item = false, $headers = false ) {
    if ( $headers ) {
      $smilies  = $this->smilies;
      foreach( $smilies as &$smiley ) {
        $smiley = array( "name" => $smiley["name"], "icon" => $smiley["icon"] );
      }
      return $smilies;
    }
    else {
      $i  = ( $item ) ? ucfirst( $item ) : "All";
      return ( checkCache( "Smiley".$i, "SmileyList" ) ) ? getCache( "Smiley".$i, "SmileyList" ) : addCache( "Smiley".$i, $this->process( $item ), "SmileyList" );
    }
  }
}

global $loadPlugins;
$ipPlugins  = new ipPlugins( dirname( dirname( __FILE__ ) )."/plugins/", "php" );
if ( $loadPlugins !== false ) {
  $ipPlugins->loadPlugins();
}

$GLOBALS["ipPlugins"] = $ipPlugins;
?>
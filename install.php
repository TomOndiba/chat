<?php
  error_reporting( 0 );

  require_once( dirname( __FILE__ )."/includes/required/password.class.php" );
  if ( !session_id() ) {
    session_start();
  }

  header( $_SERVER["SERVER_PROTOCOL"]." 200 FINE", true, 200 );

  if ( isset( $_GET["ignore"] ) ) {
    $_SESSION["warnings_ignored"] = true;
    header( "Location: install.php" );
    exit();
  }

  /**
    *  Format a mySQL string correctly for safe mySQL insert
    *  (no mater if magic quotes are on or not)
    **/
  function escape( $str ) {
    $str  = trim( $str );
    switch( gettype( $str ) ) {
      case "string":
        $str  = addslashes( stripslashes( $str ) );
      break;
      case "boolean":
        $str  = ( $str === false ) ? 0 : 1;
      break;
      default:
        $str  = ( $str === null ) ? "NULL" : $str;
      break;
    }
    return $str;
  }

  /**
   * Check whether your server meets the minimum requirements
   **/
  function isConfigurationsCorrect() {
    $errors = array();
    $config = dirname( __FILE__ )."/includes/conn/conf.php";

    if ( file_exists( $config ) && filesize( $config ) > 0 ) {
      $errors[] = "Configuration file found, Please delete <strong><code>conf.php</code></strong> and restart the installation";
    }
    else {
      if ( !function_exists( "phpversion" ) || version_compare( phpversion(), "5.0", "<" ) ) {
        $errors[] = "<strong>PHP 5.0</strong> or greater is required";
      }
      if ( !function_exists( "extension_loaded" ) || !extension_loaded( "mysqli" ) ) {
        $errors[] = '<strong>MySQL</strong> version <strong>5.0.15</strong> or greater';
      }
      if ( !function_exists( "ini_get" ) ) {
        $errors[] = "PHP function <strong>ini_get</strong> is not exists";
        ( @ini_get( 'open_basedir' ) != '' && @ini_get( 'safe_mode' ) != 'Off' );
      }
      else {
        if ( ini_get( "open_basedir" ) != "" && strtolower( ini_get( "safe_mode" ) ) != "off" ) {
          $errors[] = "<strong>Safe mode</strong> must be turned off";
        }
        if ( ini_get( "session.auto_start" ) ) {
          $errors[] = "You must disable <strong>session.auto_start</strong> in <strong>php.ini</strong>";
        }
      }

      if ( !function_exists( "fopen" ) ) {
        $errors[] = "Configuration file <strong>is not readable</strong>";
      }
      else {
        if ( $file = fopen( $config, "w+" ) ) {
          fclose( $file );
          if ( !is_writable( $config ) ) {
            $errors[] = "Configuration file <strong>is not writable</strong>";
          }
          if ( function_exists( "unlink" ) ){
            unlink( $config );
          }
        }
        else {
          $errors[] = "Configuration file <strong>is not readable</strong>";
        }
      }
    }

    return $errors;
  }

  /**
   * Check all required php extensions and trigger a warning (it doesnt break installation)
   **/
  function getExtensionsWarnings() {
    $errors = array();
    if ( !function_exists( "extension_loaded" ) ) {
      $errors[] = "Failed to check loaded extensions";
    }
    else {
      if ( !extension_loaded( "gd" ) ) {
        $errors[] = '<a href="http://www.php.net/manual/en/image.installation.php" target="_blank" title="GD Installation"><strong>GD extension</strong></a> is required for Image processing';
      }
      if ( !extension_loaded( "curl" ) ) {
        $errors[] = '<a href="http://www.php.net/manual/en/curl.installation.php" target="_blank" title="cURL Installation"><strong>cURL extension</strong></a> is required for Auto updates and stream rendering';
      }
      if ( !extension_loaded( "zlib" ) ) {
        $errors[] = '<a href="http://www.php.net/manual/en/zlib.installation.php" target="_blank" title="zlib Installation"><strong>zlib extension</strong></a> is required for a Better data output and Performance';
      }
      if ( !class_exists( "ZipArchive" ) ) {
        $errors[] = '<a href="http://www.php.net/manual/en/zip.installation.php" target="_blank" title="zip Installation"><strong>zip extension</strong></a> is required for Auto updates &amp; Plugin installations';
      }
      if ( !function_exists( "ini_get" ) || !ini_get( "file_uploads" ) ) {
        $errors[] = '<a href="http://www.radinks.com/upload/config.php" target="_blank" title="PHP File Upload Configuration"><strong>File uploads</strong></a> are required to be enable to use Attachment feature';
      }
    }

    return $errors;
  }

  /**
   * Check and add classes to input on error
   **/
  function errorClass( $class, $list ) {
    $list = array_unique( $list );
    if ( in_array( $class, $list ) ) {
      echo "error";
    }
  }

  /**
   * Get current path name
   **/
  function getPathName( $path = null ) {
    $path = dirname( dirname( __FILE__ ) ).DIRECTORY_SEPARATOR;
    return $path;
  }
  /**
   * Check given path exists
   */
  function pathExists( $path = null ) {
    $path = @realpath( trim( $path ) );
    if ( $path ) {
      $path = $path.DIRECTORY_SEPARATOR;
      if ( realpath( $path."ipChat/install.php" ) ) {
        return $path;
      }
    }
    return false;
  }

  /**
   * Get default site url
   **/
  function getSiteName( $path = null ) {
    $path = ( ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] ) ? "https" : "http" )."://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
    $path = str_ireplace( "ipchat/install.php", "", $path );
    return $path;
  }
  /**
   * Check whether site exists or not
   **/
  function siteExists( $path = null ) {
    $path = filter_var( trim( $path ), FILTER_VALIDATE_URL );

    if ( function_exists( "get_headers" ) ) {
      $head = @get_headers( $path );
      if ( stripos( strtolower( $head[0] ), "404" ) !== false ) {
        return false;
      }
    }

    return $path;
  }

  /**
   * Drop all tables in case of an error
   **/
  function dropTables( $tables = array(), $config = null, $conn = null ) {
    if ( !empty( $tables ) ) {
      mysqli_query( $conn, "DROP TABLE ".implode( ", ", $tables ) );
    }
    @unlink( $config );
  }

  $errors   = array();
  $fields   = array();
  $finished = false;
  $can_start_install  = isConfigurationsCorrect();
  $warnings = getExtensionsWarnings();
  $warnings_ignored = false;
  $impact_plus_uri = "http://ip.bestunmask.cu.cc/";

  if ( empty( $warnings ) || isset( $_SESSION["warnings_ignored"] ) ) {
    $warnings_ignored = true;
  }

  if ( empty( $can_start_install ) && $warnings_ignored === true ) {
    /** Main Database **/
    $dbhost     = ( isset( $_POST['dbhost'] ) && !empty( $_POST['dbhost'] ) ) ? escape( $_POST['dbhost'] ) : null;
    $dbuser     = ( isset( $_POST['dbuser'] ) && !empty( $_POST['dbuser'] ) ) ? escape( $_POST['dbuser'] ) : null;
    $dbpass     = ( isset( $_POST['dbpass'] ) && !empty( $_POST['dbpass'] ) ) ? escape( $_POST['dbpass'] ) : null;
    $dbname     = ( isset( $_POST['dbname'] ) && !empty( $_POST['dbname'] ) ) ? escape( $_POST['dbname'] ) : null;
    $dbprefix   = ( isset( $_POST['dbprefix'] ) && !empty( $_POST['dbprefix'] ) ) ? escape( $_POST['dbprefix'] ) : null;

    /** Our User Database or Existing **/
    $douserdb   = ( isset( $_POST['douserdb'] ) && $_POST['douserdb'] == '1' ) ? true : false;
    $userdbinit = ( !isset( $_POST['userdbinit'] ) && !$douserdb ) ? true : false;

    /** User Database **/
    $udbhost    = ( isset( $_POST['udbhost'] ) && !empty( $_POST['udbhost'] ) ) ? escape( $_POST['udbhost'] ) : null;
    $udbuser    = ( isset( $_POST['udbuser'] ) && !empty( $_POST['udbuser'] ) ) ? escape( $_POST['udbuser'] ) : null;
    $udbpass    = ( isset( $_POST['udbpass'] ) && !empty( $_POST['udbpass'] ) ) ? escape( $_POST['udbpass'] ) : null;
    $udbname    = ( isset( $_POST['udbname'] ) && !empty( $_POST['udbname'] ) ) ? escape( $_POST['udbname'] ) : null;
    $utbname    = ( isset( $_POST['utbname'] ) && !empty( $_POST['utbname'] ) ) ? escape( $_POST['utbname'] ) : null;
    /** Table Columns **/
    $ucolid     = ( isset( $_POST['ucolid'] ) && !empty( $_POST['ucolid'] ) ) ? escape( $_POST['ucolid'] ) : null;
    $ucolname   = ( isset( $_POST['ucolname'] ) && !empty( $_POST['ucolname'] ) ) ? escape( $_POST['ucolname'] ) : null;
    $ucolavatar = ( isset( $_POST['ucolavatar'] ) && !empty( $_POST['ucolavatar'] ) ) ? escape( $_POST['ucolavatar'] ) : null;
    $ucolemail  = ( isset( $_POST['ucolemail'] ) && !empty( $_POST['ucolemail'] ) ) ? escape( $_POST['ucolemail'] ) : null;
    $ucoluname  = ( isset( $_POST['ucoluname'] ) && !empty( $_POST['ucoluname'] ) ) ? escape( $_POST['ucoluname'] ) : null;
    $ucolpass   = ( isset( $_POST['ucolpass'] ) && !empty( $_POST['ucolpass'] ) ) ? escape( $_POST['ucolpass'] ) : null;

    /** Admin Info **/
    $ausername  = ( isset( $_POST['ausername'] ) && !empty( $_POST['ausername'] ) ) ? escape( $_POST['ausername'] ) : null;
    $apassword  = ( isset( $_POST['apassword'] ) && !empty( $_POST['apassword'] ) ) ? escape( $_POST['apassword'] ) : null;
    $aname      = ( isset( $_POST['aname'] ) && !empty( $_POST['aname'] ) ) ? escape( $_POST['aname'] ) : null;
    $aemail     = ( isset( $_POST['aemail'] ) && filter_var( $_POST['aemail'], FILTER_VALIDATE_EMAIL ) ) ? escape( $_POST['aemail'] ) : null;

    $root_url   = ( isset( $_POST['root_url'] ) && !empty( $_POST['root_url'] ) ) ? pathExists( $_POST['root_url'] ) : getPathName();
    $site_url   = ( isset( $_POST['site_url'] ) && !empty( $_POST['site_url'] ) ) ? siteExists( $_POST['site_url'] ) : getSiteName();

    if ( isset( $_POST['action'] ) && $_POST['action'] == 'install' ) {

      if ( empty( $site_url ) ) {
        $errors[] = "<strong>Fatal Error:</strong> You need to enter a value for <strong>Site URL</strong>";
        $fields[] = "site_url";
      }
      elseif ( empty( $root_url ) ) {
        $errors[] = "<strong>Fatal Error:</strong> You need to enter a value for <strong>Root URL</strong>";
        $fields[] = "root_url";
      }
      else {
        /** Check for Errors **/
        /** Main database checking **/
        if ( empty( $dbhost ) ) {
          $errors[] = "<strong>Main DB:</strong> You need to enter a value for database <strong>Host</strong>";
          $fields[] = "dbhost";
        }
        if ( empty( $dbuser ) ) {
          $errors[] = "<strong>Main DB:</strong> You need to enter your database <strong>Username</strong> name (with Administrative Rights)";
          $fields[] = "dbuser";
        }
        if ( empty( $dbname ) ) {
          $errors[] = "<strong>Main DB:</strong> You need to enter a value for database <strong>Name</strong>";
          $fields[] = "dbname";
        }
        elseif ( stristr( $dbname, '/' ) || stristr( $dbname, '\\' ) || stristr( $dbname, '.' ) ) {
          $errors[] = "<strong>Main DB: Name</strong> must not contain forbbiden characters";
          $fields[] = "dbname";
        }
        if ( !empty( $dbprefix ) ) {
          if ( !preg_match( "/^[A-Za-z0-9_]*$/", $dbprefix ) ) {
            $errors[] = "<strong>Main DB: Table Prefix</strong> must contain only letters, numbers and underscore";
            $fields[] = "dbprefix";
          }
        }
        if ( empty( $errors ) ) {
          $maindb_conn  = @mysqli_connect( $dbhost, $dbuser, $dbpass, $dbname );
          if ( mysqli_connect_errno( $maindb_conn ) ) {
            $conn_error = mysqli_connect_error();
            $errors[] = "<strong>Main DB: Connection failure:</strong> ".$conn_error;
            if ( stristr( $conn_error, "no such host is known" ) ) {
              $fields[] = "dbhost";
            }
            if ( stristr( $conn_error, "unknown database" ) ) {
              $fields[] = "dbname";
            }
            if ( stristr( $conn_error, "access denied for user" ) ) {
              $fields[] = "dbuser";
              if ( stristr( $conn_error, "to database" ) ) {
                $fields[] = "dbname";
              }
              elseif ( stristr( $conn_error, "using password: yes" ) ) {
                $fields[] = "dbpass";
              }
            }
          }
        }
  
        /** User database checking **/
        if ( $userdbinit ) {
          if ( empty( $udbhost ) ) {
            $errors[] = "<strong>User DB:</strong> You need to enter a value for database <strong>Host</strong>";
            $fields[] = "udbhost";
          }
          if ( empty( $udbuser ) ) {
            $errors[] = "<strong>User DB:</strong> You need to enter your database <strong>Username</strong> name (with Administrative Rights)";
            $fields[] = "udbuser";
          }
          if ( empty( $udbname ) ) {
            $errors[] = "<strong>User DB:</strong> You need to enter a value for database <strong>Name</strong>";
            $fields[] = "udbname";
          }
          elseif ( stristr( $udbname, '/' ) || stristr( $udbname, '\\' ) || stristr( $udbname, '.' ) ) {
            $errors[] = "<strong>Main DB: Name</strong> must not contain forbbiden characters";
            $fields[] = "dbname";
          }
          if ( empty( $errors ) ) {
            $userdb_conn  = @mysqli_connect( $udbhost, $udbuser, $udbpass, $udbname );
            if ( mysqli_connect_errno( $userdb_conn ) ) {
              $conn_error = mysqli_connect_error();
              $errors[] = "<strong>User DB: Connection failure:</strong> ".$conn_error;
              if ( stristr( $conn_error, "no such host is known" ) ) {
                $fields[] = "udbhost";
              }
              if ( stristr( $conn_error, "unknown database" ) ) {
                $fields[] = "udbname";
              }
              if ( stristr( $conn_error, "access denied for user" ) ) {
                $fields[] = "udbuser";
                if ( stristr( $conn_error, "to database" ) ) {
                  $fields[] = "udbname";
                }
                elseif ( stristr( $conn_error, "using password: yes" ) ) {
                  $fields[] = "udbpass";
                }
              }
            }
          }
        }
  
        /** User table checking **/
        if ( empty( $errors ) ) {
          if ( !$douserdb ) {
            $bridge_conn  = ( $userdbinit ) ? $userdb_conn : $maindb_conn;
            if ( !mysqli_query( $bridge_conn, "SELECT COUNT(*) FROM $utbname" ) ) {
              $conn_error = mysqli_error( $bridge_conn );
              $errors[] = "<strong>User DB: Table Error:</strong> ".$conn_error;
              $fields[] = "utbname";
            }
          }
        }
  
        /** User table column checking **/
        if ( empty( $errors ) ) {
          if ( !$douserdb ) {
            $bridge_conn  = ( $userdbinit ) ? $userdb_conn : $maindb_conn;
            if ( !mysqli_query( $bridge_conn, "SELECT $ucolid, $ucolname, $ucoluname FROM $utbname" ) ) {
              $conn_error = mysqli_error( $bridge_conn );
              $errors[] = "<strong>User DB: Column Error:</strong> ".$conn_error;
              if ( stristr( $conn_error, "'{$ucolid}'" ) ) {
                $fields[] = "ucolid";
              }
              elseif ( stristr( $conn_error, "'{$ucolname}'" ) ) {
                $fields[] = "ucolname";
              }
              elseif ( stristr( $conn_error, "'{$ucoluname}'" ) ) {
                $fields[] = "ucoluname";
              }
            }
    
            $ucolavatar = ( empty( $ucolavatar ) ) ? 'avatar' : $ucolavatar;
            $ucolemail  = ( empty( $ucolemail ) ) ? 'email' : $ucolemail;
            $opt_columns  = array( $ucolavatar, $ucolemail );
            foreach( $opt_columns as $opt_column ) {
              if ( !mysqli_query( $bridge_conn, "SELECT $opt_column FROM $utbname" ) ) {
                mysqli_query( $bridge_conn, "ALTER TABLE `$utbname` ADD `$opt_column` VARCHAR( 350 ) NOT NULL" );
              }
            }
          }
        }
  
        /** Admin info checking **/
        if ( empty( $errors ) ) {
          if ( empty( $ausername ) || strlen( $ausername ) < 3 ) {
            $errors[] = '<strong>Username Error:</strong> You need to enter a <strong>username</strong> with a minimum length of 3 for the Impact Plus admin account';
            $fields[] = "ausername";
          }
          else {
            if ( !preg_match( '/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/', $ausername, $username_matches ) ) {
              $errors[] = '<strong>Username Error:</strong> Use only the lower case letters of the English alphabet, the underscore and the numbers, without space.';
              $fields[] = "ausername";
            }
          }
          if ( empty( $apassword ) || strlen( $apassword ) < 3 ) {
            $errors[] = '<strong>Password Error:</strong> You need to enter a <strong>password</strong> with a minimum length of 3 for the Impact Plus admin account';
            $fields[] = "apassword";
          }
          if ( empty( $aname ) || strlen( $aname ) < 3 ) {
            $errors[] = '<strong>Name Error:</strong> You need to enter a valid <strong>name</strong> for the Impact Plus admin account';
            $fields[] = "aname";
          }
          if ( empty( $aemail ) ) {
            $errors[] = '<strong>Email Error:</strong> You need to enter a valid <strong>email address</strong> for the Impact Plus admin account';
          }
        }
  
        /** Some Random Data Merging **/
        $utbname1 = $utbname;
        if ( empty( $errors ) ) {
          if ( $douserdb ) {
            $udbhost  = $dbhost;
            $udbuser  = $dbuser;
            $udbpass  = $dbpass;
            $udbname  = $dbname;
            $utbname1 = 'MAIN_DB_PREFIX."users"';
      
            $ucolid     = "ID";
            $ucolname   = "name";
            $ucoluname  = "username";
            $ucolpass   = "password";
            $ucolavatar = "avatar";
            $ucolemail  = "email";
          }
          elseif ( !$userdbinit ) {
            $udbhost  = $dbhost;
            $udbuser  = $dbuser;
            $udbpass  = $dbpass;
            $udbname  = $dbname;
            $utbname1 = "'".$utbname1."'";
          }
          elseif ( $userdbinit ) {
            $utbname1 = "'".$utbname1."'";
          }
  
          $config_file  = dirname( __FILE__ )."/includes/conn/conf.php";
          $config_str   = '<?php
  
    if ( defined( "USER_DB_INIT" ) ) {
      return false;
    }
  
    // Chat database host name (required) (important)
    define( "MAIN_DB_HOST", "'.$dbhost.'" );
    // Chat database username (required) (important)
    define( "MAIN_DB_USER", "'.$dbuser.'" );
    // Chat database password (required) (important)
    define( "MAIN_DB_PASS", "'.$dbpass.'" );
     // Chat database name (required) (important)
    define( "MAIN_DB_NAME", "'.$dbname.'" );
    // Chat table\'s prefix (optional)
    define( "MAIN_DB_PREFIX", "'.$dbprefix.'" );
  
    // If set to "true", Impact Plus will NOT create a table for users
    define( "USER_DB_INIT", '.( ( $userdbinit ) ? 'true' : 'false' ).' );
    // User\'s database host name (required) (important)
    define( "USER_DB_HOST", '.( ( $dbhost === $udbhost ) ? 'MAIN_DB_HOST' : '"'.$udbhost.'"' ).' );
    // User\'s database username (required) (important)
    define( "USER_DB_USER", '.( ( $dbuser === $udbuser ) ? 'MAIN_DB_USER' : '"'.$udbuser.'"' ).' );
    // User\'s database password (required) (important)
    define( "USER_DB_PASS", '.( ( $dbpass === $udbpass ) ? 'MAIN_DB_PASS' : '"'.$udbpass.'"' ).' );
    // Name of user\'s database (required) (important)
    define( "USER_DB_NAME", '.( ( $dbname === $udbname ) ? 'MAIN_DB_NAME' : '"'.$udbname.'"' ).' );
    // Name of user\'s table (required) (important)
    define( "USER_TB_NAME", '.$utbname1.' );
  
    // "true" if you let Impact Plus to create a new table for users, else "false" (required)
    define( "USER_COL_EDITABLE", '.( ( $userdbinit ) ? 'false' : 'true' ).' );
    // Name of user\'s ID field (required) (important)
    define( "USER_COL_ID", "'.$ucolid.'" );
    // Name of user\'s name field (required) (important)
    define( "USER_COL_NAME", "'.$ucolname.'" );
    // Name of user\'s username field (required) (important)
    define( "USER_COL_USERNAME", "'.$ucoluname.'" );
    // Name of user\'s password field (optional)
    define( "USER_COL_PASSWORD", "'.$ucolpass.'" );
    // Name of user\'s avatar field (required) (important|will be auto created if does not exists)
    define( "USER_COL_AVATAR", "'.$ucolavatar.'" );
    // Name of user\'s email field (required) (important|will be auto created if does not exists)
    define( "USER_COL_EMAIL", "'.$ucolemail.'" );
  
    // Please dont change it, except an announcement from Impact Plus on your Admin Panel (required)
    define( "IMPACTPLUS_SERVER", "'.$impact_plus_uri.'" );
    // Your sites\'s full URL to root, where ipChat folder contains (Don\'t forget to add a trailing slash to the url) (required) (important)
    define( "ROOT_DIR", "'.str_replace( "\\", "\\\\", $root_url ).'" );
    // Your sites\'s full URL to root, where ipChat folder contains (Don\'t forget to add a trailing slash to the url) (required) (important)
    define( "SITE_URI", "'.$site_url.'" );
  
  ?>';
          file_put_contents( $config_file, $config_str );
        }
  
        $tables = array(
          "admin" =>  "CREATE TABLE IF NOT EXISTS `%sadmin` ( `username` varchar(350) NOT NULL, `password` varchar(350) NOT NULL, `email` varchar(350) NOT NULL, `name` varchar(350) NOT NULL, `avatar` varchar(350) NOT NULL, `last_login` bigint(20) NOT NULL DEFAULT '0', `current_login` bigint(20) NOT NULL DEFAULT '0', UNIQUE KEY `last_login` (`last_login`), UNIQUE KEY `last_login_2` (`last_login`) ) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
          "attachments" =>  "CREATE TABLE IF NOT EXISTS `%sattachments` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `userID` bigint(20) NOT NULL DEFAULT '0', `relationID` varchar(350) CHARACTER SET latin1 NOT NULL, `title` varchar(350) COLLATE utf8_bin NOT NULL, `subtitle` varchar(350) COLLATE utf8_bin DEFAULT NULL, `summary` tinytext COLLATE utf8_bin, `thumbnail` varchar(350) CHARACTER SET latin1 NOT NULL, `size` bigint(20) NOT NULL DEFAULT '0', `upload_date` bigint(20) NOT NULL DEFAULT '0', `target` varchar(350) CHARACTER SET latin1 NOT NULL, `mimetype` varchar(350) COLLATE utf8_bin NOT NULL DEFAULT 'application/octet-stream', `mimegroup` varchar(350) CHARACTER SET latin1 NOT NULL DEFAULT 'document', `stream` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`ID`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
          "groups"  =>  "CREATE TABLE IF NOT EXISTS `%sgroups` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `name` varchar(350) COLLATE utf8_bin NOT NULL, `created_on` bigint(20) NOT NULL DEFAULT '0', `created_by` bigint(20) NOT NULL DEFAULT '0', PRIMARY KEY (`ID`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
          "groups_rel"  =>  "CREATE TABLE IF NOT EXISTS `%sgroups_rel` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `groupID` bigint(20) NOT NULL DEFAULT '0', `userID` bigint(20) NOT NULL DEFAULT '0', `status` varchar(50) NOT NULL DEFAULT 'active', PRIMARY KEY (`ID`), UNIQUE KEY `groupID` (`groupID`,`userID`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;",
          "messages"  =>  "CREATE TABLE IF NOT EXISTS `%smessages` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `userID` bigint(20) NOT NULL DEFAULT '0', `targetID` bigint(20) NOT NULL DEFAULT '0', `groupID` bigint(20) NOT NULL DEFAULT '0', `relationID` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '0', `message` longtext COLLATE utf8_bin NOT NULL, `sent_date` bigint(20) NOT NULL DEFAULT '0', `sent_from` bigint(20) NOT NULL DEFAULT '0', `sent_to` bigint(20) NOT NULL DEFAULT '0', `is_readed` int(11) NOT NULL DEFAULT '0', `is_opened` int(11) NOT NULL DEFAULT '0', `read_date` bigint(20) NOT NULL DEFAULT '0', `read_datetime` date NOT NULL DEFAULT '0000-00-00', `read_timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `has_attachment` int(11) NOT NULL DEFAULT '0', `is_notice` int(11) NOT NULL DEFAULT '0', `notice_section` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'left', `datetime` date NOT NULL DEFAULT '0000-00-00', `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', `source_code` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT 'chat', `source_name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT 'Chat', PRIMARY KEY (`ID`), FULLTEXT KEY `message` (`message`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
          "notif" =>  "CREATE TABLE IF NOT EXISTS `%snotif` (`ID` bigint(20) NOT NULL AUTO_INCREMENT, `subject` varchar(350) COLLATE utf8_bin NOT NULL, `sender` bigint(20) NOT NULL, `reciever` bigint(20) NOT NULL, `priority` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0', `datetime` bigint(20) NOT NULL, `content` text COLLATE utf8_bin NOT NULL, `expire` timestamp NULL DEFAULT NULL, PRIMARY KEY (`ID`), FULLTEXT KEY `content` (`content`), FULLTEXT KEY `subject` (`subject`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
          "online"  =>  "CREATE TABLE IF NOT EXISTS `%sonline` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `userID` bigint(20) NOT NULL DEFAULT '0', `user_status` enum('online','offline','busy','idle') NOT NULL DEFAULT 'online', `last_seen` bigint(20) NOT NULL DEFAULT '0', PRIMARY KEY (`ID`), UNIQUE KEY `userID` (`userID`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;",
          "online_rel"  =>  "CREATE TABLE IF NOT EXISTS `%sonline_rel` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `user` bigint(20) NOT NULL, `target` bigint(20) NOT NULL DEFAULT '0', `status` varchar(50) NOT NULL DEFAULT 'offline', `seen` bigint(20) NOT NULL DEFAULT '0', PRIMARY KEY (`ID`), UNIQUE KEY `user` (`user`,`target`) ) ENGINE=MyISAM DEFAULT CHARSET=latin1;",
          "relation"  =>  "CREATE TABLE IF NOT EXISTS `%srelation` (`ID` bigint(20) NOT NULL AUTO_INCREMENT, `mainID` bigint(20) NOT NULL DEFAULT '0', `targetID` bigint(20) NOT NULL DEFAULT '0', `targetIG` varchar(50) NOT NULL DEFAULT 'user', `structure` varchar(350) NOT NULL, `time` bigint(20) NOT NULL, `identifier` varchar(350) NOT NULL, PRIMARY KEY (`ID`), UNIQUE KEY `mainID` (`mainID`,`targetID`,`targetIG`,`structure`) ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;",
          "settings"  =>  "CREATE TABLE IF NOT EXISTS `%ssettings` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `name` varchar(350) CHARACTER SET latin1 NOT NULL, `value` text CHARACTER SET latin1 NOT NULL, PRIMARY KEY (`ID`) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
          "users"  =>  "CREATE TABLE IF NOT EXISTS `%susers` ( `ID` bigint(20) NOT NULL AUTO_INCREMENT, `name` varchar(350) NOT NULL, `email` varchar(350) NOT NULL, `username` varchar(350) NOT NULL, `password` varchar(350) NOT NULL, `avatar` varchar(350) NOT NULL, `user_rememberme_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL, PRIMARY KEY (`ID`) ) ENGINE=InnoDB  DEFAULT CHARSET=latin1;"
        );
  
        $drop_query = "DROP TABLE IF EXISTS `%sadmin`, `%sattachments`, `%sgroups`, `%sgroups_rel`, `%smessages`. `%snotif`, `%sonline`, `%sonline_rel`, `%srelation`, `%ssettings`, `%susers`;";
        $drop_args  = array_fill( 0, 11, $dbprefix );
        array_unshift( $drop_args, $drop_query );
        $drop_query = call_user_func_array( "sprintf", $drop_args );
        mysqli_query( $maindb_conn, $drop_query );
  
        $db_error_occured = false;
        $db_inserted  = array();
        $db_error_text  = null;
        foreach( $tables as $table_name => $table_query ) {
          $table_name   = $dbprefix.$table_name;
          $table_query  = sprintf( $table_query, $dbprefix );
          if ( mysqli_query( $maindb_conn, $table_query ) ) {
            $db_inserted[]  = '`'.$table_name.'`';
          }
          else {
            $db_error_text    = mysqli_error( $maindb_conn );
            $db_error_occured = true;
            break;
          }
        }
  
        if ( $db_error_occured ) {
          dropTables( $db_inserted, $config_file, $maindb_conn );
          $errors[] = "DB Error: Could not create table(s): (".$db_error_text.")";
        }
  
        if ( empty( $errors ) ) {
          $site_url1  = mysqli_real_escape_string( $maindb_conn, $site_url );
          $apassword  = escape( password_hash( $apassword, PASSWORD_DEFAULT, array( "cost" => 10, "salt" => mcrypt_create_iv( 22, MCRYPT_DEV_URANDOM ) ) ) );
          $api_key    = mysqli_real_escape_string( $maindb_conn, "" );
          $version    = mysqli_real_escape_string( $maindb_conn, "3.1" );
          $time       = mysqli_real_escape_string( $maindb_conn, time() );
          $insertErrors = array();
          if ( !mysqli_query( $maindb_conn, "INSERT INTO `{$dbprefix}admin` (`username`, `password`, `name`, `email`, `avatar`) VALUES ('{$ausername}', '{$apassword}', '{$aname}', '{$aemail}', 'images/avatar_default.jpg');" ) ) {
            $insertErrors[] = mysqli_error( $maindb_conn );
          }
          if ( !mysqli_query( $maindb_conn, "INSERT INTO `{$dbprefix}settings` (`name`, `value`) VALUES ('allowed_domains', ''), ('active_plugins', ''), ('active_theme', 'facebook'), ('language', 'en'), ('home_uri', '{$site_url1}'), ('blocked_files', ''), ('blocked_files_mode', 'blacklist'), ('api_key', '{$api_key}'), ('file_uploads', '1'), ('notification_layout', ''), ('enable_socket', '0'), ('socket_host', '".gethostbyname( $_SERVER["HTTP_HOST"] )."'), ('socket_port', '8787'), ('version', '{$version}'), ('admin_email', '{$aemail}');" ) ) {
            $insertErrors[] = mysqli_error( $maindb_conn );
          }
          if ( !mysqli_query( $maindb_conn, "INSERT INTO `{$dbprefix}notif` (`subject`, `sender`, `reciever`, `priority`, `datetime`, `content`, `expire`) VALUES ('Welcome to Impact Plus', 0, 0, '0', '{$time}', '<p>Welcome to <strong>Impact Plus</strong>, which is beyond your thoughts.\nThe most advanced and powerful chat software in the world. Play around here and explore the Features and Power.</p>\n<p>Thank you.</p><p style=\"font-size:11px;line-height:12px;padding:10px;background:#FFE8E8;border:1px solid #E29F9F;color:#B82424;\"><code>Warning: If you are using Google Chrome, in print dialog, <strong>DO NOT</strong> click window close button if you want to cancel printing, just click Cancel button instead.</code></p>', NULL);" ) ) {
            $insertErrors[] = mysqli_error( $maindb_conn );
          }
          if ( !empty( $insertErrors ) ) {
            dropTables( $db_inserted, $config_file, $maindb_conn );
            $errors[] = "DB Error: Could not execute some queries: <ol><li>".implode( "</li><li>", $insertErrors )."</li></ol>";
            $insertErrors = array();
          }
        }
  
        if ( empty( $errors ) ) {
          $finished = true;
        }
  
        if ( isset( $maindb_conn ) && is_resource( $maindb_conn ) ) {
          mysqli_close( $maindb_conn );
        }
        if ( isset( $userdb_conn ) && is_resource( $userdb_conn ) ) {
          mysqli_close( $userdb_conn );
        }
      }

    }
  }
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="content-type" content="text/html" />
	<meta name="author" content="bystwn22" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">

	<title>Impact Plus &rarr; Installation</title>

  <!--[if gte IE 9]>
    <style type="text/css">
      .gradient {
         filter: none;
      }
    </style>
  <![endif]-->

  <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Maven Pro:regular,bold,italic&subset=cyrillic" />
  <link rel="stylesheet" type="text/css" href="admin/css/install.css?<?php echo time(); ?>" />

  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="install.js"></script>
</head>

<body>

  <div class="container">
    <header>
      <div class="product-logo">
        <img src="images/p-logo.png" />
      </div>
    </header>
    <section id="content">
      <?php
        if ( empty( $can_start_install ) && $warnings_ignored === true ) {
          if ( !$finished ) {
            if ( !empty( $errors ) ) {
      ?>
      <div class="bubble bubble-low-radius bubble-error" style="margin-bottom: 20px;">
        <h2>We are sorry, we cannot continue installation, the following errors were found</h2>
        <ol>
          <?php
            foreach( $errors as $error ) {
          ?>
          <li><?php echo $error; ?></li>
          <?php
            }
          ?>
        </ol>
      </div>
      <?php
            }
      ?>
      <form action="install.php" method="POST" class="form-horizontal row-fluid" autocomplete="off">
        <h2>Database</h2>
        <div class="form-group">
          <div class="control-group">
            <label for="dbhost">Host <span class="asterik"></span></label>
            <input type="text" class="<?php errorClass( "dbhost", $fields ); ?>" name="dbhost" id="dbhost" placeholder="Either a host name or an IP address" value="<?php echo htmlspecialchars( $dbhost ); ?>" class="span12" required>
            <small>Note: In the case that your DB connection dosn't use <strong>STANDARD PORT</strong> for, you need to add '<strong>:port_number</strong>', at the end Database host parameter.<br />Example: you use MySQL running on port 6606, on server matrix, then Database host will be <em>matrix:6606</em></small>
          </div>
          <div class="control-group">
            <label for="dbuser">Username <span class="asterik"></span></label>
            <input type="text" class="<?php errorClass( "dbuser", $fields ); ?>" name="dbuser" id="dbuser" placeholder="The MySQL user name" value="<?php echo htmlspecialchars( $dbuser ); ?>" class="span12" required>
          </div>
          <div class="control-group">
            <label for="dbpass">Password</label>
            <input type="text" class="<?php errorClass( "dbpass", $fields ); ?>" name="dbpass" id="dbpass" placeholder="The password to log in with" value="<?php echo htmlspecialchars( $dbpass ); ?>" class="span12">
          </div>
          <div class="control-group">
            <label for="dbname">Name <span class="asterik"></span></label>
            <input type="text" class="<?php errorClass( "dbname", $fields ); ?>" name="dbname" id="dbname" placeholder="The default database to be used when performing queries for Impact Plus" value="<?php echo htmlspecialchars( $dbname ); ?>" class="span12" required>
            <small>The database name can contains any character that is allowed in a directory name, except '/', '\', or '.'</small>
          </div>
          <div class="control-group">
            <label for="dbprefix">Table Prefix</label>
            <input type="text" class="<?php errorClass( "dbprefix", $fields ); ?>" name="dbprefix" id="dbprefix" placeholder="Enter a database tables prefix" value="<?php echo htmlspecialchars( $dbprefix ); ?>" class="span12">
            <small>Note: This parameter should be empty for the most of cases.<br /><strong>Using a Database shared with other applications:</strong> Impact Plus can be installed (using this installer) on a existing database used by another application, using a table prefix.</small>
          </div>
          <div class="form-subgroup">
            <h2>User Database</h2>
            <label class="label-only bubble">
              <input type="checkbox" name="douserdb" id="douserdb" value="1"<?php echo ( $douserdb === true ) ? ' checked' : null; ?>>
              <strong>We don't have a user database</strong>
              <small>(check this only if you don't have user database or don't want to use chat with existing users)</small>
            </label>
            <label class="label-only bubble">
              <input type="checkbox" name="userdbinit" id="userdbinit" value="1"<?php echo ( $userdbinit === true && $douserdb === false ) ? null : ' checked'; echo ( $douserdb === true ) ? ' disabled' : null; ?>>
              <strong>Our user database is as same as the main database details provided above</strong>
              <small>(check this only if your users table is in same database)</small>
            </label>
            <div class="user-db-controls" style="<?php echo ( $douserdb === true || $userdbinit === false ) ? "display:none;" : "display:block;" ?>">
              <div class="control-group">
                <label for="udbhost">Host <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "udbhost", $fields ); ?>" name="udbhost" id="udbhost" placeholder="Either a host name or an IP address" value="<?php echo htmlspecialchars( $udbhost ); ?>" class="span12" required<?php echo ( $douserdb === true || $userdbinit === false ) ? ' disabled' : null; ?>>
                <small>Note: In the case that you DB connection dosn't use <strong>STANDARD PORT</strong> for, you need to add '<strong>:port_number</strong>', at the end Database host parameter.<br />Example: you use MySQL running on port 6606, on server matrix, then Database host will be <em>matrix:6606</em></small>
              </div>
              <div class="control-group">
                <label for="udbuser">Username <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "udbuser", $fields ); ?>" name="udbuser" id="udbuser" placeholder="The MySQL user name" value="<?php echo htmlspecialchars( $udbuser ); ?>" class="span12" required<?php echo ( $douserdb === true || $userdbinit === false ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="udbpass">Password</label>
                <input type="text" class="<?php errorClass( "udbpass", $fields ); ?>" name="udbpass" id="udbpass" placeholder="The password to log in with" value="<?php echo htmlspecialchars( $udbpass ); ?>" class="span12"<?php echo ( $douserdb === true || $userdbinit === false ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="udbname">Name <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "udbname", $fields ); ?>" name="udbname" id="udbname" placeholder="The default database to be used when performing user queries for Impact Plus" value="<?php echo htmlspecialchars( $udbname ); ?>" class="span12" required<?php echo ( $douserdb === true || $userdbinit === false ) ? ' disabled' : null; ?>>
                <small>The database name can contains any character that is allowed in a directory name, except '/', '\', or '.'</small>
              </div>
            </div>
            <h2>User Table</h2>
            <div class="label-only bubble user-tb-controls-info" style="<?php echo ( $douserdb === true ) ? 'display:block;' : null; ?>">
              <strong>This feature is not available right now.</strong>
            </div>
            <div class="user-tb-controls" style="<?php echo ( $douserdb === true ) ? 'display:none;' : null; ?>">
              <div class="control-group">
                <label for="utbname">Table Name <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "utbname", $fields ); ?>" name="utbname" id="utbname" placeholder="users" value="<?php echo htmlspecialchars( $utbname ); ?>" class="span12" required<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="ucolid">'<strong>ID</strong>' column name <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "ucolid", $fields ); ?>" name="ucolid" id="ucolid" placeholder="ID" value="<?php echo htmlspecialchars( $ucolid ); ?>" class="span12" required<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="ucoluname">'<strong>Username</strong>' column name <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "ucoluname", $fields ); ?>" name="ucoluname" id="ucoluname" placeholder="username" value="<?php echo htmlspecialchars( $ucolname ); ?>" class="span12" required<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="ucolpass">'<strong>Password</strong>' column name</label>
                <input type="text" class="<?php errorClass( "ucolpass", $fields ); ?>" name="ucolpass" id="ucolpass" placeholder="password" value="<?php echo htmlspecialchars( $ucolname ); ?>" class="span12"<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="ucolname">'<strong>Name</strong>' column name <span class="asterik"></span></label>
                <input type="text" class="<?php errorClass( "ucolname", $fields ); ?>" name="ucolname" id="ucolname" placeholder="name" value="<?php echo htmlspecialchars( $ucolname ); ?>" class="span12" required<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
              </div>
              <div class="control-group">
                <label for="ucolavatar">'<strong>Avatar</strong>' column name</label>
                <input type="text" class="<?php errorClass( "ucolavatar", $fields ); ?>" name="ucolavatar" id="ucolavatar" placeholder="avatar" value="<?php echo htmlspecialchars( $ucolavatar ); ?>" class="span12"<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
                <small>(Table will be altered with column '<?php echo ( $ucolemail ) ? $ucolavatar : "avatar"; ?>', if does not exists)</small>
              </div>
              <div class="control-group">
                <label for="ucolemail">'<strong>Email</strong>' column name</label>
                <input type="text" class="<?php errorClass( "ucolemail", $fields ); ?>" name="ucolemail" id="ucolemail" placeholder="email" value="<?php echo htmlspecialchars( $ucolemail ); ?>" class="span12"<?php echo ( $douserdb === true ) ? ' disabled' : null; ?>>
                <small>(Table will be altered with the column '<?php echo ( $ucolemail ) ? $ucolemail : "email"; ?>', if does not exists)</small>
              </div>
            </div>
          </div>
        </div>
        <h2>Administrator</h2>
        <div class="control-group">
          <label for="ausername">Username <span class="asterik"></span></label>
          <input type="text" class="<?php errorClass( "ausername", $fields ); ?>" name="ausername" id="ausername" placeholder="Admin username" value="<?php echo htmlspecialchars( $ausername ); ?>" class="span12" required>
        </div>
        <div class="control-group">
          <label for="apassword" class="clearfix">Password <span class="asterik"></span> <span class="password_strength"><span class="meter"></span></span> <span class="strength_txt">Weak</span></label>
          <input type="text" class="<?php errorClass( "apassword", $fields ); ?>" name="apassword" id="apassword" placeholder="Admin Password" value="" class="span12" required>
        </div>
        <div class="control-group">
          <label for="aname">Name <span class="asterik"></span></label>
          <input type="text" class="<?php errorClass( "aname", $fields ); ?>" name="aname" id="aname" placeholder="Admin Name" value="<?php echo htmlspecialchars( $aname ); ?>" class="span12" required>
        </div>
        <div class="control-group">
          <label for="aemail">E-mail <span class="asterik"></span></label>
          <input type="email" class="<?php errorClass( "aemail", $fields ); ?>" name="aemail" id="aemail" placeholder="Admin E-mail address" value="<?php echo htmlspecialchars( $aemail ); ?>" class="span12" required>
        </div>
        <h2>Website</h2>
        <div class="control-group">
          <label for="root_url">Root Directory <span class="asterik"></span></label>
          <input type="text" class="<?php errorClass( "root_url", $fields ); ?>" name="root_url" id="root_url" placeholder="<?php echo htmlspecialchars( getPathName() ); ?>" value="<?php echo htmlspecialchars( $root_url ); ?>" class="span12" required>
          <small>Absolute path to root, where <strong>ipChat</strong> folder contains <strong>(Dont forget to add a trailing slash to the path)</strong></small>
        </div>
        <div class="control-group">
          <label for="site_url">Site URL <span class="asterik"></span></label>
          <input type="text" class="<?php errorClass( "site_url", $fields ); ?>" name="site_url" id="site_url" placeholder="<?php echo htmlspecialchars( getSiteName() ); ?>" value="<?php echo htmlspecialchars( $site_url ); ?>" class="span12" required>
          <small>Your sites full URL to root, where <strong>ipChat</strong> folder contains <strong>(Dont forget to add a trailing slash to the url)</strong></small>
        </div>
        <div class="control-group control-submit">
          <button type="submit" name="action" value="install" class="button">
            <span>Alright! Let's Start</span>
          </button>
        </div>
      </form>
      <?php
          }
          else {
      ?>
      <div class="bubble bubble-low-radius" style="margin-bottom: 20px;">
        <h2>Installation Successful</h2>
        <h4 style="text-align: center;">
          Thank you for installing <strong>Impact Plus</strong>.<br />
          <strong>Impact Plus</strong> has been successfully installed on your server.
        </h4>

        <a href="<?php echo $site_url; ?>ipChat/admin/" class="button"><span>Goto Dashboard</span></a>
      </div>
      <?php
          }
        }
        else {
      ?>
      <h2>
        Oh snaps! You cannot install Impact Plus on your server.
      </h2>
      <div class="bubble bubble-low-radius bubble-error">
        <ol>
          <?php
            if ( !empty( $can_start_install ) ) {
              foreach( $can_start_install as $req ) {
          ?>
          <li class="error-required"><?php echo $req; ?>&nbsp;<label class="badge badge-warning">error,&nbsp;required</label></li>
          <?php
              }
            }
            if ( !empty( $warnings ) ) {
              foreach( $warnings as $warning ) {
          ?>
          <li class="error-optional"><?php echo $warning; ?>&nbsp;<label class="badge badge-info">warning,&nbsp;optional</label></li>
          <?php
              }
            }
          ?>
        </ol>
        <?php
          if ( empty( $can_start_install ) ) {
        ?>
        <p class="text-right" style="margin-top: 20px;">
          <a href="install.php?ignore" class="button">Ignore &amp; Continue &rarr;</a>
        </p>
        <?php
          }
          else {
        ?>
        <p class="text-right" style="margin-top: 20px;">
          <a href="javascript:void(0);" class="button disabled">Cannot Ignore Errors</a>
        </p>
        <?php
          }
        ?>
      </div>
      <?php
        }
      ?>

    </section>
    <footer>
      <p class="copyright">Copyright &copy; 2014 - 2015, <a href="<?php echo $impact_plus_uri; ?>" target="_blank" rel="nofollow">Impact Plus</a></p>
    </footer>
  </div>

  <div id="docs-butterbar-container" class="docs-butterbar-container">
    <div class="docs-butterbar-wrap">
      <div class="jfk-butterBar jfk-butterBar-info jfk-butterBar-shown">Trying to connect...</div>
    </div>
  </div>
</body>
</html>
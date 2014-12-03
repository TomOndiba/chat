<?php

/**
 * Below are the basic and important hooks for imapct plus used to handle user authentification
 * Read the full documentation at http://ip.bestunmask.cu.cc/docs/
 **/

/**
 * Hook: "user_signup"
 * This hooks is triggered when user submits the signup form (only if default signup handler not changed)
 * 
 * @param $username The username user chosen
 * @param $email The users email address
 * @param $password user entered password
 * @param $password_confirm user entered confirmed password
 * 
 * @return array ex:  array(
 *                      "error" =>  1 if has error else 0
 *                      "message" =>  The response message
 *                    )
 */
add_action( "user_signup", function( $username = null, $email = null, $password = null, $password_confirm = null ) {
  $data = array(
    "error"   =>  1,
    "message" =>  "Registration successfully completed, please check your mail inbox for activation email"
  );
  if ( !$username || strlen( $username ) < 3 ) {
    $data["message"]  = "You must provide a valid username of minimum length 3";
  }
  else if ( !$email || !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
    $data["message"]  = "You must provide a valid email address to continue signup";
  }
  else if ( !$password || !$password_confirm ) {
    $data["message"]  = "You must provide a password and you must verify the password";
  }
  else if ( $password !== $password_confirm ) {
    $data["message"]  = "Both passwords are different, please verify";
  }
  else {
    /**
     * $ipdb is the default database connection variable
     * if your using your own database or table, then you must use `$ipudb` instead
     * We are using ezSql, read more here for function reference http://justinvincent.com/ezsql
     **/
    global $ipdb;

    $username = $ipdb->escape( $username );
    $password = $ipdb->escape( PassHash::hash( $password ) );
    $email    = $ipdb->escape( $email );

    $username_exists  = $ipdb->get_var( "SELECT COUNT(*) FROM `$ipdb->users` WHERE `username` = '{$username}'" );
    $email_exists     = $ipdb->get_var( "SELECT COUNT(*) FROM `$ipdb->users` WHERE `email` = '{$email}'" );

    if ( !$username_exists && !$email_exists ) {
      $signup = $ipdb->query( "INSERT INTO `$ipdb->users`(`username`,`name`,`email`,`password`) VALUES('{$username}','{$username}','{$email}','{$password}')" );
      if ( $signup ) {
        $data["error"]  = 0;
        writeLoginCredentials( $ipdb->get_var( "SELECT `ID` FROM `$ipdb->users` WHERE `username` = '{$username}'" ), true );
      }
      else {
        $data["message"]  = "We were unable to create your account";
      }
    }
    else {
      $data["message"]  = "Username or email already exists";
    }
  }
  return $data;
});

/**
 * Hook: "user_login"
 * This hooks is triggered when user tries to login (only if default login handler not changed)
 * 
 * @param $username user entered username
 * @param $password user entered password
 * 
 * @return array ex:  array(
 *                      "error" =>  1 if has error else 0
 *                      "message" =>  The response message
 *                    )
 */
add_action( "user_login", function( $username = null, $password = null ) {
  $data = array(
    "error"   =>  1,
    "message" =>  "Login successfully completed, please wait while we redirecting you"
  );
  if ( !$username || strlen( $username ) < 3 ) {
    $data["message"]  = "You must provide a valid username of minimum length 3";
  }
  else if ( !$password ) {
    $data["message"]  = "You must provide a password";
  }
  else {
    /**
     * $ipdb is the default database connection variable
     * if your using your own database or table, then you must use `$ipudb` instead
     * We are using ezSql, read more here for function reference http://justinvincent.com/ezsql
     **/
    global $ipdb;

    $username   = $ipdb->escape( $username );
    $user_info  = $ipdb->get_row( "SELECT `ID`, `password` FROM `$ipdb->users` WHERE `username` = '{$username}' OR `email` = '{$username}'" );

    if ( !$user_info ) {
      $data["message"]  = "Invalid username, please enter your username";
    }
    else {
      if ( PassHash::compare_hash( $user_info->password, $password ) ) {
        writeLoginCredentials( $user_info->ID, true );
        $data["error"]  = 0;
      }
      else {
        $data["message"]  = "Invalid password, please enter your password";
      }
    }
  }
  return $data;
});

/**
 * Hook: "user_reset_pass"
 * This hooks is triggered when user tries to reset password (only if default reset password handler not changed)
 * 
 * @param $username user entered username/email address
 * 
 * @return array ex:  array(
 *                      "error" =>  1 if has error else 0
 *                      "message" =>  The response message
 *                    )
 */
add_action( "user_reset_pass", function( $username = null ) {
  $data = array(
    "error"   =>  1,
    "message" =>  "Password successfully reset. Please check your email"
  );

  if ( !$username || strlen( $username ) < 3 ) {
    // there is an error
    $data["message"]  = "You must provide a valid username or email address";
  }
  else {
    /**
     * $ipdb is the default database connection variable
     * if your using your own database or table, then you must use `$ipudb` instead
     * We are using ezSql, read more here for function reference http://justinvincent.com/ezsql
     **/
    global $ipdb;

    $username = $ipdb->escape( $username );
    $email    = $ipudb->get_var( "SELECT `email` FROM `$ipudb->users` WHERE `username` = '{$user}' OR `email` = '{$user}'" );

    if ( !$email ) {
      // there is an error
      $data["message"]  = "Invalid username or email address, please try again";
    }
    else {
      /**
       * function ipgo is used to get impact plus settings
       * usage: ipgo( "settings_name" );
       **/
      $admin_email  = ipgo( "admin_email" );
      // Generate a strong random password
      $password     = generate_password();
      // Generate the password hash
      $password_hash  = $ipudb->escape( PassHash::hash( $password ) );

      // the body of mail
      $message  = '<html><head><title>Password Reset</title></head>
      <body>
        <p>Hi, you have requested to reset your password on Impact Plus Chat</p>
        <p>Your new password is <strong>'.$password.'</strong><br />
        <a href="'.site_uri().'">Click here</a> to login to your account.</p>
        <p>&nbsp;</p>
        <small>Please do not reply to this email because we are not monitoring this inbox.</small>
      </body>
    </html>';
      // mail headers
      $headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\nFrom: {$admin_email}\r\n";
      // if mail send successfully
      if ( @mail( $email, "Password Reset", $message, $headers ) ) {
        // update the user password with new one previously generated
        if ( $ipudb->query( "UPDATE `$ipudb->users` SET `{$ipudb->table->pass}` = '{$password_hash}' WHERE `{$ipudb->table->email}` = '{$email}'" ) ) {
          $data["error"]  = 0;
        }
        else {
          // there is an error
          $data["message"]  = "Error while updating database. Please try again.";
        }
      }
      else {
        // there is an error
        $data["message"]  = "Could not send mail. Please try again.";
      }
    }
  }
  return $data;
});

/**
 * Hook: "user_logout"
 * This hooks is triggered when user tries to logout (only if default logout handler not changed)
 * 
 * @return true if logged out else false
 */
add_action( "user_logout", function() {
  // destroy the session and cookies
  session_destroy();
  setcookie( "ip_login_remember", null, time() - 100, "/" );
  return true;
});

/**
 * Hook: "user_logout"
 * This hooks is triggered when we check whether user is logged in or not
 * 
 * @return true if logged in else false
 */
add_action( "is_logged_in", function() {
  /**
   * Some random check to check user logged in or not
   **/
  if ( isset( $_SESSION["userLoggedInID"] ) ) {
    return true;
  }

  list( $user_id, $token, $hash ) = getRememberMeSession();
  if ( $hash !== hash( "sha256", $user_id.":".$token ) ) {
    return false;
  }
  if ( !empty( $token ) ) {
    /**
     * $ipdb is the default database connection variable
     * if your using your own database or table, then you must use `$ipudb` instead
     * We are using ezSql, read more here for function reference http://justinvincent.com/ezsql
     **/
    global $ipdb;

    $user_id  = $ipdb->escape( $user_id );
    $token    = $ipdb->escape( $token );

    $is_valid_token = $ipdb->get_var( "SELECT COUNT(*) FROM `$ipdb->users` WHERE `ID` = '{$user_id}' AND `user_rememberme_token` = '{$token}' AND `user_rememberme_token` IS NOT NULL" );

    if ( $is_valid_token ) {
      $_SESSION["userLoggedInID"] = $user_id;
      return true;
    }
  }
  return false;
});

/**
 * Hook: "user_logout"
 * This hooks is triggered when we want to get current user id
 * 
 * @return the user id if logged in else false
 */
add_action( "get_user_id", function() {
  // if user is logged in
  if ( is_logged_in() ) {
    // return the user id previously stored when logged in
    return $_SESSION["userLoggedInID"];
  }
  return false;
});

/**
 * Hook: "user_avatar"
 * This hooks is triggered when user uploaded new avatar
 * 
 * @param $user_id the user's id
 * @param $user_avatar user uploaded avatar url
 * 
 * @return the absolute link of avatar if successfully inserted into database else false
 */
add_action( "user_avatar", function( $user_id, $user_avatar ) {
  // get the user info by id
  $user_info  = ip_get_user_info( $user_id );
  // if not user info, return false
  if ( !$user_info ) {
    return false;
  }

  // check whether old avatar available and its not an external link or default avatar image
  $old_avatar = ( stristr( $user_info->AV, "Chat" ) !== false && stristr( $user_info->AV, "http" ) === false && basename( $user_info->AV ) !== "default.jpg" ) ? realpath( dirname( ROOT_DIR ).trim( $user_info->AV ) ) : false;
  // if above conditions are met
  if ( $old_avatar ) {
    // delete old avatar from server
    @unlink( $old_avatar );
  }

  // update user avatar with new one
  if ( ip_update_user_info( $user_id, "avatar", $user_avatar ) ) {
    // return the new avatar link
    return $user_avatar;
  }

  // return false if something is not rigr
  return false;
});


/**
 * Below functions are just created for our default login service
 * you can remove this if you are using your own database and verifications
**/

/**
 * writeLoginCredentials()
 * 
 * @param int $user_id the user id
 * @param bool $cookie whether set cookie or not
 * @return null
 */
function writeLoginCredentials( $user_id = null, $cookie = false ) {
  global $ipdb;
  // generate 64 char random string
  $token_string = $ipdb->escape( hash( "sha256", mt_rand() ) );

  // write that token into database
  $ipdb->query( "UPDATE `$ipdb->users` SET `user_rememberme_token` = '{$token_string}' WHERE `ID` = '{$user_id}'" );

  // generate cookie string that consists of user id, random string and combined hash of both
  $string_first_part  = $user_id.":".$token_string;
  $string_hash  = hash( "sha256", $string_first_part );
  $string  = $string_first_part.":".$string_hash;

  $_SESSION["ip_login_remember"]  = $string;

  if ( $cookie ) {
    // set cookie
    setcookie( "ip_login_remember", $string, ( time() + 1209600 ), "/" );
  }
}

/**
 * getRememberMeSession()
 * parse and return users current token from session/cookie
 * @return the parsed token if exists else false
 */
function getRememberMeSession() {
  if ( isset( $_SESSION["ip_login_remember"] ) ) {
    return explode( ":", $_SESSION["ip_login_remember"] );
  }
  elseif ( isset( $_COOKIE["ip_login_remember"] ) ) {
    return explode( ":", $_COOKIE["ip_login_remember"] );
  }
  return false;
}
?>
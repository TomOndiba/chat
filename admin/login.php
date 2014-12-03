<?php
  $phpFile  = strtolower( trim( pathinfo( $_SERVER["PHP_SELF"], PATHINFO_FILENAME ) ) );

  require_once( dirname( dirname( __FILE__ ) )."/includes/required/admin.class.php" );
  $referer  = ( isset( $_SERVER["HTTP_REFERER"] ) ) ? $_SERVER["HTTP_REFERER"] : admin_uri();
  $referer  = ( isset( $_GET["referer"] ) ) ? urldecode( $_GET["referer"] ) : $referer;

  if ( strtolower( $_SERVER["REQUEST_METHOD"] ) === "post" ) {
    $referer  = ( isset( $_POST["referer"] ) ) ? urldecode( $_POST["referer"] ) : $referer;

    if ( isset( $_POST["username"], $_POST["password"] ) ) {
      global $ipdb;
      $username = $_POST["username"];
      $password = $_POST["password"];

      if ( ModLogin::verifyCredentials( $username, $password ) ) {
        header( "Location: ".$referer );
        exit();
      }

      $errorLogin = ModLogin::getError();
    }
  }

  if ( ModLogin::isLogged() ) {
    $isExpired  = ModLogin::isExpired();
    if ( !$isExpired ) {
      header( "Location: ".$referer );
      exit();
    }
  }

  if ( $phpFile == "language" && ( !isset( $_GET["lang"] ) && !isset( $_GET["install"] ) ) && strtolower( $_SERVER["REQUEST_METHOD"] ) !== "post" ) {
    header( "Location: ".admin_uri()."language.php?lang=en" );
    exit();
  }

  registerComponents( $headComponents, $phpFile );
  $isSidebarHided = ( isset( $_COOKIE["sidebarHide"] ) ) ? true : false;
?>
<!DOCTYPE html>
  <html lang="en" class="bg-dark">
  <head>
    <meta charset="utf-8"/>
    <title>Impact Plus &rarr; Login</title>
    <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="stylesheet" href="<?php echo admin_uri(); ?>gzip.php?l=<?php echo urlencode( "css,font,app.v2" ); ?>&nv" type="text/css"/>
    <!--[if lt IE 9]>
      <script src="js/ie/html5shiv.js" cache="false"></script>
      <script src="js/ie/respond.min.js" cache="false"></script>
      <script src="js/ie/excanvas.js" cache="false"></script>
    <![endif]-->
  </head>
<body class="<?php echo ( isset( $isExpired ) ) ? "modal-open" : null ?>">
  <?php
    if ( isset( $isExpired ) ) {
  ?>
  <form action="login.php" method="POST">
    <div class="modal" id="ajaxModal" aria-hidden="false" style="display: block;">
      <div class="modal-over">
        <div class="modal-center animated fadeInUp text-center" style="width:200px;margin:<?php echo ( isset( $errorLogin ) ) ? "-115" : "-80"; ?>px 0 0 -100px;">
          <?php echo ( isset( $errorLogin ) ) ? '<p class="alert alert-danger">'.$errorLogin.'</p>' : null; ?>
          <div class="thumb-md">
            <img src="<?php echo admin_uri().$isExpired->avatar; ?>" class="img-circle b-a b-light b-3x">
          </div>
          <p class="text-white h4 m-t m-b"><?php echo ( $isExpired->name ) ? $isExpired->name : $isExpired->username; ?></p>
          <div class="input-group">
            <input type="hidden" name="username" value="<?php echo $isExpired->username; ?>" />
            <input type="hidden" name="remember" value="1"<?php echo ( isset( $_COOKIE["mod_auth_id"] ) && isset( $_COOKIE["mod_auth_hash"] ) ) ? " checked" : null; ?> />
            <input type="hidden" name="referer" value="<?php echo $referer; ?>" />
            <input type="password" name="password" class="form-control text-sm" placeholder="Enter pwd to continue"><span class="input-group-btn"><button class="btn btn-success" type="submit" name="action" value="login"><i class="fa fa-arrow-right"></i></button></span>
          </div>
          <a href="<?php echo admin_uri(); ?>forgot-pass.php" class="pull-right m-t-xs"><small>Forgot password?</small></a>
        </div>
      </div>
    </div>
  </form>
  <div class="modal-backdrop in"></div>
  <?php
    }
    else {
  ?>
  <section id="content" class="m-t-lg wrapper-md animated fadeInUp">
    <div class="container aside-xxl">
      <a class="navbar-brand block" href="<?php echo admin_uri(); ?>">Impact Plus</a>
      <section class="panel panel-default bg-white m-t-lg">
        <header class="panel-heading text-center"><strong>Sign in</strong></header>
        <form action="login.php" class="panel-body wrapper-lg" method="POST">
          <?php echo ( isset( $errorLogin ) ) ? '<div class="alert alert-danger">'.$errorLogin.'</div>' : null; ?>
          <div class="form-group">
            <label class="control-label">Username</label>
            <input type="username" id="inputUsername" name="username" class="form-control input-lg" placeholder="Username" value="<?php echo ( isset( $username ) ) ? $username : null; ?>">
          </div>
          <div class="form-group">
            <label class="control-label">Password</label>
            <input type="password" id="inputPassword" name="password" class="form-control input-lg" placeholder="Password">
          </div>
          <div class="checkbox">
            <label><input type="checkbox" name="remember" value="1"> Keep me logged in</label>
          </div>
          <a href="<?php echo admin_uri(); ?>forgot-pass.php" class="pull-right m-t-xs"><small>Forgot password?</small></a>
          <input type="hidden" name="referer" value="<?php echo $referer; ?>" />
          <button type="submit" name="action" value="login" class="btn btn-primary">Sign in</button>
        </form>
      </section>
    </div>
  </section>

  <!-- footer -->
  <footer id="footer">
    <div class="text-center padder">
      <p>
        <small>A Powerful Chat app, which is beyond your thoughts<br>&copy; 2014 - 2015</small>
      </p>
    </div>
  </footer>
  <?php
    }
  ?>

  <!-- / footer -->
  <script src="<?php echo admin_uri(); ?>gzip.php?l=<?php echo urlencode( "js,jquery,app.v2" ); ?>&nv"></script>
</body>
</html>
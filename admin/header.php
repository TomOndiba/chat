<?php
  $referer  = ( isset( $_SERVER["HTTP_REFERER"] ) ) ? $_SERVER["HTTP_REFERER"] : $_SERVER["PHP_SELF"];
  $phpFile  = strtolower( trim( pathinfo( $_SERVER["PHP_SELF"], PATHINFO_FILENAME ) ) );
  require_once( dirname( dirname( __FILE__ ) )."/includes/required/admin.class.php" );

  if ( strtolower( $_SERVER["REQUEST_METHOD"] ) === "post" ) {
    $referer  = ( isset( $_POST["referer"] ) ) ? urldecode( $_POST["referer"] ) : $referer;
  }

  if ( !ModLogin::isLogged() || ModLogin::isExpired() ) {
    header( "Location: ".admin_uri()."login.php?referer=".$_SERVER["REQUEST_URI"] );
    exit();
  }

  $admin  = ModLogin::getInfo();

  if ( $phpFile == "language" && ( !isset( $_GET["lang"] ) && !isset( $_GET["install"] ) ) && strtolower( $_SERVER["REQUEST_METHOD"] ) !== "post" ) {
    header( "Location: ".admin_uri()."language.php?lang=en" );
    exit();
  }

  registerComponents( $headComponents, $phpFile );
  $isSidebarHided = ( isset( $_COOKIE["sidebarHide"] ) ) ? true : false;

  loadClass( "ImpactPlus", "required/impact.plus.php" );
  $has_updates  = (int)ImpactPlus::has_updates();
?>
<!DOCTYPE html>
<html lang="en" class="app">
  <head>
    <meta charset="utf-8"/>
    <title>Impact Plus &rarr; Admin Panel</title>
    <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>

    <?php
      foreach( (array)$headComponents->load( "css" ) as $cssComponent ) {
    ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $cssComponent; ?>" />
    <?php
      }
    ?>

    <!--[if lt IE 9]>
      <script src="js/ie/html5shiv.js" cache="false"></script>
      <script src="js/ie/respond.min.js" cache="false"></script>
      <script src="js/ie/excanvas.js" cache="false"></script>
    <![endif]-->

    <script type="text/javascript">
      <?php
        $scriptParams = array(
          "chat_uri"  =>  json_encode( site_uri() ),
          "admin_uri" =>  json_encode( admin_uri() ),
          "mentor"    =>  json_encode( mentor_uri() ),
          "sleeper"   =>  json_encode( ModLogin::getSession( "ti" ) ),
          "auto_update" =>  'true',
          "ip_api_key"  =>  json_encode( ipgo( "api_key" ) ),
          "ip_version"  =>  json_encode( (float)ipgo( "version" ) ),
          "searchBox"   =>  'true'
        );
        foreach( $scriptParams as $key => $val ) {
          $scriptParams[$key] = sprintf( "%s=%s", $key, $val );
        }
        echo sprintf( "var %s;", implode( ",", $scriptParams ) );
      ?>
    </script>

  </head>
  <body>
    <div id="fb-root"></div>
    <section class="vbox">
      <header class="bg-dark dk header navbar navbar-fixed-top-xs">
        <div class="navbar-header aside-md">
          <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen" data-target="#nav">
            <i class="fa fa-bars"></i>
          </a>
          <a href="<?php echo mentor_uri(); ?>" class="navbar-brand" data-toggle="fullscreen">
            <img src="<?php echo admin_uri(); ?>images/logo.png" class="m-r-sm">
            <span>Impact Plus</span>
          </a>
          <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".nav-user">
            <i class="fa fa-cog"></i>
          </a>
        </div>
        <ul class="nav navbar-nav hidden-xs">
          <li>
            <div class="m-t m-l">
              <a href="<?php echo admin_uri(); ?>updates.php" data-toggle="tooltip" data-placement="top auto" class="dropdown-toggle btn btn-xs btn-primary" title="<?php echo ( $has_updates ) ? $has_updates." updates(s)" : "Check for updates"; ?>">
                <?php echo ( $has_updates ) ? '<span>+'.$has_updates.'</span>' : '<i class="fa fa-long-arrow-up"></i>'; ?>
              </a>
            </div>
          </li>
        </ul>
        <ul class="nav navbar-nav navbar-right hidden-xs nav-user">
          <li class="hidden-xs">
            <a href="#" class="dropdown-toggle dk" data-toggle="dropdown">
              <i class="fa fa-bell"></i>
              <span class="badge badge-sm up bg-danger m-l-n-sm count">0</span>
            </a>
            <section class="dropdown-menu aside-xl">
              <section class="panel bg-white">
                <header class="panel-heading b-light bg-light">
                  <strong>You have <span class="count">0</span> notifications</strong>
                </header>
                <div class="list-group list-group-alt animated fadeInRight eventLeft"></div>
                <footer class="panel-footer text-sm">
                  <label class="switch switch-mini pull-right"><input id="events-toggler" type="checkbox" checked><span></span></label>
                </footer>
              </section>
            </section>
          </li>
          <li class="dropdown hidden-xs" id="searchBox">
            <a href="#" class="dropdown-toggle dker" data-toggle="dropdown">
              <i class="fa fa-fw fa-search"></i>
            </a>
            <?php
              $dataHref = null;
              if ( $phpFile === "plugins" ) {
                if ( isset( $_GET["action"] ) && $_GET["action"] === "install" ) {
                  $dataHref = array_merge( $_GET, array( "search" => "%s" ) );
                  $dataHref = ' data-href="'.admin_uri().'plugins.php?'.urldecode( http_build_query( $dataHref ) ).'"';
                }
              }
            ?>
            <section class="dropdown-menu aside-xl animated fadeInUp">
              <section class="panel bg-white">
                <form role="search" id="top-nav-search">
                  <div class="form-group wrapper m-b-none">
                    <div class="input-group">
                      <input type="text" class="form-control" value="<?php echo ( isset( $_GET["search"] ) ) ? $_GET["search"] : null; ?>" name="search" placeholder="Search..."<?php echo $dataHref; ?> autofocus>
                      <span class="input-group-btn">
                        <button type="submit" class="btn btn-info btn-icon" name="search-action">
                          <i class="fa fa-search"></i>
                        </button>
                      </span>
                    </div>
                  </div>
                </form>
              </section>
            </section>
          </li>
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <span class="thumb-sm avatar pull-left">
                <img src="<?php echo admin_uri().$admin->avatar; ?>">
              </span>
              <?php echo ( $admin->name ) ? $admin->name : $admin->username; ?>
              <b class="caret"></b>
            </a>
            <ul class="dropdown-menu animated fadeInRight">
              <span class="arrow top"></span>
              <li><a href="<?php echo admin_uri()."settings.php"; ?>">Settings</a></li>
              <li><a href="<?php echo admin_uri()."profile.php"; ?>">Profile</a></li>
              <li><a href="<?php echo mentor_uri()."Docs/"; ?>" target="_blank">Help</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo admin_uri()."logout.php"; ?>">Logout</a></li>
            </ul>
          </li>
        </ul>
      </header>
      <section>
        <section class="hbox stretch">
          <!-- .aside -->
          <?php
            include( "aside.php" );
          ?>
          <!-- /.aside -->
          <section id="content">
            <section class="vbox">
              <section class="scrollable padder">
              <?php
                if ( $phpFile !== "index" ) {
              ?>
              <ul class="breadcrumb no-border no-radius b-b b-light pull-in">
             	  <li>
                  <a href="<?php echo admin_uri(); ?>"><i class="fa fa-home"></i> Home</a>
                </li>
                <?php
                  switch( $phpFile ) {
                    case "files":
                      echo BreadCrumb::files();
                    break;
                    case "groups":
                      echo '<li><a href="'.admin_uri().'messages.php">Messages</a></li>';
                      echo '<li class="active"><span>Groups</span></li>';
                    break;
                    case "messages":
                      echo BreadCrumb::messages();
                    break;
                    case "users":
                      echo BreadCrumb::users();
                    break;
                    case "plugins":
                      echo BreadCrumb::plugins();
                    break;
                    case "notifications":
                      echo BreadCrumb::notifications();
                    break;
                    case "languages":
                      echo BreadCrumb::languages();
                    break;
                    case "profile":
                      echo '<li><span>Profile</span></li>';
                    break;
                    case "updates":
                      echo '<li><span>Updates</span></li>';
                    break;
                    case "settings":
                      echo '<li class="active"><span>Settings</span></li>';
                    break;
                    case "language":
                      echo '<li><a href="'.admin_uri().'language.php">Languages</a></li>';
                      echo '<li><span>'.( isset( $_GET["install"] ) ? "Install" : $edit_lang_name ).'</span></li>';
                    break;
                    default:
                      echo '<li><span>404 &mdash; Page not found</span></li>';
                    break;
                  }
                ?>
              </ul>
              <?php
                }
              ?>
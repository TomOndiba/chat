<?php
  require_once( dirname( dirname( __FILE__ ) )."/includes/required/admin.class.php" );
  $referer  = ( isset( $_SERVER["HTTP_REFERER"] ) ) ? $_SERVER["HTTP_REFERER"] : admin_uri();
  $referer  = ( isset( $_GET["referer"] ) ) ? urldecode( $_GET["referer"] ) : $referer;
  $phpFile  = strtolower( trim( pathinfo( $_SERVER["PHP_SELF"], PATHINFO_FILENAME ) ) );

  $admin  = $ipdb->get_row( "SELECT * FROM `$ipdb->admin` WHERE 1" );
  if ( strtolower( $_SERVER["REQUEST_METHOD"] ) === "post" ) {
    $referer  = ( isset( $_POST["referer"] ) ) ? urldecode( $_POST["referer"] ) : $referer;

    if ( isset( $_POST["email"] ) ) {
      global $ipdb;
      $email  = $_POST["email"];

      if ( ModLogin::resetPassword( $email, $admin->email ) ) {
        $successLogin = "Your new Password were sent to your e-mail address";
      }
      else {
        $errorLogin   = ModLogin::getError();
      }
    }
  }

  if ( ModLogin::isLogged() && !ModLogin::isExpired() ) {
    $referer  = ( stristr( $referer, "login.php" ) ) ? admin_uri() : $referer;
    header( "Location: ".$referer );
    exit();
  }

  registerComponents( $headComponents, $phpFile );
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
<body class="modal-open">
  <?php
    $last_login = ( $admin->last_login > 0 ) ? $admin->last_login : ( ( $admin->current_login > 0 ) ? $admin->current_login : 0 );
  ?>
  <form action="<?php echo admin_uri(); ?>forgot-pass.php" method="POST">
    <div class="modal" id="ajaxModal" aria-hidden="false" style="display: block;">
      <div class="modal-over">
        <div class="modal-center animated fadeInUp text-center" style="width:200px;margin:<?php echo ( isset( $errorLogin ) || isset( $successLogin ) ) ? "-125" : "-90"; ?>px 0 0 -100px;">
          <?php
            echo ( isset( $errorLogin ) ) ? '<p class="alert alert-danger">'.$errorLogin.'</p>' : null;
            echo ( isset( $successLogin ) ) ? '<p class="alert alert-success">'.$successLogin.'</p>' : null;
          ?>
          <div class="thumb-md">
            <img src="<?php echo admin_uri().$admin->avatar; ?>" class="img-circle b-a b-light b-3x">
          </div>
          <p class="text-white h4 m-t m-b">
            <?php echo $admin->name; ?><br />
            <small class="text-sm" timestamp="<?php echo (int)$last_login; ?>">Last login: <?php echo ( $last_login ) ? time_difference( $last_login ) : "never"; ?></small>
          </p>
          <div class="input-group">
            <input type="hidden" name="referer" value="<?php echo $referer; ?>" />
            <input type="email" name="email" class="form-control text-sm" placeholder="Enter email to continue" required><span class="input-group-btn"><button class="btn btn-success" type="submit" name="action" value="reset"><i class="fa fa-arrow-right"></i></button></span>
          </div>
          <a href="<?php echo admin_uri(); ?>login.php" class="pull-right m-t-xs"><small>Sign in</small></a>
        </div>
      </div>
    </div>
  </form>
  <div class="modal-backdrop in"></div>

  <!-- / footer -->
  <script src="<?php echo admin_uri(); ?>gzip.php?l=<?php echo urlencode( "js,jquery,app.v2" ); ?>&nv"></script>
  <script type="text/javascript">
    $(function() {
      if ( parseInt( $("small[timestamp]").attr("timestamp") ) ) {
        setInterval(function() {
          $("small[timestamp]").text( "Last login: "+timeDifference( $("small[timestamp]").attr("timestamp") ) );
        }, 1000);
      }
      function timeDifference(b,d,a,c){d=d||"d m Y";if(isNaN(parseInt(b)))return b;var e={s:{name:"second",sub:"s",time:30},m:{name:"minute",sub:"m",time:60},h:{name:"hour",sub:"h",time:3600},d:{name:"day",sub:"d",time:86400},w:{name:"week",sub:"w",time:604800},m2:{name:"month",sub:"mn",time:2592E3},y:{name:"year",sub:"y",time:31536E3}};if(b>time()||!0===a){a=b-time();var h="in ",p=""}else a=parseInt(time()-b),h="",p=" ago";var g="";if(60>a)switch(!0){default:g=c?"1s":"just now";break;case 20>a:g=c?a+"s":h+a+" seconds"+p;break;case 40>a:g=c?a+"s":h+"half a minute"+p;break;case 60>a:g=c?a+"s":h+"less than a minute"+p}else for(index in e)if(timei=e[index],a>=timei.time){var g=Math.round(a/timei.time),k=1==g?timei.name:timei.name+"s",t=timei.sub;"mn"===t&&c&&(t="d",g="~"+30*g);g=c?g+t:h+g+" "+k+p}return g?g:c?"":new Date( b * 1000 )};
    });
  </script>
</body>
</html>
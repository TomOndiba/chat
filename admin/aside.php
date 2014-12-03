<aside class="bg-dark lter aside-md hidden-print<?php echo ( $isSidebarHided ) ? " nav-xs" : ""; ?>" id="nav">
  <section class="vbox">
    <section class="w-f scrollable">
      <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
        <!-- nav -->
        <nav class="nav-primary hidden-xs">
          <ul class="nav">
            <li>
              <a href="<?php echo admin_uri(); ?>" class="active">
                <i class="fa fa-dashboard icon"><b class="bg-danger"></b></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li<?php echo ( in_array( $phpFile, array( "groups", "messages", "files" ) ) ) ? ' class="active"' : null; ?>>
              <a href="#chat">
                <i class="fa fa-comments"><b class="bg-warning"></b></i>
                <span class="pull-right">
                  <i class="fa fa-angle-down text"></i>
                  <i class="fa fa-angle-up text-active"></i>
                </span>
                <span>Chat</span>
              </a>
              <ul class="nav lt">
                <li<?php echo ( $phpFile === "groups" ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>groups.php">
                    <i class="fa fa-group"></i>
                    <span>Groups</span>
                  </a>
                </li>
                <li<?php echo ( $phpFile === "messages" ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>messages.php">
                    <i class="fa fa-inbox"></i>
                    <span>Messages</span>
                  </a>
                </li>
                <li<?php echo ( $phpFile === "files" ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>files.php">
                    <i class="fa fa-paperclip"></i>
                    <span>Attachments</span>
                  </a>
                </li>
              </ul>
            </li>
            <li<?php echo ( $phpFile === "plugins" ) ? ' class="active"' : null; ?>>
              <a href="#plugins">
                <i class="fa fa-flickr icon"><b class="bg-success"></b></i>
                <span class="pull-right">
                  <i class="fa fa-angle-down text"></i>
                  <i class="fa fa-angle-up text-active"></i>
                </span>
                <span>Plugins</span>
              </a>
              <ul class="nav lt">
                <li<?php echo ( $phpFile === "plugins" && ( !isset( $_GET["action"] ) || $_GET["action"] !== "install" ) ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>plugins.php">
                    <i class="fa fa-search"></i>
                    <span>View Plugins</span>
                  </a>
                </li>
                <li<?php echo ( $phpFile === "plugins" && ( isset( $_GET["action"] ) && $_GET["action"] === "install" ) ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>plugins.php?action=install">
                    <i class="fa fa-download"></i>
                    <span>Install Plugins</span>
                  </a>
                </li>
              </ul>
            </li>
            <li<?php echo ( $phpFile === "users" ) ? ' class="active"' : null; ?>>
              <a href="<?php echo admin_uri(); ?>users.php">
                <i class="fa fa-users icon">
                  <b class="bg-primary"></b>
                </i>
                <span>Users</span>
              </a>
            </li>
            <li<?php echo ( $phpFile === "notifications" ) ? ' class="active"' : null; ?>>
              <a href="<?php echo admin_uri(); ?>notifications.php">
                <i class="fa fa-exclamation-triangle icon">
                  <b class="bg-primary dker"></b>
                </i>
                <span>Notifications</span>
              </a>
            </li>
            <li<?php echo ( $phpFile === "languages" ) ? ' class="active"' : null; ?>>
              <a href="#languages">
                <i class="fa fa-leaf icon"><b class="bg-danger dker"></b></i>
                <span class="pull-right">
                  <i class="fa fa-angle-down text"></i>
                  <i class="fa fa-angle-up text-active"></i>
                </span>
                <span>Languages</span>
              </a>
              <ul class="nav lt">
                <li<?php echo ( $phpFile === "languages" && !isset( $_GET["install"] ) ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>languages.php">
                    <i class="fa fa-search"></i>
                    <span>View Languages</span>
                  </a>
                </li>
                <li<?php echo ( $phpFile === "languages" && isset( $_GET["install"] ) ) ? ' class="active"' : null; ?>>
                  <a href="<?php echo admin_uri(); ?>languages.php?install=1">
                    <i class="fa fa-download"></i>
                    <span>Install Languages</span>
                  </a>
                </li>
              </ul>
            </li>
            <li<?php echo ( $phpFile === "settings" ) ? ' class="active"' : null; ?>>
              <a href="<?php echo admin_uri(); ?>settings.php">
                <i class="fa fa-wrench icon">
                  <b class="bg-info"></b>
                </i>
                <span>Settings</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- / nav -->
      </div>
    </section>
    <footer class="footer lt hidden-xs b-t b-dark">
      <div id="invite" class="dropup">
        <section class="dropdown-menu on aside-md m-l-n">
          <section class="panel bg-white">
            <header class="panel-heading b-b b-light">
              Impact Plus
            </header>
            <div class="panel-body animated fadeInRight">
              <p class="fb-feed-loader text-center"><span class="loading-async"></span> <span class="v-middle">Loading&hellip;</span></p>
              <div class="fb-like-box" data-href="https://www.facebook.com/impact.plus.chat" data-width="200" data-colorscheme="light" data-show-faces="true" data-header="false" data-stream="false" data-show-border="false"></div>
            </div>
          </section>
        </section>
      </div>
      <a href="#nav" data-toggle="class:nav-xs" class="pull-right btn btn-sm btn-dark btn-icon<?php echo ( $isSidebarHided ) ? " active" : ""; ?>" data-cookie="sidebarHide">
        <i class="fa fa-angle-left text"></i>
        <i class="fa fa-angle-right text-active"></i>
      </a>
      <div class="btn-group hidden-nav-xs">
        <button type="button" title="Contacts" class="btn btn-icon btn-sm btn-dark" data-toggle="dropdown" data-target="#invite">
          <i class="fa fa-facebook"></i>
        </button>
      </div>
    </footer>
  </section>
</aside>
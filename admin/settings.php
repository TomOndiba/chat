<?php
  include( "header.php" );

  $blocked  = json_decode( ipgo( "blocked_files" ), true );
  $blocked  = ( is_array( $blocked ) ) ? $blocked : array();

  $domains  = json_decode( ipgo( "allowed_domains" ), true );
  $themes   = ipThemes::themes( dirname( dirname( __FILE__ ) )."/css/themes/" );

  $languages  = $ipLang->reading_languages();
?>
<script type="text/javascript">searchBox=false;</script>
<form action="<?php echo admin_uri(); ?>includes/pages/settings.php" method="POST">
  <?php echo doc_btn( "Settings" ); ?>
  <div class="alert settings-response"></div>

  <section class="panel panel-default">
    <header class="panel-heading bg-light clearfix">
      <ul class="nav nav-tabs pull-left">
        <li class="active"><a href="#setup-basic" data-toggle="tab">General</a></li>
        <li><a href="#setup-files" data-toggle="tab">Attachments</a></li>
        <li><a href="#notifications" data-toggle="tab">Notifications</a></li>
      </ul>
    </header>

    <section class="panel-body tb-settings tb-global">
			<div class="tab-content text-sm">

				<div class="tab-pane active" id="setup-basic">
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="allowed_domains">Allowed <a href="http://en.wikipedia.org/wiki/Domain_name" target="_blank" rel="nofollow">Domains</a></label>
            <div class="col-xs-10">
              <input type="text" name="allowed_domains" id="allowed_domains" class="form-control input-sm" value="<?php echo ( $domains ) ? implode( ", ", $domains ) : null; ?>">
              <span class="help-block">Enter domain names seperated by comma where <strong>ipChat</strong> is allowed to run. leave it blank to allow any domain names.</span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="active_theme">Default Theme</label>
            <div class="col-xs-10 m-b">
              <select name="active_theme" id="active_theme" class="form-control hang-selector">
                <?php
                  if ( $themes ) {
                    $default_theme  = ipgo( "active_theme" );
                    foreach( $themes as $theme ) {
                ?>
                <option value="<?php echo $theme["theme_idx"]; ?>"<?php echo ( $default_theme == $theme["theme_idx"] ) ? ' selected' : null; ?>><?php echo ( $theme["name"] ) ? $theme["name"] : $theme["theme_idx"]; ?><?php echo ( $theme["author"] ) ? " &mdash; ".$theme["author"] : null; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
              <span class="help-block">Default theme for chat. Note: it doesn't override users settings.</span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="language">Language</label>
            <div class="col-xs-10 m-b">
              <select name="language" id="language" class="form-control hang-selector">
                <?php
                  if ( $languages ) {
                    $default_language = ipgo( "language" );
                    foreach( $languages as $language_code => $language_name ) {
                ?>
                <option value="<?php echo $language_code; ?>"<?php echo ( $language_code == $default_language ) ? ' selected' : null; ?>><?php echo $language_name; ?></option>
                <?php
                    }
                  }
                ?>
              </select>
              <span class="help-block">Default language for chat. Note: it doesn't override users settings.</span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="home_uri">Home URL *</label>
            <div class="col-xs-10">
              <input type="text" name="home_uri" id="home_uri" class="form-control input-sm" value="<?php echo site_uri(); ?>">
              <span class="help-block">Your sites's full URL to root, where <strong>ipChat</strong> folder contains <strong class="text-danger">(Don't forget to add a trailing slash to the url)</strong></span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="enable_socket">Enable WebSocket</label>
            <div class="col-xs-10">
              <label class="switch">
                <input type="checkbox" name="enable_socket" id="enable_socket" value="1"<?php echo ( (int)ipgo( "enable_socket" ) === 1 ) ? " checked" : null; ?>>
                <span></span>
              </label>
              <span class="help-block">Enable/Disable the HTML5 WebSockets <a href="<?php echo mentor_uri() ?>docs/Sockets/">(know more)</a></span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="socket_host">WebSocket Address</label>
            <div class="col-xs-10">
              <input type="text" name="socket_host" id="socket_host" class="form-control input-sm" value="<?php echo ipgo( "socket_host" ); ?>">
              <span class="help-block">The address is an IP in dotted-quad notation (eg: <?php echo gethostbyname( parse_url( site_uri(), PHP_URL_HOST ) ); ?>)</span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="socket_port">WebSocket Port</label>
            <div class="col-xs-10">
              <input type="text" name="socket_port" id="socket_port" class="form-control input-sm" value="<?php echo ipgo( "socket_port" ); ?>">
              <span class="help-block">The port on which to listen for connections (eg: 8787)</span>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="api_key">API Key</label>
            <div class="col-xs-10">
              <input type="text" name="api_key" id="api_key" class="form-control input-sm" value="<?php echo ipgo( "api_key" ); ?>">
              <span class="help-block">For future Impact Plus updates, you must obtain an API key. To get an API key, follow <a href="<?php echo mentor_uri() ?>purchase/">these instructions</a>.</span>
            </div>
          </div>
				</div>

				<div class="tab-pane" id="setup-files">
          <?php
            $allow_mode = ipgo( "blocked_files_mode" );
          ?>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2">Mode</label>
            <div class="col-xs-10">
						  <div class="radio">
                <label class="radio-custom">
                  <input type="radio" name="allow_mode" value="blacklist"<?php echo ( !$allow_mode || $allow_mode === "blacklist" ) ? " checked" : null; ?>>
                  <i class="fa fa-circle-o"></i>
                  <span>Blacklist <small>(block selected)</small></span>
                </label>
              </div>
						  <div class="radio">
                <label class="radio-custom">
                  <input type="radio" name="allow_mode" value="whitelist"<?php echo ( $allow_mode === "whitelist" ) ? " checked" : null; ?>>
                  <i class="fa fa-circle-o"></i>
                  <span>Whitelist <small>(allow selected)</small></span>
                </label>
              </div>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="blocked_files_extn">Extensions</label>
            <div class="col-xs-10">
              <input type="text" name="blocked_files[extn]" id="blocked_files_extn" class="form-control" value="<?php echo ( isset( $blocked["extn"] ) ) ? implode( ",", $blocked["extn"] ) : null; ?>">
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-xs-2" for="blocked_files_mime">Mimetypes</label>
            <div class="col-xs-10">
              <input type="text" name="blocked_files[mime]" id="blocked_files_mime" class="form-control" value="<?php echo ( isset( $blocked["mime"] ) ) ? implode( ",", $blocked["mime"] ) : null; ?>">
            </div>
          </div>
				</div>

        <div class="tab-pane" id="notifications">
          <?php
            $layouts  = @unserialize( ipgo( "notification_layout" ) );
            $layouts  = ( $layouts && is_array( $layouts ) ) ? $layouts : array();
          ?>
          <div class="clearfix mls">
            <button class="btn btn-info notif-layout-add" type="button">
              <span class="fa fa-plus"></span>
              <span>Add Layout</span>
            </button>
          </div>

          <?php
            foreach( $layouts as $layout_id => $layout ) {
          ?>
          <div class="form-group clearfix notif-layout-group">
            <div class="col-xs-12">
              <div class="col-xs-3">
                <div class="input-group m-b">
                  <span class="input-group-btn">
                    <button class="btn btn-danger notif-layout-remove" type="button">
                      <span class="fa fa-times"></span>
                    </button>
                  </span>
                  <input type="text" name="notif[<?php echo htmlspecialchars( $layout_id ); ?>][id]" class="form-control" placeholder="Enter layout ID" value="<?php echo htmlspecialchars( $layout_id ); ?>" />
                </div>
              </div>
              <div class="col-xs-9">
                <div class="col-xs-12 mbs">
                  <input type="text" name="notif[<?php echo htmlspecialchars( $layout_id ); ?>][subject]" class="form-control" placeholder="Enter a Subject" value="<?php echo htmlspecialchars( $layout["subject"] ); ?>" />
                </div>
                <div class="col-xs-12">
                  <textarea name="notif[<?php echo htmlspecialchars( $layout_id ); ?>][message]" class="form-control" placeholder="Enter a Message"><?php echo htmlspecialchars( $layout["message"] ); ?></textarea>
                </div>
              </div>
            </div>
          </div>
          <?php
            }
          ?>

          <div class="form-group clearfix notif-layout-group">
            <div class="col-xs-12">
              <div class="col-xs-3">
                <div class="input-group m-b">
                  <span class="input-group-btn">
                    <button class="btn btn-danger notif-layout-remove" type="button">
                      <span class="fa fa-times"></span>
                    </button>
                  </span>
                  <input type="text" name="" class="form-control" placeholder="Enter layout ID" value="" />
                </div>
              </div>
              <div class="col-xs-9">
                <div class="col-xs-12 mbs">
                  <input type="text" name="" class="form-control" placeholder="Enter a Subject" value="" />
                </div>
                <div class="col-xs-12">
                  <textarea name="" class="form-control" placeholder="Enter a Message"></textarea>
                </div>
              </div>
            </div>
          </div>

        </div>

			</div>
    </section>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-xs-12">
          <button type="submit" name="action" value="update" class="btn btn-sm btn-default">
            <span class="fa fa-save"></span>
            <span>Update</span>
          </button>
          <button type="reset" name="action" value="reset" class="btn btn-sm btn-danger">
            <span class="fa fa-refresh"></span>
            <span>Reset</span>
          </button>
        </div>
      </div>
    </footer>
  </section>
</form>
<?php
  include( "footer.php" );
?>
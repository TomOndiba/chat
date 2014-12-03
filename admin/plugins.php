<?php
  include( "header.php" );

  $plugin = getGetArr( "plugin" );
  $format = getGetArr( "format", "js" );
  $action = getGetArr( "action", "list" );
  $folder = realpath( dirname( dirname( __FILE__ ) )."/plugins/" );

  if ( $action === "list" ) {
    $result = plugins_listing();
    $files  = $result[0];
    $pages  = render_pagination( $result[1] );
?>
<script type="text/javascript">var plugin_format=<?php echo json_encode( strtolower( $format ) ); ?>;</script>
<form action="<?php echo admin_uri(); ?>includes/pages/plugins.php" method="POST">
  <?php echo doc_btn( "Plugins" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
      <span>Plugins</span>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="plugins.php?action=create&format=<?php echo $format; ?>" class="btn btn-default" title="Create Plugin" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-plus"></span>
        </a>
        <a href="plugins.php?action=install" class="btn btn-info" title="Install Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-download"></span>
        </a>
      </div>
    </header>
    <div class="row text-sm wrapper">
      <div class="col-xs-6 m-b-xs">
        <select class="input-sm form-control input-s-sm inline" style="width: 61px;" title="Items per Page" data-toggle="tooltip" data-placement="top auto" data-href="<?php echo str_replace( "%25s%25", "%s%", change_url_index( "ipp", "%s%", "page" ) ); ?>">
          <?php
            foreach( range( 10, 100, 10 ) as $ipp ) {
          ?>
          <option value="<?php echo $ipp; ?>"<?php echo ( $result[5] == $ipp ) ? " selected" : null; ?>><?php echo $ipp; ?></option>
          <?php
            }
          ?>
        </select>
        <select class="input-sm form-control input-s-sm hidden-xs hidden-xs inline" name="action-option" style="width: 150px;">
          <option value="activate" selected>Activate selected</option>
          <option value="deactivate">De-activate selected</option>
          <option value="delete">Delete selected</option>
          <option value="clear">Clear all</option>
        </select>
        <button type="submit" name="action" value="apply" class="btn btn-sm btn-default hidden-xs inline">Apply</button>
      </div>
      <div class="col-xs-6 text-right">
        <div class="btn-group btn-group-sm">
          <a href="plugins.php?format=js" class="btn btn-default<?php echo ( $format === "js" ) ? " active" : ""; ?>" title="Javascript Plugins" data-toggle="tooltip" data-placement="top auto">
            <span>JS</span>
          </a>
          <a href="plugins.php?format=php" class="btn btn-default<?php echo ( $format === "php" ) ? " active" : ""; ?>" title="PHP Plugins" data-toggle="tooltip" data-placement="top auto">
            <span>PHP</span>
          </a>
        </div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table b-t b-light text-sm tb-plugins tb-global">
        <thead>
          <tr>
            <th width="20" class="hidden-xs"><input type="checkbox"></th>
            <th>Plugin</th>
            <th width="500" class="hidden-xs">Description</th>
            <th width="150" class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $files ) {
              global $ipPlugins;
              $active_plugins = $ipPlugins->activePlugins( $format );

              foreach( $files as $file ) {
                $extension  = strtolower( $file["extn"] );
                $activated  = ( in_array( $file["name"], $active_plugins ) );
          ?>
          <tr class="<?php echo ( $activated ) ? 'success' : ''; ?>">
            <td class="hidden-xs">
              <input type="checkbox" value="<?php echo $file["name"]; ?>" name="items[<?php echo $file["extn"]; ?>][]" />
            </td>
            <td>
              <div class="plugin plugin-name">
                <?php
                  echo ( isset( $file["data"]["Plugin URI"] ) ) ? '<a href="'.$file["data"]["Plugin URI"].'" title="Visit Plugin page" data-toggle="tooltip" data-placement="top auto" target="_blank" rel="nofollow">' : null;
                ?>
                <strong class="plugin-name">
                  <?php echo ( isset( $file["data"]["Plugin Name"] ) ) ? $file["data"]["Plugin Name"] : $file["name"]; ?>
                </strong>
                <?php
                  echo '</a>';
                ?>
              </div>
              <div class="plugin plugin-version">
                <small>
                  <span>Version:</span>
                  <span><?php echo ( isset( $file["data"]["Version"] ) ) ? $file["data"]["Version"] : "1.0" ?></span>
                </small>
              </div>
              <div class="plugin plugin-author">
                <small>
                  <span>by</span>
                  <?php
                    if ( isset( $file["data"]["Author URI"] ) ) {
                  ?>
                  <a href="<?php echo $file["data"]["Author URI"]; ?>" title="Visit Plugin author page" data-toggle="tooltip" data-placement="top auto" target="_blank" rel="nofollow">
                    <span><?php echo ( isset( $file["data"]["Author"] ) ) ? $file["data"]["Author"] : "unknown" ?></span>
                  </a>
                  <?php
                    }
                    else {
                  ?>
                  <span><?php echo ( isset( $file["data"]["Author"] ) ) ? $file["data"]["Author"] : "unknown" ?></span>
                  <?php
                    }
                  ?>
                </small>
              </div>
            </td>
            <td class="hidden-xs">
              <div class="plugin plugin-description">
                <?php echo ( isset( $file["data"]["Description"] ) ) ? $file["data"]["Description"] : "no description available" ?>
              </div>
            </td>
            <td class="clearfix text-center v-middle">
              <button type="button" class="btn btn-sm btn-default" name="edit" title="Edit Plugin" data-format="<?php echo $file["extn"]; ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-pencil"></span>
              </button>
              <button type="button" class="btn btn-sm<?php echo ( $activated ) ? ' btn-info active' : ' btn-default'; ?>" name="<?php echo ( $activated ) ? 'deactivate' : 'activate'; ?>" title="<?php echo ( $activated ) ? 'Deactivate' : 'Activate'; ?> Plugin" data-format="<?php echo $file["extn"]; ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-<?php echo ( $activated ) ? 'star' : 'star-o'; ?>"></span>
              </button>
              <button type="button" class="btn btn-sm btn-danger" name="delete" title="Remove Plugin" data-format="<?php echo $file["extn"]; ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-trash-o"></span>
              </button>
            </td>
          </tr>
          <?php
              }
            }
            else {
          ?>
          <td colspan="5">
            <div class="alert alert-danger">
              <p>It is seems like there is no plugins installed yet. Try changing the format.</p>
            </div>
          </td>
          <?php
            }
          ?>
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-4 text-left">
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s %s plugins", $result[2], $result[3], $result[4], strtolower( $format ) ); ?></small>
        </div>
        <div class="col-sm-8 text-right text-center-xs">
          <?php echo $pages; ?>
        </div>
      </div>
    </footer>

  </section>
</form>
<?php
  }
  elseif ( $action === "install" ) {
    $is_installation  = ( isset( $_POST["plugin-name"], $_POST["plugin-ID"], $_POST["plugin-size"], $_POST["plugin-version"] ) );
?>
<form action="<?php echo admin_uri(); ?>plugins.php?action=install" method="POST">
  <?php echo doc_btn( "Plugins#installing-plugins" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
      <span><?php echo ( $is_installation ) ? "Installing ".trim( htmlspecialchars( $_POST["plugin-name"] ) ) : "Plugins Gallery" ?></span>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="javascript:window.history.back();" class="btn btn-danger" title="Go back to previous page" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-arrow-left"></span>
        </a>
        <a href="plugins.php" class="btn btn-success" title="View Installed Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-search"></span>
        </a>
        <a href="plugins.php?action=create" class="btn btn-default" title="Create Plugin" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-plus"></span>
        </a>
        <a href="plugins.php?action=install" class="btn btn-info" title="Install Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-download"></span>
        </a>
      </div>
    </header>
    <?php
      if ( !$is_installation ) {
        loadClass( "ImpactPlus", "required/impact.plus.php" );
        $plugins  = ImpactPlus::list_plugins();

        //echo '<pre>'.htmlspecialchars( print_r( $plugins, true ) ).'</pre>';

        $items  = $plugins[0];
        $pages  = render_pagination( $plugins[1] );

        $filters  = array_intersect( array_keys( $filter_val = array( "author" => "Author", "search" => "Search", "format" => "Format" ) ), array_keys( $_GET ) );
        $filters  = ( empty( $filters ) ) ? false : $filters;
    ?>
    <div class="row text-sm wrapper">
      <div class="col-xs-<?php echo ( $filters ) ? 6 : 12; ?> m-b-xs">
        <select class="input-sm form-control input-s-sm inline" style="width: 61px;" title="Items per Page" data-toggle="tooltip" data-placement="top auto" data-href="<?php echo str_replace( "%25s%25", "%s%", change_url_index( "ipp", "%s%", "page" ) ); ?>">
          <?php
            foreach( range( 10, 100, 10 ) as $ipp ) {
          ?>
          <option value="<?php echo $ipp; ?>"<?php echo ( $plugins[5] == $ipp ) ? " selected" : null; ?>><?php echo $ipp; ?></option>
          <?php
            }
          ?>
        </select>
      </div>
      <?php
        if ( $filters ) {
      ?>
      <div class="col-xs-6 text-right">
        <div class="btn-group btn-group-sm">
          <?php
            foreach( $filters as $filter ) {
          ?>
          <a href="<?php echo change_url_index( null, null, $filter ); ?>" title="Remove filter &quot;<?php echo $filter_val[$filter]; ?>&quot;" data-toggle="tooltip" data-placement="top auto" class="btn btn-default"><?php echo $filter_val[$filter]; ?></a>
          <?php
            }
          ?>
        </div>
      </div>
      <?php
        }
      ?>
    </div>

    <div class="table-responsive">
      <table class="table b-t b-light text-sm tb-plugins tb-global">
        <thead>
          <tr>
            <?php
              $thead  = array(
                array( 'attr' => 'width="100" class="hidden-xs" class="text-center"', 'text' => 'Screenshot' ),
                array( 'text' => 'Plugin', 'func' => 'name', 'name' => 'Name' ),
                array( 'attr' => 'width="400" class="text-center hidden-xs"', 'text' => 'Description' ),
                array( 'attr' => 'width="180" class="text-center"', 'text' => 'Date', 'func' => 'date', 'name' => 'Date' ),
                array( 'attr' => 'width="120" class="text-center"', 'text' => 'Actions' )
              );
              echo renderTableHeader( $thead, $plugins[6], $plugins[7] );
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $items ) {
              global $ipPlugins;
              foreach( $items as $item ) {
                $f  = ( $item->format == "both" ) ? "php" : $item->format;
                $ipPlugins->setPluginFormat( $f );
                $d  = $ipPlugins->findPluginFile( $item->slug );
          ?>
          <tr class="<?php echo ( $d ) ? "success installed" : "not-installed"; ?>" tabindex="-1">
            <td class="text-center v-middle hidden-xs">
              <a href="<?php echo $item->screenshot; ?>" target="_blank" class="img-thumbnail" rel="prettyPhoto[ss]">
                <img src="<?php echo $item->screenshot; ?>" width="100%" />
              </a>
            </td>
            <td>
              <div class="plugin plugin-name">
                <?php echo ( $d ) ? '<strong class="fa fa-exclamation-circle text-primary" title="Plugin already Installed" data-toggle="tooltip" data-placement="top auto" style="display:inline-block;vertical-align:middle;font-size:15px;margin-right:2px;"></strong>' : ""; ?>
                <a href="<?php echo $item->plugin_uri; ?>" style="display:inline-block;vertical-align:middle;" target="_blank" title="View in Plugins Gallery" rel="nofollow" data-toggle="tooltip" data-placement="top auto">
                  <strong><?php echo $item->name; ?></strong>
                </a>
              </div>
              <div class="plugin plugin-version">
                <small>
                  <span>Version:</span>
                  <span><?php echo $item->version; ?></span>
                </small>
              </div>
              <div class="plugin plugin-author">
                <small>
                  <span>by</span>
                  <a href="<?php echo change_url_index( "author", $item->author->ID, array( "page" ) ) ?>" title="View all Plugins by <?php echo htmlspecialchars( $item->author->name ); ?>" data-toggle="tooltip" data-placement="top auto">
                    <span><?php echo $item->author->name; ?></span>
                  </a>
                </small>
              </div>
              <div class="plugin plugin-language">
                <small>
                  <span>in</span>
                  <a href="<?php echo change_url_index( "format", $item->format, array( "page" ) ) ?>" title="View Plugins in <?php echo ( ( $item->format ) !== "both" ) ? strtoupper( $item->format ) : "PHP and JS"; ?>" data-toggle="tooltip" data-placement="top auto">
                    <span><?php echo ( ( $item->format ) !== "both" ) ? strtoupper( $item->format ) : "PHP and JS"; ?></span>
                  </a>
                  &rarr;
                  <a href="<?php echo change_url_index( "category", $item->category->ID, array( "page" ) ) ?>" title="View Plugins in <?php echo $item->category->name; ?>" data-toggle="tooltip" data-placement="top auto">
                    <span><?php echo $item->category->name; ?></span>
                  </a>
                </small>
              </div>
            </td>
            <td class="hidden-xs">
              <div class="plugin-description"><?php echo $item->description; ?></div>
            </td>
            <td class="text-center v-middle">
              <small>
                <ul class="list-unstyled ul-horizontal">
                  <li>
                    <strong>Added:</strong>
                    <abbr title="<?php echo $item->added_date; ?>" data-toggle="tooltip" data-placement="top auto"><?php echo time_difference( strtotime( $item->added_date ), "Y-m-d H:i" ); ?></abbr>
                  </li>
                  <li>
                    <strong>Updated:</strong>
                    <abbr title="<?php echo $item->last_updated; ?>" data-toggle="tooltip" data-placement="top auto"><?php echo time_difference( strtotime( $item->last_updated ), "Y-m-d H:i" ); ?></abbr>
                  </li>
                </ul>
              </small>
            </td>
            <td class="clearfix text-center v-middle">
              <input type="hidden" name="plugin-name" value="<?php echo htmlspecialchars( $item->name ); ?>" disabled>
              <input type="hidden" name="plugin-ID" value="<?php echo htmlspecialchars( $item->ID ); ?>"  disabled>
              <input type="hidden" name="plugin-size" value="<?php echo htmlspecialchars( $item->size ); ?>"  disabled>
              <input type="hidden" name="plugin-version" value="<?php echo htmlspecialchars( $item->version ); ?>"  disabled>
              <button type="button" class="btn btn-sm btn-info" name="install-activate" title="Install &amp; Activate Plugin" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-cloud-download"></span>
              </button>
              <button type="button" class="btn btn-sm btn-default" name="install-only" title="Install Plugin" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa fa-download"></span>
              </button>
            </td>
          </tr>
          <?php
              }
            }
            else {
          ?>
          <tr>
            <td colspan="5">
              <div class="alert alert-danger">
                <p><?php echo ( $filters ) ? "There is no plugins matching for your filters" : "There is no plugins available yet" ?></p>
              </div>
            </td>
          </tr>
          <?php
            }
          ?>
        </tbody>
      </table>
    </div>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-4 text-left">
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s plugins", $plugins[2], $plugins[3], $plugins[4] ); ?></small>
        </div>
        <div class="col-sm-8 text-right text-center-xs"><?php echo $pages; ?></div>
      </div>
    </footer>
    <?php
      }
      else {
    ?>
    <section class="panel-body">
      <span class="tb-plugins sr-only"></span>
      <?php
          if ( isset( $_POST["plugin-activate"] ) ) {
      ?>
      <input type="hidden" name="plugin-activate" value="1" />
      <?php
          }
      ?>
      <input type="hidden" name="plugin-ID" value="<?php echo htmlspecialchars( $_POST["plugin-ID"] ); ?>" />
      <input type="hidden" name="plugin-name" value="<?php echo htmlspecialchars( $_POST["plugin-name"] ); ?>" />
      <input type="hidden" name="plugin-referer" value="<?php echo urlencode( $referer ); ?>" />
      <p class="transload-status text-sm">Please wait while we downloading &amp; installing "<strong><?php echo $_POST["plugin-name"]; ?></strong>"&hellip;</p>
    </section>
    <?php
      }
    ?>

  </section>
</form>
<?php
  }
  elseif ( $action === "create" ) {
?>
<script type="text/javascript">var codemirrorEditor=true,pluginFormat=<?php echo json_encode( $format ); ?>,lineFocus=10;</script>
<form action="<?php echo admin_uri(); ?>includes/pages/plugins.php" method="POST" class="form-horizontal">
  <?php echo doc_btn( "Plugins#creating-plugins" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
      <span>Create Plugin</span>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="<?php echo $referer; ?>" class="btn btn-danger" title="Go back to previous page" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-arrow-left"></span>
        </a>
        <a href="plugins.php" class="btn btn-success" title="View Installed Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-search"></span>
        </a>
        <a href="plugins.php?action=install" class="btn btn-info" title="Install Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-download"></span>
        </a>
      </div>
    </header>

    <section class="panel-body tb-plugins">
      <div class="hidden-xs">
        <?php
          if ( isset( $_SESSION["response-message"] ) ) {
        ?>
        <div class="alert alert-<?php echo ( $_SESSION["response-message"][1] ) ? "danger" : "success"; ?>">
          <p><?php echo $_SESSION["response-message"][0]; ?></p>
        </div>
        <?php
            unset( $_SESSION["response-message"] );
          }
          $plugin_name  = uniqid( "plugin-" );
        ?>
        <div class="form-group clearfix">
          <label class="control-label col-xs-2"><strong>Plugin name</strong></label>
          <div class="col-xs-10">
            <input type="text" name="plugin-name" id="plugin-name" class="form-control" onclick="return this.select();" value="<?php echo $plugin_name; ?>" />
          </div>
        </div>
        <div class="form-group clearfix">
          <label class="control-label col-xs-2"><strong>Plugin type</strong></label>
          <div class="col-xs-10">
            <select name="plugin-format" id="plugin-format" class="form-control">
              <option value="js"<?php echo ( $format == "js" ) ? " selected" : null; ?>>Javascript</option>
              <option value="php"<?php echo ( $format == "php" ) ? " selected" : null; ?>>PHP</option>
            </select>
          </div>
        </div>
        <hr />
        <div class="form-group fullscreen-window clearfix">
          <label for="plugin-content" class="clearfix control-label col-xs-12">
            <strong>Source</strong>
            <span class="codemirror-size"></span>
            <a class="go-fullscreen btn btn-xs btn-primary pull-right" href="#" title="Enter Fullscreen" data-toggle="tooltip" data-placement="top auto">
              <span class="fa fa-expand"></span>
              <span class="fa fa-compress sr-only"></span>
            </a>
            <strong class="pull-right codemirror-status"></strong>
          </label>
          <div class="col-xs-12 CodeMirrorHolder">
            <?php
              $content  = "/*\n  Plugin Name:  plugin name\n  Author:       author name\n  Author URI:   author url\n  Plugin URI:   plugin url\n  Description:  plugin description\n  Version:      1.0\n*/\n\n";
              $contentb = $content;
              $content  = ( $format == "php" ) ? "<?php \n\n".$content."\n\n?>" : $content;
              if ( isset( $_SESSION["temp-editor-content"] ) && trim( $_SESSION["temp-editor-content"] ) ) {
                $content  = htmlspecialchars( $_SESSION["temp-editor-content"] );
                unset( $_SESSION["temp-editor-content"] );
              }
            ?>
            <textarea spellcheck="false" autofocus="true" contextmenu="return false;" name="plugin-content" id="plugin-content" class="form-control" data-original-value="<?php echo htmlspecialchars( $contentb ); ?>" disabled><?php echo $content; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <button type="submit" name="action" value="create" class="btn btn-default">Create</button>
            <button type="reset" name="action" value="reset" class="btn btn-danger">Reset</button>
            <input type="hidden" name="referer" value="<?php echo urlencode( $referer ); ?>">
          </div>
        </div>
      </div>
      <div class="visible-xs">
        <div class="alert alert-danger">
          <p>Sorry you cannot create a Plugin in this viewport.</p>
        </div>
      </div>
    </section>
  </section>
</form>
<?php
  }
  elseif ( $action === "edit" ) {
    global $ipPlugins;
    $ipPlugins->setPluginFolder( $folder );
    $ipPlugins->setPluginFormat( $format );

    $subdir   = getGetArr( "subdir", null );
    $subfile  = getGetArr( "subfile", null );
    $filepath = $ipPlugins->findPluginFile( $plugin, $subdir, $subfile );
    $dirname  = $ipPlugins->isPluginInFolder( $plugin );
?>
<form action="<?php echo admin_uri(); ?>includes/pages/plugins.php" method="POST" class="form-horizontal">
  <?php echo doc_btn( "Plugins#editing-plugins" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
    <strong><?php echo $plugin; ?></strong>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="<?php echo $referer; ?>" class="btn btn-danger" title="Go back to previous page" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-arrow-left"></span>
        </a>
        <a href="plugins.php" class="btn btn-success" title="View Installed Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-search"></span>
        </a>
        <a href="plugins.php?action=create" class="btn btn-default" title="Create Plugin" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-plus"></span>
        </a>
        <a href="plugins.php?action=install" class="btn btn-info" title="Install Plugins" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-download"></span>
        </a>
      </div>
    </header>

    <section class="panel-body tb-plugins">
      <div class="hidden-xs">
        <?php
          if ( $filepath ) {
            $source   = implode( "", file( $filepath ) );
        ?>
        <div class="col-xs-12 clearfix">
          <?php
            if ( isset( $_SESSION["response-message"] ) ) {
          ?>
          <div class="alert alert-<?php echo ( $_SESSION["response-message"][1] ) ? "danger" : "success"; ?>">
            <p><?php echo $_SESSION["response-message"][0]; ?></p>
          </div>
          <?php
              unset( $_SESSION["response-message"] );
            }
          ?>
          <div class="col-xs-<?php echo ( $dirname ) ? 9 : 12; ?> col-area-edit">
            <script type="text/javascript">var codemirrorEditor=true,lineFocus=1,pluginFormat=<?php echo json_encode( strtolower( pathinfo( $filepath, PATHINFO_EXTENSION ) ) ); ?>;</script>
            <div class="form-group fullscreen-window clearfix">
              <label for="plugin-content" class="clearfix control-label col-xs-12">
                <strong>Source</strong>
                <span class="codemirror-size"></span>
                <a class="go-fullscreen btn btn-xs btn-primary pull-right" href="#" title="Enter Fullscreen" data-toggle="tooltip" data-placement="top auto">
                  <span class="fa fa-expand"></span>
                  <span class="fa fa-compress sr-only"></span>
                </a>
                <strong class="pull-right codemirror-status"></strong>
              </label>
              <div class="col-xs-12 CodeMirrorHolder">
                <textarea spellcheck="false" autofocus="true" contextmenu="return false;" name="plugin-content" id="plugin-content" class="form-control" disabled><?php echo htmlspecialchars( utf8_encode( $source ) ); ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-12">
                <button type="submit" name="action" value="update" class="btn btn-default">Update</button>
                <button type="reset" name="action" value="reset" class="btn btn-danger">Reset</button>
                <input type="hidden" name="plugin" value="<?php echo htmlspecialchars( $plugin ); ?>">
                <input type="hidden" name="format" value="<?php echo htmlspecialchars( $format ); ?>">
                <input type="hidden" name="filepath" value="<?php echo htmlspecialchars( $filepath ); ?>">
                <input type="hidden" name="referer" value="<?php echo urlencode( $referer ); ?>">
              </div>
            </div>
          </div>
          <?php
            if ( $dirname ) {
          ?>
          <div class="col-area-list col-xs-3">
            <h5 style="margin-top: 0;">
              <span style="display: block;">More file in plugin folder&hellip;</span>
              <small>Non-text files were omitted</small>
              <hr style="margin-top:7px;margin-bottom:12px;" />
            </h5>
            <ul class="list-unstyled file-system-tree">
              <?php
                echo list_folder_and_files( $dirname, $filepath, function( $item, $folder, $format, $plugin, $subdir, $subfile ) {
                  $filename   = pathinfo( $item->getFilename(), PATHINFO_FILENAME );
                  $directory  = str_replace( array( $folder.DIRECTORY_SEPARATOR.$plugin, DIRECTORY_SEPARATOR ), array( '', '/' ), $item->getPath() );
                  $edit_link  = change_url_index( array( "subdir" => $directory, "subfile" => $item->getFilename() ) );
                  if ( $filename == $plugin && $item->getPath() == $folder.DIRECTORY_SEPARATOR.$plugin ) {
                    $edit_link  = change_url_index( false, false, array( "subdir", "subfile" ) );
                  }
                  return $edit_link;
                }, array( $folder, $format, $plugin, $subdir, $subfile ) );
              ?>
            </ul>
          </div>
          <?php
            }
          ?>
        </div>
        <?php
          }
          else {
        ?>
        <div class="alert alert-danger">
          <p>The file/folder you were requested does not exists</p>
        </div>
        <?php
          }
        ?>
      </div>
      <div class="visible-xs">
        <div class="alert alert-danger">
          <p>Sorry you cannot edit a Plugin in this viewport.</p>
        </div>
      </div>
    </section>
  </section>
</form>
<?php
  }

  include( "footer.php" );
?>
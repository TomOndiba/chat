<?php
  include( "header.php" );
  if ( !isset( $_POST["installation_id"] ) ) {
    echo doc_btn( "Updates" );
?>

<div class="alert alert-danger">
  <p>Insalling an update will edit/replace/delete existing files or database. Backup your data first, before installing an update</p>
</div>

<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
  <section class="panel panel-default">
    <header class="panel-heading"><?php echo ( $has_updates ) ? '<span class="label label-danger">'.$has_updates.'</span> ' : null; ?>Updates</header>

    <table class="table b-t b-light text-sm tb-updates tb-global">
      <thead>
        <tr>
          <th>Changelog</th>
          <th width="150" class="text-center">Version</th>
          <th width="150" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $updates  = ImpactPlus::get_updates();
          if ( $updates ) {
            foreach( $updates as $update ) {
        ?>
        <tr>
          <td>
            <div><code><?php echo $update->info; ?></code></div>
          </td>
          <td class="text-center v-middle">
            <span class="badge badge-hollow"><?php echo $update->latest; ?></span>
          </td>
          <td class="text-center v-middle">
            <input type="hidden" name="installation_id" value="<?php echo $update->ID; ?>" disabled="disabled" />
            <button type="button" name="install" value="1" class="btn btn-primary btn-sm">
              <i class="fa fa-download"></i>
            </button>
          </td>
        </tr>
        <?php
            }
          }
          else {
          ?>
        <tr>
          <td colspan="3">
            <div class="alert alert-danger">
              <p>There is no updates available yet !</p>
            </div>
          </td>
        </tr>
        <?php
          }
        ?>
      </tbody>
    </table>
  </section>
</form>
<?php
  }
  else {
    $installation_id  = trim( $_POST["installation_id"] );
    $update   = ImpactPlus::do_update( $installation_id );
    $version  = ipgo( "version" );

    if ( $update ) {
      ipso( "version", $update->version );
?>
<div class="alert alert-success clearfix">
  <div>
    <h4>Update Success</h4>
    <p>Your version of <strong>Impact Plus</strong> successfully updated from version <code><?php echo $version ?></code> to <code><?php echo $update->version; ?></code></p>
  </div>
  <p></p>
  <p class="pull-right">&mdash; <a href="<?php echo mentor_uri(); ?>" target="_blank"><strong>Impact Plus</strong></a></p>
</div>
<pre style="white-space:normal;word-wrap:break-word;word-break:break-word;">
  <h4>Changelog:</h4>
  <p><?php echo $update->changelog; ?></p>
</pre>
<?php
      if ( file_exists( realpath( root_dir()."/ipChat/finish.php" ) ) ) {
?>
<div class="alert alert-info">
  <p>Please wait while we finish the update&hellip;</p>
</div>
<script type="text/javascript">window.location.href=<?php echo json_encode( site_uri()."ipChat/finish.php" ); ?>;</script>
<?php
      }
    }
    else {
?>
<div class="alert alert-danger clearfix">
  <div>
    <h4>Update Error</h4>
    <p>Update returned with an unknown error. Please try again after sometimes.</p>
    <p><?php echo ImpactPlus::get_error(); ?></p>
  </div>
  <p></p>
  <p class="pull-right">&mdash; <a href="<?php echo mentor_uri(); ?>" target="_blank"><strong>Impact Plus</strong></a></p>
</div>
<?php
    }
  }
  include( "footer.php" );
?>
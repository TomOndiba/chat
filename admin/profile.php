<?php
  include( "header.php" );
?>
  <div class="wrapper">
    <div class="clearfix m-b">
      <a class="pull-left thumb m-r _m">
        <img src="<?php echo $admin->avatar; ?>" class="img-circle">
        <span class="_o"></span>
        <form action="<?php echo admin_uri()."includes/pages/profile.php" ?>" method="POST" enctype="multipart/form-data" target="admin-avatar-target">
          <input type="file" class="_n" name="admin-avatar" id="admin-avatar" title="Click to change avatar" />
        </form>
        <iframe class="sr-only" name="admin-avatar-target"></iframe>
      </a>
      <div class="clear">
        <div class="h3 m-t-xs m-b-xs"><?php echo $admin->name; ?></div>
  			<small class="text-muted"><i class="fa fa-globe"></i> <?php echo ipgo( "home_uri" ); ?></small>
      </div>
    </div>

    <form action="#" method="POST" enctype="multipart/form-data" class="form-horizontal">
      <div class="tb-profile">
        <div class="control-group">
          <label class="control-label" for="admin-username">Username</label>
          <div class="form-control uneditable-input unselectable" id="admin-username"><?php echo $admin->username; ?></div>
        </div>
        <div class="control-group">
          <label class="control-label" for="admin-name">Name</label>
          <input type="text" class="form-control" id="admin-name" name="admin-name" value="<?php echo $admin->name; ?>" required>
        </div>
        <div class="control-group">
          <label class="control-label" for="admin-email">E-mail</label>
          <input type="email" class="form-control" id="admin-email" name="admin-email" value="<?php echo $admin->email; ?>" required>
        </div>
        <div class="control-group">
          <label class="control-label" for="admin-old-pass">Old Password</label>
          <input type="password" class="form-control" id="admin-old-pass" name="admin-old-pass" value="">
        </div>
        <div class="control-group">
          <label class="control-label" for="admin-new-pass">New Password</label>
          <input type="password" class="form-control" id="admin-new-pass" name="admin-new-pass" value="">
        </div>
        <div class="control-group">
          <label class="control-label" for="admin-retype-pass">Re-enter Password</label>
          <input type="password" class="form-control" id="admin-retype-pass" name="admin-retype-pass" value="">
        </div>
        <div class="control-group control-submit clearfix" style="margin-top: 20px;">
          <div class="pull-right">
            <button type="reset" class="btn btn-danger" name="action" valign="reset">
              <span class="fa fa-times"></span>
              <span>Reset</span>
            </button>
            <button type="submit" class="btn btn-info" name="action" valign="update">
              <span class="fa fa-save"></span>
              <span>Update Profile</span>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
<?php
  include( "footer.php" );
?>
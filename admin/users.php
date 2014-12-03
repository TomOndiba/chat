<?php
  include( "header.php" );
  $result   = users_listing();

  $users  = $result[0];
  $pages  = render_pagination( $result[1] );

  $filters  = array_intersect( array_keys( $filter_val = array( "search" => "Search" ) ), array_keys( $_GET ) );
  $filters  = ( empty( $filters ) ) ? false : $filters;
?>
<form action="<?php echo admin_uri(); ?>includes/pages/users.php" method="POST">
  <?php echo doc_btn( "Users" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">Users</header>
    <div class="row text-sm wrapper">
    	<div class="col-xs-<?php echo ( $filters ) ? 6 : 12; ?> m-b-xs">
        <select class="input-sm form-control input-s-sm inline" style="width: 61px;" title="Items per Page" data-toggle="tooltip" data-placement="top auto" data-href="<?php echo str_replace( "%25s%25", "%s%", change_url_index( "ipp", "%s%", "page" ) ); ?>">
          <?php
            foreach( range( 10, 100, 10 ) as $ipp ) {
          ?>
          <option value="<?php echo $ipp; ?>"<?php echo ( $result[5] == $ipp ) ? " selected" : null; ?>><?php echo $ipp; ?></option>
          <?php
            }
          ?>
        </select>
        <select class="input-sm form-control input-s-sm inline hidden-xs" name="action-option" style="width: 148px;">
          <option value="delete" selected>Remove selected</option>
          <option value="block">Block selected</option>
          <option value="unblock">Unblock selected</option>
          <option value="clear">Remove all</option>
        </select>
        <button type="button" name="action" value="apply" class="btn btn-sm btn-default hidden-xs inline">Apply</button>
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
      <table class="table b-t b-light text-sm tb-users tb-global">
        <thead>
          <tr>
            <?php
              $thead  = array(
                array( 'attr' => 'width="20" class="hidden-xs"', 'text' => '<input type="checkbox">' ),
                array( 'attr' => 'width="40"', 'text' => 'Picture', ),
                array( 'text' => 'Username', 'name' => 'Username', 'func' => 'user' ),
                array( 'attr' => 'class="hidden-xs"', 'text' => 'Name', 'func' => 'name', 'name' => 'Name' ),
                array( 'attr' => 'class="hidden-xs"', 'text' => 'Email', 'func' => 'email', 'name' => 'Email' ),
                array( 'attr' => 'width="100" class="text-center"', 'text' => 'Action' ),
              );
              echo renderTableHeader( $thead, $result[6], $result[7] );
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $users ) {
              $instance = new ipUsers( "foobar" );
              $blocked  = $instance->list_blocked( true );

              foreach( $users as $user ) {
                $user = $instance->process_user( $user );
                $class  = array();
                if ( $user->AV ) {
                  $class[]  = "has-avatar";
                }
                if ( in_array( $user->ID, $blocked ) ) {
                  $class[]  = "danger";
                }
                $class  = trim( implode( " ", $class ) );

                
          ?>
          <tr class="users-list <?php echo $class; ?>">
            <td class="clearfix text-center v-middle hidden-xs">
              <input type="checkbox" name="items[]" value="<?php echo $user->ID; ?>" />
            </td>
            <td class="clearfix text-left v-middle">
              <div class="avatar small">
                <img src="<?php echo $user->AV; ?>" />
              </div>
            </td>
            <td class="clearfix text-left v-middle">
              <div class="ellipsis ellipses-100"><strong><?php echo $user->UN; ?></strong></div>
            </td>
            <td class="clearfix text-left v-middle hidden-xs">
              <div class="ellipsis ellipses-100"><strong><?php echo $user->NM; ?></strong></div>
            </td>
            <td class="clearfix text-left v-middle hidden-xs">
              <div class="ellipsis ellipses-100">
                <a href="mailto:<?php echo urlencode( $user->EL ); ?>">
                  <strong><?php echo $user->EL; ?></strong>
                </a>
              </div>
            </td>
            <td class="clearfix text-center v-middle">
              <?php
                if ( in_array( $user->ID, $blocked ) ) {
              ?>
              <button type="button" class="btn btn-sm btn-success" name="unblock" title="Unblock user" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-circle-o"></span>
              </button>
              <?php
                }
                else {
              ?>
              <button type="button" class="btn btn-sm btn-info" name="block" title="Block user" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-circle"></span>
              </button>
              <?php
                }
              ?>
              <button type="button" class="btn btn-sm btn-danger" name="delete" title="Remove user" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-times"></span>
              </button>
            </td>
          </tr>
          <?php
              }
            }
            else {
          ?>
          <tr>
            <td colspan="6">
              <div class="alert alert-danger">
                <p><?php echo ( $filters ) ? "There is no users matching for your filters" : "There is no users available yet" ?></p>
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
        <div class="col-sm-8 text-left">
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s users", $result[2], $result[3], $result[4] ); ?></small>
        </div>
        <div class="col-sm-4 text-right text-center-xs">
          <?php echo $pages; ?>
        </div>
      </div>
    </footer>
  </section>
</form>
<?php
  include( "footer.php" );
?>
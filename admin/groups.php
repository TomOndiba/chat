<?php
  include( "header.php" );
  $result = groups_listing();

  $groups = $result[0];
  $pages  = render_pagination( $result[1] );

  $filters  = array_intersect( array_keys( $filter_val = array( "user" => "User", "search" => "Search" ) ), array_keys( $_GET ) );
  $filters  = ( empty( $filters ) ) ? false : $filters;
?>
<script type="text/javascript">searchBox=false;</script>
<form action="<?php echo admin_uri(); ?>includes/pages/groups.php" method="POST">
  <?php echo doc_btn( "Groups" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">Groups</header>
    <div class="row text-sm wrapper">
    	<div class="col-xs-12 m-b-xs">
        <select class="input-sm form-control input-s-sm inline" style="width: 61px;" title="Items per Page" data-toggle="tooltip" data-placement="top auto" data-href="<?php echo str_replace( "%25s%25", "%s%", change_url_index( "ipp", "%s%", "page" ) ); ?>">
          <?php
            foreach( range( 10, 100, 10 ) as $ipp ) {
          ?>
          <option value="<?php echo $ipp; ?>"<?php echo ( $result[5] == $ipp ) ? " selected" : null; ?>><?php echo $ipp; ?></option>
          <?php
            }
          ?>
        </select>
        <select class="input-sm form-control input-s-sm hidden-xs inline" name="action-option" style="width: 122px;">
          <option value="delete" selected>Delete selected</option>
          <option value="clear">Clear all</option>
        </select>
        <button type="submit" name="action" value="apply" class="btn btn-sm btn-default hidden-xs inline">Apply</button>
    	</div>
    </div>
  
    <div class="table-responsive">
      <table class="table b-t b-light text-sm tb-groups tb-global">
        <thead>
          <tr>
            <?php
              $thead  = array(
                array( 'attr' => 'width="20" class="hidden-xs"', 'text' => '<input type="checkbox">' ),
                array( 'text' => 'Name', 'func' => 'name', 'name' => 'Name' ),
                array( 'attr' => 'class="text-center hidden-xs"', 'text' => 'Owner', 'func' => 'created_by', 'name' => 'Owner' ),
                array( 'attr' => 'class="text-center"', 'text' => 'Users', 'func' => 'users', 'name' => 'Users' ),
                array( 'attr' => 'class="text-center"', 'text' => 'Messages', 'func' => 'messages', 'name' => 'Messages' ),
                array( 'attr' => 'class="text-center hidden-xs"', 'text' => 'Created', 'func' => 'created_on', 'name' => 'Created' ),
                array( 'attr' => 'width="150" class="text-center"', 'text' => 'Action' )
              );
              echo renderTableHeader( $thead, $result[6], $result[7] );
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $groups ) {
              foreach( $groups as $group ) {
          ?>
          <tr>
            <td class="clearfix text-center v-middle hidden-xs">
              <input type="checkbox" name="items[]" value="<?php echo $group->ID; ?>" />
            </td>
            <td class="v-middle">
              <div class="ellipsis ellipsis-200"><?php echo ( $group->name ) ? '<strong>'.$group->name.'</strong>' : '<code>not available</code>'; ?></div>
            </td>
            <td class="clearfix text-center v-middle hidden-xs">
              <div class="ellipsis">
                <?php
                  if ( $group->owner ) {
                ?>
                <a href="<?php echo change_url_index( "user", $group->owner->ID ) ?>" title="<?php echo $group->owner->NM; ?>" data-toggle="tooltip" data-placement="top auto"><?php echo get_lname( $group->owner->NM ); ?></a>
                <?php
                  }
                  else {
                    echo '<code>not available</code>';
                  }
                ?>
              </div>
            </td>
            <td class="clearfix text-center v-middle">
              <div class="ellipsis"><?php echo $group->users; ?></div>
            </td>
            <td class="clearfix text-center v-middle">
              <div class="ellipsis">
                <a href="<?php echo admin_uri()."messages.php?group=".$group->ID; ?>" rel="nofollow">
                  <?php echo $group->messages; ?>
                </a>
              </div>
            </td>
            <td class="clearfix text-center v-middle hidden-xs">
              <small class="meta date ellipsis" title="<?php echo date( "l, F jS, Y", $group->time ); ?>" data-toggle="tooltip" data-placement="top auto">
                <?php
                  echo time_difference( $group->time );
                ?>
              </small>
            </td>
            <td class="clearfix text-center v-middle">
              <button type="button" class="btn btn-sm btn-danger" name="delete" title="Remove Group" data-toggle="tooltip" data-placement="top auto">
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
            <td colspan="7">
              <div class="alert alert-danger">
                <p><?php echo ( $filters ) ? "There is no groups matching for your filters" : "There is no groups created yet" ?></p>
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
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s groups", $result[2], $result[3], $result[4] ); ?></small>
        </div>
        <div class="col-sm-8 text-right text-center-xs">
          <?php echo $pages; ?>
        </div>
      </div>
    </footer>
  </section>
</form>
<?php
  include( "footer.php" );
?>
<?php
  include( "header.php" );
  $result = attachment_listing();

  $files  = $result[0];
  $pages  = render_pagination( $result[1] );

  $filters  = array_intersect( array_keys( $filter_val = array( "user" => "User", "search" => "Search", "mime" => "Mimetype", "group" => "File group", "message" => "Message" ) ), array_keys( $_GET ) );
  $filters  = ( empty( $filters ) ) ? false : $filters;

  $allow_mode = ipgo( "blocked_files_mode" );
?>
<script type="text/javascript">var allow_mode=<?php echo json_encode( $allow_mode ); ?>;</script>
<form action="<?php echo admin_uri(); ?>includes/pages/files.php" method="POST">
  <?php echo doc_btn( "Attachments" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">Attachments</header>
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
        <?php
          $method1  = ucfirst( $allow_mode );
          $method2  = ( $allow_mode == "blacklist" ) ? "Whitelist" : "Blacklist";
        ?>
        <select class="input-sm form-control input-s-sm inline hidden-xs" name="action-option" style="width: 150px;">
          <optgroup label="<?php echo $method1; ?>">
            <option value="add-extn" selected><?php echo $method1; ?> Extension</option>
            <option value="add-mime"><?php echo $method1; ?> Mimetype</option>
          </optgroup>
          <optgroup label="<?php echo $method2; ?>">
            <option value="del-extn"><?php echo $method2; ?> Extension</option>
            <option value="del-mime"><?php echo $method2; ?> Mimetype</option>
          </optgroup>
          <option value="delete">Delete selected</option>
          <option value="clear">Clear all</option>
        </select>
        <button type="submit" name="action" value="apply" class="btn btn-sm btn-default hidden-xs inline">Apply</button>
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
      <table class="table b-t b-light text-sm tb-files tb-global">
        <thead>
          <tr>
            <?php
              $thead  = array(
                array( 'attr' => 'width="20" class="hidden-xs"', 'text' => '<input type="checkbox">' ),
                array( 'text' => 'File', 'func' => 'title', 'name' => 'Title' ),
                array( 'attr' =>  'class="hidden-xs hidden-sm"', 'text' => 'Size', 'func' => 'size', 'name' => 'Size' ),
                array( 'attr' => 'class="text-center"', 'text' => 'User', 'func' => 'userID', 'name' => 'User' ),
                array( 'attr' => 'class="text-center"', 'text' => 'Date', 'func' => 'upload_date', 'name' => 'Upload date' ),
                array( 'attr' => 'width="150" class="text-center"', 'text' => 'Action' )
              );
              echo renderTableHeader( $thead, $result[6], $result[7] );
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $files ) {
              foreach( $files as $file ) {
                $class  = array( "attachments-list", $file->classes->extn, $file->classes->mime, $file->block_list, ( ( $file->block_list == "blacklist" ) ? "danger" : "success" ), ( ( !$file->relationExists ) ? "no-rel warning" : null ), $file->block_params );
                $class  = implode( " ", array_filter( $class ) );
                $mime   = strtolower( trim( $file->mimetype ) );
                $hasThumb = trim( $file->thumbnail );
                $hasThumb = ( !empty( $hasThumb ) && stristr( $hasThumb, "no_img" ) === false );
          ?>
          <tr class="<?php echo $class; ?>" data-extension="<?php echo htmlspecialchars( $file->extension ); ?>" data-mimetype="<?php echo htmlspecialchars( $file->mimetype ); ?>">
            <td class="clearfix text-center v-middle hidden-xs">
              <input type="checkbox" name="items[]" value="<?php echo $file->ID; ?>" />
            </td>
            <td>
              <div class="img-thumbnail hidden-xs">
                <a href="<?php echo ( $file->mimegroup == "stream" && $hasThumb ) ? $file->thumbnail : $file->target; ?>" target="_blank" class="<?php echo ( $hasThumb ) ? "lss-img" : null; ?>" rel="<?php echo ( $hasThumb ) ? "prettyPhoto[ss]" : "nofollow"; ?>">
                  <img src="<?php echo site_uri()."ipChat/images/pixel.png"; ?>" data-src="<?php echo $file->thumbnail; ?>" width="50" height="50" />
                </a>
              </div>
              <div class="file-meta">
                <div class="meta name">
                  <a href="<?php echo $file->target; ?>" rel="nofollow" target="_blank" title="<?php echo htmlspecialchars( $file->title ); ?>" data-toggle="tooltip" data-placement="top auto">
                    <span class="m-ellipse ellipsis m-ellipse-xs"><?php echo ellipses( $file->title, 80 ); ?></span>
                  </a>
                  <?php echo ( !$file->relationExists ) ? '<span class="label label-danger" title="This files is no longer belongs to any messages" data-toggle="tooltip" data-placement="top auto">not in use</span>' : ''; ?>
                </div>
                <div class="meta mime">
                  <a href="<?php echo change_url_index( "group", $file->mimegroup, array( "page" ) ); ?>" rel="nofollow" title="Filter by &quot;<?php echo ucfirst( $file->mimegroup ); ?>&quot;" data-toggle="tooltip" data-placement="top auto">
                    <small><?php echo ucfirst( $file->mimegroup ); ?></small>
                  </a>
                  <?php
                    if ( $file->extension ) {
                  ?>
                  <a rel="nofollow" class="meta-extn">
                    <small><?php echo strtoupper( $file->extension ); ?></small>
                  </a>
                  <?php
                    }
                  ?>
                  <a href="<?php echo change_url_index( "mime", $mime, array( "page" ) ); ?>" rel="nofollow" title="Filter by &quot;<?php echo $file->mimetype; ?>&quot;" data-toggle="tooltip" data-placement="top auto" class="ellipses ellipses-200">
                    <small><?php echo $mime; ?></small>
                  </a>
                </div>
                <div class="meta size ellipsis ellipsis-250">
                  <small><?php echo ( $file->mimegroup == "stream" && $file->summary ) ? $file->summary : format_file_size( $file->size ); ?></small>
                </div>
              </div>
            </td>
            <td class="clearfix text-center v-middle hidden-xs hidden-sm">
              <small class="meta size ellipsis" title="<?php echo $file->size; ?> bytes" data-toggle="tooltip" data-placement="top auto">
                <?php
                  echo format_file_size( $file->size );
                ?>
              </small>
            </td>
            <td class="clearfix text-center v-middle">
              <?php
                if ( $file->user ) {
                  $preview  = ( $file->user->AV ) ? $file->user->AV : admin_uri()."img/no_img_50.png";
              ?>
              <div class="img-thumbnail hidden-xs hidden-sm">
                <img src="<?php echo site_uri()."ipChat/images/pixel.png"; ?>" data-src="<?php echo $preview; ?>" width="50" height="50" />
              </div>
              <div class="user-meta">
                <div class="meta name">
                  <a href="<?php echo admin_uri(); ?>files.php?user=<?php echo urlencode( $file->userID ); ?>" rel="nofollow" data-toggle="tooltip" data-placement="top auto" title="<?php echo $file->user->NM; ?>">
                    <span><?php echo get_lname( $file->user->NM ); ?></span>
                  </a>
                </div>
                <div class="meta status status-<?php echo ( ( $file->user->SA ) ? $file->user->SA : $file->user->ST ); ?>">
                  <small><?php echo ( ( $file->user->SA ) ? $file->user->SA : $file->user->ST ); ?></small>
                </div>
              </div>
              <?php
                }
              ?>
            </td>
            <td class="clearfix text-center v-middle">
              <small class="meta date ellipsis" title="<?php echo date( "l, F jS, Y", $file->upload_date ); ?>" data-toggle="tooltip" data-placement="top auto">
                <?php
                  echo time_difference( $file->upload_date );
                ?>
              </small>
            </td>
            <td class="clearfix text-center v-middle">
              <button type="button" class="btn btn-sm btn-danger" name="delete" title="Remove File" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-times"></span>
              </button>
              <?php
                if ( $file->extension ) {
                  if ( $allow_mode === "blacklist" ) {
              ?>
              <button type="button" class="btn btn-sm btn-default ublk-btn-inline<?php echo ( $file->block_list === "whitelist" ) ? " sr-only" : ""; ?>" name="block-del" title="Remove from <?php echo ucfirst( $allow_mode ); ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-minus"></span>
              </button>
              <button type="button" class="btn btn-sm btn-default blk-btn-inline<?php echo ( $file->block_list === "blacklist" ) ? " sr-only" : ""; ?>" name="block-add" title="Add to <?php echo ucfirst( $allow_mode ); ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-plus"></span>
              </button>
              <?php
                  }
                  else {
              ?>
              <button type="button" class="btn btn-sm btn-default ublk-btn-inline<?php echo ( $file->block_list === "blacklist" ) ? " sr-only" : ""; ?>" name="block-del" title="Remove from <?php echo ucfirst( $allow_mode ); ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-minus"></span>
              </button>
              <button type="button" class="btn btn-sm btn-default blk-btn-inline<?php echo ( $file->block_list === "whitelist" ) ? " sr-only" : ""; ?>" name="block-add" title="Add to <?php echo ucfirst( $allow_mode ); ?>" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-plus"></span>
              </button>
              <?php
                  }
                }
              ?>
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
                <p><?php echo ( $filters ) ? "There is no files matching for your filters" : "There is no files uploaded yet" ?></p>
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
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s files", $result[2], $result[3], $result[4] ); ?></small>
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
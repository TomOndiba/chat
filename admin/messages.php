<?php
  include( "header.php" );
  $result = messages_listing();

  $messages = $result[0];
  $pages    = render_pagination( $result[1] );

  $filters  = array_intersect( array_keys( $filter_val = array( "user" => "User", "search" => "Search", "group" => "Group", "sent_to" => "Sent to", "sent_by" => "Sent from" ) ), array_keys( $_GET ) );
  $filters  = ( empty( $filters ) ) ? false : $filters;
?>
<form action="<?php echo admin_uri(); ?>includes/pages/messages.php" method="POST">
  <?php echo doc_btn( "Messages" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">Messages</header>
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
        <select class="input-sm form-control input-s-sm hidden-xs inline" name="action-option" style="width: 122px;">
          <option value="delete" selected>Delete selected</option>
          <option value="clear">Clear all</option>
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
      <table class="table table-striped b-t b-light text-sm tb-messages tb-global">
        <thead>
          <tr>
            <?php
              $thead  = array(
                array( 'attr' => 'width="20" class="hidden-xs"', 'text' => '<input type="checkbox">' ),
                array( 'text' => 'Message', 'func' => 'message', 'name' => 'Message' ),
                array( 'attr' => 'width="30" class="text-center hidden-xs"', 'text' => 'Files' ),
                array( 'attr' => 'width="80" class="text-center"', 'text' => 'From', 'func' => 'sent_from', 'name' => 'From' ),
                array( 'attr' => 'width="70" class="text-center"', 'text' => 'To', 'func' => 'sent_to', 'name' => 'To' ),
                array( 'attr' => 'width="80" class="text-center hidden-xs"', 'text' => 'Date', 'func' => 'sent_date', 'name' => 'Sent date' ),
                array( 'attr' => 'width="20" class="text-center"', 'text' => 'Action' ),
              );
              echo renderTableHeader( $thead, $result[6], $result[7] );
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $messages ) {
              loadClass( "relation" );
              $relation = new ipRelation( "admin" );

              $rels   = array();
              $users  = array();
              foreach( $messages as $message ) {
                $group  = false;
                $is_group = false;
                if ( (int)$message->groupID !== 0 ) {
                  $is_group = $message->groupID;
                  $group  = $relation->getGroupInfo( $message->groupID  );
                }

                $related  = ( in_array( $message->relationID, $rels ) );
                $sanitized  = trim( strip_tags( $message->message ) );
                //$message->message = $sanitized;

                $class  = array( "mrel-".$message->relationID );
                $text   = null;
                if ( $message->is_sticker ) {
                  $class[]  = "tb-stcker";
                  $text = '<code class="inline-block"><span class="inline-block fa fa-github-alt"'.( ( !$related ) ? ' title="Emojis" data-toggle="tooltip" data-placement="top auto"' : null ).'></span> <span class="inline-block">Sticker</span></code>';
                }
                elseif ( $message->is_notice ) {
                  if ( $message->has_attachment ) {
                    $class[]  = "tb-attachment";
                    $text = '<code class="inline-block"><span class="inline-block fa fa-paperclip"'.( ( !$related ) ? ' title="Attachments" data-toggle="tooltip" data-placement="top auto"' : null ).'></span> <span class="inline-block">Attachments</span></code>';
                  }
                  else {
                    $class[]  = "tb-notice";
                    $text = '<code class="inline-block"><span class="inline-block fa fa-warning"'.( ( !$related ) ? ' title="Notification" data-toggle="tooltip" data-placement="top auto"' : null ).'></span> <span class="inline-block">Notification</span></code>';
                  }
                }
                elseif ( !$sanitized ) {
                  if ( $message->has_attachment ) {
                    $class[]  = "tb-attachment";
                    $text = '<code class="inline-block"><span class="inline-block fa fa-paperclip"'.( ( !$related ) ? ' title="Attachments" data-toggle="tooltip" data-placement="top auto"' : null ).'></span> <span class="inline-block">Attachments</span></code>';
                  }
                  else {
                    $class[]  = "tb-emoticon";
                    $text = '<code class="inline-block"><span class="inline-block fa fa-meh-o"'.( ( !$related ) ? ' title="Emoticons" data-toggle="tooltip" data-placement="top auto"' : null ).'></span> <span class="inline-block">Smiley</span></code>';
                  }
                }
                else {
                  $text = $message->message;
                }
                if ( $related ) {
                  $class[]  = "related-message";
                }
                $class  = implode( " ", $class );

                $sent_by  = ipUsers::get_users( "admin", $message->sent_from, null, null, null, true );
                $sent_to  = null;
                if ( $is_group ) {
                  $sent_to  = ( $group ) ? $group->name : null;
                }
                else {
                  if ( checkCache( json_encode( $message->targetID ), "users_by_ID" ) ) {
                    $sent_to  = getCache( json_encode( $message->targetID ), "users_by_ID" );
                  }
                  else {
                    $sent_to  = addCache( json_encode( $message->targetID ), ipUsers::get_users( "admin", $message->targetID, null, null, null, true ), "users_by_ID" );
                  }
                  $sent_to  = ( $sent_to && isset( $sent_to[$message->targetID] ) ) ? $sent_to[$message->targetID] : null;
                }
                //echo '<pre>'.print_r( $sent_to, true ).'</pre>';
                //continue;

                $sent_by  = ( $sent_by && !empty( $sent_by ) && isset( $sent_by[$message->sent_from] ) ) ? $sent_by[$message->sent_from] : false;
          ?>
          <tr class="messages-list <?php echo $class; ?>" data-rel="<?php echo $message->relationID; ?>">
            <td class="clearfix text-center v-middle hidden-xs">
              <input type="checkbox" name="items[]" value="<?php echo $message->ID; ?>" />
            </td>
            <td class="clearfix text-left v-middle">
              <div class="db-message-text<?php echo ( $related ) ? ' related-message' : null; ?>"><?php echo $text; ?></div>
            </td>
            <td class="clearfix text-center v-middle hidden-xs">
              <?php
                if ( $message->has_attachment && $message->attachments ) {
                  $alables  = array_map( function($v) {
                    return ellipses( trim( htmlspecialchars( $v->title ) ), 30 );
                  }, $message->attachments );
                  $alables  = array_filter( $alables );
                  $anumbers = count( $alables );
                  $alables  = array_splice( $alables, 0, 10 );
                  $alables  = ( $anumbers === count( $alables ) ) ? implode( ", ", $alables ) : implode( ", ", $alables )." and ".( $anumbers - count( $alables ) )." more";
              ?>
              <a href="<?php echo admin_uri(); ?>files.php?message=<?php echo urlencode( $message->relationID ); ?>"<?php echo ( !$related ) ? ' title="'.$alables.'" data-toggle="tooltip" data-placement="top auto"' : ''; ?>>
                <strong><?php echo ( $message->has_attachment ) ? count( $message->attachments ) : '0'; ?></strong>
              </a>
              <?php
                }
                else {
              ?>
              <strong>0</strong>
              <?php
                }
              ?>
            </td>
            <td class="clearfix text-center v-middle">
              <?php
                if ( $sent_by ) {
              ?>
              <a href="<?php echo change_url_index( "sent_by", $sent_by->ID, array( "page" ) ); ?>" class="ellipsis ellipsis-150" title="<?php echo $sent_by->NM; ?>" data-toggle="tooltip" data-placement="top auto">
                <strong><?php echo get_lname( $sent_by->NM ); ?></strong>
              </a>
              <?php
                }
                else {
              ?>
              <kbd class="ellipsis">undefined</kbd>
              <?php
                }
              ?>
            </td>
            <td class="clearfix text-center v-middle">
              <?php
                if ( $sent_to ) {
              ?>
              <a href="<?php echo ( is_string( $sent_to ) ) ? admin_uri().'messages.php?group='.$message->groupID : change_url_index( "sent_to", $sent_to->ID, array( "page" ) ); ?>" class="ellipsis ellipsis-150" title="<?php echo ( is_string( $sent_to ) ) ? "Group '".$sent_to."'" : $sent_to->NM; ?>" data-toggle="tooltip" data-placement="top auto">
                <strong>
                  <?php echo ( is_string( $sent_to ) ) ? '<span class="fa fa-td fa-comments-o"></span> '.ellipses( $sent_to, 20 ) : get_lname( $sent_to->NM ); ?>
                </strong>
              </a>
              <?php
                }
                else {
              ?>
              <kbd class="ellipsis">undefined</kbd>
              <?php
                }
              ?>
            </td>
            <td class="clearfix text-center v-middle hidden-xs">
              <small class="meta ellipsis date"<?php echo ( !$related ) ? ' title="'.date( "l, F jS, Y", $message->sent_date ).'" data-toggle="tooltip" data-placement="top auto"' : null; ?>>
                <?php
                  echo time_difference( $message->sent_date, "j<\s\u\p>S</\s\u\p> M, Y" );
                ?>
              </small>
            </td>
            <td class="clearfix text-center v-middle">
              <button type="button" class="btn btn-sm btn-danger" title="Delete message" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-times"></span>
              </button>
            </td>
          </tr>
          <?php
                if ( !$related ) {
                  $rels[] = $message->relationID;
                }
              }
              $rels = null;
            }
            else {
          ?>
          <tr>
            <td colspan="7">
              <div class="alert alert-danger">
                <p><?php echo ( $filters ) ? "There is no messages matching for your filters" : "There is no messages sent yet" ?></p>
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
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s messages", $result[2], $result[3], $result[4] ); ?></small>
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
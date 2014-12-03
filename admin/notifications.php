<?php
  include( "header.php" );
  $action = getGetArr( "action", "list" );

  if ( $action === "list" ) {
    $result = notifications_listing();
  
    $notifs = $result[0];
    $pages  = render_pagination( $result[1] );
  
    $filters  = array_intersect( array_keys( $filter_val = array( "sender" => "Sender", "search" => "Search", "reciever" => "Reciever", "priority" => "Priority" ) ), array_keys( $_GET ) );
    $filters  = ( empty( $filters ) ) ? false : $filters;
?>
<form action="<?php echo admin_uri(); ?>includes/pages/notifs.php" method="POST">
  <?php echo doc_btn( "Notifications" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
      <span>Notifications</span>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="<?php echo admin_uri(); ?>notifications.php?action=create" class="btn btn-default" title="Add Notification" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-plus"></span>
        </a>
        <a href="<?php echo admin_uri(); ?>settings.php#notifications" class="btn btn-default" title="Change Settings" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-gear"></span>
        </a>
      </div>
    </header>
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
        <select class="input-sm form-control input-s-sm inline hidden-xs" name="action-option" style="width: 130px;">
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
      <table class="table b-t b-light text-sm tb-notifs tb-global">
        <thead>
          <tr>
            <?php
              $thead  = array(
                array( 'attr' => 'width="20" class="hidden-xs"', 'text' => '<input type="checkbox">' ),
                array( 'attr' => 'width="200"', 'text' => 'Subject', 'func' => 'subject', 'name' => 'Subject' ),
                array( 'attr' => 'class="hidden-xs"', 'text' => 'Content', 'func' => 'content', 'name' => 'Content' ),
                array( 'attr' => 'width="100" class="text-center"', 'text' => 'Sender', 'func' => 'sender', 'name' => 'Sender' ),
                array( 'attr' => 'width="100" class="text-center"', 'text' => 'Reciever', 'func' => 'reciever', 'name' => 'Reciever' ),
                array( 'attr' => 'width="120" class="text-center hidden-xs"', 'text' => 'Date', 'func' => 'datetime', 'name' => 'Date' ),
                array( 'attr' => 'width="150" class="text-center"', 'text' => 'Action' )
              );
              echo renderTableHeader( $thead, $result[6], $result[7] );
            ?>
          </tr>
        </thead>
        <tbody>
          <?php
            if ( $notifs ) {
              foreach( $notifs as $notif ) {
                $sender   = ( (int)$notif->sender !== 0 ) ? ipUsers::get_users( "admin", $notif->sender, null, null, null, true, false ) : false;
                $reciever = ( (int)$notif->reciever !== 0 ) ? ipUsers::get_users( "admin", $notif->reciever, null, null, null, true, false ) : false;
                $content  = $_b = preg_replace_callback( "/(@)\[\[(\d+):([\w\s@\.,-\/#!$%\^&\*;:{}=\-_`~()]+)\]\]/i", function( $a ) {
                  return '<strong class="mention user-mention" title="'.htmlspecialchars( $a[3] ).'">'.$a[3].'</strong>';
                }, htmlspecialchars( $notif->content ) );
          ?>
          <tr class="<?php echo ( (int)$notif->priority === 1 ) ? "warning" : null; ?>">
            <td class="clearfix text-center v-middle hidden-xs">
              <input type="checkbox" name="items[]" value="<?php echo $notif->ID; ?>" />
            </td>
            <td class="v-middle">
              <div class="notif-subject">
                <a href="<?php echo change_url_index( "priority", $notif->priority, "page"); ?>" class="fa btn btn-xs inline-block fa-<?php echo ( (int)$notif->priority === 1 ) ? 'exclamation-circle btn-danger' : 'globe btn-success' ?>" title="<?php echo ( (int)$notif->priority === 1 ) ? 'High Priority' : 'Low Priority' ?>" data-toggle="tooltip" data-placement="top auto"></a>
                <strong class="inline-block ellipsis ellipsis-100" title="<?php echo htmlspecialchars( $notif->subject ); ?>" data-toggle="tooltip" data-placement="top auto"><?php echo htmlspecialchars( $notif->subject ); ?></strong>
              </div>
            </td>
            <td class="v-middle hidden-xs">
              <summary class="marquee"><?php echo $content; ?></summary>
            </td>
            <td class="text-center v-middle">
              <strong class="ellipses"><?php echo ( $sender ) ? '<a href="'.change_url_index( "sender", $sender->ID, "page" ).'" title="'.$sender->NM.'" data-toggle="tooltip" data-placement="top auto">'.get_lname( $sender->NM ).'</a>' : '<code>Global</code>'; ?></strong>
            </td>
            <td class="text-center v-middle">
              <strong class="ellipses"><?php echo ( $reciever ) ? '<a href="'.change_url_index( "reciever", $reciever->ID, "page" ).'" title="'.$reciever->NM.'" data-toggle="tooltip" data-placement="top auto">'.get_lname( $reciever->NM ).'</a>' : '<code>Global</code>'; ?></strong>
            </td>
            <td class="text-center v-middle hidden-xs">
              <abbr class="ellipses" data-toggle="tooltip" data-placement="top auto" title="<?php echo date( "r", $notif->datetime ); ?>"><?php echo time_difference( $notif->datetime, "j<\s\u\p>S</\s\u\p> M, Y" ); ?></abbr>
            </td>
            <td class="clearfix text-center v-middle">
              <button type="button" class="btn btn-sm btn-default" name="edit" title="Edit Notification" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-pencil"></span>
              </button>
              <button type="button" class="btn btn-sm btn-danger" name="delete" title="Remove Notification" data-toggle="tooltip" data-placement="top auto">
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
                <p><?php echo ( $filters ) ? "There is no notifications matching for your filters" : "There is no notifications added yet" ?></p>
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
          <small class="text-muted inline m-t-sm m-b-sm"><?php echo sprintf( "showing %s-%s of %s notifications", $result[2], $result[3], $result[4] ); ?></small>
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
  elseif ( $action == "edit" ) {
    if ( isset( $_POST["id"], $_POST["sender"], $_POST["reciever"], $_POST["subject"], $_POST["content"], $_POST["datetime"], $_POST["expire"] ) ) {
      $id       = $ipdb->escape( trim( $_POST["id"] ) );
      $sender   = $ipdb->escape( trim( $_POST["sender"] ) );
      $reciever = $ipdb->escape( trim( $_POST["reciever"] ) );
      $subject  = $ipdb->escape( trim( $_POST["subject"] ) );
      $content  = $ipdb->escape( trim( $_POST["content"] ) );
      $datetime = $ipdb->escape( trim( $_POST["datetime"] ) );
      $expire   = $ipdb->escape( trim( $_POST["expire"] ) );
      $priority = ( isset( $_POST["priority"] ) && (int)$_POST["priority"] === 1 ) ? 1 : 0;

      $sender   = ( empty( $sender ) || (int)$sender === 0 ) ? 0 : $sender;
      $reciever = ( empty( $reciever ) || (int)$reciever === 0 ) ? 0 : $reciever;
      $datetime = ( date( "d-m-Y H:i:s", strtotime( $datetime ) ) == $datetime ) ? strtotime( $datetime ) : date( "d-m-Y H:i:s" );
      $expire   = ( date( "d-m-Y H:i:s", strtotime( $expire ) ) == $expire ) ? "'".date( "Y-m-d H:i:s", strtotime( $expire ) )."'" : "NULL";

      if ( $content && $subject && $ipdb->query( "UPDATE `$ipdb->notif` SET `subject` = '{$subject}', `sender` = '{$sender}', `reciever` = '{$reciever}', `priority` = '{$priority}', `datetime` = '{$datetime}', `content` = '{$content}', `expire` = $expire WHERE `ID` = '{$id}'" ) ) {
        $ipdb->query( "DELETE FROM `$ipdb->relation` WHERE `targetID` = '{$id}' AND `structure` = 'notifReaded'" );
        $_SESSION["response-message"] = array( "Notification successfully updated", false );
      }
      else {
        $_SESSION["response-message"] = array( "Error while updating notification", true );
      }
    }

    $id     = $ipdb->escape( getGetArr( "id", false, true, true ) );
    $notif  = $ipdb->get_row( "SELECT * FROM `$ipdb->notif` WHERE `ID` = '{$id}'" );

    if ( $notif ) {
?>
<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
  <?php echo doc_btn( "Notifications" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
      <span>Edit &rarr; <kbd><?php echo $notif->subject; ?></kbd></span>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="<?php echo admin_uri(); ?>notifications.php?action=create" class="btn btn-default" title="Add Notification" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-plus"></span>
        </a>
        <a href="<?php echo admin_uri(); ?>notifications.php" class="btn btn-default" title="View Notifications" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-bell"></span>
        </a>
        <a href="<?php echo admin_uri(); ?>settings.php#notifications" class="btn btn-default" title="Change Settings" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-gear"></span>
        </a>
      </div>
    </header>

    <section class="panel-body tb-global notif-controller">
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
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-sender"><strong>From</strong></label>
        <div class="col-xs-10">
          <input type="text" name="sender" id="n-sender" class="hang-selector" value="<?php echo ( (int)$notif->sender === 0 ) ? null : htmlspecialchars( $notif->sender ); ?>" style="min-width: 250px;">
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-reciever"><strong>To</strong></label>
        <div class="col-xs-10">
          <input type="text" name="reciever" id="n-reciever" class="hang-selector" value="<?php echo ( (int)$notif->reciever === 0 ) ? null : htmlspecialchars( $notif->reciever ); ?>" style="min-width: 250px;">
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-subject"><strong>Subject</strong></label>
        <div class="col-xs-10">
          <input type="text" name="subject" id="n-subject" class="form-control" onclick="return this.select();" value="<?php echo htmlspecialchars( $notif->subject ); ?>" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-content"><strong>Content</strong></label>
        <div class="col-xs-10">
          <textarea name="content" id="n-content" class="form-control tagged_text"><?php echo htmlspecialchars( $notif->content ); ?></textarea>
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-priority"><strong>Important?</strong></label>
        <div class="col-xs-10">
          <label class="switch"><input type="checkbox" value="1" name="priority" id="n-priority"<?php echo ( (int)$notif->priority === 1 ) ? " checked" : null; ?>><span></span></label>
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-datetime"><strong>Date &amp; Time</strong></label>
        <div class="col-xs-10">
          <input type="text" name="datetime" id="n-datetime" class="combodate form-control" data-format="DD-MM-YYYY HH:mm:ss" data-template="DD MMM YYYY - HH : mm : ss" value="<?php echo ( (int)$notif->datetime !== 0 ) ? date( "d-m-Y H:i:s", $notif->datetime ) : date( "d-m-Y H:i:s" ); ?>">
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-expire"><strong>Expire on</strong></label>
        <div class="col-xs-10">
          <input type="text" name="expire" id="n-expire" class="combodate form-control" data-format="DD-MM-YYYY HH:mm:ss" data-template="DD MMM YYYY - HH : mm : ss" value="<?php echo ( !is_null( $notif->expire ) ) ? date( "d-m-Y H:i:s", strtotime( $notif->expire ) ) : null; ?>">
        </div>
      </div>
    </section>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-12 text-right">
          <input type="hidden" name="id" value="<?php echo $id; ?>" />
          <button type="submit" class="btn btn-success" name="action" value="update">
            <span>Update notification</span>
          </button>
        </div>
      </div>
    </footer>
  </section>
</form>
<?php
    }
    else {
?>
<div class="alert alert-danger">
  <p>The requested notification is not available this time | <a href="<?php echo ( isset( $_SERVER["HTTP_REFERER"] ) ) ? $_SERVER["HTTP_REFERER"] : admin_uri()."notifications.php";?>"><kbd>Go back</kbd></a></p>
</div>
<?php
    }
  }
  elseif ( $action === "create" ) {
    if ( isset( $_POST["sender"], $_POST["reciever"], $_POST["subject"], $_POST["content"], $_POST["datetime"], $_POST["expire"] ) ) {
      $sender   = $ipdb->escape( trim( $_POST["sender"] ) );
      $reciever = $ipdb->escape( trim( $_POST["reciever"] ) );
      $subject  = $ipdb->escape( trim( $_POST["subject"] ) );
      $content  = $ipdb->escape( trim( $_POST["content"] ) );
      $datetime = $ipdb->escape( trim( $_POST["datetime"] ) );
      $expire   = $ipdb->escape( trim( $_POST["expire"] ) );
      $priority = ( isset( $_POST["priority"] ) && (int)$_POST["priority"] === 1 ) ? 1 : 0;

      $sender   = ( empty( $sender ) || (int)$sender === 0 ) ? 0 : $sender;
      $reciever = ( empty( $reciever ) || (int)$reciever === 0 ) ? 0 : $reciever;
      $datetime = ( date( "d-m-Y H:i:s", strtotime( $datetime ) ) == $datetime ) ? strtotime( $datetime ) : date( "d-m-Y H:i:s" );
      $expire   = ( date( "d-m-Y H:i:s", strtotime( $expire ) ) == $expire ) ? "'".date( "Y-m-d H:i:s", strtotime( $expire ) )."'" : "NULL";

      if ( $content && $subject && $ipdb->query( "INSERT INTO `$ipdb->notif`(`subject`, `sender`, `reciever`, `priority`, `datetime`, `content`, `expire`) VALUES ('{$subject}','{$sender}','{$reciever}','{$priority}','{$datetime}','{$content}',{$expire})" ) ) {
        $_SESSION["response-message"] = array( "Notification \"<a href=\"".admin_uri()."notifications.php?action=edit&id=".$ipdb->insert_id."\" title=\"Click to Edit\">".htmlspecialchars( $subject )."</a>\" successfully created", false );
      }
      else {
        $_SESSION["response-message"] = array( "Error while creating notification", true );
      }
    }
?>
<form action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="POST">
  <?php echo doc_btn( "Notifications" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">
      <span>Create</span>
      <div class="btn-group btn-group-sm pull-right" style="margin-top: -6px;">
        <a href="<?php echo admin_uri(); ?>notifications.php" class="btn btn-default" title="View Notifications" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-bell"></span>
        </a>
        <a href="<?php echo admin_uri(); ?>settings.php#notifications" class="btn btn-default" title="Change Settings" data-toggle="tooltip" data-placement="top auto">
          <span class="fa fa-gear"></span>
        </a>
      </div>
    </header>

    <section class="panel-body tb-global notif-controller">
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
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-sender"><strong>From</strong></label>
        <div class="col-xs-10">
          <input type="text" name="sender" id="n-sender" class="hang-selector" value="" style="min-width: 250px;">
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-reciever"><strong>To</strong></label>
        <div class="col-xs-10">
          <input type="text" name="reciever" id="n-reciever" class="hang-selector" value="" style="min-width: 250px;">
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-subject"><strong>Subject</strong></label>
        <div class="col-xs-10">
          <input type="text" name="subject" id="n-subject" class="form-control" onclick="return this.select();" value="" />
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-content"><strong>Content</strong></label>
        <div class="col-xs-10">
          <textarea name="content" id="n-content" class="form-control tagged_text"></textarea>
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-priority"><strong>Important?</strong></label>
        <div class="col-xs-10">
          <label class="switch"><input type="checkbox" value="1" name="priority" id="n-priority"><span></span></label>
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-datetime"><strong>Date &amp; Time</strong></label>
        <div class="col-xs-10">
          <input type="text" name="datetime" id="n-datetime" class="combodate form-control" data-format="DD-MM-YYYY HH:mm:ss" data-template="DD MMM YYYY - HH : mm : ss" value="<?php echo date( "d-m-Y H:i:s" ); ?>">
        </div>
      </div>
      <div class="form-group clearfix">
        <label class="control-label col-xs-2" for="n-expire"><strong>Expire on</strong></label>
        <div class="col-xs-10">
          <input type="text" name="expire" id="n-expire" class="combodate form-control" data-format="DD-MM-YYYY HH:mm:ss" data-template="DD MMM YYYY - HH : mm : ss" value="">
        </div>
      </div>
    </section>

    <footer class="panel-footer">
      <div class="row">
        <div class="col-sm-12 text-right">
          <button type="submit" class="btn btn-success" name="action" value="create">
            <span>Create notification</span>
          </button>
        </div>
      </div>
    </footer>
  </section>
</form>
<?php
  }
  include( "footer.php" );
?>
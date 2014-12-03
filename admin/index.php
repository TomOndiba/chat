<?php
  include( "header.php" );
?>
  <script type="text/javascript">searchBox=false;</script>
  <div class="m-b-md">
    <h3 class="m-b-none">Dashboard</h3>
    <small>Welcome back, <?php echo ( $admin->name ) ? $admin->name : $admin->username; ?></small>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <section class="panel panel-default">
        <div class="row m-l-none m-r-none bg-light lter">
        	<div class="col-sm-6 col-md-3 padder-v b-r b-light">
            <span class="fa-stack fa-2x pull-left m-r-sm">
              <i class="fa fa-circle fa-stack-2x text-info"></i>
              <i class="fa fa-comment fa-stack-1x text-white"></i>
            </span>
            <a class="clear" href="<?php echo admin_uri()."messages.php"; ?>">
              <span class="h3 block m-t-xs"><strong><?php echo ChatStatistics::messages(); ?></strong></span>
              <small class="text-muted text-uc">Messages</small>
            </a>
          </div>
        	<div class="col-sm-6 col-md-3 padder-v b-r b-light lt">
            <span class="fa-stack fa-2x pull-left m-r-sm">
              <i class="fa fa-circle fa-stack-2x text-warning"></i>
              <i class="fa fa-comments fa-stack-1x text-white"></i>
            </span>
            <a class="clear" href="<?php echo admin_uri()."groups.php"; ?>">
              <span class="h3 block m-t-xs"><strong><?php echo ChatStatistics::groups(); ?></strong></span>
              <small class="text-muted text-uc">Groups</small>
            </a>
        	</div>
        	<div class="col-sm-6 col-md-3 padder-v b-r b-light">
            <span class="fa-stack fa-2x pull-left m-r-sm">
              <i class="fa fa-circle fa-stack-2x text-danger"></i>
              <i class="fa fa-files-o fa-stack-1x text-white"></i>
            </span>
            <a class="clear" href="<?php echo admin_uri()."files.php"; ?>">
              <span class="h3 block m-t-xs"><strong><?php echo ChatStatistics::attachments(); ?></strong></span>
              <small class="text-muted text-uc">Attachments</small>
            </a>
        	</div>
        	<div class="col-sm-6 col-md-3 padder-v b-r b-light lt">
            <span class="fa-stack fa-2x pull-left m-r-sm">
              <i class="fa fa-circle fa-stack-2x icon-muted"></i>
              <i class="fa fa-male fa-stack-1x text-white"></i>
            </span>
            <a class="clear" href="<?php echo admin_uri()."users.php"; ?>">
              <span class="h3 block m-t-xs"><strong id="users-all"><?php echo ChatStatistics::users(); ?></strong></span>
              <small class="text-muted text-uc">Users (<?php echo ChatStatistics::online(); ?> online)</small>
            </a>
        	</div>
        </div>
      </section>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <section class="panel panel-default">
        <?php
          global $ipdb;
          $currentYear  = ( isset( $_GET["year"] ) ) ? $ipdb->escape( trim( $_GET["year"] ) ) : $ipdb->escape( date( "Y" ) );
          $currentMonth = ( isset( $_GET["month"] ) ) ? $ipdb->escape( trim( $_GET["month"] ) ) : $ipdb->escape( date( "m" ) );

          $messages = $ipdb->get_results( "
            SELECT
              `sent_date` as `s`,
              `sent_to` as `u`,
              YEAR(`timestamp`) as `y`,
              MONTH(`timestamp`) as `m`,
              DAY(`timestamp`) as `d`,
              COUNT(*) as `t`
            FROM `$ipdb->messages`
            WHERE
              YEAR(`timestamp`) = '{$currentYear}'
              AND
              MONTH(`timestamp`) = '{$currentMonth}'
            GROUP BY
              `m`, `d`
            ORDER BY
              `s` ASC
          " );
        ?>
        <script type="text/javascript">var flotPar=<?php echo json_encode( $messages ); ?>;</script>
        <div class="col-xs-12 padder-v"><div class="flot-db-messages"></div></div>
      </section>
    </div>
  </div>
<?php
  include( "footer.php" );
?>
<?php
  include( "header.php" );
  $do_install = ( isset( $_GET["install"] ) ) ? true : false;

  $lang_file      = dirname( dirname( __FILE__ ) )."/languages/%s.lng";
  $edit_lang_code = "en";
  $edit_lang_name = "English";
  if ( isset( $_GET["lang"] ) ) {
    $edit_lang_code = trim( strtolower( $_GET["lang"] ) );
    $edit_lang_name = $ipLang->lang_code_to_name( $edit_lang_code );
  }

  if ( !$do_install ) {
    $languages  = $ipLang->reading_languages();
  
    $lang1  = sprintf( $lang_file, "en" );
    $lang2  = sprintf( $lang_file, $edit_lang_code );
  
    $lang1  = ( file_exists( $lang1 ) ) ? implode( "", file( $lang1 ) ) : json_encode( array() );
    $lang2  = ( file_exists( $lang2 ) ) ? implode( "", file( $lang2 ) ) : json_encode( array() );
  
    $lang1  = str_replace( "\xEF\xBB\xBF", "", $lang1 );
    $lang2  = str_replace( "\xEF\xBB\xBF", "", $lang2 );
  
    $lang1  = json_decode( $lang1 );
    $lang2  = json_decode( $lang2 );
  
    $lcodes = realpath( dirname( dirname( __FILE__ ) )."/includes/lang_names.json" );
    $lcodes = ( $lcodes ) ? json_decode( implode( "", file( $lcodes ) ), true ) : array( "en" => "English" );
    $lcodes = array_filter( array_unique( $lcodes ) );
  
    $has_ime  = $ipLang->ime_list( $edit_lang_code );
  
?>
<script type="text/javascript">searchBox=false;</script>
<form action="<?php echo admin_uri(); ?>includes/pages/languages.php" method="POST">
  <?php echo doc_btn( "Languages" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">Languages</header>
    <div class="row text-sm wrapper">
      <div class="col-xs-12 m-b-xs clearfix">
        <div class="btn-group pull-right">
          <a href="languages.php" class="btn btn-default active">List</a>
          <a href="languages.php?install=1" class="btn btn-default">Install</a>
        </div>
      </div>
    </div>

    <table class="table b-t b-light text-sm tb-languages table-striped tb-global">
      <thead>
        <tr id="lang-creator-lists" style="display: none;">
          <td>
            <select class="form-control" style="width:150px;">
              <?php
                foreach( $lcodes as $lcode_c => $lcode_n ) {
              ?>
              <option value="<?php echo $lcode_c; ?>"><?php echo $lcode_n; ?></option>
              <?php
                }
              ?>
            </select>
          </td>
        </tr>
        <tr id="lang-chooser-lists" style="display: none;">
          <td>
            <select class="form-control" style="width:150px;">
              <?php
                foreach( $languages as $lcode => $lname ) {
              ?>
              <option value="<?php echo $lcode; ?>"<?php echo ( $lcode == $edit_lang_code ) ? ' selected' : null; ?>><?php echo $lname; ?></option>
              <?php
                }
              ?>
            </select>
          </td>
        </tr>
        <tr>
          <td class="clearfix">
            <div class="pull-left">
              <button type="button" name="save" class="btn btn-info" title="Save Language" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-save"></span>
              </button>
              <button type="button" name="delete" class="btn btn-danger" title="Delete Language" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-times"></span>
              </button>
            </div>
          </td>
          <td class="clearfix">
            <div class="pull-right">
              <button type="button" name="fill" class="btn btn-default" title="Fill default strings" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-list-alt"></span>
              </button>
              <button type="button" name="select" class="btn btn-default" title="Select a language" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-keyboard-o"></span>
              </button>
              <button type="button" name="create" class="btn btn-default" title="Create new language" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-file"></span>
              </button>
              <?php
                if ( $has_ime ) {
              ?>
              <button type="button" name="ime" class="btn btn-primary" title="Turn on Instant Translation" data-toggle="tooltip" data-placement="top auto">
                <span class="fa fa-star-o"></span>
              </button>
              <?php
                }
              ?>
            </div>
          </td>
        </tr>
      </thead>
      <tbody>
        <?php
          if ( $lang1 ) {
            foreach( $lang1 as $idx => $idn ) {
        ?>
        <tr>
        	<td colspan="2">
            <div>
              <textarea class="form-control" rows="1" name="lang_var[<?php echo $idx; ?>]" placeholder="<?php echo htmlspecialchars( $idn ); ?>" style="resize: none; border: 0 none !important; background: transparent !important; font-size: 14px;"><?php echo ( isset( $lang2->{$idx} ) ) ? htmlspecialchars( $lang2->{$idx} ) : null; ?></textarea>
            </div>
          </td>
        </tr>
        <?php
            }
          }
        ?>
      </tbody>
    </table>

  </section>
  <input type="hidden" name="lang_idn" value="<?php echo htmlspecialchars( $edit_lang_code ); ?>">
</form>

<div class="tooltip-popover"></div>
<?php
  }
  else {
    loadClass( "ImpactPlus", "required/impact.plus.php" );
    $dl_langs   = ImpactPlus::dl_languages();
    $all_langs  = ImpactPlus::get_languages();
?>
<script type="text/javascript">searchBox=false;</script>
<form action="<?php echo admin_uri(); ?>languages.php?install=1" method="POST">
  <?php echo doc_btn( "Languages#installing-languages" ); ?>
  <section class="panel panel-default">
    <header class="panel-heading">Install Languages</header>
    <div class="row text-sm wrapper">
      <div class="col-xs-12 m-b-xs clearfix">
        <div class="btn-group pull-right">
          <a href="languages.php" class="btn btn-default">List</a>
          <a href="languages.php?install=1" class="btn btn-default active">Install</a>
        </div>
      </div>
    </div>


    <table class="table b-t b-light text-sm tb-languages tb-languages-install tb-global">
      <thead>
        <tr>
          <th>Language</th>
          <th width="300" style="text-align: center;">Date</th>
          <th width="150" class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if ( $all_langs ) {
            foreach( $all_langs as $lang ) {
        ?>
        <tr class="<?php echo ( in_array( $lang->code, $dl_langs ) ) ? "success" : null; ?>">
          <td class="clearfix text-center v-middle">
            <div class="clearfix language-code-names">
              <div class="pull-left">
                <div class="text-as-image"><?php echo $lang->code; ?></div>
              </div>
              <div class="pull-left">
                <div class="language-name-fixed"><?php echo $lang->name; ?></div>
              </div>
            </div>
          </td>
          <td class="clearfix text-center v-middle">
            <dl class="dl-horizontal language-datetime">
              <dt>Added</dt>
              <dd><?php echo time_difference( $lang->date_created ); ?></dd>
              <dt>Updated</dt>
              <dd><?php echo ( (int)$lang->date_updated ) ? time_difference( $lang->date_updated ) : "Never"; ?></dd>
            </dl>
          </td>
          <td class="clearfix text-center v-middle">
            <button type="button" class="btn btn-sm btn-success" name="install" title="Install Language &quot;<?php echo htmlspecialchars( $lang->name ) ?>&quot;" data-id="<?php echo $lang->ID; ?>" data-name="<?php echo $lang->name; ?>" data-toggle="tooltip" data-placement="top auto">
              <span class="fa fa-save"></span>
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
              <p>There is no languages added yet !</p>
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

  include( "footer.php" );
?>
                </section>
              <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen" data-target="#nav"></a>
            </section>
            <aside class="bg-light lter b-l aside-md hide" id="notes">
              <div class="wrapper">Notification</div>
            </aside>
          </section>
        </section>
      </section>
    </section>

    <script type="text/javascript" src="<?php echo admin_uri(); ?>js/jquery.js"></script>
    <?php
      $jsComponents = $headComponents->load( "js" );
      if ( isset( $jsComponents["item"] ) ) {
        foreach( $jsComponents["item"] as $key => $value ) {
          if ( isset( $jsComponents["call"] ) && is_callable( $jsComponents["call"] ) ) {
            echo call_user_func( $jsComponents["call"], $value, $key );
          }
          else {
            echo '<script type="text/javascript" src="'.$value.'"></script>';
          }
        }
      }
      else {
        foreach( $jsComponents as $jsComponent ) {
          echo '<script type="text/javascript" src="'.$jsComponent.'"></script>';
        }
      }
    ?>
    <div class="_5kdv"><div class="_5kgu"><i class="fa fa-check"></i> <span class="_5kgv">Processing&hellip;</span></div></div>
  </body>
</html>
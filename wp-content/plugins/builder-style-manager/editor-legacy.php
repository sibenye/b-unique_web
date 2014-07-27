<?php

/*
Written by Chris Jean for iThemes.com
Licensed under GPLv2

Version 1.2.0

Change Notes:
	1.0.0 - 2013-01-18 - Chris Jean
		Forked version of editor.php version 1.1.2 to retain legacy features.
	1.1.0 - 2013-01-22 - Chris Jean
		Removed clear_fade_messages.
		Added format notice.
	1.2.0 - 2013-02-18 - Chris Jean
		Added it_bsm_filter_preview_url filter to control the URL used for the preview.
*/

class BuilderStyleManager extends ITCoreClass {
	var $_var = 'builder_style_manager';
	var $_page_title = 'Builder Style Manager';
	var $_page_var = 'builder-style-manager';
	var $_menu_title = 'Style Manager';
	var $_default_menu_function = 'add_theme_page';
	var $_menu_priority = '11';
	
	var $_global_storage = true;
	
	var $_styling_groups = array();
	
	
	function BuilderStyleManager() {
		$this->ITCoreClass();
		
		$this->_file = __FILE__;
	}
	
	function init() {
		ITCoreClass::init();
		
		add_action( 'builder_editor_add_custom_settings', array( &$this, 'add_custom_layout_settings' ) );
		add_action( 'builder_editor_save_custom_settings', array( &$this, 'save_custom_layout_settings' ) );
		add_filter( 'get_user_option_closedpostboxes_builder_style_manager', array( &$this, 'filter_closedpostboxes' ), 10, 3 );
	}
	
	function filter_closedpostboxes( $result, $option, $user ) {
		return $this->_closed_meta_boxes;
	}
	
	function add_admin_scripts() {
		ITCoreClass::add_admin_scripts();
		
		wp_enqueue_script( "{$this->_var}-stylesheet-lib", "{$this->_plugin_url}/js/stylesheet-lib.js", array(), '0.1', true );
		wp_enqueue_script( "{$this->_var}-postbox", "{$this->_plugin_url}/js/postbox.js", array( 'jquery', 'postbox' ) );
		wp_enqueue_script( "{$this->_var}-iframe", "{$this->_plugin_url}/js/iframe.js", array( 'jquery' ), '0.1', true );
		wp_enqueue_script( "{$this->_var}-editor", "{$this->_plugin_url}/js/editor.js", array( 'jquery' ), '0.1' );
		wp_enqueue_script( "{$this->_var}-colorpicker", "{$this->_plugin_url}/js/colorpicker/colorpicker.js", array( 'jquery' ), '1.0', true );
	}
	
	function add_admin_styles() {
		ITCoreClass::add_admin_styles();
		
		wp_enqueue_style( "{$this->_var}-style", "{$this->_plugin_url}/css/editor.css" );
		wp_enqueue_style( "{$this->_var}-colorpicker", "{$this->_plugin_url}/js/colorpicker/colorpicker.css" );
	}
	
	
	// Pages //////////////////////////////////////
	
	function add_custom_layout_settings( $layout ) {
		$data = array();
		
		if ( ! empty( $this->_options['layouts'][$layout['guid']] ) )
			$data['style'] = $this->_options['layouts'][$layout['guid']];
		
		$form =& new ITForm( $data, array( 'prefix' => $this->_var ) );
		
		
		$styles = array( '' => '' );
		
		foreach ( (array) $this->_options['styles'] as $id => $style )
			$styles[$id] = $style['name'];
		
?>
	<tr><th scope="row"><label for="width">Style</label></th>
		<td>
			<?php $form->add_drop_down( 'style', $styles ); ?><br />
			Setting a Style overrides the global Style (if one is set).
		</td>
	</tr>
<?php
		
	}
	
	function save_custom_layout_settings( $layout ) {
		if ( ! empty( $_POST["$this->_var-style"] ) )
			$this->_options['layouts'][$layout['guid']] = $_POST["$this->_var-style"];
		else
			unset( $this->_options['layouts'][$layout['guid']] );
		
		$this->_save();
	}
	
	function index() {
		ITCoreClass::index();
		
		if ( isset( $_REQUEST['cancel'] ) )
			$this->_list_styles();
		else if ( isset( $_REQUEST['reset_data'] ) )
			$this->_reset_data();
		else if ( isset( $_REQUEST['upload_background_image_save'] ) )
			$this->_upload_background_image_save();
		else if ( isset( $_REQUEST['upload_background_image'] ) )
			$this->_upload_background_image();
		else if ( isset( $_REQUEST['generate_stylesheet_file'] ) )
			$this->_generate_stylesheet_file( $_REQUEST['generate_stylesheet_file'] );
		else if ( isset( $_REQUEST['delete_style'] ) )
			$this->_delete_style();
		else if ( isset( $_REQUEST['delete_style_screen'] ) )
			$this->_delete_style_screen();
		else if ( isset( $_REQUEST['duplicate_style'] ) )
			$this->_duplicate_style();
		else if ( isset( $_REQUEST['duplicate_style_screen'] ) )
			$this->_duplicate_style_screen();
		else if ( isset( $_REQUEST['save'] ) || isset( $_REQUEST['save_and_continue'] ) )
			$this->_save_style();
		else if ( isset( $_REQUEST['style'] ) || isset( $_REQUEST['add_style'] ) )
			$this->_modify_style();
		else
			$this->_list_styles();
	}
	
	function _reset_data() {
		global $wpdb;
		
		foreach ( (array) $this->_options['style'] as $id => $style )
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='_custom_style' AND meta_value=%s", $id ) );
		
		
		do_action( "it_storage_reset_{$this->_var}" );
		
		ITUtility::show_status_message( 'Data reset' );
		
		$this->_list_styles();
	}
	
	function _delete_style() {
		$style = $_REQUEST['delete_style_screen'];
		
		if ( isset( $_POST['replacement_style'] ) ) {
			$replacement = $_POST['replacement_style'];
			
			$this->_options['global'] = $replacement;
		}
		else if ( $style === $this->_options['global'] ) {
			unset( $this->_options['global'] );
		}
		
		unset( $this->_options['styles'][$style] );
		$this->_save();
		
		$this->_delete_stylesheet( $style );
		
?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		
		win.jQuery("#entry-<?php echo $style; ?> .set_global").html("&nbsp;");
		win.jQuery("#entry-<?php echo $style; ?>").css("background-color", "#FF3333").fadeOut("slow").attr("id", "___<?php echo $style; ?>");
		
		<?php if ( isset( $replacement ) ) : ?>
			win.jQuery("#entry-<?php echo $replacement; ?> .set_global").html('<?php $this->_print_listing_global_entry( $replacement ); ?>');
		<?php endif; ?>
		
		<?php if ( empty( $this->_options['styles'] ) ) : ?>
			win.jQuery(".wrap .tablenav:first").remove();
			win.jQuery(".wrap br").remove();
			win.jQuery(".wrap #styles:first").replaceWith("<div>No styles have been created. Please click the Create Style button to create a new style.</div>");
		<?php else : ?>
			win.jQuery("tr[id^='entry-']:even").addClass("alternate");
			win.jQuery("tr[id^='entry-']:odd").removeClass("alternate");
		<?php endif; ?>
		
		win.tb_remove();
		/* ]]> */
	</script>
<?php
		
	}
	
	function _delete_style_screen() {
		it_classes_load( 'it-array-sort.php' );
		
		
		if ( count( $this->_options['styles'] ) > 1 ) {
			$styles = array( '' => '' );
			
			$sorter = new ITArraySort( $this->_options['styles'], 'name' );
			$this->_options['styles'] = $sorter->get_sorted_array();
			
			foreach ( (array) $this->_options['styles'] as $id => $style ) {
				if ( $id != $_REQUEST['delete_style_screen'] )
					$styles[$id] = $style['name'];
			}
		}
		
		$style = $_REQUEST['delete_style_screen'];
		$data = $this->_options['styles'][$style];
		
		$form =& new ITForm();
		
?>
<?php $form->start_form(); ?>
	<?php if ( isset( $styles ) && isset( $this->_options['global'] ) && ( $this->_options['global'] === $style ) ) : ?>
		<div>This style is the global style. Select a style from the drop_down to select a replacement global style.</div>
		<br />
		
		<div>Please select a style to use as the new global style. If a new global style is not selected, a global style will not be used.</div>
		<br />
		
		<div>New Global: <?php $form->add_drop_down( 'replacement_style', $styles ); ?></div>
	<?php else : ?>
		<div>Please confirm that you would like to delete the <strong><?php echo $this->_options['styles'][$style]['name']; ?></strong> style.</div>
	<?php endif; ?>
	
	<br />
	<div style="text-align:center;">
		<?php $form->add_submit( 'delete_style', array( 'value' => 'Delete', 'class' => 'button-primary' ) ); ?>
		<?php $form->add_submit( 'cancel', array( 'value' => 'Cancel', 'class' => 'button-secondary', 'onclick' => 'var win = window.dialogArguments || opener || parent || top; win.tb_remove(); return false;' ) ); ?>
	</div>
	
	<?php $form->add_hidden( 'delete_style_screen', $style ); ?>
	<?php $form->add_hidden( 'render_clean', '1' ); ?>
<?php $form->end_form(); ?>
<?php
		
	}
	
	function _duplicate_style() {
		$source = $_POST['duplicate_style_screen'];
		$name = $_POST['duplicate_name'];
		$id = uniqid( '' );
		
		if ( empty( $name ) ) {
			ITUtility::show_error_message( 'You must supply a name for the duplicated style.' );
			$this->_duplicate_style_screen();
			return;
		}
		foreach ( (array) $this->_options['styles'] as $style ) {
			if ( $name === $style['name'] ) {
				ITUtility::show_error_message( 'A style with that name already exists. Please enter a different name.' );
				$this->_duplicate_style_screen();
				return;
			}
		}
		
		$this->_options['styles'][$id] = $this->_options['styles'][$source];
		$this->_options['styles'][$id]['name'] = $name;
		
		$this->_save();
		$this->_regenerate_stylesheet( $id );
		
		
		$style = $this->_options['styles'][$id];
		
		
		$name = str_replace( '\'', '\\\'', $name );
		
		
?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		
		<?php $this->_output_js_string_var( 'newRow', $this->_print_listing( $id, '', true, false ) ); ?>
		
		var rows = win.jQuery("tr[id^='entry-']");
		var i;
		for(i = 0; i < rows.get().length; i++) {
			if("<?php echo strtolower( $name ); ?>" < win.jQuery("tr[id^='entry-']:eq(" + i + ") a[title='Modify Style']").html().toLowerCase())
				break;
		}
		
		i--;
		
		if((rows.get().length > 0) && (i >= 0)) {
			win.jQuery("tr[id^='entry-']:eq(" + i + ")").after(newRow);
		}
		else {
			if(win.jQuery("table#styles > tbody") == undefined) {
				win.jQuery("table#styles").html(newRow);
			}
			else {
				win.jQuery("table#styles > tbody").prepend(newRow);
			}
		}
		
		win.jQuery("tr[id^='entry-']:even").addClass("alternate");
		win.jQuery("tr[id^='entry-']:odd").removeClass("alternate");
		
		var origColor = win.jQuery("#entry-<?php echo $id; ?>").css("background-color");
		win.jQuery("#entry-<?php echo $id; ?>").css("background-color", "#33FF33").fadeIn("slow").animate({backgroundColor:origColor}, 300);
		
		win.tb_init("#entry-<?php echo $id; ?> a[href*='TB_iframe']");
		win.tb_remove();
		/* ]]> */
	</script>
<?php
		
	}
	
	function _duplicate_style_screen() {
		$style = $_REQUEST['duplicate_style_screen'];
		
		$data = array();
		if ( isset( $_REQUEST['duplicate_name'] ) )
			$data['duplicate_name'] = $_REQUEST['duplicate_name'];
		
		$form =& new ITForm( $data );
		
?>
	<?php $form->start_form(); ?>
		<div>Duplicating <strong><?php echo $this->_options['styles'][$style]['name']; ?>.</strong></div>
		<br />
		
		<div>Please name the new style:</div>
		<div><?php $form->add_text_box( 'duplicate_name' ); ?></div>
		<br />
		
		<div style="text-align:center;">
			<?php $form->add_submit( 'duplicate_style', array( 'value' => 'Create Duplicate', 'class' => 'button-primary' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => 'Cancel', 'class' => 'button-secondary', 'onclick' => 'var win = window.dialogArguments || opener || parent || top; win.tb_remove(); return false;' ) ); ?>
		</div>
		
		<?php $form->add_hidden( 'duplicate_style_screen', $style ); ?>
		<?php $form->add_hidden( 'render_clean', '1' ); ?>
	</form>
<?php
		
	}
	
	function _show_other_styles_exist_notice() {
		if ( defined( 'BUILDER_STYLE_MANAGER_HIDE_FORMAT_NOTICE' ) || ! empty( $this->_options['hide_other_styles_exist_notice'] ) )
			return;
		
		if ( ! empty( $_GET['hide_other_styles_exist_notice'] ) ) {
			$this->_options['hide_other_styles_exist_notice'] = 1;
			$this->_save();
			return;
		}
		
		$storage =& new ITStorage( 'builder_style_manager_2', true );
		$options = $storage->load();
		
		if ( empty( $options ) )
			return;
		
		echo '<div id="message" class="updated">';
		echo '<p>Style Manager now supports two different formats: a format for the newer, responsive-ready child themes and a format for the older, non-responsive child themes. These formats are not compatible with one another, meaning that you will only have access to the Styles that are usable by your current child theme.</p>';
		echo '<p>Since you are running a non-responsive child theme, the listing below will only show the Styles created in this format. Any Styles in the other format still exist and can still be used if you switch back to a responsive-ready child theme.</p>';
		echo "<p><a href='{$this->_self_link}&hide_other_styles_exist_notice=1'>Click here to remove this notice</a></p>";
		echo '</div>';
	}
	
	function _list_styles() {
		if ( ! empty( $this->_options['styles'] ) ) {
			it_classes_load( 'it-array-sort.php' );
			
			$sorter = new ITArraySort( $this->_options['styles'], 'name' );
			$styles = $sorter->get_sorted_array();
		}
		
		$form =& new ITForm();
		
?>
	<div class="wrap">
		<?php $form->start_form(); ?>
			<h2>Styles</h2>
			
			<?php $this->_show_other_styles_exist_notice(); ?>
			
			<?php if ( ! empty( $styles ) ) : ?>
				<div class="tablenav">
					<div class="alignleft actions">
						<?php $form->add_submit( 'add_style', array( 'value' => 'Create Style', 'class' => 'button-secondary add' ) ); ?>
					</div>
					
					<br class="clear" />
				</div>
				
				<br class="clear" />
				
				<table id="styles" class="widefat fixed" cellspacing="0">
					<thead>
						<tr class="thead">
							<th>Style Name</th>
							<th title="The global style is used for all layouts that don't have a specific style">Global</th>
						</tr>
					</thead>
					<tfoot>
						<tr class="thead">
							<th>Style Name</th>
							<th title="The global style is used for all layouts that don't have a specific style">Global</th>
						</tr>
					</tfoot>
					<tbody>
						<?php
							$class = ' class="alternate"';
							
							foreach ( (array) $styles as $style_id => $style ) {
								$this->_print_listing( $style_id, $class );
								$class = ( $class == '' ) ? ' class="alternate"' : '';
							}
						?>
					</tbody>
				</table>
				
				<br class="clear" />
			<?php else : ?>
				<div>No styles have been created. Please click the Create Style button to create a new style.</div>
			<?php endif; ?>
			
			<div class="tablenav">
				<div class="alignleft actions">
					<?php $form->add_submit( 'add_style', array( 'value' => 'Create Style', 'class' => 'button-secondary add' ) ); ?>
				</div>
				
				<br class="clear" />
			</div>
		<?php $form->end_form(); ?>
	</div>
	
	<script type="text/javascript">
		/* <![CDATA[ */
		jQuery(document).ready( function() { tb_init("a[href*='TB_iframe']"); } );
		/* ]]> */
	</script>
<?php
		
	}
	
	function _print_listing( $style_id, $class, $return = false, $display = true ) {
		if ( true === $return )
			ob_start();
		
		$global = ( isset( $this->_options['global'] ) && ( $this->_options['global'] === $style_id ) ) ? true : false;
		
?>
	<tr <?php if ( false === $display ) echo 'style="display:none;" '; ?>id="entry-<?php echo $style_id; ?>"<?php echo $class; ?>>
		<td>
			<strong><a href="<?php echo $this->_self_link; ?>&style=<?php echo $style_id; ?>" title="Modify Style"><?php echo $this->_options['styles'][$style_id]['name']; ?></a></strong><br />
			<div class="row-actions">
				<span class="edit"><a href="<?php echo $this->_self_link; ?>&style=<?php echo $style_id; ?>" title="Modify Style">Edit</a> | </span>
				<span class="duplicate"><a href="<?php echo $this->_self_link; ?>&render_clean=1&duplicate_style_screen=<?php echo $style_id; ?>&TB_iframe=true&height=100&width=250" title="Duplicate Style">Duplicate</a> | </span>
				<span class="delete"><a href="<?php echo $this->_self_link; ?>&render_clean=1&delete_style_screen=<?php echo $style_id; ?>&TB_iframe=true&height=100&width=<?php echo ( ( true === $global ) && ( count( $this->_options['styles'] ) > 1 ) ) ? 400 : 200; ?>" title="Delete Style">Delete</a></span>
			</div>
		</td>
		<td class="set_global" title="The global style is used for all layouts that don't have a specific style">
			<?php $this->_print_listing_global_entry( $style_id ); ?>
		</td>
	</tr>
<?php
		
		if ( true === $return ) {
			$listing = ob_get_contents();
			
			ob_end_clean();
			
			return $listing;
		}
	}
	
	function _print_listing_global_entry( $style_id ) {
		if ( isset( $this->_options['global'] ) && ( $this->_options['global'] === $style_id ) )
			echo '<strong>Yes</strong>';
		else
			echo '<div class="row-actions"><a href="' . $this->_self_link . '&render_clean=1&set_global_style_screen=' . $style_id . '&TB_iframe=true&height=100&width=250" title="Set this style as global">Set as global</a></div>';
	}
	
	function _save_style() {
		$used_inputs = explode( ',', $_POST['used-inputs'] );
		$data = array();
		
		$skip = array( 'save', 'save_and_continue', 'cancel', 'style', 'add_style', 'global' );
		
		foreach ( (array) $used_inputs as $index => $var ) {
			if ( in_array( $var, $skip ) )
				unset( $used_inputs[$index] );
			else {
				$data[$var] = ( isset( $_POST[$var] ) ) ? $_POST[$var] : '';
				
				$data[$var] = preg_replace( "/\r\n|\n|\r/", "\n", $data[$var] );
			}
		}
		
		
		$error = false;
		
		if ( empty( $data['name'] ) ) {
			ITUtility::show_error_message( 'You must supply a Name for the style.' );
			$error = true;
		}
		
		if ( isset( $this->_options['styles'] ) && is_array( $this->_options['styles'] ) ) {
			foreach ( (array) $this->_options['styles'] as $id => $style ) {
				if ( ( strtolower( $data['name'] ) === strtolower( $style['name'] ) ) && ( ! isset( $_REQUEST['style'] ) || ( $_REQUEST['style'] !== $id ) ) ) {
					ITUtility::show_error_message( 'A style with that Name already exists. Please choose a unique Name.' );
					$error = true;
					
					break;
				}
			}
		}
		
		if ( true === $error ) {
			$this->_cached_style = $data;
			$this->_modify_style();
			
			return;
		}
		
		
		if ( ! isset( $this->_options['styles'] ) || ! is_array( $this->_options['styles'] ) )
			$this->_options['styles'] = array();
		
		
		if ( isset( $_REQUEST['add_style'] ) ) {
			$id = uniqid( '' );
			
			$_REQUEST['style'] = $id;
			unset( $_REQUEST['add_style'] );
			
			ITUtility::show_status_message( "{$data['name']} created" );
		}
		else if ( isset( $_REQUEST['style'] ) ) {
			$id = $_REQUEST['style'];
			
			if ( $data['name'] !== $this->_options['styles'][$id]['name'] )
				$this->_delete_stylesheet( $id );
			$this->_delete_old_stylesheets( $id );
			
			ITUtility::show_status_message( "{$data['name']} updated" );
		}
		
		
		if ( ! empty( $_POST['global'] ) )
			$this->_options['global'] = $id;
		else if ( isset( $this->_options['global'] ) && ( $id === $this->_options['global'] ) && empty( $_POST['global'] ) )
			$this->_options['global'] = null;
		
		$this->_options['styles'][$id] = $data;
		
		
		$this->_save();
		$this->_regenerate_stylesheet( $id );
		
		
		if ( empty( $_REQUEST['save_and_continue'] ) )
			$this->_list_styles();
		else
			$this->_modify_style();
	}
	
	function _regenerate_stylesheet( $id ) {
		require_once( dirname( __FILE__ ) . '/generator-legacy.php' );
		
		$generator =& new BuilderStyleManagerGenerator();
		$result = $generator->generate_stylesheet( $id );
		
		if ( ! empty( $result['url'] ) )
			ITUtility::show_status_message( "Saved stylesheet to <a href='{$result['url']}' target='_blank'>" . str_replace( ABSPATH, '', $result['file'] ) . '</a>.' );
		else
			ITUtility::show_error_message( 'Unable to generate stylesheet file. Check file permissions for your uploads directory. The stylesheet will still function but will not be as efficient as it could be.' );
	}
	
	function _delete_stylesheet( $id ) {
		require_once( dirname( __FILE__ ) . '/generator-legacy.php' );
		
		$generator =& new BuilderStyleManagerGenerator();
		$generator->delete_stylesheet( $id );
	}
	
	function _delete_old_stylesheets() {
		require_once( dirname( __FILE__ ) . '/generator-legacy.php' );
		
		$generator =& new BuilderStyleManagerGenerator();
		$generator->delete_old_stylesheets();
	}
	
	function _modify_style() {
		$this->_add_meta_box( 'Site Background', 'left' );
		$this->_add_meta_box( 'Site Font', 'left' );
		$this->_add_meta_box( 'Links', 'left' );
		$this->_add_meta_box( 'Headings', 'left' );
		$this->_add_meta_box( 'Container', 'left' );
		$this->_add_meta_box( 'Post/Page Content Styling', 'left' );
		$this->_add_meta_box( 'Comments Styling', 'left' );
		$this->_add_meta_box( 'Basic Module Styling', 'left' );
		$this->_add_meta_box( 'Module Sidebars', 'left' );
		$this->_add_meta_box( 'Header Module', 'left' );
		$this->_add_meta_box( 'Content Module', 'left' );
		$this->_add_meta_box( 'Navigation Module', 'left' );
		$this->_add_meta_box( 'Image Module', 'left' );
		$this->_add_meta_box( 'Widget Bar Module', 'left' );
		$this->_add_meta_box( 'HTML Module', 'left' );
		$this->_add_meta_box( 'Footer Module', 'left' );
		
		$this->_add_meta_box( 'Preview', 'right', false );
		$this->_add_meta_box( 'CSS Preview', 'right', false );
		$this->_add_meta_box( 'Custom CSS', 'right', false );
		
		$this->_style = array();
		
		if ( isset( $this->_cached_style ) && is_array( $this->_cached_style ) )
			$this->_style = $this->_cached_style;
		else if ( isset( $_REQUEST['style'] ) && isset( $this->_options['styles'][$_REQUEST['style']] ) ) {
			$this->_style = $this->_options['styles'][$_REQUEST['style']];
			
			if ( isset( $this->_options['global'] ) && ( $_REQUEST['style'] == $this->_options['global'] ) )
				$this->_style['global'] = 'yes';
		}
		
		
		$form =& new ITForm( $this->_style );
		$this->_form =& $form;
		
?>
	<div class="wrap">
		<p><a href="<?php echo $this->_self_link; ?>">&laquo; Back to Styles</a></p>
		
		<h2><?php _e( 'Style Editor', $this->_var ); ?></h2>
		
		<?php $form->start_form(); ?>
			<table class="form-table">
				<tr><th scope="row">Name</th>
					<td><?php $form->add_text_box( 'name' ); ?></td>
				</tr>
				<tr><th scope="row">Global</th>
					<td><?php $form->add_drop_down( 'global', array( '' => 'No', 'yes' => 'Yes' ) ); ?>
						<p>Setting a Style as Global applies the styling to all layouts that don't have a Style selected.</p>
						<p><i>Note: Only one Style can be Global.</i></p>
					</td>
				</tr>
			</table>
			
			<div id="alert-message-container"></div>
			
			<?php $this->_print_meta_boxes(); ?>
			
			<p class="submit">
				<?php $form->add_submit( 'save', array( 'value' => 'Save Style', 'class' => 'button-primary', 'disabled' => 'disabled' ) ); ?>
				<?php $form->add_submit( 'save_and_continue', array( 'value' => 'Save Style and Continue Editing', 'class' => 'button-secondary', 'disabled' => 'disabled' ) ); ?>
				<?php $form->add_submit( 'cancel', array( 'value' => 'Cancel', 'class' => 'button-secondary cancel' ) ); ?>
			</p>
			
			<?php
				if ( isset( $_REQUEST['style'] ) )
					$form->add_hidden( 'style', $_REQUEST['style'] );
				else if ( isset( $_REQUEST['add_style'] ) )
					$form->add_hidden( 'add_style', $_REQUEST['add_style'] );
				
				$form->add_hidden( 'normalize-styling-css-href', "{$this->_plugin_url}/css/normalize-legacy-styling.css", true );
				
				foreach ( (array) $this->_style_groups as $group => $names )
					$form->add_hidden( "$group-inputs", implode( ',', $names ), true );
			?>
		<?php $form->end_form(); ?>
		
		<form style="display:none" method="get" action="">
			<p>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			</p>
		</form>
	</div>
	
	<script type="text/javascript">
		/* <![CDATA[ */
//		init_layout_editor();
		/* ]]> */
	</script>
<?php
		
	}
	
	function _add_meta_box( $title, $position, $closed = true ) {
		$var = strtolower( preg_replace( '/-+/', '-', preg_replace( '/[^a-z\-]+/i', '-', $title ) ) );
		$method_var = str_replace( '-', '_', $var );
		
		add_meta_box( "$this->_var-style-$var", $title, array( &$this, "meta_box_style_$method_var" ), $this->_var, $position );
		
		if ( true === $closed )
			$this->_closed_meta_boxes[] = "$this->_var-style-$var";
	}
	
	
	
	function _print_meta_boxes() {
		$form = $this->_form;
		
		$form->add_hidden( 'html-selector', 'html', true );
		$form->add_hidden( 'body-selector', 'body', true );
		$form->add_hidden( 'container-selector', '.builder-container-outer-wrapper', true );
		$form->add_hidden( 'module-selector', '.builder-module', true );
		$form->add_hidden( 'module_outer-selector', '.builder-module-outer-wrapper', true );
		$form->add_hidden( 'builder_module_sidebar-selector', '.builder-module-sidebar', true );
		$form->add_hidden( 'module_not_last-selector', '.builder-module-top,.builder-module-middle', true );
		$form->add_hidden( 'module_first-selector', '.builder-module-top,.builder-module-single', true );
		$form->add_hidden( 'module_last-selector', '.builder-module-bottom,.builder-module-single', true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( 'links-selector', '.loop a,.entry-content a,.builder-module-html .builder-module-element a', true );
			$form->add_hidden( 'links_hover-selector', '.loop a:hover,.entry-content a:hover,.builder-module-html .builder-module-element a:hover', true );
		}
		else {
			$form->add_hidden( 'links-selector', '.post-content a,.post-meta a,.meta-bottom a,.builder-module-html .builder-module-element a', true );
			$form->add_hidden( 'links_hover-selector', '.post-content a:hover,.post-meta a:hover,.meta-bottom a:hover,.builder-module-html .builder-module-element a:hover', true );
		}
		
		
		$form->add_hidden( 'h1-selector', 'h1', true );
		$form->add_hidden( 'h2-selector', 'h2', true );
		$form->add_hidden( 'h3-selector', 'h3', true );
		$form->add_hidden( 'h4-selector', 'h4', true );
		$form->add_hidden( 'h5-selector', 'h5', true );
		$form->add_hidden( 'h6-selector', 'h6', true );
		
		$form->add_hidden( 'site_title-selector', '.site-title', true );
		$form->add_hidden( 'site_tagline-selector', '.site-tagline', true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( 'post_title-selector', '.hentry .entry-title,.hentry .entry-title h1,.hentry .entry-title h2,.hentry .entry-title h3', true );
			$form->add_hidden( 'page_title-selector', '.page .entry-title,.page .entry-title h1,.page .entry-title h2,.page .entry-title h3', true );
		}
		else {
			$form->add_hidden( 'post_title-selector', '.post .post-title h1,.post .post-title h2,.post .post-title h3', true );
			$form->add_hidden( 'page_title-selector', '.page .post-title h1,.page .post-title h2,.page .post-title h3', true );
		}
		
		$form->add_hidden( 'h1_descendants-selector', 'h1', true );
		$form->add_hidden( 'h2_descendants-selector', 'h2', true );
		$form->add_hidden( 'h3_descendants-selector', 'h3', true );
		$form->add_hidden( 'h4_descendants-selector', 'h4', true );
		$form->add_hidden( 'h5_descendants-selector', 'h5', true );
		$form->add_hidden( 'h6_descendants-selector', 'h6', true );
		
		$form->add_hidden( 'site_title_descendants-selector', '.site-title,.site-title a', true );
		$form->add_hidden( 'site_tagline_descendants-selector', '.site-tagline,.site-tagline a', true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( 'post_title_descendants-selector', '.hentry .entry-title,.hentry .entry-title h1,.hentry .entry-title h2,.hentry .entry-title h3', true );
			$form->add_hidden( 'page_title_descendants-selector', '.page .entry-title,.page .entry-title h1,.page .entry-title h2,.page .entry-title h3', true );
		}
		else {
			$form->add_hidden( 'post_title_descendants-selector', '.post .post-title,.post .post-title h1,.post .post-title h2,.post .post-title h3', true );
			$form->add_hidden( 'page_title_descendants-selector', '.page .post-title,.page .post-title h1,.page .post-title h2,.page .post-title h3', true );
		}
		
		$form->add_hidden( 'h1_link-selector', 'h1 a', true );
		$form->add_hidden( 'h2_link-selector', 'h2 a', true );
		$form->add_hidden( 'h3_link-selector', 'h3 a', true );
		$form->add_hidden( 'h4_link-selector', 'h4 a', true );
		$form->add_hidden( 'h5_link-selector', 'h5 a', true );
		$form->add_hidden( 'h6_link-selector', 'h6 a', true );
		
		$form->add_hidden( 'site_title_link-selector', '.site-title a', true );
		$form->add_hidden( 'site_tagline_link-selector', '.site-tagline a', true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( 'post_title_link-selector', '.hentry .entry-title a', true );
			$form->add_hidden( 'page_title_link-selector', '.page .entry-title a', true );
		}
		else {
			$form->add_hidden( 'post_title_link-selector', '.post .post-title a', true );
			$form->add_hidden( 'page_title_link-selector', '.page .post-title a', true );
		}
		
		$form->add_hidden( 'h1_link_hover-selector', 'h1 a:hover', true );
		$form->add_hidden( 'h2_link_hover-selector', 'h2 a:hover', true );
		$form->add_hidden( 'h3_link_hover-selector', 'h3 a:hover', true );
		$form->add_hidden( 'h4_link_hover-selector', 'h4 a:hover', true );
		$form->add_hidden( 'h5_link_hover-selector', 'h5 a:hover', true );
		$form->add_hidden( 'h6_link_hover-selector', 'h6 a:hover', true );
		
		$form->add_hidden( 'site_title_link_hover-selector', '.site-title a:hover', true );
		$form->add_hidden( 'site_tagline_link_hover-selector', '.site-tagline a:hover', true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( 'post_title_link_hover-selector', '.hentry .entry-title a:hover', true );
			$form->add_hidden( 'page_title_link_hover-selector', '.page .entry-title a:hover', true );
		}
		else {
			$form->add_hidden( 'post_title_link_hover-selector', '.post .post-title a:hover', true );
			$form->add_hidden( 'page_title_link_hover-selector', '.page .post-title a:hover', true );
		}
		
		$form->add_hidden( 'module_sidebars-selector', '.builder-module-sidebar-with-element', true );
		$form->add_hidden( 'module_sidebars_link-selector', '.builder-module-sidebar-with-element a', true );
		$form->add_hidden( 'module_sidebars_link_hover-selector', '.builder-module-sidebar-with-element a:hover', true );
		$form->add_hidden( 'module_sidebar_widgets_vertical_gap-selector', '.builder-module-sidebar-with-element .widget-top,.builder-module-sidebar-with-element .widget-middle,.builder-module-sidebar-with-element .widget-wrapper-top .widget,.builder-module-sidebar-with-element .widget-wrapper-left .widget,.builder-module-sidebar-with-element .widget-wrapper-right .widget', true );
		$form->add_hidden( 'module_sidebar_widgets-selector', '.builder-module-sidebar-with-element .widget', true );
		$form->add_hidden( 'module_sidebar_widgets_link-selector', '.builder-module-sidebar-with-element .widget a', true );
		$form->add_hidden( 'module_sidebar_widgets_link_hover-selector', '.builder-module-sidebar-with-element .widget a:hover', true );
		$form->add_hidden( 'module_sidebar_widgets_title-selector', '.builder-module-sidebar-with-element .widget .widget-title', true );
		$form->add_hidden( 'module_sidebar_widgets_horizontal_left_gap-selector', '.builder-module-sidebar-with-element .widget-wrapper-right .widget', true );
		$form->add_hidden( 'module_sidebar_widgets_horizontal_right_gap-selector', '.builder-module-sidebar-with-element .widget-wrapper-left .widget', true );
		
//		$form->add_hidden( 'module_outside_gap_top-selector', '.builder-module .builder-module-block', true );
//		$form->add_hidden( 'module_outside_gap_bottom-selector', '.builder-module .builder-module-block', true );
//		$form->add_hidden( 'module_outside_gap_left-selector', '.builder-module .left .builder-module-block', true );
//		$form->add_hidden( 'module_outside_gap_right-selector', '.builder-module .right .builder-module-block', true );
//		$form->add_hidden( 'widget_bar_widgets_outside_gap_left-selector', '.builder-module-widget-bar .left .widget,.builder-module-widget-bar .single .widget', true );
//		$form->add_hidden( 'widget_bar_widgets_outside_gap_right-selector', '.builder-module-widget-bar .right .widget,.builder-module-widget-bar .single .widget', true );
		
		$form->add_hidden( 'widget_bar_modules-selector', '.builder-module-widget-bar', true );
		$form->add_hidden( 'widget_bar_modules_outside_gap_top-selector', '.builder-module-widget-bar .widget-top,.builder-module-widget-bar .widget-single', true );
		$form->add_hidden( 'widget_bar_modules_outside_gap_bottom-selector', '.builder-module-widget-bar .widget-bottom,.builder-module-widget-bar .widget-single', true );
		$form->add_hidden( 'widget_bar_modules_outside_gap_left-selector', '.builder-module-widget-bar .left .widget,.builder-module-widget-bar .single .widget', true );
		$form->add_hidden( 'widget_bar_modules_outside_gap_right-selector', '.builder-module-widget-bar .right .widget,.builder-module-widget-bar .single .widget', true );
		$form->add_hidden( 'widget_bar_widgets-selector', '.builder-module-widget-bar .widget', true );
		$form->add_hidden( 'widget_bar_widgets_link-selector', '.builder-module-widget-bar .widget a', true );
		$form->add_hidden( 'widget_bar_widgets_link_hover-selector', '.builder-module-widget-bar .widget a:hover', true );
		$form->add_hidden( 'widget_bar_widgets_title-selector', '.builder-module-widget-bar .widget .widget-title', true );
		$form->add_hidden( 'widget_bar_widgets_vertical_gap-selector', '.builder-module-widget-bar .widget-top,.builder-module-widget-bar .widget-middle', true );
//		$form->add_hidden( 'widget_bar_widgets_vertical_gap-selector', '.builder-module-widget-bar .widget', true );
		$form->add_hidden( 'widget_bar_widgets_horizontal_left_gap-selector', '.builder-module-widget-bar .widget-wrapper-right .widget,.builder-module-widget-bar .widget-wrapper-middle .widget', true );
		$form->add_hidden( 'widget_bar_widgets_horizontal_right_gap-selector', '.builder-module-widget-bar .widget-wrapper-left .widget,.builder-module-widget-bar .widget-wrapper-middle .widget', true );
		
		$form->add_hidden( 'navigation_bg-selector', '.builder-module.builder-module-navigation,.builder-module-navigation li a', true );
		$form->add_hidden( 'navigation_bg_hover-selector', '.builder-module-navigation li a:hover', true );
		$form->add_hidden( 'navigation_border-selector', '.builder-module.builder-module-navigation,.builder-module-navigation li li,.builder-module-navigation li ul', true );
		$form->add_hidden( 'navigation_text-selector', '.builder-module-navigation li a', true );
		$form->add_hidden( 'navigation_text_hover-selector', '.builder-module-navigation li a:hover', true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( 'post-selector', '.hentry', true );
			$form->add_hidden( 'post_link-selector', '.hentry a', true );
			$form->add_hidden( 'post_link_hover-selector', '.hentry a:hover', true );
		}
		else {
			$form->add_hidden( 'post-selector', '.post', true );
			$form->add_hidden( 'post_link-selector', '.post a', true );
			$form->add_hidden( 'post_link_hover-selector', '.post a:hover', true );
		}
		
		$form->add_hidden( 'comments-selector', '#comments', true );
		$form->add_hidden( 'comments_commentlist-selector', '#comments ol.commentlist', true );
		$form->add_hidden( 'comments_heading-selector', '#comments h3', true );
		$form->add_hidden( 'comments_comment-selector', '#comments .comment', true );
		$form->add_hidden( 'comments_comment_link-selector', '#comments .comment a', true );
		$form->add_hidden( 'comments_comment_link_hover-selector', '#comments .comment a:hover', true );
		$form->add_hidden( 'comments_last_comment-selector', '#comments .comment:last-child', true );
		
		$form->add_hidden( 'module-margin-top-sync', 'module-margin-bottom' );
		$form->add_hidden( 'module_sidebar_widgets_vertical_gap-margin-bottom-sync', 'module_sidebar_widgets_horizontal_left_gap-margin-left---.5,module_sidebar_widgets_horizontal_right_gap-margin-right---.5', true );
		$form->add_hidden( 'module_sidebar_widgets_outside_gap_top-margin-top-sync', 'module_sidebar_widgets_outside_gap_bottom-margin-bottom,module_sidebar_widgets_outside_gap_left-margin-left,module_sidebar_widgets_outside_gap_right-margin-right', true );
		$form->add_hidden( 'widget_bar_modules_outside_gap_top-margin-top-sync', 'widget_bar_modules_outside_gap_bottom-margin-bottom,widget_bar_modules_outside_gap_left-margin-left,widget_bar_modules_outside_gap_right-margin-right', true );
		$form->add_hidden( 'widget_bar_widgets_vertical_gap-margin-bottom-sync', 'widget_bar_widgets_horizontal_left_gap-margin-left---.5,widget_bar_widgets_horizontal_right_gap-margin-right---.5', true );
		$form->add_hidden( 'comments_comment-margin-bottom-sync', 'comments_comment-margin-top---0,comments_last_comment-margin-bottom---0' );
		
		
		$this->_add_style_hidden( $form, 'module-margin-bottom' );
		$this->_add_style_hidden( $form, 'module_sidebar_widgets_horizontal_left_gap-margin-left' );
		$this->_add_style_hidden( $form, 'module_sidebar_widgets_horizontal_right_gap-margin-right' );
		$this->_add_style_hidden( $form, 'widget_bar_modules_outside_gap_left-margin-left' );
		$this->_add_style_hidden( $form, 'widget_bar_modules_outside_gap_bottom-margin-bottom' );
		$this->_add_style_hidden( $form, 'widget_bar_modules_outside_gap_right-margin-right' );
		$this->_add_style_hidden( $form, 'widget_bar_widgets_horizontal_left_gap-margin-left' );
		$this->_add_style_hidden( $form, 'widget_bar_widgets_horizontal_right_gap-margin-right' );
/*		$this->_add_style_hidden( $form, 'module_first-margin-top', '0', true );
		$this->_add_style_hidden( $form, 'module_last-margin-bottom', '0', true );
		$this->_add_style_hidden( $form, 'post-margin-left', '0', true );
		$this->_add_style_hidden( $form, 'post-margin-right', '0', true );
		$this->_add_style_hidden( $form, 'comments-margin-left', '0', true );
		$this->_add_style_hidden( $form, 'comments-margin-right', '0', true );*/
		$this->_add_style_hidden( $form, 'comments_heading-margin-bottom' );
		$this->_add_style_hidden( $form, 'comments_comment-padding' );
		$this->_add_style_hidden( $form, 'comments_comment-margin-top' );
		$this->_add_style_hidden( $form, 'comments_last_comment-margin-bottom' );
/*		$this->_add_style_hidden( $form, 'comments_commentlist-background', 'transparent', true );
		$this->_add_style_hidden( $form, 'builder_module_sidebar-background', 'transparent', true );*/
		
?>
	<div id="dashboard-widgets-wrap">
		<div class="metabox-holder" id="dashboard-widgets">
			<div class="postbox-container" style="width:98%;">
				<?php do_meta_boxes( $this->_var, 'top', '' ); ?>
			</div>
			
			<div class="clear"></div>
			
			<div class="postbox-container" style="width:39%;">
				<?php do_meta_boxes( $this->_var, 'left', '' ); ?>
			</div>
			
			<div class="postbox-container" style="width:59%;">
				<?php do_meta_boxes( $this->_var, 'right', '' ); ?>
			</div>
			
			<div class="clear"></div>
			
			<div class="postbox-container" style="width:98%;">
				<?php do_meta_boxes( $this->_var, 'bottom', '' ); ?>
			</div>
		</div>
		
		<div class="clear"></div>
	</div>
<?php
		
	}
	
	function meta_box_style_site_background() {
		$form = $this->_form;
		
		$this->_add_section_description( 'These options modify the main site background.' );
		
		$this->_add_background_options( 'body', 'Background', true );
	}
	
	function meta_box_style_site_font() {
		$form = $this->_form;
		
?>
	<?php $this->_add_section_description( 'These options modify the base font on the site. These settings can be overridden for specific areas by using the options offered in other sections.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Font Options', true ); ?>
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, 'body' ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, 'html' ); ?></td>
		</tr>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, 'body-color' ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function meta_box_style_links() {
		$this->_add_section_description( 'These options are for global styling of links. These settings can be overridden for specific areas by using the options offered in other sections.' );
		
		$this->_add_basic_link_options( 'links', 'Links', true );
	}
	
	function meta_box_style_container() {
		$form = $this->_form;
		
		$group = 'container';
		
?>
	<?php $this->_add_section_description( 'These options style the container. The container is the wrapper div that contains all the modules.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Padding</th>
			<td><?php $this->_add_padding_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Top Margin</th>
			<td><?php $this->_add_padding_drop_down( $form, $group, 'margin-top', 0, 500 ); ?></td>
		</tr>
		<tr><th scope="row">Bottom Margin</th>
			<td><?php $this->_add_padding_drop_down( $form, $group, 'margin-bottom', 0, 500 ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
<?php
		
	}
	
	function meta_box_style_basic_module_styling() {
		$form = $this->_form;
		
		$group = 'module';
		
?>
	<?php $this->_add_section_description( 'These options set some basic module styling options that apply to all modules. The background and border options can be overridden for specific modules in each modules\' options.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Margin Between Modules</th>
			<td><?php $this->_add_padding_drop_down( $form, 'module', 'margin-top' ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
<?php
		
	}
	
	function meta_box_style_header_module() {
		$form = $this->_form;
		
		$this->_add_basic_module_options( 'header_module', 'header', 'Header' );
		
?>
	<?php $this->_start_show_hide_section( 'Site Title Text', false ); ?>
		<?php $group = 'site_title'; ?>
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, $group, 50, 300, 10, 60 ); ?></td>
		</tr>
		<tr><th scope="row">Text Align</th>
			<td><?php $this->_add_text_align_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_descendants-color" ); ?></td>
		</tr>
		<tr><th scope="row">Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Text Transform</th>
			<td><?php $this->_add_text_transform_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Text Indent</th>
			<td><?php $this->_add_text_indent_drop_down( $form, "{$group}" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_link_hover-color" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_link_hover" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_link_hover" ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_start_show_hide_section( 'Site Tagline Text', false ); ?>
		<?php $group = 'site_tagline'; ?>
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, $group, 50, 300, 10, 60 ); ?></td>
		</tr>
		<tr><th scope="row">Text Align</th>
			<td><?php $this->_add_text_align_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_descendants-color" ); ?></td>
		</tr>
		<tr><th scope="row">Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Text Transform</th>
			<td><?php $this->_add_text_transform_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Text Indent</th>
			<td><?php $this->_add_text_indent_drop_down( $form, "{$group}" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_link_hover-color" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_link_hover" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_link_hover" ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function meta_box_style_content_module() {
		$this->_add_basic_module_options( 'content_module', 'content', 'Content' );
	}
	
	function meta_box_style_footer_module() {
		$this->_add_basic_module_options( 'footer_module', 'footer', 'Footer' );
	}
	
	function meta_box_style_image_module() {
		$this->_add_basic_module_options( 'image_module', 'image', 'Image' );
	}
	
	function meta_box_style_html_module() {
		$this->_add_basic_module_options( 'html_module', 'html', 'HTML' );
	}
	
	function meta_box_style_post_page_content_styling() {
		$form = $this->_form;
		
		$group = 'post';
		
?>
	<?php $this->_add_section_description( 'These options style the page and post content.' ); ?>
	
	<?php $this->_add_basic_text_options( $group ); ?>
	<?php $this->_add_basic_link_options( "{$group}_link" ); ?>
	<?php $this->_add_title_options( 'post_title', 'Post Title', '', true ); ?>
	<?php $this->_add_title_options( 'page_title', 'Page Title', '', true ); ?>
<?php
		
	}
	
	function meta_box_style_comments_styling() {
		$form = $this->_form;
		
?>
	<?php $this->_add_section_title( 'Comments Section' ); ?>
	<?php $this->_add_section_description( 'Styling for the wrapper around all the comments' ); ?>
	
	<?php $group = 'comments'; ?>
	
	<?php $this->_add_basic_heading_options( "{$group}_heading" ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Padding</th>
			<td><?php $this->_add_padding_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Margin Between Comments</th>
			<td><?php $this->_add_padding_drop_down( $form, "{$group}_comment", 'margin-bottom' ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
	
	
	<?php $this->_add_section_title( 'Individual Comments' ); ?>
	<?php $this->_add_section_description( 'These styles are added to each comment.' ); ?>
	
	<?php $group = 'comments_comment'; ?>
	
	<?php $this->_add_basic_text_options( $group ); ?>
	<?php $this->_add_basic_link_options( "{$group}_link" ); ?>
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
<?php
		
	}
	
	function meta_box_style_navigation_module() {
		
?>
	<?php $this->_add_section_title( 'Basic Styling' ); ?>
	<?php $this->_add_section_description( 'These settings style the unhovered appearance of the Navigation Modules.' ); ?>
	
	<?php $this->_add_basic_text_options( 'navigation_text' ); ?>
	<?php $this->_add_background_options( 'navigation_bg' ); ?>
	<?php $this->_add_border_options( 'navigation_border' ); ?>
	
	
	<?php $this->_add_section_title( 'Hover Styling' ); ?>
	<?php $this->_add_section_description( 'These settings style the appearance of the Navigation Modules\' links when hovered over.' ); ?>
	
	<?php $this->_add_basic_text_options( 'navigation_text_hover' ); ?>
	<?php $this->_add_background_options( 'navigation_bg_hover' ); ?>
<?php
		
	}
	
	function meta_box_style_module_sidebars() {
		$form = $this->_form;
		
		$group = 'module_sidebars';
		
?>
	<?php $this->_add_section_title( 'Sidebars' ); ?>
	<?php $this->_add_section_description( 'These options style the background behind the modules in all module sidebars. Note that Widget Bar module sidebars are styled separately in the Widget Bar Modules section.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Margin Between Widgets</th>
			<td><?php $this->_add_padding_drop_down( $form, 'module_sidebar_widgets_vertical_gap', 'margin-bottom' ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	
	
	<?php $group = 'module_sidebar_widgets'; ?>
	
	<?php $this->_add_section_title( 'Widgets' ); ?>
	<?php $this->_add_section_description( 'These options style the widgets in all module sidebars. Note that Widget Bar module widgets are styled separately in the Widget Bar Modules section.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Padding</th>
			<td><?php $this->_add_padding_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_basic_text_options( $group ); ?>
	<?php $this->_add_basic_link_options( "{$group}_link" ); ?>
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
	
	<?php $this->_add_title_options( "{$group}_title", 'Widget Titles', 'These options style each widget\'s title.' ); ?>
<?php
		
	}
	
	function meta_box_style_widget_bar_module() {
		$form = $this->_form;
		
		$group = 'widget_bar_modules';
		
?>
	<?php $this->_add_section_title( 'Module Wrapper' ); ?>
	<?php $this->_add_section_description( 'These options style the Widget Bar module container that holds all the widget areas.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Outside Margin Around Module</th>
			<td><?php $this->_add_padding_drop_down( $form, "{$group}_outside_gap_top", 'margin-top' ); ?></td>
		</tr>
		<tr><th scope="row">Margin Between Widgets</th>
			<td><?php $this->_add_padding_drop_down( $form, 'widget_bar_widgets_vertical_gap', 'margin-bottom' ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
	
	
	<?php $group = 'widget_bar_widgets'; ?>
	
	<?php $this->_add_section_title( 'Widgets' ); ?>
	<?php $this->_add_section_description( 'These options style the widgets in all Widget Bar modules. Note that widgets for other modules are styled separately in the Module Sidebars section.' ); ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Padding</th>
			<td><?php $this->_add_padding_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_basic_text_options( $group ); ?>
	<?php $this->_add_basic_link_options( "{$group}_link" ); ?>
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
	
	<?php $this->_add_title_options( "{$group}_title", 'Widget Titles', 'Thse options style each widget\'s title.' ); ?>
<?php
		
	}
	
	function meta_box_style_sidebars() {
		$form = $this->_form;
		
?>
	<table class="form-table">
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, 'body' ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, 'html' ); ?></td>
		</tr>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, 'body-color' ); ?></td>
		</tr>
	</table>
<?php
		
	}
	
	function meta_box_style_headings() {
		$headings = array(
			'h1'	=> 'H1',
			'h2'	=> 'H2',
			'h3'	=> 'H3',
			'h4'	=> 'H4',
			'h5'	=> 'H5',
			'h6'	=> 'H6',
		);
		
		$this->_add_section_description( 'These styles are mostly-useful for modifying styling of content as different sections allow for styling specific headings. For example, the Post/Page Content Styling section allows for styling both post and page titles, which can override any settings made here.' );
		
		foreach ( (array) $headings as $group => $name )
			$this->_add_title_options( $group, $name, '', true );
	}
	
	function meta_box_style_basic_styling() {
		$form = $this->_form;
		
?>
	<h4 style="padding-left:10px;">Background</h4>
	<div class="form-table-wrapper">
		<table class="form-table">
			<tr><th scope="row">Color</th>
				<td><?php $this->_add_style_text_box( $form, 'basic-background-color' ); ?></td>
			</tr>
			<tr><th scope="row">Image</th>
				<td><?php $this->_add_style_text_box( $form, 'basic-background-color' ); ?></td>
			</tr>
			<tr><th scope="row">Attachment</th>
				<td><?php $this->_add_style_text_box( $form, 'basic-background-color' ); ?></td>
			</tr>
			<tr><th scope="row">Repeat</th>
				<td><?php $this->_add_style_text_box( $form, 'basic-background-color' ); ?></td>
			</tr>
			<tr><th scope="row">Horizontal Positioning</th>
				<td><?php $this->_add_style_text_box( $form, 'basic-background-color' ); ?></td>
			</tr>
			<tr><th scope="row">Vertical Positioning</th>
				<td><?php $this->_add_style_text_box( $form, 'basic-background-color' ); ?></td>
			</tr>
		</table>
	</div>
	
	<h4 style="padding-left:10px;">Font</h4>
	<div class="form-table-wrapper">
		<table class="form-table">
			<tr><th scope="row">Font Family</th>
				<td><?php $this->_add_font_drop_down( $form, 'basic' ); ?></td>
			</tr>
		</table>
	</div>
<?php
		
	}
	
	function meta_box_style_preview() {
		$default_url = get_bloginfo( 'url' );
		$url = apply_filters( 'it_bsm_filter_preview_url', $default_url );
		
		if ( ! preg_match( '|^https?://|', $url ) ) {
			ITUtility::show_error_message( 'The preview URL supplied by the <code>it_bsm_filter_preview_url</code> filter doesn\'t start with a valid protocol (<code>http://</code> or <code>https://</code>). This is likely to cause the preview to fail.' );
		}
		else if ( 0 !== strpos( $url, $default_url ) ) {
			ITUtility::show_error_message( sprintf( 'The preview URL supplied by the <code>it_bsm_filter_preview_url</code> filter doesn\'t point to the current site (<code>%s</code>). This is likely to cause the preview to fail.', $default_url ) );
		}
		
		if ( false !== strpos( $url, '?' ) )
			$url .= '&';
		else
			$url .= '?';
		
		$url .= 'preview=1&builder-render-no-styles=1';
		
		
		$this->_form->add_hidden_no_save( 'builder_preview_url', $url, true );
	}
	
	function meta_box_style_css_preview() {
		$this->_form->add_text_area( 'builder_css_preview', array( 'readonly' => '' ) );
	}
	
	function meta_box_style_custom_css() {
		$this->_form->add_text_area( 'builder_custom_css', array( 'class' => 'handle-tabs' ) );
	}
	
	function _upload_background_image_save() {
		$var = $_POST['upload_background_image'];
		
		
		it_classes_load( 'it-file-utility.php' );
		
		if ( ITFileUtility::file_uploaded( 'image' ) ) {
			$file = ITFileUtility::upload_file( 'image' );
			
			if ( is_wp_error( $file ) )
				$errors[] = "Unable to save uploaded image. Ensure that the web server has permissions to write to the uploads folder.\n\nMessage: " . $file->get_error_message();
		}
		else if ( empty( $_POST['attachment'] ) )
			$errors[] = 'You must upload an image.';
		
		
		if ( ! empty( $errors ) ) {
			foreach ( (array) $errors as $error )
				ITUtility::show_error_message( $error );
			
			$this->_uploaded_background_image();
			
			return;
		}
		
?>
	<script type="text/javascript">
		/* <![CDATA[ */
		var win = window.dialogArguments || opener || parent || top;
		
		win.jQuery("input[name='<?php echo $var; ?>']").val("url('<?php echo $file['url']; ?>')");
		win.jQuery("input[name='<?php echo $var; ?>-backup']").val("url('<?php echo $file['url']; ?>')");
		win.builder_update_style_preview(win.jQuery("input[name='<?php echo $var; ?>']"));
		
		close_thickbox();
		/* ]]> */
	</script>
<?php
		
	}
	
	function _upload_background_image() {
		$form =& new ITForm();
		
		$form->start_form();
		
?>
	<table class='valign-top'>
		<tr id="image-upload-row">
			<td>Upload Image</td>
			<td>
				<?php $form->add_file_upload( 'image' ); ?>
			</td>
		</tr>
	</table>
<?php
		
		echo "<br />\n";
		
		echo "<div style=\"text-align:center;\">\n";
		
		$form->add_submit( 'save', array( 'value' => 'Save', 'class' => 'button-primary save' ) );
		$form->add_submit( 'cancel', array( 'value' => 'Cancel', 'class' => 'button-secondary', 'onclick' => 'var win = window.dialogArguments || opener || parent || top; win.tb_remove(); return false;' ) );
		
		echo "</div>\n";
		
		$form->add_hidden( 'upload_background_image_save', '1' );
		$form->add_hidden( 'upload_background_image', $_REQUEST['upload_background_image'] );
		$form->add_hidden( 'render_clean', '1' );
		
		$form->end_form();
	}
	
	
	// Utility Functions //////////////////////////
	
	function _add_font_family_drop_down( $form, $selector ) {
		$font_families = array(
			''                                                                                                  => '-- Default --',
			'Baskerville, \'Baskerville old face\', \'Hoefler Text\', Garamond, \'Times New Roman\', serif'     => 'Baskerville (serif)',
			'\'Big Caslon\', \'Book Antiqua\', \'Palatino Linotype\', Georgia, serif'                           => 'Big Caslon (serif)',
			'\'Bodoni MT\', Didot, \'Didot LT STD\', \'Hoefler Text\', Garamond, \'Times New Roman\', serif'    => 'Bodoni (serif)',
			'\'Book Antiqua\', Palatino, \'Palatino Linotype\', \'Palatino LT STD\', Georgia, serif'            => 'Book Antiqua (serif)',
			'Cambria, Georgia, serif'                                                                           => 'Cambria (serif)',
			'Candara, Calibri, Segoe, \'Segoe UI\', Optima, Arial, sans-serif'                                  => 'Candara (sans-serif)',
			'Calibri, Candara, Segoe, \'Segoe UI\', Optima, Arial, sans-serif'                                  => 'Calibri (sans-serif)',
			'Constantia, Palatino, \'Palatino Linotype\', \'Palatino LT STD\', Georgia, serif'                  => 'Constantia (serif)',
			'Didot, \'Didot LT STD\', \'Hoefler Text\', Garamond, \'Times New Roman\', serif'                   => 'Didot (serif)',
			'\'Franklin Gothic Medium\', Arial, sans-serif'                                                     => 'Franklin Gothic Medium (sans-serif)',
			'Futura, \'Trebuchet MS\', Arial, sans-serif'                                                       => 'Futura (sans-serif)',
			'Garamond, Baskerville, \'Baskerville Old Face\', \'Hoefler Text\', \'Times New Roman\', serif'     => 'Garamond (serif)',
			'Geneva, Tahoma, Verdana, sans-serif'                                                               => 'Geneva (sans-serif)',
			'\'Gill Sans\', \'Gill Sans MT\', Calibri, sans-serif'                                              => 'Gill Sans (sans-serif)',
			'\'Goudy Old Style\', Garamond, \'Big Caslon\', \'Times New Roman\', serif'                         => 'Goudy Old Style (serif)',
			'\'Helvetica Neue\', \'Liberation Sans\', Arial, sans-serif'                                        => 'Helvetica Neue (sans-serif)',
			'\'Hoefler Text\', \'Baskerville old face\', Garamond, \'Times New Roman\', serif'                  => 'Hoefler Text (serif)',
			'\'Lucida Bright\', Georgia, serif'                                                                 => 'Lucida Bright (serif)',
			'\'Lucida Grande\', \'Lucida Sans Unicode\', \'Lucida Sans\', Geneva, Verdana, sans-serif'          => 'Lucida Grande (sans-serif)',
			'Optima, Segoe, \'Segoe UI\', Candara, Calibri, Arial, sans-serif'                                  => 'Optima (sans-serif)',
			'Palatino, \'Palatino Linotype\', \'Palatino LT STD\', \'Book Antiqua\', Georgia, serif'            => 'Palatino (serif)',
			'Segoe, \'Segoe UI\', \'Helvetica Neue\', Arial, sans-serif'                                        => 'Segoe (sans-serif)',
			'Tahoma, Geneva, Verdana, sans-serif'                                                               => 'Tahoma (sans-serif)',
			'\'Trebuchet MS\', \'Lucida Grande\', \'Lucida Sans Unicode\', \'Lucida Sans\', Tahoma, sans-serif' => 'Trebuchet (sans-serif)',
			'Verdana, Geneva, sans-serif'                                                                       => 'Verdana (sans-serif)',
		);
		$font_families = apply_filters( 'builder_filter_style_manager_general_font_families', $font_families );
		asort( $font_families );
		
		$input = $this->_get_style_drop_down( $form, "$selector-font-family", array( 'value' => $font_families ) );
		
		$input = preg_replace( '/<option value="([^"]+)"/', '<option value="$1" style="font-family:$1"', $input );
		
		echo $input;
	}
	
	function _add_font_size_drop_down( $form, $selector, $start_percent = 50, $stop_percent = 150, $start_pixel = 10, $stop_pixel = 30 ) {
		$font_sizes = array(
			'' => '-- Default --',
		);
		
		for ( $size = $start_percent; $size <= $stop_percent; $size += 12.5 ) {
			$val_size = ( 100 === $size ) ? 100.01 : $size;
			
			$font_sizes["$val_size%"] = "$size%";
		}
		
		for ( $size = $start_pixel; $size <= $stop_pixel; $size++ )
			$font_sizes["{$size}px"] = "{$size}px";
		
		$font_sizes = apply_filters( 'builder_filter_style_manager_general_font_sizes', $font_sizes, $start_percent, $stop_percent, $start_pixel, $stop_pixel );
		
		$this->_add_style_drop_down( $form, "$selector-font-size", array( 'value' => $font_sizes ) );
	}
	
	function _add_font_weight_drop_down( $form, $selector ) {
		$font_weights = array(
			''        => '-- Default --',
			'normal'  => 'Normal',
			'lighter' => 'Lighter',
			'normal'  => 'Normal',
			'bold'    => 'Bold',
			'bolder'  => 'Bolder',
		);
		
		$this->_add_style_drop_down( $form, "$selector-font-weight", array( 'value' => $font_weights ) );
	}
	
	function _add_text_transform_drop_down( $form, $selector ) {
		$font_weights = array(
			''           => '-- Default --',
			'none'       => 'None',
			'capitalize' => 'Capitalize',
			'uppercase'  => 'Uppercase',
			'lowercase'  => 'Lowercase',
		);
		
		$this->_add_style_drop_down( $form, "$selector-text-transform", array( 'value' => $font_weights ) );
	}
	
	function _add_text_indent_drop_down( $form, $selector ) {
		$font_weights = array(
			''      => '-- Default --',
			'10px'  => '10px',
			'20px'  => '20px',
			'30px'  => '30px',
			'40px'  => '40px',
			'50px'  => '50px',
			'60px'  => '60px',
			'70px'  => '70px',
			'80px'  => '80px',
			'90px'  => '90px',
			'100px' => '100px',
			'110px' => '110px',
			'120px' => '120px',
			'130px' => '130px',
			'140px' => '140px',
			'150px' => '150px',
			'160px' => '160px',
			'170px' => '170px',
			'180px' => '180px',
			'190px' => '190px',
			'200px' => '200px',
		);
		
		$this->_add_style_drop_down( $form, "$selector-text-indent", array( 'value' => $font_weights ) );
	}
	
	function _add_text_decoration_drop_down( $form, $selector ) {
		$decorations = array(
			''             => '-- Default --',
			'none'         => 'None',
			'underline'    => 'Underline',
			'overline'     => 'Overline',
			'line-through' => 'Line Through',
		);
		
		$this->_add_style_drop_down( $form, "$selector-text-decoration", array( 'value' => $decorations ) );
	}
	
	function _add_border_style_drop_down( $form, $selector ) {
		$border_styles = array(
			''       => '-- Default --',
			'none'   => 'None',
			'dashed' => 'Dashed',
			'double' => 'Double',
			'groove' => 'Groove',
			'inset'  => 'Inset',
			'outset' => 'Outset',
			'ridge'  => 'Ridge',
			'solid'  => 'Solid',
		);
		
		$this->_add_style_drop_down( $form, "$selector-border-style", array( 'value' => $border_styles ) );
	}
	
	function _add_border_width_drop_down( $form, $selector ) {
		$border_widths = array(
			''       => '-- Default --',
			'thin'   => 'Thin',
			'medium' => 'Medium',
			'thick'  => 'Thick',
		);
		
		for ( $count = 0; $count <= 30; $count++ )
			$border_widths["{$count}px"] = "{$count}px";
		
		$this->_add_style_drop_down( $form, "$selector-border-width", array( 'value' => $border_widths ) );
	}
	
	function _add_text_align_drop_down( $form, $selector ) {
		$options = array(
			''        => '-- Default --',
			'center'  => 'Center',
			'justify' => 'Justify',
			'left'    => 'Left',
			'right'   => 'Right',
		);
		
		$this->_add_style_drop_down( $form, "$selector-text-align", array( 'value' => $options ) );
	}
	
	function _add_padding_drop_down( $form, $selector, $attribute = 'padding', $start = 0, $stop = 30, $step = 1, $unit = 'px' ) {
		$paddings = array(
			'' => '-- Default --',
		);
		
		for ( $count = $start; $count <= $stop; $count += $step )
			$paddings["$count$unit"] = "$count$unit";
		
		$this->_add_style_drop_down( $form, "$selector-$attribute", array( 'value' => $paddings ) );
	}
	
	function _add_background_attachment_drop_down( $form, $selector ) {
		$options = array(
			''       => '-- Default --',
			'scroll' => 'Scroll',
			'fixed'  => 'Fixed',
		);
		
		$this->_add_style_drop_down( $form, "$selector-background-attachment", array( 'value' => $options ) );
	}
	
	function _add_background_repeat_drop_down( $form, $selector ) {
		$options = array(
			''          => '-- Default --',
			'repeat'    => 'Repeat',
			'repeat-x'  => 'Repeat Horizontally',
			'repeat-y'  => 'Repeat Vertically',
			'no-repeat' => 'No Repeat',
		);
		
		$this->_add_style_drop_down( $form, "$selector-background-repeat", array( 'value' => $options ) );
	}
	
	function _add_background_position_drop_down( $form, $selector ) {
		$options = array(
			''              => '-- Default --',
			'top left'      => 'Top Left',
			'top center'    => 'Top Center',
			'top right'     => 'Top Right',
			'center left'   => 'Middle Left',
			'center center' => 'Middle Center',
			'center right'  => 'Middle Right',
			'bottom left'   => 'Bottom Left',
			'bottom center' => 'Bottom Center',
			'bottom right'  => 'Bottom Right',
		);
		
		$this->_add_style_drop_down( $form, "$selector-background-position", array( 'value' => $options ) );
	}
	
	function _add_background_image_upload( $form, $selector ) {
		$options = array(
			''      => '-- Default --',
			'image' => 'Image',
			'none'  => 'None',
		);
		
		$this->_add_to_style_groups( "$selector-background-image" );
		
		$form->add_hidden( "$selector-background-image", array( 'class' => 'styling-option' ) );
		$form->add_hidden( "$selector-background-image-backup" );
		$form->add_drop_down( "$selector-background-image-option", array( 'class' => 'background-image-option', 'value' => $options ) );
		
?>
		<a href="<?php echo $this->_self_link; ?>&upload_background_image=<?php echo $selector; ?>-background-image&render_clean=1&TB_iframe=true&height=200&width=400" class="thickbox <?php echo $selector; ?>-background-image-upload-link" style="display:none;">Upload Image</a>
<?php
		
	}
	
	function _add_style_text_box( $form, $name, $options = array() ) {
		$default_options = array(
			'class' => 'styling-option',
		);
		$options = array_merge( $default_options, $options );
		
		$this->_add_to_style_groups( $name );
		
		$form->add_text_box( $name, $options );
	}
	
	function _add_style_drop_down( $form, $name, $options = array() ) {
		echo $this->_get_style_drop_down( $form, $name, $options );
	}
	
	function _get_style_drop_down( $form, $name, $options = array() ) {
		$default_options = array(
			'class' => 'styling-option',
		);
		$options = array_merge( $default_options, $options );
		
		$this->_add_to_style_groups( $name );
		
		return $form->get_drop_down( $name, $options );
	}
	
	function _add_style_hidden( $form, $name, $options = array(), $force = false ) {
		if ( ! is_array( $options ) )
			$options = array( 'value' => $options );
		
		$default_options = array(
			'class' => 'styling-option',
		);
		$options = array_merge( $default_options, $options );
		
		$this->_add_to_style_groups( $name );
		
		$form->add_hidden( $name, $options, $force );
	}
	
	function _output_js_string_var( $var_name, $string ) {
		echo "var $var_name = ''\n";
		
		foreach ( (array) explode( "\n", $string ) as $line )
			echo "$var_name += '" . addslashes( $line ) . "';\n";
	}
	
	function _add_to_style_groups( $name ) {
		if ( ! preg_match( "/^([^-]+)-/", $name, $matches ) )
			return;
		
		$this->_style_groups[$matches[1]][] = $name;
	}
	
	function _start_show_hide_section( $description, $show = false ) {
		if ( false === $show ) {
			$link_text = '(Show Options)';
			$class = 'show-hide-target hide';
		}
		else {
			$link_text = '(Hide Options)';
			$class = 'show-hide-target';
		}
		
?>
	<div>
		<p class="css-options-heading"><?php echo $description; ?></p>
		<p class="show-hide-wrapper"><a href="#"><?php echo $link_text; ?></a></p>
		
		<table class="form-table <?php echo $class; ?>">
<?php
		
	}
	
	function _end_show_hide_section() {
		
?>
		</table>
	</div>
<?php
		
	}
	
	function _add_basic_text_options( $group, $name = 'Text', $show = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_start_show_hide_section( $name, $show ); ?>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "$group-color" ); ?></td>
		</tr>
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Text Align</th>
			<td><?php $this->_add_text_align_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function _add_basic_link_options( $group, $name = 'Links', $show = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_start_show_hide_section( $name, $show ); ?>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "$group-color" ); ?></td>
		</tr>
		<tr><th scope="row">Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Hover Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_hover-color" ); ?></td>
		</tr>
		<tr><th scope="row">Hover Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_hover" ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function _add_background_options( $group, $name = 'Background', $show = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_start_show_hide_section( $name, $show ); ?>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "$group-background-color" ); ?></td>
		</tr>
		<tr><th scope="row">Image</th>
			<td><?php $this->_add_background_image_upload( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Attachment</th>
			<td><?php $this->_add_background_attachment_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Repeat</th>
			<td><?php $this->_add_background_repeat_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Positioning</th>
			<td><?php $this->_add_background_position_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function _add_border_options( $group, $name = 'Borders', $show = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_start_show_hide_section( $name, $show ); ?>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "$group-border-color" ); ?></td>
		</tr>
		<tr><th scope="row">Width</th>
			<td><?php $this->_add_border_width_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Style</th>
			<td><?php $this->_add_border_style_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function _add_title_options( $group, $name = 'Title', $description = 'These options style the title.', $advanced_options = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_add_section_title( $name ); ?>
	<?php $this->_add_section_description( $description ); ?>
	
	<?php
		if ( false === $advanced_options )
			$this->_add_basic_heading_options( $group );
		else
			$this->_add_advanced_heading_options( $group );
	?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Side Margins</th>
			<td><?php $this->_add_padding_drop_down( $form, $group, 'margin-left', -30, 50 ); ?></td>
		</tr>
		<tr><th scope="row">Top Margin</th>
			<td><?php $this->_add_padding_drop_down( $form, $group, 'margin-top', -30, 50 ); ?></td>
		</tr>
		<tr><th scope="row">Bottom Margin</th>
			<td><?php $this->_add_padding_drop_down( $form, $group, 'margin-bottom', -30, 50 ); ?></td>
		</tr>
		<tr><th scope="row">Padding</th>
			<td><?php $this->_add_padding_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
<?php
		
		echo "<div>\n";
		$form->add_hidden( "$group-margin-left-sync", "$group-margin-right" );
		$this->_add_style_hidden( $form, "$group-margin-right" );
		echo "</div>\n";
	}
	
	function _add_basic_module_options( $group, $var, $name ) {
		$form = $this->_form;
		
?>
	<?php if ( 'content' == $var ) : ?>
		<?php $this->_add_section_description( "These options control the styling of the $name Module. The post and page content can be styled in the Post/Page Content Styling section. Comments can be styled in the Comments Styling section." ); ?>
	<?php else : ?>
		<?php $this->_add_section_description( "These options control the styling of the $name Module." ); ?>
	<?php endif; ?>
	
	<?php $this->_start_show_hide_section( 'Spacing' ); ?>
		<tr><th scope="row">Margin Between <?php echo $name; ?> and Sidebars</th>
			<td><?php $this->_add_padding_drop_down( $form, "{$group}_element_inner_left_gap", 'margin-left' ); ?></td>
		</tr>
		<tr><th scope="row">Outside Margin Around Module</th>
			<td><?php $this->_add_padding_drop_down( $form, "{$group}_outside_gap_top", 'margin-top' ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
	
	<?php $this->_add_background_options( $group ); ?>
	<?php $this->_add_border_options( $group ); ?>
<?php
		
		$form->add_hidden( "{$group}-selector", ".builder-module.builder-module-$var", true );
		
		$form->add_hidden( "{$group}_element_inner_left_gap-selector", ".builder-module-$var .middle .builder-module-element,.builder-module-$var .right .builder-module-element", true );
		$form->add_hidden( "{$group}_element_inner_right_gap-selector", ".builder-module-$var .middle .builder-module-element,.builder-module-$var .left .builder-module-element", true );
		
		if ( builder_theme_supports( 'builder-3.0' ) ) {
			$form->add_hidden( "{$group}_outside_gap_top-selector", ".builder-module-$var .builder-module-block,.builder-module-$var .builder-module-block .hentry:first-child", true );
			$form->add_hidden( "{$group}_outside_gap_bottom-selector", ".builder-module-$var .builder-module-block,.builder-module-$var .builder-module-block .hentry:last-child", true );
		}
		else {
			$form->add_hidden( "{$group}_outside_gap_top-selector", ".builder-module-$var .builder-module-block,.builder-module-$var .builder-module-block .post:first-child", true );
			$form->add_hidden( "{$group}_outside_gap_bottom-selector", ".builder-module-$var .builder-module-block,.builder-module-$var .builder-module-block .post:last-child", true );
		}
		
		
		$form->add_hidden( "{$group}_outside_gap_left-selector", ".builder-module-$var .left .builder-module-element,.builder-module-$var .single .builder-module-element,.builder-module-$var .left .widget-wrapper-single .widget,.builder-module-$var .left .widget-wrapper-left .widget", true );
		$form->add_hidden( "{$group}_outside_gap_right-selector", ".builder-module-$var .right .builder-module-element,.builder-module-$var .single .builder-module-element,.builder-module-$var .right .widget-wrapper-single .widget,.builder-module-$var .right .widget-wrapper-right .widget", true );
		
		$form->add_hidden( "{$group}_element_inner_left_gap-margin-left-sync", "{$group}_element_inner_right_gap-margin-right", true );
		$form->add_hidden( "{$group}_outside_gap_top-margin-top-sync", "{$group}_outside_gap_right-margin-right,{$group}_outside_gap_bottom-margin-bottom,{$group}_outside_gap_left-margin-left", true );
		
//		$this->_add_style_hidden( $form, "{$group}_element_inner_right_gap-margin-right", '0', true );
		$this->_add_style_hidden( $form, "{$group}_outside_gap_bottom-margin-bottom" );
		$this->_add_style_hidden( $form, "{$group}_outside_gap_left-margin-left" );
		$this->_add_style_hidden( $form, "{$group}_outside_gap_right-margin-right" );
	}
	
	function _add_basic_heading_options( $group, $name = 'Text', $show = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_start_show_hide_section( $name, $show ); ?>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "$group-color" ); ?></td>
		</tr>
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, $group, 50, 300, 10, 60 ); ?></td>
		</tr>
		<tr><th scope="row">Text Align</th>
			<td><?php $this->_add_text_align_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, $group ); ?></td>
		</tr>
		<tr><th scope="row">Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, $group ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function _add_advanced_heading_options( $group, $name = 'Text', $show = false ) {
		$form = $this->_form;
		
?>
	<?php $this->_start_show_hide_section( $name, $show ); ?>
		<tr><th scope="row">Font Family</th>
			<td><?php $this->_add_font_family_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Size</th>
			<td><?php $this->_add_font_size_drop_down( $form, $group, 50, 300, 10, 60 ); ?></td>
		</tr>
		<tr><th scope="row">Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_descendants-color" ); ?></td>
		</tr>
		<tr><th scope="row">Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Text Transform</th>
			<td><?php $this->_add_text_transform_drop_down( $form, "{$group}_descendants" ); ?></td>
		</tr>
		<tr><th scope="row">Link Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_link-color" ); ?></td>
		</tr>
		<tr><th scope="row">Link Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_link" ); ?></td>
		</tr>
		<tr><th scope="row">Link Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_link" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Color</th>
			<td><?php $this->_add_style_text_box( $form, "{$group}_link_hover-color" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Font Weight</th>
			<td><?php $this->_add_font_weight_drop_down( $form, "{$group}_link_hover" ); ?></td>
		</tr>
		<tr><th scope="row">Link Hover Decoration</th>
			<td><?php $this->_add_text_decoration_drop_down( $form, "{$group}_link_hover" ); ?></td>
		</tr>
	<?php $this->_end_show_hide_section(); ?>
<?php
		
	}
	
	function _add_section_title( $title ) {
		echo "<h4>$title</h4>\n";
	}
	
	function _add_section_description( $description ) {
		echo "<p class='css-options-section-description'>$description</p>\n";
	}
}

new BuilderStyleManager();

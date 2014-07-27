<?php
    /*  Copyright 2010 Jens Karembo

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License, version 2, as 
        published by the Free Software Foundation.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    */  
?>
<?php
add_action('admin_init', 'simplevisitorcounter_init');
add_action('admin_menu', 'simplevisitorcounter_menu');
                                      
function simplevisitorcounter_init() {
		register_setting('wp-better-categories-group', 'simplevisitorcounter_exclude_ips',  'iplist');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_display_footer', 'intval');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_display_credit', 'intval');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_style');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_data', 'intval');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_align');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_count_admin', 'intval');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_count_404', 'intval');
    register_setting('wp-better-categories-group', 'simplevisitorcounter_pad_zeros', 'intval'); 
    register_setting('wp-better-categories-group', 'simplevisitorcounter_count_only_unique', 'intval');
}

function iplist($list) {
	// validate a line-delimited list of IPs
	$ips = split("\n",$list);
	
	foreach($ips as $ip) {
		if($ip=="") {
			continue;
		}
		
		if(preg_match("/(\b\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\b)/",$ip)) {
			$return_list[] = $ip;
		}
	}

	if(is_array($return_list)) {	
		return join("\n",$return_list);
	} else {
		return true;
	}
}

function file_array($path, $exclude = ".|..|.svn|.DS_Store", $recursive = true) {
    $path = rtrim($path, "/") . "/";
    $folder_handle = opendir($path) or die("Eof");
    $exclude_array = explode("|", $exclude);
    $result = array();
    while(false !== ($filename = readdir($folder_handle))) {
        if(!in_array(strtolower($filename), $exclude_array)) {
            if(is_dir($path . $filename . "/")) {
                // Need to include full "path" or it's an infinite loop
                if($recursive) $result[] = file_array($path . $filename . "/", $exclude, true);
            } else {
                if ($filename === '0.gif') {
                    if (!$done[$path]) {
                        $result[] = $path;
                        $done[$path] = 1;
                    }
                }
            }
        }
    }
    return $result;
}

function simplevisitorcounter_menu() {
  add_options_page('Simple Visitor Counter Options', 'Visitor Counter', 'administrator', 'wp-better-categories', 'simplevisitorcounter_options');
}

function simplevisitorcounter_options() {
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"><br></div>
    <h2>Visitor Counter Settings</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'wp-better-categories-group' ); ?>
        <?php
            $data = file_array(WP_CONTENT_DIR . '/plugins/wp-better-categories/styles/');
            foreach ($data as $parent_folder => $records) {
                foreach ($records as $style_folder => $style_records) {
                    foreach ($style_records as $style => $test) {
                        preg_match('/styles\/(.*?)\/(.*?)\//', $test, $match);
                        $groups[$match[1]][] = $match[2];
                    }
                }
            }
        ?>
        <h3>Basic Settings</h3>
        <table class="form-table">
            <tr valign="top">
            <th scope="row">Set visitor counter to</th>
            <td><input type="text" name="simplevisitorcounter_data" value="<?php echo get_option('simplevisitorcounter_data') ?>" /></td>
            </tr>
            <tr valign="top">
            <th scope="row">Exclude IP addresses from stats (one per line)</th>
            <td><textarea name="simplevisitorcounter_exclude_ips" rows="4" cols="20"><?php echo get_option('simplevisitorcounter_exclude_ips') ?></textarea></td>
            </tr>
            <tr valign="top">
            <th scope="row">Display Visitor Counter in Footer</th>
            <td><input type="checkbox" name="simplevisitorcounter_display_footer" value="1" <?php echo checked('1', get_option('simplevisitorcounter_display_footer')) ?> /></td>
            <tr valign="top">
            <th scope="row">Counter Alignment (left, right, center, none)?</th>
            <td>
                <select name="simplevisitorcounter_align">
                    <option value="">None</option>
                    <option <?php if (get_option('simplevisitorcounter_align')==='left') { echo 'selected'; }?>>left</option>
                    <option <?php if (get_option('simplevisitorcounter_align')==='center') { echo 'selected'; } ?>>center</option>
                    <option <?php if (get_option('simplevisitorcounter_align')==='right') { echo 'selected'; } ?>>right</option>
                </select>
            </td>
            </tr>
            <tr valign="top">
            <th scope="row">Count visits from wp-admin?</th>
            <td><input type="checkbox" name="simplevisitorcounter_count_admin" value="1" <?php echo checked('1', get_option('simplevisitorcounter_count_admin')) ?> /></td>
            </tr>
            <tr valign="top">
            <th scope="row">Count 404 pages as visits?</th>
            <td><input type="checkbox" name="simplevisitorcounter_count_404" value="1" <?php echo checked('1', get_option('simplevisitorcounter_count_404')) ?> /></td>
            </tr>
            <tr valign="top">
            <th scope="row">Pad with zeros? ('000281' rather than default '281')</th>
            <td><input type="checkbox" name="simplevisitorcounter_pad_zeros" value="1" <?php echo checked('1', get_option('simplevisitorcounter_pad_zeros')) ?> /></td>
            </tr>
            <tr valign="top">
            <th scope="row">Count only unique visitors? (resets every 24h)</th>
            <td><input type="checkbox" name="simplevisitorcounter_count_only_unique" value="1" <?php echo checked('1', get_option('simplevisitorcounter_count_only_unique')) ?> /></td>
            </tr>
        </table>
        <br/><br/>
        <h3>Counter Styles</h3>
        <?php
            foreach ($groups as $style_name => $style) {
?>
<h3>Style: <?php echo $style_name; ?></h3>
<table class="form-table">
    <?php
                foreach ($style as $name) {
                    ?>
                	<tr>
                		<td>
                		<input type="radio" id="img1" name="simplevisitorcounter_style" value="<?php echo $style_name . '/' . $name; ?>" <?php echo checked($style_name . '/' . $name, get_option('simplevisitorcounter_style')) ?> />
                		<img src='<?php echo WP_PLUGIN_URL?>/wp-better-categories/styles/<?php echo $style_name . '/' . $name . '/'; ?>0.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/wp-better-categories/styles/<?php echo $style_name . '/' . $name . '/'; ?>1.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/wp-better-categories/styles/<?php echo $style_name . '/' . $name . '/'; ?>2.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/wp-better-categories/styles/<?php echo $style_name . '/' . $name . '/'; ?>3.gif'>
                		<img src='<?php echo WP_PLUGIN_URL?>/wp-better-categories/styles/<?php echo $style_name . '/' . $name . '/'; ?>4.gif'>
                		</td>
                	</tr>
                    <?php
                }
    ?>
</table>
<hr/>
<?php
            }
        ?>
        <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    </div>
    <?php
}
?>

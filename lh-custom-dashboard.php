<?php
/*
Plugin Name: LH Custom Dashboard
Plugin URI: http://lhero.org/plugins/lh-custom-dashboard/
Description: Configurable customisation of your wp dashboard
Version: 1.0
Author: Peter Shaw
Author URI: http://shawfactor.com
License: GPL
*/

/*  Copyright 2014  Peter Shaw  (email : pete@localhero.biz)

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

define ( 'LH_CUSTOM_DASHBOARD_PLUGIN_URL', plugin_dir_url(__FILE__)); // with forward slash (/).

class LH_custom_dashboard_plugin {

var $options;
var $opt_name = 'lh_custom_dashboard_options';

function plugin_menu() {
add_options_page('LH Custom Dashboard Options', 'LH Custom Dashboard', 'manage_options', $this->filename, array($this,"plugin_options"));
}


function plugin_options() {

if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

    // variables for the field and option names 
    	
	$lh_custom_dashboard_admin_footer_text_field_name = 'lh_custom_dashboard_admin_footer_text';
	$lh_custom_dashboard_update_footer_text_field_name = 'lh_custom_dashboard_update_footer_text';
	$lh_custom_dashboard_favicon_field_name = 'lh_custom_dashboard-favicon';

    $lh_custom_dashboard_opt_name = 'lh_custom_dashboard_options';
    $lh_custom_dashboard_hidden_field_name = 'lh_custom_dashboard_submit_hidden';

 // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[   $lh_custom_dashboard_hidden_field_name ]) && $_POST[   $lh_custom_dashboard_hidden_field_name ] == 'Y' ) {
        // Read their posted value


$lh_custom_dashboard_options[$lh_custom_dashboard_admin_footer_text_field_name] = sanitize_text_field($_POST[ $lh_custom_dashboard_admin_footer_text_field_name ]);

$lh_custom_dashboard_options[$lh_custom_dashboard_update_footer_text_field_name] = sanitize_text_field($_POST[ $lh_custom_dashboard_update_footer_text_field_name ]);

if (get_post_status( sanitize_text_field($_POST[ $lh_custom_dashboard_favicon_field_name ]) ) != FALSE ) {

$lh_custom_dashboard_options[$lh_custom_dashboard_favicon_field_name] = sanitize_text_field($_POST[ $lh_custom_dashboard_favicon_field_name ]);

}

        // Save the posted value in the database
	update_option( $this->opt_name, $lh_custom_dashboard_options );


$this->options = get_option($this->opt_name);

        // Put an settings updated message on the screen



?>
<div class="updated"><p><strong><?php _e('Options saved', 'menu-test' ); ?></strong></p></div>
<?php

    } 

// Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __('LH Custom Dashboard', 'menu-test' ) . "</h2>";

    // settings form
    
    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $lh_custom_dashboard_hidden_field_name; ?>" value="Y">

<p><?php _e("Favicon url:", 'lh-custom-dashboard'); ?> 
<input type="hidden" name="<?php echo $lh_custom_dashboard_favicon_field_name; ?>"  id="<?php echo $lh_custom_dashboard_favicon_field_name; ?>" value="<?php echo $this->options[$lh_custom_dashboard_favicon_field_name]; ?>" size="10" />
<input type="url" name="<?php echo $lh_custom_dashboard_favicon_field_name; ?>-url" id="<?php echo $lh_custom_dashboard_favicon_field_name; ?>-url" value="<?php echo wp_get_attachment_url($this->options[$lh_custom_dashboard_favicon_field_name]); ?>" size="50" />
<input type="button" class="button" name="<?php echo $lh_custom_dashboard_favicon_field_name; ?>-upload_button" id="<?php echo $lh_custom_dashboard_favicon_field_name; ?>-upload_button" value="Upload/Select Image" />
</p>


<p><?php _e("Admin Footer text (left side);", 'lh-custom-dashboard' ); ?> 
<input type="text" name="<?php echo $lh_custom_dashboard_admin_footer_text_field_name; ?>" value="<?php echo $this->options[$lh_custom_dashboard_admin_footer_text_field_name]; ?>" size="50" />
</p>

<p><?php _e("Update Footer text (right side);", 'lh-custom-dashboard' ); ?> 
<input type="text" name="<?php echo $lh_custom_dashboard_update_footer_text_field_name; ?>" value="<?php 
echo $this->options[$lh_custom_dashboard_update_footer_text_field_name]; ?>" size="50">
</p>


<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>



</div>



<?php


	

}

// Prepare the media uploader
public function add_admin_scripts(){

if (isset($_GET['page']) && $_GET['page'] == $this->filename) {
	// must be running 3.5+ to use color pickers and image upload
	wp_enqueue_media();
wp_register_script('lh-custom-dashboard-admin', LH_CUSTOM_DASHBOARD_PLUGIN_URL.'scripts/uploader.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('lh-custom-dashboard-admin');

}
}



public function override_left_admin_footer_text_output($er_left) {

return $this->options['lh_custom_dashboard_admin_footer_text'];

}

function override_right_admin_footer_text_output($er_right) {

return $this->options['lh_custom_dashboard_update_footer_text'];


}

function hide_update_notice_to_all_but_admin_users() {
    if (!current_user_can('update_core')) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}

function override_dashboard_adminbar_icon(){


        echo " \n\n <style type=\"text/css\">#wp-admin-bar-wp-logo { display:none; } #wpadminbar #wp-admin-bar-site-name > .ab-item:before { content: normal;}</style> \n\n";

if ($this->options['lh_custom_dashboard-favicon']){


        echo '<script type="text/javascript"> jQuery(document).ready(function(){ ';
        echo  'jQuery("#wp-admin-bar-root-default").prepend(" <li id=\"wlcms_admin_logo\"> <span style=\"float:left;height:28px;line-height:28px;vertical-align:middle;text-align:center;width:28px\"><img src=\"'.wp_get_attachment_url($this->options['lh_custom_dashboard-favicon']).'\" width=\"16\" height=\"16\" alt=\"Login\" style=\"height:16px;width:16px;vertical-align:middle\" /> </span> </li> "); ';
		echo '  }); ';
        echo '</script> ';

}

  
}

// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){
		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';
	}
	return $links;
}


function __construct() {

$this->options = get_option($this->opt_name);

$this->filename = plugin_basename( __FILE__ );

add_filter('admin_footer_text', array($this,"override_left_admin_footer_text_output"),11); //left side

add_filter('update_footer', array($this,"override_right_admin_footer_text_output"),11); //right side

add_action( 'admin_head', array($this,"hide_update_notice_to_all_but_admin_users"));

add_action('admin_menu', array($this,"plugin_menu"));

add_action('admin_enqueue_scripts', array($this,"add_admin_scripts"));

add_action('wp_before_admin_bar_render', array($this,"override_dashboard_adminbar_icon"));

add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);

}

}

$lh_custom_dashboard = new LH_custom_dashboard_plugin();


?>
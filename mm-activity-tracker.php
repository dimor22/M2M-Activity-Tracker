<?php
/*
Plugin Name:  M2M Activity Tracker
Plugin URI:   http://dlopez.xyz/plugins/m2m-activity-tracker
Description:  Simple wordpress plugin that keeps track of the activity between ward members and ward missionaries
Version:      1.0
Author:       David Lopez Gamez
Author URI:   http://dlopez.xyz
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/



/** Step 2 (from text above). */
add_action( 'admin_menu', 'my_plugin_menu' );

/** Step 1. */
function my_plugin_menu() {
    add_options_page( 'My Plugin Options', 'My Plugin', 'manage_options', 'my-unique-identifier', 'my_plugin_options' );
}

/** Step 3. */
function my_plugin_options() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo '<div class="wrap">';
    echo '<p>Here is where the form would go if I actually had options.</p>';
    echo '</div>';
}

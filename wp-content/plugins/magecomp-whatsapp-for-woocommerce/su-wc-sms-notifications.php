<?php
/*
Plugin Name: WooCommerce Whatsapp Notifications
Version: 2.0.2
Plugin URI: http://magecomp.com
Description: Sends Whatsapp notifications to your clients for order status changes. You can also receive an Whatsapp message when a new order is received.
Author URI: http://Magecomp.com
Author: Magecomp
Requires at least: 3.8
Tested up to: 5.8
*/

//Exit if accessed directly
if (!defined('ABSPATH')) {
    exit();
}

global $wpdb, $waapico_db_version, $waapico_db_table;
$waapico_db_version = 1.73;
$waapico_db_table = $wpdb->prefix . 'waapico_cart_notifications';

//Define text domain
$waapico_plugin_name = 'WooCommerce Whatsapp Notification';
$waapico_plugin_file = plugin_basename(__FILE__);
$waapico_plugin_domn = 'waapico';
load_plugin_textdomain($waapico_plugin_domn, false, dirname($waapico_plugin_file) . '/languages');

//Add links to plugin listing
add_filter("plugin_action_links_$waapico_plugin_file", 'waapico_add_action_links');
function waapico_add_action_links($links)
{
    global $waapico_plugin_domn;
    $links[] = '<a href="' . admin_url("admin.php?page=$waapico_plugin_domn") . '">Settings</a>';
    $links[] = '<a href="http://magecomp.com" target="_blank">Plugin Documentation</a>';
    return $links;
}

//Add links to plugin settings page
add_filter('plugin_row_meta', "waapico_plugin_row_meta", 10, 2);
function waapico_plugin_row_meta($links, $file)
{
    global $waapico_plugin_file;
    if (strpos($file, $waapico_plugin_file) !== false) {
        $links[] = '<a href="http://wa.magecomp.com" target="_blank">Get Credentials</a>';
        $links[] = '<a href="http://magecomp.com" target="_blank">Plugin Documentation</a>';
    }
    return $links;
}

//WooCommerce is required for the plugin to work
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    include('plugin-core.php');
} else {
    add_action('admin_notices', 'waapico_require_wc');
    function waapico_require_wc()
    {
        global $waapico_plugin_name, $waapico_plugin_domn;
        echo '<div class="error fade" id="message"><h3>' . $waapico_plugin_name . '</h3><h4>' . __("This plugin requires WooCommerce", $waapico_plugin_domn) . '</h4></div>';
        deactivate_plugins($waapico_plugin_file);
    }
}

//Handle uninstallation
register_uninstall_hook(__FILE__, 'waapico_uninstaller');
function waapico_uninstaller()
{
    delete_option('waapico_settings');
}

//Create table on activation / update
register_activation_hook(__FILE__, 'waapico_db_install');
function waapico_db_install()
{
    global $wpdb, $waapico_db_version, $waapico_db_table;
    if ($waapico_db_version == get_option('waapico_db_version')) {
        return;
    }

    //Use WP built-in functionality to create tables
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $charset_collate = $wpdb->get_charset_collate();

    //Create required tables
    $sql = "CREATE TABLE $waapico_db_table (
        billing_phone varchar(15) NOT NULL,
        first_name varchar(255) NOT NULL,
        order_id bigint(20) unsigned NOT NULL DEFAULT 0,
        register_ts datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        msg_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_1_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_2_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_3_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_4_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_5_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_6_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_7_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_8_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_9_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
        reminder_10_sent tinyint(1) unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (billing_phone),
        KEY resiter_ts_key (register_ts)
	) $charset_collate;";
    dbDelta($sql);

    //Update the db version
    update_option('waapico_db_version', $waapico_db_version);
}

//Create a WP-Cron interval of 5 minutes
add_filter( 'cron_schedules', 'waapico_add_cron_interval' );
function waapico_add_cron_interval( $schedules ) { 
    if ( empty( $schedules['five_minutes'] ) ) {
        $schedules['five_minutes'] = array(
            'interval' => 5 * 60,
            'display'  => esc_html__( 'Every Five Minutes' ),
        );
    }
    return $schedules;
}

//Schedule the hook
register_activation_hook(__FILE__, 'waapico_schedule_cron');
function waapico_schedule_cron() {
    if ( ! wp_next_scheduled( 'waapico_cron_hook' ) ) {
        $res = wp_schedule_event( time(), 'five_minutes', 'waapico_cron_hook' );
        if ( ! $res ) wp_die( 'Failed to schedule waapico_cron_hook' );
    }
}

//Disable cron on plugin deactivation
register_deactivation_hook( __FILE__, 'waapico_cron_deactivate' ); 
function waapico_cron_deactivate() {
    if ( wp_next_scheduled( 'waapico_cron_hook' ) ) {
        $timestamp = wp_next_scheduled( 'waapico_cron_hook' );
        wp_unschedule_event( $timestamp, 'waapico_cron_hook' );
    }
}

//Apply activation functions for upgrades as well
add_action( 'upgrader_process_complete', 'waapico_post_upgrade', 10, 2 );
function waapico_post_upgrade( $_, $options ) {
    global $waapico_plugin_file;
    if ( 'update' == $options['action'] && 'plugin' == $options['type'] ) {
        foreach ( $options['plugins'] as $plugin ) {
            if ( $plugin == $waapico_plugin_file ) {
                waapico_db_install();
                waapico_schedule_cron();
            }
        }        
    }
}

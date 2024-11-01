<?php
/*
Plugin Name: TPC Memory Usage (Updated)
Plugin URI: http://wordpress.org/plugins/tpc-memory-usage-updated/
Description: Updated by Imperative Ideas (@imperativeideas) for WP 3.8. Used to keep track of WordPress memory usage, and system information.
Version: 0.9.3
Author: Chris Strosser (Original)
Author URI: http://webjawns.com/
*/

/**
 * TPC! Memory Usage version
 * 
 * @var string
 */
define('TPCMEM_VERSION', '0.9.3');

/**
 * TPC! Memory Usage path
 * 
 * @var string
 */
define('TPCMEM_PATH', dirname(__FILE__) . '/');

/**
 * TPC! Memory Usage basename
 * 
 * @var string
 */
define('TPCMEM_FOLDER', plugin_basename(dirname(__FILE__)));

/**
 * TPC! Memory Usage Plugin Path & URL
 */
define('TPCMEM_PLUGIN_PATH', plugin_dir_path( __FILE__ ));
define('TPCMEM_PLUGIN_URL', plugin_dir_url( __FILE__ ));


/**
 * Whether or not to log memory usage to file.
 * 
 * This should not be used on a busy production server as it could 
 * slow things down considerably. This constant can be defined in 
 * wp-config.php.
 * 
 * @var bool
 */
if (!defined('TPCMEM_LOGGING'))
	define('TPCMEM_LOGGING', get_option('tpc_memory_usage_logging'));

if (!defined('TPCMEM_LOGGING_TYPE'))
	define('TPCMEM_LOGGING_TYPE', get_option('tpc_memory_usage_logging_type'));

/**
 * TPC! Memory Usage log file
 * 
 * @var string
 */
define('TPCMEM_LOG', TPCMEM_PATH . 'logs/tpcmem.log');

/**
 * Checkpoints and log table names
 * 
 * @global $wpdb
 * @var string
 */
global $wpdb;
define('TPCMEM_DB_CHECKPOINTS', $wpdb->prefix . 'tpcmem_checkpoints');
define('TPCMEM_DB_LOG', $wpdb->prefix . 'tpcmem_log');

set_include_path(implode(PATH_SEPARATOR, array(
	TPCMEM_PATH . 'library',
	get_include_path(),
)));

/** Load TPC! Memory Usage Administration API */
require_once('tpcmem-admin.php');

/** Load TPC! Memory Usage Checkpoints API */
require_once('tpcmem-checkpoints.php');

/** Load TPC! Memory Usage Core API */
require_once('tpcmem-core.php');

/** Load TPC! Memory Usage Formatting API */
require_once('tpcmem-formatting.php');

/** Load TPC! Memory Usage Log API */
require_once('tpcmem-log.php');

/** Load TPC! Memory Usage Security API */
require_once('tpcmem-security.php');

/** Load TPC! Memory Usage Template API */
require_once('tpcmem-template.php');

// Register activation/deactivation functions
register_activation_hook(__FILE__, 'tpcmem_activate');
register_deactivation_hook(__FILE__, 'tpcmem_deactivate');

function tcp_enqueue_scripts() {
    if (isset($_GET['page'])) {
        switch ($_GET['page']) {
            case 'tpc-memory-usage':
                wp_enqueue_script('tpcmem-overview');

            case 'tpcmem-checkpoint':
                wp_enqueue_style('tpcmem');
                break;

            case 'tpcmem-checkpoint-manager':
                wp_enqueue_script('tpcmem');
                break;

            case 'tpcmem-reports':
                wp_enqueue_style('tpcmem');
                wp_enqueue_script('tpcmem-reports');
                break;

            default:
                break;
        }
    }
    else {
        wp_enqueue_style('tpcmem');
    }
}

function tcp_register_scripts() {
    // Register scripts and styles
    wp_register_script('tpcmem', TPCMEM_PLUGIN_URL . 'js/tpcmem.js', array('jquery'), TPCMEM_VERSION, true);
    wp_register_script('tpcmem-overview', TPCMEM_PLUGIN_URL . 'js/overview.js', array('jquery-ui-tabs'), TPCMEM_VERSION, true);
    wp_register_script('tpcmem-tablesorter',TPCMEM_PLUGIN_URL . 'js/jquery.tablesorter.min.js', array('jquery'), '2.0.3', true);
    wp_register_script('tpcmem-reports', TPCMEM_PLUGIN_URL . 'js/reports.js', array('tpcmem-tablesorter'), TPCMEM_VERSION, true);
    wp_register_style('tpcmem', TPCMEM_PLUGIN_URL . 'css/tpcmem.css', array(), TPCMEM_VERSION);
}


// Add default actions
add_action('plugins_loaded', 'tpcmem_init');
add_action('admin_menu', 'tpcmem_admin_menu');
add_action('admin_init', 'tpcmem_admin_init');
add_action('admin_enqueue_scripts', 'tcp_register_scripts');
add_action('admin_enqueue_scripts', 'tcp_enqueue_scripts');
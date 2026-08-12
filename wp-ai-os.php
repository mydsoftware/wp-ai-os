<?php
/**
 * Plugin Name: WP AI OS
 * Plugin URI: https://example.com/
 * Description: AI Readiness, GEO, AEO, RAG, Agents, WooCommerce and AI infrastructure for WordPress.
 * Version: 1.0.0
 * Author: WP AI OS
 * Text Domain: wp-ai-os
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.0
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'WP_AI_OS_VERSION', '1.0.0' );
define( 'WP_AI_OS_FILE', __FILE__ );
define( 'WP_AI_OS_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_AI_OS_URL', plugin_dir_url( __FILE__ ) );

require_once WP_AI_OS_PATH . 'admin/class-admin.php';
require_once WP_AI_OS_PATH . 'api/class-api-controller.php';
require_once WP_AI_OS_PATH . 'core/class-public-ai-files.php';
require_once WP_AI_OS_PATH . 'core/class-schema-engine.php';
require_once WP_AI_OS_PATH . 'core/class-license-manager.php';
require_once WP_AI_OS_PATH . 'database/class-schema.php';
require_once WP_AI_OS_PATH . 'ai/agents/class-agent-scheduler.php';
require_once WP_AI_OS_PATH . 'integrations/woocommerce/class-woocommerce.php';

add_action( 'plugins_loaded', static function () {
	load_plugin_textdomain( 'wp-ai-os', false, dirname( plugin_basename( WP_AI_OS_FILE ) ) . '/languages' );
	new WP_AI_OS_API_Controller();
	new WP_AI_OS_Public_AI_Files();
	new WP_AI_OS_Schema_Engine();
	new WP_AI_OS_Agent_Scheduler();
	new WP_AI_OS_WooCommerce();
	if ( is_admin() ) { new WP_AI_OS_Admin(); }
} );

register_activation_hook( WP_AI_OS_FILE, static function () { WP_AI_OS_DB_Schema::install(); ( new WP_AI_OS_Agent_Scheduler() )->schedule(); flush_rewrite_rules(); } );
register_deactivation_hook( WP_AI_OS_FILE, static function () { ( new WP_AI_OS_Agent_Scheduler() )->unschedule(); flush_rewrite_rules(); } );

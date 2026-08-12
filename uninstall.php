<?php
/**
 * Uninstall WP AI OS.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

global $wpdb;

$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wp_ai_os_knowledge" );
delete_option( 'wp_ai_os_settings' );
delete_option( 'wp_ai_os_license' );
delete_option( 'wp_ai_os_last_readiness_report' );
wp_clear_scheduled_hook( 'wp_ai_os_agent_tick' );

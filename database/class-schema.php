<?php
/**
 * Database schema.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WP_AI_OS_DB_Schema {
	public static function install(): void {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table = $wpdb->prefix . 'wp_ai_os_knowledge';
		$charset = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			object_id bigint(20) unsigned NOT NULL DEFAULT 0,
			object_type varchar(32) NOT NULL DEFAULT 'post',
			title text NOT NULL,
			content longtext NOT NULL,
			url text NOT NULL,
			content_hash char(64) NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY (id),
			UNIQUE KEY object_key (object_id,object_type),
			KEY hash_key (content_hash),
			KEY updated_key (updated_at)
		) {$charset};";
		dbDelta( $sql );
	}
}

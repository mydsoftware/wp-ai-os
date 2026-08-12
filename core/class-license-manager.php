<?php
/**
 * License manager.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WP_AI_OS_License_Manager {
	private const OPTION = 'wp_ai_os_license';

	public static function get(): array {
		$value = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $value ) ? $value : array(), array( 'key' => '', 'status' => 'inactive', 'plan' => 'free', 'expires_at' => '', 'checked_at' => 0 ) );
	}

	public static function save_key( string $key ): bool {
		$current = self::get();
		$current['key'] = sanitize_text_field( $key );
		$current['status'] = '' === $current['key'] ? 'inactive' : 'pending';
		return update_option( self::OPTION, $current, false );
	}

	public static function site_fingerprint(): string {
		return hash( 'sha256', wp_parse_url( home_url( '/' ), PHP_URL_HOST ) . '|' . wp_salt( 'auth' ) );
	}

	public static function has_feature( string $feature ): bool {
		$license = self::get();
		if ( 'free' === $license['plan'] ) {
			return (bool) apply_filters( 'wp_ai_os_free_feature_' . sanitize_key( $feature ), true );
		}
		return (bool) apply_filters( 'wp_ai_os_feature_' . sanitize_key( $feature ), 'active' === $license['status'] );
	}

	public static function masked(): array {
		$license = self::get();
		if ( '' !== $license['key'] ) { $license['key'] = '********'; }
		return $license;
	}
}

<?php
/**
 * Settings manager.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_AI_OS_Settings {

	private const OPTION = 'wp_ai_os_settings';

	public static function defaults(): array {
		return array(
			'ai_provider'       => 'none',
			'ai_api_key'        => '',
			'ai_model'          => '',
			'ai_base_url'       => '',
			'enable_llms_txt'   => true,
			'enable_schema'     => true,
			'organization_name' => get_bloginfo( 'name' ),
			'organization_url'  => home_url( '/' ),
		);
	}

	public static function all(): array {
		$value = get_option( self::OPTION, array() );
		return wp_parse_args( is_array( $value ) ? $value : array(), self::defaults() );
	}

	public static function get( string $key, $default = null ) {
		$settings = self::all();
		return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
	}

	public static function update( array $settings ): bool {
		$current = self::all();
		$allowed = array_keys( self::defaults() );
		$clean   = array();

		foreach ( $allowed as $key ) {
			if ( ! array_key_exists( $key, $settings ) ) {
				continue;
			}
			$value = $settings[ $key ];
			switch ( $key ) {
				case 'enable_llms_txt':
				case 'enable_schema':
					$clean[ $key ] = (bool) $value;
					break;
				case 'ai_api_key':
				case 'ai_provider':
				case 'ai_model':
					$clean[ $key ] = sanitize_text_field( (string) $value );
					break;
				case 'ai_base_url':
				case 'organization_url':
					$clean[ $key ] = esc_url_raw( (string) $value );
					break;
				case 'organization_name':
					$clean[ $key ] = sanitize_text_field( (string) $value );
					break;
			}
		}

		return update_option( self::OPTION, array_merge( $current, $clean ), false );
	}
}

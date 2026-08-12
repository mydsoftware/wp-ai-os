<?php
/**
 * REST API controller.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_AI_OS_API_Controller {

	private const NAMESPACE = 'wp-ai-os/v1';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/status',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'status' ),
				'permission_callback' => array( $this, 'permissions' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/readiness/scan',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'scan' ),
				'permission_callback' => array( $this, 'permissions' ),
			)
		);
	}

	public function permissions(): bool {
		return current_user_can( 'manage_options' );
	}

	public function status(): WP_REST_Response {
		$report = get_option( 'wp_ai_os_last_readiness_report', null );

		return new WP_REST_Response(
			array(
				'plugin'    => 'wp-ai-os',
				'version'   => WP_AI_OS_VERSION,
				'wordpress' => get_bloginfo( 'version' ),
				'php'       => PHP_VERSION,
				'last_scan' => is_array( $report ) ? $report : null,
			),
			200
		);
	}

	public function scan(): WP_REST_Response {
		require_once WP_AI_OS_PATH . 'ai/readiness/class-scanner.php';

		$scanner = new WP_AI_OS_Readiness_Scanner();
		$report  = $scanner->scan();

		update_option( 'wp_ai_os_last_readiness_report', $report, false );

		return new WP_REST_Response( $report, 200 );
	}
}

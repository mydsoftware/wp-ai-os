<?php
/**
 * REST API controller.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once WP_AI_OS_PATH . 'includes/class-settings.php';
require_once WP_AI_OS_PATH . 'ai/providers/class-openai-compatible-provider.php';
require_once WP_AI_OS_PATH . 'ai/optimization/class-content-analyzer.php';
require_once WP_AI_OS_PATH . 'ai/content/class-content-assistant.php';
require_once WP_AI_OS_PATH . 'ai/rag/class-knowledge-base.php';
require_once WP_AI_OS_PATH . 'ai/rag/class-rag-engine.php';

class WP_AI_OS_API_Controller {
	private const NAMESPACE = 'wp-ai-os/v1';
	public function __construct() { add_action( 'rest_api_init', array( $this, 'register_routes' ) ); }
	public function register_routes(): void {
		register_rest_route( self::NAMESPACE, '/status', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( $this, 'status' ), 'permission_callback' => array( $this, 'permissions' ) ) );
		register_rest_route( self::NAMESPACE, '/readiness/scan', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'scan' ), 'permission_callback' => array( $this, 'permissions' ) ) );
		register_rest_route( self::NAMESPACE, '/settings', array( array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( $this, 'settings_get' ), 'permission_callback' => array( $this, 'permissions' ) ), array( 'methods' => WP_REST_Server::EDITABLE, 'callback' => array( $this, 'settings_update' ), 'permission_callback' => array( $this, 'permissions' ), 'args' => array( 'settings' => array( 'required' => true, 'type' => 'object' ) ) ) ) );
		register_rest_route( self::NAMESPACE, '/ai/test', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'ai_test' ), 'permission_callback' => array( $this, 'permissions' ) ) );
		register_rest_route( self::NAMESPACE, '/content/analyze', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'content_analyze' ), 'permission_callback' => array( $this, 'permissions' ) ) );
		register_rest_route( self::NAMESPACE, '/content/assist', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'content_assist' ), 'permission_callback' => array( $this, 'permissions' ) ) );
		register_rest_route( self::NAMESPACE, '/knowledge/index', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'knowledge_index' ), 'permission_callback' => array( $this, 'permissions' ) ) );
		register_rest_route( self::NAMESPACE, '/knowledge/search', array( 'methods' => WP_REST_Server::READABLE, 'callback' => array( $this, 'knowledge_search' ), 'permission_callback' => array( $this, 'permissions' ), 'args' => array( 'q' => array( 'required' => true, 'type' => 'string' ) ) );
		register_rest_route( self::NAMESPACE, '/knowledge/ask', array( 'methods' => WP_REST_Server::CREATABLE, 'callback' => array( $this, 'knowledge_ask' ), 'permission_callback' => array( $this, 'permissions' ), 'args' => array( 'question' => array( 'required' => true, 'type' => 'string' ) ) );
	}
	public function permissions(): bool { return current_user_can( 'manage_options' ); }
	public function status(): WP_REST_Response { $report = get_option( 'wp_ai_os_last_readiness_report', null ); return new WP_REST_Response( array( 'plugin' => 'wp-ai-os', 'version' => WP_AI_OS_VERSION, 'wordpress' => get_bloginfo( 'version' ), 'php' => PHP_VERSION, 'last_scan' => is_array( $report ) ? $report : null ), 200 ); }
	public function scan(): WP_REST_Response { require_once WP_AI_OS_PATH . 'ai/readiness/class-scanner.php'; $report = ( new WP_AI_OS_Readiness_Scanner() )->scan(); update_option( 'wp_ai_os_last_readiness_report', $report, false ); return new WP_REST_Response( $report, 200 ); }
	public function settings_get(): WP_REST_Response { $settings = WP_AI_OS_Settings::all(); if ( '' !== $settings['ai_api_key'] ) { $settings['ai_api_key'] = '********'; } return new WP_REST_Response( $settings, 200 ); }
	public function settings_update( WP_REST_Request $request ): WP_REST_Response { $settings = $request->get_param( 'settings' ); if ( ! is_array( $settings ) ) { return new WP_REST_Response( array( 'code' => 'invalid_settings' ), 400 ); } if ( isset( $settings['ai_api_key'] ) && '********' === $settings['ai_api_key'] ) { unset( $settings['ai_api_key'] ); } WP_AI_OS_Settings::update( $settings ); return $this->settings_get(); }
	public function ai_test(): WP_REST_Response { $result = ( new WP_AI_OS_OpenAI_Compatible_Provider() )->generate( 'You are a concise WordPress AI assistant.', 'Reply with exactly: WP AI OS connection OK', array( 'temperature' => 0 ) ); if ( is_wp_error( $result ) ) { return new WP_REST_Response( array( 'success' => false, 'code' => $result->get_error_code(), 'message' => $result->get_error_message() ), 400 ); } return new WP_REST_Response( array( 'success' => true, 'content' => $result['content'] ), 200 ); }
	public function content_analyze( WP_REST_Request $request ): WP_REST_Response { return new WP_REST_Response( ( new WP_AI_OS_Content_Analyzer() )->analyze( (string) $request->get_param( 'content' ), (string) $request->get_param( 'title' ) ), 200 ); }
	public function content_assist( WP_REST_Request $request ): WP_REST_Response { $result = ( new WP_AI_OS_Content_Assistant() )->generate_for_post( absint( $request->get_param( 'post_id' ) ), sanitize_key( (string) $request->get_param( 'task' ) ) ); if ( is_wp_error( $result ) ) { return new WP_REST_Response( array( 'success' => false, 'code' => $result->get_error_code(), 'message' => $result->get_error_message() ), 400 ); } return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 200 ); }
	public function knowledge_index(): WP_REST_Response { $count = ( new WP_AI_OS_Knowledge_Base() )->index_all(); return new WP_REST_Response( array( 'indexed' => $count ), 200 ); }
	public function knowledge_search( WP_REST_Request $request ): WP_REST_Response { return new WP_REST_Response( array( 'results' => ( new WP_AI_OS_Knowledge_Base() )->search( (string) $request->get_param( 'q' ) ) ), 200 ); }
	public function knowledge_ask( WP_REST_Request $request ): WP_REST_Response { $result = ( new WP_AI_OS_RAG_Engine() )->ask( (string) $request->get_param( 'question' ) ); if ( is_wp_error( $result ) ) { return new WP_REST_Response( array( 'success' => false, 'code' => $result->get_error_code(), 'message' => $result->get_error_message() ), 400 ); } return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 200 ); }
}

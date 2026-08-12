<?php
/**
 * OpenAI-compatible provider.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/class-provider-interface.php';
require_once WP_AI_OS_PATH . 'includes/class-settings.php';

class WP_AI_OS_OpenAI_Compatible_Provider implements WP_AI_OS_Provider_Interface {

	public function id(): string {
		return 'openai_compatible';
	}

	public function label(): string {
		return __( 'OpenAI Compatible', 'wp-ai-os' );
	}

	public function is_configured(): bool {
		return '' !== (string) WP_AI_OS_Settings::get( 'ai_api_key', '' )
			&& '' !== (string) WP_AI_OS_Settings::get( 'ai_base_url', '' )
			&& '' !== (string) WP_AI_OS_Settings::get( 'ai_model', '' );
	}

	public function generate( string $system, string $prompt, array $options = array() ): WP_Error|array {
		if ( ! $this->is_configured() ) {
			return new WP_Error( 'provider_not_configured', __( 'AI provider is not configured.', 'wp-ai-os' ) );
		}

		$base_url = untrailingslashit( (string) WP_AI_OS_Settings::get( 'ai_base_url', '' ) );
		$url      = $base_url . '/chat/completions';
		$api_key  = (string) WP_AI_OS_Settings::get( 'ai_api_key', '' );
		$model    = (string) WP_AI_OS_Settings::get( 'ai_model', '' );

		$body = array(
			'model'    => $model,
			'messages' => array(
				array( 'role' => 'system', 'content' => $system ),
				array( 'role' => 'user', 'content' => $prompt ),
			),
		);

		if ( isset( $options['temperature'] ) ) {
			$body['temperature'] = (float) $options['temperature'];
		}

		$response = wp_remote_post(
			$url,
			array(
			'timeout' => 60,
			'headers' => array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type'  => 'application/json',
			),
			'body' => wp_json_encode( $body ),
		)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status = wp_remote_retrieve_response_code( $response );
		$data   = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $status < 200 || $status >= 300 ) {
			$message = is_array( $data ) && isset( $data['error']['message'] ) ? (string) $data['error']['message'] : __( 'AI provider request failed.', 'wp-ai-os' );
			return new WP_Error( 'provider_http_error', $message, array( 'status' => $status ) );
		}

		$content = $data['choices'][0]['message']['content'] ?? null;
		if ( ! is_string( $content ) ) {
			return new WP_Error( 'provider_invalid_response', __( 'AI provider returned an invalid response.', 'wp-ai-os' ) );
		}

		return array(
			'content' => $content,
			'raw'     => $data,
		);
	}
}

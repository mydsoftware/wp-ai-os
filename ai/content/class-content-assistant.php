<?php
/**
 * AI content assistant.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WP_AI_OS_PATH . 'ai/providers/class-openai-compatible-provider.php';

class WP_AI_OS_Content_Assistant {

	public function generate_for_post( int $post_id, string $task = 'optimize' ): WP_Error|array {
		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post ) {
			return new WP_Error( 'post_not_found', __( 'Post not found.', 'wp-ai-os' ) );
		}

		$allowed = array( 'optimize', 'faq', 'meta', 'outline' );
		if ( ! in_array( $task, $allowed, true ) ) {
			return new WP_Error( 'invalid_task', __( 'Unsupported content task.', 'wp-ai-os' ) );
		}

		$content = wp_strip_all_tags( $post->post_content );
		$content = function_exists( 'mb_substr' ) ? mb_substr( $content, 0, 12000 ) : substr( $content, 0, 12000 );
		$system  = 'You are a senior WordPress SEO, GEO and AEO editor. Return useful, factual, concise output. Do not invent claims. Return JSON only.';
		$prompt  = $this->prompt( $post->post_title, $content, $task );

		$result = ( new WP_AI_OS_OpenAI_Compatible_Provider() )->generate( $system, $prompt, array( 'temperature' => 0.2 ) );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$json = json_decode( trim( $result['content'] ), true );
		if ( ! is_array( $json ) ) {
			return new WP_Error( 'invalid_ai_json', __( 'The AI provider did not return valid JSON.', 'wp-ai-os' ) );
		}

		return array( 'post_id' => $post_id, 'task' => $task, 'result' => $json );
	}

	private function prompt( string $title, string $content, string $task ): string {
		$schema = array(
			'optimize' => '{"summary":"...","key_entities":["..."],"questions":["..."],"recommended_headings":["..."],"improvements":["..."]}',
			'faq'      => '{"faqs":[{"question":"...","answer":"..."}]}',
			'meta'     => '{"seo_title":"...","meta_description":"...","social_title":"...","social_description":"..."}',
			'outline'  => '{"outline":[{"heading":"...","purpose":"..."}]}',
		);

		return "Task: {$task}\nOutput schema: {$schema[$task]}\nTitle: {$title}\nContent:\n{$content}";
	}
}

<?php
/**
 * RAG engine.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once WP_AI_OS_PATH . 'ai/rag/class-knowledge-base.php';
require_once WP_AI_OS_PATH . 'ai/providers/class-openai-compatible-provider.php';

class WP_AI_OS_RAG_Engine {
	public function ask( string $question, int $limit = 5 ): WP_Error|array {
		$question = trim( wp_strip_all_tags( $question ) );
		if ( '' === $question ) { return new WP_Error( 'empty_question', __( 'Question is required.', 'wp-ai-os' ) ); }
		$sources = ( new WP_AI_OS_Knowledge_Base() )->search( $question, $limit );
		if ( empty( $sources ) ) { return new WP_Error( 'no_sources', __( 'No matching knowledge sources were found.', 'wp-ai-os' ) ); }

		$context = array();
		foreach ( $sources as $source ) {
			$context[] = "SOURCE: {$source['title']}\nURL: {$source['url']}\nCONTENT:\n" . ( function_exists( 'mb_substr' ) ? mb_substr( $source['content'], 0, 5000 ) : substr( $source['content'], 0, 5000 ) );
		}

		$system = 'You answer using only the supplied WordPress knowledge sources. If the sources do not support an answer, say so. Cite source URLs in a sources array. Return JSON only.';
		$prompt = "QUESTION:\n{$question}\n\nKNOWLEDGE:\n" . implode( "\n\n---\n\n", $context ) . "\n\nReturn: {\"answer\":\"...\",\"sources\":[{\"title\":\"...\",\"url\":\"...\"}]}";
		$result = ( new WP_AI_OS_OpenAI_Compatible_Provider() )->generate( $system, $prompt, array( 'temperature' => 0.1 ) );
		if ( is_wp_error( $result ) ) { return $result; }
		$data = json_decode( trim( $result['content'] ), true );
		if ( ! is_array( $data ) || ! isset( $data['answer'] ) ) { return new WP_Error( 'invalid_rag_response', __( 'AI provider returned an invalid RAG response.', 'wp-ai-os' ) ); }
		return array( 'answer' => (string) $data['answer'], 'sources' => isset( $data['sources'] ) && is_array( $data['sources'] ) ? $data['sources'] : array(), 'retrieved' => $sources );
	}
}

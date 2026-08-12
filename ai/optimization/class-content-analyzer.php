<?php
/**
 * Content optimization analyzer.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_AI_OS_Content_Analyzer {

	public function analyze( string $content, string $title = '' ): array {
		$text       = trim( wp_strip_all_tags( $content ) );
		$words      = $this->words( $text );
		$sentences  = $this->sentences( $text );
		$word_count = count( $words );
		$score      = 0;
		$checks     = array();

		$checks[] = $this->check( 'title', __( 'Descriptive title', 'wp-ai-os' ), '' !== trim( $title ), 15, __( 'Add a clear, specific title that answers the search intent.', 'wp-ai-os' ) );
		$checks[] = $this->check( 'length', __( 'Useful content depth', 'wp-ai-os' ), $word_count >= 300, 15, __( 'Expand the page with useful, original information.', 'wp-ai-os' ) );
		$checks[] = $this->check( 'headings', __( 'Structured headings', 'wp-ai-os' ), preg_match( '/<h[2-4][^>]*>/i', $content ) === 1, 15, __( 'Use descriptive H2/H3 headings to segment the answer.', 'wp-ai-os' ) );
		$checks[] = $this->check( 'questions', __( 'Question coverage', 'wp-ai-os' ), (bool) preg_match( '/\?|\bhow\b|\bwhat\b|\bwhy\b|\bچگونه\b|\bچیست\b|\bچرا\b/iu', $text ), 15, __( 'Add direct answers to likely user questions.', 'wp-ai-os' ) );
		$checks[] = $this->check( 'lists', __( 'Answer-friendly lists', 'wp-ai-os' ), (bool) preg_match( '/<([ou]l)\b/i', $content ), 10, __( 'Use lists for steps, features, comparisons and facts where appropriate.', 'wp-ai-os' ) );
		$checks[] = $this->check( 'short_sentences', __( 'Readable answers', 'wp-ai-os' ), $this->average_sentence_words( $sentences ) <= 24, 15, __( 'Break long sentences into concise statements.', 'wp-ai-os' ) );
		$checks[] = $this->check( 'entities', __( 'Entity clarity', 'wp-ai-os' ), $this->has_entities( $text ), 15, __( 'Name important people, products, organizations and concepts explicitly.', 'wp-ai-os' ) );

		foreach ( $checks as $check ) {
			$score += $check['score'];
		}

		return array(
			'score'       => $score,
			'max_score'   => 100,
			'word_count'  => $word_count,
			'checks'      => $checks,
			'recommendations' => array_values( array_filter( array_map( static function ( $item ) { return $item['recommendation']; }, $checks ) ) ),
		);
	}

	private function check( string $id, string $label, bool $passed, int $points, string $recommendation ): array {
		return array( 'id' => $id, 'label' => $label, 'score' => $passed ? $points : 0, 'max_score' => $points, 'status' => $passed ? 'pass' : 'warning', 'recommendation' => $passed ? '' : $recommendation );
	}

	private function words( string $text ): array {
		$words = preg_split( '/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
		return is_array( $words ) ? $words : array();
	}

	private function sentences( string $text ): array {
		$sentences = preg_split( '/[.!?؟]+/u', $text, -1, PREG_SPLIT_NO_EMPTY );
		return is_array( $sentences ) ? array_filter( array_map( 'trim', $sentences ) ) : array();
	}

	private function average_sentence_words( array $sentences ): float {
		if ( empty( $sentences ) ) {
			return 0.0;
		}
		$total = 0;
		foreach ( $sentences as $sentence ) {
			$total += count( $this->words( $sentence ) );
		}
		return $total / count( $sentences );
	}

	private function has_entities( string $text ): bool {
		return preg_match( '/\b[A-Z][A-Za-z0-9_-]{2,}\b/u', $text ) === 1 || preg_match( '/[آ-ی]{3,}/u', $text ) === 1;
	}
}

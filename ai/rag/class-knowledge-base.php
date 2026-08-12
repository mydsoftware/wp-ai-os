<?php
/**
 * Local knowledge base.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WP_AI_OS_Knowledge_Base {
	private function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'wp_ai_os_knowledge';
	}

	public function index_post( int $post_id ): bool {
		global $wpdb;
		$post = get_post( $post_id );
		if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) { return false; }
		$content = trim( wp_strip_all_tags( $post->post_title . "\n\n" . $post->post_content ) );
		$hash = hash( 'sha256', $content );
		return false !== $wpdb->replace( $this->table(), array(
			'object_id' => $post_id,
			'object_type' => $post->post_type,
			'title' => $post->post_title,
			'content' => $content,
			'url' => get_permalink( $post_id ),
			'content_hash' => $hash,
			'updated_at' => current_time( 'mysql' ),
		), array( '%d', '%s', '%s', '%s', '%s', '%s', '%s' ) );
	}

	public function index_all( int $limit = 500 ): int {
		$ids = get_posts( array( 'post_type' => array( 'post', 'page' ), 'post_status' => 'publish', 'posts_per_page' => min( 500, max( 1, $limit ) ), 'fields' => 'ids', 'no_found_rows' => true ) );
		$count = 0;
		foreach ( $ids as $id ) { if ( $this->index_post( absint( $id ) ) ) { $count++; } }
		return $count;
	}

	public function search( string $query, int $limit = 5 ): array {
		global $wpdb;
		$query = trim( wp_strip_all_tags( $query ) );
		if ( '' === $query ) { return array(); }
		$like = '%' . $wpdb->esc_like( $query ) . '%';
		$sql = $wpdb->prepare( "SELECT id, object_id, object_type, title, content, url, updated_at FROM {$this->table()} WHERE title LIKE %s OR content LIKE %s ORDER BY CASE WHEN title LIKE %s THEN 0 ELSE 1 END, updated_at DESC LIMIT %d", $like, $like, $like, min( 20, max( 1, $limit ) ) );
		$rows = $wpdb->get_results( $sql, ARRAY_A );
		return is_array( $rows ) ? $rows : array();
	}
}

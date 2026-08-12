<?php
/**
 * Schema engine.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WP_AI_OS_PATH . 'includes/class-settings.php';

class WP_AI_OS_Schema_Engine {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'output' ), 20 );
	}

	public function output(): void {
		if ( is_admin() || ! WP_AI_OS_Settings::get( 'enable_schema', true ) ) {
			return;
		}

		$graph = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				$this->organization(),
				$this->website(),
			),
		);

		if ( is_singular() ) {
			$graph['@graph'][] = $this->article_or_page();
		}

		echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}

	private function organization(): array {
		return array(
			'@type' => 'Organization',
			'@id'   => home_url( '/#organization' ),
			'name'  => (string) WP_AI_OS_Settings::get( 'organization_name', get_bloginfo( 'name' ) ),
			'url'   => (string) WP_AI_OS_Settings::get( 'organization_url', home_url( '/' ) ),
		);
	}

	private function website(): array {
		return array(
			'@type' => 'WebSite',
			'@id'   => home_url( '/#website' ),
			'url'   => home_url( '/' ),
			'name'  => get_bloginfo( 'name' ),
			'publisher' => array( '@id' => home_url( '/#organization' ) ),
			'inLanguage' => get_bloginfo( 'language' ),
		);
	}

	private function article_or_page(): array {
		$post = get_queried_object();
		$type = ( $post instanceof WP_Post && 'post' === $post->post_type ) ? 'Article' : 'WebPage';
		$data = array(
			'@type' => $type,
			'@id'   => get_permalink( $post ) . '#primary',
			'url'   => get_permalink( $post ),
			'name'  => get_the_title( $post ),
			'inLanguage' => get_bloginfo( 'language' ),
		);

		if ( 'Article' === $type ) {
			$data['headline']      = get_the_title( $post );
			$data['datePublished'] = get_the_date( DATE_W3C, $post );
			$data['dateModified']  = get_the_modified_date( DATE_W3C, $post );
			$data['author']        = array( '@type' => 'Person', 'name' => get_the_author_meta( 'display_name', $post->post_author ) );
			$data['publisher']     = array( '@id' => home_url( '/#organization' ) );
		}
		return $data;
	}
}

<?php
/**
 * WooCommerce integration.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WP_AI_OS_WooCommerce {
	public function __construct() {
		add_action( 'wp_head', array( $this, 'product_schema' ), 25 );
	}

	public function product_schema(): void {
		if ( ! function_exists( 'is_product' ) || ! is_product() || ! function_exists( 'wc_get_product' ) ) { return; }
		$product = wc_get_product( get_the_ID() );
		if ( ! $product ) { return; }
		$data = array(
			'@context' => 'https://schema.org',
			'@type' => 'Product',
			'name' => $product->get_name(),
			'url' => get_permalink( $product->get_id() ),
			'description' => wp_strip_all_tags( $product->get_short_description() ?: $product->get_description() ),
			'sku' => $product->get_sku(),
		);
		if ( $product->get_image_id() ) { $data['image'] = wp_get_attachment_image_url( $product->get_image_id(), 'full' ); }
		if ( $product->get_price() !== '' ) {
			$data['offers'] = array( '@type' => 'Offer', 'price' => $product->get_price(), 'priceCurrency' => get_woocommerce_currency(), 'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock', 'url' => get_permalink( $product->get_id() ) );
		}
		echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
	}
}

<?php
/**
 * AI provider contract.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface WP_AI_OS_Provider_Interface {
	public function id(): string;
	public function label(): string;
	public function is_configured(): bool;
	public function generate( string $system, string $prompt, array $options = array() ): WP_Error|array;
}

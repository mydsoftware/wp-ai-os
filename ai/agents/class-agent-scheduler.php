<?php
/**
 * Agent scheduler.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once WP_AI_OS_PATH . 'ai/agents/class-agent-runner.php';

class WP_AI_OS_Agent_Scheduler {
	private const HOOK = 'wp_ai_os_agent_tick';

	public function __construct() {
		add_action( self::HOOK, array( $this, 'tick' ) );
	}

	public function schedule(): void {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'twicedaily', self::HOOK );
		}
	}

	public function unschedule(): void {
		$timestamp = wp_next_scheduled( self::HOOK );
		if ( $timestamp ) { wp_unschedule_event( $timestamp, self::HOOK ); }
	}

	public function tick(): void {
		if ( ! apply_filters( 'wp_ai_os_enable_agent_automation', false ) ) { return; }
		( new WP_AI_OS_Agent_Runner() )->run( 'readiness_scan' );
		( new WP_AI_OS_Agent_Runner() )->run( 'knowledge_index' );
	}
}

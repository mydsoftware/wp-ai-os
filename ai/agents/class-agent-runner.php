<?php
/**
 * AI agent task runner.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once WP_AI_OS_PATH . 'ai/rag/class-knowledge-base.php';
require_once WP_AI_OS_PATH . 'ai/optimization/class-content-analyzer.php';

class WP_AI_OS_Agent_Runner {
	private const TASKS = array( 'readiness_scan', 'knowledge_index', 'content_analyze' );

	public function run( string $task, array $args = array() ): WP_Error|array {
		if ( ! in_array( $task, self::TASKS, true ) ) { return new WP_Error( 'unknown_agent_task', __( 'Unknown or disabled agent task.', 'wp-ai-os' ) ); }

		switch ( $task ) {
			case 'readiness_scan':
				require_once WP_AI_OS_PATH . 'ai/readiness/class-scanner.php';
				$report = ( new WP_AI_OS_Readiness_Scanner() )->scan();
				update_option( 'wp_ai_os_last_readiness_report', $report, false );
				return array( 'task' => $task, 'result' => $report );
			case 'knowledge_index':
				return array( 'task' => $task, 'result' => array( 'indexed' => ( new WP_AI_OS_Knowledge_Base() )->index_all() ) );
			case 'content_analyze':
				return array( 'task' => $task, 'result' => ( new WP_AI_OS_Content_Analyzer() )->analyze( (string) ( $args['content'] ?? '' ), (string) ( $args['title'] ?? '' ) ) );
		}
		return new WP_Error( 'agent_task_failed', __( 'Agent task failed.', 'wp-ai-os' ) );
	}

	public function tasks(): array {
		return self::TASKS;
	}
}

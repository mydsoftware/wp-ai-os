<?php
/**
 * AI Readiness Dashboard.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$report = get_option( 'wp_ai_os_last_readiness_report', null );
$score  = is_array( $report ) ? (int) ( $report['score'] ?? 0 ) : null;
?>
<div class="wrap wp-ai-os-dashboard">
	<h1><?php esc_html_e( 'WP AI OS', 'wp-ai-os' ); ?></h1>
	<p class="description"><?php esc_html_e( 'Make your WordPress site understandable, discoverable and usable by AI systems.', 'wp-ai-os' ); ?></p>

	<div class="wp-ai-os-toolbar">
		<button type="button" class="button button-primary" id="wp-ai-os-scan"><?php esc_html_e( 'Scan Website', 'wp-ai-os' ); ?></button>
		<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=wp-ai-os-settings' ) ); ?>"><?php esc_html_e( 'AI Settings', 'wp-ai-os' ); ?></a>
		<span id="wp-ai-os-scan-status" role="status" aria-live="polite"></span>
	</div>

	<div class="wp-ai-os-cards">
		<div class="wp-ai-os-score-card" id="wp-ai-os-score-card">
			<h2><?php esc_html_e( 'AI Readiness', 'wp-ai-os' ); ?></h2>
			<div class="wp-ai-os-score" id="wp-ai-os-score">
				<?php if ( null === $score ) : ?><span><?php esc_html_e( 'Not scanned', 'wp-ai-os' ); ?></span><?php else : ?><?php echo esc_html( $score ); ?><span>/ 100</span><?php endif; ?>
			</div>
			<p><?php esc_html_e( 'Technical readiness for AI discovery.', 'wp-ai-os' ); ?></p>
		</div>
		<div class="wp-ai-os-info-card">
			<h2><?php esc_html_e( 'AI Discovery', 'wp-ai-os' ); ?></h2>
			<p><strong>llms.txt</strong><br><a href="<?php echo esc_url( home_url( '/llms.txt' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( home_url( '/llms.txt' ) ); ?></a></p>
			<p><strong>Schema.org</strong><br><?php esc_html_e( 'Organization, WebSite and page schema are generated automatically.', 'wp-ai-os' ); ?></p>
		</div>
		<div class="wp-ai-os-info-card">
			<h2><?php esc_html_e( 'AI Stack', 'wp-ai-os' ); ?></h2>
			<p><?php esc_html_e( 'Readiness → GEO/AEO → AI Content → RAG → Agents → WooCommerce', 'wp-ai-os' ); ?></p>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=wp-ai-os-settings' ) ); ?>"><?php esc_html_e( 'Configure the AI provider →', 'wp-ai-os' ); ?></a></p>
		</div>
	</div>

	<div class="wp-ai-os-checks">
		<h2><?php esc_html_e( 'Readiness Checks', 'wp-ai-os' ); ?></h2>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Check', 'wp-ai-os' ); ?></th><th><?php esc_html_e( 'Score', 'wp-ai-os' ); ?></th><th><?php esc_html_e( 'Status', 'wp-ai-os' ); ?></th><th><?php esc_html_e( 'Message', 'wp-ai-os' ); ?></th></tr></thead>
			<tbody id="wp-ai-os-results">
			<?php if ( ! is_array( $report ) || empty( $report['results'] ) ) : ?>
				<tr><td colspan="4"><?php esc_html_e( 'Run your first scan to see the results.', 'wp-ai-os' ); ?></td></tr>
			<?php else : foreach ( $report['results'] as $result ) : ?>
				<tr><td><strong><?php echo esc_html( $result['title'] ); ?></strong></td><td><?php echo esc_html( $result['score'] . '/' . $result['max_score'] ); ?></td><td><?php echo esc_html( ucfirst( $result['status'] ) ); ?></td><td><?php echo esc_html( $result['message'] ); ?><?php if ( ! empty( $result['recommendation'] ) ) : ?><br><small><strong><?php esc_html_e( 'Recommendation:', 'wp-ai-os' ); ?></strong> <?php echo esc_html( $result['recommendation'] ); ?></small><?php endif; ?></td></tr>
			<?php endforeach; endif; ?>
			</tbody>
		</table>
	</div>

	<p><small id="wp-ai-os-last-scan"><?php if ( is_array( $report ) && ! empty( $report['scanned_at'] ) ) { printf( esc_html__( 'Last scan: %s', 'wp-ai-os' ), esc_html( $report['scanned_at'] ) ); } ?></small></p>
</div>

<?php
/**
 * Admin functionality.
 *
 * @package WP_AI_OS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WP_AI_OS_PATH . 'includes/class-settings.php';

class WP_AI_OS_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_menu(): void {
		add_menu_page(
			__( 'WP AI OS', 'wp-ai-os' ),
			__( 'WP AI OS', 'wp-ai-os' ),
			'manage_options',
			'wp-ai-os',
			array( $this, 'render_dashboard' ),
			'dashicons-admin-generic',
			3
		);

		add_submenu_page(
			'wp-ai-os',
			__( 'AI Settings', 'wp-ai-os' ),
			__( 'Settings', 'wp-ai-os' ),
			'manage_options',
			'wp-ai-os-settings',
			array( $this, 'render_settings' )
		);
	}

	public function render_dashboard(): void {
		require WP_AI_OS_PATH . 'admin/views/dashboard.php';
	}

	public function render_settings(): void {
		$settings = WP_AI_OS_Settings::all();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WP AI OS Settings', 'wp-ai-os' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'wp_ai_os_settings' ); ?>
				<table class="form-table" role="presentation">
					<tr><th scope="row"><label for="wp-ai-os-provider"><?php esc_html_e( 'AI Provider', 'wp-ai-os' ); ?></label></th><td><select id="wp-ai-os-provider" name="wp_ai_os_settings[ai_provider]"><option value="none" <?php selected( $settings['ai_provider'], 'none' ); ?>><?php esc_html_e( 'None', 'wp-ai-os' ); ?></option><option value="openai_compatible" <?php selected( $settings['ai_provider'], 'openai_compatible' ); ?>><?php esc_html_e( 'OpenAI Compatible', 'wp-ai-os' ); ?></option></select></td></tr>
					<tr><th scope="row"><label for="wp-ai-os-base-url"><?php esc_html_e( 'AI Base URL', 'wp-ai-os' ); ?></label></th><td><input class="regular-text" id="wp-ai-os-base-url" name="wp_ai_os_settings[ai_base_url]" value="<?php echo esc_attr( $settings['ai_base_url'] ); ?>" placeholder="https://api.example.com/v1"></td></tr>
					<tr><th scope="row"><label for="wp-ai-os-model"><?php esc_html_e( 'AI Model', 'wp-ai-os' ); ?></label></th><td><input class="regular-text" id="wp-ai-os-model" name="wp_ai_os_settings[ai_model]" value="<?php echo esc_attr( $settings['ai_model'] ); ?>"></td></tr>
					<tr><th scope="row"><label for="wp-ai-os-key"><?php esc_html_e( 'API Key', 'wp-ai-os' ); ?></label></th><td><input type="password" autocomplete="new-password" class="regular-text" id="wp-ai-os-key" name="wp_ai_os_settings[ai_api_key]" value="<?php echo esc_attr( $settings['ai_api_key'] ); ?>"></td></tr>
					<tr><th scope="row"><label for="wp-ai-os-org"><?php esc_html_e( 'Organization Name', 'wp-ai-os' ); ?></label></th><td><input class="regular-text" id="wp-ai-os-org" name="wp_ai_os_settings[organization_name]" value="<?php echo esc_attr( $settings['organization_name'] ); ?>"></td></tr>
					<tr><th scope="row"><label for="wp-ai-os-org-url"><?php esc_html_e( 'Organization URL', 'wp-ai-os' ); ?></label></th><td><input type="url" class="regular-text" id="wp-ai-os-org-url" name="wp_ai_os_settings[organization_url]" value="<?php echo esc_attr( $settings['organization_url'] ); ?>"></td></tr>
					<tr><th scope="row"><?php esc_html_e( 'AI discovery files', 'wp-ai-os' ); ?></th><td><label><input type="checkbox" name="wp_ai_os_settings[enable_llms_txt]" value="1" <?php checked( $settings['enable_llms_txt'], true ); ?>> <?php esc_html_e( 'Enable llms.txt', 'wp-ai-os' ); ?></label><br><label><input type="checkbox" name="wp_ai_os_settings[enable_schema]" value="1" <?php checked( $settings['enable_schema'], true ); ?>> <?php esc_html_e( 'Enable AI schema layer', 'wp-ai-os' ); ?></label></td></tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function register_settings(): void {
		register_setting(
			'wp_ai_os_settings',
			'wp_ai_os_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => WP_AI_OS_Settings::defaults(),
			)
		);
	}

	public function sanitize_settings( $input ): array {
		return WP_AI_OS_Settings::update( is_array( $input ) ? $input : array() ) ? WP_AI_OS_Settings::all() : WP_AI_OS_Settings::all();
	}

	public function enqueue_assets( string $hook_suffix ): void {
		if ( 'toplevel_page_wp-ai-os' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-ai-os-admin', WP_AI_OS_URL . 'assets/css/admin.css', array(), WP_AI_OS_VERSION );
		wp_enqueue_script( 'wp-ai-os-admin', WP_AI_OS_URL . 'assets/js/admin.js', array(), WP_AI_OS_VERSION, true );
		wp_localize_script( 'wp-ai-os-admin', 'WP_AI_OS_Admin', array( 'restUrl' => esc_url_raw( rest_url( 'wp-ai-os/v1' ) ), 'nonce' => wp_create_nonce( 'wp_rest' ) ) );
	}
}

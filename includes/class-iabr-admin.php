<?php
/**
 * Settings screen under Settings.
 *
 * @package In_App_Browser_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin settings page and plugin links.
 */
final class IABR_Admin {

	const PAGE        = 'iabr-settings';
	const AUTHOR_URL  = 'https://bealinawaz.com/';
	const CONTACT_URL = 'https://bealinawaz.com/contact-me/';
	const DONATE_URL  = 'https://www.buymeacoffee.com/bealinawaz';

	/**
	 * Registers hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_setting' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( IABR_FILE ), array( __CLASS__, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'row_meta' ), 10, 2 );
	}

	/**
	 * Adds the settings page under Settings.
	 */
	public static function add_page() {
		add_options_page(
			__( 'In-App Browser Redirect', 'in-app-browser-redirect' ),
			__( 'In-App Browser', 'in-app-browser-redirect' ),
			'manage_options',
			self::PAGE,
			array( __CLASS__, 'render' )
		);
	}

	/**
	 * Registers the settings option.
	 */
	public static function register_setting() {
		register_setting(
			IABR_Settings::GROUP,
			IABR_Settings::OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'IABR_Settings', 'sanitize' ),
			)
		);
	}

	/**
	 * Loads the admin styles on the settings page.
	 *
	 * @param string $hook Current admin page.
	 */
	public static function enqueue( $hook ) {
		if ( 'settings_page_' . self::PAGE !== $hook ) {
			return;
		}
		wp_enqueue_style( 'iabr-admin', plugin_dir_url( IABR_FILE ) . 'assets/admin.css', array(), IABR_VERSION );
	}

	/**
	 * Adds a Settings link on the Plugins screen.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public static function action_links( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=' . self::PAGE ) ) . '">' . esc_html__( 'Settings', 'in-app-browser-redirect' ) . '</a>' );
		return $links;
	}

	/**
	 * Adds custom development and donation links under the plugin description.
	 *
	 * @param array  $links Plugin meta links.
	 * @param string $file  Plugin file.
	 * @return array
	 */
	public static function row_meta( $links, $file ) {
		if ( plugin_basename( IABR_FILE ) !== $file ) {
			return $links;
		}
		$links[] = '<a href="' . esc_url( self::CONTACT_URL ) . '" target="_blank" rel="noopener">' . esc_html__( 'Custom development', 'in-app-browser-redirect' ) . '</a>';
		$links[] = '<a href="' . esc_url( self::DONATE_URL ) . '" target="_blank" rel="noopener">' . esc_html__( 'Buy me a coffee', 'in-app-browser-redirect' ) . '</a>';
		return $links;
	}

	/**
	 * Settings field name.
	 *
	 * @param string $key Setting key.
	 * @return string
	 */
	private static function name( $key ) {
		return IABR_Settings::OPTION . '[' . $key . ']';
	}

	/**
	 * Renders the settings page.
	 */
	public static function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$s = IABR_Settings::get();
		?>
		<div class="wrap iabr-settings">
			<h1><?php esc_html_e( 'In-App Browser Redirect', 'in-app-browser-redirect' ); ?></h1>
			<p><?php esc_html_e( 'Links opened inside Facebook, Instagram, TikTok, and other apps load in a built-in browser that often breaks logins, payments, cookies, and analytics. This plugin moves those visitors to their real browser.', 'in-app-browser-redirect' ); ?></p>

			<form action="options.php" method="post">
				<?php settings_fields( IABR_Settings::GROUP ); ?>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Status', 'in-app-browser-redirect' ); ?></th>
						<td><label><input type="checkbox" name="<?php echo esc_attr( self::name( 'enabled' ) ); ?>" value="1" <?php checked( $s['enabled'] ); ?> /> <?php esc_html_e( 'Enable the redirect and banner', 'in-app-browser-redirect' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Apps', 'in-app-browser-redirect' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php esc_html_e( 'Apps', 'in-app-browser-redirect' ); ?></legend>
								<?php foreach ( IABR_Settings::apps() as $key => $label ) : ?>
									<label class="iabr-app"><input type="checkbox" name="<?php echo esc_attr( self::name( 'apps' ) ); ?>[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $s['apps'], true ) ); ?> /> <?php echo esc_html( $label ); ?></label>
								<?php endforeach; ?>
							</fieldset>
							<p class="description"><?php esc_html_e( 'Act on visitors who open your links inside these apps. X (Twitter) is usually only detectable on Android, because its iPhone app opens links in a Safari view that identifies itself as Safari.', 'in-app-browser-redirect' ); ?></p>
						</td>
					</tr>
				</tbody></table>

				<h2><?php esc_html_e( 'Android', 'in-app-browser-redirect' ); ?></h2>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Behavior', 'in-app-browser-redirect' ); ?></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text"><?php esc_html_e( 'Behavior', 'in-app-browser-redirect' ); ?></legend>
								<label><input type="radio" name="<?php echo esc_attr( self::name( 'android_mode' ) ); ?>" value="auto" <?php checked( $s['android_mode'], 'auto' ); ?> /> <?php esc_html_e( 'Open the page in Chrome automatically', 'in-app-browser-redirect' ); ?></label><br />
								<label><input type="radio" name="<?php echo esc_attr( self::name( 'android_mode' ) ); ?>" value="button" <?php checked( $s['android_mode'], 'button' ); ?> /> <?php esc_html_e( 'Show a banner with an "Open in Chrome" button', 'in-app-browser-redirect' ); ?></label>
							</fieldset>
							<p class="description"><?php esc_html_e( 'The automatic redirect runs once per page and tab. If Chrome is not installed, the page stays open and the banner is shown instead, so visitors never get stuck in a loop.', 'in-app-browser-redirect' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="iabr-android-text"><?php esc_html_e( 'Banner text', 'in-app-browser-redirect' ); ?></label></th>
						<td><input id="iabr-android-text" class="large-text" type="text" name="<?php echo esc_attr( self::name( 'android_text' ) ); ?>" value="<?php echo esc_attr( $s['android_text'] ); ?>" /></td>
					</tr>
				</tbody></table>

				<h2><?php esc_html_e( 'iPhone and iPad', 'in-app-browser-redirect' ); ?></h2>
				<p><?php esc_html_e( 'iOS does not let a website open Safari from an in-app browser, so visitors see a short hint and a "Copy link" button instead.', 'in-app-browser-redirect' ); ?></p>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><?php esc_html_e( 'Banner', 'in-app-browser-redirect' ); ?></th>
						<td><label><input type="checkbox" name="<?php echo esc_attr( self::name( 'ios_banner' ) ); ?>" value="1" <?php checked( $s['ios_banner'] ); ?> /> <?php esc_html_e( 'Show the "open in Safari" banner', 'in-app-browser-redirect' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="iabr-ios-text"><?php esc_html_e( 'Banner text', 'in-app-browser-redirect' ); ?></label></th>
						<td><textarea id="iabr-ios-text" class="large-text" rows="2" name="<?php echo esc_attr( self::name( 'ios_text' ) ); ?>"><?php echo esc_textarea( $s['ios_text'] ); ?></textarea></td>
					</tr>
				</tbody></table>

				<h2><?php esc_html_e( 'Exclusions', 'in-app-browser-redirect' ); ?></h2>
				<table class="form-table" role="presentation"><tbody>
					<tr>
						<th scope="row"><label for="iabr-exclude-ids"><?php esc_html_e( 'Post or page IDs', 'in-app-browser-redirect' ); ?></label></th>
						<td>
							<input id="iabr-exclude-ids" class="regular-text" type="text" name="<?php echo esc_attr( self::name( 'exclude_ids' ) ); ?>" value="<?php echo esc_attr( implode( ', ', $s['exclude_ids'] ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Comma-separated, for example 12, 48.', 'in-app-browser-redirect' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="iabr-exclude-paths"><?php esc_html_e( 'Paths', 'in-app-browser-redirect' ); ?></label></th>
						<td>
							<textarea id="iabr-exclude-paths" class="large-text code" rows="4" name="<?php echo esc_attr( self::name( 'exclude_paths' ) ); ?>" placeholder="/landing-page/"><?php echo esc_textarea( implode( "\n", $s['exclude_paths'] ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'One per line. A path also excludes everything below it, so /shop/ covers /shop/shirts/.', 'in-app-browser-redirect' ); ?></p>
						</td>
					</tr>
				</tbody></table>

				<?php submit_button(); ?>
			</form>

			<h2><?php esc_html_e( 'Test it', 'in-app-browser-redirect' ); ?></h2>
			<p>
				<?php esc_html_e( 'Detection runs in the visitor\'s browser, so it works with page caching. To preview the banner in any browser, add a test parameter to a page address:', 'in-app-browser-redirect' ); ?>
				<a href="<?php echo esc_url( add_query_arg( 'iabr_test', 'ios', home_url( '/' ) ) ); ?>" target="_blank" rel="noopener"><code>?iabr_test=ios</code></a>
				<?php esc_html_e( 'or', 'in-app-browser-redirect' ); ?>
				<a href="<?php echo esc_url( add_query_arg( 'iabr_test', 'android', home_url( '/' ) ) ); ?>" target="_blank" rel="noopener"><code>?iabr_test=android</code></a>.
				<?php esc_html_e( 'Test mode only shows the banner and never redirects. For a real test, post a link in a Facebook or Instagram message to yourself and open it on your phone. Close the banner to hide it for the rest of the session.', 'in-app-browser-redirect' ); ?>
			</p>

			<?php self::render_support(); ?>
		</div>
		<?php
	}

	/**
	 * Renders the author box with support, custom work, and donation links.
	 */
	private static function render_support() {
		?>
		<div class="iabr-support">
			<h2><?php esc_html_e( 'Need help or a custom version?', 'in-app-browser-redirect' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: %s: link to the author's website. */
					esc_html__( 'In-App Browser Redirect is free and built by Ali Nawaz, a WordPress and WooCommerce developer at %s. Need landing pages that convert social traffic, checkout fixes for in-app browsers, or tracking that survives them?', 'in-app-browser-redirect' ),
					'<a href="' . esc_url( self::AUTHOR_URL ) . '" target="_blank" rel="noopener">bealinawaz.com</a>'
				);
				?>
			</p>
			<p class="iabr-support__actions">
				<a class="button button-primary" href="<?php echo esc_url( self::CONTACT_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Hire me for custom work', 'in-app-browser-redirect' ); ?></a>
				<a class="button" href="<?php echo esc_url( self::AUTHOR_URL ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Visit bealinawaz.com', 'in-app-browser-redirect' ); ?></a>
				<a class="button" href="<?php echo esc_url( self::DONATE_URL ); ?>" target="_blank" rel="noopener">&#9749; <?php esc_html_e( 'Buy me a coffee', 'in-app-browser-redirect' ); ?></a>
			</p>
		</div>
		<?php
	}
}

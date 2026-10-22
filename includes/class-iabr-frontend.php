<?php
/**
 * Front-end script loading.
 *
 * @package In_App_Browser_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * Loads the detection script. Detection runs in the browser, so pages stay cacheable.
 */
final class IABR_Frontend {

	/**
	 * Registers hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	/**
	 * Whether the current request is excluded in the settings.
	 *
	 * @param array $settings Settings.
	 * @return bool
	 */
	private static function is_excluded( $settings ) {
		if ( $settings['exclude_ids'] ) {
			$id = (int) get_queried_object_id();
			if ( $id && ( is_singular() || is_home() ) && in_array( $id, $settings['exclude_ids'], true ) ) {
				return true;
			}
		}
		if ( $settings['exclude_paths'] && isset( $_SERVER['REQUEST_URI'] ) ) {
			$path = IABR_Settings::normalize_path( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
			$home = IABR_Settings::normalize_path( home_url( '/' ) );
			$path = untrailingslashit( $path );
			$home = untrailingslashit( $home );
			foreach ( $settings['exclude_paths'] as $excluded ) {
				$excluded = untrailingslashit( $excluded );
				if ( '' === $excluded ) {
					// "/" excludes only the front page.
					if ( $path === $home ) {
						return true;
					}
					continue;
				}
				foreach ( array_unique( array( $excluded, $home . $excluded ) ) as $candidate ) {
					if ( $path === $candidate || 0 === strpos( $path, $candidate . '/' ) ) {
						return true;
					}
				}
			}
		}

		/**
		 * Filters whether the in-app browser script is skipped on the current page.
		 *
		 * @param bool  $excluded Whether the page is excluded.
		 * @param array $settings Plugin settings.
		 */
		return (bool) apply_filters( 'iabr_is_excluded', false, $settings );
	}

	/**
	 * Enqueues the script on front-end pages when enabled.
	 */
	public static function enqueue() {
		$settings = IABR_Settings::get();
		if ( ! $settings['enabled'] || ! $settings['apps'] || is_feed() || is_embed() || self::is_excluded( $settings ) ) {
			return;
		}
		$url = plugin_dir_url( IABR_FILE ) . 'assets/';
		wp_enqueue_script( 'iabr-redirect', $url . 'redirect.js', array(), IABR_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );

		$config = array(
			'apps'        => $settings['apps'],
			'androidMode' => $settings['android_mode'],
			'iosBanner'   => (bool) $settings['ios_banner'],
			'css'         => $url . 'banner.css?ver=' . IABR_VERSION,
			'text'        => array(
				'android' => $settings['android_text'],
				'ios'     => $settings['ios_text'],
				'region'  => __( 'Open in your browser', 'in-app-browser-redirect' ),
				'open'    => __( 'Open in Chrome', 'in-app-browser-redirect' ),
				'copy'    => __( 'Copy link', 'in-app-browser-redirect' ),
				'copied'  => __( 'Link copied. Paste it into Safari.', 'in-app-browser-redirect' ),
				'close'   => __( 'Close', 'in-app-browser-redirect' ),
			),
		);
		wp_add_inline_script( 'iabr-redirect', 'window.iabrConfig = ' . wp_json_encode( $config ) . ';', 'before' );
	}
}

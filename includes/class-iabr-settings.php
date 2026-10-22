<?php
/**
 * Plugin settings.
 *
 * @package In_App_Browser_Redirect
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reads and validates the plugin settings.
 */
final class IABR_Settings {

	const OPTION = 'iabr_settings';
	const GROUP  = 'iabr';

	/**
	 * Supported in-app browsers, keyed by app ID.
	 *
	 * @return array
	 */
	public static function apps() {
		return array(
			'facebook'  => __( 'Facebook', 'in-app-browser-redirect' ),
			'messenger' => __( 'Messenger', 'in-app-browser-redirect' ),
			'instagram' => __( 'Instagram', 'in-app-browser-redirect' ),
			'linkedin'  => __( 'LinkedIn', 'in-app-browser-redirect' ),
			'tiktok'    => __( 'TikTok', 'in-app-browser-redirect' ),
			'snapchat'  => __( 'Snapchat', 'in-app-browser-redirect' ),
			'line'      => __( 'LINE', 'in-app-browser-redirect' ),
			'twitter'   => __( 'X (Twitter)', 'in-app-browser-redirect' ),
		);
	}

	/**
	 * Default settings.
	 *
	 * @return array
	 */
	public static function defaults() {
		return array(
			'enabled'       => true,
			'apps'          => array_keys( self::apps() ),
			'android_mode'  => 'auto',
			'android_text'  => __( 'This page works best in your browser.', 'in-app-browser-redirect' ),
			'ios_banner'    => true,
			'ios_text'      => __( 'For the best experience, open this page in Safari: tap the ••• or share button, then choose "Open in browser".', 'in-app-browser-redirect' ),
			'exclude_ids'   => array(),
			'exclude_paths' => array(),
		);
	}

	/**
	 * Current settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get() {
		$saved = get_option( self::OPTION, array() );
		$s     = array_replace( self::defaults(), is_array( $saved ) ? $saved : array() );

		$s['apps']          = array_values( array_intersect( (array) $s['apps'], array_keys( self::apps() ) ) );
		$s['exclude_ids']   = array_values( array_filter( array_map( 'absint', (array) $s['exclude_ids'] ) ) );
		$s['exclude_paths'] = array_values( array_filter( array_map( 'strval', (array) $s['exclude_paths'] ) ) );
		if ( '' === trim( (string) $s['android_text'] ) ) {
			$s['android_text'] = self::defaults()['android_text'];
		}
		if ( '' === trim( (string) $s['ios_text'] ) ) {
			$s['ios_text'] = self::defaults()['ios_text'];
		}
		return $s;
	}

	/**
	 * Sanitizes the settings form.
	 *
	 * @param mixed $input Submitted values.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();

		$apps = ( isset( $input['apps'] ) && is_array( $input['apps'] ) ) ? array_map( 'sanitize_key', $input['apps'] ) : array();

		// Values arrive as text from the form, or as arrays when WordPress sanitizes an already clean value again.
		$ids = array();
		if ( isset( $input['exclude_ids'] ) ) {
			$raw = is_array( $input['exclude_ids'] ) ? $input['exclude_ids'] : preg_split( '/[\s,]+/', (string) $input['exclude_ids'] );
			$ids = array_values( array_unique( array_filter( array_map( 'absint', $raw ) ) ) );
		}

		$paths = array();
		if ( isset( $input['exclude_paths'] ) ) {
			$raw = is_array( $input['exclude_paths'] ) ? $input['exclude_paths'] : preg_split( '/[\r\n]+/', (string) $input['exclude_paths'] );
			foreach ( $raw as $line ) {
				$path = self::normalize_path( $line );
				if ( '' !== $path ) {
					$paths[] = $path;
				}
			}
		}

		return array(
			'enabled'       => ! empty( $input['enabled'] ),
			'apps'          => array_values( array_intersect( array_keys( self::apps() ), $apps ) ),
			'android_mode'  => ( isset( $input['android_mode'] ) && 'button' === $input['android_mode'] ) ? 'button' : 'auto',
			'android_text'  => isset( $input['android_text'] ) ? sanitize_text_field( $input['android_text'] ) : '',
			'ios_banner'    => ! empty( $input['ios_banner'] ),
			'ios_text'      => isset( $input['ios_text'] ) ? sanitize_text_field( $input['ios_text'] ) : '',
			'exclude_ids'   => $ids,
			'exclude_paths' => array_values( array_unique( $paths ) ),
		);
	}

	/**
	 * Turns a URL or path into a path starting with a slash, without query string.
	 *
	 * @param string $value URL or path.
	 * @return string
	 */
	public static function normalize_path( $value ) {
		$value = trim( sanitize_text_field( (string) $value ) );
		if ( '' === $value ) {
			return '';
		}
		$path = wp_parse_url( $value, PHP_URL_PATH );
		$path = is_string( $path ) ? $path : '';
		if ( '' === $path ) {
			return '';
		}
		return '/' . ltrim( $path, '/' );
	}
}

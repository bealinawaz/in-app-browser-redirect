<?php
/**
 * Plugin Name:       In-App Browser Redirect – Open Social Media Links in Chrome or Safari
 * Plugin URI:        https://github.com/bealinawaz/in-app-browser-redirect
 * Description:       Moves visitors out of the Facebook, Instagram, Messenger, TikTok, and other in-app browsers: Android visitors are sent to Chrome, and iPhone visitors see how to open the page in Safari.
 * Version:           2.0.0
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Author:            Ali Nawaz
 * Author URI:        https://bealinawaz.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       in-app-browser-redirect
 *
 * @package In_App_Browser_Redirect
 */

defined( 'ABSPATH' ) || exit;

define( 'IABR_VERSION', '2.0.0' );
define( 'IABR_FILE', __FILE__ );

require_once __DIR__ . '/includes/class-iabr-settings.php';
require_once __DIR__ . '/includes/class-iabr-frontend.php';

IABR_Frontend::init();

if ( is_admin() ) {
	require_once __DIR__ . '/includes/class-iabr-admin.php';
	IABR_Admin::init();
}

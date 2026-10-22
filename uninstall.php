<?php
/**
 * Removes the plugin settings.
 *
 * @package In_App_Browser_Redirect
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'iabr_settings' );

<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.storeprose.com
 * @since      1.0.0
 *
 * @package    Ps_Hpacb
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete the plugin options.
$checked_value = 'on';
$options       = get_option( 'ps_hpacb' );

if ( isset( $options['delete_options'] ) && $options['delete_options'] === $checked_value ) {
	delete_option( 'ps_hpacb' );
	$meta_names = array( '_ps_hpacb_price', '_ps_hpacb_btn' );
	foreach ( $meta_names as $meta_key ) {
		delete_metadata( 'post', 0, $meta_key, '', true );
	}
}

delete_option( 'ps_hpacb-no-bug' );
delete_option( 'ps_hpacb-activation-date' );

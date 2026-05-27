<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Real3DFlipbook
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete global options.
delete_option( 'real3dflipbook_global' );
delete_option( 'real3dflipbook_capability' );
delete_option( 'r3d_version' );
delete_option( 'r3d_flush_rewrite_rules' );

$r3d_posts = get_posts(
	array(
		'post_type'      => 'r3d',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	)
);

foreach ( $r3d_posts as $r3d_post_id ) {
	wp_delete_post( $r3d_post_id, true );
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching -- uninstall cleanup runs once; caching is pointless.

$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'real3dflipbook_last_page_%'" );

$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_flipbook_pdf_%' OR option_name LIKE '_transient_timeout_flipbook_pdf_%'" );

// phpcs:enable WordPress.DB.DirectDatabaseQuery.NoCaching

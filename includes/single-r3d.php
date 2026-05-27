<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	get_header();
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress core function returns safe HTML.
	get_footer();
	return;
}

if ( ! function_exists( 'r3d_user_has_woo_subscription' ) ) {
	function r3d_user_has_woo_subscription() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$user_id = get_current_user_id();
		if ( 0 === $user_id ) {
			return false;
		}

		if ( function_exists( 'wcs_user_has_subscription' ) ) {
			$has_active_subscription               = wcs_user_has_subscription( $user_id, '', 'active' );
			$has_pending_cancellation_subscription = wcs_user_has_subscription( $user_id, '', 'pending-cancel' );

			return $has_active_subscription || $has_pending_cancellation_subscription;
		}

		return false;
	}
}

$show_flipbook = true;

$r3d_post_id = (int) get_the_ID();
$flipbook    = r3d_get_flipbook( $r3d_post_id );
if ( ! is_array( $flipbook ) ) {
	$flipbook = array();
}
$flipbook_global_options = get_option( 'real3dflipbook_global', array() );
$flipbook                = array_merge( $flipbook_global_options, $flipbook );

if ( isset( $flipbook['access'] ) ) {

	if ( $flipbook['access'] === 'woo_subscription' ) {

		$show_flipbook = r3d_user_has_woo_subscription();

	} elseif ( $flipbook['access'] === 'none' ) {

		$show_flipbook = false;
	}
}

if ( $show_flipbook ) {

	if ( isset( $flipbook['mode'] ) && $flipbook['mode'] === 'fullscreen' ) {
		// Inline CSS to hide common header and footer selectors
		echo '<style>
				#header, .header, #footer, .footer,
				.site-header, #site-header, .main-header, #main-header,
				.top-header, #top-header, .page-header, #masthead,
				.site-footer, #site-footer, .main-footer, #main-footer,
				.bottom-footer, #bottom-footer, .page-footer, #colophon {
					display: none;
				}
			</style>
			';
	}
	get_header();
	echo do_shortcode( '[real3dflipbook id="' . esc_attr( $r3d_post_id ) . '"]' );
	get_footer();
} else {

	esc_html_e( 'Forbidden', 'real3d-flipbook' );
}

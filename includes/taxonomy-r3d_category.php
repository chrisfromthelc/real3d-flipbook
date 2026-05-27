<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>
<div style="text-align: center;">
	<?php
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
				return wcs_user_has_subscription( $user_id, '', 'active' );
			}
			return false;
		}
	}

	$query_args = array(
		'post_type'   => 'r3d',
		'post_status' => 'publish',
		'tax_query'   => array(
			array(
				'taxonomy' => 'r3d_category',
				'field'    => 'term_id',
				'terms'    => intval( get_queried_object_id() ),
			),
		),
	);

	$query = new WP_Query( $query_args );

	$flipbook_global_options = get_option( 'real3dflipbook_global', array() );

	while ( $query->have_posts() ) {

		$query->the_post();
		$post_id  = (int) get_the_ID();
		$flipbook = r3d_get_flipbook( $post_id );
		$flipbook = r3d_array_merge_deep( $flipbook_global_options, is_array( $flipbook ) ? $flipbook : array() );

		$show_flipbook = true;

		if ( isset( $flipbook['access'] ) ) {

			if ( $flipbook['access'] === 'woo_subscription' ) {

				$show_flipbook = r3d_user_has_woo_subscription();

			} elseif ( $flipbook['access'] === 'none' ) {

				$show_flipbook = false;
			}
		}

		if ( $show_flipbook ) {

			$shortcode = '[real3dflipbook id="' . esc_attr( $post_id ) . '" mode="lightbox"]';

			echo do_shortcode( $shortcode );
		}
	}

	wp_reset_postdata();

	?>
</div>
<?php

get_footer();

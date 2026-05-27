<?php
/**
 * Flipbook data abstraction layer.
 *
 * All flipbook config is stored as `r3d_flipbook_options` post meta
 * on the `r3d` custom post type. The WordPress post_id IS the flipbook ID.
 *
 * @package Real3DFlipbook
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function r3d_get_flipbook( int $post_id ): ?array {
	$post = get_post( $post_id );
	if ( ! $post || 'r3d' !== $post->post_type ) {
		return null;
	}

	$options = get_post_meta( $post_id, 'r3d_flipbook_options', true );

	if ( ! is_array( $options ) ) {
		$options = r3d_migrate_legacy_flipbook( $post_id );
		if ( ! is_array( $options ) ) {
			return null;
		}
	}

	$options['id']      = $post_id;
	$options['post_id'] = $post_id;

	return $options;
}

/**
 * Migrate a flipbook from the legacy wp_options storage to post meta.
 *
 * Looks up the old flipbook_id post meta, reads the corresponding
 * real3dflipbook_{id} option, writes it to r3d_flipbook_options post meta,
 * and cleans up the old data.
 *
 * @param int $post_id The post ID to migrate.
 * @return array|null Migrated flipbook data, or null if no legacy data found.
 */
function r3d_migrate_legacy_flipbook( int $post_id ): ?array {
	$legacy_id = get_post_meta( $post_id, 'flipbook_id', true );
	if ( empty( $legacy_id ) ) {
		return null;
	}

	$legacy_id = intval( $legacy_id );
	$options   = get_option( 'real3dflipbook_' . $legacy_id );
	if ( ! is_array( $options ) ) {
		return null;
	}

	$options['id']      = $post_id;
	$options['post_id'] = $post_id;
	$options['name']    = get_the_title( $post_id );

	update_post_meta( $post_id, 'r3d_flipbook_options', $options );

	delete_post_meta( $post_id, 'flipbook_id' );
	delete_option( 'real3dflipbook_' . $legacy_id );

	return $options;
}

function r3d_save_flipbook( int $post_id, string $title, array $options ): bool {
	$post = get_post( $post_id );
	if ( ! $post || 'r3d' !== $post->post_type ) {
		return false;
	}

	$existing = get_post_meta( $post_id, 'r3d_flipbook_options', true );
	if ( is_array( $existing ) && isset( $existing['notes'] ) && ! isset( $options['notes'] ) ) {
		$options['notes'] = $existing['notes'];
	}

	$options['name']    = sanitize_text_field( $title );
	$options['post_id'] = $post_id;
	$options['id']      = $post_id;

	$webgl    = isset( $options['webgl'] ) ? $options['webgl'] : false;
	$viewMode = isset( $options['viewMode'] ) ? $options['viewMode'] : null;

	$options['viewMode'] = ! $webgl ? $viewMode : $webgl;

	if ( ! $options['viewMode'] ) {
		unset( $options['viewMode'] );
	}

	update_post_meta( $post_id, 'r3d_flipbook_options', $options );

	return true;
}

function r3d_delete_flipbook_data( int $post_id ): void {
	delete_post_meta( $post_id, 'r3d_flipbook_options' );
}

function r3d_get_all_flipbooks( array $args = array() ): array {
	$defaults = array(
		'post_type'      => 'r3d',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	$query_args = array_merge( $defaults, $args );
	$posts      = get_posts( $query_args );
	$flipbooks  = array();

	foreach ( $posts as $post ) {
		$options = r3d_get_flipbook( $post->ID );
		if ( $options ) {
			$flipbooks[ $post->ID ] = $options;
		}
	}

	return $flipbooks;
}

function r3d_resolve_flipbook_by_name( string $name ): ?array {
	$posts = get_posts(
		array(
			'post_type'      => 'r3d',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
			'title'          => $name,
		)
	);

	if ( empty( $posts ) ) {
		return null;
	}

	return r3d_get_flipbook( $posts[0]->ID );
}

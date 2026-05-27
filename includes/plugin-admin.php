<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$r3d_globals_settings = get_option( 'real3dflipbook_global' );

if ( ! $r3d_globals_settings ) {
	r3dfb_setDefaults();
}

function r3dfb_setDefaults() {
	$defaults = r3dfb_getDefaults();
	delete_option( 'real3dflipbook_global' );
	add_option( 'real3dflipbook_global', $defaults );
}

function r3d_sanitize_array( $input ) {
	foreach ( $input as $key => $value ) {
		if ( is_array( $value ) ) {
			$input[ $key ] = r3d_sanitize_array( $value );
		} else {
			$input[ $key ] = sanitize_text_field( $value );
		}
	}
	return $input;
}

add_action( 'wp_ajax_r3d_save_general', 'r3d_save_general_callback' );

function r3d_save_general_callback() {

	check_ajax_referer( 'r3d_nonce', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'real3d-flipbook' ), '', array( 'response' => 403 ) );
	}

	unset( $_POST['security'], $_POST['action'] );

	$allowed_keys = array_keys( r3dfb_getDefaults() );
	$allowed_keys = array_merge( $allowed_keys, array( 'slug', 'manageFlipbooks', 'wooShowThankyouFlipbook' ) );
	$allowed_keys = array_flip( $allowed_keys );
	$data         = array_intersect_key( $_POST, $allowed_keys );
	$data         = r3d_sanitize_array( $data );

	if ( isset( $data['slug'] ) && ( get_option( 'real3dflipbook_global' )['slug'] ?? '' ) !== $data['slug'] ) {
		update_option( 'r3d_flush_rewrite_rules', true );
	}

	if ( isset( $data['wooShowThankyouFlipbook'] ) ) {
		update_option( 'r3d_woo_show_thankyou_flipbook', 'true' === $data['wooShowThankyouFlipbook'] );
		unset( $data['wooShowThankyouFlipbook'] );
	} else {
		update_option( 'r3d_woo_show_thankyou_flipbook', false );
	}

	if ( isset( $data['manageFlipbooks'] ) ) {

		$role = sanitize_text_field( $data['manageFlipbooks'] );

		$capability_map = array(
			'Administrator' => 'manage_options',
			'Shop Manager'  => 'manage_woocommerce',
			'Editor'        => 'publish_pages',
			'Author'        => 'edit_others_posts',
		);

		$capability = $capability_map[ $role ] ?? 'edit_others_posts';

		update_option( 'real3dflipbook_capability', $capability );
		unset( $data['manageFlipbooks'] );
	}

	update_option( 'real3dflipbook_global', $data );

	wp_die();
}

add_action( 'wp_ajax_r3d_reset_general', 'r3d_reset_general_callback' );

function r3d_reset_general_callback() {

	check_ajax_referer( 'r3d_nonce', 'security' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'real3d-flipbook' ), '', array( 'response' => 403 ) );
	}

	r3dfb_setDefaults();

	wp_die();
}


add_action( 'wp_ajax_r3d_save_thumbnail', 'r3dfb_save_thumbnail_callback' );

function r3dfb_save_thumbnail_callback() {
	// Security & permission
	check_ajax_referer( 'saving-real3d-flipbook', 'security' );
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'You do not have permission to upload files.', 'real3d-flipbook' ) ) );
	}

	// Flipbook ID check
	$id = isset( $_POST['id'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['id'] ) ) ) : 0;
	if ( $id <= 0 ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid flipbook ID.', 'real3d-flipbook' ) ) );
	}

	$book = r3d_get_flipbook( $id );
	if ( ! $book ) {
		wp_send_json_error( array( 'message' => esc_html__( 'The specified flipbook does not exist.', 'real3d-flipbook' ) ) );
	}

	// Upload paths
	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Upload directory error: ', 'real3d-flipbook' ) . esc_html( $upload_dir['error'] ) ) );
	}

	$book_folder = $upload_dir['basedir'] . "/real3d-flipbook/flipbook_{$id}/";
	$book_url    = $upload_dir['baseurl'] . "/real3d-flipbook/flipbook_{$id}/";

	// Fix 2: Use WP_Filesystem instead of raw mkdir() / is_writable().
	global $wp_filesystem;
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
	}

	if ( ! $wp_filesystem->is_dir( $book_folder ) && ! $wp_filesystem->mkdir( $book_folder, FS_CHMOD_DIR ) ) {
		wp_send_json_error( array( 'message' => esc_html( sprintf( __( 'Cannot create folder: %s', 'real3d-flipbook' ), $book_folder ) ) ) );
	}
	if ( ! $wp_filesystem->is_writable( $book_folder ) ) {
		wp_send_json_error( array( 'message' => esc_html( sprintf( __( 'Cannot write to folder: %s', 'real3d-flipbook' ), $book_folder ) ) ) );
	}

	// Fix 1: Replace empty(tmp_name) check with proper upload-error handling.
	if ( ! isset( $_FILES['file'] ) || ! is_uploaded_file( $_FILES['file']['tmp_name'] ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'No file uploaded.', 'real3d-flipbook' ) ) );
	}
	if ( UPLOAD_ERR_OK !== $_FILES['file']['error'] ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Upload error occurred.', 'real3d-flipbook' ) ) );
	}

	// File size (2MB)
	if ( $_FILES['file']['size'] > 2 * 1024 * 1024 ) {
		wp_send_json_error( array( 'message' => esc_html__( 'File size exceeds the maximum limit.', 'real3d-flipbook' ) ) );
	}

	// Extension & image check
	$allowed_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
	$filename           = sanitize_file_name( $_FILES['file']['name'] );
	$extension          = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	if ( ! in_array( $extension, $allowed_extensions ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid file extension.', 'real3d-flipbook' ) ) );
	}
	if ( getimagesize( $_FILES['file']['tmp_name'] ) === false ) {
		wp_send_json_error( array( 'message' => esc_html__( 'File is not a valid image.', 'real3d-flipbook' ) ) );
	}

	// Unique filename
	$hashed_filename = 'thumbnail_' . time() . '_' . wp_generate_password( 4, false, false ) . '.' . $extension;
	$destination     = $book_folder . $hashed_filename;
	$counter         = 0;
	while ( file_exists( $destination ) ) {
		++$counter;
		$hashed_filename = 'thumbnail_' . time() . '_' . wp_generate_password( 4, false, false ) . "_{$counter}." . $extension;
		$destination     = $book_folder . $hashed_filename;
	}

	// Move file
	if ( ! move_uploaded_file( $_FILES['file']['tmp_name'], $destination ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Failed to save the uploaded file.', 'real3d-flipbook' ) ) );
	}
	$thumbnail_url = $book_url . $hashed_filename;

	// Fix 3 & 4: Replace @unlink() with wp_delete_file() and add realpath path-traversal protection.
	if ( ! empty( $book['lightboxThumbnailUrl'] ) ) {
		$old_file      = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $book['lightboxThumbnailUrl'] );
		$real_old_file = realpath( $old_file );
		$real_uploads  = realpath( $upload_dir['basedir'] );
		if ( false !== $real_old_file && false !== $real_uploads
			&& strpos( $real_old_file, $real_uploads . DIRECTORY_SEPARATOR ) === 0 ) {
			wp_delete_file( $real_old_file );
		}
	}

	// Save option
	$book['lightboxThumbnailUrl'] = esc_url( $thumbnail_url );
	r3d_save_flipbook( $id, $book['name'] ?? '', $book );

	wp_send_json_success( array( 'thumbnail_url' => $thumbnail_url ) );
}

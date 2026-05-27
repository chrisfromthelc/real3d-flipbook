<?php

class R3D_Post_Type {


	public static $instance;

	public $main;

	public function __construct() {

		$this->main = Real3DFlipbook::get_instance();

		$real3dflipbook_global = $this->main->getFlipbookGlobal();

		$rewriteSlug = 'flipbook';

		if ( ! empty( $real3dflipbook_global['slug'] ) ) {
			$rewriteSlug = sanitize_title( $real3dflipbook_global['slug'] );
		}

		register_post_type(
			'r3d',
			array(
				'labels'              => array(
					'name'               => esc_html__( 'Real3D Flipbook', 'real3d-flipbook' ),
					'singular_name'      => esc_html__( 'Real3D Flipbook', 'real3d-flipbook' ),
					'menu_name'          => esc_html__( 'Real3D Flipbook', 'real3d-flipbook' ),
					'name_admin_bar'     => esc_html__( 'Real3D Flipbook', 'real3d-flipbook' ),
					'add_new'            => esc_html__( 'Add New', 'real3d-flipbook' ),
					'add_new_item'       => esc_html__( 'Add New Flipbook', 'real3d-flipbook' ),
					'new_item'           => esc_html__( 'New Book', 'real3d-flipbook' ),
					'edit_item'          => esc_html__( 'Edit Book', 'real3d-flipbook' ),
					'view_item'          => esc_html__( 'View Book', 'real3d-flipbook' ),
					'all_items'          => esc_html__( 'Flipbooks', 'real3d-flipbook' ),
					'search_items'       => esc_html__( 'Search', 'real3d-flipbook' ),
					'parent_item_colon'  => esc_html__( 'Parent Book:', 'real3d-flipbook' ),
					'not_found'          => esc_html__( 'Flipbook Not found.', 'real3d-flipbook' ),
					'not_found_in_trash' => esc_html__( 'Flipbook Not found in Trash.', 'real3d-flipbook' ),
				),
				'description'         => esc_html__( 'Description.', 'real3d-flipbook' ),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'query_var'           => true,
				'rewrite'             => array(
					'slug'       => $rewriteSlug,
					'with_front' => false,
				),
				'capability_type'     => 'post',
				'has_archive'         => true,
				'hierarchical'        => false,
				'menu_position'       => 81,
				'menu_icon'           => 'dashicons-book',
				'supports'            => array( 'title', 'thumbnail', 'slug', 'author' ),
				'exclude_from_search' => true,
			)
		);

		if ( get_option( 'r3d_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'r3d_flush_rewrite_rules', false );
		}

		register_taxonomy(
			'r3d_category',
			'r3d',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Flipbook Categories', 'real3d-flipbook' ),
					'singular_name' => esc_html__( 'Flipbook Category', 'real3d-flipbook' ),
					'search_items'  => esc_html__( 'Search Categories', 'real3d-flipbook' ),
					'all_items'     => esc_html__( 'All Categories', 'real3d-flipbook' ),
					'edit_item'     => esc_html__( 'Edit Categories', 'real3d-flipbook' ),
					'update_item'   => esc_html__( 'Update Category', 'real3d-flipbook' ),
					'add_new_item'  => esc_html__( 'Add New Category', 'real3d-flipbook' ),
					'new_item_name' => esc_html__( 'New Category', 'real3d-flipbook' ),
					'menu_name'     => esc_html__( 'Categories', 'real3d-flipbook' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'rewrite'           => array( 'slug' => 'r3d_category' ),
			)
		);

		register_taxonomy(
			'r3d_author',
			'r3d',
			array(
				'labels'            => array(
					'name'          => esc_html__( 'Flipbook Authors', 'real3d-flipbook' ),
					'singular_name' => esc_html__( 'Flipbook Author', 'real3d-flipbook' ),
					'search_items'  => esc_html__( 'Search Authors', 'real3d-flipbook' ),
					'all_items'     => esc_html__( 'All Authors', 'real3d-flipbook' ),
					'edit_item'     => esc_html__( 'Edit Author', 'real3d-flipbook' ),
					'update_item'   => esc_html__( 'Update Author', 'real3d-flipbook' ),
					'add_new_item'  => esc_html__( 'Add New Author', 'real3d-flipbook' ),
					'new_item_name' => esc_html__( 'New Author', 'real3d-flipbook' ),
					'menu_name'     => esc_html__( 'Authors', 'real3d-flipbook' ),
				),
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'rewrite'           => array( 'slug' => 'r3d_author' ),
				'parent_item'       => null,
				'parent_item_colon' => null,
			)
		);

		if ( is_admin() ) {
			$this->init_admin();
		}
	}

	public function init_admin() {
		add_filter( 'manage_r3d_posts_columns', array( $this, 'r3d_columns' ) );
		add_action( 'manage_r3d_posts_custom_column', array( $this, 'r3d_columns_content' ), 10, 2 );

		add_filter( 'manage_edit-r3d_category_columns', array( $this, 'r3d_cat_columns' ) );
		add_filter( 'manage_r3d_category_custom_column', array( $this, 'r3d_cat_columns_content' ), 10, 3 );

		add_filter( 'post_row_actions', array( $this, 'duplicate_post_link' ), 10, 2 );

		add_action( 'restrict_manage_posts', array( $this, 'add_category_filter_dropdown' ) );
		add_filter( 'parse_query', array( $this, 'filter_posts_by_category' ) );

		add_action( 'admin_action_r3d_duplicate_post', array( $this, 'duplicate_post' ) );

		add_action( 'before_delete_post', array( $this, 'deleted_post' ) );
	}

	public function deleted_post( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'r3d' !== $post->post_type ) {
			return;
		}
		r3d_delete_flipbook_data( $post_id );
	}

	public function custom_actions( $actions ) {

		if ( isset( get_current_screen()->post_type ) && 'r3d' == get_current_screen()->post_type ) {
			unset( $actions['inline hide-if-no-js'] );

			$actions['duplicate'] = '<a href="">Duplicate</a>';
		}

		return $actions;
	}

	public function duplicate_post() {
		if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] ) || ( isset( $_REQUEST['action'] ) && 'r3d_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
			wp_die( esc_html__( 'No post to duplicate has been supplied!', 'real3d-flipbook' ) );
		}

		if ( ! isset( $_GET['duplicate_nonce'] ) ) {
			return;
		}

		$duplicate_nonce = sanitize_text_field( wp_unslash( $_GET['duplicate_nonce'] ) );

		if ( ! wp_verify_nonce( $duplicate_nonce, basename( __FILE__ ) ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'You do not have permission to duplicate posts.', 'real3d-flipbook' ) );
		}

		$post_id = ( isset( $_GET['post'] ) ? absint( $_GET['post'] ) : absint( $_POST['post'] ) );

		$post = get_post( $post_id );

		if ( isset( $post ) && $post != null ) {

			$cloned_options         = r3d_get_flipbook( $post_id );
			$cloned_options         = is_array( $cloned_options ) ? $cloned_options : array();
			$cloned_options['date'] = current_time( 'mysql' );
			$new_title              = $post->post_title . ' (copy)';

			$args = array(
				'post_title'  => $new_title,
				'post_type'   => 'r3d',
				'post_status' => 'publish',
			);

			$new_post_id = wp_insert_post( $args );

			r3d_save_flipbook( $new_post_id, $new_title, $cloned_options );

			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
			}

			wp_safe_redirect( admin_url( 'edit.php?post_type=r3d' ) );
			exit;
		} else {
			wp_die( esc_html__( 'Post creation failed, could not find original post: ', 'real3d-flipbook' ) . esc_html( $post_id ) );
		}
	}

	public function duplicate_post_link( $actions, $post ) {

		if ( current_user_can( 'edit_posts' ) && isset( get_current_screen()->post_type ) && 'r3d' == get_current_screen()->post_type ) {
			$actions['duplicate'] = '<a href="' . wp_nonce_url( 'admin.php?action=r3d_duplicate_post&post=' . $post->ID, basename( __FILE__ ), 'duplicate_nonce' ) . '" title="Duplicate this item" rel="permalink">Duplicate</a>';
		}

		return $actions;
	}



	public function r3d_columns() {

		$columns = array(
			'cb'        => '<input type="checkbox" />',
			'cover'     => esc_html__( 'Cover', 'real3d-flipbook' ),
			'title'     => esc_html__( 'Title', 'real3d-flipbook' ),
			'shortcode' => esc_html__( 'Shortcode', 'real3d-flipbook' ),
			'date'      => esc_html__( 'Date', 'real3d-flipbook' ),
			'author'    => esc_html__( 'Author', 'real3d-flipbook' ),
		);

		return $columns;
	}

	public function r3d_cat_columns( $defaults ) {
		$defaults['shortcode'] = 'Shortcode';
		return $defaults;
	}

	public function r3d_columns_content( $column_name, $post_id ) {

		$post_id = absint( $post_id );

		switch ( $column_name ) {
			case 'shortcode':
				echo '<code>[real3dflipbook id="' . esc_attr( $post_id ) . '"]</code>  <div id="' . esc_attr( $post_id ) . '" class="button-secondary copy-shortcode">Copy</div>';
				break;

			case 'cover':
				$book  = r3d_get_flipbook( $post_id );
				$thumb = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=';
				if ( isset( $book['lightboxThumbnailUrl'] ) ) {
					$thumb = $book['lightboxThumbnailUrl'];
				}
				echo '<div class="thumb" style=";background-image:url(' . esc_url( $thumb ) . ');"><a href="#" class="edit" name="' . esc_attr( $post_id ) . '"></a></div>';
				break;
		}
	}

	public function r3d_cat_columns_content( $c, $column_name, $term_id = '' ) {
		$term = get_term( absint( $term_id ), 'r3d_category' );
		if ( ! $term || is_wp_error( $term ) ) {
			return '';
		}
		$slug = esc_attr( $term->slug );
		return '<code>[real3dflipbook category="' . $slug . '"]</code>   <div id="' . $slug . '" class="button-secondary copy-shortcode">Copy</div>';
	}

	public function add_category_filter_dropdown() {
		global $typenow;

		if ( $typenow == 'r3d' ) {
			$taxonomy      = 'r3d_category';
			$taxonomy_obj  = get_taxonomy( $taxonomy );
			$taxonomy_name = $taxonomy_obj->labels->name;

			$selected = isset( $_GET[ $taxonomy ] ) ? intval( $_GET[ $taxonomy ] ) : '';

			wp_dropdown_categories(
				array(
					'show_option_all' => esc_html__( 'All Categories', 'real3d-flipbook' ),
					'taxonomy'        => $taxonomy,
					'name'            => $taxonomy,
					'orderby'         => 'name',
					'selected'        => $selected,
					'hierarchical'    => true,
					'show_count'      => true,
					'hide_empty'      => false,
				)
			);
		}
	}

	public function filter_posts_by_category( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		global $pagenow;

		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		$taxonomy  = 'r3d_category';

		if ( $pagenow === 'edit.php' && $post_type === 'r3d' && ! empty( $_GET[ $taxonomy ] ) ) {
			$term_id = absint( $_GET[ $taxonomy ] );
			$term    = get_term( $term_id, $taxonomy );

			if ( $term && ! is_wp_error( $term ) ) {
				$query->query_vars[ $taxonomy ] = $term->slug;
			}
		}
	}


	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof R3D_Post_Type ) ) {
			self::$instance = new R3D_Post_Type();
		}

		return self::$instance;
	}
}

$r3d_post_type = R3D_Post_Type::get_instance();

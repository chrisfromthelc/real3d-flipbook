<?php

class Real3DFlipbook {


	public $PLUGIN_VERSION;
	public $PLUGIN_DIR_URL;
	public $PLUGIN_DIR_PATH;

	private static $instance = null;

	protected $pro             = false;
	protected $flipbook_global = null;
	public $products;

	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	protected function __construct() {

		$this->PLUGIN_VERSION  = REAL3D_FLIPBOOK_VERSION;
		$this->PLUGIN_DIR_URL  = plugin_dir_url( REAL3D_FLIPBOOK_FILE );
		$this->PLUGIN_DIR_PATH = plugin_dir_path( REAL3D_FLIPBOOK_FILE );

		$this->pro = true;

		$this->products = array(
			'r3d'      => array( 'name' => 'Real3D Flipbook' ),
			'addons'   => array( 'name' => 'Addons Bundle' ),
			'pefrf'    => array(
				'name'  => 'Page Editor Addon',
				'class' => 'R3D_Page_Editor',
			),
			'ptfrf'    => array(
				'name'  => 'PDF Tools Addon',
				'class' => 'R3D_PDF_Tools',
			),
			'bs'       => array(
				'name'  => 'Bookshelf Addon',
				'class' => 'Bookshelf_Addon',
			),
			'wafrf'    => array(
				'name'  => 'WooCommerce Addon',
				'class' => 'R3D_Woo',
			),
			'eafrf'    => array(
				'name'  => 'Elementor Addon',
				'class' => 'Elementor_Real3D_Flipbook',
			),
			'wpb_r3d'  => array(
				'name'  => 'WPBakery Addon',
				'class' => 'Real3DFlipbook_VCAddon',
			),
			'prev_r3d' => array(
				'name'  => 'Preview Addon',
				'class' => 'R3D_Preview',
			),
		);
		$this->add_actions();
		register_activation_hook( REAL3D_FLIPBOOK_FILE, array( $this, 'activation_hook' ) );
		register_deactivation_hook( REAL3D_FLIPBOOK_FILE, array( $this, 'deactivation_hook' ) );
	}

	public function activation_hook( $network_wide ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- required by register_activation_hook signature.
		update_option( 'r3d_flush_rewrite_rules', true );
	}

	public function deactivation_hook() {
		flush_rewrite_rules();
	}

	public function enqueue_scripts() {

		wp_register_script( 'real3d-flipbook', $this->PLUGIN_DIR_URL . 'js/flipbook.min.js', array(), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-book3', $this->PLUGIN_DIR_URL . 'js/flipbook.book3.min.js', array( 'real3d-flipbook' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-bookswipe', $this->PLUGIN_DIR_URL . 'js/flipbook.swipe.min.js', array( 'real3d-flipbook' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'sweet-alert-2', $this->PLUGIN_DIR_URL . 'js/libs/sweetalert2.all.min.js', array(), $this->PLUGIN_VERSION, true );
		wp_register_style( 'sweet-alert-2', $this->PLUGIN_DIR_URL . 'css/sweetalert2.min.css', array(), $this->PLUGIN_VERSION );

		wp_register_script( 'real3d-flipbook-threejs', $this->PLUGIN_DIR_URL . 'js/libs/three.min.js', array(), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-webgl', $this->PLUGIN_DIR_URL . 'js/flipbook.webgl.min.js', array( 'real3d-flipbook', 'real3d-flipbook-threejs' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-pdfjs', $this->PLUGIN_DIR_URL . 'js/libs/pdf.min.js', array(), $this->PLUGIN_VERSION, true );
		wp_register_script( 'real3d-flipbook-pdfworkerjs', $this->PLUGIN_DIR_URL . 'js/libs/pdf.worker.min.js', array(), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-pdfservice', $this->PLUGIN_DIR_URL . 'js/flipbook.pdfservice.min.js', array(), $this->PLUGIN_VERSION, true );

		! get_option( 'r3d' ) && wp_register_script( 'real3d-flipbook-embed', $this->PLUGIN_DIR_URL . 'js/embed.js', array( 'real3d-flipbook' ), $this->PLUGIN_VERSION, true );

		wp_register_style( 'real3d-flipbook-style', $this->PLUGIN_DIR_URL . 'css/flipbook.min.css', array(), $this->PLUGIN_VERSION );

		if ( isset( $this->flipbook_global['convertPDFLinks'] ) && $this->flipbook_global['convertPDFLinks'] == 'true' ) {
			wp_enqueue_script( 'real3d-flipbook-forntend', $this->PLUGIN_DIR_URL . 'js/frontend.js', array(), $this->PLUGIN_VERSION, true );
			wp_add_inline_script(
				'real3d-flipbook-forntend',
				'var r3d_frontend = ' . wp_json_encode(
					array(
						'rootFolder' => $this->PLUGIN_DIR_URL,
						'version'    => $this->PLUGIN_VERSION,
					)
				) . ';',
				'before'
			);

			wp_add_inline_script(
				'real3d-flipbook-forntend',
				'var flipbookOptions_global = ' . wp_json_encode( $this->flipbook_global, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
				'before'
			);
		}
	}

	public function admin_enqueue_scripts( $hook_suffix ) {

		wp_register_script( 'alpha-color-picker', $this->PLUGIN_DIR_URL . 'js/alpha-color-picker.js', array( 'jquery', 'wp-color-picker' ), $this->PLUGIN_VERSION, true );
		wp_register_style( 'alpha-color-picker', $this->PLUGIN_DIR_URL . 'css/alpha-color-picker.css', array( 'wp-color-picker' ), $this->PLUGIN_VERSION );

		wp_register_script( 'real3d-flipbook-admin', $this->PLUGIN_DIR_URL . 'js/edit_flipbook.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-selectable', 'real3d-flipbook-pdfjs', 'alpha-color-picker', 'common', 'wp-lists', 'postbox' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-edit-post', $this->PLUGIN_DIR_URL . 'js/edit_flipbook_post.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-resizable', 'jquery-ui-selectable', 'real3d-flipbook-pdfjs', 'alpha-color-picker', 'common', 'wp-lists', 'postbox' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-settings', $this->PLUGIN_DIR_URL . 'js/settings.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-resizable', 'jquery-ui-selectable', 'alpha-color-picker', 'common', 'wp-lists', 'postbox' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-flipbooks', $this->PLUGIN_DIR_URL . 'js/flipbooks.js', array( 'jquery', 'common', 'wp-lists', 'postbox' ), $this->PLUGIN_VERSION, true );

		wp_register_script( 'real3d-flipbook-import', $this->PLUGIN_DIR_URL . 'js/import.js', array( 'jquery' ), $this->PLUGIN_VERSION, true );

		wp_register_style( 'real3d-flipbook-admin', $this->PLUGIN_DIR_URL . 'css/flipbook-admin.css', array(), $this->PLUGIN_VERSION );

		if ( in_array( $hook_suffix, array( 'edit.php' ) ) ) {
			$screen = get_current_screen();

			if ( is_object( $screen ) && 'r3d' == $screen->post_type ) {

				wp_register_style( 'real3d-flipbook-posts', $this->PLUGIN_DIR_URL . 'css/posts.css', array(), $this->PLUGIN_VERSION );
				wp_enqueue_style( 'real3d-flipbook-posts' );

				wp_register_script( 'real3d-flipbook-posts', $this->PLUGIN_DIR_URL . 'js/posts.js', array(), $this->PLUGIN_VERSION, true );
				wp_enqueue_script( 'real3d-flipbook-posts' );
			}
		}

		if ( in_array( $hook_suffix, array( 'edit-tags.php' ) ) ) {
			$screen = get_current_screen();

			if ( is_object( $screen ) && 'r3d' == $screen->post_type ) {

				wp_register_script( 'real3d-flipbook-categories', $this->PLUGIN_DIR_URL . 'js/categories.js', array(), $this->PLUGIN_VERSION, true );
				wp_enqueue_script( 'real3d-flipbook-categories' );
			}
		}
	}

	public function admin_link( $links ) {
		array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=flipbooks' ) ) . '">Admin</a>' );

		return $links;
	}

	public function init() {
		global $l10n;

		if ( function_exists( 'register_block_type' ) ) {
			register_block_type( 'r3dfb/embed', array() );
			add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		}

		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'media_buttons', array( $this, 'insert_flipbook_button' ) );
		}

		if ( get_option( 'r3d_version' ) != $this->PLUGIN_VERSION ) {
			update_option( 'r3d_version', $this->PLUGIN_VERSION );
			update_option( 'r3d_flush_rewrite_rules', true );
		}

		$flipbook_global_options = get_option( 'real3dflipbook_global' );

		if ( isset( $l10n['real3d-flipbook'] ) ) {
			unset( $flipbook_global_options['strings'] );
			$buttonNames = array(
				'btnAutoplay',
				'btnNext',
				'btnLast',
				'btnPrev',
				'btnFirst',
				'btnZoomIn',
				'btnZoomOut',
				'btnToc',
				'btnThumbs',
				'btnShare',
				'btnNotes',
				'btnDownloadPages',
				'btnDownloadPdf',
				'btnSound',
				'btnExpand',
				'btnSingle',
				'btnSearch',
				'search',
				'btnBookmark',
				'btnPrint',
				'btnClose',
			);
			foreach ( $buttonNames as $name ) {
				unset( $flipbook_global_options[ $name ]['title'] );
			}
		}
		$flipbook_global_defaults = r3dfb_getDefaults();

		$this->flipbook_global = r3d_array_merge_deep( $flipbook_global_defaults, $flipbook_global_options );

		$this->enqueue_scripts();

		add_filter( 'widget_text', 'do_shortcode' );
		add_shortcode( 'real3dflipbook', array( $this, 'on_shortcode' ) );

		require_once plugin_dir_path( __FILE__ ) . 'r3d-flipbook-data.php';
		include_once plugin_dir_path( __FILE__ ) . 'post-type.php';
	}

	public function getFlipbookGlobal() {
		return $this->flipbook_global;
	}

	public function override_shortcodes() {
		if ( isset( $this->flipbook_global['overridePDFEmbedder'] ) && $this->flipbook_global['overridePDFEmbedder'] == 'true' ) {

			remove_shortcode( 'pdf-embedder' );
			add_shortcode( 'pdf-embedder', array( $this, 'overridePDFEmbedder' ) );

			add_action(
				'wp_enqueue_scripts',
				function () {
					wp_dequeue_script( 'pdfemb_pdfjs' );
					wp_dequeue_script( 'pdfemb_embed_pdf' );
					wp_deregister_script( 'pdfemb_pdfjs' );
					wp_deregister_script( 'pdfemb_embed_pdf' );
				},
				PHP_INT_MAX
			);
			add_filter( 'render_block', array( $this, 'overridePDFEmbedderBlock' ), 10, 2 );
		}

		if ( isset( $this->flipbook_global['overrideDflip'] ) && $this->flipbook_global['overrideDflip'] == 'true' ) {

			remove_shortcode( 'dflip' );
			add_shortcode( 'dflip', array( $this, 'overrideDflip' ) );
			add_action(
				'wp_enqueue_scripts',
				function () {
					wp_dequeue_script( 'dflip-script' );
					wp_dequeue_style( 'dflip-style' );
					wp_deregister_script( 'dflip-script' );
					wp_deregister_style( 'dflip-style' );
				},
				PHP_INT_MAX
			);
		}

		if ( isset( $this->flipbook_global['overrideWonderPDFEmbed'] ) && $this->flipbook_global['overrideWonderPDFEmbed'] == 'true' ) {

			remove_shortcode( 'wonderplugin_pdf' );
			add_shortcode( 'wonderplugin_pdf', array( $this, 'overrideWonderPDFEmbed' ) );
		}

		if ( isset( $this->flipbook_global['override3DFlipBook'] ) && $this->flipbook_global['override3DFlipBook'] == 'true' ) {

			remove_shortcode( '3d-flip-book' );
			add_shortcode( '3d-flip-book', array( $this, 'override3DFlipBook' ) );
		}

		if ( isset( $this->flipbook_global['overridePDFjsViewer'] ) && $this->flipbook_global['overridePDFjsViewer'] == 'true' ) {

			remove_shortcode( 'pdfjs-viewer' );
			add_shortcode( 'pdfjs-viewer', array( $this, 'overridePDFjsViewer' ) );
		}
	}


	public function overridePDFEmbedder( $atts, $content = null ) {
		$args = shortcode_atts(
			array(
				'url' => '-1',
			),
			$atts
		);

		if ( $args['url'] != '-1' ) {
			return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $args['url'] ) . '"]' );
		}

		return 'No PDF URL provided.';
	}

	public function overridePDFEmbedderBlock( $block_content, $block ) {

		if ( $block['blockName'] === 'pdfemb/pdf-embedder-viewer' ) {
			$attributes = $block['attrs'];
			$pdf_url    = isset( $attributes['url'] ) ? $attributes['url'] : '';

			$shortcode = '[real3dflipbook pdf="' . esc_url( $pdf_url ) . '" mode="normal"]';

			return do_shortcode( $shortcode );
		}

		return $block_content;
	}

	public function overrideDflip( $atts, $content = null ) {
		$args = shortcode_atts(
			array(
				'source' => '-1',
				'id'     => '-1',
				'type'   => '-1',
			),
			$atts
		);

		if ( $args['source'] != '-1' ) {
			return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $args['source'] ) . '"]' );
		} elseif ( $args['id'] != '-1' ) {
			$data = get_post_meta( $args['id'], '_dflip_data', true );

			if ( isset( $data['pdf_source'] ) ) {
				if ( $args['type'] == 'thumb' && ! empty( $data['pdf_thumb'] ) ) {
					$thumb_url = $data['pdf_thumb'];
					return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $data['pdf_source'] ) . '" thumb="' . esc_url( $thumb_url ) . '" mode="lightbox" thumbcss="display: inline-block;box-sizing: border-box;margin: 30px 15px 15px !important;text-align: center;border: 0;width: 140px;height: auto;word-break: break-word;vertical-align: bottom;"]' );
				} else {
					return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $data['pdf_source'] ) . '"]' );
				}
			}
		}

		return 'No PDF URL provided.';
	}

	public function overrideWonderPDFEmbed( $atts, $content = null ) {
		$args = shortcode_atts(
			array(
				'src' => '-1',
			),
			$atts
		);

		if ( $args['src'] != '-1' ) {
			return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $args['src'] ) . '"]' );
		}

		return 'No PDF URL provided.';
	}

	public function overridePDFjsViewer( $atts, $content = null ) {
		$args = shortcode_atts(
			array(
				'url' => '-1',
			),
			$atts
		);

		if ( $args['url'] != '-1' ) {
			return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $args['url'] ) . '"]' );
		}

		return 'No PDF URL provided.';
	}

	public function override3DFlipBook( $atts, $content = null ) {
		$args = shortcode_atts(
			array(
				'pdf' => '-1',
				'id'  => '-1',
			),
			$atts
		);

		if ( $args['pdf'] != '-1' ) {
			return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $args['pdf'] ) . '"]' );
		} elseif ( $args['id'] != '-1' ) {
			$data = get_post_meta( $args['id'], '3dfb_data', true );
			if ( isset( $data['guid'] ) ) {
				return do_shortcode( '[real3dflipbook pdf="' . esc_attr( $data['guid'] ) . '"]' );
			}
		}

		return 'No PDF URL provided.';
	}


	public function plugins_loaded() {
		load_plugin_textdomain( 'real3d-flipbook', false, plugin_basename( dirname( REAL3D_FLIPBOOK_FILE ) ) . '/languages' );

		foreach ( $this->products as $key => &$val ) {
			if ( isset( $val['class'] ) ) {
				$val['active'] = class_exists( $val['class'] ) && ! function_exists( $key . '_fs' );
			}
		}

		if ( ! defined( 'R3D_PDF_TOOLS_VERSION' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_get_pdf_tools' ) );
			return;
		}
	}

	public function admin_notice_get_pdf_tools() {
		global $pagenow, $post_type;
		$admin_pages = array( 'edit.php', 'post.php', 'post-new.php' );

		if ( in_array( $pagenow, $admin_pages ) && $post_type == 'r3d' ) {
			$message = sprintf(
			/* translators: %1$s is replaced with the anchor HTML for the "PDF Tools Addon" link. */
				esc_html__(
					'Optimize Real3D PDF Flipbooks with %1$s by converting PDF to images and JSON. Speed up the flipbook loading and secure the PDF.',
					'real3d-flipbook'
				),
				sprintf(
					'<a href="%1$s" style="text-decoration: none; font-weight: bold;" target="_blank">%2$s</a>',
					esc_url( 'https://real3dflipbook.com/pdf-tools-addon/?ref=wp' ),
					esc_html__( 'PDF Tools Addon for Real3D Flipbook', 'real3d-flipbook' )
				)
			);

			printf(
				'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
				wp_kses(
					$message,
					array(
						'a' => array(
							'href'   => array(),
							'style'  => array(),
							'target' => array(),
						),
					)
				)
			);
		}
	}


	protected function add_actions() {

		add_action( 'init', array( $this, 'init' ) );

		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );

		add_action( 'init', array( $this, 'override_shortcodes' ), 100 );

		// add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		if ( is_admin() ) {
			include_once plugin_dir_path( __FILE__ ) . 'plugin-admin.php';
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'admin_link' ) );
			// add_action('media_buttons', array($this, 'insert_flipbook_button'));

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			add_action( 'wp_ajax_r3d_import', array( $this, 'ajax_import_flipbooks' ) );

			add_action( 'wp_ajax_r3d_get_json', array( $this, 'ajax_get_json' ) );

			add_action( 'admin_footer', array( $this, 'admin_footer' ), 11 );

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 100 );
			add_action( 'edit_form_after_title', array( $this, 'print_content' ) );
			add_action( 'save_post_r3d', array( $this, 'save_post_r3d' ), 10, 3 );
		}

		add_action( 'wp_ajax_r3d_last_page', array( $this, 'ajax_last_page' ) );

		add_filter( 'single_template', array( $this, 'load_r3d_template' ) );
		add_filter( 'taxonomy_template', array( $this, 'load_r3d_taxonomy_template' ) );

		add_action( 'rest_api_init', array( $this, 'register_flipbook_api_routes' ) );

		add_action( 'wp_ajax_pdf', array( $this, 'serve_pdf' ) );
		add_action( 'wp_ajax_nopriv_pdf', array( $this, 'serve_pdf' ) );
	}


	public function register_flipbook_api_routes() {
		register_rest_route(
			'flipbook/v1',
			'/create',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_flipbook' ),
				'permission_callback' => array( $this, 'rest_permission_callback' ),
				'args'                => array(
					'title'            => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'attachmentId'     => array(
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					'pdfUrl'           => array(
						'type'              => 'string',
						'sanitize_callback' => 'esc_url_raw',
						'default'           => '',
					),
					'flipbook_options' => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
					),
				),
			)
		);
		register_rest_route(
			'flipbook/v1',
			'/update',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'update_flipbook' ),
				'permission_callback' => array( $this, 'rest_permission_callback' ),
				'args'                => array(
					'flipbookId'       => array(
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					'postId'           => array(
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					'title'            => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
					),
					'attachmentId'     => array(
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
						'default'           => 0,
					),
					'pdfUrl'           => array(
						'type'              => 'string',
						'sanitize_callback' => 'esc_url_raw',
						'default'           => '',
					),
					'flipbook_options' => array(
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'default'           => '',
					),
				),
			)
		);
	}

	/**
	 * Validates a capability string against a hardcoded allowlist to prevent
	 * low-privilege options (e.g. 'read') from being stored and later used.
	 *
	 * @param string $capability The capability to validate.
	 * @return string A safe, allowlisted capability.
	 */
	private static function validated_capability( $capability ) {
		$minimum_caps = array( 'edit_others_posts', 'manage_options', 'manage_woocommerce', 'publish_pages' );
		if ( ! in_array( $capability, $minimum_caps, true ) ) {
			$capability = 'edit_others_posts';
		}
		return $capability;
	}

	public function rest_permission_callback() {
		$capability = self::validated_capability( get_option( 'real3dflipbook_capability', 'edit_others_posts' ) );
		return current_user_can( $capability );
	}

	public function create_flipbook( WP_REST_Request $request ) {
		$params = $request->get_json_params();

		if ( empty( $params['title'] ) ) {
			return new WP_Error( 'missing_title', 'Title is required', array( 'status' => 400 ) );
		}

		$title        = sanitize_text_field( $params['title'] );
		$attachmentId = isset( $params['attachmentId'] ) ? intval( $params['attachmentId'] ) : 0;

		if ( $attachmentId ) {
			$pdfUrl = wp_get_attachment_url( $attachmentId );
			if ( ! $pdfUrl ) {
				return new WP_Error( 'invalid_attachment', 'Invalid attachment ID', array( 'status' => 400 ) );
			}
		} else {
			$pdfUrl = isset( $params['pdfUrl'] ) ? esc_url_raw( $params['pdfUrl'] ) : '';
			if ( $pdfUrl && ! wp_http_validate_url( $pdfUrl ) ) {
				return new WP_Error( 'invalid_url', 'Invalid PDF URL', array( 'status' => 400 ) );
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_type'   => 'r3d',
				'post_status' => 'publish',
			)
		);

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error( 'post_creation_failed', 'Failed to create post', array( 'status' => 500 ) );
		}

		$thumbnailUrl = $this->generate_thumbnail_from_pdf( $pdfUrl, $post_id );

		$options = array();

		if ( ! empty( $params['flipbook_options'] ) ) {
			$json    = urldecode( $params['flipbook_options'] );
			$decoded = json_decode( $json, true );

			if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
				$options = $decoded;
			}
		}

		$options['pdfUrl']               = $pdfUrl;
		$options['lightboxThumbnailUrl'] = $thumbnailUrl;

		$options = r3d_sanitize_array( $options );

		r3d_save_flipbook( $post_id, $title, $options );

		return new WP_REST_Response(
			array(
				'post_id'          => $post_id,
				'message'          => 'Flipbook created',
				'flipbook_options' => $options,
			),
			200
		);
	}

	public function update_flipbook( WP_REST_Request $request ) {
		$params = $request->get_json_params();

		$post_id      = isset( $params['postId'] ) ? intval( $params['postId'] ) : 0;
		$title        = isset( $params['title'] ) ? sanitize_text_field( $params['title'] ) : '';
		$attachmentId = isset( $params['attachmentId'] ) ? intval( $params['attachmentId'] ) : 0;

		$post = get_post( $post_id );
		if ( ! $post || 'r3d' !== $post->post_type ) {
			return new WP_Error( 'invalid_post', 'Invalid post ID', array( 'status' => 400 ) );
		}

		if ( $title ) {
			wp_update_post(
				array(
					'ID'         => $post_id,
					'post_title' => $title,
				)
			);
		} else {
			$title = $post->post_title;
		}

		if ( $attachmentId ) {
			$pdfUrl = wp_get_attachment_url( $attachmentId );
			if ( ! $pdfUrl ) {
				return new WP_Error( 'invalid_attachment', 'Invalid attachment ID', array( 'status' => 400 ) );
			}
		} else {
			$pdfUrl = isset( $params['pdfUrl'] ) ? esc_url_raw( $params['pdfUrl'] ) : '';
			if ( $pdfUrl && ! wp_http_validate_url( $pdfUrl ) ) {
				return new WP_Error( 'invalid_url', 'Invalid PDF URL', array( 'status' => 400 ) );
			}
		}

		$options = array();

		if ( ! empty( $params['flipbook_options'] ) ) {
			$json    = urldecode( $params['flipbook_options'] );
			$decoded = json_decode( $json, true );

			if ( json_last_error() === JSON_ERROR_NONE && is_array( $decoded ) ) {
				$options = $decoded;
			}
		}

		if ( $pdfUrl ) {
			$thumbnailUrl                    = $this->generate_thumbnail_from_pdf( $pdfUrl, $post_id );
			$options['pdfUrl']               = $pdfUrl;
			$options['lightboxThumbnailUrl'] = $thumbnailUrl;
		}

		$options = r3d_sanitize_array( $options );

		r3d_save_flipbook( $post_id, $title, $options );

		return new WP_REST_Response(
			array(
				'post_id'          => $post_id,
				'message'          => 'Flipbook updated',
				'flipbook_options' => $options,
			),
			200
		);
	}


	private function generate_thumbnail_from_pdf( $pdfUrl, $post_id ) {
		$upload_dir    = wp_upload_dir();
		$booksFolder   = $upload_dir['basedir'] . '/real3d-flipbook/';
		$bookFolder    = $booksFolder . 'flipbook_' . $post_id . '/';
		$thumbnailPath = $bookFolder . 'thumbnail.jpg';

		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( ! $wp_filesystem->is_dir( $booksFolder ) ) {
			if ( ! $wp_filesystem->mkdir( $booksFolder ) ) {
				/* translators: %s is the path to the folder that failed to be created */
				wp_die( esc_html( sprintf( __( 'Failed to create directory: %s', 'real3d-flipbook' ), $booksFolder ) ) );
			}
		}

		if ( ! $wp_filesystem->is_dir( $bookFolder ) ) {
			if ( ! $wp_filesystem->mkdir( $bookFolder ) ) {
				/* translators: %s is the path to the folder that failed to be created */
				wp_die( esc_html( sprintf( __( 'Failed to create directory: %s', 'real3d-flipbook' ), $bookFolder ) ) );
			}
		}

		try {
			$upload_basedir = $upload_dir['basedir'];

			if ( filter_var( $pdfUrl, FILTER_VALIDATE_URL ) ) {
				$parsedUrl = wp_parse_url( $pdfUrl );
				$scheme    = isset( $parsedUrl['scheme'] ) ? $parsedUrl['scheme'] : '';

				if ( ! in_array( $scheme, array( 'http', 'https' ), true ) ) {
					return new WP_Error( 'invalid_url_scheme', 'Only http and https URLs are allowed.' );
				}

				$site_host = wp_parse_url( site_url(), PHP_URL_HOST );
				if ( isset( $parsedUrl['host'] ) && $site_host && $parsedUrl['host'] === $site_host ) {
					$upload_url_path = $upload_dir['baseurl'];
					if ( strpos( $pdfUrl, $upload_url_path ) === 0 ) {
						$relative     = substr( $pdfUrl, strlen( $upload_url_path ) );
						$localPdfPath = $upload_basedir . $relative;
					} else {
						$localPdfPath = $bookFolder . 'original.pdf';
						$pdfContents  = wp_safe_remote_get( $pdfUrl );
						if ( is_wp_error( $pdfContents ) ) {
							return new WP_Error( 'pdf_download_failed', 'Failed to download PDF file.' );
						}
						$pdfBody = wp_remote_retrieve_body( $pdfContents );
						if ( ! $wp_filesystem->put_contents( $localPdfPath, $pdfBody, FS_CHMOD_FILE ) ) {
							return new WP_Error( 'file_write_failed', 'Failed to write the PDF file locally.' );
						}
					}
					$realPath = realpath( $localPdfPath );

					if ( false === $realPath || strpos( $realPath, realpath( $upload_basedir ) ) !== 0 ) {
						return new WP_Error( 'path_traversal', 'PDF path must be within the uploads directory.' );
					}
					$localPdfPath = $realPath;
				} else {
					$localPdfPath = $bookFolder . 'original.pdf';
					$pdfContents  = wp_safe_remote_get( $pdfUrl );

					if ( is_wp_error( $pdfContents ) ) {
						return new WP_Error( 'pdf_download_failed', 'Failed to download PDF file.' );
					}

					$pdfBody = wp_remote_retrieve_body( $pdfContents );

					if ( ! $wp_filesystem->put_contents( $localPdfPath, $pdfBody, FS_CHMOD_FILE ) ) {
						return new WP_Error( 'file_write_failed', 'Failed to write the PDF file locally.' );
					}
				}
			} else {
				$localPdfPath = realpath( $pdfUrl );
				if ( false === $localPdfPath || strpos( $localPdfPath, realpath( $upload_basedir ) ) !== 0 ) {
					return new WP_Error( 'path_traversal', 'PDF path must be within the uploads directory.' );
				}
			}

			if ( ! extension_loaded( 'imagick' ) ) {
				return new WP_Error( 'imagick_missing', 'The Imagick PHP extension is not available.', array( 'status' => 500 ) );
			}

			$imagick = new Imagick();
			$imagick->setResolution( 72, 72 );
			$imagick->readImage( $localPdfPath . '[0]' );
			$imagick->setImageFormat( 'jpeg' );

			$imagick->setImageBackgroundColor( 'white' );
			$imagick = $imagick->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );

			$imagick->thumbnailImage( 200, 0 );

			if ( ! $imagick->writeImage( $thumbnailPath ) ) {
				throw new Exception( 'Failed to write image to ' . $thumbnailPath );
			}
			$imagick->clear();
			$imagick->destroy();

			return esc_url( $upload_dir['baseurl'] . '/real3d-flipbook/flipbook_' . $post_id . '/thumbnail.jpg' );
		} catch ( ImagickException $e ) {
			return new WP_Error( 'thumbnail_generation_failed', 'Failed to generate thumbnail: ' . $e->getMessage(), array( 'status' => 500 ) );
		} catch ( Exception $e ) {
			return new WP_Error( 'thumbnail_generation_failed', 'Failed to generate thumbnail: ' . $e->getMessage(), array( 'status' => 500 ) );
		} catch ( Error $e ) {
			return new WP_Error( 'thumbnail_generation_failed', 'Failed to generate thumbnail: ' . $e->getMessage(), array( 'status' => 500 ) );
		}
	}




	public function save_post_r3d( $post_ID, $post, $update ) {

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['_inline_edit'] ) ) {
			return;
		}

		if ( isset( $_GET['action'] ) && $_GET['action'] === 'untrash' ) {
			return;
		}

		if ( isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		if ( ! isset( $_POST['r3d_flipbook_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['r3d_flipbook_nonce'] ) ), 'saving-real3d-flipbook' ) ) {
			return;
		}

		$status = $post->post_status;
		$title  = $post->post_title;

		if ( 'auto-draft' === $status && $title ) {
			wp_update_post(
				array(
					'ID'         => $post_ID,
					'post_title' => '',
				)
			);
		} elseif ( 'draft' === $status || 'publish' === $status ) {

			$flipbook = null;

			if ( isset( $_POST['flipbook_options'] ) && ! empty( $_POST['flipbook_options'] ) ) {
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON string, sanitized after decode via sanitize_flipbook_data()
				$encodedOptionsString = wp_unslash( $_POST['flipbook_options'] );
				$optionsString        = urldecode( $encodedOptionsString );
				$flipbook             = json_decode( $optionsString, true );

				if ( json_last_error() !== JSON_ERROR_NONE ) {
					wp_die(
						/* translators: %s is the JSON error message */
						esc_html( sprintf( __( 'Invalid JSON data: %s', 'real3d-flipbook' ), json_last_error_msg() ) ),
						esc_html__( 'Error', 'real3d-flipbook' ),
						array( 'response' => 400 )
					);
				}

				$flipbook = $this->sanitize_flipbook_data( $flipbook );
			}

			if ( $flipbook ) {
				r3d_save_flipbook( $post_ID, $title, $flipbook );
			}
		}
	}

	private function sanitize_flipbook_data( $flipbook ) {
		$pages_data = null;
		if ( isset( $flipbook['pages'] ) ) {
			$pages_data = $flipbook['pages'];
			unset( $flipbook['pages'] );
		}

		$flipbook = r3d_sanitize_array( $flipbook );

		if ( $pages_data ) {
			foreach ( $pages_data as $pageIndex => $page ) {
				$html_content = null;
				if ( isset( $page['htmlContent'] ) ) {
					$decodedHtmlContent = urldecode( $page['htmlContent'] );
					if ( ! current_user_can( 'unfiltered_html' ) ) {
						$html_content = wp_kses_post( $decodedHtmlContent );
					} else {
						$html_content = $decodedHtmlContent;
					}
					unset( $page['htmlContent'] );
				}
				$page = r3d_sanitize_array( $page );
				if ( null !== $html_content ) {
					$page['htmlContent'] = $html_content;
				}
				$pages_data[ $pageIndex ] = $page;
			}
			$flipbook['pages'] = $pages_data;
		}

		return $flipbook;
	}

	public function load_r3d_template( $template ) {

		global $post;

		if ( 'r3d' === $post->post_type ) {
			return plugin_dir_path( __FILE__ ) . 'single-r3d.php';
		}

		return $template;
	}

	public function load_r3d_taxonomy_template( $template ) {

		if ( is_tax( 'r3d_category' ) ) {
			return plugin_dir_path( __FILE__ ) . 'taxonomy-r3d_category.php';
		}

		return $template;
	}



	public function insert_flipbook_button() {

		global $pagenow;
		if ( ! in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
			return;
		}

		printf(
			'<a href="%1$s" class="thickbox button r3d-insert-flipbook-button" title="%2$s"><span class="wp-media-buttons-icon" style="background:url(%3$simages/th.png); background-repeat: no-repeat; background-position: left bottom;"></span>%4$s</a>',
			esc_url( '#TB_inline?&inlineId=choose_flipbook' ),
			esc_attr__( 'Select flipbook to insert into post', 'real3d-flipbook' ),
			esc_url( $this->PLUGIN_DIR_URL ),
			esc_html__( 'Real3D Flipbook', 'real3d-flipbook' )
		);
	}

	public function ajax_import_flipbooks() {

		check_ajax_referer( 'r3d_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'real3d-flipbook' ), '', array( 'response' => 403 ) );
		}

		if ( isset( $_POST['flipbooks'] ) && ! empty( $_POST['flipbooks'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON string, sanitized after decode via r3d_sanitize_array()
			$json         = wp_unslash( $_POST['flipbooks'] );
			$newFlipbooks = json_decode( $json, true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				wp_die(
					/* translators: %s is the JSON error message */
					esc_html( sprintf( __( 'Invalid JSON data: %s', 'real3d-flipbook' ), json_last_error_msg() ) ),
					esc_html__( 'Error', 'real3d-flipbook' ),
					array( 'response' => 400 )
				);
			}
		} else {
			wp_die(
				esc_html__( 'Missing flipbooks data.', 'real3d-flipbook' ),
				esc_html__( 'Error', 'real3d-flipbook' ),
				array( 'response' => 400 )
			);
		}

		if ( (string) $json != '' && is_array( $newFlipbooks ) ) {

			$allposts = get_posts(
				array(
					'post_type'   => 'r3d',
					'numberposts' => -1,
					'post_status' => array( 'any', 'trash' ),
				)
			);
			foreach ( $allposts as $eachpost ) {
				wp_delete_post( $eachpost->ID, true );
			}

			foreach ( $newFlipbooks as $b ) {
				if ( ! isset( $b['id'] ) ) {
					continue;
				}

				$id = $b['id'];

				if ( isset( $b['post_id'] ) ) {
					unset( $b['post_id'] );
				}

				$sanitized = r3d_sanitize_array( $b );

				if ( 'global' === $id ) {
					update_option( 'real3dflipbook_global', $sanitized );
				} elseif ( ctype_digit( (string) $id ) ) {
					$title   = isset( $sanitized['name'] ) ? sanitize_text_field( $sanitized['name'] ) : '';
					$post_id = wp_insert_post(
						array(
							'post_title'  => $title,
							'post_type'   => 'r3d',
							'post_status' => 'publish',
						)
					);

					if ( ! is_wp_error( $post_id ) ) {
						r3d_save_flipbook( $post_id, $title, $sanitized );
					}
				}
			}
		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}




	public function ajax_last_page() {

		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'nonce_flipbook_embed' ) ) {
			wp_send_json_error( 'Nonce verification failed.', 403 );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in to save last page.', 403 );
		}

		$bookId = isset( $_POST['bookId'] ) ? absint( $_POST['bookId'] ) : 0;
		$page   = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 0;

		$userId = get_current_user_id();

		if ( ! $bookId || $page <= 0 ) {
			wp_send_json_error( 'Invalid input data.', 400 );
		}

		$meta_key = 'real3dflipbook_last_page_' . $bookId;

		update_user_meta( $userId, $meta_key, $page );

		wp_send_json_success( 'Last page saved successfully.' );
	}


	public function ajax_get_json() {
		check_ajax_referer( 'r3d_nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'real3d-flipbook' ), '', array( 'response' => 403 ) );
		}

		$all = r3d_get_all_flipbooks( array( 'post_status' => array( 'publish', 'draft', 'trash' ) ) );

		$flipbooks = array();

		foreach ( $all as $post_id => $book ) {
			if ( empty( $book['date'] ) ) {
				$book['date'] = get_the_date( 'Y-m-d H:i:s', $post_id );
			}
			$book['post_status']   = get_post_status( $post_id );
			$flipbooks[ $post_id ] = $book;
		}

		wp_send_json_success( $flipbooks );
	}

	public function admin_menu() {

		$capability = self::validated_capability( get_option( 'real3dflipbook_capability', 'edit_others_posts' ) );

		add_menu_page(
			'Real3D Flipbook',
			'Real3D Flipbook',
			$capability,
			'real3d_flipbook_admin',
			array( $this, 'admin' ),
			'dashicons-book'
		);

		add_submenu_page(
			'real3d_flipbook_admin',
			esc_html__( 'Flipbooks', 'real3d-flipbook' ),
			esc_html__( 'Flipbooks', 'real3d-flipbook' ),
			$capability,
			'edit.php?post_type=r3d'
		);

		add_submenu_page(
			'real3d_flipbook_admin',
			esc_html__( 'Add new', 'real3d-flipbook' ),
			esc_html__( 'Add new', 'real3d-flipbook' ),
			$capability,
			'post-new.php?post_type=r3d'
		);

		add_submenu_page(
			'real3d_flipbook_admin',
			esc_html__( 'Categories', 'real3d-flipbook' ),
			esc_html__( 'Categories', 'real3d-flipbook' ),
			$capability,
			'edit-tags.php?taxonomy=r3d_category&post_type=r3d'
		);

		add_submenu_page(
			'real3d_flipbook_admin',
			esc_html__( 'Authors', 'real3d-flipbook' ),
			esc_html__( 'Authors', 'real3d-flipbook' ),
			$capability,
			'edit-tags.php?taxonomy=r3d_author&post_type=r3d'
		);

		add_submenu_page(
			'real3d_flipbook_admin',
			esc_html__( 'Import / Export', 'real3d-flipbook' ),
			esc_html__( 'Import / Export', 'real3d-flipbook' ),
			$capability,
			'real3d_flipbook_import',
			array( $this, 'import' )
		);

		remove_submenu_page( 'real3d_flipbook_admin', 'real3d_flipbook_admin' );

		add_submenu_page(
			'real3d_flipbook_admin',
			esc_html__( 'Settings', 'real3d-flipbook' ),
			esc_html__( 'Settings', 'real3d-flipbook' ),
			'manage_options',
			'real3d_flipbook_settings',
			array( $this, 'settings' )
		);

		add_submenu_page(
			'real3d_flipbook_admin',
			'Addons',
			'<span style="font-weight: 700; color: #33FF22">Add-ons</span>',
			$capability,
			'real3d_flipbook_addons',
			array( $this, 'addons' ),
			99
		);

		if ( ! $this->pro ) {

			add_submenu_page(
				'real3d_flipbook_admin',
				'Upgrade',
				'<span style="font-weight: 700; color: #33FF22">Upgrade to PRO</span>',
				$capability,
				'real3d_flipbook_upgrade',
				array( $this, 'upgrade' ),
				99
			);
		}

		add_submenu_page(
			'real3d_flipbook_admin',
			'Help',
			'Help',
			$capability,
			'real3d_flipbook_help',
			array( $this, 'help' )
		);

		if ( current_user_can( $capability ) ) {

			do_action( 'real3d_flipbook_menu' );
		}
	}

	public function admin_footer() {

		global $pagenow;
		global $current_screen;

		if ( $current_screen->post_type == 'r3d' ) {
			return;
		}

		if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {

			$all_flipbooks = r3d_get_all_flipbooks();
			$flipbooks     = array();
			foreach ( $all_flipbooks as $post_id => $b ) {
				$book = array(
					'id'   => $post_id,
					'name' => isset( $b['name'] ) ? $b['name'] : '',
				);
				array_push( $flipbooks, $book );
			}

			wp_enqueue_script( 'r3dfb-insert-js', $this->PLUGIN_DIR_URL . 'js/insert-flipbook.js', array( 'jquery' ), $this->PLUGIN_VERSION, true );

			wp_enqueue_style( 'r3dfb-insert-css', $this->PLUGIN_DIR_URL . 'css/insert-flipbook.css', array(), $this->PLUGIN_VERSION, true );

			?>

<div id="choose_flipbook" style="display: none;">
	<div id="r3d-tb-wrapper">
		<div class="r3d-tb-inner">
			<?php
			if ( count( $flipbooks ) ) {
				?>
			<h3 style='margin-bottom: 20px;'><?php esc_html_e( 'Insert Flipbook', 'real3d-flipbook' ); ?></h3>
			<select id='r3d-select-flipbook'>
				<option value='' selected=selected>
				<?php esc_html_e( 'Default Flipbook (Global Settings)', 'real3d-flipbook' ); ?>
				</option>
				<?php
				foreach ( $flipbooks as $book ) {
					$id   = $book['id'];
					$name = $book['name'];
					?>
				<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_attr( $name ); ?></option>
					<?php
				}
				?>
			</select>
				<?php
			} else {
				esc_html_e( 'No flipbooks found. Create new flipbook or set flipbook source', 'real3d-flipbook' );
			}
			?>

			<h3 style="margin-top: 40px;"><?php esc_html_e( 'Flipbook source', 'real3d-flipbook' ); ?></h3>
			<p><?php esc_html_e( 'Select PDF or images from media library, or enter PDF URL. PDF needs to be on the same domain or CORS needs to be enabled.', 'real3d-flipbook' ); ?>
			</p>

			<div class="r3d-row r3d-row-pdf">

				<input type='text' class='regular-text' id='r3d-pdf-url' placeholder="PDF URL">
				<button class='button-secondary'
					id='r3d-select-pdf'><?php esc_html_e( 'Select PDF', 'real3d-flipbook' ); ?></button>
				<button class='button-secondary'
					id='r3d-select-images'><?php esc_html_e( 'Select images', 'real3d-flipbook' ); ?></button>
				<div class="r3d-pages"></div>

			</div>

			<h3 style="margin-top: 40px;"><?php esc_html_e( 'Thumbnail', 'real3d-flipbook' ); ?></h3>
			<p><?php esc_html_e( 'Select image from media library, or enter URL.', 'real3d-flipbook' ); ?></p>

			<div class="r3d-row r3d-row-thumb">
				<input type='text' class='regular-text' id='r3d-thumb-url' placeholder="Thumbnail URL">
				<button class='button-secondary'
					id='r3d-select-thumb'><?php esc_html_e( 'Select Image', 'real3d-flipbook' ); ?></button>

			</div>

			<h3 style="margin-top: 40px;"><?php esc_html_e( 'Flipbook settings', 'real3d-flipbook' ); ?></h3>

			<div class="r3d-row r3d-row-mode">
				<span class="r3d-label-wrapper"><label
						for="r3d-mode"><?php esc_html_e( 'Mode', 'real3d-flipbook' ); ?></label></span>
				<select id='r3d-mode' class="r3d-setting">
					<option selected="selected" value=""><?php esc_html_e( 'Default', 'real3d-flipbook' ); ?></option>
					<option value="normal">Normal (inside div)</option>
					<option value="lightbox">Lightbox (popup)</option>
					<option value="fullscreen">Fullscreen</option>
				</select>
			</div>

			<div class="r3d-row r3d-row-thumb r3d-row-lightbox" style="display: none;">
				<span class="r3d-label-wrapper"><label
						for="r3d-thumb"><?php esc_html_e( 'Show thumbnail', 'real3d-flipbook' ); ?></label></span>
				<select id='r3d-thumb' class="r3d-setting">
					<option selected="selected" value=""><?php esc_html_e( 'Default', 'real3d-flipbook' ); ?></option>
					<option value="1">yes</option>
					<option value="">no</option>
				</select>
			</div>

			<div class="r3d-row r3d-row-class r3d-row-lightbox" style="display: none;">
				<span class="r3d-label-wrapper"><label
						for="r3d-class"><?php esc_html_e( 'CSS class', 'real3d-flipbook' ); ?></label></span>
				<input id="r3d-class" type="text" class="r3d-setting">
			</div>

			<?php
			echo esc_html( apply_filters( 'r3d_select_flipbook_before_insert', '' ) );
			?>

			<div class="r3d-row r3d-row-insert">
				<button class="button button-primary button-large" disabled="disabled"
					id="r3d-insert-btn"><?php esc_html_e( 'Insert flipbook', 'real3d-flipbook' ); ?></button>
			</div>

		</div>
	</div>
</div>

			<?php
		}
	}

	public function enqueue_block_assets() {
	}

	public function enqueue_block_editor_assets() {
		wp_enqueue_script(
			'r3dfb-block-js',
			$this->PLUGIN_DIR_URL . 'js/blocks.js',
			array( 'wp-block-editor', 'wp-blocks', 'wp-i18n', 'wp-element' ),
			$this->PLUGIN_VERSION,
			true
		);

		$all_flipbooks = r3d_get_all_flipbooks();
		$books         = array();

		foreach ( $all_flipbooks as $post_id => $fb ) {
			$book         = array();
			$book['id']   = $post_id;
			$book['name'] = isset( $fb['name'] ) ? $fb['name'] : '';
			if ( isset( $fb['mode'] ) ) {
				$book['mode'] = $fb['mode'];
			}
			if ( isset( $fb['pdfUrl'] ) ) {
				$book['pdfUrl'] = $fb['pdfUrl'];
			}
			array_push( $books, $book );
		}

		wp_add_inline_script(
			'r3dfb-block-js',
			'var r3dfb = ' . wp_json_encode( $books, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ';',
			'before'
		);
	}

	public function settings() {

		include_once plugin_dir_path( __FILE__ ) . 'settings.php';
	}

	public function import() {

		include_once plugin_dir_path( __FILE__ ) . 'import.php';
	}

	public function addons() {

		include_once plugin_dir_path( __FILE__ ) . 'addons.php';
	}

	public function upgrade() {
		include_once plugin_dir_path( __FILE__ ) . 'upgrade-to-pro.php';
	}

	public function help() {

		include_once plugin_dir_path( __FILE__ ) . 'help.php';
	}

	public function print_content() {

		global $current_screen;
		if ( $current_screen->post_type == 'r3d' ) {
			include_once plugin_dir_path( __FILE__ ) . 'edit-flipbook-post.php';
		}
	}

	public function add_meta_boxes() {

		add_meta_box( 'r3d_post_meta_box_shortcode', esc_html__( 'Shortcode', 'real3d-flipbook' ), array( $this, 'create_meta_box_shortcode' ), 'r3d', 'side', 'high' );
	}

	public function create_meta_box_shortcode( $post ) {
		if ( $post->post_type !== 'r3d' ) {
			return;
		}

		?>
<code>[real3dflipbook id="<?php echo esc_attr( $post->ID ); ?>"]</code>
<div id="<?php echo esc_attr( $post->ID ); ?>" class="button-secondary copy-shortcode">Copy</div>
		<?php
	}




	public function serve_pdf() {
		if ( ! wp_doing_ajax() ) {
			wp_die( esc_html__( 'Invalid request.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 403 ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- nonce checked below via wp_verify_nonce
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'nonce_flipbook_embed' ) ) {
			wp_die( esc_html__( 'Forbidden.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 403 ) );
		}

		if ( ! isset( $_GET['id'] ) || empty( $_GET['id'] ) ) {
			wp_die( esc_html__( 'Missing PDF ID.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 400 ) );
		}

		$pdf_id = sanitize_text_field( wp_unslash( $_GET['id'] ) );

		$file = get_transient( 'flipbook_pdf_' . $pdf_id );

		if ( ! $file ) {
			wp_die( esc_html__( 'Invalid PDF ID.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 400 ) );
		}

		$real_file    = realpath( $file );
		$upload_dir   = wp_upload_dir();
		$real_uploads = realpath( $upload_dir['basedir'] );

		if ( false === $real_file || false === $real_uploads || strpos( $real_file, $real_uploads . DIRECTORY_SEPARATOR ) !== 0 ) {
			wp_die( esc_html__( 'Access denied.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 403 ) );
		}

		$file = $real_file;

		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime  = finfo_file( $finfo, $file );
		finfo_close( $finfo );
		if ( 'application/pdf' !== $mime ) {
			wp_die( esc_html__( 'Invalid file type.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 403 ) );
		}

		$size = filesize( $file );

		header( 'Content-Type: application/pdf' );
		header( 'Accept-Ranges: bytes' );
		header( 'Content-Disposition: inline' );
		header( 'X-Content-Type-Options: nosniff' );

		$range_header = isset( $_SERVER['HTTP_RANGE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_RANGE'] ) ) : '';

		if ( $range_header && preg_match( '/^bytes=(\d+)-(\d*)$/', $range_header, $matches ) ) {
			$from = intval( $matches[1] );
			$to   = ( '' === $matches[2] ) ? $size - 1 : intval( $matches[2] );

			if ( $from > $to || $to >= $size || $from < 0 ) {
				header( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
				header( "Content-Range: bytes */$size" );
				exit;
			}

			$length = $to - $from + 1;
			header( 'HTTP/1.1 206 Partial Content' );
			header( "Content-Range: bytes $from-$to/$size" );
			header( "Content-Length: $length" );

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- streaming binary PDF data
			$fp = fopen( $file, 'rb' );
			if ( $fp ) {
				fseek( $fp, $from );
				$remaining = $length;
				while ( $remaining > 0 && ! feof( $fp ) ) {
					$chunk = min( $remaining, 8192 );
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, WordPress.WP.AlternativeFunctions.file_system_operations_fread -- binary PDF stream
					echo fread( $fp, $chunk );
					$remaining -= $chunk;
					flush();
				}
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- binary PDF stream
				fclose( $fp );
			} else {
				wp_die( esc_html__( 'Unable to read file.', 'real3d-flipbook' ), esc_html__( 'Error', 'real3d-flipbook' ), array( 'response' => 500 ) );
			}
		} else {
			header( "Content-Length: $size" );
			header( 'HTTP/1.1 200 OK' );

			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile -- streaming binary PDF data
			readfile( $file );
		}

		exit;
	}



	public function register_pdf( $unique_id, $pdf_url ) {
		$upload_dir = wp_upload_dir();
		$base_url   = $upload_dir['baseurl'];
		$base_path  = $upload_dir['basedir'];

		if ( strpos( $pdf_url, $base_url ) !== 0 ) {
			return;
		}

		$relative_path = substr( $pdf_url, strlen( $base_url ) );
		$full_path     = $base_path . $relative_path;

		$real_path    = realpath( $full_path );
		$real_uploads = realpath( $base_path );

		if ( false === $real_path || false === $real_uploads || strpos( $real_path, $real_uploads . DIRECTORY_SEPARATOR ) !== 0 ) {
			return;
		}

		set_transient( 'flipbook_pdf_' . $unique_id, $real_path, 12 * HOUR_IN_SECONDS );
	}


	public function on_shortcode( $atts, $content = null ) {

		$args = shortcode_atts(
			array(
				'id'                   => '-1',
				'name'                 => '-1',
				'pdf'                  => '-1',
				'mode'                 => '-1',
				'class'                => '-1',
				'aspect'               => '-1',
				'thumb'                => '-1',
				'title'                => '-1',
				'viewmode'             => '-1',
				'lightboxopened'       => '-1',
				'lightboxfullscreen'   => '-1',
				'lightboxtext'         => '-1',
				'lightboxcssclass'     => '-1',
				'lightboxthumbnail'    => '-1',
				'lightboxthumbnailurl' => '-1',
				'hidemenu'             => '-1',
				'autoplayonstart'      => '-1',
				'autoplayinterval'     => '-1',
				'autoplayloop'         => '-1',
				'zoom'                 => '-1',
				'zoomdisabled'         => '-1',
				'btndownloadpdfurl'    => '-1',
				'thumbcss'             => '-1',
				'containercss'         => '-1',
				'singlepage'           => '-1',
				'startpage'            => '-1',
				'pagenumberoffset'     => '-1',
				'deeplinkingprefix'    => '-1',
				'search'               => '-1',
				'pages'                => '-1',
				'thumbs'               => '-1',
				'thumbalt'             => '-1',
				'category'             => '-1',
				'author'               => '-1',
				'num'                  => '-1',
				'order'                => '-1',
				'orderby'              => '-1',
				'pagerangestart'       => '-1',
				'pagerangeend'         => '-1',
				'previewpages'         => '-1',
				'securepdf'            => '-1',
				'rtl'                  => '-1',
				'lang'                 => '-1',
			),
			$atts
		);

		if ( $args['lang'] != '-1' ) {
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$current_lang = ICL_LANGUAGE_CODE;
			} elseif ( function_exists( 'pll_current_language' ) ) {
				$current_lang = pll_current_language();
			} else {
				$current_lang = substr( get_locale(), 0, 2 );
			}

			// If current site language doesn't match shortcode lang, stop rendering
			if ( strtolower( $args['lang'] ) !== strtolower( $current_lang ) ) {
				return ''; // Nothing is rendered
			}
		}

		if ( $args['id'] === 'all' ) {
			$output        = '';
			$all_flipbooks = r3d_get_all_flipbooks();

			foreach ( $all_flipbooks as $post_id => $fb ) {
				$child_atts         = $args;
				$child_atts['id']   = (string) $post_id;
				$child_atts['mode'] = 'lightbox';
				$child_atts['name'] = '-1';

				$output .= $this->on_shortcode( $child_atts, $content );
			}

			return $output;
		}

		if ( $args['category'] != -1 ) {

			$output = '';

			$num = '-1';
			if ( isset( $args['num'] ) ) {
				$num = $args['num'];
			}

			$query_args = array(
				'post_type'      => 'r3d',
				'post_status'    => 'publish',
				'posts_per_page' => $num,
				'tax_query'      => array(
					array(
						'taxonomy' => 'r3d_category',
						'field'    => 'slug',
						'terms'    => array( $args['category'] ),
					),
				),
			);

			if ( $args['order'] != -1 ) {
				$query_args['order'] = $args['order'];
			}
			if ( $args['orderby'] != -1 ) {
				$query_args['orderby'] = $args['orderby'];
			}

			$query = new WP_Query( $query_args );

			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

				$shortcode = '[real3dflipbook id="' . $post_id . '" mode="lightbox"]';

				$output .= do_shortcode( $shortcode );
				wp_reset_postdata();
			}

			return $output;
		}

		if ( $args['author'] != -1 ) {

			$output = '';

			$num = '-1';
			if ( isset( $args['num'] ) ) {
				$num = $args['num'];
			}

			$query_args = array(
				'post_type'      => 'r3d',
				'post_status'    => 'publish',
				'posts_per_page' => $num,
				'tax_query'      => array(
					array(
						'taxonomy' => 'r3d_author',
						'field'    => 'slug',
						'terms'    => $args['author'],
					),
				),
			);

			$query = new WP_Query( $query_args );

			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id = get_the_ID();

				$shortcode = '[real3dflipbook id="' . $post_id . '" mode="lightbox"]';

				$output .= do_shortcode( $shortcode );
				wp_reset_postdata();
			}

			return $output;
		}

		$id   = (int) $args['id'];
		$name = $args['name'];
		$g    = $this->flipbook_global;

		if ( $name != -1 ) {
			$resolved = r3d_resolve_flipbook_by_name( $name );
			if ( $resolved ) {
				$flipbook = $resolved;
				$id       = $resolved['id'];
			}
		} elseif ( $id != -1 ) {

			$flipbook = r3d_get_flipbook( $id );

		} else {
			$flipbook = array();
			$id       = '0';
		}

		if ( ! $flipbook ) {
			$flipbook = array();
			$id       = '0';
		}

		$bookId = $id . '_' . uniqid();

		foreach ( $args as $key => $val ) {
			if ( $val != -1 ) {

				if ( $key == 'mode' ) {
					$key = 'mode';
				}
				if ( $key == 'viewmode' ) {
					$key = 'viewMode';
				}

				if ( $key == 'pdf' && $val != '' ) {
					$key = 'pdfUrl';
				}

				if ( $key == 'title' ) {
					$key = 'lightboxText';
					if ( $val == 'true' ) {
						$val = $flipbook['name'];
					} elseif ( $val == 'false' ) {
						$val = '';
					}
				}
				if ( $key == 'btndownloadpdfurl' ) {
					$key = 'btnDownloadPdfUrl';
				}
				if ( $key == 'hidemenu' ) {
					$key = 'hideMenu';
				}
				if ( $key == 'autoplayonstart' ) {
					$key = 'autoplayOnStart';
				}
				if ( $key == 'autoplayinterval' ) {
					$key = 'autoplayInterval';
				}
				if ( $key == 'autoplayloop' ) {
					$key = 'autoplayLoop';
				}
				if ( $key == 'zoom' ) {
					$key = 'zoomLevels';
				}
				if ( $key == 'zoomisabled' ) {
					$key = 'zoomDisabled';
				}

				if ( $key == 'lightboxtext' ) {
					$key = 'lightboxText';
				}
				if ( $key == 'lightboxcssclass' ) {
					$key = 'lightboxCssClass';
				}
				if ( $key == 'class' ) {
					$key                              = 'lightboxCssClass';
					$flipbook['lightboxThumbnailUrl'] = '';
					$flipbook['mode']                 = 'lightbox';
				}

				if ( $key == 'lightboxthumbnailurl' ) {
					$key = 'lightboxThumbnailUrl';
				}
				if ( $key == 'thumbcss' ) {
					$key = 'lightboxThumbnailUrlCSS';
				}
				if ( $key == 'thumb' ) {
					$key = 'lightboxThumbnailUrl';
				}
				if ( $key == 'containercss' ) {
					$key = 'lightboxContainerCSS';
				}
				if ( $key == 'lightboxopened' ) {
					$key = 'lightBoxOpened';
				}
				if ( $key == 'lightboxfullscreen' ) {
					$key = 'lightBoxFullscreen';
				}

				if ( $key == 'aspect' ) {
					$key = 'containerRatio';
				}

				if ( $key == 'singlepage' ) {
					$key = 'singlePageMode';
				}

				if ( $key == 'startpage' ) {
					$key = 'startPage';
				}

				if ( $key == 'deeplinkingprefix' ) {
					$flipbook['deeplinking']['prefix'] = $val;
				}

				if ( $key == 'search' ) {
					$key = 'searchOnStart';
				}

				if ( $key == 'thumbalt' ) {
					$key = 'thumbAlt';
				}
				if ( $key == 'pagenumberoffset' ) {
					$key = 'pageNumberOffset';
				}

				if ( $key == 'pagerangestart' ) {
					$key = 'pageRangeStart';
				}
				if ( $key == 'pagerangeend' ) {
					$key = 'pageRangeEnd';
				}
				if ( $key == 'previewpages' ) {
					$key = 'previewPages';
				}
				if ( $key == 'rtl' ) {
					$key = 'rightToLeft';
				}

				$flipbook[ $key ] = $val;
			}
		}

		if ( isset( $flipbook['pdfUrl'] ) && $flipbook['pdfUrl'] ) {

			$pdf_url = esc_url( $flipbook['pdfUrl'] );

			$flipbook['securePdf'] = $args['securepdf'] != '-1';
			if ( $flipbook['securePdf'] ) {
				$unique_id = uniqid( '0', true );
				$this->register_pdf( $unique_id, $pdf_url );
				$flipbook['pdfUrl'] = admin_url( 'admin-ajax.php' ) . '?action=pdf&id=' . $unique_id . '&_wpnonce=' . wp_create_nonce( 'nonce_flipbook_embed' );
			}
		}

		if ( $args['pages'] != -1 ) {
			$pages = explode( ',', $args['pages'] );

			$thumbs = array();
			if ( $args['thumbs'] != -1 ) {
				$thumbs = explode( ',', $args['thumbs'] );
			}

			$flipbook['pages'] = array();
			foreach ( $pages as $key => $src ) {
				$flipbook['pages'][ $key ]        = array();
				$flipbook['pages'][ $key ]['src'] = esc_url( trim( $src ) );
				if ( ! empty( $thumbs[ $key ] ) ) {
					$flipbook['pages'][ $key ]['thumb'] = esc_url( trim( $thumbs[ $key ] ) );
				}
			}
		}

		$flipbook['rootFolder'] = $this->PLUGIN_DIR_URL;
		$flipbook['version']    = $this->PLUGIN_VERSION;
		$flipbook['uniqueId']   = $bookId;

		if ( ! isset( $flipbook['date'] ) && isset( $flipbook['post_id'] ) ) {
			$flipbook['date'] = get_the_date( 'Y-m-d', get_post( $flipbook['post_id'] ) );
		}

		if ( $args['previewpages'] == -1 ) {
			if ( ! $g['previewMode'] ) {
				$flipbook['previewPages'] = '0';
			} elseif ( $g['previewMode'] == 'logged_out' ) {
				if ( is_user_logged_in() ) {
					$flipbook['previewPages'] = '0';
				}
			} elseif ( $g['previewMode'] == 'woo_purchased_or_subscription' ) {
				$full_access = apply_filters( 'r3d_woo_purchased_or_subscription', false );
				if ( $full_access ) {
					$flipbook['previewPages'] = '0';
				}
			} else {
				$flipbook['previewPages'] = '0';
			}
		}

		$notes       = $flipbook['notes'] ?? array();
		$notesToShow = array();
		foreach ( $notes as $key => $note ) {
			$current_user_id = get_current_user_id();
			// Check if user logged in
			if ( $current_user_id < 1 ) {
				if ( ! isset( $flipbook['btnNotes'] ) ) {
					$flipbook['btnNotes'] = $g['btnNotes'];
				}
				$flipbook['btnNotes']['enabled'] = false;
				break;
			}
			// Note author ID
			$note_author_id = $note['userId'];

			// Note author
			$user = get_userdata( $note_author_id );

			// Note author user roles array.
			$user_roles = $user->roles;

			if ( $note_author_id != $current_user_id ) {
				$note['readonly'] = true;
			}

			// Check if the role you're interested in, is present in the array.
			if ( is_array( $user_roles ) && in_array( 'administrator', $user_roles, true ) ) {
				// Admin note
				$note['type'] = 3;
				array_push( $notesToShow, $note );
			} elseif ( $note_author_id == $current_user_id ) {
				// Current user note
				$note['type'] = 1;
				array_push( $notesToShow, $note );
			} elseif ( class_exists( 'Groups_User' ) ) {
				// Find if note is by author in same group
				$groups_user = new Groups_User( $current_user_id );
				// Get group objects
				$user_groups = $groups_user->groups;
				// Get group ids (user is direct member)
				$user_group_ids = $groups_user->group_ids;
				// Get group ids (user is direct member or by group inheritance)
				$user_group_ids_deep = $groups_user->group_ids_deep;

				foreach ( $user_groups as $group ) {
					// Ignore group "Registered" since all users belong to that group
					if ( $group->name == 'Registered' ) {
						continue;
					}
					$users = $group->users;
					foreach ( $users as $group_user ) {
						if ( $group_user->ID == $note_author_id ) {
							$note['type'] = 2;
							if ( ! in_array( $note, $notesToShow ) ) {
								array_push( $notesToShow, $note );
							}
						}
					}
				}
			}
		}
		$flipbook['notes'] = $notesToShow;

		if ( $g['resumeReading'] == 'true' && is_user_logged_in() ) {
			$userID          = get_current_user_id();
			$meta_key        = 'real3dflipbook_last_page_' . $id;
			$last_saved_page = get_user_meta( $userID, $meta_key, true );
			if ( ! empty( $last_saved_page ) ) {
				$flipbook['startPage'] = $last_saved_page;
			}
		}

		$deeplinking = isset( $flipbook['deeplinking'] ) ? $flipbook['deeplinking'] : $g['deeplinking'];

		if ( ( $deeplinking['enabled'] ?? null ) === 'true' ) {
			if ( empty( $deeplinking['prefix'] ?? '' ) && isset( $flipbook['post_id'] ) ) {
				$post = get_post( $flipbook['post_id'] );
				if ( $post !== null && isset( $post->post_name ) ) {
					$flipbook['deeplinkingPrefix'] = $post->post_name . '/';
				}
			}
		}

		$fbPages = $flipbook['pages'] ?? array();
		$fbPages = is_array( $fbPages ) ? $fbPages : array();

		$basePath = r3d_common_folder_from_pages( $fbPages );

		if ( $basePath ) {

			foreach ( $fbPages as $i => $page ) {

				if ( ! is_array( $page ) ) {
					continue;
				}

				foreach ( array( 'src', 'thumb', 'json' ) as $key ) {

					if ( empty( $page[ $key ] ) || ! is_string( $page[ $key ] ) ) {
						continue;
					}

					$url = str_replace( '\\', '/', $page[ $key ] );

					if ( strpos( $url, $basePath ) === 0 ) {
						$fbPages[ $i ][ $key ] = substr( $url, strlen( $basePath ) );
					}
				}
			}

			$flipbook['basePath'] = $basePath;
			$flipbook['pages']    = $fbPages;
		}

		$flipbook = self::strip_server_only_keys( $flipbook );
		$flipbook = self::sanitize_page_html_for_output( $flipbook );

		$output = '<div class="real3dflipbook" id="' . esc_attr( $bookId ) . '" style="position:absolute;"></div>';

		$json = wp_json_encode( $flipbook, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		$output .= '<script type="application/json" class="real3dflipbook-options" data-book-id="' . esc_attr( $bookId ) . '">';
		$output .= $json;
		$output .= '</script>';

		static $global_printed = false;

		if ( ! $global_printed ) {
			$g_filtered  = self::strip_server_only_keys( $g );
			$json_global = wp_json_encode( $g_filtered, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

			$output .= '<script type="application/json" id="real3dflipbook-global-options">';
			$output .= $json_global;
			$output .= '</script>';

			$global_printed = true;
		}

		if ( ! wp_script_is( 'real3d-flipbook', 'enqueued' ) ) {
			wp_enqueue_script( 'real3d-flipbook' );
		}

		if ( ! wp_script_is( 'real3d-flipbook-embed', 'enqueued' ) ) {
			wp_enqueue_script( 'real3d-flipbook-embed' );

			wp_add_inline_script(
				'real3d-flipbook-embed',
				'var r3d = ' . wp_json_encode(
					array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'nonce_flipbook_embed' ),
					)
				) . ';',
				'before'
			);
		}

		if ( ! wp_style_is( 'real3d-flipbook-style', 'enqueued' ) ) {
			wp_enqueue_style( 'real3d-flipbook-style' );
		}

		return $output;
	}

	private static function sanitize_page_html_for_output( $data ) {
		if ( empty( $data['pages'] ) || ! is_array( $data['pages'] ) ) {
			return $data;
		}

		foreach ( $data['pages'] as &$page ) {
			if ( ! is_array( $page ) ) {
				continue;
			}

			if ( isset( $page['htmlContent'] ) && is_string( $page['htmlContent'] ) ) {
				$page['htmlContent'] = wp_kses_post( $page['htmlContent'] );
			}

			if ( ! empty( $page['items'] ) && is_array( $page['items'] ) ) {
				foreach ( $page['items'] as &$item ) {
					if ( isset( $item['content'] ) && is_string( $item['content'] ) ) {
						$item['content'] = wp_kses_post( $item['content'] );
					}
				}
				unset( $item );
			}
		}
		unset( $page );

		return $data;
	}

	private static function strip_server_only_keys( $data ) {
		$server_only = array(
			'post_id',
			'id',
			'post_status',
			'access',
			'securePdf',
			'manageFlipbooks',
			'slug',
			'capability',
		);

		foreach ( $server_only as $key ) {
			unset( $data[ $key ] );
		}

		if ( isset( $data['notes'] ) && is_array( $data['notes'] ) ) {
			foreach ( $data['notes'] as &$note ) {
				if ( is_array( $note ) ) {
					unset( $note['user_id'] );
				}
			}
			unset( $note );
		}

		return $data;
	}
}

if ( ! function_exists( 'r3d_array_merge_deep' ) ) {
	function r3d_array_merge_deep( $array1, $array2 ) {
		$merged = $array1;

		if ( ! is_array( $array2 ) ) {
			return $merged;
		}

		foreach ( $array2 as $key => &$value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				$merged[ $key ] = r3d_array_merge_deep( $merged[ $key ], $value );
			} else {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}
}

if ( ! function_exists( 'r3d_common_folder_from_pages' ) ) {
	function r3d_common_folder_from_pages( array $pages, array $keys = array( 'src', 'thumb', 'json' ) ): ?string {
		$dirs = array();

		foreach ( $pages as $p ) {
			if ( ! is_array( $p ) ) {
				continue;
			}

			foreach ( $keys as $k ) {
				if ( empty( $p[ $k ] ) || ! is_string( $p[ $k ] ) ) {
					continue;
				}

				$u = str_replace( '\\', '/', $p[ $k ] );
				$u = preg_split( '/[?#]/', $u, 2 )[0]; // strip query/fragment

				$pos = strrpos( $u, '/' );
				if ( $pos === false ) {
					continue;
				}

				$dirs[] = substr( $u, 0, $pos + 1 ); // keep trailing slash
			}
		}

		if ( count( $dirs ) < 2 ) {
			return null;
		}

		// Common prefix of segments
		$common = explode( '/', rtrim( $dirs[0], '/' ) );

		for ( $i = 1; $i < count( $dirs ); $i++ ) {
			$seg = explode( '/', rtrim( $dirs[ $i ], '/' ) );
			$max = min( count( $common ), count( $seg ) );

			$j = 0;
			while ( $j < $max && $common[ $j ] === $seg[ $j ] ) {
				++$j;
			}

			$common = array_slice( $common, 0, $j );
			if ( ! $common ) {
				return null;
			}
		}

		$base = implode( '/', $common ) . '/';

		// sanity: avoid returning something too generic
		if ( strlen( $base ) < 12 ) {
			return null;
		}

		return $base;
	}
}

function r3dfb_getDefaults() {
	return array(

		'pages'                            => array(),
		'pdfUrl'                           => '',
		'printPdfUrl'                      => '',
		'tableOfContent'                   => array(),
		'id'                               => '',
		'bookId'                           => '',
		'date'                             => '',
		'lightboxThumbnailUrl'             => '',
		'mode'                             => 'normal',
		'viewMode'                         => 'webgl',
		'pageTextureSize'                  => '3000',
		'pageTextureSizeSmall'             => '1500',
		'pageTextureSizeMobile'            => '1500',
		'pageTextureSizeMobileSmall'       => '1000',
		'rangeChunkSize'                   => '256',
		'minPixelRatio'                    => '1',
		'pdfTextLayer'                     => 'true',
		'zoomMin'                          => '0.9',
		'zoomStep'                         => '2',
		'zoomSize'                         => '',
		'zoomReset'                        => 'false',
		'doubleClickZoom'                  => 'true',
		'pageDrag'                         => 'true',
		'singlePageMode'                   => 'false',
		'pageFlipDuration'                 => '1',
		'sound'                            => 'true',
		'startPage'                        => '1',
		'pageNumberOffset'                 => '0',
		'deeplinking'                      => array(
			'enabled' => 'false',
			'prefix'  => '',
		),
		'responsiveView'                   => 'true',
		'responsiveViewTreshold'           => '768',
		'responsiveViewRatio'              => '1',
		'minimalView'                      => 'true',
		'minimalViewBreakpoint'            => '600',
		'cover'                            => 'true',
		'backCover'                        => 'true',
		'scaleCover'                       => 'false',
		'pageCaptions'                     => 'false',
		'height'                           => '400',
		'responsiveHeight'                 => 'true',
		'containerRatio'                   => '',
		'thumbnailsOnStart'                => 'false',
		'contentOnStart'                   => 'false',
		'searchOnStart'                    => '',
		'searchResultsThumbs'              => 'false',
		'tableOfContentCloseOnClick'       => 'true',
		'thumbsCloseOnClick'               => 'true',
		'autoplayOnStart'                  => 'false',
		'autoplayInterval'                 => '3000',
		'autoplayLoop'                     => 'true',
		'autoplayStartPage'                => '1',
		'rightToLeft'                      => 'false',
		'pageWidth'                        => '',
		'pageHeight'                       => '',
		'thumbSize'                        => '130',
		'logoImg'                          => '',
		'logoUrl'                          => '',
		'logoUrlTarget'                    => '',
		'logoCSS'                          => 'position:absolute;left:0;top:0;',
		'menuSelector'                     => '',
		'zIndex'                           => 'auto',
		'preloaderText'                    => '',
		'googleAnalyticsTrackingCode'      => '',
		'pdfBrowserViewerIfIE'             => 'false',
		'modeMobile'                       => '',
		'viewModeMobile'                   => '',
		'aspectMobile'                     => '',
		'pageTextureSizeMobile'            => '',
		'aspectRatioMobile'                => '0.71',
		'singlePageModeIfMobile'           => 'false',
		'logoHideOnMobile'                 => 'false',
		'mobile'                           => array(
			'thumbnailsOnStart'     => 'false',
			'contentOnStart'        => 'false',
			'pagesInMemory'         => '6',
			'bitmapResizeHeight'    => '',
			'bitmapResizeQuality'   => '',
			'currentPage'           => array(
				'enabled' => 'false',
			),
			'pdfUrl'                => '',
			'minimalViewBreakpoint' => '360',

		),
		'lightboxCssClass'                 => '',
		'lightboxLink'                     => '',
		'lightboxLinkNewWindow'            => 'true',
		'lightboxBackground'               => 'rgb(81, 85, 88)',
		'lightboxBackgroundPattern'        => '',
		'lightboxBackgroundImage'          => '',
		'lightboxContainerCSS'             => 'display:inline-block;padding:10px;',
		'lightboxThumbnailHeight'          => '300',
		'lightboxThumbnailUrlCSS'          => 'display:block;',
		'lightboxThumbnailInfo'            => 'false',
		'lightboxThumbnailInfoText'        => '',
		'lightboxThumbnailInfoCSS'         => 'top: 0;  width: 100%; height: 100%; font-size: 16px; color: #000; background: rgba(255,255,255,.8); ',
		'showTitle'                        => 'false',
		'showDate'                         => 'false',
		'hideThumbnail'                    => 'false',
		'lightboxText'                     => '',
		'lightboxTextCSS'                  => 'display:block;',
		'lightboxTextPosition'             => 'top',
		'lightBoxOpened'                   => 'false',
		'lightBoxFullscreen'               => 'false',
		'lightboxStartPage'                => '',
		'lightboxMarginV'                  => '0',
		'lightboxMarginH'                  => '0',
		'lights'                           => 'true',
		'lightPositionX'                   => '0',
		'lightPositionY'                   => '150',
		'lightPositionZ'                   => '1400',
		'lightIntensity'                   => '0.6',
		'shadows'                          => 'true',
		'shadowMapSize'                    => '2048',
		'shadowOpacity'                    => '0.2',
		'shadowDistance'                   => '15',
		'pageHardness'                     => '2',
		'coverHardness'                    => '2',
		'pageRoughness'                    => '1',
		'pageMetalness'                    => '0',
		'pageSegmentsW'                    => '6',
		'pageSegmentsH'                    => '1',
		'pagesInMemory'                    => '20',
		'bitmapResizeHeight'               => '',
		'bitmapResizeQuality'              => '',
		'pageMiddleShadowSize'             => '4',
		'pageMiddleShadowColorL'           => '#7F7F7F',
		'pageMiddleShadowColorR'           => '#AAAAAA',
		'antialias'                        => 'false',
		'pan'                              => '0',
		'tilt'                             => '0',
		'rotateCameraOnMouseDrag'          => 'true',
		'panMax'                           => '20',
		'panMin'                           => '-20',
		'tiltMax'                          => '0',
		'tiltMin'                          => '0',
		'currentPage'                      => array(
			'enabled' => 'true',
			'title'   => __( 'Current page', 'real3d-flipbook' ),
			'hAlign'  => 'left',
			'vAlign'  => 'top',
		),
		'btnAutoplay'                      => array(
			'enabled' => 'true',
			'title'   => __( 'Auto flip', 'real3d-flipbook' ),
		),
		'btnNext'                          => array(
			'enabled' => 'true',
			'title'   => __( 'Next Page', 'real3d-flipbook' ),
		),
		'btnLast'                          => array(
			'enabled' => 'false',
			'title'   => __( 'Last Page', 'real3d-flipbook' ),
		),
		'btnPrev'                          => array(
			'enabled' => 'true',
			'title'   => __( 'Previous Page', 'real3d-flipbook' ),
		),
		'btnFirst'                         => array(
			'enabled' => 'false',
			'title'   => __( 'First Page', 'real3d-flipbook' ),
		),
		'btnZoomIn'                        => array(
			'enabled' => 'true',
			'title'   => __( 'Zoom in', 'real3d-flipbook' ),
		),
		'btnZoomOut'                       => array(
			'enabled' => 'true',
			'title'   => __( 'Zoom out', 'real3d-flipbook' ),
		),
		'btnToc'                           => array(
			'enabled' => 'true',
			'title'   => __( 'Table of Contents', 'real3d-flipbook' ),
		),
		'btnThumbs'                        => array(
			'enabled' => 'true',
			'title'   => __( 'Pages', 'real3d-flipbook' ),
		),
		'btnShare'                         => array(
			'enabled' => 'true',
			'title'   => __( 'Share', 'real3d-flipbook' ),
		),
		'btnNotes'                         => array(
			'enabled' => 'false',
			'title'   => __( 'Notes', 'real3d-flipbook' ),
		),
		'btnDownloadPages'                 => array(
			'enabled' => 'false',
			'url'     => '',
			'title'   => __( 'Download pages', 'real3d-flipbook' ),
		),
		'btnDownloadPdf'                   => array(
			'enabled'         => 'true',
			'url'             => '',
			'title'           => __( 'Download PDF', 'real3d-flipbook' ),
			'forceDownload'   => 'true',
			'openInNewWindow' => 'true',
		),
		'btnSound'                         => array(
			'enabled' => 'true',
			'title'   => __( 'Sound', 'real3d-flipbook' ),
		),
		'btnExpand'                        => array(
			'enabled' => 'true',
			'title'   => __( 'Toggle fullscreen', 'real3d-flipbook' ),
		),
		'btnSingle'                        => array(
			'enabled' => 'true',
			'title'   => __( 'Toggle single page', 'real3d-flipbook' ),
		),
		'btnSearch'                        => array(
			'enabled' => 'false',
			'title'   => __( 'Search', 'real3d-flipbook' ),
		),
		'search'                           => array(
			'enabled' => 'false',
			'title'   => __( 'Search', 'real3d-flipbook' ),
		),
		'btnBookmark'                      => array(
			'enabled' => 'false',
			'title'   => __( 'Bookmark', 'real3d-flipbook' ),
		),
		'btnPrint'                         => array(
			'enabled' => 'true',
			'title'   => __( 'Print', 'real3d-flipbook' ),
		),
		'btnTools'                         => array(
			'enabled' => 'true',
			'title'   => __( 'More', 'real3d-flipbook' ),
		),
		'btnClose'                         => array(
			'enabled' => 'true',
			'title'   => __( 'Close', 'real3d-flipbook' ),
		),

		'whatsapp'                         => array(
			'enabled' => 'true',
		),
		'twitter'                          => array(
			'enabled' => 'true',
		),
		'facebook'                         => array(
			'enabled' => 'true',
		),
		'pinterest'                        => array(
			'enabled' => 'true',
		),
		'email'                            => array(
			'enabled' => 'true',
		),
		'linkedin'                         => array(
			'enabled' => 'true',
		),
		'digg'                             => array(
			'enabled' => 'false',
		),
		'reddit'                           => array(
			'enabled' => 'false',
		),

		'shareUrl'                         => '',
		'shareTitle'                       => '',
		'shareImage'                       => '',

		'layout'                           => 1,
		'icons'                            => 'FontAwesome',
		'skin'                             => 'light',
		'useFontAwesome5'                  => 'true',
		'sideNavigationButtons'            => 'true',
		'menuNavigationButtons'            => 'false',
		'backgroundColor'                  => 'rgb(81, 85, 88)',
		'backgroundPattern'                => '',
		'backgroundImage'                  => '',
		'backgroundTransparent'            => 'false',

		'menuBackground'                   => '',
		'menuShadow'                       => '',
		'menuMargin'                       => '0',
		'menuPadding'                      => '0',
		'menuOverBook'                     => 'false',
		'menuFloating'                     => 'false',
		'menuTransparent'                  => 'false',

		'menu2Background'                  => '',
		'menu2Shadow'                      => '',
		'menu2Margin'                      => '0',
		'menu2Padding'                     => '0',
		'menu2OverBook'                    => 'true',
		'menu2Floating'                    => 'false',
		'menu2Transparent'                 => 'true',

		'skinColor'                        => '',
		'skinBackground'                   => '',

		'hideMenu'                         => 'false',
		'menuAlignHorizontal'              => 'center',
		'btnColor'                         => '',
		'btnColorHover'                    => '',
		'btnBackground'                    => 'none',
		'btnRadius'                        => '0',
		'btnMargin'                        => '0',
		'btnSize'                          => '18',
		'btnPaddingV'                      => '10',
		'btnPaddingH'                      => '10',
		'btnShadow'                        => '',
		'btnTextShadow'                    => '',
		'btnBorder'                        => '',
		'arrowColor'                       => '#fff',
		'arrowColorHover'                  => '#fff',
		'arrowBackground'                  => 'rgba(0,0,0,0)',
		'arrowBackgroundHover'             => 'rgba(0, 0, 0, .15)',
		'arrowRadius'                      => '4',
		'arrowMargin'                      => '4',
		'arrowSize'                        => '40',
		'arrowPadding'                     => '10',
		'arrowTextShadow'                  => '0px 0px 1px rgba(0, 0, 0, 1)',
		'arrowBorder'                      => '',
		'closeBtnColorHover'               => '#FFF',
		'closeBtnBackground'               => 'rgba(0,0,0,.4)',
		'closeBtnRadius'                   => '0',
		'closeBtnMargin'                   => '0',
		'closeBtnSize'                     => '20',
		'closeBtnPadding'                  => '5',
		'closeBtnTextShadow'               => '',
		'closeBtnBorder'                   => '',
		'floatingBtnColor'                 => '',
		'floatingBtnColorHover'            => '',
		'floatingBtnBackground'            => '',
		'floatingBtnBackgroundHover'       => '',
		'floatingBtnRadius'                => '',
		'floatingBtnMargin'                => '',
		'floatingBtnSize'                  => '',
		'floatingBtnPadding'               => '',
		'floatingBtnShadow'                => '',
		'floatingBtnTextShadow'            => '',
		'floatingBtnBorder'                => '',
		'currentPageMarginV'               => '5',
		'currentPageMarginH'               => '5',
		'arrowsAlwaysEnabledForNavigation' => 'true',
		'arrowsDisabledNotFullscreen'      => 'true',
		'touchSwipeEnabled'                => 'true',
		'fitToWidth'                       => 'false',
		'rightClickEnabled'                => 'true',
		'linkColor'                        => 'rgba(0, 0, 0, 0)',
		'linkColorHover'                   => 'rgba(255, 255, 0, 1)',
		'linkOpacity'                      => '0.4',
		'linkTarget'                       => '_blank',
		'pdfAutoLinks'                     => 'false',
		'disableRange'                     => 'false',

		'strings'                          => array(
			'currentPage'          => __( 'Current page', 'real3d-flipbook' ),
			'firstPage'            => __( 'First page', 'real3d-flipbook' ),
			'previousPage'         => __( 'Previous page', 'real3d-flipbook' ),
			'nextPage'             => __( 'Next page', 'real3d-flipbook' ),
			'lastPage'             => __( 'Last page', 'real3d-flipbook' ),
			'zoomIn'               => __( 'Zoom in', 'real3d-flipbook' ),
			'zoomOut'              => __( 'Zoom out', 'real3d-flipbook' ),
			'rotateLeft'           => __( 'Rotate left', 'real3d-flipbook' ),
			'rotateRight'          => __( 'Rotate right', 'real3d-flipbook' ),
			'autoFlip'             => __( 'Auto flip', 'real3d-flipbook' ),
			'search'               => __( 'Search', 'real3d-flipbook' ),
			'bookmarks'            => __( 'Bookmarks', 'real3d-flipbook' ),
			'notes'                => __( 'Notes', 'real3d-flipbook' ),
			'tableOfContent'       => __( 'Table of Contents', 'real3d-flipbook' ),
			'pages'                => __( 'Pages', 'real3d-flipbook' ),
			'thumbnails'           => __( 'Thumbnails', 'real3d-flipbook' ),
			'share'                => __( 'Share', 'real3d-flipbook' ),
			'print'                => __( 'Print', 'real3d-flipbook' ),
			'download'             => __( 'Download', 'real3d-flipbook' ),
			'downloadPdf'          => __( 'Download PDF', 'real3d-flipbook' ),
			'sound'                => __( 'Sound', 'real3d-flipbook' ),
			'more'                 => __( 'More', 'real3d-flipbook' ),
			'toggleFullscreen'     => __( 'Toggle fullscreen', 'real3d-flipbook' ),
			'toggleSinglePage'     => __( 'Toggle single page', 'real3d-flipbook' ),
			'close'                => __( 'Close', 'real3d-flipbook' ),
			'printLeftPage'        => __( 'Print left page', 'real3d-flipbook' ),
			'printRightPage'       => __( 'Print right page', 'real3d-flipbook' ),
			'printCurrentPage'     => __( 'Print current page', 'real3d-flipbook' ),
			'printAllPages'        => __( 'Print all pages', 'real3d-flipbook' ),
			'downloadLeftPage'     => __( 'Download left page', 'real3d-flipbook' ),
			'downloadRightPage'    => __( 'Download right page', 'real3d-flipbook' ),
			'downloadCurrentPage'  => __( 'Download current page', 'real3d-flipbook' ),
			'downloadAllPages'     => __( 'Download all pages', 'real3d-flipbook' ),
			'bookmarkLeftPage'     => __( 'Bookmark left page', 'real3d-flipbook' ),
			'bookmarkRightPage'    => __( 'Bookmark right page', 'real3d-flipbook' ),
			'bookmarkCurrentPage'  => __( 'Bookmark current page', 'real3d-flipbook' ),
			'findInDocument'       => __( 'Find in document', 'real3d-flipbook' ),
			'pagesFoundContaining' => __( 'pages found containing', 'real3d-flipbook' ),
			'noMatches'            => __( 'No matches', 'real3d-flipbook' ),
			'matchesFound'         => __( 'matches found', 'real3d-flipbook' ),
			'page'                 => __( 'Page', 'real3d-flipbook' ),
			'match'                => __( 'match', 'real3d-flipbook' ),
			'matches'              => __( 'matches', 'real3d-flipbook' ),
			'pressEscToClose'      => __( 'Press ESC to close', 'real3d-flipbook' ),
			'password'             => __( 'Password', 'real3d-flipbook' ),
			'addNote'              => __( 'Add note', 'real3d-flipbook' ),
			'typeInYourNote'       => __( 'Type in your note...', 'real3d-flipbook' ),
			'copyLink'             => __( 'Copy link', 'real3d-flipbook' ),
			'copied'               => __( 'Copied', 'real3d-flipbook' ),
		),

		'access'                           => 'free', // free, woo_subscription, ...
		'backgroundMusic'                  => '',
		'cornerCurl'                       => 'false',
		'pdfTools'                         => array(
			'pageHeight'  => 1500,
			'thumbHeight' => 200,
			'quality'     => 0.8,
			'textLayer'   => 'true',
			'autoConvert' => 'true',
		),
		'slug'                             => '',
		'convertPDFLinks'                  => 'true',
		'convertPDFLinksWithClass'         => '',
		'convertPDFLinksWithoutClass'      => '',
		'overridePDFEmbedder'              => 'true',
		'overrideDflip'                    => 'true',
		'overrideWonderPDFEmbed'           => 'true',
		'override3DFlipBook'               => 'true',
		'overridePDFjsViewer'              => 'true',
		'resumeReading'                    => 'false',
		'previewPages'                     => '',
		'previewMode'                      => '',
	);
}

Real3DFlipbook::get_instance();
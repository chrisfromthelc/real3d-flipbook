<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$r3d_addon_urls = array(
	'bundle'      => array(
		'info' => 'https://real3dflipbook.com/addons/',
	),
	'pdfTools'    => array(
		'info' => 'https://real3dflipbook.com/pdf-tools-addon/',
	),
	'pageEditor'  => array(
		'info' => 'https://real3dflipbook.com/page-editor-addon-for-real-3d-flipbook/',
	),
	'bookShelf'   => array(
		'info' => 'https://real3dflipbook.com/real3d-flipbook-bookshelf-addon/',
	),
	'elementor'   => array(
		'info' => 'https://real3dflipbook.com/elementor-addon/',
	),
	'wooCommerce' => array(
		'info' => 'https://real3dflipbook.com/woocommerce-addon/',
	),
	'wpBakery'    => array(
		'info' => 'https://real3dflipbook.com/wpbakery-addon/',
	),
	'previewMode' => array(
		'info' => 'https://real3dflipbook.com/preview-mode-addon/',
	),
);


function createAddon( $name, $title, $description, $r3d_addon_urls, $isInstalled = false ) {
	$infoUrl = isset( $r3d_addon_urls[ $name ]['info'] ) ? esc_url( $r3d_addon_urls[ $name ]['info'] ) : '';
	?>
	<div class="addons-banner-block-item">
		<div class="addons-banner-block-item-content">
			<h3><?php echo esc_html( $title ); ?></h3>
			<p><?php echo esc_html( $description ); ?></p>

			<?php if ( ! empty( $infoUrl ) ) : ?>
				<a class="button button-secondary button-large addons-button"
					href="<?php echo esc_url( $r3d_addon_urls[ $name ]['info'] ); ?>" target="_blank">
					<?php esc_html_e( 'More Info', 'real3d-flipbook' ); ?>
				</a>
			<?php endif; ?>

			<?php if ( $isInstalled ) : ?>
				<span class="button disabled button-primary button-large addons-button">
					<?php esc_html_e( 'Installed', 'real3d-flipbook' ); ?>
				</span>
			<?php endif; ?>
		</div>
	</div>
	<?php
}



?>

<div class='wrap r3d_wrap'>

	<h3><?php esc_html_e( 'Real3D Flipbook Addons', 'real3d-flipbook' ); ?></h3>

	<div class="addons">

		<div class="addons-block">

			<p><?php esc_html_e( 'Make Real3D Flipbook more powerful with Addons', 'real3d-flipbook' ); ?></p>

			<div class="addons-banner-block-items">

				<?php

				createAddon(
					'bundle',
					__( 'Addon Bundle', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'All 7 add-ons: Book Shelf, PDF Tools, Page Editor, WooCommerce, Elementor, WPBakery, Preview Mode, 57% OFF', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					false
				);

				createAddon(
					'pageEditor',
					__( 'Page Editor Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Add links, videos, sounds, Youtube, Vimeo and more to flipbook pages easily with visual editor', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					defined( 'R3D_PAGE_EDITOR_VERSION' )
				);

				createAddon(
					'wooCommerce',
					__( 'WooCommerce Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Display flipbook on WooCommerce single product page', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					defined( 'R3D_WOO_VERSION' )
				);

				createAddon(
					'pdfTools',
					__( 'PDF Tools Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Optimize PDF flipbooks for faster loading by converting PDF to images and JSON', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					defined( 'R3D_PDF_TOOLS_VERSION' )
				);

				createAddon(
					'elementor',
					__( 'Elementor Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Use Real3D Flipbook with Elementor as an element', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					class_exists( 'Elementor_Real3D_Flipbook' )
				);

				createAddon(
					'bookShelf',
					__( 'Bookshelf Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Create responsive book shelves with flipbooks', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					class_exists( 'Bookshelf_Addon' )
				);

				createAddon(
					'wpBakery',
					__( 'WPBakery Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Use Real3D Flipbook with WPBakery page builder', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					class_exists( 'Real3DFlipbook_VCAddon' )
				);

				createAddon(
					'previewMode',
					__( 'Preview Mode Addon', 'real3d-flipbook' ), // escaped in createAddon()
					__( 'Show first x number of pages based on user login status', 'real3d-flipbook' ), // escaped in createAddon()
					$r3d_addon_urls,
					class_exists( 'R3D_Preview' )
				);



				?>

			</div>

		</div>

	</div>

</div>

<?php

wp_enqueue_style( 'real3d-flipbook-admin' );

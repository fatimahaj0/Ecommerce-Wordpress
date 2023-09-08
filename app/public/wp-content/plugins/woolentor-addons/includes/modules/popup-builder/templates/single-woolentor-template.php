<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( class_exists('\Elementor\Plugin') ){
	\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-canvas' );
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); ?></title>
	<?php endif; ?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<div class="wlpb-elementor-popup-editor">
		<div class="wlpb-elementor-popup-editor-content-wrapper">
			<div class="wlpb-elementor-popup-editor-overlay"></div>
			<div class="wlpb-elementor-popup-editor-content">
				<span class="wlpb-popup-close-btn">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"></rect><g><path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"></path></g></svg>
				</span>
				
				<?php
					wp_enqueue_style('wlpb-frontend');
					// Print elementor editor
					\Elementor\Plugin::$instance->modules_manager->get_modules( 'page-templates' )->print_content();
				?>
			</div>
		</div>
	</div>

	<?php wp_footer(); ?>
</body>
</html>
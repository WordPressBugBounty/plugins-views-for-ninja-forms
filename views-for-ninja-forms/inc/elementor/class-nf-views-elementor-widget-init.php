<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @since 1.1.0
 */

class NF_Views_Elementor_Widget_Init {
	function __construct() {

		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
	}

	function register_widget( $widgets_manager ) {

		require_once NF_VIEWS_DIR_URL . '/inc/elementor/class-nf-views-elementor-widget.php';

		$widgets_manager->register( new \NF_Views_Elementor_Widget() );

	}

}
new NF_Views_Elementor_Widget_Init();

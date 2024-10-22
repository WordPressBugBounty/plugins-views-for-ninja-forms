<?php
/*
 * Plugin Name: Views for Ninja Forms
 * Plugin URI: https://nfviews.com
 * Description: Display Ninja Forms Submissions on site frontend.
 * Version: 3.1
 * Author: WebHolics
 * Author URI: https://nfviews.com
 * Text Domain: views-for-ninja-forms
 * Requires Plugins:ninja-forms
 * Copyright 2024.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nf_views_lite_activate() {
	if ( is_plugin_active( 'views-for-ninja-forms-pro/nf-form-views.php' ) ) {
		deactivate_plugins( 'views-for-ninja-forms-pro/nf-form-views.php' );
	}
}
register_activation_hook( __FILE__, 'nf_views_lite_activate' );

function nf_views_load_plugin_textdomain() {
	load_plugin_textdomain( 'views-for-ninja-forms', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'nf_views_load_plugin_textdomain' );




define( 'NF_VIEWS_URL', plugins_url() . '/' . basename( dirname( __FILE__ ) ) );
define( 'NF_VIEWS_DIR_URL', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );

 add_action( 'plugins_loaded', 'nf_views_lite_include_files' );

function nf_views_lite_include_files() {

	 require_once NF_VIEWS_DIR_URL . '/inc/helpers.php';

	// Backend
	require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-posttype.php';
	require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-list-table.php';
	require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-editor.php';
	require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-ajax.php';
	require_once NF_VIEWS_DIR_URL . '/inc/admin/review/class-nf-views-review.php';
	require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-lite-support.php';
	require_once NF_VIEWS_DIR_URL . '/inc/elementor/class-nf-views-elementor-widget-init.php';
	require_once NF_VIEWS_DIR_URL . '/inc/admin/class-nf-views-upgrade-to-pro-page.php';
	// Frontend
	require_once NF_VIEWS_DIR_URL . '/inc/pagination.php';
	require_once NF_VIEWS_DIR_URL . '/inc/class-nf-views-field-values.php';
	require_once NF_VIEWS_DIR_URL . '/inc/class-nf-views-shortcode.php';

}

 add_action( 'admin_enqueue_scripts', 'nf_views_admin_scripts' );

 add_action( 'wp_enqueue_scripts', 'nf_views_frontend_scripts' );

function nf_views_admin_scripts( $hook ) {
	global $post;

	if ( ( $hook === 'edit.php' ) && ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'nf-views' ) ) {
		wp_enqueue_style( 'sweet-alert', NF_VIEWS_URL . '/assets/css/sweetalert2.min.css' );
		wp_enqueue_script( 'sweet-alert', NF_VIEWS_URL . '/assets/js/sweetalert2.min.js', array( 'jquery' ), '', true );

		wp_enqueue_style( 'nf_views_admin', NF_VIEWS_URL . '/assets/css/admin.css' );
		wp_enqueue_script( 'nf_views_admin', NF_VIEWS_URL . '/assets/js/admin.js', array( 'jquery' ), '', true );
		$nf_views_admin = array(
			'admin_url'    => admin_url(),
			'create_nonce' => wp_create_nonce( 'nf-views-create' ),
		);
		wp_localize_script( 'nf_views_admin', 'nf_views_admin', $nf_views_admin );

	}
	if ( $hook === 'admin_page_nf-views' || $hook === 'dashboard_page_nf-views' ) {
		wp_enqueue_style( 'fontawesome', NF_VIEWS_URL . '/assets/css/font-awesome.css' );
		wp_enqueue_style( 'pure-css', NF_VIEWS_URL . '/assets/css/pure-min.css' );
		wp_enqueue_style( 'pure-grid-css', NF_VIEWS_URL . '/assets/css/grids-responsive-min.css' );
		wp_enqueue_style( 'nf-views-editor', NF_VIEWS_URL . '/assets/css/nf-views-editor.css', array( 'wp-components' ) );

		$js_dir   = NF_VIEWS_DIR_URL . '/build/static/js';
		$js_files = array_diff( scandir( $js_dir ), array( '..', '.' ) );
		$count    = 0;
		foreach ( $js_files as $js_file ) {
			if ( strpos( $js_file, '.js.map' ) === false ) {
				$js_file_name = $js_file;
				wp_enqueue_script( 'nf_views_script' . $count, NF_VIEWS_URL . '/build/static/js/' . $js_file_name, array( 'jquery' ), '', true );
				$count++;
			}
		}

		$css_dir   = NF_VIEWS_DIR_URL . '/build/static/css';
		$css_files = array_diff( scandir( $css_dir ), array( '..', '.' ) );
		foreach ( $css_files as $css_file ) {
			if ( strpos( $css_file, '.css.map' ) === false ) {
				$css_file_name = $css_file;
			}
		}

		wp_enqueue_style( 'nf_views_style', NF_VIEWS_URL . '/build/static/css/' . $css_file_name );
	}

}

function nf_views_frontend_scripts() {
	wp_enqueue_style( 'pure-css', NF_VIEWS_URL . '/assets/css/pure-min.css' );
	wp_enqueue_style( 'pure-grid-css', NF_VIEWS_URL . '/assets/css/grids-responsive-min.css' );
	wp_enqueue_style( 'nf-views-front', NF_VIEWS_URL . '/assets/css/nf-views-display.css' );
}

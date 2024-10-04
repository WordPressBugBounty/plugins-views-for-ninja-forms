<?php

class NF_Views_LiteEditor {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'register_sub_menu' ), 8 );
	}
	function register_sub_menu() {

		add_submenu_page( 'custom-settings', 'Edit View', 'Edit View', 'manage_options', 'nf-views', array( &$this, 'views_editor' ) );
	}

	function views_editor() {
		// echo 'here'; die;
			$post_id = (int) $_GET['view_id'];
			if ( class_exists( 'Ninja_Forms' ) ) {

			$forms = Ninja_Forms()->form()->get_forms();
			// echo '<pre>';

			$view_forms = array( array( 'id' => '', 'label' => 'Select' ) );
			foreach ( $forms as $form ) {
				// print_r($models = Ninja_Forms()->form( $form->get_id() )->get_fields()); die;
				$view_forms[] = array( 'id' => $form->get_id(), 'label' => $form->get_setting( 'title' ) );
			}
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'nf_views_metabox', 'nf_views_nonce' );
			// delete_post_meta($post->ID, 'view_settings');
			$nf_view_saved_settings = get_post_meta( $post_id, 'view_settings', true );
			if ( empty( $nf_view_saved_settings ) ) {
				$nf_view_saved_settings = '{}';
				$form_id = '';
				if ( ! empty( $view_forms[1]['id'] ) ) {
					$form_id = $view_forms[1]['id'];
				}
			} else {
				$view_settings = json_decode( html_entity_decode( $nf_view_saved_settings ) );
				$form_id = $view_settings->formId;
			}
			$form_fields = nf_views_lite_get_ninja_form_fields( $form_id );
			$nf_views_config = apply_filters(
				'nf_view_config',
				array(
					'prefix' => 'nf',
					'addons' => array( '' ),
					'nonce'  => wp_create_nonce( 'nf-views-builder' ),
				)
			);
			?>
				<script>
					var view_forms = '<?php echo addslashes( json_encode( $view_forms ) ); ?>';
					var _view_id = '<?php echo $post_id; ?>';
					var _view_title = '<?php echo addslashes( get_the_title( $post_id ) ); ?>';
					var _view_saved_settings = '<?php echo addslashes( $nf_view_saved_settings ); ?>';
					var _view_form_fields =  '<?php echo addslashes( $form_fields ); ?>';
					var _view_config =  '<?php echo addslashes( json_encode( $nf_views_config ) ); ?>';

				</script>
			<?php do_action( 'before_nf_views_builder' ); ?>
				   <div id="views-container"></div>
			<?php do_action( 'after_nf_views_builder' ); ?>
			<?php
		} else {
			echo 'Please install Ninja Forms 3.0 or later to use this plugin';
		}

		?>
			<script>

			(function($){
				$(function(){
				$('#menu-dashboard').removeClass('wp-has-current-submenu','wp-menu-open menu-top');
					$('#menu-posts-nf-views').removeClass('wp-not-current-submenu');
					$('#menu-posts-nf-views').addClass('wp-has-current-submenu','wp-menu-open menu-top');
				})

			})(jQuery)
			</script>

		<?php
	}



}

new NF_Views_LiteEditor();

<?php

class NF_Views_Lite_List_Table {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'disable_add_new' ) );
		add_filter( 'views_edit-nf-views', array( $this, 'nf_views_list_header' ) );
		add_filter( 'get_edit_post_link', array( $this, 'edit_view_link' ), 100, 2 );
		add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 100, 2 );
	}

	/**
	 * Add Add new button and logo on NF views page
	 *
	 * @param [type] $views
	 * @return void
	 */
	public function nf_views_list_header( $views ) {
		if ( class_exists( 'Ninja_Forms' ) ) {

			$forms      = Ninja_Forms()->form()->get_forms();
			$view_forms = array();
			foreach ( $forms as $form ) {
				// print_r($models = Ninja_Forms()->form( $form->get_id() )->get_fields()); die;
				$view_forms[ $form->get_id() ] = $form->get_setting( 'title' );
			}
			?>
		<script>
			var view_forms = '<?php echo addslashes( json_encode( $view_forms ) ); ?>';
		</script>
			<?php
			echo '<div class="nf-views-header"><img  src="' . NF_VIEWS_URL . '/assets/images/nf-views-logo.png" class="nf-views-logo" alt="logo" >
		<a class="add_new_nf_view">Add New</a></div>
	';
		}
		return $views;
	}

	/**
	 * Update Edit View link which dislays on View title hover in View Table
	 *
	 * @param [type] $link
	 * @param [type] $post_id
	 * @return void
	 */
	public function edit_view_link( $link, $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( $post_type === 'nf-views' ) {
			return admin_url( 'admin.php?page=nf-views&view_id=' . $post_id );
		}

		return $link;
	}

	/**
	 * Remove Quick Edit Link for NF Views
	 *
	 * @param [type] $actions
	 * @param [type] $post
	 * @return void
	 */
	public function remove_quick_edit( $actions, $post ) {
		if ( $post->post_type == 'nf-views' ) {
			// Remove "Quick Edit"
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	function disable_add_new() {
		// Hide sidebar link
		global $submenu;
			unset( $submenu['edit.php?post_type=nf-views'][10] );
		// $submenu['edit.php?post_type=nf-views'][10][2] = 'edit.php?post_type=nf-views?addnew=true';
	}



}
new NF_Views_Lite_List_Table();

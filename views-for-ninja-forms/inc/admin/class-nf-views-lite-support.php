<?php
class NF_Views_Lite_Support {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );

	}

	function add_page() {
		add_submenu_page(
			'edit.php?post_type=nf-views',
			__( 'Help', 'textdomain' ),
			__( 'Help', 'textdomain' ),
			'manage_options',
			'nf-views-help',
			array( $this, 'help' )
		);
	}

	function help() {
		?>
		<style>
			.nfviews-views-help{
				padding: 10px 30px 41px 30px;
				background: #fff;
				margin: 20px 20px;
			}
			.nfviews-views-help-content{
				background: #F2F4F5;
				border-radius: 8px;
				padding: 20px;
			}

		</style>

			<div class="nfviews-views-help">
			<h3>Support and feedback</h3>
			<div class="nfviews-views-help-content">
			<p><a target="_blank" href="https://nfviews.com/docs">Please check docs here.</a></p>
			<p><a target="_blank" href="https://nfviews.com/contact">Support is available here.</a></p>
			<p>If you enjoyed using Views for Ninja Forms, <a target="_blank" href="https://wordpress.org/plugins/views-for-ninja-forms/reviews/?filter=5">please leave a positive review here.</a> On the other hand, if you were not satisfied with it, <a target="_blank" href="https://nfviews.com/contact">please let us know the reasons why at this link.</a></p>
			</div>
			</div>

		<?php
	}


}
new NF_Views_Lite_Support();

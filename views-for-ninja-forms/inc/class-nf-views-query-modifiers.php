<?php if ( ! defined( 'ABSPATH' ) ) exit;

class NF_Views_Query_Modifiers {
	private static $instance;

	static $where;
	static $join;
	static $orderby;

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof NF_Views_Query_Modifiers ) ) {
			self::$instance = new NF_Views_Query_Modifiers();
		}
		return self::$instance;
	}

	public function set_join_string( $join ) {
		self::$join= $join;
	}
		public function set_where_string( $where ) {
		self::$where = $where;
	}

		public function set_orderby_string( $orderby ) {
		self::$orderby = $orderby;
	}
	public static function update_join( $join ) {

		if ( ! empty( self::$join ) ) {
			$join .= ' ' . self::$join;
		}
		return $join;
	}


	public static function update_where( $where ) {

		if ( ! empty( self::$where ) ) {
			$where .= ' ' . self::$where;
		}
		return $where;
	}

	public static function update_orderby( $orderby ) {
	// 	var_dump(self::$orderby);
	// echo 'here'; die;
		if ( ! empty( self::$orderby ) ) {
			$orderby = ' ' . self::$orderby;
		}
		return $orderby;
	}

}

function NF_Views_Query_Modifiers() {
	return NF_Views_Query_Modifiers::instance();
}

NF_Views_Query_Modifiers();

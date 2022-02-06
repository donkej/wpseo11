<?php



defined( 'ABSPATH' ) or exit;



class wpSEOde_User {


	

	public static function add_field( $data ) {
		
		$data['twitter'] = 'Twitter Handle';

		return $data;
	}
}
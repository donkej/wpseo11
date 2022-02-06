<?php


defined( 'ABSPATH' ) OR exit;



class wpSEOde_Ajax {
	
	public static function dismiss() {
		if ( in_array( $_POST['key'], array( 'wpseo-upgrade', 'wpseo-infomsg' ) ) ) {
			wpSEOde_Transients::delete( $_POST['key'] );
			wpSEOde_Transients::getDefault( $_POST['key'], false );
		}
		wp_die();
	}
}
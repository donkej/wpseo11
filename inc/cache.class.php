<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Cache {


	

	private static $_cache;


	

	public static function get( $key ) {
		
		if ( empty( $key ) ) {
			return;
		}

		
		$cache = (array) self::$_cache;

		
		if ( empty( $cache[ $key ] ) ) {
			return null;
		}

		

		return $cache[ $key ];
	}


	

	public static function set( $key, $value ) {
		
		if ( empty( $key ) ) {
			return;
		}

		
		$cache = (array) self::$_cache;

		
		$cache[ $key ] = $value;

		
		self::$_cache = $cache;
	}
}
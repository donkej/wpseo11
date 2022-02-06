<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Transients {


	

	public static function get( $key, $callback ) {
		
		if ( empty( $key ) OR empty( $callback ) ) {
			return false;
		}

		
		$data = self::_data();

		
		if ( isset( $data[ $key ] ) ) {
			return $data[ $key ];
		}

		return self::_update( $key, $callback );
	}

	
	public static function getDefault( $key, $val ) {
		
		if ( empty( $key ) ) {
			return false;
		}

		
		$data = self::_data();

		
		if ( isset( $data[ $key ] ) ) {
			return $data[ $key ];
		}

		return self::_updateDefault( $key, $val );
	}

	

	public static function delete( $keys ) {
		
		if ( empty( $keys ) ) {
			return false;
		}

		
		if ( ! is_array( $keys ) ) {
			$keys = array( $keys );
		}

		
		$data = self::_data();

		
		foreach ( $keys as $key ) {
			unset( $data[ $key ] );
		}

		
		self::_set( $data );
	}


	

	private static function _update( $key, $callback ) {
		
		if ( empty( $key ) OR empty( $callback ) OR ! is_callable( $callback ) ) {
			return false;
		}

		
		if ( ( $response = call_user_func( $callback ) ) === false ) {
			return false;
		}

		
		$data = self::_data();

		
		$data[ $key ] = $response;

		
		self::_set( $data );

		return $response;
	}


	

	private static function _updateDefault( $key, $val ) {
		
		if ( empty( $key ) ) {
			return false;
		}

		
		$data = self::_data();

		
		$data[ $key ] = $val;

		
		self::_set( $data );

		return $val;
	}


	

	private static function _data() {
		
		$data = (array) wpSEOde_Cache::get( 'transients' );

		
		if ( ! empty( $data ) ) {
			return $data;
		}

		
		$data = (array) get_transient( 'wpseo' );

		
		wpSEOde_Cache::set( 'transients', $data );

		return $data;
	}


	

	private static function _set( $data ) {
		
		set_transient(
			'wpseo',
			$data,
			60 * 60 * 24
		);

		
		wpSEOde_Cache::set( 'transients', $data );
	}
}
<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Base {


	

	public static function get_locale( $default_locale = null ) {
		
		if ( empty( $default_locale ) ) {
			$default_locale = get_locale();
		}

		
		$custom_locale = $default_locale;

		
		if ( $plugin_locale = wpSEOde_Options::get( 'misc_lang' ) ) {
			if ( in_array( $plugin_locale, array( 'de_DE', 'en_EN' ) ) ) {
				$custom_locale = $plugin_locale;
			}
		}

		
		if ( strpos( $custom_locale, 'de_' ) !== false ) {
			$custom_locale = 'de_DE';
		}

		return $custom_locale;
	}


	

	public static function get_host( $url ) {
		
		preg_match( '@^(?:https?://)?([^/]+)@i', (string) $url, $matches );

		return @$matches[1];
	}


	

	public static function get_file(
		$url,
		$method = 'get',
		$args = array(
			'timeout'    => 30,
			'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:20.0) Gecko/20100101 Firefox/20.0'
		),
		$clean = true
	) {
		
		if ( empty( $url ) OR ! in_array( $method,
				array( 'get', 'post', 'request' ) ) OR ! is_callable( 'wp_remote_' . $method ) ) {
			return new WP_Error(
				'http_request_failed',
				__( 'Empty url or unauthorized method type' )
			);
		}

		
		if ( $clean === true ) {
			$response = call_user_func(
				'wp_remote_' . $method,
				esc_url_raw(
					$url,
					array(
						'http',
						'https'
					)
				),
				$args
			);
		} else {
			$response = call_user_func(
				'wp_remote_' . $method,
				$url,
				$args
			);
		}


		
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$responsecode = 0;
		$responsecode = wp_remote_retrieve_response_code( $response );


		
		if ( $responsecode != 200 ) {
			return new WP_Error(
				'http_request_failed',
				sprintf(
					__( 'Could not open: %s' ),
					$url
				)
			);
		}

		return wp_remote_retrieve_body( $response );
	}
}
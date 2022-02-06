<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Update {


	

	public static function do_update_check( $updateable_plugins ) {
		
		$api_data = self::_get_api_data();

		
		if ( empty( $api_data->version ) ) {
			return $updateable_plugins;
		}

		
		if ( version_compare( $api_data->version, wpSEOde::get_plugin_data( 'Version' ), '>' ) ) {
			$updateable_plugins->response[ WPSEODE_BASE ] = self::_prepare_update_vars( 'update_check' );
		}

		return $updateable_plugins;
	}


	

	private static function _get_api_data() {
		
		if ( ! $data = wpSEOde_Cache::get( 'api_data' ) ) {
			
			$data = self::_do_api_response();

			
			wpSEOde_Cache::set(
				'api_data',
				$data
			);
		}

		return $data;
	}


	

	private static function _do_api_response() {
		
		$response = wpSEOde_Base::get_file(
			strrev( 'nosj.etadpu/ed/nosj/3v/nigulp/ed.oespw.ndc//:ptth' ) 
		);

		
		if ( is_wp_error( $response ) ) {
			return false;
		}

		
		$json = json_decode( $response );

		
		if ( is_object( $json ) ) {
			return $json;
		}

		return false;
	}


	

	public static function provide_plugin_info( $data, $action = null, $args = null ) {
		
		if ( $action != 'plugin_information' OR empty( $args->slug ) OR $args->slug !== 'wpseo' ) {
			return $data;
		}

		return self::_prepare_update_vars(
			'update_info'
		);
	}


	

	private static function _prepare_update_vars( $type ) {
		
		$api_data = self::_get_api_data();

		
		if ( empty( $api_data->version ) ) {
			return false;
		}

		
		$data = new StdClass;

		
		switch ( $type ) {
			case 'update_info':
				$data->name           = 'wpSEO';
				$data->slug           = 'wpseo';
				$data->author         = '<a href="https://wpseo.de" target="_blank">Team wpSEO</a>';
				$data->homepage       = 'https://wpseo.de';
				$data->version        = esc_attr( $api_data->version );
				$data->tested         = esc_attr( $api_data->tested );
				$data->requires       = esc_attr( $api_data->requires );
				$data->last_updated   = esc_attr( $api_data->last_updated );
				$data->download_link  = esc_url( $api_data->download_url );
				$data->sections       = get_object_vars( $api_data->sections );
				$data->upgrade_notice = esc_attr( $api_data->upgrade_notice );
				break;

			case 'update_check':
				$data->id             = 0;
				$data->url            = 'https://wpseo.de';
				$data->slug           = 'wpseo';
				$data->plugin         = WPSEODE_BASE;
				$data->package        = esc_url( $api_data->download_url );
				$data->new_version    = esc_attr( $api_data->version );
				$data->upgrade_notice = esc_attr( $api_data->upgrade_notice );
				$data->tested         = esc_attr( $api_data->tested );
				break;

			default:
				break;
		}

		return $data;
	}
}
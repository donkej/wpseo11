<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Suggest {


	

	public static function add_resources() {
		
		$options = wpSEOde_Options::get();

		
		$version = wpSEOde::get_plugin_data( 'Version' );

		
		wp_enqueue_style(
			'wpseo-suggest',
			wpSEOde::plugin_url( 'css/suggest.min.css' ),
			false,
			$version
		);
		wp_enqueue_script(
			'wpseo-suggest',
			wpSEOde::plugin_url( 'js/suggest.min.js' ),
			array( 'jquery' ),
			$version
		);

		
		$items = array(
			'wpseo_title' => '#_wpseo_edit_title',
			'wpseo_desc'  => '#_wpseo_edit_description',
			'wpseo_key'   => '#_wpseo_edit_keywords',
			'post_title'  => '#title'
		);

		
		if ( ! $options['post_title_suggest'] ) {
			unset( $items['post_title'] );
		}
		if ( ! $options['title_suggest'] ) {
			unset( $items['wpseo_title'] );
		}
		if ( ! $options['desc_suggest'] ) {
			unset( $items['wpseo_desc'] );
		}
		if ( ! $options['key_suggest'] ) {
			unset( $items['wpseo_key'] );
		}

		wp_localize_script(
			'wpseo-suggest',
			'wpseo_vars',
			array(
				'lang'  => esc_js( wpSEOde_Base::get_locale() ),
				'items' => esc_js( implode( ', ', $items ) )
			)
		);
	}
}
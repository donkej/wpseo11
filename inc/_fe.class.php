<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde {


	

	public static function init() {
		
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) OR ( defined( 'DOING_CRON' ) && DOING_CRON ) OR ( defined( 'DOING_AJAX' ) && DOING_AJAX ) OR ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || wpSEOde_License::expired() ) {
			return;
		}

		add_action(
			'init',
			array(
				'wpSEOde_License',
				'shame'
			)
		);

		add_action(
			'init',
			array(
				'wpSEOde_Rewrite',
				'rewrite'
			)
		);

		
		add_filter(
			'the_content',
			array(
				__CLASS__,
				'prepare_content'
			)
		);
		add_filter(
			'the_content',
			array(
				'wpSEOde_Output',
				'replace_vars_callback'
			)
		);
		add_filter(
			'the_title',
			array(
				'wpSEOde_Output',
				'replace_vars_callback'
			)
		);

		
		add_action(
			'template_redirect',
			array(
				'wpSEOde_Output',
				'prepare_redirect'
			),
			class_exists( 'TCB_Landing_Page' ) ? 1 : 10 
		);

		
		add_action(
			'wpseo_the_meta',
			array(
				'wpSEOde_Output',
				'the_output'
			)
		);
		add_action(
			'wpseo_the_title',
			array(
				'wpSEOde_Output',
				'the_title'
			)
		);
		add_action(
			'wpseo_the_desc',
			array(
				'wpSEOde_Output',
				'the_description'
			)
		);
		add_action(
			'wpseo_the_keys',
			array(
				'wpSEOde_Output',
				'the_keywords'
			)
		);
		add_action(
			'wpseo_the_robots',
			array(
				'wpSEOde_Output',
				'the_robots'
			)
		);
		add_action(
			'wpseo_the_canonical',
			array(
				'wpSEOde_Output',
				'the_canonical'
			)
		);
		add_filter(
			'wpseo_set_meta',
			array(
				__CLASS__,
				'set_meta'
			),
			10,
			1
		);
		add_filter(
			'wpseo_get_keywords',
			array(
				'wpSEOde_Output',
				'get_keywords'
			),
			10,
			1
		);
		add_filter(
			'wpseo_get_description',
			array(
				'wpSEOde_Output',
				'get_description'
			),
			10,
			1
		);
		add_filter(
			'wpseo_get_robots',
			array(
				'wpSEOde_Output',
				'get_robots'
			),
			10,
			1
		);
		add_filter(
			'wpseo_get_canonical',
			array(
				'wpSEOde_Output',
				'get_canonical'
			),
			10,
			1
		);
		add_filter(
			'wpseo_get_title',
			array(
				'wpSEOde_Output',
				'get_title'
			),
			10,
			1
		);

		
		if ( class_exists( 'Borlabs\Factory' ) && defined( 'BORLABS_CACHE_VERSION' ) ) {
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'fix' . DIRECTORY_SEPARATOR . '1-borlabs-cache-3.php';
		}

		
		$options = wpSEOde_Options::get();

		
		if ( $options['open_graph'] ) {
			add_filter( 'jetpack_enable_opengraph', '__return_false', 99 );

		} else {

			add_filter(
				'jetpack_open_graph_tags',
				array(
					'wpSEOde_Output',
					'filter_og_tags'
				)
			);
		}

		
		if ( $options['misc_monitor'] && $options['misc_monitor_theme'] ) {
			wpSEOde_Dashboard::actions();
		}
	}


	

	public static function prepare_content( $input ) {
		$output = $input;

		
		if ( is_404() OR is_feed() OR is_trackback() OR is_attachment() OR is_robots() OR post_password_required() OR self::is_mobile() ) {
			return $input;
		}

		
		$output = trim( $input );


		
		if ( strpos( $output, '[wpseo]' ) !== false ) {
			$output = preg_replace(
				'#\[wpseo\](.+?)\[/wpseo\]#s',
				'$1',
				$output
			);
		}


		
		if ( empty( $output ) ) {
			return;
		}

		
		if ( ! is_singular() ) {
			return $output;
		}

		
		if ( wpSEOde_Options::get( 'misc_nextpage' ) && strpos( $output, '<!--nextpage' ) !== false ) {
			return preg_replace(
				'#<!--nextpage(title|desc|keys):(?:.*?)-->(?:<br />)?#uis',
				'',
				$output
			);
		}

		return $output;
	}


	

	public static function is_mobile() {
		return strpos( TEMPLATEPATH, 'wptouch' );
	}


	

	public static function set_meta( $input ) {
		
		if ( empty( $input ) OR ! is_array( $input ) ) {
			return;
		}

		
		$meta = (array) wpSEOde_Cache::get( 'meta' );

		
		$legal_keys = array(
			'title',
			'desc',
			'keys',
			'robots',
			'canonical'
		);

		
		foreach ( $input as $key => $value ) {
			if ( in_array( $key, $legal_keys ) ) {
				if ( $key == 'canonical' ) {
					$meta[ $key ] = esc_url_raw( $value );
				} else {
					$meta[ $key ] = sanitize_text_field( $value );
					$meta[ $key ] = trim( $meta[ $key ], ', ' );
				}
			}
		}

		
		wpSEOde_Cache::set( 'meta', $meta );
	}

	

	public static function get_plugin_data( $field = null ) {
		
		if ( ! $plugin_data = wpSEOde_Cache::get( 'plugin_data' ) ) {

			
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}

			
			$plugin_data = get_plugin_data( WPSEODE_FILE );

			
			wpSEOde_Cache::set( 'plugin_data', $plugin_data );
		}

		
		if ( ! empty( $field ) && isset( $plugin_data[ $field ] ) ) {
			return $plugin_data[ $field ];
		}

		return $plugin_data;
	}
}
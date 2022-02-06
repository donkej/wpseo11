<?php



defined( 'ABSPATH' ) or exit;



class wpSEOde_Dashboard {


	

	private static $_bloginfo;


	

	public static function init() {
		
		if ( ! current_user_can( 'edit_dashboard' ) or wpSEOde_Feedback::get( 'critical' ) || wpSEOde_License::expired() ) {
			return false;
		}

		
		wp_add_dashboard_widget(
			'wpseo_misc_monitor',
			'SEO Monitor',
			array(
				__CLASS__,
				'add_front'
			),
			array(
				__CLASS__,
				'add_settings'
			)
		);

		
		add_action(
			'admin_head',
			array(
				__CLASS__,
				'add_css'
			)
		);

		
		self::actions();
	}


	

	public static function actions() {
		
		$options = self::_options();

		
		if ( $options['xovi'] ) {
			add_action(
				'wpseo_xovi_ovi',
				array(
					__CLASS__,
					'the_xovi_ovi'
				)
			);
		}
		
		if ( $options['seitwert'] ) {
			add_action(
				'wpseo_seit_wert',
				array(
					__CLASS__,
					'the_seit_wert'
				)
			);
		}
		if ( $options['pagespeed'] ) {
			add_action(
				'wpseo_page_speed',
				array(
					__CLASS__,
					'the_page_speed'
				)
			);
		}
		if ( $options['seokicks'] ) {
			add_action(
				'wpseo_seokicks_domainpop',
				array(
					__CLASS__,
					'the_seokicks_domainpop'
				)
			);
			add_action(
				'wpseo_seokicks_linkpop',
				array(
					__CLASS__,
					'the_seokicks_linkpop'
				)
			);
		}
		if ( $options['metricstools'] ) {
			add_action(
				'wpseo_metricstools_sk',
				array(
					__CLASS__,
					'the_metricstools_sk'
				)
			);
		}
		if ( $options['twitter'] ) {
			add_action(
				'wpseo_twitter_count',
				array(
					__CLASS__,
					'the_twitter_follower'
				)
			);
		}
	}


	

	public static function add_css() {
		
		wp_register_style(
			'wpseo',
			wpSEOde::plugin_url( 'css/dashboard.min.css' ),
			false,
			wpSEOde::get_plugin_data( 'Version' )
		);

		
		wp_print_styles( 'wpseo' );
	}


	

	public static function add_front() {
		
		$fields = array();

		
		$options = self::_options();

		
		if ( $options['xovi'] ) {
			$fields[] = array(
				'hook' => 'wpseo_xovi_ovi',
				'name' => 'Xovi OVI',
				'link' => 'http://www.xovi.de/xovi-tool/ovi/'
			);
		}

		
		if ( $options['seitwert'] ) {
			$fields[] = array(
				'hook' => 'wpseo_seit_wert',
				'name' => 'Seitwert',
				'link' => 'http://www.seitwert.de/?url=' . parse_url( get_bloginfo( 'url' ), PHP_URL_HOST )
			);
		}

		
		if ( $options['pagespeed'] ) {
			$fields[] = array(
				'hook' => 'wpseo_page_speed',
				'name' => 'Page Speed',
				'link' => 'https://developers.google.com/speed/pagespeed/insights/?url=' . parse_url( get_bloginfo( 'url' ),
						PHP_URL_HOST )
			);
		}

		
		if ( $options['seokicks'] ) {
			$fields[] = array(
				'hook' => 'wpseo_seokicks_domainpop',
				'name' => 'SEOkicks D-Pop',
				'link' => 'https://www.seokicks.de/backlinks/' . parse_url( get_bloginfo( 'url' ), PHP_URL_HOST )
			);
			$fields[] = array(
				'hook' => 'wpseo_seokicks_linkpop',
				'name' => 'SEOkicks Linkpop',
				'link' => 'https://www.seokicks.de/backlinks/' . parse_url( get_bloginfo( 'url' ), PHP_URL_HOST )
			);
		}

		
		if ( $options['metricstools'] ) {
			$fields[] = array(
				'hook' => 'wpseo_metricstools_sk',
				'name' => 'Metrics Tools SK',
				'link' => 'https://metrics.tools/x/pcju'
			);
		}

		
		if ( $options['twitter'] ) {
			$fields[] = array(
				'hook' => 'wpseo_twitter_count',
				'name' => 'Follower',
				'link' => 'http://twitter.com/' . ( substr( $options['twitter_id'], 0,
						1 ) == '@' ? substr( $options['twitter_id'], 1 ) : $options['twitter_id'] )
			);
		}

		
		
		
		if ( $fields = (array) apply_filters( 'wpseo_set_monitor', $fields ) ) { ?>
            <div class="main">
                <ul>
					<?php foreach ( $fields as $field ) { ?>
                        <li class="icon <?php esc_attr_e( $field['hook'] ) ?>">
                            <a href="<?php echo esc_url( $field['link'], '', 'display' ) ?>" target="_blank">
								<?php do_action( $field['hook'] );
								echo ' ' . esc_html( $field['name'] ) ?>
                            </a>
                        </li>
					<?php } ?>
                </ul>
            </div>
		<?php }
	}


	

	public static function add_settings() {
		
		if ( ! current_user_can( 'edit_dashboard' ) ) {
			return;
		}

		
		if ( ! empty( $_POST['monitor_options'] ) ) {
			
			check_admin_referer( '_wpseo__dashboard_nonce' );

			
			$options = self::_options();

			
			$incoming = (array) $_POST['monitor_options'];

			
			$outgoing = array(
				'twitter_id'      => (string) preg_replace( '/[^a-z0-9_@]/i', '',
					sanitize_text_field( $incoming['twitter_id'] ) ),
				'seitwert_key'    => (string) preg_replace( '/[^a-z0-9]/i', '',
					sanitize_text_field( $incoming['seitwert_key'] ) ),
				
				'facebook_app_id' => (string) preg_replace( '/[^a-z0-9\-_\.]/i', '',
					sanitize_text_field( $incoming['facebook_app_id'] ) ),
				
				'pagespeed_key'   => (string) preg_replace( '/[^a-z0-9-_]/i', '',
					sanitize_text_field( $incoming['pagespeed_key'] ) ),
			);

			
			$outgoing = array_merge(
				$outgoing,
				array(
					'seokicks'     => (int) ( ! empty( $incoming['seokicks'] ) ),
					'metricstools' => (int) ( ! empty( $incoming['metricstools'] ) ),
					'xovi'         => (int) ( ! empty( $incoming['xovi'] ) ),
					
					'twitter'      => (int) ( ! ( empty( $incoming['twitter'] ) || empty( $outgoing['twitter_id'] ) ) ),
					'seitwert'     => (int) ( ! ( empty( $incoming['seitwert'] ) || empty( $outgoing['seitwert_key'] ) ) ),

					'pagespeed' => (int) ( ! ( empty( $incoming['pagespeed'] ) || empty( $outgoing['pagespeed_key'] ) ) )
				)
			);

			
			$transients = array();

			
			if ( $options['seitwert_key'] != $outgoing['seitwert_key'] ) {
				$transients[] = 'seitwert_count';
			}
			if ( $options['pagespeed_key'] != $outgoing['pagespeed_key'] ) {
				$transients[] = 'pagespeed_score';
			}
			
			if ( $options['twitter_id'] != $outgoing['twitter_id'] ) {
				$transients[] = 'twitter_count';
			}

			
			if ( ! empty( $transients ) ) {
				wpSEOde_Transients::delete( $transients );
			}

			
			wpSEOde_Options::update(
				array(
					'monitor_options' => $outgoing
				)
			);
		}

		
		$options = self::_options();

		
		wp_nonce_field( '_wpseo__dashboard_nonce' ); ?>


        <div class="main">
            <ul>
                <li>
                    <label for="wpseo_monitor_metricstools">
                        <input type="checkbox" id="wpseo_monitor_metricstools" name="monitor_options[metricstools]"
                               value="1" <?php checked( $options['metricstools'], 1 ) ?> />
                        Metrics Tools SK
                    </label>
                </li>
                <li style="width: 100%;">
                    <label for="wpseo_monitor_seokicks">
                        <input type="checkbox" id="wpseo_monitor_seokicks" name="monitor_options[seokicks]"
                               value="1" <?php checked( $options['seokicks'], 1 ) ?> />
                        SEOkicks Domainpop + Linkpop
                    </label>
                </li>
				<?php
				
				?>

                <ul>
                    <li>
                        <label for="wpseo_monitor_seitwert">
                            <input type="checkbox" id="wpseo_monitor_seitwert" name="monitor_options[seitwert]"
                                   value="1" <?php checked( $options['seitwert'], 1 ) ?> />
                            Seitwert
                        </label>
						<?php wpSEOde::help_icon( 23 ) ?>
                    </li>
                    <li>
                        <input type="text" name="monitor_options[seitwert_key]"
                               value="<?php echo esc_attr( $options['seitwert_key'] ) ?>"
                               placeholder="Seitwert&nbsp;(API Key)"/>
                    </li>
                </ul>

                <ul>
                    <li>
                        <label for="wpseo_monitor_pagespeed">
                            <input type="checkbox" id="wpseo_monitor_pagespeed" name="monitor_options[pagespeed]"
                                   value="1" <?php checked( $options['pagespeed'], 1 ) ?> />
                            Page Speed
                        </label>
						<?php wpSEOde::help_icon( 23 ) ?>
                    </li>
                    <li>
                        <input type="text" name="monitor_options[pagespeed_key]"
                               value="<?php echo esc_attr( $options['pagespeed_key'] ) ?>"
                               placeholder="Page Speed&nbsp;(API Key)"/>
                    </li>
                </ul>

				<?php
				
				?>
                <ul>
                    <li>Facebook App-ID:<br><small>Zur Identifizierung der Webseite.</small></li>
                    <li>
                        <input type="text" name="monitor_options[facebook_app_id]"
                               value="<?php echo esc_attr( $options['facebook_app_id'] ) ?>"
                               placeholder="Facebook&nbsp;App&nbsp;ID"/>
                    </li>
                </ul>
				<?php
				
				?>
                <ul>
                    <li>
                        <label for="wpseo_monitor_twitter">
                            <input type="checkbox" id="wpseo_monitor_twitter" name="monitor_options[twitter]"
                                   value="1" <?php checked( $options['twitter'], 1 ) ?> />
                            Twitter
                        </label>
						<?php wpSEOde::help_icon( 23 ) ?>
                    </li>
                    <li>
                        <input type="text" name="monitor_options[twitter_id]"
                               value="<?php echo esc_attr( $options['twitter_id'] ) ?>"
                               placeholder="Twitter&nbsp;(ID)"/>
                    </li>
                </ul>

        </div>
	<?php }


	

	private static function _options() {
		return wp_parse_args(
			wpSEOde_Options::get( 'monitor_options' ),
			array(
				'xovi'            => 0,
				'seitwert'        => 0,
				'seitwert_key'    => '',
				'pagespeed'       => 0,
				'pagespeed_key'   => '',
				'twitter'         => 0,
				'twitter_id'      => '',
				
				'facebook_app_id' => '',
				
				'seokicks'        => 0,
				'metricstools'    => 0
			)
		);
	}


	

	private static function _get_bloginfo( $value ) {
		
		if ( ! empty( self::$_bloginfo[ $value ] ) ) {
			return self::$_bloginfo[ $value ];
		}

		
		switch ( $value ) {
			case 'url':
				return self::$_bloginfo[ $value ] = get_bloginfo( 'url' );

			case 'host':
				return self::$_bloginfo[ $value ] = wpSEOde_Base::get_host( get_bloginfo( 'url' ) );

			default:
				return null;
		}
	}

	

	public static function the_xovi_ovi() {
		echo wpSEOde_Transients::get(
			'xovi_count',
			array(
				__CLASS__,
				'get_xovi_ovi'
			)
		);
	}


	

	public static function get_xovi_ovi() {
		
		$response = wpSEOde_Base::get_file(
			add_query_arg(
				urlencode_deep(
					array(
						'domain' => self::_get_bloginfo( 'host' )
					)
				),
				'http://www.xovi.de/ovi_api/'
			)
		);

		
		if ( is_wp_error( $response ) or empty( $response ) ) {
			return 0; 
		}

		
		$index = str_replace( ',', '.', $response );

		
		if ( ! is_numeric( $index ) ) {
			return 0; 
		}

		

		return $response;
	}


	
	

	
	

	

	public static function the_seit_wert() {
		echo number_format_i18n(
			wpSEOde_Transients::get(
				'seitwert_count',
				array(
					__CLASS__,
					'get_seit_wert'
				)
			)
		);
	}


	

	public static function get_seit_wert() {
		
		$options = wpSEOde_Options::get( 'monitor_options' );

		
		if ( empty( $options['seitwert_key'] ) ) {
			return 0; 
		}

		
		$response = wpSEOde_Base::get_file(
			add_query_arg(
				urlencode_deep(
					array(
						'url' => self::_get_bloginfo( 'host' ),
						'api' => $options['seitwert_key']
					)
				),
				'https://www.seitwert.de/api/getseitwert.php'
			)
		);

		
		if ( is_wp_error( $response ) ) {
			return 0; 
		}

		
		$xml = simplexml_load_string( $response );

		

		return (int) @$xml->seitwert;
	}


	

	public static function the_page_speed() {
		echo number_format_i18n(
			wpSEOde_Transients::get(
				'pagespeed_score',
				array(
					__CLASS__,
					'get_page_speed'
				)
			)
		);
	}


	

	public static function get_page_speed() {
		
		$options = wpSEOde_Options::get( 'monitor_options' );

		
		if ( empty( $options['pagespeed_key'] ) ) {
			return 0; 
		}

		
		$response = wpSEOde_Base::get_file(
			add_query_arg(
				urlencode_deep(
					array(
						'url' => self::_get_bloginfo( 'url' ),
						'key' => $options['pagespeed_key']
					)
				),
				'https://www.googleapis.com/pagespeedonline/v1/runPagespeed?fields=score'
			)
		);

		
		if ( is_wp_error( $response ) ) {
			return 0; 
		}

		
		$json = json_decode( $response );

		

		return (int) @$json->score;
	}


	

	public static function the_twitter_follower() {
		echo number_format_i18n(
			wpSEOde_Transients::get(
				'twitter_count',
				array(
					__CLASS__,
					'get_twitter_follower'
				)
			)
		);
	}


	

	public static function get_twitter_follower() {
		
		$options = wpSEOde_Options::get( 'monitor_options' );

		
		if ( empty( $options['twitter_id'] ) ) {
			return 0; 
		}

		
		$response = wpSEOde_Base::get_file(
			sprintf(
				'https://api.twitter.com/1.1/users/show.json?screen_name=%s',
				$options['twitter_id']
			),
			'get',
			array(
				'blocking'    => true,
				'sslverify'   => false,
				'httpversion' => '1.1',
				'headers'     => array(
					'Authorization' => "Bearer AAAAAAAAAAAAAAAAAAAAAB9TSwAAAAAATW%2BrkBDDGKIrBy2iVIx3IeZXG4o%3Defd36iTzYSJrQuMTnCWRydsGadam2UqbMfweCTliQA"
				)
			)
		);

		
		if ( is_wp_error( $response ) ) {
			return 0; 
		}

		
		$json = json_decode( $response );

		

		return (int) @$json->followers_count;
	}

	

	public static function the_metricstools_sk() {
		echo number_format_i18n(
			wpSEOde_Transients::get(
				'metricstools_sk',
				array(
					__CLASS__,
					'get_metricstools_sk'
				)
			),
			4
		);
	}

	

	public static function the_seokicks_domainpop() {
		echo number_format_i18n(
			wpSEOde_Transients::get(
				'seokicks_domainpop',
				array(
					__CLASS__,
					'get_seokicks_domainpop'
				)
			)
		);
	}

	
	public static function the_seokicks_linkpop() {
		echo number_format_i18n(
			wpSEOde_Transients::get(
				'seokicks_linkpop',
				array(
					__CLASS__,
					'get_seokicks_linkpop' 				)
			)
		);
	}

	
	public static function get_seokicks_linkpop() {
		return wpSEOde_Transients::getDefault(
			'seokicks_linkpop',
			0
		);
	}

	

	public static function get_seokicks_domainpop() {
		
		$response = wpSEOde_Base::get_file(
			add_query_arg(
				urlencode_deep(
					array(
						'url' => self::_get_bloginfo( 'host' )
					)
				),
				'https://api.wpseo.de/gw/gw-seokicks.php'
			)
		);

		
		if ( is_wp_error( $response ) ) {
			return 0; 
		}

		
		$json = json_decode( $response );

		
		if ( ! is_numeric( $json->Overview->domainpop ) ) {
			return 0; 
		}

		wpSEOde_Transients::getDefault(
			'seokicks_linkpop',
			(int) $json->Overview->linkpop
		);

		

		return (int) @$json->Overview->domainpop;
	}

	

	public static function get_metricstools_sk() {

		
		$response = wpSEOde_Base::get_file(
			add_query_arg(
				urlencode_deep(
					array(
						'url' => self::_get_bloginfo( 'host' )
					)
				),
				'https://api.wpseo.de/gw/gw-metricstools.php'
			)
		);

		
		if ( is_wp_error( $response ) ) {
			return 0; 
		}

		
		$json = json_decode( $response );

		
		if ( $json === false || $json->result != 'success' || ! is_numeric( $json->values->sk ) ) {
			return 0; 
		}

		

		return (float) @$json->values->sk;
	}

}
<?php



defined( 'ABSPATH' ) or exit;



class wpSEOde_GUI {


	

	public static function init_menu() {
		
		if ( ( ! current_user_can( 'manage_options' ) ) or wpSEOde_Feedback::get( 'critical' ) ) {
			return;
		} elseif ( wpSEOde_License::expired() ) {

			
			define(
				'WPSEODE_MENU',
				add_menu_page(
					'wpSEO',
					'wpSEO',
					'manage_options',
					'wpseode',
					array(
						__CLASS__,
						'show_page'
					),
					WP_CONTENT_URL . '/plugins/wpseo/wpseo-icon.png',
					99
				)
			);

			
			add_action(
				'load-' . WPSEODE_MENU,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_MENU,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_MENU,
				array(
					__CLASS__,
					'add_js'
				)
			);


			define(
				'WPSEODE_LICENSING',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Licensing', 'wpseo' ),
					esc_html__( 'Licensing', 'wpseo' ),
					'manage_options',
					'wpseode_licensing',
					array(
						'wpSEOde_GUI',
						'show_page'
					) )
			);


			

			add_action(
				'load-' . WPSEODE_LICENSING,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_LICENSING,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_LICENSING,
				array(
					__CLASS__,
					'add_js'
				)
			);


		} else {
			
			define(
				'WPSEODE_MENU',
				add_menu_page(
					'wpSEO',
					'wpSEO',
					'manage_options',
					'wpseode',
					array(
						__CLASS__,
						'show_page'
					),
					WP_CONTENT_URL . '/plugins/wpseo/wpseo-icon.png',
					99
				)
			);

			
			add_action(
				'load-' . WPSEODE_MENU,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_MENU,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_MENU,
				array(
					__CLASS__,
					'add_js'
				)
			);


			add_submenu_page(
				'wpseode',
				'Monitor',
				'Monitor',
				'manage_options',
				'wpseode',
				array(
					'wpSEOde_GUI',
					'show_page'
				)
			);

			define(
				'WPSEODE_TITLE_TAGS',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Pagetitle', 'wpseo' ),
					esc_html__( 'Pagetitle', 'wpseo' ),
					'manage_options',
					'wpseode_title_tags',
					array(
						'wpSEOde_GUI',
						'show_page'
					) )
			);


			
			add_action(
				'load-' . WPSEODE_TITLE_TAGS,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_TITLE_TAGS,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_TITLE_TAGS,
				array(
					__CLASS__,
					'add_js'
				)
			);


			define(
				'WPSEODE_DESCRIPTION',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Description', 'wpseo' ),
					esc_html__( 'Description', 'wpseo' ),
					'manage_options',
					'wpseode_descriptions',
					array(
						'wpSEOde_GUI',
						'show_page'
					)
				)
			);

			
			add_action(
				'load-' . WPSEODE_DESCRIPTION,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_DESCRIPTION,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_DESCRIPTION,
				array(
					__CLASS__,
					'add_js'
				)
			);


			define(
				'WPSEODE_INDEXING',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Indexing', 'wpseo' ),
					esc_html__( 'Indexing', 'wpseo' ),
					'manage_options',
					'wpseode_indexing',
					array(
						'wpSEOde_GUI',
						'show_page'
					)
				)
			);


			
			add_action(
				'load-' . WPSEODE_INDEXING,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_INDEXING,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_INDEXING,
				array(
					__CLASS__,
					'add_js'
				)
			);


			define(
				'WPSEODE_SOCIAL',
				add_submenu_page(
					'wpseode',
					'Social',
					'Social',
					'manage_options',
					'wpseode_social',
					array(
						'wpSEOde_GUI',
						'show_page'
					)
				)
			);


			
			add_action(
				'load-' . WPSEODE_SOCIAL,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_SOCIAL,
				array(
					__CLASS__,
					'add_css'
				)
			);


			add_action(
				'admin_print_scripts-' . WPSEODE_SOCIAL,
				array(
					__CLASS__,
					'add_js'
				)
			);


			define(
				'WPSEODE_ADVANCED',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Advanced', 'wpseo' ),
					esc_html__( 'Advanced', 'wpseo' ),
					'manage_options',
					'wpseode_advanced',
					array(
						'wpSEOde_GUI',
						'show_page'
					)
				)
			);


			
			add_action(
				'load-' . WPSEODE_ADVANCED,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_ADVANCED,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_ADVANCED,
				array(
					__CLASS__,
					'add_js'
				)
			);


			define(
				'WPSEODE_SETTINGS',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Settings', 'wpseo' ),
					esc_html__( 'Settings', 'wpseo' ),
					'manage_options',
					'wpseode_settings',
					array(
						'wpSEOde_GUI',
						'show_page'
					) )
			);


			
			add_action(
				'load-' . WPSEODE_SETTINGS,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_SETTINGS,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_SETTINGS,
				array(
					__CLASS__,
					'add_js'
				)
			);

			define(
				'WPSEODE_LICENSING',
				add_submenu_page(
					'wpseode',
					esc_html__( 'Licensing', 'wpseo' ),
					esc_html__( 'Licensing', 'wpseo' ),
					'manage_options',
					'wpseode_licensing',
					array(
						'wpSEOde_GUI',
						'show_page'
					) )
			);


			

			add_action(
				'load-' . WPSEODE_LICENSING,
				array(
					__CLASS__,
					'add_page'
				)
			);

			
			add_action(
				'admin_print_styles-' . WPSEODE_LICENSING,
				array(
					__CLASS__,
					'add_css'
				)
			);

			add_action(
				'admin_print_scripts-' . WPSEODE_LICENSING,
				array(
					__CLASS__,
					'add_js'
				)
			);
		}
	}


	

	public static function add_css() {
		wp_enqueue_style(
			'wpseo',
			wpSEOde::plugin_url( 'css/style.min.css' ),
			false,
			wpSEOde::get_plugin_data( 'Version' )
		);
	}


	

	public static function add_js() {
		wp_enqueue_script(
			'wpseo',
			wpSEOde::plugin_url( 'js/script.min.js' ),
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-tooltip' ),
			wpSEOde::get_plugin_data( 'Version' )
		);

	}


	

	public static function save_changes() {
		
		if ( empty( $_POST ) or empty( $_POST['_wpseo_action'] ) or ! in_array( $_POST['_wpseo_action'], array(
				'reset',
				'import',
				'import_yoast',
				'export',
				'update',
				'verify',
				'unverify'
			) ) ) {
			return;
		}

		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		
		check_admin_referer( '_wpseo__settings_nonce' );


		$redirect_array = array(
			'updated' => 'true'
		);

		
		switch ( (string) $_POST['_wpseo_action'] ) {
			case 'reset':
				wpSEOde_Options::reset();
				break;


			case 'import_yoast':
				$wpseo_tools = new wpSEOde_Tools( $_POST['_wpseo_action'] );

				if ( has_action( 'cachify_flush_cache' ) ) {
					do_action( 'cachify_flush_cache' );
				}

				
				wpSEOde::redirect_referer(
					array(
						'updated' => 'true',
						'result'  => $wpseo_tools->get_result()
					)
				);
				exit();
				break;

			case 'import':
			case 'export':
				new wpSEOde_Tools( $_POST['_wpseo_action'] );
				break;

			case 'update':
				
				$_POST = array_map( 'stripslashes_deep', $_POST );

				if ( $_POST['_wpseo_page'] == 'title_tags' ) {
					wpSEOde_Options::update(
						array(


							
							'title_enable' => (int) ( ! empty( $_POST['title_enable'] ) ),

							'title_channel_home'       => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_home'] ) ),
							'title_channel_single'     => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_single'] ) ),
							'title_channel_page'       => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_page'] ) ),
							'title_channel_posttype'   => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_posttype'] ) ),
							'title_channel_category'   => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_category'] ) ),
							'title_channel_search'     => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_search'] ) ),
							'title_channel_archive'    => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_archive'] ) ),
							'title_channel_tagging'    => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_tagging'] ) ),
							'title_channel_author'     => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_author'] ) ),
							'title_channel_tax'        => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_tax'] ) ),
							'title_channel_attachment' => (array) explode( ',', sanitize_text_field( @$_POST['title_channel_attachment'] ) ),

							'title_desc_home'       => sanitize_text_field( @$_POST['title_desc_home'] ),
							'title_desc_home_home'  => sanitize_text_field( @$_POST['title_desc_home_home'] ),
							'title_desc_single'     => sanitize_text_field( @$_POST['title_desc_single'] ),
							'title_desc_page'       => sanitize_text_field( @$_POST['title_desc_page'] ),
							'title_desc_posttype'   => sanitize_text_field( @$_POST['title_desc_posttype'] ),
							'title_desc_category'   => sanitize_text_field( @$_POST['title_desc_category'] ),
							'title_desc_search'     => sanitize_text_field( @$_POST['title_desc_search'] ),
							'title_desc_archive'    => sanitize_text_field( @$_POST['title_desc_archive'] ),
							'title_desc_tagging'    => sanitize_text_field( @$_POST['title_desc_tagging'] ),
							'title_desc_author'     => sanitize_text_field( @$_POST['title_desc_author'] ),
							'title_desc_tax'        => sanitize_text_field( @$_POST['title_desc_tax'] ),
							'title_desc_attachment' => sanitize_text_field( @$_POST['title_desc_attachment'] ),

							'title_separator' => sanitize_text_field( $_POST['title_separator'] ),
							'title_cleanup'   => (int) ( ! empty( $_POST['title_cleanup'] ) ),
							'title_case'      => (int) @$_POST['title_case'],


							'title_manually'      => (int) ( ! empty( $_POST['title_manually'] ) ),
							'title_suggest'       => (int) ( ! empty( $_POST['title_suggest'] ) ),
							'title_manually_only' => (int) ( ! empty( $_POST['title_manually_only'] ) ),


														'tax_title_manually'  => (int) ( ! empty( $_POST['tax_title_manually'] ) ),


							
							'post_title_suggest'  => (int) ( ! empty( $_POST['post_title_suggest'] ) ),
						)
					);

					$aPostTypes = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
					$aUpdate    = array();
					foreach ( $aPostTypes as $sName => $oPostType ) {
						$aUpdate[ 'title_channel_posttype_' . $sName ] = (array) explode( ',', sanitize_text_field( @$_POST[ 'title_channel_posttype_' . $sName ] ) );
						$aUpdate[ 'title_desc_posttype_' . $sName ]    = sanitize_text_field( @$_POST[ 'title_desc_posttype_' . $sName ] );
					}
					wpSEOde_Options::update( $aUpdate );
					unset( $aUpdate );
				} elseif ( $_POST['_wpseo_page'] == 'descriptions' ) {
					wpSEOde_Options::update(
						array(


							
							'desc_enable' => (int) ( ! empty( $_POST['desc_enable'] ) ),

							'desc_home'       => (int) @$_POST['desc_home'],
							'desc_single'     => (int) @$_POST['desc_single'],
							'desc_page'       => (int) @$_POST['desc_page'],
							'desc_posttype'   => (int) @$_POST['desc_posttype'],
							'desc_category'   => (int) @$_POST['desc_category'],
							'desc_search'     => (int) @$_POST['desc_search'],
							'desc_archive'    => (int) @$_POST['desc_archive'],
							'desc_tagging'    => (int) @$_POST['desc_tagging'],
							'desc_author'     => (int) @$_POST['desc_author'],
							'desc_tax'        => (int) @$_POST['desc_tax'],
							'desc_attachment' => (int) @$_POST['desc_attachment'],
							'desc_cleanup'    => (int) ( ! empty( $_POST['desc_cleanup'] ) ),

							'desc_counter'      => (int) @$_POST['desc_counter'],
							'desc_tender'       => (int) ( ! empty( $_POST['desc_tender'] ) ),
							'desc_default'      => sanitize_text_field( $_POST['desc_default'] ),
							'desc_default_home' => sanitize_text_field( @$_POST['desc_default_home'] ),

							
							'desc_manually'     => (int) ( ! empty( $_POST['desc_manually'] ) ),
							'desc_suggest'      => (int) ( ! empty( $_POST['desc_suggest'] ) ),

														'tax_manually'      => (int) ( ! empty( $_POST['tax_manually'] ) ),
							'tax_manually_prio' => (int) ( ! empty( $_POST['tax_manually_prio'] ) ),
						)
					);

					$aPostTypes = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
					$aUpdate    = array();
					foreach ( $aPostTypes as $sName => $oPostType ) {
						$aUpdate[ 'desc_posttype_' . $sName ] = (array) explode( ',', sanitize_text_field( @$_POST[ 'desc_posttype_' . $sName ] ) );
						$aUpdate[ 'desc_posttype_' . $sName ] = sanitize_text_field( @$_POST[ 'desc_posttype_' . $sName ] );
					}
					wpSEOde_Options::update( $aUpdate );
					unset( $aUpdate );
				} elseif ( $_POST['_wpseo_page'] == 'indexing' ) {
					wpSEOde_Options::update(
						array(


							
							'noindex_enable'      => (int) ( ! empty( $_POST['noindex_enable'] ) ),
							'noindex_canonical'   => (int) ( ! empty( $_POST['noindex_canonical'] ) ),
							'noindex_age'         => (int) ( ! empty( $_POST['noindex_age'] ) ),
							'noindex_http'        => (int) ( ! empty( $_POST['noindex_http'] ) ),
							'noindex_hidden'      => (int) ( ! empty( $_POST['noindex_hidden'] ) ),
							'noindex_nocanonical' => (int) ( ! empty( $_POST['noindex_nocanonical'] ) ),

							
							'noindex_home'        => (int) @$_POST['noindex_home'],
							'noindex_single'      => (int) @$_POST['noindex_single'],
							'noindex_page'        => (int) @$_POST['noindex_page'],
							'noindex_posttype'    => (int) @$_POST['noindex_posttype'],
							'noindex_category'    => (int) @$_POST['noindex_category'],
							'noindex_search'      => (int) @$_POST['noindex_search'],
							'noindex_archive'     => (int) @$_POST['noindex_archive'],
							'noindex_tagging'     => (int) @$_POST['noindex_tagging'],
							'noindex_author'      => (int) @$_POST['noindex_author'],
							'noindex_tax'         => (int) @$_POST['noindex_tax'],
							'noindex_attachment'  => (int) @$_POST['noindex_attachment'],
							'misc_noodp'          => (int) ( ! empty( $_POST['misc_noodp'] ) ),
							'misc_noarchive'      => (int) ( ! empty( $_POST['misc_noarchive'] ) ),

							'noindex_manually' => (int) ( ! empty( $_POST['noindex_manually'] ) ),

							'sitemap'          => (int) ( ! empty( $_POST['sitemap'] ) ),
							'sitemap_manually' => (int) ( ! empty( $_POST['sitemap_manually'] ) ),

							'canonical_manually' => (int) ( ! empty( $_POST['canonical_manually'] ) ),

							'redirect_manually'   => (int) ( ! empty( $_POST['redirect_manually'] ) ),
							'redirect_attachment' => (int) ( ! empty( $_POST['redirect_attachment'] ) ),


														'tax_robots_manually' => (int) ( ! empty( $_POST['tax_robots_manually'] ) ),
						)
					);

					$aPostTypes = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
					$aUpdate    = array();
					foreach ( $aPostTypes as $sName => $oPostType ) {
						$aUpdate[ 'noindex_posttype_' . $sName ] = (int) @$_POST[ 'noindex_posttype_' . $sName ];
					}
					wpSEOde_Options::update( $aUpdate );
					unset( $aUpdate );
					
					wpSEOde_Rewrite::flush_schedule();
				} elseif ( $_POST['_wpseo_page'] == 'social' ) {
					wpSEOde_Options::update(
						array(
							
							'open_graph'                      => (int) ( ! empty( $_POST['open_graph'] ) ),
							'open_graph_manually'             => (int) ( ! empty( $_POST['open_graph_manually'] ) ),
							'open_graph_date_disable'         => (int) ( ! empty( $_POST['open_graph_date_disable'] ) ),
							'open_graph_title_manually'       => (int) ( ! empty( $_POST['open_graph_title_manually'] ) ),
							'open_graph_description_manually' => (int) ( ! empty( $_POST['open_graph_description_manually'] ) ),
							'open_graph_image_manually'       => (int) ( ! empty( $_POST['open_graph_image_manually'] ) ),
							'opengraph_start_title'           => sanitize_text_field( @$_POST['opengraph_start_title'] ),
							'opengraph_start_description'     => sanitize_text_field( @$_POST['opengraph_start_description'] ),
							'opengraph_start_image'           => sanitize_text_field( @$_POST['opengraph_start_image'] ),
							'opengraph_post_title'            => sanitize_text_field( @$_POST['opengraph_post_title'] ),
							'opengraph_post_description'      => sanitize_text_field( @$_POST['opengraph_post_description'] ),
							'opengraph_post_image'            => sanitize_text_field( @$_POST['opengraph_post_image'] ),
							'opengraph_page_title'            => sanitize_text_field( @$_POST['opengraph_page_title'] ),
							'opengraph_page_description'      => sanitize_text_field( @$_POST['opengraph_page_description'] ),
							'opengraph_page_image'            => sanitize_text_field( @$_POST['opengraph_page_image'] ),


							
							'twitter_site_account'            => (string) preg_replace( '/[^0-9a-zA-Z_\-@]/u', '', sanitize_text_field( $_POST['twitter_site_account'] ) ),
							'twitter_cards_manually'          => (int) ( ! empty( $_POST['twitter_cards_manually'] ) ),
							'twitter_authorship'              => (int) ( ! empty( $_POST['twitter_authorship'] ) ),
							'twitter_authorship_manually'     => (int) ( ! empty( $_POST['twitter_authorship_manually'] ) ),
							'twittercard_start_title'         => sanitize_text_field( @$_POST['twittercard_start_title'] ),
							'twittercard_start_description'   => sanitize_text_field( @$_POST['twittercard_start_description'] ),
							'twittercard_start_image'         => sanitize_text_field( @$_POST['twittercard_start_image'] ),

							
							'pinterest_domain_verify_tag'     => sanitize_text_field( $_POST['pinterest_domain_verify_tag'] ),

							
							'social_profiles'                 => (int) ( ! empty( $_POST['social_profiles'] ) ),
							'social_data_name'                => sanitize_text_field( @$_POST['social_data_name'] ),
							'social_data_type'                => sanitize_text_field( @$_POST['social_data_type'] ),
							'social_profile_youtube'          => sanitize_text_field( @$_POST['social_profile_youtube'] ),
							'social_profile_facebook'         => sanitize_text_field( @$_POST['social_profile_facebook'] ),
							'social_profile_twitter'          => sanitize_text_field( @$_POST['social_profile_twitter'] ),
							'social_profile_linkedin'         => sanitize_text_field( @$_POST['social_profile_linkedin'] ),
							'social_profile_instagram'        => sanitize_text_field( @$_POST['social_profile_instagram'] ),
							'social_profile_pinterest'        => sanitize_text_field( @$_POST['social_profile_pinterest'] ),
						)
					);
				} elseif ( $_POST['_wpseo_page'] == 'advanced' ) {
					wpSEOde_Options::update(
						array(

							

							'strip_categorybase'        => (int) ( ! empty( $_POST['strip_categorybase'] ) ),
							'redirect_old_categorybase' => (int) ( ! empty( $_POST['redirect_old_categorybase'] ) ),

							'ignore_manually' => (int) ( ! empty( $_POST['ignore_manually'] ) ),


							'misc_nextpage'         => (int) ( ! empty( $_POST['misc_nextpage'] ) ),
							'misc_nextpage_rewrite' => (int) ( ! empty( $_POST['misc_nextpage_rewrite'] ) ),

							'misc_slug'     => (int) ( ! empty( $_POST['misc_slug'] ) ),
							'misc_slug_max' => (int) @$_POST['misc_slug_max'],

							'misc_wplink' => (int) ( ! empty( $_POST['misc_wplink'] ) ),

							'paged_archive' => (int) ( ! empty( $_POST['paged_archive'] ) ),

							'key_manually' => (int) ( ! empty( $_POST['key_manually'] ) ),
							'key_suggest'  => (int) ( ! empty( $_POST['key_suggest'] ) ),
							'key_news'     => (int) ( ! empty( $_POST['key_news'] ) ),

							'misc_order'    => (array) explode( ',', preg_replace( '/[^title|desc|keys|,]/', '', $_POST['misc_order'] ) ),

							
							'speed_nocheck' => (int) @$_POST['speed_nocheck'],

							
							'snippets_data' => (array) self::_build_snippets( @$_POST['snippets'] )
						)
					);
					
					wpSEOde_Rewrite::flush_schedule();
				} elseif ( $_POST['_wpseo_page'] == 'settings' ) {
					wpSEOde_Options::update(
						array(

							

							'misc_lang' => (string) preg_replace( '/[^denus_]/i', '', $_POST['misc_lang'] ),
						)
					);
				} elseif ( $_POST['_wpseo_page'] == 'default' ) {

					wpSEOde_Options::update(
						array(
							
							'misc_monitor'       => (int) ( ! empty( $_POST['misc_monitor'] ) ),
							'misc_monitor_theme' => (int) ( ! empty( $_POST['misc_monitor_theme'] ) ),
						)
					);

					
					if ( ! empty( $_POST['monitor_options'] ) ) {
						

						
						$options = wpSEOde_Options::get( 'monitor_options' );

						
						$incoming = (array) $_POST['monitor_options'];

						if ( ! isset( $incoming['twitter_id'] ) ) {
							$incoming['twitter_id'] = '';
						}
						if ( ! isset( $incoming['seitwert_key'] ) ) {
							$incoming['seitwert_key'] = '';
						}
						
						if ( ! isset( $incoming['facebook_app_id'] ) ) {
							$incoming['facebook_app_id'] = '';
						}
						
						if ( ! isset( $incoming['pagespeed_key'] ) ) {
							$incoming['pagespeed_key'] = '';
						}

						
						$outgoing = array(
							'twitter_id'      => (string) preg_replace( '/[^a-z0-9_@]/i', '', sanitize_text_field( $incoming['twitter_id'] ) ),
							'seitwert_key'    => (string) preg_replace( '/[^a-z0-9]/i', '', sanitize_text_field( $incoming['seitwert_key'] ) ),
							
							'facebook_app_id' => (string) preg_replace( '/[^a-z0-9\-_\.]/i', '', sanitize_text_field( $incoming['facebook_app_id'] ) ),
							
							'pagespeed_key'   => (string) preg_replace( '/[^a-z0-9-_]/i', '', sanitize_text_field( $incoming['pagespeed_key'] ) ),
						);

						if ( ! isset( $incoming['seokicks'] ) ) {
							$incoming['seokicks'] = 0;
						}
						if ( ! isset( $incoming['metricstools'] ) ) {
							$incoming['metricstools'] = 0;
						}
						if ( ! isset( $incoming['xovi'] ) ) {
							$incoming['xovi'] = 0;
						}
						
						if ( ! isset( $incoming['twitter'] ) ) {
							$incoming['twitter'] = 0;
						}
						if ( ! isset( $incoming['seitwert'] ) ) {
							$incoming['seitwert'] = 0;
						}
						
						if ( ! isset( $incoming['pagespeed'] ) ) {
							$incoming['pagespeed'] = 0;
						}

						
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

						
						if ( ! isset( $options['seitwert_key'] ) || $options['seitwert_key'] != $outgoing['seitwert_key'] ) {
							$transients[] = 'seitwert_count';
						}
						if ( ! isset( $options['pagespeed_key'] ) || $options['pagespeed_key'] != $outgoing['pagespeed_key'] ) {
							$transients[] = 'pagespeed_score';
						}
						
						if ( ! isset( $options['twitter_id'] ) || $options['twitter_id'] != $outgoing['twitter_id'] ) {
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
				}
				break;


			case 'verify':
				if ( $_POST['_wpseo_page'] == 'licensing' ) {
					if ( wpSEOde_License::verify() ) {
						$redirect_array['result'] = 'success';
					} else {
						$redirect_array['result'] = 'error';
					}
				}
				break;


			case 'unverify':
				if ( $_POST['_wpseo_page'] == 'licensing' ) {
					wpSEOde_License::remove( true );
					$redirect_array['result'] = 'success2';
				}
				break;


			default:
				break;
		}

		
		if ( has_action( 'cachify_flush_cache' ) ) {
			do_action( 'cachify_flush_cache' );
		}

		
		wpSEOde::redirect_referer(
			$redirect_array
		);
	}


	

	private static function _build_snippets( $post_snippets ) {
		
		$raw_snippets = array();

		
		if ( empty( $post_snippets ) or count( $post_snippets ) <= 2 ) {
			return array();
		}

		
		if (
			( defined( 'WPSEO_DISABLE_SNIPPETS' ) && WPSEO_DISABLE_SNIPPETS ) || 			( defined( 'WPSEODE_DISABLE_SNIPPETS' ) && WPSEODE_DISABLE_SNIPPETS )
		) {
			return array();
		}

		
		for ( $a = 2; $a < count( $post_snippets ); $a = $a + 2 ) {
			array_push(
				$raw_snippets,
				array(
					'code' => $post_snippets[ $a ]['code'],
					'name' => sanitize_text_field( $post_snippets[ $a + 1 ]['name'] )
				)
			);
		}

		return $raw_snippets;
	}

	
	public static function _add_wpseo_to_array( $aArray ) {
		array_push( $aArray, 'wpseo' );

		return $aArray;
	}

	
	public static function add_page() {
		
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );

		
		$attr = array(
			'custom'      => 'Custom fields',
			'title'       => 'Pagetitle',
			'desc'        => 'Description',
			'noindex'     => 'Duplicate content',
			'performance' => 'Performance',
			'misc'        => 'Advanced',
			'tools'       => 'Tools',
			'snippets'    => 'Snippets'
		);

		
		if (
			( defined( 'WPSEO_DISABLE_SNIPPETS' ) && WPSEO_DISABLE_SNIPPETS ) || 			( defined( 'WPSEODE_DISABLE_SNIPPETS' ) && WPSEODE_DISABLE_SNIPPETS )
		) {
			unset( $attr['snippets'] );
		}

		if ( wpSEOde_License::expired() ) {

			add_meta_box(
				'metabox_licensing',
				esc_html__( 'Licensing', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_licensing'
				),
				WPSEODE_MENU,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_MENU . '_metabox_licensing', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );


			add_meta_box(
				'metabox_licensing',
				esc_html__( 'Licensing', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_licensing'
				),
				WPSEODE_LICENSING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_MENU . '_metabox_licensing', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );
		} else {


			add_meta_box(
				'metabox_settings',
				esc_html__( 'Monitor', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_settings'
				),
				WPSEODE_MENU,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_MENU . '_metabox_settings', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_monitor',
				esc_html__( 'Monitor Data', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_monitor'
				),
				WPSEODE_MENU,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_MENU . '_metabox_monitor', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_title',
				esc_html__( 'Pagetitle', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_title'
				),
				WPSEODE_TITLE_TAGS,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_TITLE_TAGS . '_metabox_title', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_desc',
				esc_html__( 'Description', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_desc'
				),
				WPSEODE_DESCRIPTION,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_DESCRIPTION . '_metabox_desc', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_noindex',
				esc_html__( 'Index Control', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_noindex'
				),
				WPSEODE_INDEXING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_INDEXING . '_metabox_noindex', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_sitemap',
				esc_html__( 'Sitemap', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_sitemap'
				),
				WPSEODE_INDEXING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_INDEXING . '_metabox_sitemap', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_canonicals',
				esc_html__( 'Canonicals', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_canonicals'
				),
				WPSEODE_INDEXING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_INDEXING . '_metabox_canonicals', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_redirects',
				esc_html__( 'Redirects', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_redirects'
				),
				WPSEODE_INDEXING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_INDEXING . '_metabox_redirects', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_robots',
				esc_html__( 'Robots', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_robots'
				),
				WPSEODE_INDEXING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_INDEXING . '_metabox_robots', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_opengraph',
				esc_html__( 'Open Graph (Facebook & Google+ Snippets)', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_opengraph'
				),
				WPSEODE_SOCIAL,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SOCIAL . '_metabox_opengraph', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_twittercards',
				esc_html__( 'Twitter', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_twittercards'
				),
				WPSEODE_SOCIAL,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SOCIAL . '_metabox_twittercards', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_googleplus',
				esc_html__( 'Google Plus', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_googleplus'
				),
				WPSEODE_SOCIAL,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SOCIAL . '_metabox_googleplus', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_pinterest',
				esc_html__( 'Pinterest', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_pinterest'
				),
				WPSEODE_SOCIAL,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SOCIAL . '_metabox_pinterest', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_socialprofiles',
				esc_html__( 'Company/Person', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_socialprofiles'
				),
				WPSEODE_SOCIAL,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SOCIAL . '_metabox_socialprofiles', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_misc',
				esc_html__( 'Advanced', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_misc'
				),
				WPSEODE_ADVANCED,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_ADVANCED . '_metabox_misc', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_performance',
				esc_html__( 'Performance', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_performance'
				),
				WPSEODE_ADVANCED,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_ADVANCED . '_metabox_performance', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_snippets',
				esc_html__( 'Snippets', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_snippets'
				),
				WPSEODE_ADVANCED,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_ADVANCED . '_metabox_snippets', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_language',
				esc_html__( 'Language', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_language'
				),
				WPSEODE_SETTINGS,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SETTINGS . '_metabox_language', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_tools',
				esc_html__( 'Tools', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_tools'
				),
				WPSEODE_SETTINGS,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SETTINGS . '_metabox_tools', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_import',
				esc_html__( 'Import', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_import'
				),
				WPSEODE_SETTINGS,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_SETTINGS . '_metabox_import', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			add_meta_box(
				'metabox_licensing',
				esc_html__( 'Licensing', 'wpseo' ),
				array(
					__CLASS__,
					'metabox_licensing'
				),
				WPSEODE_LICENSING,
				'side',
				'core'
			);
			add_filter( 'postbox_classes_' . WPSEODE_LICENSING . '_metabox_licensing', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );

			unset( $attr['custom'] );
			unset( $attr['title'] );
			unset( $attr['desc'] );
			unset( $attr['noindex'] );
			unset( $attr['misc'] );
			unset( $attr['tools'] );
			unset( $attr['performance'] );
			unset( $attr['snippets'] );

			
			foreach ( $attr as $k => $v ) {
				add_meta_box(
					'metabox_' . $k,
					esc_html__( $v, 'wpseo' ),
					array(
						__CLASS__,
						'metabox_' . $k
					),
					WPSEODE_MENU,
					'side',
					'core'
				);
				add_filter( 'postbox_classes_' . WPSEODE_MENU . '_metabox_' . $k, array(
					__CLASS__,
					'_add_wpseo_to_array'
				) );
			}

			
			

			
			$screen = get_current_screen();

			
			$screen->add_help_tab(
				array(
					'id'      => 'wpseode-helpdesk',
					'title'   => esc_html__( 'wpSEO Helpdesk', 'wpseo' ),
					'content' => sprintf(
						'<p>%s</p><p>%s</p>',
						esc_html__( 'wpSEO contains more than 100 options. Most options are directly linked to the corresponding point in the online manual, where the specific option is described in detail. Below the link to the wpSEO Helpdesk with descriptions of all the available functions of wpSEO.', 'wpseo' ),
						'<a href="http://helpdesk.wpseo.de" target="_blank">' . esc_html__( 'wpSEO Helpdesk', 'wpseo' ) . '</a>'
					)
				)
			);
			$screen->set_help_sidebar(
				'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
				'<p><a href="https://plus.google.com/b/106964298865639944324/" target="_blank">Google+</a></p>' .
				'<p><a href="https://twitter.com/wpSEO" target="_blank">Twitter</a></p>'
			);
		}
	}

	

	public static function show_page( $page = false ) {
		if ( ! $_GET['page'] ) {

			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="default"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_MENU, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_MENU, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_MENU, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php

		} elseif ( $_GET['page'] == 'wpseode' ) {

			if ( wpSEOde_License::expired() ) {
				?>
                <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO
                </h2>
                <div id="poststuff" class="metabox-holder">

                    <div class="postbox " id="metabox_licensing">
                        <h2><span><?php esc_html_e( 'The trial period of wpSEO has expired.', 'wpseo' ); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <td>
										<?php echo sprintf( __( 'To activate your license select the "Licensing" tab in the wpSEO menu or click <a href="%s">here</a>. If you don\'t have a license yet please visit <a href="https://wpseo.de/" target="_blank">https://wpseo.de</a> to buy a license.', 'wpseo' ), admin_url( 'admin.php?page=wpseode_licensing' ) ); ?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br/>
                        </div>
                    </div>
                </div>
				<?php
			} else {
				?>
                <div id="wpseo_changes_form" class="wrap">
                    <h2>
                        wpSEO &raquo; Monitor
                    </h2>

                    <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
						<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
						<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
						<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                        <input type="hidden" name="action" value="save_wpseo_changes"/>
                        <input type="hidden" name="_wpseo_action" value="update"/>
                        <input type="hidden" name="_wpseo_page" value="default"/>

                        <div id="poststuff"
                             class="metabox-holder <?php  ?>">
                            <div id="side-info-column" class="postbox-container">
								<?php do_meta_boxes( WPSEODE_MENU, 'side', array() ) ?>
                            </div>
                            <div id="post-body" class="postbox-container">
                                <div id="post-body-content" class="has-sidebar-content">
									<?php do_meta_boxes( WPSEODE_MENU, 'additional', array() ) ?>
									<?php do_meta_boxes( WPSEODE_MENU, 'normal', array() ) ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
				<?php
			}

		} elseif ( $_GET['page'] == 'wpseode_title_tags' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; <?php esc_html_e( 'Pagetitle', 'wpseo' ); ?>
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="title_tags"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_TITLE_TAGS, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_TITLE_TAGS, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_TITLE_TAGS, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		} elseif ( $_GET['page'] == 'wpseode_descriptions' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; <?php esc_html_e( 'Description', 'wpseo' ); ?>
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="descriptions"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_DESCRIPTION, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_DESCRIPTION, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_DESCRIPTION, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		} elseif ( $_GET['page'] == 'wpseode_indexing' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; <?php esc_html_e( 'Indexing', 'wpseo' ); ?>
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="indexing"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_INDEXING, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_INDEXING, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_INDEXING, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		} elseif ( $_GET['page'] == 'wpseode_social' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; Social
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="social"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_SOCIAL, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_SOCIAL, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_SOCIAL, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		} elseif ( $_GET['page'] == 'wpseode_advanced' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; <?php esc_html_e( 'Advanced', 'wpseo' ); ?>
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="advanced"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_ADVANCED, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_ADVANCED, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_ADVANCED, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		} elseif ( $_GET['page'] == 'wpseode_settings' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; <?php esc_html_e( 'Settings', 'wpseo' ); ?>
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" value="update"/>
                    <input type="hidden" name="_wpseo_page" value="settings"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_SETTINGS, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_SETTINGS, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_SETTINGS, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		} elseif ( $_GET['page'] == 'wpseode_licensing' ) {
			?>
            <div id="wpseo_changes_form" class="wrap">
                <h2>
                    wpSEO &raquo; <?php esc_html_e( 'Licensing', 'wpseo' ); ?>
                </h2>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post">
					<?php wp_nonce_field( '_wpseo__settings_nonce' ) ?>
					<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ) ?>
					<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ) ?>
                    <input type="hidden" name="action" value="save_wpseo_changes"/>
                    <input type="hidden" name="_wpseo_action" id="_wpseo_action" value="verify"/>
                    <input type="hidden" name="_wpseo_page" value="licensing"/>

                    <div id="poststuff"
                         class="metabox-holder <?php  ?>">
                        <div id="side-info-column" class="postbox-container">
							<?php do_meta_boxes( WPSEODE_LICENSING, 'side', array() ) ?>
                        </div>
                        <div id="post-body" class="postbox-container">
                            <div id="post-body-content" class="has-sidebar-content">
								<?php do_meta_boxes( WPSEODE_LICENSING, 'additional', array() ) ?>
								<?php do_meta_boxes( WPSEODE_LICENSING, 'normal', array() ) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
			<?php
		}
	}

	

	public static function metabox_custom() {
		
		$options = wpSEOde_Options::get(); ?>

        <table class="form-table">

        </table>

		<?php submit_button() ?>
	<?php }

	

	public static function metabox_title() {
		
		$options = wpSEOde_Options::get(); ?>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="title_enable" id="title_enable"
                           value="1" <?php checked( $options['title_enable'], 1 ) ?> />
                </th>
                <td>
                    <label for="title_enable">
						<?php esc_html_e( 'Activate generation of the page title', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Output of the title in blog pages', 'wpseo' ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <label>
									<?php esc_html_e( 'Sort of placeholder via Drag&amp;Drop', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Determination of the components and the arrangement in the title', 'wpseo' ) ?><?php wpSEOde::help_icon( 4827 ) ?>
                                </small>
                            </td>
                        </tr>

                        <tr>
                            <td>
								<?php foreach ( wpSEOde_Vars::get( 'group_items' ) as $item => $name ) {
									
									$option  = 'title_channel_' . $item;
									$default = array_keys( wpSEOde_Vars::get( 'meta_title', $item ) );
									if ( ( ! is_array( $default ) || count( $default ) == 0 ) && substr( $item, 0, 9 ) == 'posttype_' ) {
										$default = array_keys( wpSEOde_Vars::get( 'meta_title', 'posttype' ) );
									}

									
									$current = ( isset( $options[ $option ] ) ? (array) $options[ $option ] : null );
									if ( ( ! is_array( $current ) || count( $current ) == 0 ) && substr( $item, 0, 9 ) == 'posttype_' ) {
										$current = (array) $options['title_channel_posttype'];
									}
									$available = array_diff( $default, $current );

									
									$dict = wpSEOde_Vars::get( 'meta_title', $item ); ?>

                                    <fieldset>
                                        <legend>
											<?php esc_html_e( $name, 'wpseo' ) ?>
                                        </legend>

                                        <div class="sortable connect">
                                            <input type="hidden" name="title_channel_<?php echo esc_attr( $item ) ?>"
                                                   id="title_channel_<?php echo esc_attr( $item ) ?>"
                                                   value="<?php echo esc_attr( implode( ',', $current ) ) ?>"/>

                                            <div>
                                                <p>
													<?php esc_html_e( 'active', 'wpseo' ) ?>
                                                </p>
                                                <ul class="x_axis"><?php self::_the_list( $current, $option, $dict ); ?></ul>
                                            </div>

                                            <div>
                                                <p>
													<?php esc_html_e( 'inactive', 'wpseo' ) ?>
                                                </p>
                                                <ul class="y_axis"><?php self::_the_list( $available, $option, $dict ); ?></ul>
                                            </div>
                                        </div>
                                    </fieldset>
								<?php } ?>
                            </td>
                        </tr>
                    </table>

                    <table class="level2">
                        <tr>
                            <td>
                                <label>
									<?php esc_html_e( 'Text field as a placeholder', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Assign a freely defined text for each section', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>
                    <table class="level2 list">
						<?php if ( get_option( 'page_for_posts' ) ) { ?>
                            <tr>
                                <td>
                                    <label for="title_desc_home_home">
										<?php esc_html_e( 'Blog startpage', 'wpseo' ) ?>
                                    </label>
                                </td>
                                <td>
                                    <input type="text" name="title_desc_home_home" id="title_desc_home_home"
                                           value="<?php echo esc_attr( $options['title_desc_home_home'] ) ?>"/>
                                </td>
                            </tr>
						<?php } ?>

						<?php foreach ( wpSEOde_Vars::get( 'group_items' ) as $item => $name ) { ?>
                            <tr>
                                <td>
                                    <label for="title_desc_<?php echo esc_attr( $item ) ?>">
										<?php esc_html_e( $name, 'wpseo' ) ?>
                                    </label>
                                </td>
                                <td>
                                    <input type="text" name="title_desc_<?php echo esc_attr( $item ) ?>"
                                           id="title_desc_<?php echo esc_attr( $item ) ?>"
                                           value="<?php echo( isset( $options[ 'title_desc_' . $item ] ) ? esc_attr( $options[ 'title_desc_' . $item ] ) : '' ); ?>"/>
                                </td>
                            </tr>
						<?php } ?>
                    </table>


                    <table class="level2">
                        <tr>
                            <td>
                                <label>
									<?php esc_html_e( 'Separator for placeholder', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Sign as a visual separator between individual placeholders', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                                </small>

                                <input type="text" name="title_separator" id="title_separator"
                                       value="<?php echo esc_attr( $options['title_separator'] ) ?>"/>
                            </td>
                        </tr>
                    </table>

                    <table class="level2">
                        <tr>
                            <td>
                                <label>
									<?php esc_html_e( 'Reformatting the spelling', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Conversion of Title in upper or lower case', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                                </small>

                                <select name="title_case" id="title_case">
                                    <option value=""><?php esc_html_e( 'No', 'wpseo' ) ?></option>
									<?php foreach ( wpSEOde_Vars::get( 'title_format', $item ) as $k => $v ) { ?>
                                        <option value="<?php echo esc_attr( $k ) ?>" <?php selected( $options['title_case'], $k ) ?>><?php esc_html_e( $v, 'wpseo' ) ?></option>
									<?php } ?>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="title_cleanup" id="title_cleanup"
                                       value="1" <?php checked( $options['title_cleanup'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="title_cleanup">
									<?php esc_html_e( 'Cleanup of redundant code', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Removal of HTML and other disturbing fragments', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr>
                <th>
                    <input type="checkbox" name="title_manually" id="title_manually"
                           value="1" <?php checked( $options['title_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="title_manually">
						<?php esc_html_e( 'Display field for manual input of title', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign a customized page title per each article or page', 'wpseo' ) ?><?php wpSEOde::help_icon( 133 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="title_suggest" id="title_suggest"
                                       value="1" <?php checked( $options['title_suggest'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="title_suggest">
									<?php esc_html_e( 'Activate Google Suggest to add suggestions to your entries', 'wpseo' ) ?><?php wpSEOde::help_icon( 80 ) ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input type="checkbox" name="title_manually_only" id="title_manually_only"
                                       value="1" <?php checked( $options['title_manually_only'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="title_manually_only">
									<?php esc_html_e( 'Output of manual page titles without complete', 'wpseo' ) ?><?php wpSEOde::help_icon( 171 ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="checkbox" name="tax_title_manually" id="tax_title_manually"
                           value="1" <?php checked( $options['tax_title_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="tax_manually">
						<?php esc_html_e( 'Display field for taxonomy titles ', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign a customized title for post tags, categories and taxonomies', 'wpseo' ) ?><?php wpSEOde::help_icon( 137 ) ?>
                    </small>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="checkbox" name="post_title_suggest" id="post_title_suggest"
                           value="1" <?php checked( $options['post_title_suggest'], 1 ) ?> />
                </th>
                <td>
                    <label for="post_title_suggest">
						<?php esc_html_e( 'Activate Google Suggest for post title in the editor', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Language specific keyword suggestions while writing the post title', 'wpseo' ) ?><?php wpSEOde::help_icon( 80 ) ?>
                    </small>
                </td>
            </tr>
        </table>

		<?php submit_button() ?>
	<?php }

	

	private static function _the_list( $ids, $prefix, $dict ) {
		
		if ( empty( $ids ) or ! is_array( $ids ) or empty( $prefix ) ) {
			return;
		}

		foreach ( $ids as $id ) {
			if ( ! empty( $id ) ) {
				echo sprintf(
					'<li id="%s">%s</li>',
					esc_attr( $prefix . '_' . $id ),
					esc_html__( $dict[ $id ], 'wpseo' )
				);
			}
		}
	}

	

	public static function metabox_desc() {
		
		$options = wpSEOde_Options::get(); ?>

        <table class="form-table">

            <tr>
                <th>
                    <input type="checkbox" name="desc_enable" id="desc_enable"
                           value="1" <?php checked( $options['desc_enable'], 1 ) ?> />
                </th>
                <td>
                    <label for="desc_enable">
						<?php esc_html_e( 'Activate generation of the meta description', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Output of the description as meta information in blog pages', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>

                    <table class="level2 list">
						<?php foreach ( wpSEOde_Vars::get( 'group_items' ) as $item => $name ) { ?>
                            <tr>
                                <td>
                                    <label for="desc_<?php echo esc_attr( $item ) ?>">
										<?php esc_html_e( $name, 'wpseo' ) ?>
                                    </label>
                                </td>
                                <td>
                                    <select name="desc_<?php echo esc_attr( $item ) ?>"
                                            id="desc_<?php echo esc_attr( $item ) ?>">
                                        <option value=""><?php esc_html_e( 'No value', 'wpseo' ) ?></option>
										<?php foreach ( wpSEOde_Vars::get( 'meta_desc', $item ) as $k => $v ) {
											if ( ! isset( $options[ 'desc_' . $item ] ) && substr( $item, 0, 9 ) == 'posttype_' ) {
												$options[ 'desc_' . $item ] = $options['desc_posttype'];
											}
											?>
                                            <option value="<?php echo esc_attr( $k ) ?>" <?php selected( $options[ 'desc_' . $item ], $k ) ?>><?php esc_html_e( $v, 'wpseo' ) ?></option>
										<?php } ?>
                                    </select>
                                </td>
                            </tr>
						<?php } ?>
                    </table>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="desc_counter" id="desc_counter"
                                       value="1" <?php checked( $options['desc_counter'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="desc_counter">
									<?php esc_html_e( 'Limited to a maximum of 150 characters', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Ensuring full visibility in Google Snippets', 'wpseo' ) ?><?php wpSEOde::help_icon( 144 ) ?>
                                </small>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input type="checkbox" name="desc_tender" id="desc_tender"
                                       value="1" <?php checked( $options['desc_tender'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="desc_tender">
									<?php esc_html_e( 'Only full sentences', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Shorter, but full sentences in search results', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="desc_manually" id="desc_manually"
                           value="1" <?php checked( $options['desc_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="desc_manually">
						<?php esc_html_e( 'Display field for manual input of description', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign a customized description per article or page', 'wpseo' ) ?><?php wpSEOde::help_icon( 133 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="desc_suggest" id="desc_suggest"
                                       value="1" <?php checked( $options['desc_suggest'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="desc_suggest">
									<?php esc_html_e( 'Activate Google Suggest to add suggestions to your entries', 'wpseo' ) ?><?php wpSEOde::help_icon( 80 ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="checkbox" name="tax_manually" id="tax_manually"
                           value="1" <?php checked( $options['tax_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="tax_manually">
						<?php esc_html_e( 'Display field for taxonomy short description', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign customized text for post tags, categories and taxonomies', 'wpseo' ) ?><?php wpSEOde::help_icon( 137 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="tax_manually_prio" id="tax_manually_prio"
                                       value="1" <?php checked( $options['tax_manually_prio'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="tax_manually_prio">
									<?php esc_html_e( 'Manually set short description overwrites regular meta description', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'This will be the default behaviour in a future version of wpSEO', 'wpseo' ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="form-table">
            <tr>
                <td>
                    <label for="desc_default">
						<?php esc_html_e( 'Optional meta description on start page', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Manual description for the start page', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>

                    <input type="text" name="desc_default" id="desc_default" class="regular-text"
                           value="<?php echo esc_attr( $options['desc_default'] ) ?>"/>
                </td>
            </tr>
        </table>

		<?php if ( get_option( 'page_for_posts' ) ) { ?>
            <table class="form-table">
                <tr>
                    <td>
                        <label for="desc_default_home">
							<?php esc_html_e( 'Optional meta description on blog start page', 'wpseo' ) ?>
                        </label>
                        <small>
							<?php esc_html_e( 'Manual description for the blog start page', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                        </small>

                        <input type="text" name="desc_default_home" id="desc_default_home" class="regular-text"
                               value="<?php echo esc_attr( $options['desc_default_home'] ) ?>"/>
                    </td>
                </tr>
            </table>
		<?php } ?>

		<?php submit_button() ?>
	<?php }


	

	public static function metabox_noindex() {
		
		$options = wpSEOde_Options::get(); ?>

        <table class="form-table">

            <tr>
                <th>
                    <input type="checkbox" name="noindex_enable" id="noindex_enable"
                           value="1" <?php checked( $options['noindex_enable'], 1 ) ?> />
                </th>
                <td>
                    <label for="noindex_enable">
						<?php esc_html_e( 'Activate integration of the robots metatag', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Control the indexing of individual areas of the website', 'wpseo' ) ?><?php wpSEOde::help_icon( 179 ) ?>
                    </small>

                    <table class="level2 list">
						<?php foreach ( wpSEOde_Vars::get( 'group_items' ) as $item => $name ) { ?>
                            <tr>
                                <td>
                                    <label for="noindex_<?php echo esc_attr( $item ) ?>">
										<?php esc_html_e( $name, 'wpseo' ) ?>
                                    </label>
                                </td>
                                <td>
                                    <select name="noindex_<?php echo esc_attr( $item ) ?>"
                                            id="noindex_<?php echo esc_attr( $item ) ?>">
                                        <option value=""><?php esc_html_e( 'No value', 'wpseo' ) ?></option>
										<?php foreach ( wpSEOde_Vars::get( 'meta_robots' ) as $k => $v ) {
											if ( $k == 0 ) {
												continue;
											}
											if ( ! isset( $options[ 'noindex_' . $item ] ) && substr( $item, 0, 9 ) == 'posttype_' ) {
												$options[ 'noindex_' . $item ] = $options['noindex_posttype'];
											}
											?>
                                            <option value="<?php echo esc_attr( $k ) ?>" <?php selected( $options[ 'noindex_' . $item ], $k ) ?>>
												<?php echo esc_html( $v ) ?>
                                            </option>
										<?php } ?>
                                    </select>
                                </td>
                            </tr>
						<?php } ?>
                    </table>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="checkbox" name="noindex_manually" id="noindex_manually"
                           value="1" <?php checked( $options['noindex_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="noindex_manually">
						<?php esc_html_e( 'Display a choice for the manual control of indexing', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php _e( 'Assign a customized robots value per article or page', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>
                </td>
            </tr>


        </table>


        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="noindex_hidden" id="noindex_hidden"
                           value="1" <?php checked( $options['noindex_hidden'], 1 ) ?> />
                </th>
                <td>
                    <label for="noindex_hidden">
						<?php _e( "Don't publish metatag values <em>index, follow</em> and <em>index</em>", 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( "Default values don't need declaration in source code", 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>
                </td>
            </tr>


            <tr>
                <th>
                    <input type="checkbox" name="noindex_age" id="noindex_age"
                           value="1" <?php checked( $options['noindex_age'], 1 ) ?> />
                </th>
                <td>
                    <label for="noindex_age">
						<?php _e( 'Set posts after 6 months as <em>noindex, follow</em>', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Automatically block posts to be indexed', 'wpseo' ) ?><?php wpSEOde::help_icon( 114 ) ?>
                    </small>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="tax_robots_manually" id="tax_robots_manually"
                           value="1" <?php checked( $options['tax_robots_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="tax_robots_manually">
						<?php esc_html_e( 'Display a choice for the manual control of indexing on taxonomies', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign a customized robots value for post tags, categories and taxonomies', 'wpseo' ) ?><?php wpSEOde::help_icon( 179 ) ?>
                    </small>
                </td>
            </tr>
        </table>

		<?php submit_button() ?>
	<?php }

	public static function metabox_canonicals() {
		
		$options = wpSEOde_Options::get(); ?>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="canonical_manually" id="canonical_manually"
                           value="1" <?php checked( $options['canonical_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="canonical_manually">
						<?php esc_html_e( 'Display field for manual input of canonical url', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php _e( 'Assign a customized canonical value per article or page', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="noindex_canonical" id="noindex_canonical"
                           value="1" <?php checked( $options['noindex_canonical'], 1 ) ?> />
                </th>
                <td>
                    <label for="noindex_canonical">
						<?php esc_html_e( 'Generate meta canonical link for each webpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Determine preferred permalink and output as canonical url', 'wpseo' ) ?><?php wpSEOde::help_icon( 120 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="noindex_nocanonical" id="noindex_nocanonical"
                                       value="1" <?php checked( $options['noindex_nocanonical'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="noindex_nocanonical">
									<?php _e( 'Not on pages with <em>noindex</em> as metatag value', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>

					<?php if ( is_ssl() ) { ?>
                        <table class="level2">
                            <tr>
                                <td>
                                    <input type="checkbox" name="noindex_http" id="noindex_http"
                                           value="1" <?php checked( $options['noindex_http'], 1 ) ?> />
                                </td>
                                <td>
                                    <label for="noindex_http">
										<?php _e( 'Forcing the HTTP protocol in canonical urls', 'wpseo' ) ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
					<?php } ?>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	public static function metabox_sitemap() {
		
		$options = wpSEOde_Options::get();
		if ( wpSEOde_Sitemap::exists() ) {
			echo '<span class="notice notice-warning">wpSEO: ' . sprintf( __( 'Warning: One of the files <a href="%s" target="_blank">sitemap.xml</a>, <a href="%s" target="_blank">sitemap-page.xml</a>, <a href="%s" target="_blank">sitemap-post.xml</a> or <a href="%s" target="_blank">sitemap-custom.xml</a> exists on this Installation, you have to delete those in order to use the sitemap-feature of wpSEO!', 'wpseo' ), home_url( '/sitemap.xml' ), home_url( '/sitemap-page.xml' ), home_url( '/sitemap-post.xml' ), home_url( '/sitemap-custom.xml' ) ) . '</span>';
		}
		?>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="sitemap" id="sitemap"
                           value="1" <?php checked( $options['sitemap'], 1 ) ?> />
                </th>
                <td>
                    <label for="sitemap">
						<?php esc_html_e( 'Create Sitemap', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php _e( 'Create sitemap.xml for better indexing', 'wpseo' ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="sitemap_manually" id="sitemap_manually"
                                       value="1" <?php checked( $options['sitemap_manually'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="sitemap_manually">
									<?php esc_html_e( 'Assign Sitemap inclusion and exclusion option per article or page', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	public static function metabox_redirects() {
		
		$options = wpSEOde_Options::get(); ?>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="redirect_manually" id="redirect_manually"
                           value="1" <?php checked( $options['redirect_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="redirect_manually">
						<?php esc_html_e( 'Display field for manual input of redirect url', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign a customized redirect value per article or page', 'wpseo' ) ?><?php wpSEOde::help_icon( 69 ) ?>
                    </small>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="redirect_attachment" id="redirect_attachment"
                           value="1" <?php checked( $options['redirect_attachment'], 1 ) ?> />
                </th>
                <td>
                    <label for="redirect_attachment">
						<?php esc_html_e( 'redirect attachment-page to attachment', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'WordPress adds an attachment-page for each attachment created. By activating this option those pages directly redirect the visitor to the attachment.', 'wpseo' ) ?>
                    </small>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	public static function metabox_robots() {
		
		$options = wpSEOde_Options::get(); ?>
        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="misc_noodp" id="misc_noodp"
                           value="1" <?php checked( $options['misc_noodp'], 1 ) ?> />
                </th>
                <td>
                    <label for="misc_noodp">
						<?php _e( 'Write Noodp as value in robots metatag', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'No data usage of DMOZ/ODP directory', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="checkbox" name="misc_noarchive" id="misc_noarchive"
                           value="1" <?php checked( $options['misc_noarchive'], 1 ) ?> />
                </th>
                <td>
                    <label for="misc_noarchive">
						<?php _e( 'Write Noarchive as value in robots metatag', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'No caching of websites by search engines', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	

	public static function metabox_performance() {
		
		$options = wpSEOde_Options::get();
		?>

        <table class="form-table">
			<?php foreach ( wpSEOde_Vars::get( 'speed_nocheck' ) as $k => $v ) { ?>
                <tr>
                    <th>
                        <input type="radio" name="speed_nocheck" id="speed_nocheck_<?php echo esc_attr( $k ) ?>"
                               value="<?php echo esc_attr( $k ) ?>" <?php checked( $options['speed_nocheck'], $k ) ?> />
                    </th>
                    <td>
                        <label for="speed_nocheck_<?php echo esc_attr( $k ) ?>">
							<?php esc_html_e( $v[0], 'wpseo' ) ?>
                        </label>
                        <small>
							<?php esc_html_e( $v[1], 'wpseo' ) ?><?php wpSEOde::help_icon( 50 ) ?>
                        </small>
                    </td>
                </tr>
			<?php } ?>
        </table>

		<?php submit_button();
	}


	

	public static function metabox_misc() {
		
		$options = wpSEOde_Options::get();

		
		if ( ! $order = $options['misc_order'] ) {
			$order = array_keys( wpSEOde_Vars::get( 'misc_order' ) );
		} ?>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="strip_categorybase" id="strip_categorybase"
                           value="1" <?php checked( $options['strip_categorybase'], 1 ) ?> />
                </th>
                <td>
                    <label for="strip_categorybase">
						<?php esc_html_e( 'Option to show remove /category/ from category permalinks', 'wpseo' ) ?>
                    </label>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="redirect_old_categorybase" id="redirect_old_categorybase"
                                       value="1" <?php checked( $options['redirect_old_categorybase'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="redirect_old_categorybase">
									<?php esc_html_e( 'Redirect old /category/ path to new URL via 301', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="ignore_manually" id="ignore_manually"
                           value="1" <?php checked( $options['ignore_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="ignore_manually">
						<?php esc_html_e( 'Option to show internal exception list', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'No optimization by wpSEO for selected posts', 'wpseo' ) ?><?php wpSEOde::help_icon( 157 ) ?>
                    </small>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="misc_nextpage" id="misc_nextpage"
                           value="1" <?php checked( $options['misc_nextpage'], 1 ) ?> />
                </th>
                <td>
                    <label for="misc_nextpage">
						<?php esc_html_e( 'Allow meta data for multi page articles', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Search for manual added SEO values on divided posts', 'wpseo' ) ?><?php wpSEOde::help_icon( 41 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="misc_nextpage_rewrite" id="misc_nextpage_rewrite"
                                       value="1" <?php checked( $options['misc_nextpage_rewrite'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="misc_nextpage_rewrite">
									<?php esc_html_e( 'Replace title of following article with nextpage title', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

			<?php if ( strpos( get_option( 'permalink_structure' ), '%postname%' ) !== false ) { ?>
                <tr>
                    <th>
                        <input type="checkbox" name="misc_slug" id="misc_slug"
                               value="1" <?php checked( $options['misc_slug'], 1 ) ?> />
                    </th>
                    <td>
                        <label for="misc_slug">
							<?php esc_html_e( 'Generate the permalink from keywords of current post title', 'wpseo' ) ?>
                        </label>
                        <small>
							<?php esc_html_e( 'Use title nouns as constituents of permalinks', 'wpseo' ) ?><?php wpSEOde::help_icon( 110 ) ?>
                        </small>

                        <table class="level2">
                            <tr>
                                <td>
                                    <select name="misc_slug_max" id="misc_slug_max">
										<?php for ( $i = 2; $i <= 8; $i ++ ) { ?>
                                            <option value="<?php echo $i ?>" <?php selected( $options['misc_slug_max'], $i ) ?>><?php echo sprintf( esc_html__( 'Length: %d nouns', 'wpseo' ), $i ) ?></option>
										<?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
			<?php } ?>

            <tr>
                <th>
                    <input type="checkbox" name="misc_wplink" id="misc_wplink"
                           value="1" <?php checked( $options['misc_wplink'], 1 ) ?> />
                </th>
                <td>
                    <label for="misc_wplink">
						<?php esc_html_e( 'Display additional options when inserting links in the editor', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php _e( 'Assignment of <em>title</em> and <em>rel=nofollow</em> attributes', 'wpseo' ) ?><?php wpSEOde::help_icon( 122 ) ?>
                    </small>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="checkbox" name="paged_archive" id="paged_archive"
                           value="1" <?php checked( $options['paged_archive'], 1 ) ?> />
                </th>
                <td>
                    <label for="paged_archive">
						<?php _e( 'Treat paginated pages as <em>remaining pages</em>', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'At default the paginated pages inherit the setting of the main page', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>
                </td>
            </tr>
        </table>


        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="key_manually" id="key_manually"
                           value="1" <?php checked( $options['key_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="key_manually">
						<?php esc_html_e( 'Display field for manual input of keywords', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Assign customized keywords per article or page', 'wpseo' ) ?><?php wpSEOde::help_icon( 133 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="key_suggest" id="key_suggest"
                                       value="1" <?php checked( $options['key_suggest'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="key_suggest">
									<?php esc_html_e( 'Activate Google Suggest to add suggestions to your entries', 'wpseo' ) ?><?php wpSEOde::help_icon( 80 ) ?>
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <input type="checkbox" name="key_news" id="key_news"
                                       value="1" <?php checked( $options['key_news'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="key_news">
									<?php esc_html_e( 'Output the keywords as news_keywords for Google News', 'wpseo' ) ?><?php wpSEOde::help_icon( 153 ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="form-table">
            <tr>
                <td>
                    <label for="misc_order">
						<?php esc_html_e( 'Order of meta tags via Drag&amp;Drop', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Hierarchy of the page titles and the tags at the output', 'wpseo' ) ?><?php wpSEOde::help_icon( 130 ) ?>
                    </small>

                    <div class="sortable less">
                        <input type="hidden" name="misc_order" id="misc_order"
                               value="<?php echo esc_attr( implode( ',', $order ) ); ?>"/>

                        <div>
                            <ul class="x_axis">
								<?php self::_the_list( $order, 'misc_order', wpSEOde_Vars::get( 'misc_order' ) ); ?>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

		<?php submit_button() ?>
	<?php }


	public static function metabox_language() {
		$options = wpSEOde_Options::get();
		?>
        <table class="form-table">
            <tr>
                <td>
                    <label for="misc_lang">
						<?php esc_html_e( 'Select language of frontend', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Deactivation of language recognition but assignment of language', 'wpseo' ) ?><?php wpSEOde::help_icon( 1 ) ?>
                    </small>

                    <select name="misc_lang" id="misc_lang">
						<?php foreach ( wpSEOde_Vars::get( 'plugin_lang' ) as $k => $v ) { ?>
                            <option value="<?php echo esc_attr( $k ) ?>" <?php selected( $options['misc_lang'], $k ) ?>><?php echo esc_html( $v ) ?></option>
						<?php } ?>
                    </select>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	

	public static function metabox_tools() { ?>
        <table class="form-table">
            <tr>
                <th>
                    <input type="submit" class="button" id="wpseo_action_export"
                           value="<?php esc_attr_e( 'Export as XML', 'wpseo' ) ?>"/>
                </th>
                <td>
                    <label>
						<?php esc_html_e( 'Save wpSEO options as XML', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Suitable for forwarding, as a backup and import', 'wpseo' ) ?><?php wpSEOde::help_icon( 6 ) ?>
                    </small>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="submit" class="button" id="wpseo_action_reset"
                           value="<?php esc_attr_e( 'Reset wpSEO', 'wpseo' ) ?>"/>
                </th>
                <td>
                    <label>
						<?php esc_html_e( 'Load default settings', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Irrevocable loading of the default settings', 'wpseo' ) ?><?php wpSEOde::help_icon( 27 ) ?>
                    </small>
                </td>
            </tr>

            <tr>
                <th>
                    <input type="submit" class="button" id="wpseo_action_import"
                           value="<?php esc_attr_e( 'Import from XML', 'wpseo' ) ?>"/>
                </th>
                <td>
                    <label>
                        <input type="file" name="wpseo_upadd_file"
                               title="<?php esc_attr_e( 'Please select the XML file first.', 'wpseo' ) ?>"/>
                    </label>
                    <small>
						<?php esc_html_e( 'To Import XML file with wpSEO options', 'wpseo' ) ?><?php wpSEOde::help_icon( 6 ) ?>
                    </small>
                </td>
            </tr>
        </table>
	<?php }

	public static function metabox_import() {
		$import_data = get_option( 'wpseo_import_results' );
		if ( $import_data ) {
			$date = date( 'd.m.Y H:i:s', $import_data['last_update'] );
		}
		?>
        <table class="form-table">
            <tr>
                <th>
                    <input type="submit" class="button" id="wpseo_action_import_yoast"
                           value="<?php esc_attr_e( 'Yoast SEO', 'wpseo' ) ?>"/>
                </th>
                <td>
                    <label>
						<?php esc_html_e( 'Import from Yoast SEO', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Import data from Yoast SEO installation', 'wpseo' ) ?><?php wpSEOde::help_icon( 6 ) ?>
                    </small>
                </td>
            </tr>
			<?php
			if ( $import_data ) {
				?>
                <td colspan="2" style="padding: 10px;">
                    <strong><?php esc_html_e( 'Last import', 'wpseo' ) ?><?php echo ': ' . $date; ?></strong>
                </td>
				<?php
			}
			?>
        </table>
		<?php
	}

	public static function metabox_settings() {
		$options = wpSEOde_Options::get();
		?>
        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="misc_monitor" id="misc_monitor"
                           value="1" <?php checked( $options['misc_monitor'], 1 ) ?> />
                </th>
                <td>
                    <label for="misc_monitor">
						<?php esc_html_e( 'Display SEO Monitor on Dashboard', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Relevant figures for the analysis of the SEO optimization', 'wpseo' ) ?><?php wpSEOde::help_icon( 23 ) ?>
                    </small>

                    <table class="level2">
                        <tr>
                            <td>
                                <input type="checkbox" name="misc_monitor_theme" id="misc_monitor_theme"
                                       value="1" <?php checked( $options['misc_monitor_theme'], 1 ) ?> />
                            </td>
                            <td>
                                <label for="misc_monitor_theme">
									<?php esc_html_e( 'Allow theme access to the values', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		<?php
		submit_button();
	}

	public static function metabox_licensing() {
		$options = wpSEOde_Options::get();
		if ( isset( $_GET['result'] ) ) {
			if ( $_GET['result'] == 'error' ) {
				$class   = 'notice notice-error is-dismissible';
				$message = __( 'Your license could not be activated.', 'wpseo' );
			} elseif ( $_GET['result'] == 'success2' ) {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Your license was successfully deactivated.', 'wpseo' );
			} else {
				$class   = 'notice notice-success is-dismissible';
				$message = __( 'Your license was successfully activated.', 'wpseo' );
			}
			printf( '<div class="%1$s"><p>wpSEO: %2$s</p></div>', $class, $message );
		}

		?>
        <table class="form-table">
            <tr>
                <td><?php esc_html_e( 'Enter the license key you received via mail and click on activate. If you want to change your license key just enter the new one and click the change button.', 'wpseo' ); ?></td>
            </tr>
            <tr>
                <td>
					<?php esc_html_e( 'wpSEO license key', 'wpseo' ); ?>:
					<?php if ( wpSEOde_License::valid() ) {
						esc_html_e( 'Activated', 'wpseo' );
						echo '<br>';
						echo sprintf( __( 'License-Identifier: <b>%s</b>', 'wpseo' ), wpSEOde_License::get_hashed() );
						echo '<br><small>(' . __( 'Encrypted identification of your license key.', 'wpseo' ) . ')</small><br>';
					} ?>

                    <input type="text" name="_wpseo_key" id="_wpseo_key" size="64"/>
                    <input type="submit" name="submit" value="<?php if ( ! wpSEOde_License::valid() ) {
						esc_html_e( 'Activate', 'wpseo' );
					} else {
						esc_html_e( 'Change', 'wpseo' );
					} ?>" class="button button-primary regular"/><br/><br/>
                    <input type="submit" name="submit"
                           value="<?php esc_html_e( 'Remove and deactivate license', 'wpseo' ); ?>"
                           class="button button-secondary"
                           onclick="if ( confirm( '<?php esc_attr_e( 'Are you sure to remove your license key on this installation and deactivate this domains activation? You can reactivate your installation by entering your license key afterwards.', 'wpseo' ); ?>') === true ) { jQuery('#_wpseo_action').val('unverify'); } else { return false; }"/>
                </td>
            </tr>
        </table>
        <br>
		<?php

	}


	public static function metabox_googleplus() {
		
		$options = wpSEOde_Options::get();

		
		if ( ! $order = $options['misc_order'] ) {
			$order = array_keys( wpSEOde_Vars::get( 'misc_order' ) );
		} ?>

        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="authorship_manually" id="authorship_manually"
                           value="1" <?php checked( $options['authorship_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="authorship_manually">
						<?php esc_html_e( 'Show option to disable the Google+ Authorship', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Manually control of Authorship output on individual blog pages', 'wpseo' ) ?><?php wpSEOde::help_icon( 174 ) ?>
                    </small>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	public static function metabox_pinterest() {
		
		$options = wpSEOde_Options::get();

		
		if ( ! $order = $options['misc_order'] ) {
			$order = array_keys( wpSEOde_Vars::get( 'misc_order' ) );
		} ?>

        <table class="form-table">
            <tr>
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <label for="pinterest_domain_verify_tag">
									<?php esc_html_e( 'Pinterest domain-verification', 'wpseo' ) ?>
                                </label>
                                <br/>
                                <input type="text" name="pinterest_domain_verify_tag" id="pinterest_domain_verify_tag"
                                       value="<?php echo esc_attr( $options['pinterest_domain_verify_tag'] ) ?>"/>
                            </td>
                        </tr>
                    </table>

                    <small>
						<?php esc_html_e( 'Your domain-verification tag provided by Pinterest.', 'wpseo' ) ?><?php wpSEOde::help_icon( 180 ) ?>
                    </small>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	public static function metabox_socialprofiles() {
		
		$options = wpSEOde_Options::get();
		?>
        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="social_profiles" id="social_profiles"
                           value="1" <?php checked( $options['social_profiles'], 1 ) ?> />
                </th>
                <td>
                    <label for="social_profiles">
						<?php esc_html_e( 'Enable Company/Person Data and Social Profiles as Rich-Snippet', 'wpseo' ) ?>
                    </label>
                </td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <td>
                    <b><?php esc_html_e( 'Company/Person Data', 'wpseo' ); ?>:</b>
                    <table>
                        <tr>
                            <th>
                                <label for="social_data_type">
									<?php esc_html_e( 'This&nbsp;is', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <select name="social_data_type" id="social_data_type">
                                    <option value="Organization"<?php selected( $options['social_data_type'], 'Organization' ); ?>><?php esc_html_e( 'a Companies Website', 'wpseo' ); ?></option>
                                    <option value="Person"<?php selected( $options['social_data_type'], 'Person' ); ?>><?php esc_html_e( 'a Persons Website', 'wpseo' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social_data_name">
									<?php esc_html_e( 'Name', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_data_name" id="social_data_name"
                                       value="<?php echo esc_attr( $options['social_data_name'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. wpSEO', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <b><?php esc_html_e( 'Social Profiles', 'wpseo' ); ?>:</b>
                    <table>
                        <tr>
                            <th>
                                <label for="social_profile_youtube">
									<?php esc_html_e( 'Youtube', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_profile_youtube" id="social_profile_youtube"
                                       value="<?php echo esc_attr( $options['social_profile_youtube'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. https://www.youtube.com/user/GoogleWebmasterHelp', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social_profile_facebook">
									<?php esc_html_e( 'Facebook', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_profile_facebook" id="social_profile_facebook"
                                       value="<?php echo esc_attr( $options['social_profile_facebook'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. https://www.facebook.com/wpseode/', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social_profile_twitter">
									<?php esc_html_e( 'Twitter', 'wpseo' ) ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_profile_twitter" id="social_profile_twitter"
                                       value="<?php echo esc_attr( $options['social_profile_twitter'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. https://twitter.com/wpSEO', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social_profile_linkedin">
									<?php esc_html_e( 'Linkedin', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_profile_linkedin" id="social_profile_linkedin"
                                       value="<?php echo esc_attr( $options['social_profile_linkedin'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. https://www.linkedin.com/company/Google', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social_profile_instagram">
									<?php esc_html_e( 'Instagram', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_profile_instagram"
                                       id="social_profile_instagram"
                                       value="<?php echo esc_attr( $options['social_profile_instagram'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. https://www.instagram.com/google', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social_profile_pinterest">
									<?php esc_html_e( 'Pinterest', 'wpseo' ); ?>
                                </label>
                            </th>
                            <td>
                                <input type="text" size="40" name="social_profile_pinterest"
                                       id="social_profile_pinterest"
                                       value="<?php echo esc_attr( $options['social_profile_pinterest'] ) ?>"
                                       placeholder="<?php esc_attr_e( 'e. g. https://www.pinterest.de/Google/', 'wpseo' ); ?>"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}


	

	public static function metabox_twittercards() {
		
		$options = wpSEOde_Options::get();
		?>

        <table class="form-table">
            <tr>
                <td colspan="2">
                    <label for="twitter_site_account">
						<?php esc_html_e( 'Twitter account of website', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Link the content to your websites twitter profile', 'wpseo' ) ?>
                    </small>
                    <input type="text" size="40" name="twitter_site_account" id="twitter_site_account"
                           value="<?php echo esc_attr( $options['twitter_site_account'] ) ?>"/>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="twitter_cards_manually" id="twitter_cards_manually"
                           value="1" <?php checked( $options['twitter_cards_manually'], 1 ) ?> />
                </th>
                <td>
                    <label for="twitter_cards_manually">
						<?php esc_html_e( 'Show option to disable Twitter Cards', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Manually control the output of Twitter Cards on individual blog pages', 'wpseo' ) ?>
                    </small>
                </td>
            </tr>
            <tr>
                <th>
                    <input type="checkbox" name="twitter_authorship" id="twitter_authorship"
                           value="1" <?php checked( $options['twitter_authorship'], 1 ) ?> />
                </th>
                <td>
                    <label for="twitter_authorship">
						<?php esc_html_e( 'Output the authors Twitter account', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Add the authors Twitter account to the Twitter card', 'wpseo' ) ?>
                    </small>
                    <table class="level2">
                        <tr>
                            <th>
                                <input type="checkbox" name="twitter_authorship_manually"
                                       id="twitter_authorship_manually"
                                       value="1" <?php checked( $options['twitter_authorship_manually'], 1 ) ?> />
                            </th>
                            <td>
                                <label for="twitter_authorship_manually">
									<?php esc_html_e( 'Show option to disable the output', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Manually control the output of the authors twitter account on individual blog pages', 'wpseo' ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <td colspan="2">
                    <label for="twittercard_start_title">
						<?php esc_html_e( 'Twittercard title for startpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This title is used if startpage is not a static page', 'wpseo' ) ?>
                    </small>
                    <input type="text" size="40" name="twittercard_start_title" id="twittercard_start_title"
                           value="<?php echo esc_attr( $options['twittercard_start_title'] ) ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="twittercard_start_description">
						<?php esc_html_e( 'Twittercard description for startpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This description is used if startpage is not a static page', 'wpseo' ) ?>
                    </small>
                    <textarea cols="40" rows="5" name="twittercard_start_description"
                              id="twittercard_start_description"><?php echo esc_textarea( $options['twittercard_start_description'] ) ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="twittercard_start_image">
						<?php esc_html_e( 'Twittercard image URL for startpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This image URL is used if startpage is not a static page', 'wpseo' ) ?>
                    </small>
					<?php
					wpSEOde_GUI::wpseo_image_upload( 'twittercard_start_image', 'twittercard_start_image', esc_html__( 'Select or upload image', 'wpseo' ), esc_attr( $options['twittercard_start_image'] ) );
					?>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	public static function wpseo_image_upload( $id = '', $name = '', $button_text = 'Upload', $value = '' ) {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_media();
		?>
        <div>
            <input type="text" name="<?php echo $name; ?>" id="<?php echo $id; ?>" class="regular-text"
                   value="<?php echo esc_attr( $value ) ?>">
            <input type="button" name="upload-btn" id="<?php echo $id; ?>-upload-btn" class="button-secondary"
                   value="<?php echo $button_text; ?>">

        </div>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('#<?php echo $id;?>-upload-btn').click(function (e) {
                    e.preventDefault();
                    var image = wp.media({
                        title: 'Upload Image',
                        multiple: false
                    }).open()
                        .on('select', function (e) {
                            var uploaded_image = image.state().get('selection').first();
                            console.log(uploaded_image);
                            var image_url = uploaded_image.toJSON().url;
                            $('#<?php echo $id;?>').val(image_url).trigger('change');
                        });
                });
            });
        </script>
		<?php
	}

	

	public static function metabox_opengraph() {
		
		$options = wpSEOde_Options::get();
		?>
        <table class="form-table">
            <tr>
                <th>
                    <input type="checkbox" name="open_graph" id="open_graph"
                           value="1" <?php checked( $options['open_graph'], 1 ) ?> />
                </th>
                <td>
                    <label for="open_graph">
						<?php esc_html_e( 'Enable Open Graph Tags', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Enable the output of Open Gaph data for Facebook and Google+ Snippets', 'wpseo' ) ?>
                    </small>
                    <table class="level2">
                        <tr>
                            <th>
                                <input type="checkbox" name="open_graph_title_manually" id="open_graph_title_manually"
                                       value="1" <?php checked( $options['open_graph_title_manually'], 1 ) ?> />
                            </th>
                            <td>
                                <label for="open_graph_title_manually">
									<?php esc_html_e( 'Assign a customized Open Graph title per article or page', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <input type="checkbox" name="open_graph_description_manually"
                                       id="open_graph_description_manually"
                                       value="1" <?php checked( $options['open_graph_description_manually'], 1 ) ?> />
                            </th>
                            <td>
                                <label for="open_graph_description_manually">
									<?php esc_html_e( 'Assign a customized Open Graph description per article or page', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <input type="checkbox" name="open_graph_image_manually" id="open_graph_image_manually"
                                       value="1" <?php checked( $options['open_graph_image_manually'], 1 ) ?> />
                            </th>
                            <td>
                                <label for="open_graph_image_manually">
									<?php esc_html_e( 'Assign a customized Open Graph image per article or page', 'wpseo' ) ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <input type="checkbox" name="open_graph_manually" id="open_graph_manually"
                                       value="1" <?php checked( $options['open_graph_manually'], 1 ) ?> />
                            </th>
                            <td>
                                <label for="open_graph_manually">
									<?php esc_html_e( 'Show option to disable Open Graph Tags', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Manually control the output of Open Graph Tags on individual blog pages', 'wpseo' ) ?>
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <input type="checkbox" name="open_graph_date_disable" id="open_graph_date_disable"
                                       value="1" <?php checked( $options['open_graph_date_disable'], 1 ) ?> />
                            </th>
                            <td>
                                <label for="open_graph_date_disable">
									<?php esc_html_e( 'Show option to disable Open Graph Date', 'wpseo' ) ?>
                                </label>
                                <small>
									<?php esc_html_e( 'Manually control the output of Open Graph Date on individual blog pages', 'wpseo' ) ?>
                                </small>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <td colspan="2">
                    <label for="opengraph_start_title">
						<?php esc_html_e( 'Open Graph title for startpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This title is used if startpage is not a static page', 'wpseo' ) ?>
                    </small>
                    <input type="text" size="40" name="opengraph_start_title" id="opengraph_start_title"
                           value="<?php echo esc_attr( $options['opengraph_start_title'] ) ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opengraph_start_description">
						<?php esc_html_e( 'Open Graph description for startpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This description is used if startpage is not a static page', 'wpseo' ) ?>
                    </small>
                    <textarea cols="40" rows="5" name="opengraph_start_description"
                              id="opengraph_start_description"><?php echo esc_textarea( $options['opengraph_start_description'] ) ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opengraph_start_image">
						<?php esc_html_e( 'Open Graph image URL for startpage', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This image URL is used if startpage is not a static page', 'wpseo' ) ?>
                    </small>
					<?php
					wpSEOde_GUI::wpseo_image_upload( 'opengraph_start_image', 'opengraph_start_image', esc_html__( 'Select or upload image', 'wpseo' ), esc_attr( $options['opengraph_start_image'] ) );
					?>
                </td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <td colspan="2">
                    <label for="opengraph_post_title">
						<?php esc_html_e( 'Open Graph title for posts', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This title is used if post has no specified Open Graph title', 'wpseo' ) ?>
                    </small>
                    <input type="text" size="40" name="opengraph_post_title" id="opengraph_post_title"
                           value="<?php echo esc_attr( $options['opengraph_post_title'] ) ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opengraph_post_description">
						<?php esc_html_e( 'Open Graph description for posts', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This description is used if post has no specified Open Graph description', 'wpseo' ) ?>
                    </small>
                    <textarea cols="40" rows="5" name="opengraph_post_description"
                              id="opengraph_post_description"><?php echo esc_textarea( $options['opengraph_post_description'] ) ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opengraph_post_image">
						<?php esc_html_e( 'Open Graph image URL for posts', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This image URL is used if post has no specified Open Graph image', 'wpseo' ) ?>
                    </small>
					<?php
					wpSEOde_GUI::wpseo_image_upload( 'opengraph_post_image', 'opengraph_post_image', esc_html__( 'Select or upload image', 'wpseo' ), esc_attr( $options['opengraph_post_image'] ) );
					?>
                </td>
            </tr>
        </table>
        <table class="form-table">
            <tr>
                <td colspan="2">
                    <label for="opengraph_page_title">
						<?php esc_html_e( 'Open Graph title for pages', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This title is used if page has no specified Open Graph title', 'wpseo' ) ?>
                    </small>
                    <input type="text" size="40" name="opengraph_page_title" id="opengraph_page_title"
                           value="<?php echo esc_attr( $options['opengraph_page_title'] ) ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opengraph_page_description">
						<?php esc_html_e( 'Open Graph description for pages', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This description is used if page has no specified Open Graph description', 'wpseo' ) ?>
                    </small>
                    <textarea cols="40" rows="5" name="opengraph_page_description"
                              id="opengraph_page_description"><?php echo esc_textarea( $options['opengraph_page_description'] ) ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="opengraph_page_image">
						<?php esc_html_e( 'Open Graph image URL for pages', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'This image URL is used if page has no specified Open Graph image', 'wpseo' ) ?>
                    </small>
					<?php
					wpSEOde_GUI::wpseo_image_upload( 'opengraph_page_image', 'opengraph_page_image', esc_html__( 'Select or upload image', 'wpseo' ), esc_attr( $options['opengraph_page_image'] ) );
					?>
                </td>
            </tr>
        </table>
		<?php submit_button();
	}

	

	public static function metabox_snippets() {
		
		$snippets = wpSEOde_Options::get( 'snippets_data' ); ?>

        <table class="form-table">
            <tr>
                <td>
                    <label>
						<?php esc_html_e( 'Snippets output in the source code of blog pages', 'wpseo' ) ?>
                    </label>
                    <small>
						<?php esc_html_e( 'Important: Save the plugin settings after modifications', 'wpseo' ) ?><?php wpSEOde::help_icon( 146 ) ?>
                    </small>
                </td>
            </tr>
        </table>

        <fieldset>
            <legend>
				<?php esc_html_e( 'New Snippet', 'wpseo' ) ?>
            </legend>

            <ul class="draft">
                <li class="snippet">
                    <label>
                        Code
                    </label>
                    <textarea name="snippets[][code]" spellcheck="false"></textarea>

                    <p class="submit">
                        <button class="button" disabled><?php esc_html_e( 'Create Snippet', 'wpseo' ) ?></button>
                    </p>
                    <a href="#" class="dashicons dashicons-post-trash remove"
                       data-confirm-msg="<?php esc_attr_e( 'Delete Snippet?', 'wpseo' ) ?>"></a>

                    <label>
						<?php esc_attr_e( 'Short description', 'wpseo' ) ?>
                    </label>
                    <input type="text" name="snippets[][name]" value=""/>
                </li>
            </ul>
        </fieldset>

        <fieldset>
            <legend>
				<?php esc_html_e( 'Existing Snippets', 'wpseo' ) ?>
            </legend>

            <ul class="snippets"
                data-default-msg="<?php esc_attr_e( 'No snippets available', 'wpseo' ) ?>"><?php if ( ! empty( $snippets ) ) {
					foreach ( $snippets as $snippet ) { ?>
                        <li class="snippet">
                            <label>
                                Code
                            </label>
                            <textarea name="snippets[][code]"
                                      spellcheck="false"><?php echo esc_textarea( $snippet['code'] ) ?></textarea>

                            <a href="#" class="dashicons dashicons-post-trash remove"
                               data-confirm-msg="<?php esc_attr_e( 'Delete Snippet?', 'wpseo' ) ?>"></a>

                            <label>
								<?php esc_attr_e( 'Short description', 'wpseo' ) ?>
                            </label>
                            <input type="text" name="snippets[][name]" value="<?php esc_attr_e( $snippet['name'] ) ?>"/>
                        </li>
					<?php }
				} ?></ul>
        </fieldset>

		<?php submit_button() ?>
	<?php }

	public static function metabox_monitor() {

		$options = wpSEOde_Options::get( 'monitor_options' );
		?>

        <table class="form-table">
        <tr>
            <td>
                <label for="wpseo_monitor_seokicks">
                    <input type="checkbox" id="wpseo_monitor_seokicks" name="monitor_options[seokicks]"
                           value="1" <?php checked( @$options['seokicks'], 1 ) ?> />
                    SEOkicks Domainpop + Linkpop
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <label for="wpseo_monitor_metricstools">
                    <input type="checkbox" id="wpseo_monitor_metricstools" name="monitor_options[metricstools]"
                           value="1" <?php checked( @$options['metricstools'], 1 ) ?> />
                    Metrics Tools SK
                </label>
            </td>
        </tr>
		<?php
		
		?>
        <tr>
            <td>
                <label for="wpseo_monitor_seitwert">
                    <input type="checkbox" id="wpseo_monitor_seitwert" name="monitor_options[seitwert]"
                           value="1" <?php checked( @$options['seitwert'], 1 ) ?> />
                    Seitwert
                </label>
				<?php wpSEOde::help_icon( 23 ) ?>

                <input type="text" name="monitor_options[seitwert_key]"
                       value="<?php echo esc_attr( @$options['seitwert_key'] ) ?>"
                       placeholder="Seitwert&nbsp;(API Key)"/>
            </td>
        </tr>

        <tr>
            <td>
                <label for="wpseo_monitor_pagespeed">
                    <input type="checkbox" id="wpseo_monitor_pagespeed" name="monitor_options[pagespeed]"
                           value="1" <?php checked( @$options['pagespeed'], 1 ) ?> />
                    Page Speed
                </label>
				<?php wpSEOde::help_icon( 23 ) ?>
                <input type="text" name="monitor_options[pagespeed_key]"
                       value="<?php echo esc_attr( @$options['pagespeed_key'] ) ?>"
                       placeholder="Page Speed&nbsp;(API Key)"/>
            </td>
        </tr>

        <tr>
        <td>
        <table>
			<?php
			
			?>
            <table>
                <tr>
                    <td>Facebook App-ID:<br><small>Zur Identifizierung der Webseite.</small></td>
                    <td><input type="text" name="monitor_options[facebook_app_id]"
                               value="<?php echo esc_attr( @$options['facebook_app_id'] ) ?>"
                               placeholder="Facebook&nbsp;App ID"/></td>
                </tr>
            </table>
			<?php
			
			?>
            <tr>
                <td>
                    <label for="wpseo_monitor_twitter">
                        <input type="checkbox" id="wpseo_monitor_twitter" name="monitor_options[twitter]"
                               value="1" <?php checked( @$options['twitter'], 1 ) ?> />
                        Twitter
                    </label>
					<?php wpSEOde::help_icon( 23 ) ?>
                    <input type="text" name="monitor_options[twitter_id]"
                           value="<?php echo esc_attr( @$options['twitter_id'] ) ?>" placeholder="Twitter&nbsp;(ID)"/>
                </td>
            </tr>

        </table>
		<?php submit_button();
	}
}
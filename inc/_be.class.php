<?php


defined( 'ABSPATH' ) or exit;



class wpSEOde {


	

	public static function init() {
		wpSEOde_Options::_migrate2();

		
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) or ( defined( 'DOING_CRON' ) && DOING_CRON ) or ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) ) {
			return;
		}

		
		add_action(
			'wp_ajax_wpseo_dismiss',
			array(
				'wpSEOde_Ajax',
				'dismiss'
			)
		);

		add_action(
			'registered_taxonomy',
			array(
				'wpSEOde_Tax',
				'add_actions'
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
			'pre_set_site_transient_update_plugins',
			array(
				'wpSEOde_Update',
				'do_update_check'
			)
		);

		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			if ( ! empty( $_POST['action'] ) && $_POST['action'] === 'sample-permalink' && wpSEOde_Options::get( 'misc_slug' ) ) {
				wpSEOde_Slug::init();
			} elseif ( ! empty( $_POST['action'] ) && $_POST['action'] === 'add-tag' && ( wpSEOde_Options::get( 'tax_title_manually' ) || wpSEOde_Options::get( 'tax_manually' ) || wpSEOde_Options::get( 'tax_robots_manually' ) or wpSEOde_Options::get( 'canonical_manually' ) or wpSEOde_Options::get( 'open_graph' ) or wpSEOde_Options::get( 'twitter_site_account' ) != '' ) ) {
				wpSEOde_Tax::init();
			} elseif ( ! empty( $_POST['action'] ) && $_POST['action'] === 'inline-save' && ( wpSEOde_Options::get( 'title_manually' ) || wpSEOde_Options::get( 'desc_manually' ) ) ) {
				add_action(
					'save_post',
					array(
						'wpSEOde_Meta',
						'update_fields'
					)
				);
				add_filter(
					'manage_posts_columns',
					array(
						'wpSEOde_Meta',
						'add_column_labels'
					)
				);
				add_filter(
					'manage_pages_columns',
					array(
						'wpSEOde_Meta',
						'add_column_labels'
					)
				);
				add_filter(
					'manage_media_columns',
					array(
						'wpSEOde_Meta',
						'add_column_labels'
					)
				);

				add_filter(
					'manage_posts_custom_column',
					array(
						'wpSEOde_Meta',
						'add_column_values'
					),
					10,
					2
				);
				add_filter(
					'manage_pages_custom_column',
					array(
						'wpSEOde_Meta',
						'add_column_values'
					),
					10,
					2
				);
				add_filter(
					'manage_media_custom_column',
					array(
						'wpSEOde_Meta',
						'add_column_values'
					),
					10,
					2
				);
			}

			

			return;
		}

		
		add_action(
			'admin_menu',
			array(
				__CLASS__,
				'init_menu'
			)
		);


		add_action(
			'network_admin_menu',
			array(
				'wpSEOde_GUI',
				'init_menu'
			)
		);

		add_action(
			'admin_print_scripts',
			array(
				__CLASS__,
				'add_scripts'
			)
		);

		
		$page_now = self::current_page();

		
		if ( self::skip_loading( $page_now, 'options' ) ) {
			return;
		}

		
		add_action(
			'init',
			array(
				__CLASS__,
				'load_textdomain'
			)
		);
		add_action(
			'admin_notices',
			array(
				'wpSEOde_Feedback',
				'rules'
			),
			10
		);
		add_action(
			'network_admin_notices',
			array(
				'wpSEOde_Feedback',
				'rules'
			),
			10
		);
		add_action(
			'network_admin_notices',
			array(
				'wpSEOde_Feedback',
				'network'
			),
			11
		);
		add_action(
			'admin_notices',
			array(
				'wpSEOde_Feedback',
				'admin'
			),
			11
		);

		add_filter(
			'plugins_api',
			array(
				'wpSEOde_Update',
				'provide_plugin_info'
			),
			10,
			3
		);

		add_action(
			'init',
			array(
				'wpSEOde_License',
				'shame'
			)
		);
		add_action(
			'wpmu_new_blog',
			array(
				__CLASS__,
				'install_later'
			)
		);
		add_action(
			'delete_blog',
			array(
				__CLASS__,
				'uninstall_later'
			)
		);

		
		if ( self::skip_loading( $page_now, 'pages' ) ) {
			return;
		}

		
		if ( ( wpSEOde_Feedback::get( 'critical' ) or wpSEOde_License::expired() ) && $page_now != 'admin-post' && isset( $_POST['_wpseo_page'] ) && $_POST['_wpseo_page'] != 'licensing' ) {
			return;
		}

		
		$options = wpSEOde_Options::get();

		
		switch ( $page_now ) {
			
			case 'index':
				
				if ( $options['misc_monitor'] ) {
					add_action(
						'wp_dashboard_setup',
						array(
							'wpSEOde_Dashboard',
							'init'
						)
					);
				}
				break;

			
			case 'profile':
			case 'user-edit':
				add_filter(
					'user_contactmethods',
					array(
						'wpSEOde_User',
						'add_field'
					)
				);
				break;

			
			case 'term':
			case 'edit-tags':
				if ( $options['tax_title_manually'] or $options['tax_manually'] or $options['tax_robots_manually'] or $options['canonical_manually'] or $options['open_graph'] or $options['twitter_site_account'] != '' ) {
					add_action(
						'admin_init',
						array(
							'wpSEOde_Tax',
							'init'
						)
					);
				}
				break;


			
			case 'edit':
			case 'upload':
				
				if ( $options['title_manually'] or $options['desc_manually'] or $options['key_manually'] or $options['noindex_manually'] or $options['canonical_manually'] or $options['redirect_manually'] or $options['ignore_manually'] or $options['authorship_manually'] ) {
					
					add_action(
						'admin_init',
						array(
							'wpSEOde_Meta',
							'init'
						)
					);
					add_action(
						'save_post',
						array(
							'wpSEOde_Meta',
							'update_fields'
						)
					);
					add_action(
						'quick_edit_custom_box',
						array(
							'wpSEOde_Meta',
							'post_add_quick_edit'
						),
						10,
						2
					);
				}

				
				if ( $options['title_manually'] or $options['desc_manually'] or $options['key_manually'] or $options['noindex_manually'] or $options['canonical_manually'] or $options['redirect_manually'] ) {
					add_filter(
						'manage_posts_columns',
						array(
							'wpSEOde_Meta',
							'add_column_labels'
						)
					);
					add_filter(
						'manage_pages_columns',
						array(
							'wpSEOde_Meta',
							'add_column_labels'
						)
					);
					add_filter(
						'manage_media_columns',
						array(
							'wpSEOde_Meta',
							'add_column_labels'
						)
					);

					add_filter(
						'manage_posts_custom_column',
						array(
							'wpSEOde_Meta',
							'add_column_values'
						),
						10,
						2
					);
					add_filter(
						'manage_pages_custom_column',
						array(
							'wpSEOde_Meta',
							'add_column_values'
						),
						10,
						2
					);
					add_filter(
						'manage_media_custom_column',
						array(
							'wpSEOde_Meta',
							'add_column_values'
						),
						10,
						2
					);
				}
				break;


			
			case 'post':
			case 'post-new':
				
				if ( $options['misc_wplink'] ) {
					add_action(
						'admin_print_scripts',
						array(
							__CLASS__,
							'add_wplink'
						)
					);
				}

				
				if ( $options['post_title_suggest'] or $options['title_suggest'] or $options['desc_suggest'] or $options['key_suggest'] ) {
					add_action(
						'admin_enqueue_scripts',
						array(
							'wpSEOde_Suggest',
							'add_resources'
						)
					);
				}

				
				if ( isset( $_GET['app'] ) && $_GET['app'] == 'uxbuilder' ) {
					add_filter( 'wpseo_add_meta_boxes', '__return_false' );
				}

				
				if ( $options['title_manually'] or $options['desc_manually'] or $options['key_manually'] or $options['noindex_manually'] or $options['canonical_manually'] or $options['redirect_manually'] or $options['ignore_manually'] or $options['authorship_manually'] ) {
					
					add_action(
						'admin_init',
						array(
							'wpSEOde_Meta',
							'init'
						)
					);
					add_action(
						'save_post',
						array(
							'wpSEOde_Meta',
							'update_fields'
						)
					);
					add_action(
						'edit_attachment',
						array(
							'wpSEOde_Meta',
							'update_fields'
						)
					);
					add_action(
						'before_delete_post',
						array(
							'wpSEOde_Meta',
							'delete_fields'
						)
					);
				}

				
				if ( $options['misc_slug'] ) {
					wpSEOde_Slug::init();
				}
				break;


			
			case 'plugins':
				add_action(
					'init',
					array(
						'wpSEOde_License',
						'verify'
					)
				);

				add_action(
					'after_plugin_row_' . WPSEODE_BASE,
					array(
						__CLASS__,
						'add_key_field'
					)
				);
				add_filter(
					'plugin_action_links_' . WPSEODE_BASE,
					array(
						__CLASS__,
						'add_action_links'
					)
				);
				add_filter(
					'plugin_row_meta',
					array(
						__CLASS__,
						'add_rekey_link'
					),
					10,
					2
				);
				break;

			
			case 'admin':
				remove_action(
					'admin_menu',
					array(
						__CLASS__,
						'init_menu'
					)
				);
				add_action(
					'admin_menu',
					array(
						'wpSEOde_GUI',
						'init_menu'
					)
				);
				break;

			
			case 'admin-post':
				add_action(
					'admin_post_save_wpseo_changes',
					array(
						'wpSEOde_GUI',
						'save_changes'
					)
				);
				break;

			default:
				break;
		}
	}




			

	

	public static function install( $is_network ) {
		
		if ( $is_network ) {
			
			$ids = self::_get_blog_ids();

			
			foreach ( $ids as $id ) {
				switch_to_blog( $id );
				self::_install_backend();
			}

			
			restore_current_blog();

		} else {
			self::_install_backend();
		}
	}


	

	public static function install_later( $id ) {
		
		if ( ! self::is_network_active() ) {
			return;
		}

		
		switch_to_blog( $id );

		
		self::_install_backend();

		
		restore_current_blog();
	}


	

	private static function _install_backend() {
		
		wpSEOde_Options::init();

		
		delete_transient( 'wpseo' );
	}


	

	public static function uninstall() {
		
		if ( is_network_admin() ) {
			
			$ids = self::_get_blog_ids();

			
			foreach ( $ids as $id ) {
				switch_to_blog( $id );
				self::_uninstall_backend();
			}

			
			restore_current_blog();
		} else {
			self::_uninstall_backend();
		}
	}


	

	public static function uninstall_later( $id ) {
		
		if ( ! self::is_network_active() ) {
			return;
		}

		
		switch_to_blog( $id );

		
		self::_uninstall_backend();

		
		restore_current_blog();
	}


	

	private static function _uninstall_backend() {
		
		global $wpdb;

		
		delete_option( 'wpseo' );

		
		delete_transient( 'wpseo' );

		
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options WHERE ( option_name LIKE (%s) OR option_name LIKE (%s) )",
				'wpseo\\_%',
				'%\\_transient%\\_wpseo%'
			)
		);
	}


	

	private static function _get_blog_ids() {
		
		global $wpdb;

		return $wpdb->get_col( "SELECT blog_id FROM `$wpdb->blogs`" );
	}




			

	

	public static function is_network_active() {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		return is_plugin_active_for_network( WPSEODE_BASE );
	}




			
	
	public static function add_scripts() {
		wp_enqueue_script(
			'wpseo-lib',
			wpSEOde::plugin_url( 'js/lib.min.js' ),
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-tooltip' ),
			wpSEOde::get_plugin_data( 'Version' ),
			true
		);
		if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'edit.php' ) {
			wp_enqueue_script(
				'wpseo-edit',
				wpSEOde::plugin_url( 'js/edit.min.js' ),
				array( 'jquery' ),
				wpSEOde::get_plugin_data( 'Version' ),
				true
			);
		}
	}

	

	public static function add_wplink() {
		
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		
		wp_enqueue_script(
			'wpseo_wplink',
			wpSEOde::plugin_url( 'js/wplink.min.js' ),
			array( 'wplink', 'jquery' ),
			wpSEOde::get_plugin_data( 'Version' ),
			true
		);
	}


	

	public static function init_menu() {
		
		if ( ! current_user_can( 'manage_options' ) or wpSEOde_Feedback::get( 'critical' ) ) {
			return;
		} elseif ( wpSEOde_License::expired() ) {
			add_menu_page(
				'wpSEO',
				'wpSEO',
				'manage_options',
				'wpseode',
				array(
					'wpSEOde_GUI',
					'show_page'
				),
				WP_CONTENT_URL . '/plugins/wpseo/wpseo-icon.png',
				99
			);
			wpSEOde_GUI::init_menu();
		} else {

			

			add_menu_page(
				'wpSEO',
				'wpSEO',
				'manage_options',
				'wpseode',
				array(
					'wpSEOde_GUI',
					'show_page'
				),
				WP_CONTENT_URL . '/plugins/wpseo/wpseo-icon.png',
				99
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

			add_submenu_page(
				'wpseode',
				esc_html__( 'Pagetitle', 'wpseo' ),
				esc_html__( 'Pagetitle', 'wpseo' ),
				'manage_options',
				'wpseode_title_tags',
				array(
					'wpSEOde_GUI',
					'show_page'
				)
			);

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
			);

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
			);

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
			);

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
			);

			add_submenu_page(
				'wpseode',
				esc_html__( 'Settings', 'wpseo' ),
				esc_html__( 'Settings', 'wpseo' ),
				'manage_options',
				'wpseode_settings',
				array(
					'wpSEOde_GUI',
					'show_page'
				)
			);

			add_submenu_page(
				'wpseode',
				esc_html__( 'Licensing', 'wpseo' ),
				esc_html__( 'Licensing', 'wpseo' ),
				'manage_options',
				'wpseode_licensing',
				array(
					'wpSEOde_GUI',
					'show_page'
				)
			);
		}
	}


	

	public static function plugin_url( $path ) {
		return plugins_url(
			$path,
			WPSEODE_FILE
		);
	}


	

	public static function current_page() {
		return ( empty( $GLOBALS['pagenow'] ) ? 'index' : basename( $GLOBALS['pagenow'], '.php' ) );
	}


	

	private static function skip_loading( $page_now, $page_type ) {
		
		if ( empty( $page_now ) ) {
			return false;
		}

		
		switch ( $page_type ) {
			case 'options':
				return ( $page_now == 'admin' && ( empty( $_GET['page'] ) or ( $_GET['page'] != 'wpseode' && $_GET['page'] != 'wpseode_title_tags' && $_GET['page'] != 'wpseode_descriptions' && $_GET['page'] != 'wpseode_indexing' && $_GET['page'] != 'wpseode_social' && $_GET['page'] != 'wpseode_advanced' && $_GET['page'] != 'wpseode_settings' && $_GET['page'] != 'wpseode_licensing' ) ) );
			case 'pages':
				return ! in_array(
					$page_now,
					array(
						'index',
						'profile',
						'user-edit',
						'term',
						'edit-tags',
						'edit',
						'post',
						'post-new',
						'plugins',
						'admin-post',
						'options-general',
						'upload',
						'admin'
					)
				);
		}

		return false;
	}


	

	public static function check_security( $nonce = '_wpseo_nonce' ) {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		
		check_admin_referer( $nonce );
	}


	

	public static function add_action_links( $data ) {
		
		$output = array_filter(
			$data,
			array( 'self', '_add_action_links_helper' )
		);

		
		if ( ! ( ! current_user_can( 'manage_options' ) or wpSEOde_Feedback::get( 'critical' ) ) ) {
			$output = array_merge(
				$output,
				array(
					sprintf(
						'<a href="%s">%s</a>',
						add_query_arg(
							array(
								'page' => 'wpseode'
							),
							admin_url( 'admin.php' )
						),
						__( 'Settings' )
					)
				)
			);
		}

		return $output;
	}

	
	public static function _add_action_links_helper( $f ) {
		return ! strpos( $f, 'plugin-editor.php' );
	}

	

	public static function add_rekey_link( $data, $page ) {
		
		if ( WPSEODE_BASE != $page ) {
			return $data;
		}

		
		if ( is_multisite() && ! is_network_admin() ) {
			return $data;
		}

		
		if ( current_user_can( 'manage_options' ) && wpSEOde_License::valid() ) {
			return array_merge(
				$data,
				array(
					sprintf(
						'<a href="%s">%s</a>',
						add_query_arg(
							array(
								'page' => 'wpseode_licensing'
							),
							network_admin_url( 'admin.php' )
						),
						esc_html__( 'Enter another key', 'wpseo' )
					)
				)
			);
		}

		return $data;
	}


	

	public static function add_key_field() {
		
		if ( ! current_user_can( 'manage_options' ) or ( is_multisite() && ! is_network_admin() ) ) {
			return;
		}

		
		if ( wpSEOde_License::valid() ) {
			return;
		} ?>

        <tr class="plugin-update-tr">
            <td colspan="3" class="plugin-update">
                <div class="update-message">
					<?php echo sprintf( __( 'To activate your license select the "Licensing" tab in the wpSEO menu or click <a href="%s">here</a>. If you don\'t have a license yet please visit <a href="https://wpseo.de/" target="_blank">https://wpseo.de</a> to buy a license.',
						'wpseo' ), admin_url( 'admin.php?page=wpseode_licensing' ) ); ?>
                </div>
            </td>
        </tr>

        <style>
            #wpseo + .plugin-update-tr .update-message {
                margin-top: 12px;
            }

            #wpseo + .plugin-update-tr .update-message::before {
                display: none;
            }

            #wpseo + .plugin-update-tr label {
                line-height: 30px;
                vertical-align: top;
            }

            #wpseo + .plugin-update-tr input[type="text"] {
                width: 300px;
                margin-left: 10px;
            }
        </style>
	<?php }


	

	public static function is_min_wp( $version ) {
		return version_compare(
			$GLOBALS['wp_version'],
			$version . 'alpha',
			'>='
		);
	}


	

	public static function is_min_php( $version ) {
		return version_compare(
			phpversion(),
			$version,
			'>='
		);
	}


	

	public static function redirect_referer( $params = array() ) {
		wp_safe_redirect(
			add_query_arg(
				$params,
				wp_get_referer()
			)
		);

		die();
	}


	

	public static function load_textdomain() {
		add_filter(
			'plugin_locale',
			array(
				'wpSEOde_Base',
				'get_locale'
			)
		);

		
		load_plugin_textdomain(
			'wpseo',
			false,
			dirname( WPSEODE_BASE ) . '/lang'
		);
	}


	

	public static function help_icon( $id, $return = false ) {
		$rewrites = array(
			6    => 'manual/export-und-import-von-einstellungen/',
			15   => 'faq/verifizierung-der-seriennummer-gescheitert/',
			23   => 'manual/seo-monitor-mit-essentiellen-kennzahlen/',
			27   => 'manual/plugin-reset-auf-grundeinstellungen/',
			41   => 'hilfecenter/wpseo-funktionen-dokumentation/',
			50   => 'manual/ausgabemoeglichkeiten-und-die-geschwindigkeit/',
			64   => 'installation-hilfe/mindestanforderungen-der-installation/',
			69   => 'manual/url-weiterleitung-bzw-maskierung-von-links/',
			80   => 'manual/google-suggest-fuer-artikeltitel-und-metadaten/',
			110  => 'manual/permalinks-aus-keywords-im-titel-generieren/',
			114  => 'manual/sperrung-der-indexierung-fuer-aeltere-beitraege/',
			120  => 'manual/ausgabe-und-steuerung-von-canonical-urls/',
			122  => 'manual/nofollow-option-beim-einfuegen-von-links/',
			130  => 'manual/justierung-der-ausgabereihenfolge-von-metatags/',
			133  => 'faq/sind-manuelle-metadaten-sinnvoll/',
			137  => 'manual/eingabe-einer-kurzbeschreibung-fuer-taxonomien/',
			141  => 'manual/spalten-mit-metadaten-in-der-artikeluebersicht/',
			144  => 'manual/kuerzung-der-beschreibung-auf-140-zeichen/',
			146  => 'manual/code-snippets-in-wpseo-anlegen-und-ausgeben/',
			153  => 'manual/eingabe-von-news-keywords-fuer-blog-artikel/',
			157  => 'manual/sperrung-der-indexierung-fuer-aeltere-beitraege/',
			171  => 'manual/manueller-seitentitel-ohne-vervollstaendigung/',
			174  => 'faq/google-autoreninformation-in-wpseo/',
			179  => 'manual/steuerung-der-indexierung-fuer-seiten-und-bereiche/',
			180  => 'faq/pinterest-verifizierung/',
			4827 => 'manual/steuerung-der-flexiblen-platzhalter-im-seitentitel/',
		);

		if ( empty( $id ) or empty( $rewrites[ $id ] ) ) {
			return;
		}

		$icon = sprintf(
			'<a href="https://wpseo.de/%s" class="dashicons dashicons-editor-help help" target="_blank"></a>',
			$rewrites[ $id ]
		);

		if ( $return ) {
			return $icon;
		}

		echo $icon;
	}

	

	public static function info_tooltip( $sText, $return = false ) {
		wp_enqueue_script(
			'wpseo-lib',
			wpSEOde::plugin_url( 'js/lib.min.js' ),
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-tooltip' ),
			wpSEOde::get_plugin_data( 'Version' ),
			true
		);

		$icon = sprintf(
			'<span class="dashicons dashicons-warning help wpseo-tooltip" title="%s"></span>',
			esc_attr( $sText )
		);

		if ( $return ) {
			return $icon;
		}

		echo $icon;
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
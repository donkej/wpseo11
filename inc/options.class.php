<?php



defined( 'ABSPATH' ) or exit;



class wpSEOde_Options {


	

	public static function get( $field = '' ) {
		
		$options = wpSEOde_Cache::get( 'options' );

		
		if ( empty( $options ) ) {
			
			$options = get_option( 'wpseode' );

			
			if ( empty( $options ) ) {
				
				self::init();

				
				$options = get_option( 'wpseode' );
			}

			
			$options = array_merge(
				self::defaults(),
				$options
			);

			
			wpSEOde_Cache::set(
				'options',
				$options
			);
		}

		
		if ( empty( $field ) ) {
			return $options;
		}

		return ( empty( $options[ $field ] ) ? '' : $options[ $field ] );
	}


	

	public static function update( $fields ) {
		
		if ( empty( $fields ) ) {
			return false;
		}

		
		$options = array_merge(
			(array) get_option( 'wpseode' ),
			$fields
		);

		
		update_option(
			'wpseode',
			$options
		);

		
		wpSEOde_Cache::set(
			'options',
			$options
		);
	}


	

	public static function init() {
		
		add_option(
			'wpseode',
			self::defaults()
		);

		
		self::_migrate();

		
		add_site_option(
			strrev( 'galf_llatsni_tluafed' ), 
			time()
		);
	}


	

	public static function reset() {
		self::update(
			self::defaults()
		);
	}


	

	private static function _migrate() {
		
		if ( ! $old = get_option( 'wpseo_options' ) ) {
			return false;
		}

		
		update_user_meta(
			$GLOBALS['current_user']->ID,
			'screen_layout_settings_page_wpseo',
			2
		);

		
		$new = array();

		
		if ( $key = $old[ strrev( 'yek_nretni_oes_pw' ) ] ) { 
			wpSEOde_License::compare( md5( $key ) );
		}

		
		$options = self::defaults();

		
		foreach ( $options as $key => $value ) {
			if ( ! is_array( $value ) && ! empty( $old[ 'wp_seo_' . $key ] ) ) {
				$new[ $key ] = $old[ 'wp_seo_' . $key ];
			} else {
				$new[ $key ] = $value;
			}
		}

		
		if ( self::get( 'title_enable' ) != $old['wp_seo_title_enable'] ) {
			return;
		}

		
		self::update( $new );

		
		delete_option( 'wpseo_options' );

		
		$GLOBALS['wpdb']->query( "OPTIMIZE TABLE `" . $GLOBALS['wpdb']->options . "`" );
	}


	

	static function _migrate2() {
		$version = get_option( 'wpseode_version' );

		if ( $version !== false && version_compare( $version, '4.5.6', '<' ) ) {
			delete_site_transient( strrev( 'esnecil_edoespw' ) );
			update_option( 'wpseode_version', wpSEOde::get_plugin_data( 'Version' ), true );
		}

		
		if ( $version !== false ) {
			return false;
		}

		
		$new = array();
		
		if ( ( $opt = get_option( strrev( 'esnecil_oespw' ) ) ) !== false ) { 
			wpSEOde_License::compare( $opt['key'] );
		}

		
		$options = self::defaults();
		$old     = get_option( 'wpseo' );

		
		foreach ( $options as $key => $value ) {
			if ( isset( $old[ $key ] ) && $old[ $key ] != $value ) {
				$new[ $key ] = $old[ $key ];
			} else {
				$new[ $key ] = $value;
			}
		}

		
		self::update( $new );

		update_option( 'wpseode_version', wpSEOde::get_plugin_data( 'Version' ), true );

		
		$GLOBALS['wpdb']->query( 'OPTIMIZE TABLE `' . $GLOBALS['wpdb']->options . '`' );
	}


	

	public static function defaults() {
		
		$aReturn = array(
			
			'post_title_suggest' => 0,

			'title_manually'      => 1,
			'title_suggest'       => 0,
			'title_manually_only' => 0,

			'desc_manually' => 1,
			'desc_suggest'  => 0,

			'key_manually' => 0,
			'key_suggest'  => 0,
			'key_news'     => 0,

			'noindex_manually' => 0,

			'sitemap'          => 0,
			'sitemap_manually' => 0,

			'canonical_manually' => 0,

			'redirect_manually'   => 0,
			'redirect_attachment' => 0,

			'strip_categorybase'        => 0,
			'redirect_old_categorybase' => 0,

			'ignore_manually' => 0,

			'tax_title_manually'  => 0,
			'tax_manually'        => 0,
			'tax_manually_prio'   => 0,
			'tax_robots_manually' => 0,
			'authorship_manually' => 0,


			
			'title_enable'        => 1,

			'title_channel_home'       => array( 'title', 'blog' ),
			'title_channel_single'     => array( 'title', 'blog' ),
			'title_channel_page'       => array( 'title', 'blog' ),
			'title_channel_posttype'   => array( 'title', 'blog' ),
			'title_channel_attachment' => array( 'title', 'blog' ),
			'title_channel_category'   => array( 'title', 'pager', 'blog' ),
			'title_channel_search'     => array( 'title', 'pager', 'blog' ),
			'title_channel_tagging'    => array( 'title', 'pager', 'blog' ),
			'title_channel_author'     => array( 'title', 'pager', 'blog' ),
			'title_channel_tax'        => array( 'title', 'pager', 'blog' ),
			'title_channel_archive'    => array( 'title', 'pager', 'blog' ),

			'title_desc_home'       => '',
			'title_desc_home_home'  => '',
			'title_desc_single'     => '',
			'title_desc_page'       => '',
			'title_desc_posttype'   => '',
			'title_desc_category'   => '',
			'title_desc_search'     => '',
			'title_desc_archive'    => '',
			'title_desc_tagging'    => '',
			'title_desc_author'     => '',
			'title_desc_tax'        => '',
			'title_desc_attachment' => '',

			'title_separator' => '›',
			'title_cleanup'   => 0,
			'title_case'      => 0,


			
			'desc_enable'     => 1,

			'desc_home'       => 2,
			'desc_single'     => 2,
			'desc_page'       => 2,
			'desc_posttype'   => 2,
			'desc_attachment' => 2,
			'desc_category'   => 2,
			'desc_search'     => 2,
			'desc_archive'    => 2,
			'desc_tagging'    => 2,
			'desc_author'     => 2,
			'desc_tax'        => 2,

			'desc_counter'          => 1,
			'desc_tender'           => 0,
			'desc_default'          => '',
			'desc_default_home'     => '',


			
			'noindex_enable'        => 1,
			'noindex_canonical'     => 1,
			'noindex_age'           => 0,
			'noindex_http'          => 0,
			'noindex_hidden'        => 0,
			'misc_noodp'            => 0,
			'misc_noarchive'        => 0,
			'noindex_nocanonical'   => 0,


			
			'noindex_home'          => 1,
			'noindex_single'        => 1,
			'noindex_page'          => 1,
			'noindex_posttype'      => 1,
			'noindex_attachment'    => 1,
			'noindex_category'      => 4,
			'noindex_search'        => 4,
			'noindex_archive'       => 4,
			'noindex_tagging'       => 4,
			'noindex_author'        => 4,
			'noindex_tax'           => 4,


			
			'speed_nocheck'         => 0,


			
			'misc_lang'             => 0,
			'misc_order'            => array( 'title', 'desc', 'keys' ),
			'misc_monitor'          => 1,
			'misc_monitor_theme'    => 0,
			'misc_wplink'           => 0,
			'misc_nextpage'         => 0,
			'misc_nextpage_rewrite' => 0,

			'misc_slug'     => 0,
			'misc_slug_max' => 3,

			'paged_archive'                   => 1,

			
			'pinterest_domain_verify_tag'     => null,

			
			'open_graph'                      => 0,
			'open_graph_manually'             => 0,
			'open_graph_date_disable'         => 0,
			'open_graph_title_manually'       => 1,
			'open_graph_description_manually' => 1,
			'open_graph_image_manually'       => 1,
			'opengraph_start_title'           => null,
			'opengraph_start_description'     => null,
			'opengraph_start_image'           => null,
			'opengraph_post_title'            => null,
			'opengraph_post_description'      => null,
			'opengraph_post_image'            => null,
			'opengraph_page_title'            => null,
			'opengraph_page_description'      => null,
			'opengraph_page_image'            => null,

			
			'twitter_site_account'            => null,
			'twitter_cards_manually'          => 0,
			'twitter_authorship'              => 0,
			'twitter_authorship_manually'     => 0,
			'twittercard_start_title'         => null,
			'twittercard_start_description'   => null,
			'twittercard_start_image'         => null,

			
			'social_profiles'                 => 0,
			'social_data_type'                => null,
			'social_data_name'                => null,
			'social_profile_youtube'          => null,
			'social_profile_facebook'         => null,
			'social_profile_twitter'          => null,
			'social_profile_linkedin'         => null,
			'social_profile_instagram'        => null,
			'social_profile_pinterest'        => null,

			
			'snippets_data'                   => array(),

			
			'monitor_options'                 => array(
				'seokicks' => '',
			)
		);

		$aPostTypes = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
		foreach ( $aPostTypes as $sName => $oPostType ) {
			$aReturn[ 'title_channel_posttype_' . $sName ] = $aReturn['title_channel_posttype'];
			$aReturn[ 'title_desc_posttype_' . $sName ]    = $aReturn['title_desc_posttype'];
			$aReturn[ 'desc_posttype_' . $sName ]          = $aReturn['desc_posttype'];
			$aReturn[ 'noindex_posttype_' . $sName ]       = $aReturn['noindex_posttype'];
		}

		return $aReturn;
	}

	

	public static function get_option( $arg ) {
		$a = array(
			'twitter'          => false,
			'opengraph'        => false,
			'og_default_image' => ''
		);

		return $a;
	}
}

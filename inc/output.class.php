<?php



defined( 'ABSPATH' ) or exit;



class wpSEOde_Output {


	

	private static $_title;
	private static $_desc;
	private static $_keys;
	private static $_robots;
	private static $_canonical;
	private static $_authorship;
	private static $_page;
	private static $_cpage;
	private static $_output;
	private static $_twitter_author;


	

	public static function prepare_redirect() {
		
		if ( is_feed() or is_trackback() or is_robots() or wpSEOde::is_mobile() ) {
			return;
		}

		
		if ( function_exists( 'bp_is_blog_page' ) && ! bp_is_blog_page() ) {
			return;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( is_singular() ) {

			
			if ( $options['redirect_manually'] && $url = get_post_meta( self::_current_post_id(),
					'_wpseo_edit_redirect', true ) ) {
				wp_redirect( esc_url_raw( $url ), 301 );
				exit;
			}

			
			if ( $options['misc_nextpage'] && self::_paginated_page() ) {
				self::_nextpage_meta();
			}

			
			if ( is_attachment() ) {
				
				if ( $options['redirect_attachment'] ) {
					$url = apply_filters( 'wpseo_redirect_attachment_url',
						wp_get_attachment_url( get_queried_object_id() ), get_queried_object_id() );
					if ( ! empty( $url ) ) {
						wp_redirect( esc_url_raw( $url ), 301 );
						exit;
					}
				}
			}

		} elseif ( is_category() ) {


			
			if ( $options['redirect_manually'] && $url = self::get_tax_data( 'redirect' ) ) {
				wp_redirect( esc_url_raw( $url ), 301 );
				exit;
			}


			
		} elseif ( is_tag() ) {


			
			if ( $options['redirect_manually'] && $url = self::get_tax_data( 'redirect' ) ) {
				wp_redirect( esc_url_raw( $url ), 301 );
				exit;
			}
			
		} elseif ( is_tax() ) {
			
			if ( $options['redirect_manually'] && $url = self::get_tax_data( 'redirect' ) ) {
				wp_redirect( esc_url_raw( $url ), 301 );
				exit;
			}
		}

		
		if ( $options['speed_nocheck'] == 2 ) {
			return;
		}

		
		self::get_output();

		

		
		$borlabsCacheActive = false;

		if ( class_exists( 'Borlabs\Factory' ) && defined( 'BORLABS_CACHE_VERSION' ) ) {
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'fix' . DIRECTORY_SEPARATOR . '1-borlabs-cache-1.php';
		}

		
		if ( $borlabsCacheActive == false ) {
			ob_start( array(
					'wpSEOde_Output',
					'modify_output'
				)
			);
		}
	}

	

	private static function _current_post_id() {
		if ( is_singular() && $postID = get_the_ID() ) {
			return $postID;
		}

		return get_queried_object_id();
	}

	

	private static function _paginated_page() {
		global $post, $numpages;
		$post = get_post( self::_current_post_id() );
		if ( is_object( $post ) ) {
			setup_postdata( $post );
		}

		
		if ( self::$_page === false or ! empty( self::$_page ) ) {
			return self::$_page;
		}

		
		$page = (int) get_query_var( 'page' );

		if ( isset( $numpages ) ) {
			if ( $page > (int) $numpages ) {
				$page = (int) $numpages;
			}
		}
		$page = ( $page ? $page : (int) get_query_var( 'paged' ) );


		return self::$_page = ( $page < 2 ? false : $page );
	}




	

	

	private static function _nextpage_meta() {
		
		if ( ! $page = self::_nextpage_content() ) {
			return;
		}

		
		preg_match_all(
			'/^<!--nextpage(title|desc|keys):\s*(.+?)\s*-->/mu',
			$page,
			$matches,
			PREG_SET_ORDER
		);

		
		if ( empty( $matches ) ) {
			return;
		}

		
		$meta = array();

		
		foreach ( $matches as $value ) {
			$meta[ $value[1] ] = $value[2];
		}

		
		if ( empty( $meta ) ) {
			return;
		}

		
		apply_filters(
			'wpseo_set_meta',
			$meta
		);

		
		if ( wpSEOde_Options::get( 'misc_nextpage_rewrite' ) ) {
			add_filter(
				'the_title',
				array(
					'wpSEOde_Output',
					'rewrite_title'
				),
				10,
				2
			);
		}
	}

	


	

	

	private static function _nextpage_content() {
		
		if ( empty( $GLOBALS['posts'] ) ) {
			return;
		}

		
		if ( ! ( $content = $GLOBALS['posts'][0]->post_content ) ) {
			return;
		}

		
		if ( strripos( $content, '<!--nextpage-->' ) === false ) {
			return;
		}

		
		$pages = explode( '<!--nextpage-->', $content );

		
		$index = intval( (int) self::_paginated_page() - 1 );

		
		if ( empty( $pages[ $index ] ) ) {
			return;
		}

		

		return (string) $pages[ $index ];
	}

	

	private static function get_tax_data( $type ) {
		
		$term = get_queried_object();

		
		if ( is_wp_error( $term ) || empty( $term->term_id ) || empty( $term->taxonomy ) ) {
			return;
		}

		return get_option(
			sprintf(
				'wpseo_%s_%d%s',
				$term->taxonomy,
				$term->term_id,
				( $type != '' ? '_' . $type : '' )
			)
		);
	}

	

	public static function get_output() {
		
		if ( self::$_output === false or ! empty( self::$_output ) ) {
			return self::$_output;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( is_singular() && $options['ignore_manually'] && get_post_meta( self::_current_post_id(),
				'_wpseo_edit_ignore', true ) ) {
			return self::$_output = false;
		}

		
		if ( ! $order = $options['misc_order'] ) {
			$order = array( 'title', 'desc', 'keys' );
		}

		
		remove_theme_support( 'title-tag' );

		
		$meta = '';

		
		foreach ( $order as $item ) {
			switch ( $item ) {
				case 'title':
					$meta .= self::_html_title();
					break;

				case 'desc':
					$meta .= self::_html_description();
					break;

				default:
					$meta .= self::_html_keywords();
					break;
			}
		}

		
		self::$_output = sprintf(
			'%s%s%s%s%s%s%s%s%s%s%s%s',
			self::_html_brand(),
			$meta,
			self::_html_robots(),
			self::_html_canonical(),
			self::_html_prevnext(),
			self::_html_opengraph(),
			self::_html_twittercard(),
			self::_html_authorship(),
			self::_html_snippets(),
			self::_rich_snippets(),
			self::_pinterest_verify(),
			"\n"
		);

		
		wp_reset_query();

		

		return self::$_output;
	}

	

	private static function _html_title() {
		if ( $title = self::_replace_vars( self::_get_title() ) ) {
			return sprintf(
				'%s<title>%s</title>',
				"\n",
				esc_html(
					self::_replace_vars(
						self::_filter_output(
							strip_shortcodes( $title )
						)
					)
				)
			);
		}
	}

	

	private static function _replace_vars( $string ) {

		$string = str_replace( '%wpseo_today%', date_i18n( get_option( 'date_format' ), strtotime( 'today' ) ),
			$string );
		$string = str_replace( '%wpseode_today%', date_i18n( get_option( 'date_format' ), strtotime( 'today' ) ),
			$string );
		$string = str_replace( '%wpseo_today_day%', date_i18n( 'l', strtotime( 'today' ) ), $string );
		$string = str_replace( '%wpseo_today_name%', date_i18n( 'l', strtotime( 'today' ) ), $string );
		$string = str_replace( '%wpseode_today_name%', date_i18n( 'D', strtotime( 'today' ) ), $string );
		$string = str_replace( '%wpseode_today_fullname%', date_i18n( 'l', strtotime( 'today' ) ), $string );


		$string = str_replace( '%wpseo_tomorrow%', date_i18n( get_option( 'date_format' ), strtotime( 'tomorrow' ) ),
			$string );
		$string = str_replace( '%wpseode_tomorrow%', date_i18n( get_option( 'date_format' ), strtotime( 'tomorrow' ) ),
			$string );

		$string = str_replace( '%wpseode_tomorrow_name%', date_i18n( 'D', strtotime( 'tomorrow' ) ), $string );
		$string = str_replace( '%wpseode_tomorrow_fullname%', date_i18n( 'l', strtotime( 'tomorrow' ) ), $string );

		$string = str_replace( '%monthnum%', date( 'm' ), $string );
		$string = str_replace( '%next_monthnum%', date( 'm', strtotime( 'first day of this month +1 month' ) ),
			$string );

		$string = str_replace( '%year%', date( 'Y' ), $string );
		$string = str_replace( '%next_year%', ( date( 'Y' ) + 1 ), $string );

		$string = str_replace( '%wpseo_month%', date( 'm' ), $string );
		$string = str_replace( '%wpseode_month%', date( 'm' ), $string );
		$string = str_replace( '%wpseode_month_name%', date_i18n( 'M' ), $string );
		$string = str_replace( '%wpseode_month_fullname%', date_i18n( 'F' ), $string );
		$string = str_replace( '%wpseo_next_month%', date( 'm', strtotime( 'first day of this month +1 month ' ) ),
			$string );
		$string = str_replace( '%wpseode_next_month%', date( 'm', strtotime( 'first day of this month +1 month ' ) ),
			$string );
		$string = str_replace( '%wpseode_next_month_name%',
			date_i18n( 'M', strtotime( 'first day of this month +1 month ' ) ), $string );
		$string = str_replace( '%wpseode_next_month_fullname%',
			date_i18n( 'F', strtotime( 'first day of this month +1 month ' ) ), $string );

		$string = str_replace( '%wpseo_year%', date( 'Y' ), $string );
		$string = str_replace( '%wpseode_year%', date( 'Y' ), $string );
		$string = str_replace( '%wpseo_next_year%', ( date( 'Y' ) + 1 ), $string );
		$string = str_replace( '%wpseode_next_year%', ( date( 'Y' ) + 1 ), $string );

		$string = str_replace( '%wpseo_next_january%',
			'01/' . date( 'Y', strtotime( 'january' . ( date( 'n' ) > 1 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_february%',
			'02/' . date( 'Y', strtotime( 'february' . ( date( 'n' ) > 2 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_march%',
			'03/' . date( 'Y', strtotime( 'march' . ( date( 'n' ) > 3 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_april%',
			'04/' . date( 'Y', strtotime( 'april' . ( date( 'n' ) > 4 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_may%',
			'05/' . date( 'Y', strtotime( 'may' . ( date( 'n' ) > 5 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_june%',
			'06/' . date( 'Y', strtotime( 'june' . ( date( 'n' ) > 6 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_july%',
			'07/' . date( 'Y', strtotime( 'july' . ( date( 'n' ) > 7 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_august%',
			'08/' . date( 'Y', strtotime( 'august' . ( date( 'n' ) > 8 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_september%',
			'09/' . date( 'Y', strtotime( 'september' . ( date( 'n' ) > 9 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_october%',
			'10/' . date( 'Y', strtotime( 'october' . ( date( 'n' ) > 10 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_november%',
			'11/' . date( 'Y', strtotime( 'november' . ( date( 'n' ) > 11 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseo_next_december%',
			'12/' . date( 'Y', strtotime( 'december' . ( date( 'n' ) > 12 ? ' +1 year' : '' ) ) ), $string );

		$string = str_replace( '%wpseode_next_january%',
			'01/' . date( 'Y', strtotime( 'january' . ( date( 'n' ) > 1 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_february%',
			'02/' . date( 'Y', strtotime( 'february' . ( date( 'n' ) > 2 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_march%',
			'03/' . date( 'Y', strtotime( 'march' . ( date( 'n' ) > 3 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_april%',
			'04/' . date( 'Y', strtotime( 'april' . ( date( 'n' ) > 4 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_may%',
			'05/' . date( 'Y', strtotime( 'may' . ( date( 'n' ) > 5 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_june%',
			'06/' . date( 'Y', strtotime( 'june' . ( date( 'n' ) > 6 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_july%',
			'07/' . date( 'Y', strtotime( 'july' . ( date( 'n' ) > 7 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_august%',
			'08/' . date( 'Y', strtotime( 'august' . ( date( 'n' ) > 8 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_september%',
			'09/' . date( 'Y', strtotime( 'september' . ( date( 'n' ) > 9 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_october%',
			'10/' . date( 'Y', strtotime( 'october' . ( date( 'n' ) > 10 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_november%',
			'11/' . date( 'Y', strtotime( 'november' . ( date( 'n' ) > 11 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_december%',
			'12/' . date( 'Y', strtotime( 'december' . ( date( 'n' ) > 12 ? ' +1 year' : '' ) ) ), $string );

		$string = str_replace( '%wpseode_next_january_name%',
			date_i18n( 'M Y', strtotime( 'january' . ( date( 'n' ) > 1 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_february_name%',
			date_i18n( 'M Y', strtotime( 'february' . ( date( 'n' ) > 2 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_march_name%',
			date_i18n( 'M Y', strtotime( 'march' . ( date( 'n' ) > 3 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_april_name%',
			date_i18n( 'M Y', strtotime( 'april' . ( date( 'n' ) > 4 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_may_name%',
			date_i18n( 'M Y', strtotime( 'may' . ( date( 'n' ) > 5 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_june_name%',
			date_i18n( 'M Y', strtotime( 'june' . ( date( 'n' ) > 6 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_july_name%',
			date_i18n( 'M Y', strtotime( 'july' . ( date( 'n' ) > 7 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_august_name%',
			date_i18n( 'M Y', strtotime( 'august' . ( date( 'n' ) > 8 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_september_name%',
			date_i18n( 'M Y', strtotime( 'september' . ( date( 'n' ) > 9 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_october_name%',
			date_i18n( 'M Y', strtotime( 'october' . ( date( 'n' ) > 10 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_november_name%',
			date_i18n( 'M Y', strtotime( 'november' . ( date( 'n' ) > 11 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_december_name%',
			date_i18n( 'M Y', strtotime( 'december' . ( date( 'n' ) > 12 ? ' +1 year' : '' ) ) ), $string );

		$string = str_replace( '%wpseode_next_january_fullname%',
			date_i18n( 'F Y', strtotime( 'january' . ( date( 'n' ) > 1 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_february_fullname%',
			date_i18n( 'F Y', strtotime( 'february' . ( date( 'n' ) > 2 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_march_fullname%',
			date_i18n( 'F Y', strtotime( 'march' . ( date( 'n' ) > 3 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_april_fullname%',
			date_i18n( 'F Y', strtotime( 'april' . ( date( 'n' ) > 4 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_may_fullname%',
			date_i18n( 'F Y', strtotime( 'may' . ( date( 'n' ) > 5 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_june_fullname%',
			date_i18n( 'F Y', strtotime( 'june' . ( date( 'n' ) > 6 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_july_fullname%',
			date_i18n( 'F Y', strtotime( 'july' . ( date( 'n' ) > 7 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_august_fullname%',
			date_i18n( 'F Y', strtotime( 'august' . ( date( 'n' ) > 8 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_september_fullname%',
			date_i18n( 'F Y', strtotime( 'september' . ( date( 'n' ) > 9 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_october_fullname%',
			date_i18n( 'F Y', strtotime( 'october' . ( date( 'n' ) > 10 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_november_fullname%',
			date_i18n( 'F Y', strtotime( 'november' . ( date( 'n' ) > 11 ? ' +1 year' : '' ) ) ), $string );
		$string = str_replace( '%wpseode_next_december_fullname%',
			date_i18n( 'F Y', strtotime( 'december' . ( date( 'n' ) > 12 ? ' +1 year' : '' ) ) ), $string );

		return $string;
	}

	

	private static function _get_title() {
		
		if ( ! empty( self::$_title ) ) {
			return self::$_title;
		}

		
		if ( is_404() ) {
			return self::$_title = self::_prepare_title( __( 'Page not found' ) );
		}

		
		remove_all_filters( 'wp_title' );

		
		$options = wpSEOde_Options::get();

		
		$title = self::_get_title_attr( 'title' );

		
		if ( ( $meta = wpSEOde_Cache::get( 'meta' ) ) && ! empty( $meta['title'] ) ) {
			return self::$_title = (string) apply_filters(
				'wpseo_set_title',
				$meta['title']
			);
		}

		
		if ( is_singular() or is_home() ) {
			if ( $options['title_manually'] && $custom = get_post_meta( self::_current_post_id(), '_wpseo_edit_title',
					true ) ) {
				if ( $options['title_manually_only'] ) {
					return self::$_title = (string) apply_filters(
						'wpseo_set_title',
						$custom
					);
				} else {
					$title = $custom;
				}
			}
		}

		
		if ( ! $options['title_enable'] ) {
			return;
		}

		
		if ( is_front_page() or is_home() ) {
			if ( is_paged() ) {
				$output = self::_replace_title_attr(
					array(
						'blog'  => array( 'attr_blog' ),
						'title' => (string) $title,
						'pager' => array( 'attr_paged' ),
						'area'  => (string) $options['title_desc_archive']
					),
					$options['title_channel_archive']
				);
			} else {
				$output = self::_replace_title_attr(
					array(
						'blog'  => array( 'attr_blog' ),
						'title' => (string) $title,
						'area'  => (string) ( is_front_page() ? $options['title_desc_home'] : $options['title_desc_home_home'] )
					),
					$options['title_channel_home']
				);
			}

			
		} elseif ( is_singular() ) {
			
			if ( is_attachment() ) {
				$output = self::_replace_title_attr(
					array(
						'blog'   => array( 'attr_blog' ),
						'title'  => (string) $title,
						'parent' => array( 'attr_parent' ),
						'author' => array( 'attr_author' ),
						'area'   => (string) $options['title_desc_attachment']
					),
					$options['title_channel_attachment']
				);

				
			} elseif ( self::_comment_page() ) {
				$output = self::_replace_title_attr(
					array(
						'blog'  => array( 'attr_blog' ),
						'title' => (string) $title,
						'pager' => array( 'attr_cpage' ),
						'area'  => (string) $options['title_desc_archive']
					),
					$options['title_channel_archive']
				);

				
			} elseif ( self::_paginated_page() ) {
				$output = self::_replace_title_attr(
					array(
						'blog'  => array( 'attr_blog' ),
						'title' => (string) $title,
						'pager' => array( 'attr_paginated' ),
						'area'  => (string) $options['title_desc_archive']
					),
					$options['title_channel_archive']
				);

				
			} elseif ( self::_is_post_type() ) {
				$output = self::_replace_title_attr(
					array(
						'blog'     => array( 'attr_blog' ),
						'title'    => (string) $title,
						'label'    => array( 'attr_label' ),
						'tag'      => array( 'attr_tags' ),
						'category' => array( 'attr_cats' ),
						'tax'      => array( 'attr_tax' ),
						'author'   => array( 'attr_author' ),
						'area'     => isset( $options[ 'title_desc_posttype_' . get_post_type() ] ) ? (string) $options[ 'title_desc_posttype_' . get_post_type() ] : (string) $options['title_desc_posttype']
					),
					isset( $options[ 'title_channel_posttype_' . get_post_type() ] ) ? $options[ 'title_channel_posttype_' . get_post_type() ] : $options['title_channel_posttype']
				);

				
			} elseif ( is_page() ) {
				$output = self::_replace_title_attr(
					array(
						'blog'   => array( 'attr_blog' ),
						'title'  => (string) $title,
						'author' => array( 'attr_author' ),
						'parent' => array( 'attr_parent' ),
						'area'   => (string) $options['title_desc_page']
					),
					$options['title_channel_page']
				);

				
			} else {
				$output = self::_replace_title_attr(
					array(
						'blog'     => array( 'attr_blog' ),
						'title'    => (string) $title,
						'tag'      => array( 'attr_tags' ),
						'category' => array( 'attr_cats' ),
						'author'   => array( 'attr_author' ),
						'area'     => (string) $options['title_desc_single']
					),
					$options['title_channel_single']
				);
			}

			
		} elseif ( is_paged() && $options['paged_archive'] ) {
			$output = self::_replace_title_attr(
				array(
					'blog'  => array( 'attr_blog' ),
					'title' => (string) $title,
					'pager' => array( 'attr_paged' ),
					'area'  => (string) $options['title_desc_archive']
				),
				$options['title_channel_archive']
			);

			
		} elseif ( is_search() ) {
			$output = self::_replace_title_attr(
				array(
					'blog'  => array( 'attr_blog' ),
					'title' => (string) $title,
					'pager' => array( 'attr_paged' ),
					'area'  => (string) $options['title_desc_search']
				),
				$options['title_channel_search']
			);

			
		} elseif ( is_category() ) {
			$output = self::_replace_title_attr(
				array(
					'blog'        => array( 'attr_blog' ),
					'title'       => (string) $title,
					'wpseo_title' => array( 'tax_title' ),
					'desc'        => array( 'attr_term' ),
					'short'       => array( 'tax_short' ),
					'pager'       => array( 'attr_paged' ),
					'area'        => (string) $options['title_desc_category']
				),
				$options['title_channel_category']
			);

			
		} elseif ( is_tag() ) {
			$output = self::_replace_title_attr(
				array(
					'blog'        => array( 'attr_blog' ),
					'title'       => (string) $title,
					'desc'        => array( 'attr_term' ),
					'wpseo_title' => array( 'tax_title' ),
					'short'       => array( 'tax_short' ),
					'pager'       => array( 'attr_paged' ),
					'area'        => (string) $options['title_desc_tagging']
				),
				$options['title_channel_tagging']
			);

			
		} elseif ( is_author() ) {
			$output = self::_replace_title_attr(
				array(
					'blog'  => array( 'attr_blog' ),
					'title' => (string) $title,
					'desc'  => array( 'attr_bio' ),
					'pager' => array( 'attr_paged' ),
					'area'  => (string) $options['title_desc_author']
				),
				$options['title_channel_author']
			);

			
		} elseif ( is_tax() ) {
			$output = self::_replace_title_attr(
				array(
					'blog'        => array( 'attr_blog' ),
					'title'       => (string) single_term_title( '', false ),
					'desc'        => array( 'attr_term' ),
					'wpseo_title' => array( 'tax_title' ),
					'short'       => array( 'tax_short' ),
					'pager'       => array( 'attr_paged' ),
					'area'        => (string) $options['title_desc_tax']
				),
				$options['title_channel_tax']
			);

			
		} else {
			$output = self::_replace_title_attr(
				array(
					'blog'  => array( 'attr_blog' ),
					'title' => (string) $title,
					'pager' => array( 'attr_paged' ),
					'area'  => (string) $options['title_desc_archive']
				),
				$options['title_channel_archive']
			);
		}

		
		if ( empty( $output ) ) {
			$oCurrent = self::_current_post();
			if ( ! is_wp_error( $oCurrent ) && $oCurrent !== false ) {
				$output = $oCurrent->post_title;
			}
		}

		

		if ( class_exists( 'WooCommerce' ) ) {
			if ( function_exists( 'is_shop' ) && is_shop() ) {
				$title = (string) get_post_meta( wc_get_page_id( 'shop' ), '_wpseo_edit_title', true );

				$output = self::_replace_title_attr(
					array(
						'blog'   => array( 'attr_blog' ),
						'title'  => (string) $title,
						'author' => array( 'attr_author' ),
						'parent' => array( 'attr_parent' ),
						'area'   => (string) $options['title_desc_page']
					),
					$options['title_channel_page']
				);

			}
		}


		
		$output = (string) apply_filters(
			'wpseo_set_title',
			self::_prepare_title( $output )
		);

		return self::$_title = $output;
	}

	

	private static function _prepare_title( $input ) {
		
		if ( empty( $input ) || is_wp_error( $input ) ) {
			return;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( is_array( $input ) ) {
			
			if ( $sep = $options['title_separator'] ) {
				$sep = sprintf( ' %s ', $sep );
			} else {
				$sep = ' ';
			}

			
			$input = implode(
				$sep,
				array_values(
					array_filter( $input )
				)
			);
		}

		
		$output = (string) $input;

		
		if ( $options['title_cleanup'] ) {
			
			$output = wp_strip_all_tags(
				$output,
				true
			);

			
			$output = preg_replace(
				'/\s+/u',
				' ',
				$output
			);
		}

		
		switch ( $options['title_case'] ) {
			case 1:
				$output = ( function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $output ) : strtoupper( $output ) );
				break;

			case 2:
				$output = ( function_exists( 'mb_strtolower' ) ? mb_strtolower( $output ) : strtolower( $output ) );
				break;

			default:
				break;
		}

		return $output;
	}

	

	private static function _get_title_attr( $type ) {
		
		$post = self::_current_post();

		
		switch ( $type ) {
			case 'title':
				return ( $title = trim( wp_title( '', false, 'right' ) ) ) ? $title : get_bloginfo( 'description' );

			case 'blog':
				return get_bloginfo( 'name' );

			case 'tags':
				$sTags = get_the_tag_list( '', ', ' );
				if ( is_wp_error( $sTags ) ) {
					return '';
				}

				return strip_tags( $sTags );

			case 'cats':
				return strip_tags( get_the_category_list( ', ' ) );

			case 'term':
				return trim( strip_tags( term_description() ) );

			case 'author':
				return sprintf(
					'%s %s',
					self::_translated_string_by(),
					get_userdata( $post->post_author )->display_name
				);

			case 'bio':
				return get_userdata( $post->post_author )->description;

			case 'paged':
				return self::_apply_filter_set_paged_title(
					self::_translated_string_page(),
					get_query_var( 'paged' )
				);

			case 'cpage':
				return self::_apply_filter_set_paged_title(
					self::_translated_string_page(),
					self::_comment_page()
				);

			case 'paginated':
				return self::_apply_filter_set_paged_title(
					self::_translated_string_page(),
					self::_paginated_page()
				);

			case 'parent':
				return ( $post->post_parent ? get_the_title( $post->post_parent ) : '' );

			case 'tax':
				return self::_get_tax_value();

			case 'label':
				return get_post_type_object( get_post_type() )->label;

			default:
				return;
		}
	}


	


	

	

	private static function _current_post() {
		if ( is_singular() ) {
			return get_queried_object();
		} elseif ( ! empty( $GLOBALS['wp_query']->posts[0] ) ) {
			return $GLOBALS['wp_query']->posts[0];
		}

		return false;
	}

	

	private static function _translated_string_by() {
		return ( wpSEOde_Base::get_locale() === 'de_DE' ? 'Von' : __( 'By' ) );
	}

	

	private static function _apply_filter_set_paged_title( $s, $d ) {
		if ( empty( $s ) or empty( $d ) ) {
			return '';
		}

		return wp_filter_nohtml_kses(
			apply_filters(
				'wpseo_set_paged_title',
				sprintf(
					'%s %d',
					(string) $s,
					(int) $d
				)
			)
		);
	}

	

	private static function _translated_string_page() {
		return ( wpSEOde_Base::get_locale() === 'de_DE' ? 'Seite' : __( 'Page' ) );
	}

	

	public static function _comment_page() {
		
		if ( ! get_option( 'comments_per_page' ) ) {
			return self::$_cpage = false;
		}

		
		if ( self::$_cpage === false or ! empty( self::$_cpage ) ) {
			return self::$_cpage;
		}

		
		global $wp_rewrite;

		
		if ( $wp_rewrite->using_permalinks() ) {
			
			if ( empty( $_SERVER['REQUEST_URI'] ) or strpos( $_SERVER['REQUEST_URI'], '/comment-page-' ) === false ) {
				return self::$_cpage = false;
			}

			
			preg_match(
				'/comment-page-(\d+)$/i',
				untrailingslashit(
					parse_url(
						$_SERVER['REQUEST_URI'],
						PHP_URL_PATH
					)
				),
				$matches
			);

			return self::$_cpage = ( empty( $matches[1] ) ? false : (int) $matches[1] );

			
		} else {
			return self::$_cpage = (int) get_query_var( 'cpage' );
		}
	}

	

	

	private static function _get_tax_value() {
		
		$items = (array) get_object_taxonomies(
			get_post_type()
		);

		
		if ( empty( $items ) ) {
			return;
		}

		
		$items = array_filter(
			$items,
			array( 'self', '_get_tax_value_helper' )
		);

		
		if ( empty( $items ) or ! is_array( $items ) ) {
			return;
		}

		
		$items = array_values( $items );

		
		$tax = $items[0];

		
		$output = array();

		
		if ( $terms = wp_get_object_terms( get_queried_object_id(), $tax ) ) {
			foreach ( $terms as $term ) {
				$output[] = $term->name;
			}

			if ( ! empty( $output ) ) {
				return implode( ', ', $output );
			}
		}

		return;
	}

	


	

	

	private static function _replace_title_attr( $data, $options ) {
		
		if ( empty( $options ) ) {
			return '';
		}

		
		$output = array();

		
		foreach ( $options as $option ) {
			
			if ( empty( $data[ $option ] ) ) {
				continue;
			}

			$output[] = ( is_array( $data[ $option ] ) ? self::_get_title_proxy( $data[ $option ][0] ) : $data[ $option ] );
		}

		return $output;
	}

	


	

	

	private static function _get_title_proxy( $type ) {
		switch ( $type ) {
			case 'attr_blog':
				return self::_get_title_attr( 'blog' );

			case 'attr_paged':
				return self::_get_title_attr( 'paged' );

			case 'attr_cpage':
				return self::_get_title_attr( 'cpage' );

			case 'attr_paginated':
				return self::_get_title_attr( 'paginated' );

			case 'attr_label':
				return self::_get_title_attr( 'label' );

			case 'attr_tags':
				return self::_get_title_attr( 'tags' );

			case 'attr_cats':
				return self::_get_title_attr( 'cats' );

			case 'attr_tax':
				return self::_get_title_attr( 'tax' );

			case 'attr_author':
				return self::_get_title_attr( 'author' );

			case 'attr_parent':
				return self::_get_title_attr( 'parent' );

			case 'attr_term':
				return self::_get_title_attr( 'term' );

			case 'attr_bio':
				return self::_get_title_attr( 'bio' );

			case 'tax_short':
				return self::_get_tax_short();

			case 'tax_title':
				return self::_get_tax_title();

			default:
				return;
		}
	}

	


	

	

	private static function _get_tax_short() {
		
		$term = get_queried_object();

		
		if ( is_wp_error( $term ) || empty( $term->term_id ) || empty( $term->taxonomy ) ) {
			return;
		}

		return get_option(
			sprintf(
				'wpseo_%s_%d',
				$term->taxonomy,
				$term->term_id
			)
		);
	}

	

	private static function _get_tax_title() {
		
		$term = get_queried_object();

		
		if ( is_wp_error( $term ) || empty( $term->term_id ) || empty( $term->taxonomy ) ) {
			return;
		}

		return get_option(
			sprintf(
				'wpseo_%s_%d_title',
				$term->taxonomy,
				$term->term_id
			)
		);
	}

	

	private static function _is_post_type() {
		return ! in_array(
			get_post_type(),
			array( 'post', 'page', 'attachment' )
		);
	}

	


	

	

	private static function _filter_output( $value ) {
		
		if ( function_exists( 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			return qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage( $value );
		}

		

		return (string) apply_filters(
			'wpseo_filter_output',
			$value
		);
	}

	


	

	

	private static function _html_description() {
		if ( $description = self::_get_description() ) {
			return sprintf(
				'%s<meta name="description" content="%s" />',
				"\n",
				esc_attr(
					self::_replace_vars(
						self::_filter_output(
							strip_shortcodes( $description )
						)
					)
				)
			);
		}
	}

	


	

	

	private static function _get_description() {
		
		if ( is_404() ) {
			return;
		}

		
		if ( ! empty( self::$_desc ) ) {
			return self::$_desc;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( ( $meta = wpSEOde_Cache::get( 'meta' ) ) && ! empty( $meta['desc'] ) ) {
			return self::$_desc = (string) apply_filters(
				'wpseo_set_desc',
				$meta['desc']
			);
		}

		
		if ( is_singular() or is_home() ) {
			if ( $options['desc_manually'] && $custom = get_post_meta( self::_current_post_id(),
					'_wpseo_edit_description', true ) ) {
				return self::$_desc = (string) apply_filters(
					'wpseo_set_desc',
					$custom
				);
			}
		}

		
		if ( is_singular() && post_password_required() ) {
			return;
		}

		
		if ( ! is_paged() ) {
			
			if ( is_front_page() ) {
				if ( ! empty( $options['desc_default'] ) ) {
					return self::$_desc = (string) apply_filters(
						'wpseo_set_desc',
						$options['desc_default']
					);
				}

				
			} elseif ( is_home() ) {
				if ( ! empty( $options['desc_default_home'] ) ) {
					return self::$_desc = (string) apply_filters(
						'wpseo_set_desc',
						$options['desc_default_home']
					);
				}
			} elseif ( ( is_tax() || is_tag() || is_category() ) && ( $options['tax_manually_prio'] == '1' && ( $sShort = self::_get_tax_short() ) !== false && $sShort !== '' ) ) {
				return self::$_desc = (string) apply_filters(
					'wpseo_set_desc',
					$sShort
				);
			}
		}

		
		if ( ! $options['desc_enable'] ) {
			return;
		}

		
		if ( ! $post = self::_current_post() ) {
			return;
		}

		$output = '';

		
		if ( is_front_page() or is_home() ) {
			if ( is_paged() ) {
				switch ( $options['desc_archive'] ) {
					case 0:
						return;

					case 2:
						$output = self::_get_post_titles();
						break;
				}
			} else {
				switch ( $options['desc_home'] ) {
					case 0:
						return;

					case 2:
						$output = self::_get_post_titles();
						break;
				}
			}

			
		} elseif ( is_singular() ) {
			
			if ( is_attachment() ) {
				switch ( $options['desc_attachment'] ) {
					case 0:
						return;

					case 1:
						$output = $post->post_title;
						break;

					case 2:
						$output = $post->post_title;
						break;

					case 3:
						$oParentPost = get_post( $post->post_parent );
						if ( ! is_wp_error( $oParentPost ) && $oParentPost !== false ) {
							$output = $oParentPost->post_title;
						}
						break;

					case 4:
						$oParentPost = get_post( $post->post_parent );
						if ( ! is_wp_error( $oParentPost ) && $oParentPost !== false ) {
							$output = $oParentPost->post_content;
						}
						break;
				}

				
			} elseif ( self::_comment_page() ) {
				switch ( $options['desc_archive'] ) {
					case 0:
						return;

					case 2:
						$output = self::_get_post_titles();
						break;
				}

				
			} elseif ( self::_is_post_type() ) {
				switch ( isset( $options[ 'desc_posttype_' . get_post_type() ] ) ? $options[ 'desc_posttype_' . get_post_type() ] : $options['desc_posttype'] ) {
					case 0:
						return;

					case 1:
						$output = $post->post_title;
						break;

					case 3:
						$output = ( empty( $post->post_excerpt ) ? '' : $post->post_excerpt );
						break;
				}

				
			} elseif ( is_page() ) {
				switch ( $options['desc_page'] ) {
					case 0:
						return;

					case 1:
						$output = $post->post_title;
						break;
				}

				
			} else {
				switch ( $options['desc_single'] ) {
					case 0:
						return;

					case 1:
						$output = $post->post_title;
						break;

					case 3:
						$output = ( empty( $post->post_excerpt ) ? '' : $post->post_excerpt );
						break;
				}
			}

			
		} elseif ( is_paged() && $options['paged_archive'] ) {
			switch ( $options['desc_archive'] ) {
				case 0:
					return;

				case 2:
					$output = self::_get_post_titles();
					break;
			}

			
		} elseif ( is_search() ) {
			switch ( $options['desc_search'] ) {
				case 0:
					return;

				case 2:
					$output = self::_get_post_titles();
					break;
			}

			
		} elseif ( is_category() ) {
			switch ( $options['desc_category'] ) {
				case 0:
					return;

				case 1:
					$output = term_description();
					break;

				case 2:
					$output = self::_get_post_titles();
					break;

				case 3:
					$output = self::_get_tax_short();
					break;
			}

			
		} elseif ( is_tag() ) {
			switch ( $options['desc_tagging'] ) {
				case 0:
					return;

				case 2:
					$output = self::_get_post_titles();
					break;

				case 3:
					$output = term_description();
					break;

				case 4:
					$output = self::_get_tax_short();
					break;
			}

			
		} elseif ( is_author() ) {
			switch ( $options['desc_author'] ) {
				case 0:
					return;

				case 2:
					$output = self::_get_post_titles();
					break;

				case 3:
					$output = get_userdata( $post->post_author )->description;
					break;
			}

			
		} elseif ( is_tax() ) {
			switch ( $options['desc_tax'] ) {
				case 0:
					return;

				case 2:
					$output = self::_get_post_titles();
					break;

				case 3:
					$output = term_description();
					break;

				case 4:
					$output = self::_get_tax_short();
					break;
			}

			
		} else {
			switch ( $options['desc_archive'] ) {
				case 0:
					return;

				case 2:
					$output = self::_get_post_titles();
					break;
			}
		}

		
		if ( empty( $output ) ) {
			$output = ( self::_paginated_page() ? self::_nextpage_content() : $post->post_content );
		}


		

		if ( class_exists( 'WooCommerce' ) ) {
			if ( function_exists( 'is_shop' ) && is_shop() ) {
				$output = (string) get_post_meta( wc_get_page_id( 'shop' ), '_wpseo_edit_description', true );

			}
		}

		
		$output = (string) apply_filters(
			'wpseo_set_desc',
			self::_prepare_description( $output )
		);

		return self::$_desc = $output;
	}

	

	

	private static function _get_post_titles() {
		
		if ( ! $posts = $GLOBALS['wp_query']->posts ) {
			return;
		}

		
		$data = array();

		
		foreach ( $posts as $v ) {
			$data[] = $v->post_title;
		}

		return $data;
	}

	

	public static function _prepare_description( $input ) {
		
		if ( empty( $input ) || is_wp_error( $input ) ) {
			return;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( is_array( $input ) ) {
			$input = implode( '. ', $input );

			if ( substr( $input, - 1 ) !== '.' ) {
				$input .= '.';
			}
		}

		
		$output = $input;

		
		if ( strpos( $output, '[wpseo]' ) !== false ) {
			$output = preg_replace(
				'#\[wpseo\](.+?)\[/wpseo\]#s',
				'$1',
				$output
			);
		}

		
		if ( apply_filters( 'wpseo_strip_shortcodes', true ) ) {
			$output = strip_shortcodes( $output );
		}
		
		if ( class_exists( 'WPBMap' ) && method_exists( 'WPBMap', 'addAllMappedShortcodes' ) ) {
			WPBMap::addAllMappedShortcodes();
		}
		$output = do_shortcode( $output );

		
		$output = wp_strip_all_tags( $output, true );

		
		if ( strpos( $output, 'http://' ) !== false or strpos( $output, 'https://' ) !== false ) {
			$output = preg_replace(
				'#(?i)\b((?:https?://|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»????]))#u',
				'',
				$output
			);
		}

		
		$output = trim( $output );

		
		$length = ( $options['desc_counter'] ? 140 : (int) apply_filters( 'wpseo_set_desc_chars', 280 ) );

		
		if ( function_exists( 'mb_strlen' ) && mb_strlen( $input ) > $length ) {
			$output = trim(
				preg_replace(
					'/[^ ]*$/',
					'',
					mb_substr(
						$output,
						0,
						$length
					)
				)
			);

			
			if ( mb_strlen( $output ) <= $length ) {
				$output .= ' ...';
			}
		} else {
			$output .= ' ';
		}

		
		if ( $options['desc_tender'] ) {
			preg_match(
				'/^(.+[\.\?\!])\s/',
				$output,
				$matches
			);

			if ( ! empty( $matches[1] ) ) {
				$output = $matches[1];
			}
		}

		
		$output = trim( $output );

		return $output;
	}

	

	private static function _html_keywords() {
		if ( $keywords = self::_get_keywords() ) {
			return sprintf(
				'%s<meta name="%s" content="%s" />',
				"\n",
				( wpSEOde_Options::get( 'key_news' ) ? 'news_keywords' : 'keywords' ),
				esc_attr(
					self::_replace_vars(
						self::_filter_output( $keywords )
					)
				)
			);
		}
	}

	

	private static function _get_keywords() {
		
		if ( is_404() ) {
			return;
		}

		
		if ( ! empty( self::$_keys ) ) {
			return self::$_keys;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( ( $meta = wpSEOde_Cache::get( 'meta' ) ) && ! empty( $meta['keys'] ) ) {
			return self::$_keys = $meta['keys'];
		}

		
		if ( is_singular() or is_home() ) {
			if ( $options['key_manually'] && $keywords = get_post_meta( self::_current_post_id(),
					'_wpseo_edit_keywords', true ) ) {
				return self::$_keys = $keywords;
			}
		}

		
		$output = (string) apply_filters(
			'wpseo_set_keys',
			''
		);

		return self::$_keys = $output;
	}

	

	private static function _html_brand() {
		if ( $brand = self::_get_brand() ) {
			return sprintf(
				'%s<!-- %s -->',
				"\n",
				esc_html( $brand )
			);
		}
	}

	

	private static function _get_brand() {
		
		$data = get_site_option( 'wpseode_license' );

		
		if ( ! empty( $data['by'] ) && $data['by'] == md5( - 1 ) ) {
			return;
		}

		
		if ( strpos( get_locale(), 'de_' ) !== false ) {
			return strrev( 'ed.oespw//:sptth / OESpw nov treimitpo-OES edruw etieS eseiD' ); 
		} else {
			return strrev( 'ed.oespw//:sptth / OESpw yb dezimitpo OES si etis sihT' ); 
		}
	}

	

	private static function _html_robots() {
		if ( $robots = self::_get_robots() ) {
			return sprintf(
				'%s<meta name="robots" content="%s" />',
				"\n",
				esc_attr( $robots )
			);
		}
	}

	

	private static function _get_robots() {
		
		if ( ! empty( self::$_robots ) ) {
			return self::$_robots;
		}

		
		if ( is_404() ) {
			return self::$_robots = 'noindex, follow';
		}

		
		if ( is_singular() && post_password_required() ) {
			return self::$_robots = 'noindex, follow';
		}

		
		if ( ( $meta = wpSEOde_Cache::get( 'meta' ) ) && ! empty( $meta['robots'] ) ) {
			return self::$_robots = $meta['robots'];
		}

		
		$output = array();
		$index  = 0;
		$type   = '';

		
		$types = array(
			1 => 'index, follow',
			2 => 'index, nofollow',
			6 => 'index',
			4 => 'noindex, follow',
			5 => 'noindex, nofollow',
			3 => 'noindex'
		);

		
		$options = wpSEOde_Options::get();

		
		if ( is_singular() ) {
			if ( $options['noindex_manually'] && $noindex = get_post_meta( self::_current_post_id(),
					'_wpseo_edit_robots', true ) ) {
				$index = $noindex;

				
			} elseif ( $options['noindex_age'] && is_single() ) {
				$months = (int) apply_filters( 'wpseo_set_noindex_age', 6 );

				if ( abs( current_time( 'timestamp' ) ) - get_the_time( 'U' ) > $months * 30 * 86400 ) {
					$index = 4;
				}
			}
		} elseif ( is_category() or is_tag() or is_tax() ) {
			if ( $options['tax_robots_manually'] && $robots = wpSEOde_Tax::get_meta_data( get_queried_object(),
					'robots' ) ) {
				$index = $robots;
			}
		}

		
		if ( $options['noindex_enable'] && ! $index ) {
			
			if ( is_front_page() or is_home() ) {
				if ( is_paged() ) {
					$type = 'archive';
				} else {
					$type = 'home';
				}

				
			} elseif ( is_singular() ) {
				if ( is_attachment() ) {
					$type = 'attachment';
				} elseif ( self::_comment_page() ) {
					$type = 'archive';
				} elseif ( self::_is_post_type() ) {
					$type = 'posttype_' . get_post_type();
				} elseif ( is_page() ) {
					$type = 'page';
				} else {
					$type = 'single';
				}

				
			} elseif ( is_paged() && $options['paged_archive'] ) {
				$type = 'archive';

				
			} elseif ( is_search() ) {
				$type = 'search';

				
			} elseif ( is_category() ) {
				$type = 'category';

				
			} elseif ( is_tag() ) {
				$type = 'tagging';

				
			} elseif ( is_author() ) {
				$type = 'author';

				
			} elseif ( is_tax() ) {
				$type = 'tax';

				
			} else {
				$type = 'archive';
			}

			if ( ! isset( $options[ 'noindex_' . $type ] ) && substr( $type, 0, 9 ) == 'posttype_' ) {
				$options[ 'noindex_' . $type ] = $options['noindex_posttype'];
			}
			
			if ( ! empty( $type ) && ! empty( $options[ 'noindex_' . $type ] ) ) {
				$index = $options[ 'noindex_' . $type ];
			}
		}

		
		if ( ! empty( $index ) ) {
			$output['noindex'] = $types[ $index ];

			
			if ( $options['noindex_hidden'] && in_array( $index, array( 1, 6 ) ) ) {
				unset( $output['noindex'] );
			}
		}

		
		if ( $options['misc_noodp'] ) {
			$output['noodp'] = 'noodp';
		}

		
		if ( $options['misc_noarchive'] ) {
			$output['noarchive'] = 'noarchive';
		}

		
		$output = (string) apply_filters(
			'wpseo_set_robots',
			implode( ', ', array_values( $output ) )
		);

		return self::$_robots = $output;
	}

	

	private static function _html_canonical() {
		if ( $canonical = self::_get_canonical() ) {
			return sprintf(
				'%s<link rel="canonical" href="%s" />',
				"\n",
				esc_url(
					$canonical,
					array(
						'http',
						'https'
					)
				)
			);
		}
	}

	

	private static function _get_canonical() {
		
		if ( is_404() ) {
			return;
		}

		
		if ( ! empty( self::$_canonical ) ) {
			return self::$_canonical;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( $options['noindex_nocanonical'] && ! empty( self::$_robots ) && strpos( self::$_robots,
				'noindex' ) !== false ) {
			return;
		}

		
		if ( ( $meta = wpSEOde_Cache::get( 'meta' ) ) && ! empty( $meta['canonical'] ) ) {
			return self::_prepare_canonical( $meta['canonical'] );
		}

		
		if ( is_singular() ) {
			if ( $options['canonical_manually'] && $canonical = get_post_meta( self::_current_post_id(),
					'_wpseo_edit_canonical', true ) ) {
				return self::_prepare_canonical( $canonical );
			}
		} elseif ( is_category() ) {

			if ( $options['canonical_manually'] && $canonical = self::get_tax_data( 'canonical' ) ) {
				return self::_prepare_canonical( $canonical );
			}


			
		} elseif ( is_tag() ) {


			if ( $options['canonical_manually'] && $canonical = self::get_tax_data( 'canonical' ) ) {
				return self::_prepare_canonical( $canonical );
			}
		} elseif ( is_tax() ) {
			if ( $options['canonical_manually'] && $canonical = self::get_tax_data( 'canonical' ) ) {
				return self::_prepare_canonical( $canonical );
			}
		}


		
		if ( ! $options['noindex_canonical'] ) {
			return;
		}

		
		if ( is_singular() ) {
			remove_action(
				'wp_head',
				'rel_canonical'
			);
		}

		
		if ( is_paged() ) {
			return self::_prepare_canonical(
				get_pagenum_link( get_query_var( 'paged' ) )
			);
		}

		if ( is_front_page() ) {
			
			if ( function_exists( 'icl_get_home_url' ) ) {
				return self::_prepare_canonical( icl_get_home_url() );
			}

			return self::_prepare_canonical(
				trailingslashit( get_option( 'home' ) )
			);
		}

		if ( is_home() ) {
			return self::_prepare_canonical(
				get_permalink( get_queried_object_id() )
			);
		}

		if ( is_singular() ) {
			if ( is_attachment() ) {
				return self::_prepare_canonical(
					get_attachment_link( get_query_var( 'attachment_id' ) )
				);
			} else {
				return self::_prepare_canonical(
					self::_paginated_permalink( get_query_var( 'p' ) )
				);
			}
		}

		if ( is_search() ) {
			return self::_prepare_canonical(
				home_url( '?s=' . urlencode( get_search_query( false ) ) )
			);
		}

		if ( is_category() ) {
			return self::_prepare_canonical(
				get_category_link( get_query_var( 'cat' ) )
			);
		}

		if ( is_tag() ) {
			return self::_prepare_canonical(
				get_tag_link( get_term_by( 'slug', get_query_var( 'tag' ), 'post_tag' )->term_id )
			);
		}

		if ( is_author() ) {
			return self::_prepare_canonical(
				get_author_posts_url( get_query_var( 'author' ) )
			);
		}

		if ( is_tax() ) {
			return self::_prepare_canonical(
				get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) )
			);
		}

		if ( is_day() ) {
			return self::_prepare_canonical(
				get_day_link( get_query_var( 'year' ), get_query_var( 'monthnum' ), get_query_var( 'day' ) )
			);
		}

		if ( is_month() ) {
			return self::_prepare_canonical(
				get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) )
			);
		}

		if ( is_year() ) {
			return self::_prepare_canonical(
				get_year_link( get_query_var( 'year' ) )
			);
		}

		
		if ( is_post_type_archive( get_post_types( array( '_builtin' => false, 'public' => true ), 'names' ) ) ) {
			return self::_prepare_canonical(
				get_post_type_archive_link( get_post_type() )
			);
		}

	}

	

	private static function _prepare_canonical( $input ) {
		
		if ( empty( $input ) || is_wp_error( $input ) ) {
			return;
		}

		
		$options = wpSEOde_Options::get();

		
		$output = (string) $input;

		
		if ( $options['noindex_http'] ) {
			$output = str_replace(
				'https://',
				'http://',
				$output
			);
		}

		
		if ( is_paged() && $GLOBALS['wp_rewrite']->using_permalinks() ) {
			$output = self::_remove_url_query( $output );
		}

		
		$output = (string) apply_filters(
			'wpseo_set_canonical',
			$output
		);

		
		self::$_canonical = $output;

		return $output;
	}

	

	private static function _remove_url_query( $url ) {
		
		if ( strpos( $url, '?' ) === false ) {
			return $url;
		}

		
		$url_parts = parse_url( $url );

		
		if ( ! isset( $url_parts['host'] ) && $url[0] == '/' ) {
			$url       = get_home_url() . $url;
			$url_parts = parse_url( $url );
		}

		return sprintf( '%s://%s%s',
			$url_parts['scheme'],
			$url_parts['host'],
			( empty( $url_parts['path'] ) ? '' : $url_parts['path'] )
		);
	}

	

	private static function _paginated_permalink( $id = null ) {
		
		if ( empty( $id ) ) {
			$id = get_queried_object_id();
		}

		
		$permalink = get_permalink( $id );

		
		if ( ! ( $page = (int) self::_paginated_page() ) ) {
			return $permalink;
		}

		
		if ( $GLOBALS['wp_rewrite']->using_permalinks() ) {
			return user_trailingslashit( trailingslashit( $permalink ) . $page . '/', 'paged' );
		} else {
			return add_query_arg( 'page', $page, $permalink );
		}
	}

	

	private static function _html_prevnext() {
		global $wp_query;
		$sReturn = '';
		if ( is_archive() ) {
			if ( is_paged() && ( $link = apply_filters( 'wpseo_get_rel_prev', get_previous_posts_page_link() ) ) != '' ) {
				$link    = esc_url( $link, array( 'http', 'https' ) );
				$sReturn .= sprintf( '%s<link rel="prev" href="%s" />',
					"\n",
					apply_filters( 'wpseo_set_rel_prev', $link )
				);
			}
			if ( isset( $wp_query->max_num_pages ) && $wp_query->max_num_pages > 0 && ( $link = apply_filters( 'wpseo_get_rel_next', get_next_posts_page_link( $wp_query->max_num_pages ) ) ) != '' ) {
				$link    = esc_url( $link, array( 'http', 'https' ) );
				$sReturn .= sprintf( '%s<link rel="next" href="%s" />',
					"\n",
					apply_filters( 'wpseo_set_rel_next', $link )
				);
			}
		}

		return $sReturn;
	}

	

	private static function _html_opengraph() {
		
		$options         = wpSEOde_Options::get();
		$options_monitor = wpSEOde_Options::get( 'monitor_options' );
		$opengraph       = '';

		if ( self::_get_canonical() != '' ) {
			$open_graph_url = self::_get_canonical();
		} elseif ( isset( $_SERVER['HTTPS'] ) && isset( $_SERVER['HTTP_HOST'] ) ) {
			$open_graph_url = ( $_SERVER['HTTPS'] != '' ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} elseif ( ! isset( $_SERVER['HTTPS'] ) && isset( $_SERVER['HTTP_HOST'] ) ) {
			$open_graph_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		} else {
			return $opengraph;
		}

		$open_graph_url = esc_url( $open_graph_url );

		
		if ( ( is_front_page() || is_home() || ( function_exists( 'is_shop' ) && is_shop() ) ) && $options['open_graph'] ) {
			$opengraph = "\n" . '<meta property="og:type" content="website">' . "\n";
			$opengraph .= sprintf( '<meta property="og:url" content="%s">', $open_graph_url ) . "\n";
			$opengraph .= sprintf( '<meta property="og:title" content="%s">',
					esc_attr( self::_replace_vars( ( ( isset( $options['opengraph_start_title'] ) && $options['opengraph_start_title'] != '' ) ? $options['opengraph_start_title'] : self::_get_title() ) ) ) ) . "\n";
			$opengraph .= sprintf( '<meta property="og:description" content="%s">',
					esc_attr( self::_replace_vars( ( ( isset( $options['opengraph_start_description'] ) && $options['opengraph_start_description'] != '' ) ? $options['opengraph_start_description'] : self::_get_description() ) ) ) ) . "\n";
			$ogimg     = get_post_meta( self::_current_post_id(), '_wpseo_edit_og_image', true );
			if ( empty( $ogimg ) && isset( $options['opengraph_start_image'] ) ) {
				$ogimg = $options['opengraph_start_image'];
			}
			if ( ! empty( $ogimg ) ) {
				$opengraph .= sprintf( '<meta property="og:image" content="%s">',
						esc_url( $ogimg ) ) . "\n";
			}
		} elseif ( is_singular() && $options['open_graph'] && ! get_post_meta( self::_current_post_id(),
				'_wpseo_edit_opengraph', true ) ) {
			$og_image = wp_get_attachment_image_src( get_post_thumbnail_id( self::_current_post_id() ),
				'single-post-thumbnail' );
			$sType    = get_post_type( self::_current_post_id() );

			if ( ! in_array( $sType, get_post_types( array( '_builtin' => true ), 'names' ) ) ) {
				$sType = 'post';
			}

			$opengraph = "\n" . '<meta property="og:type" content="article">' . "\n";
			$opengraph .= sprintf( '<meta property="og:url" content="%s">', $open_graph_url ) . "\n";
			if ( ! isset( $options['open_graph_date_disable'] ) || ( $options['open_graph_date_disable'] == '1' && get_post_meta( self::_current_post_id(),
						'_wpseo_edit_opengraph_date_disable', true ) != '1' ) ) {
				$opengraph .= sprintf( '<meta property="article:published_time" content="%s">',
						get_the_time( 'Y-m-d\TH:i:sP', self::_current_post_id() ) ) . "\n";
			}

			$opengraph .= sprintf( '<meta property="og:title" content="%s">',
					esc_attr( self::_replace_vars( ( get_post_meta( self::_current_post_id(), '_wpseo_edit_og_title',
						true ) != '' ? get_post_meta( self::_current_post_id(), '_wpseo_edit_og_title',
						true ) : ( ( ( isset( $options[ 'opengraph_' . $sType . '_title' ] ) && $options[ 'opengraph_' . $sType . '_title' ] != '' ) ? $options[ 'opengraph_' . $sType . '_title' ] : self::_get_title() ) ) ) ) ) ) . "\n";
			$opengraph .= sprintf( '<meta property="og:description" content="%s">',
					esc_attr( self::_replace_vars( ( get_post_meta( self::_current_post_id(),
						'_wpseo_edit_og_description',
						true ) != '' ? get_post_meta( self::_current_post_id(), '_wpseo_edit_og_description',
						true ) : ( ( isset( $options[ 'opengraph_' . $sType . '_description' ] ) && $options[ 'opengraph_' . $sType . '_description' ] != '' ) ? $options[ 'opengraph_' . $sType . '_description' ] : self::_get_description() ) ) ) ) ) . "\n";
			if ( get_post_meta( self::_current_post_id(), '_wpseo_edit_og_image', true ) != '' ) {
				$opengraph .= sprintf( '<meta property="og:image" content="%s">',
						esc_url( get_post_meta( self::_current_post_id(), '_wpseo_edit_og_image', true ) ) ) . "\n";
			} elseif ( has_post_thumbnail() && $og_image[0] != '' ) {
				$opengraph .= sprintf( '<meta property="og:image" content="%s">', esc_url( $og_image[0] ) ) . "\n";
			} elseif ( isset( $options[ 'opengraph_' . $sType . '_image' ] ) && $options[ 'opengraph_' . $sType . '_image' ] != '' ) {
				$opengraph .= sprintf( '<meta property="og:image" content="%s">',
						esc_url( $options[ 'opengraph_' . $sType . '_image' ] ) ) . "\n";
			}
		} elseif ( is_category() && $options['open_graph'] ) {
			$term = get_queried_object();
			if ( ! is_wp_error( $term ) && get_option( self::meta_data_name( $term, 'og_disable' ), false ) != '1' ) {
				$opengraph = "\n" . '<meta property="og:type" content="website">' . "\n";
				$opengraph .= sprintf( '<meta property="og:url" content="%s">', $open_graph_url ) . "\n";
				$opengraph .= sprintf( '<meta property="og:title" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'og_title' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'og_title' ),
							'' ) : self::_get_title() ) ) ) ) . "\n";
				$opengraph .= sprintf( '<meta property="og:description" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'og_desc' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'og_desc' ),
							'' ) : self::_get_description() ) ) ) ) . "\n";
				if ( get_option( self::meta_data_name( $term, 'og_image' ), '' ) != '' ) {
					$opengraph .= sprintf( '<meta property="og:image" content="%s">',
							esc_url( get_option( self::meta_data_name( $term, 'og_image' ), '' ) ) ) . "\n";
				}
			}
		} elseif ( is_tag() && $options['open_graph'] ) {
			$term = get_queried_object();
			if ( ! is_wp_error( $term ) && get_option( self::meta_data_name( $term, 'og_disable' ), false ) != '1' ) {
				$opengraph = "\n" . '<meta property="og:type" content="website">' . "\n";
				$opengraph .= sprintf( '<meta property="og:url" content="%s">', $open_graph_url ) . "\n";
				$opengraph .= sprintf( '<meta property="og:title" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'og_title' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'og_title' ),
							'' ) : self::_get_title() ) ) ) ) . "\n";
				$opengraph .= sprintf( '<meta property="og:description" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'og_desc' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'og_desc' ),
							'' ) : self::_get_description() ) ) ) ) . "\n";
				if ( get_option( self::meta_data_name( $term, 'og_image' ), '' ) != '' ) {
					$opengraph .= sprintf( '<meta property="og:image" content="%s">',
							esc_url( get_option( self::meta_data_name( $term, 'og_image' ), '' ) ) ) . "\n";
				}
			}
		} elseif ( is_tax() && $options['open_graph'] ) {
			$term = get_queried_object();
			if ( ! is_wp_error( $term ) && get_option( self::meta_data_name( $term, 'og_disable' ), false ) != '1' ) {
				$opengraph = "\n" . '<meta property="og:type" content="website">' . "\n";
				$opengraph .= sprintf( '<meta property="og:url" content="%s">', $open_graph_url ) . "\n";
				$opengraph .= sprintf( '<meta property="og:title" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'og_title' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'og_title' ),
							'' ) : self::_get_title() ) ) ) ) . "\n";
				$opengraph .= sprintf( '<meta property="og:description" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'og_desc' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'og_desc' ),
							'' ) : self::_get_description() ) ) ) ) . "\n";
				if ( get_option( self::meta_data_name( $term, 'og_image' ), '' ) != '' ) {
					$opengraph .= sprintf( '<meta property="og:image" content="%s">',
							esc_url( get_option( self::meta_data_name( $term, 'og_image' ), '' ) ) ) . "\n";
				}
			}
		}
		if ( ! empty( $options_monitor['facebook_app_id'] ) ) {
			$opengraph .= '<meta property="fb:app_id" content="' . esc_attr( $options_monitor['facebook_app_id'] ) . '"/>' . "\n";
		}

		return $opengraph;
	}

	private static function meta_data_name( $term, $suffix = null ) {
		if ( is_object( $term ) ) {
			return sprintf(
				'wpseo_%s_%d%s',
				$term->taxonomy,
				$term->term_id,
				( $suffix ? '_' . $suffix : '' )
			);
		} else {
			return false;
		}
	}

	

	private static function _html_twittercard() {
		
		$options      = wpSEOde_Options::get();
		$twitter_card = '';
		
		if ( ( is_front_page() && is_home() || ( function_exists( 'is_shop' ) && is_shop() ) ) && $options['twitter_site_account'] != '' ) {
			if ( $options['twittercard_start_image'] != '' ) {
				$twitter_card = "\n" . '<meta name="twitter:card" content="summary_large_image">' . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:image" content="%s">',
						esc_url( $options['twittercard_start_image'] ) ) . "\n";
			} else {
				$twitter_card = '<meta name="twitter:card" content="summary">' . "\n";
			}

			$twitter_card .= sprintf( '<meta name="twitter:site" content="%s">',
					esc_attr( $options['twitter_site_account'] ) ) . "\n";
			$twitter_card .= sprintf( '<meta name="twitter:title" content="%s">',
					esc_attr( self::_replace_vars( ( $options['twittercard_start_title'] != '' ? $options['twittercard_start_title'] : self::_get_title() ) ) ) ) . "\n";
			$twitter_card .= sprintf( '<meta name="twitter:description" content="%s">',
					esc_attr( self::_replace_vars( ( $options['twittercard_start_description'] != '' ? $options['twittercard_start_description'] : self::_get_description() ) ) ) ) . "\n";
		} elseif ( is_singular() && $twitter_site_account = $options['twitter_site_account'] && ! get_post_meta( self::_current_post_id(),
					'_wpseo_edit_twittercard', true ) ) {
			$twitter_image = wp_get_attachment_image_src( get_post_thumbnail_id( self::_current_post_id() ),
				'single-post-thumbnail' );
			if ( get_post_meta( self::_current_post_id(), '_wpseo_edit_twittercard_image', true ) != '' ) {
				$twitter_card = "\n" . '<meta name="twitter:card" content="summary_large_image">' . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:image" content="%s">',
						esc_url( get_post_meta( self::_current_post_id(), '_wpseo_edit_twittercard_image',
							true ) ) ) . "\n";
			} elseif ( has_post_thumbnail() && $twitter_image[0] != '' ) {
				$twitter_card = "\n" . '<meta name="twitter:card" content="summary_large_image">' . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:image" content="%s">',
						esc_url( $twitter_image[0] ) ) . "\n";
			} else {
				$twitter_card = '<meta name="twitter:card" content="summary">' . "\n";
			}

			$twitter_card .= sprintf( '<meta name="twitter:site" content="%s">',
					esc_attr( $options['twitter_site_account'] ) ) . "\n";

			if ( $twittercard_authorship = self::_get_twittercard() && substr( self::_get_twittercard(), 0,
					1 ) == '@' && ! get_post_meta( self::_current_post_id(), '_wpseo_edit_twittercard_authorship',
					true ) ) {
				$twitter_card .= sprintf( '<meta name="twitter:creator" content="%s">',
						esc_attr( self::_get_twittercard() ) ) . "\n";
			}
			$twitter_card .= sprintf( '<meta name="twitter:title" content="%s">',
					esc_attr( self::_replace_vars( ( get_post_meta( self::_current_post_id(),
						'_wpseo_edit_twittercard_title', true ) != '' ? get_post_meta( self::_current_post_id(),
						'_wpseo_edit_twittercard_title', true ) : self::_get_title() ) ) ) ) . "\n";
			$twitter_card .= sprintf( '<meta name="twitter:description" content="%s">',
					esc_attr( self::_replace_vars( ( get_post_meta( self::_current_post_id(),
						'_wpseo_edit_twittercard_description',
						true ) != '' ? get_post_meta( self::_current_post_id(), '_wpseo_edit_twittercard_description',
						true ) : self::_get_description() ) ) ) ) . "\n";
		} elseif ( is_category() && $twitter_site_account = $options['twitter_site_account'] ) {
			$term = get_queried_object();
			if ( ! is_wp_error( $term ) && get_option( self::meta_data_name( $term, 'twittercard_disable' ),
					false ) != '1' ) {

				if ( get_option( self::meta_data_name( $term, 'twittercard_image' ), '' ) != '' ) {
					$twitter_card = "\n" . '<meta name="twitter:card" content="summary_large_image">' . "\n";
					$twitter_card .= sprintf( '<meta name="twitter:image" content="%s">',
							esc_url( get_option( self::meta_data_name( $term, 'twittercard_image' ), '' ) ) ) . "\n";
				} else {
					$twitter_card = '<meta name="twitter:card" content="summary">' . "\n";
				}

				$twitter_card .= sprintf( '<meta name="twitter:site" content="%s">',
						esc_attr( $options['twitter_site_account'] )
				                 ) . "\n";

				$twitter_card .= sprintf( '<meta name="twitter:title" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'twittercard_title' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'twittercard_title' ),
							'' ) : self::_get_title() ) ) )
				                 ) . "\n";

				$twitter_card .= sprintf( '<meta name="twitter:description" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'twittercard_desc' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'twittercard_desc' ),
							'' ) : self::_get_description() ) ) )
				                 ) . "\n";
			}
		} elseif ( is_tag() && $twitter_site_account = $options['twitter_site_account'] ) {
			$term = get_queried_object();
			if ( ! is_wp_error( $term ) && get_option( self::meta_data_name( $term, 'twittercard_disable' ),
					false ) != '1' ) {

				if ( get_option( self::meta_data_name( $term, 'twittercard_image' ), '' ) != '' ) {
					$twitter_card = "\n" . '<meta name="twitter:card" content="summary_large_image">' . "\n";
					$twitter_card .= sprintf( '<meta name="twitter:image" content="%s">',
							esc_url( get_option( self::meta_data_name( $term, 'twittercard_image' ), '' ) ) ) . "\n";
				} else {
					$twitter_card = '<meta name="twitter:card" content="summary">' . "\n";
				}

				$twitter_card .= sprintf( '<meta name="twitter:site" content="%s">',
						esc_attr( $options['twitter_site_account'] ) ) . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:title" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'twittercard_title' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'twittercard_title' ),
							'' ) : self::_get_title() ) ) ) ) . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:description" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'twittercard_desc' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'twittercard_desc' ),
							'' ) : self::_get_description() ) ) ) ) . "\n";
			}
		} elseif ( is_tax() && $twitter_site_account = $options['twitter_site_account'] ) {
			$term = get_queried_object();
			if ( ! is_wp_error( $term ) && get_option( self::meta_data_name( $term, 'twittercard_disable' ),
					false ) != '1' ) {

				if ( get_option( self::meta_data_name( $term, 'twittercard_image' ), '' ) != '' ) {
					$twitter_card = "\n" . '<meta name="twitter:card" content="summary_large_image">' . "\n";
					$twitter_card .= sprintf( '<meta name="twitter:image" content="%s">',
							esc_url( get_option( self::meta_data_name( $term, 'twittercard_image' ), '' ) ) ) . "\n";
				} else {
					$twitter_card = '<meta name="twitter:card" content="summary">' . "\n";
				}

				$twitter_card .= sprintf( '<meta name="twitter:site" content="%s">',
						esc_attr( $options['twitter_site_account'] ) ) . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:title" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'twittercard_title' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'twittercard_title' ),
							'' ) : self::_get_title() ) ) ) ) . "\n";
				$twitter_card .= sprintf( '<meta name="twitter:description" content="%s">',
						esc_attr( self::_replace_vars( ( get_option( self::meta_data_name( $term, 'twittercard_desc' ),
							'' ) != '' ? get_option( self::meta_data_name( $term, 'twittercard_desc' ),
							'' ) : self::_get_description() ) ) ) ) . "\n";
			}
		}

		return $twitter_card;
	}

	

	private static function _get_twittercard() {
		
		if ( ! is_singular() ) {
			return;
		}

		
		if ( ! empty( self::$_twitter_author ) ) {
			return self::$_twitter_author;
		}

		
		if ( ! $post = self::_current_post() ) {
			return;
		}

		
		$output = (string) apply_filters(
			'wpseo_set_twitter_creator',
			get_the_author_meta( 'twitter', $post->post_author )
		);

		

		return self::$_twitter_author = $output;
	}

	

	private static function _html_authorship() {
		
		$options = wpSEOde_Options::get();
	}

	

	private static function _get_authorship() {
		
		if ( ! is_singular() ) {
			return;
		}

		
		if ( ! empty( self::$_authorship ) ) {
			return self::$_authorship;
		}

		
		if ( ! $post = self::_current_post() ) {
			return;
		}

		

		return self::$_authorship = $output;
	}

	

	private static function _html_snippets() {
		if ( $snippets = self::_get_snippets() ) {
			return sprintf(
				'%s%s',
				"\n",
				$snippets
			);
		}
	}

	

	private static function _get_snippets() {
		
		if (
			( defined( 'WPSEO_DISABLE_SNIPPETS' ) && WPSEO_DISABLE_SNIPPETS ) || 			( defined( 'WPSEODE_DISABLE_SNIPPETS' ) && WPSEODE_DISABLE_SNIPPETS )
		) {
			return null;
		}

		
		$snippets = (array) apply_filters(
			'wpseo_set_snippets',
			wpSEOde_Options::get( 'snippets_data' )
		);

		
		if ( empty( $snippets ) ) {
			return null;
		}

		
		$output = '';

		
		foreach ( $snippets as $snippet ) {
			if ( empty( $snippet ) or ! is_array( $snippet ) ) {
				continue;
			}

			$output .= sprintf(
				'%s%s',
				"\n",
				$snippet['code']
			);
		}

		return $output;
	}

	

	private static function _rich_snippets() {
		if ( ! is_home() && ! is_front_page() ) {
			return '';
		}
		
		$options = wpSEOde_Options::get();

		if ( ! $options['social_profiles'] ) {
			return '';
		}
		$sReturn  = '<script type="application/ld+json">{"@context": "http://schema.org","@type":"' . esc_js( $options['social_data_type'] ) . '","name":"' . esc_js( $options['social_data_name'] ) . '","url":"' . esc_js( home_url() ) . '"';
		$profiles = array();
		if ( ! empty( $options['social_profile_youtube'] ) ) {
			array_push( $profiles, esc_js( $options['social_profile_youtube'] ) );
		}
		if ( ! empty( $options['social_profile_facebook'] ) ) {
			array_push( $profiles, esc_js( $options['social_profile_facebook'] ) );
		}
		if ( ! empty( $options['social_profile_twitter'] ) ) {
			array_push( $profiles, esc_js( $options['social_profile_twitter'] ) );
		}
		if ( ! empty( $options['social_profile_linkedin'] ) ) {
			array_push( $profiles, esc_js( $options['social_profile_linkedin'] ) );
		}
		if ( ! empty( $options['social_profile_instagram'] ) ) {
			array_push( $profiles, esc_js( $options['social_profile_instagram'] ) );
		}
		if ( ! empty( $options['social_profile_pinterest'] ) ) {
			array_push( $profiles, esc_js( $options['social_profile_pinterest'] ) );
		}
		if ( ! empty( $profiles ) ) {
			$sReturn .= ',"sameAs":["' . implode( '","', $profiles ) . '"]';
		}
		$sReturn .= '}</script>';

		return $sReturn;
	}

	

	private static function _pinterest_verify() {
		if ( ! is_home() && ! is_front_page() ) {
			return '';
		}
		
		$options = wpSEOde_Options::get();

		if ( ! $options['pinterest_domain_verify_tag'] ) {
			return '';
		}

		return '<meta name="p:domain_verify" content="' . esc_attr( $options['pinterest_domain_verify_tag'] ) . '"/>';
	}

	
	public static function modify_output( $data = '' ) {
		$borlabsCacheActive = false;
		
		if ( class_exists( 'Borlabs\Factory' ) && defined( 'BORLABS_CACHE_VERSION' ) ) {
			require dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'fix' . DIRECTORY_SEPARATOR . '1-borlabs-cache-2.php';
		}

		
		$options = wpSEOde_Options::get();

		
		$output = self::get_output();

		
		if ( empty( $output ) ) {
			return $data;
		}

		
		if ( ! $options['speed_nocheck'] ) {
			
			$todo  = array();
			$clean = array();

			
			if ( $options['noindex_enable'] or $options['noindex_manually'] or $options['misc_noodp'] or $options['misc_noarchive'] ) {
				$todo[] = 'robots';
			}

			
			if ( $options['desc_enable'] or $options['desc_manually'] ) {
				$todo[] = 'description';
			}

			
			if ( $options['key_manually'] ) {
				$todo[] = 'keywords';
			}

			
			if ( ( $options['title_enable'] or $options['title_manually'] ) && strripos( $data,
					'<title>' ) !== false ) {
				$clean['title'] = '<title>.*?<\/title>';
			}

			
			if ( ( $options['noindex_canonical'] or $options['canonical_manually'] ) && strripos( $data,
					'canonical' ) !== false ) {
				$clean['link'] = '<link[^>]*?[\'"]canonical[\'"].*?>';
			}

			
			if ( $todo && strripos( $data, '<meta' ) !== false ) {
				$clean['meta'] = '<meta[^>]*?name=[\'"](' . implode( '|', $todo ) . ')[\'"].*?>';
			}

			
			if ( ! empty( $clean['meta'] ) or ! empty( $clean['title'] ) or ! empty( $clean['link'] ) ) {
				$data = preg_replace(
					'/(?:' . implode( '|', $clean ) . ')/is',
					'',
					$data
				);
			}
		}

		
		$content = preg_replace(
			'/<head(.*?)>(.*?)<meta(.*?)charset=(.*?)>/si',
			"<head$1>$2<meta$3charset=$4>\n" . str_replace( '$', '\$', $output ),
			$data,
			1
		);

		
		if ( empty( $content ) or strlen( $content ) === strlen( $data ) ) {
			
			if ( strpos( $data, '</head>' ) !== false ) {
				return str_replace(
					'</head>',
					$output . "\n</head>",
					$data
				);
			}

			

			return str_replace(
				'</html>',
				$output . "\n</html>",
				$data
			);
		}

		if ( ! $borlabsCacheActive ) {
			return $content;
		}
		echo $content;
	}

	

	public static function rewrite_title( $title, $post_id ) {
		
		if ( empty( $post_id ) or get_queried_object_id() != $post_id ) {
			return $title;
		}

		
		if ( ( $meta = wpSEOde_Cache::get( 'meta' ) ) && ! empty( $meta['title'] ) ) {
			return $meta['title'];
		}

		return $title;
	}

	

	public static function filter_og_tags( $tags ) {
		$tags['og:title']       = self::_get_title();
		$tags['og:description'] = self::_get_description();

		return $tags;
	}

	

	public static function the_output() {
		echo self::get_output();
	}


	

	public static function the_title() {
		echo self::_replace_vars( self::_get_title() );
	}


	

	public static function get_title( $sTitleStd ) {
		$sTitle = self::_replace_vars( self::_get_title() );

		return ! empty( $sTitle ) ? $sTitle : $sTitleStd;
	}

	

	public static function replace_vars_callback( $sOutput ) {
		return self::_replace_vars( $sOutput );
	}


	

	public static function the_description() {
		echo esc_attr( self::_replace_vars( self::_get_description() ) );
	}


	

	public static function get_description( $sDescriptionStd ) {
		$sDescription = self::_replace_vars( self::_get_description() );

		return ! empty( $sDescription ) ? $sDescription : $sDescriptionStd;
	}


	

	public static function the_keywords() {
		echo esc_attr( self::_replace_vars( self::_get_keywords() ) );
	}


	

	public static function get_keywords( $sKeywordsStd ) {
		$sKeywords = self::_replace_vars( self::_get_keywords() );

		return ! empty( $sKeywords ) ? $sKeywords : $sKeywordsStd;
	}


	

	public static function the_robots() {
		echo self::_get_robots();
	}


	

	public static function get_robots( $sRobotsStd ) {
		$sRobots = self::_get_robots();

		return ! empty( $sRobots ) ? $sRobots : $sRobotsStd;
	}


	

	public static function the_canonical() {
		echo self::_get_canonical();
	}


	

	public static function get_canonical( $sCanonicalStd ) {
		$sCanonical = self::_get_canonical();

		return ! empty( $sCanonical ) ? $sCanonical : $sCanonicalStd;
	}


	

	public static function the_authorship() {
		echo self::_get_authorship();
	}


	

	public static function get_authorship( $sAuthorshipStd ) {
		$sAuthorship = self::_get_authorship();

		return ! empty( $sAuthorship ) ? $sAuthorship : $sAuthorshipStd;
	}

	
	private static function _get_tax_value_helper( $o ) {
		return ! in_array( $o, array( 'category', 'post_tag' ) );
	}
}
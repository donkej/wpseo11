<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Rewrite {

	
	public static function rewrite() {
		if ( wpSEOde_Options::get( 'strip_categorybase' ) == '1' ) {
			add_action( 'created_category', array( __CLASS__, 'flush_schedule' ) );
			add_action( 'edited_category', array( __CLASS__, 'flush_schedule' ) );
			add_action( 'delete_category', array( __CLASS__, 'flush_schedule' ) );

			add_filter( 'term_link', array( __CLASS__, 'strip_category_base' ), 10, 3 );
			add_filter( 'category_rewrite_rules', array( __CLASS__, 'category_rewrite_rules' ) );
		}

		if ( wpSEOde_Options::get( 'sitemap' ) == '1' ) {
			add_rewrite_rule( ( site_url( '/' ) != home_url( '/' ) ? '^[^/]*/' : '' ) . 'sitemap\\-?(page|post|custom)?.xml$',
				'index.php?wpseo_sitemap=$matches[1]', 'top' );
		}

		add_filter( 'query_vars', array( __CLASS__, 'query_vars' ) );
		add_filter( 'request', array( __CLASS__, 'request' ) );

		add_action( 'init', array( __CLASS__, 'flush_exec' ), 9999 );
	}

	
	public static function flush_schedule() {
		update_option( 'wpseo_rewrite_flush', true );
	}

	
	public static function flush_exec() {
		if ( get_option( 'wpseo_rewrite_flush' ) ) {
			add_action( 'shutdown', 'flush_rewrite_rules' );
			delete_option( 'wpseo_rewrite_flush' );

			return true;
		}

		return false;
	}

	
	public static function strip_category_base( $sLink, $oTerm, $sTaxonomy ) {
		if ( $sTaxonomy != 'category' ) {
			return $sLink;
		}

		$sCategoryBase = get_option( 'category_base' );

		if ( empty( $sCategoryBase ) ) {
			$sCategoryBase = 'category';
		}

		if ( substr( $sCategoryBase, 0, 1 ) == '/' ) {
			$sCategoryBase = substr( $sCategoryBase, 1 );
		}

		$sCategoryBase = trailingslashit( $sCategoryBase );

		return preg_replace( '#' . preg_quote( $sCategoryBase, '#' ) . '#u', '', $sLink, 1 );
	}

	
	public static function category_rewrite_rules( $aRules ) {
		global $wp_rewrite;

		$aRulesNew           = array();
		$oTaxonomy           = get_taxonomy( 'category' );
		$sPermalinkStructure = get_option( 'permalink_structure' );

		$sPrefix = '';
		
		if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $permalink_structure,
				'/blog/' ) ) {
			$sPrefix = 'blog/';
		}

		if ( wpSEOde_Options::get( 'redirect_old_categorybase' ) == '1' ) {
			$sBase = $wp_rewrite->get_category_permastruct();
			$sBase = trim( str_replace( '%category%', '(.+)', $sBase ) );
			if ( substr( $sBase, 0, 1 ) == '/' ) {
				$sBase = substr( $sBase, 1 );
			}
			$aNewRules[ $sBase . '$' ] = 'index.php?wpseo_old_category=$matches[1]';
		}

		$aCategories = get_categories( array( 'hide_empty' => false, 'orderby' => 'slug' ) );
		$aSlugs      = array();
		foreach ( $aCategories as $oCategory ) {
			$sSlug = $oCategory->slug;
			if ( $oCategory->parent != $oCategory->cat_ID && $oTaxonomy->rewrite['hierarchical'] === true && $oCategory->parent != 0 ) {
				$sParents = get_category_parents( $oCategory->parent, false, '/', true );
				if ( ! is_wp_error( $sParents ) ) {
					$sSlug = $sParents . $sSlug;
				}
			}

			$aNewRules[ $sPrefix . '(' . $sSlug . ')/(.+/)?feed/(feed|rdf|rss|rss2|atom)/?$' ]                    = 'index.php?category_name=$matches[1]&feed=$matches[3]';
			$aNewRules[ $sPrefix . '(' . $sSlug . ')/(.+/)?(feed|rdf|rss|rss2|atom)/?$' ]                         = 'index.php?category_name=$matches[1]&feed=$matches[3]';
			$aNewRules[ $sPrefix . '(' . $sSlug . ')/(.+/)?embed/?$' ]                                            = 'index.php?category_name=$matches[1]&embed=true';
			$aNewRules[ $sPrefix . '(' . $sSlug . ')/(.+/)?' . $wp_rewrite->pagination_base . '/([0-9]{1,})/?$' ] = 'index.php?category_name=$matches[1]&paged=$matches[3]';
			$aNewRules[ $sPrefix . '(' . $sSlug . ')$' ]                                                          = 'index.php?category_name=$matches[1]';
		}

		return $aNewRules;
	}

	
	public static function query_vars( $aQueryVars ) {
		if ( wpSEOde_Options::get( 'strip_categorybase' ) == '1' ) {
			array_push( $aQueryVars, 'wpseo_old_category' );
		}
		if ( wpSEOde_Options::get( 'sitemap' ) == '1' ) {
			array_push( $aQueryVars, 'wpseo_sitemap' );
		}

		return $aQueryVars;
	}

	
	public static function request( $aQueryVars ) {
		global $wp_rewrite;
		if ( wpSEOde_Options::get( 'strip_categorybase' ) == '1' && isset( $aQueryVars['wpseo_old_category'] ) && ! empty( $aQueryVars['wpseo_old_category'] ) ) {
			$sLink = trailingslashit( get_option( 'home' ) ) . user_trailingslashit( $aQueryVars['wpseo_old_category'],
					'category' );

			wp_redirect( $sLink, 301 );
			exit;
		} elseif ( wpSEOde_Options::get( 'sitemap' ) == '1' && isset( $aQueryVars['wpseo_sitemap'] ) ) {
			new wpSEOde_Sitemap( $aQueryVars['wpseo_sitemap'] );
		}

		return $aQueryVars;
	}

}

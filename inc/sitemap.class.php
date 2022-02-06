<?php


defined( 'ABSPATH' ) OR exit;


class wpSEOde_Sitemap {
	public static function exists() {
		return ( file_exists( get_home_path() . DIRECTORY_SEPARATOR . 'sitemap.xml' ) || file_exists( get_home_path() . DIRECTORY_SEPARATOR . 'sitemap-page.xml' ) || file_exists( get_home_path() . DIRECTORY_SEPARATOR . 'sitemap-post.xml' ) || file_exists( get_home_path() . DIRECTORY_SEPARATOR . 'sitemap-custom.xml' ) );
	}

	public function __construct( $sSitemap ) {
		header( 'Content-type: text/xml' );
		$this->_header();
		switch ( $sSitemap ) {
			case 'post':
				$this->_post();
				break;
			case 'page':
				$this->_page();
				break;
			case 'custom':
				$this->_custom();
				break;
			default:
				$this->_overview();
				break;
		}
		$this->_footer();
		die();
	}

	private function _header() {
		echo '<?xml version="1.0" encoding="UTF-8"?>';
	}

	private function _footer() {
	}

	private function _overview() {
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		if ( $this->_hasEntries( 'post' ) ) {
			echo '<sitemap><loc>' . get_site_url( null, 'sitemap-post.xml' ) . '</loc></sitemap>';
		}
		if ( $this->_hasEntries( 'page' ) ) {
			echo '<sitemap><loc>' . get_site_url( null, 'sitemap-page.xml' ) . '</loc></sitemap>';
		}
		if ( $this->_hasEntries( 'custom' ) ) {
			echo '<sitemap><loc>' . get_site_url( null, 'sitemap-custom.xml' ) . '</loc></sitemap>';
		}
		echo '</sitemapindex>';
	}

	private function _post() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$aPostTypes = array( 'post' );
		$this->_entries( $aPostTypes );
		echo '</urlset>';
	}

	private function _custom() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		$aPostTypes       = array();
		$aPostTypesResult = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
		foreach ( $aPostTypesResult AS $sName => $oPostType ) {
			array_push( $aPostTypes, $sName );
		}
		$this->_entries( $aPostTypes );
		echo '</urlset>';
	}

	private function _page() {
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		if ( get_option( 'page_on_front' ) == '0' ) {
			echo '<url><loc>' . $this->_escape( trailingslashit( get_home_url() ) ) . '</loc></url>';
		}
		$aPostTypes = array( 'page' );
		$this->_entries( $aPostTypes );
		echo '</urlset>';
	}

	private function _hasEntries( $sType ) {
		$aPostTypes = array();
		switch ( $sType ) {
			case 'post':
				array_push( $aPostTypes, 'post' );
				break;
			case 'custom':
				$aPostTypesResult = get_post_types( array( '_builtin' => false, 'public' => true ), 'objects' );
				foreach ( $aPostTypesResult AS $sName => $oPostType ) {
					array_push( $aPostTypes, $sName );
				}
				break;
			case 'page':
				array_push( $aPostTypes, 'page' );
				break;
		}
		for ( $iRow = 0; $iRow < count( $aPostTypes ); $iRow ++ ) {
			$aArgs  = array(
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'post_type'        => $aPostTypes[ $iRow ],
				'fields'           => 'ids',
				'suppress_filters' => 0
			);
			$aPosts = get_posts( $aArgs );
			if ( count( $aPosts ) > 0 ) {
				return true;
			}
		}

		return false;
	}

	private function _entries( $aPostTypes ) {
		$options = wpSEOde_Options::get();
		if ( ! is_array( $aPostTypes ) || count( $aPostTypes ) == 0 ) {
			return;
		}
		for ( $iRow = 0; $iRow < count( $aPostTypes ); $iRow ++ ) {
			$iOffset  = 0;
			$iPerPage = 10;
			$aArgs    = array(
				'post_status'    => 'publish',
				'posts_per_page' => $iPerPage,
				'offset'         => &$iOffset,
				'post_type'      => $aPostTypes[ $iRow ],
				'fields'         => 'ids'
			);
			$aPosts   = get_posts( $aArgs );
			while ( count( $aPosts ) > 0 ) {
				for ( $iPost = 0; $iPost < count( $aPosts ); $iPost ++ ) {
					$sNoindex = get_post_meta( $aPosts[ $iPost ], '_wpseo_edit_robots', true );
					$sSitemap = get_post_meta( $aPosts[ $iPost ], '_wpseo_edit_sitemap', true );
					if (
						! $options['sitemap'] ||
						( $options['sitemap_manually'] && $sSitemap == '2' ) ||
						( $options['noindex_manually'] && in_array( $sNoindex, array( 3, 4, 5 ) ) ) ||
						( $sNoindex == '' && (
								( $aPostTypes[ $iRow ] == 'post' && in_array( $options['noindex_single'],
										array( 3, 4, 5 ) ) ) ||
								( $aPostTypes[ $iRow ] == 'page' && in_array( $options['noindex_page'],
										array( 3, 4, 5 ) ) ) ||
								( ! in_array( $aPostTypes[ $iRow ], array(
										'page',
										'post'
									) ) && in_array( $options[ 'noindex_posttype_' . $aPostTypes[ $iRow ] ],
										array( 3, 4, 5 ) ) )
							) ||
						  ( $options['noindex_age'] && abs( current_time( 'timestamp' ) ) - get_the_time( 'U',
								  $aPosts[ $iPost ] ) > (int) apply_filters( 'wpseo_set_noindex_age', 6 ) * 30 * 86400 )
						)
					) {
						continue;
					}
					echo '<url><loc>' . $this->_escape( get_permalink( $aPosts[ $iPost ] ) ) . '</loc></url>';
				}
				$iOffset += $iPerPage;
				$aPosts  = get_posts( $aArgs );
			}
		}
	}

	private function _escape( $sStr ) {
		return htmlspecialchars( $sStr, ENT_XML1, 'UTF-8' );
	}
}

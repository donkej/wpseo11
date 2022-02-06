<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Slug {


	

	public static function init() {
		
		if ( strpos( get_option( 'permalink_structure' ), '%postname%' ) === false ) {
			return null;
		}

		
		remove_filter(
			'sanitize_title',
			'sanitize_title_with_dashes'
		);

		
		add_filter(
			'sanitize_title',
			array(
				__CLASS__,
				'filter'
			),
			9,
			3
		);
		add_filter(
			'sanitize_title',
			array(
				__CLASS__,
				'build'
			),
			10
		);
	}


	

	public static function build( $slug ) {
		
		if ( empty( $slug ) ) {
			return;
		}

		
		if ( strpos( $slug, ' ' ) === false ) {
			return self::_sanitize_data( $slug );
		}

		
		$options = wpSEOde_Options::get();

		
		$max = (int) $options['misc_slug_max'];

		
		$nouns = self::_nouns( $slug, $max );

		
		if ( sizeof( $nouns ) == $max ) {
			$draft = join( ' ', $nouns );
		} else {
			$draft = $slug;
		}

		

		return self::_sanitize_data( $draft );
	}


	

	public static function filter( $title, $fallback = '', $context = 'save' ) {
		return ( $context == 'save' ? $fallback : $title );
	}


	

	private static function _nouns( $str, $max = null ) {
		
		if ( empty( $str ) ) {
			return array();
		}

		
		$str = str_replace(
			array( 'Google+' ),
			array( 'Googleplus' ),
			$str
		);

		
		$str = preg_replace(
			'/(\p{L}*\p{Lu}+(?:\p{L}|\p{N})*)\s(\d+)/u',
			'$1$2',
			$str
		);

		
		preg_match_all(
			'/\p{L}*\p{Lu}+(\p{L}|\p{N})*/u',
			$str,
			$matches
		);

		
		if ( empty( $matches[0] ) ) {
			return array();
		}

		
		$output = array_unique( $matches[0] );

		
		if ( ! $max OR sizeof( $output ) <= $max ) {
			return $output;
		}

		

		return array_slice(
			$output,
			0,
			$max
		);
	}


	

	private static function _sanitize_data( $str ) {
		
		if ( empty( $str ) ) {
			return;
		}

		
		$replace_locale = ! in_array( get_locale(), array( 'de_DE', 'da_DK' ) );

		
		if ( $replace_locale ) {
			add_filter(
				'locale',
				array(
					__CLASS__,
					'return_de_locale'
				)
			);
		}

		
		$sanitized_string = sanitize_title_with_dashes(
			remove_accents( $str ),
			null,
			'save'
		);

		
		if ( $replace_locale ) {
			remove_filter(
				'locale',
				array(
					__CLASS__,
					'return_de_locale'
				)
			);
		}

		return $sanitized_string;
	}


	

	public static function return_de_locale() {
		return 'de_DE';
	}
}
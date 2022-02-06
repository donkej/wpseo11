<?php

ini_set( 'display_errors', '1' );


defined( 'ABSPATH' ) or exit;



class wpSEOde_Tools {

	static public $result = false;

	

	public function __construct( $action ) {
		
		if ( ! in_array( (string) $action, array( 'import', 'export', 'import_yoast' ) ) ) {
			return false;
		}


		
		$callback = array(
			$this,
			'_' . $action
		);

		
		if ( is_callable( $callback ) ) {
			call_user_func( $callback );
		}

	}


	

	private static function _import() {
		
		if ( empty( $_FILES['wpseo_upadd_file'] ) or ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		
		$data = $_FILES['wpseo_upadd_file'];

		
		if ( empty( $data['type'] ) or $data['type'] != 'text/xml' or ! empty( $data['error'] ) or empty( $data['size'] ) or empty( $data['tmp_name'] ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		
		if ( ! $xml = (array) simplexml_load_file( $data['tmp_name'], null,
				LIBXML_NOCDATA ) or empty( $xml['options'] ) ) {
			wp_die( __( 'The XML file must contain options of wpSEO 3.x!<br />Options from older plugin versions can not be imported.',
				'wpseo' ) );
		}

		
		$data = (array) $xml['options'];

		
		$fields = array_keys( wpSEOde_Options::defaults() );

		
		$options = array();

		
		foreach ( $fields as $field ) {
			
			if ( isset( $data[ $field ] ) ) {
				
				$item = (array) $data[ $field ];

				
				if ( empty( $item['item'] ) ) {
					if ( ! empty( $item[0] ) and is_string( $item[0] ) ) {
						$options[ $field ] = sanitize_text_field( $item[0] );
					} else {
						$options[ $field ] = @$item[0];
					}
				} else {
					$options[ $field ] = array_map(
						'sanitize_text_field',
						(array) $item['item']
					);
				}
			}
		}

		
		if ( empty( $options ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		
		wpSEOde_Options::update( $options );

		
		$GLOBALS['wpdb']->query( "OPTIMIZE TABLE `" . $GLOBALS['wpdb']->options . "`" );
	}


	

	private static function _export() {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		
		$charset = get_bloginfo( 'charset' );

		
		$xml = '<?xml version="1.0" encoding="' . $charset . '"?>';
		$xml .= "\n<root>";
		$xml .= "\n<options>\n";

		
		$options = wpSEOde_Options::get();

		
		$fields = array_keys( wpSEOde_Options::defaults() );

		
		foreach ( $fields as $field ) {
			
			if ( in_array( $field, array( 'monitor_options', 'snippets_data' ) ) ) {
				continue;
			}

			
			$value = $options[ $field ];

			
			$xml .= "  <" . $field . ">";

			
			if ( is_numeric( $value ) ) {
				$xml .= $value;
			} elseif ( is_array( $value ) ) {
				$xml .= "\n";
				foreach ( $value as $sub_key => $sub_value ) {
					$xml .= "    <item>" . $sub_value . "</item>\n";
				}
				$xml .= "  ";
			} else {
				$xml .= "<![CDATA[" . ( seems_utf8( $value ) ? $value : utf8_encode( $value ) ) . "]]>";
			}

			
			$xml .= "</" . $field . ">\n";
		}

		
		$xml .= "</options>\n";
		$xml .= '</root>';

		
		@ob_end_clean();

		
		nocache_headers();

		
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=wpSEO-' . date_i18n( 'd.m.Y' ) . '.xml' );
		header( 'Content-Length: ' . strlen( $xml ) );
		header( 'Content-type: text/xml; charset=' . $charset, true );

		
		echo $xml;

		
		die();
	}

	private static function _import_yoast() {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		global $wpdb;

		$output = new stdClass;

		@$output->update = 0;
		@$output->ignore = 0;
		@$output->elements = array();

		$output->elements['Posts'] = array();

		
		$source = array(
			'Custom Doctitle'     => '_yoast_wpseo_title',
			'META Description'    => '_yoast_wpseo_metadesc',
			'META Keywords'       => '_yoast_wpseo_metakeywords',
			'Canonical URI'       => '_yoast_wpseo_canonical',
			'Redirect URI'        => '_yoast_wpseo_redirect',
			'OG Title'            => '_yoast_wpseo_opengraph-title',
			'OG Description'      => '_yoast_wpseo_opengraph-description',
			'OG Image'            => '_yoast_wpseo_opengraph-image',
			'Twitter Title'       => '_yoast_wpseo_twitter-title',
			'Twitter Description' => '_yoast_wpseo_twitter-description',
			'Twitter Image'       => '_yoast_wpseo_twitter-image',
		);

		$target = array(
			'Custom Doctitle'     => '_wpseo_edit_title',
			'META Description'    => '_wpseo_edit_description',
			'META Keywords'       => '_wpseo_edit_keywords',
			'Canonical URI'       => '_wpseo_edit_canonical',
			'Redirect URI'        => '_wpseo_edit_redirect',
			'OG Title'            => '_wpseo_edit_og_title',
			'OG Description'      => '_wpseo_edit_og_description',
			'OG Image'            => '_wpseo_edit_og_image',
			'Twitter Title'       => '_wpseo_edit_twittercard_title',
			'Twitter Description' => '_wpseo_edit_twittercard_description',
			'Twitter Image'       => '_wpseo_edit_twittercard_image',
		);


		foreach ( $source as $label => $meta_key ) {


						$result = self::convert_post_data( $source[ $label ], $target[ $label ] );

			$output->elements['Posts'][ $label ] = $result->count;

						if ( is_wp_error( $result ) ) {
				continue;
			}

			

		}
		

		$output->elements['Posts']['Noindex / Nofollow'] = 0;

		$ar_index_noindex = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s",
			'_yoast_wpseo_meta-robots-noindex' ), ARRAY_A );
		foreach ( $ar_index_noindex as $item ) {

			$output->elements['Posts']['Noindex / Nofollow'] ++;

			$noindex  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s and post_id=%d",
				'_yoast_wpseo_meta-robots-noindex', $item['post_id'] ), ARRAY_A );
			$nofollow = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s and post_id=%d",
				'_yoast_wpseo_meta-robots-nofollow', $item['post_id'] ), ARRAY_A );

			if ( isset( $noindex[0] ) ) {
				$noindex = $noindex[0]['meta_value'];
			} else {
				$noindex = false;
			}

			if ( isset( $nofollow[0] ) ) {
				$nofollow = $nofollow[0]['meta_value'];
			} else {
				$nofollow = false;
			}
			switch ( $noindex ) {
				case '1':
					if ( $nofollow == 1 ) {
						$value = '5';
					} else {
						$value = '4';

					}
					break;

				case '2':
					if ( $nofollow == 1 ) {
						$value = '2';
					} else {
						$value = '1';

					}
					break;

				default:
					if ( $nofollow == 1 ) {
						$value = '2';
					} else {
						$value = '1';

					}
			}


			$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta (`post_id`,`meta_key`,`meta_value`) VALUES (%d,%s,%s)  ON DUPLICATE KEY UPDATE `meta_key`=%s",
				$item['post_id'], '_wpseo_edit_robots', $value, $value ) );
		}

		$ar_index_nofollow = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s",
			'_yoast_wpseo_meta-robots-nofollow' ), ARRAY_A );
		foreach ( $ar_index_nofollow as $item ) {
			$noindex  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s and post_id=%d",
				'_yoast_wpseo_meta-robots-noindex', $item['post_id'] ), ARRAY_A );
			$nofollow = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s and post_id=%d",
				'_yoast_wpseo_meta-robots-nofollow', $item['post_id'] ), ARRAY_A );


			if ( isset( $noindex[0] ) ) {
				$noindex = $noindex[0]['meta_value'];
			} else {
				$noindex = false;
			}

			if ( isset( $nofollow[0] ) ) {
				$nofollow = $nofollow[0]['meta_value'];
			} else {
				$nofollow = false;
			}

			switch ( $noindex ) {
				case '1':
					if ( $nofollow == 1 ) {
						$value = '5';
					} else {
						$value = '4';

					}
					break;

				case '2':
					if ( $nofollow == 1 ) {
						$value = '2';
					} else {
						$value = '1';

					}
					break;

				default:
					if ( $nofollow == 1 ) {
						$value = '2';
					} else {
						$value = '1';

					}
			}


			$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta (`post_id`,`meta_key`,`meta_value`) VALUES (%d,%s,%s)  ON DUPLICATE KEY UPDATE `meta_key`=%s",
				$item['post_id'], '_wpseo_edit_robots', $value, $value ) );
		}

		
		$taxonomy_meta_data = get_option( 'wpseo_taxonomy_meta' );
		if ( $taxonomy_meta_data ) {

			$source = array(
				'Custom Doctitle'     => 'wpseo_title',
				'META Description'    => 'wpseo_desc',
				'OG Title'            => 'wpseo_opengraph-title',
				'OG Description'      => 'wpseo_opengraph-description',
				'OG Image'            => 'wpseo_opengraph-image',
				'Twitter Title'       => 'wpseo_twitter-title',
				'Twitter Description' => 'wpseo_twitter-description',
				'Twitter Image'       => 'wpseo_twitter-image',
				'Canonical'           => 'wpseo_canonical',
				'Robots'              => 'wpseo_noindex'
			);

			$target = array(
				'Custom Doctitle'     => 'title',
				'META Description'    => '',
				'OG Title'            => 'og_title',
				'OG Description'      => 'og_desc',
				'OG Image'            => 'og_image',
				'Twitter Title'       => 'twittercard_title',
				'Twitter Description' => 'twittercard_desc',
				'Twitter Image'       => 'twittercard_image',
				'Canonical'           => 'canonical',
				'Robots'              => 'robots'
			);

			$output->elements['Taxonomies'] = array();

			foreach ( $taxonomy_meta_data as $category_type => $data ) {
				$output->elements['Taxonomies'][ $category_type ] = array();

				foreach ( $data as $id => $value ) {
					foreach ( $source as $label => $field ) {
						$output->elements['Taxonomies'][ $category_type ][ $label ] ++;
						if ( $label != 'Robots' ) {

							if ( isset( $value[ $field ] ) ) {
								$field_name = sprintf(
									'wpseo_%s_%d%s',
									$category_type,
									$id,
									( $target[ $label ] != '' ? '_' . $target[ $label ] : '' )
								);

								update_option( $field_name, $value[ $field ] );
							}
						} else {
							if ( isset( $value[ $field ] ) ) {
								switch ( $value[ $field ] ) {
									case 'noindex':
										$value[ $field ] = 3;
										break;
									default:
										$value[ $field ] = 6;
								}

								$field_name = sprintf(
									'wpseo_%s_%d%s',
									$category_type,
									$id,
									( $target[ $label ] != '' ? '_' . $target[ $label ] : '' )
								);

								update_option( $field_name, $value[ $field ] );
							}
						}
					}
				}
			}
		}
		
		$aOpts = get_option( 'wpseo_permalinks' );
		if ( is_array( $aOpts ) && isset( $aOpts['stripcategorybase'] ) ) {
			if ( $aOpts['stripcategorybase'] === true ) {
				wpSEOde_Options::update( array(
						'strip_categorybase'        => 1,
						'redirect_old_categorybase' => 1
					)
				);
			} else {
				wpSEOde_Options::update( array(
						'strip_categorybase'        => 0,
						'redirect_old_categorybase' => 0
					)
				);
			}
		}

		$output->elements['last_update'] = time();

		update_option( 'wpseo_import_results', $output->elements );

		self::$result = $output->update;
	}


	public static function convert_post_data( $old = '', $new = '', $delete_old = false ) {

		global $wpdb;

		$output = new stdClass;
		@$output->count = 0;

		if ( ! $old || ! $new ) {
			$output->WP_Error = 1;

			return $output;
		}

				$ar_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s", $old ),
			ARRAY_A );

		foreach ( $ar_data as $data ) {
			$output->count ++;
			$wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->postmeta (`post_id`,`meta_key`,`meta_value`) VALUES (%d,%s,%s)  ON DUPLICATE KEY UPDATE `meta_value`=%s",
				$data['post_id'], $new, $data['meta_value'], $data['meta_value'] ) );
		}

		

		return $output;

	}

	public static function get_result() {
		return self::$result;
	}
}

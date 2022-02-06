<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Meta {

	
	public static function _add_wpseo_to_array( $aArray ) {
		array_push( $aArray, 'wpseo' );

		return $aArray;
	}

	

	public static function init() {
		
		if ( ! apply_filters( 'wpseo_add_meta_boxes', true ) ) {
			return;
		}

		
		if ( wpSEOde_Feedback::get( 'critical' ) || wpSEOde_License::expired() ) {
			return;
		}

		
		$post_types = get_post_types(
			array(
				'show_ui' => true
			)
		);

		
		if ( empty( $post_types ) ) {
			return;
		}

		
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			
			if ( ! is_object( $post_type_object ) OR ! current_user_can( $post_type_object->cap->edit_posts ) ) {
				continue;
			}

			add_meta_box(
				'wpseo_edit_box',
				'wpSEO',
				array(
					'wpSEOde_Meta',
					'custom_box'
				),
				$post_type
			);
			add_filter( 'postbox_classes_' . $post_type . '_wpseo_edit_box', array(
				__CLASS__,
				'_add_wpseo_to_array'
			) );
		}

		
		add_action(
			'admin_enqueue_scripts',
			array(
				'wpSEOde_Meta',
				'add_resources'
			)
		);
	}


	

	public static function add_resources() {
		wp_enqueue_style(
			'wpseo-meta',
			wpSEOde::plugin_url( 'css/meta.min.css' ),
			false,
			wpSEOde::get_plugin_data( 'Version' )
		);
		wp_enqueue_style(
			'jquery-ui-wpseo',
			wpSEOde::plugin_url( 'css/jquery-ui.min.css' ),
			false,
			wpSEOde::get_plugin_data( 'Version' )
		);
		wp_enqueue_style(
			'jquery-ui-structure-wpseo',
			wpSEOde::plugin_url( 'css/jquery-ui.structure.min.css' ),
			array( 'jquery-ui-wpseo' ),
			wpSEOde::get_plugin_data( 'Version' )
		);
		wp_enqueue_style(
			'jquery-ui-theme-wpseo',
			wpSEOde::plugin_url( 'css/jquery-ui.theme.min.css' ),
			array( 'jquery-ui-wpseo' ),
			wpSEOde::get_plugin_data( 'Version' )
		);
		wp_enqueue_script(
			'jquery-progresspiesvg',
			wpSEOde::plugin_url( 'js/jquery-progresspiesvg/jquery-progresspiesvg-min.js' ),
			array( 'jquery' ),
			wpSEOde::get_plugin_data( 'Version' )
		);
		if ( in_array( wpSEOde::current_page(), array( 'post', 'post-new' ) ) ) {
			wp_enqueue_script(
				'wpseo-meta',
				wpSEOde::plugin_url( 'js/meta.min.js' ),
				array( 'jquery', 'jquery-ui-tabs' ),
				wpSEOde::get_plugin_data( 'Version' )
			);
		}
	}


	

	public static function update_fields( $post_id ) {


		
		if ( empty( $_POST ) ) {
			return $post_id;
		}

				if ( is_multisite() && ms_is_switched() ) {
			return $post_id;
		}

		
		if ( wpSEOde_Feedback::get( 'critical' ) || wpSEOde_License::expired() ) {
			return $post_id;
		}

		
		if ( $_POST['post_ID'] != $post_id ) {
			return $post_id;
		}

		
		if ( empty( $_POST['post_type'] ) ) {
			return $post_id;
		}

		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		
		if ( wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		
		if ( wp_is_post_revision( $post_id ) ) {
			return $post_id;
		}

		
		$post_type_object = get_post_type_object( $_POST['post_type'] );

		
		if ( ! is_object( $post_type_object ) OR ! current_user_can( $post_type_object->cap->edit_posts ) ) {
			return $post_id;
		}

		
		foreach ( wpSEOde_Vars::get( 'custom_fields' ) as $custom_field ) {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX && $_POST['action'] == 'inline-save' && ! in_array( $custom_field, array(
					'_wpseo_edit_title',
					'_wpseo_edit_description'
				) ) ) {
				continue;
				
			} elseif ( empty( $_POST[ $custom_field ] ) ) {
				
				if ( get_post_meta( $post_id, $custom_field, true ) ) {
					delete_post_meta(
						$post_id,
						$custom_field
					);
				}

				
			} else {
				
				$updated_value = $_POST[ $custom_field ];

				
				if ( in_array( $custom_field, array( '_wpseo_edit_canonical', '_wpseo_edit_redirect' ) ) ) {
					$updated_value = esc_url_raw( $updated_value );
				} else {
					$updated_value = sanitize_text_field( $updated_value );
				}

				
				add_post_meta( $post_id, $custom_field, $updated_value, true ) OR update_post_meta( $post_id,
					$custom_field, $updated_value );
			}
		}

		return $post_id;
	}


	

	public static function delete_fields( $post_id ) {
		
		if ( empty( $post_id ) ) {
			return;
		}

		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		
		foreach ( wpSEOde_Vars::get( 'custom_fields' ) as $custom_field ) {
			delete_post_meta(
				$post_id,
				$custom_field
			);
		}
	}


	

	public static function custom_box( $post ) {
		
		
		
		$options = wpSEOde_Options::get();

		$sContent = apply_filters( 'the_content', get_post_field( 'post_content', $post->ID ) );

		?>
        <script type="text/javascript">
            var wpseo_preview_css = ['<?php echo wpSEOde::plugin_url( 'css/preview.min.css' ); ?>', '<?php esc_attr_e( includes_url( 'css/dashicons.min.css' ) ); ?>'];
            var wpseo_preview_type = 'meta';

            var wpseo_ajax_error = '<?php esc_attr_e( 'Autogeneration not available for preview',
				'wpseo' ); ?> <?php wpSEOde::help_icon( 999999 ); ?>';
            var wpseo_content = jQuery('<div/>').html('<?php echo esc_js( $sContent ); ?>').text();
            var wpseo_preview_title = '';
            var wpseo_preview_desc = '';
            var wpseo_preview_og_title = '';
            var wpseo_preview_og_desc = '';
            var wpseo_preview_og_image = '';
            var wpseo_preview_twitter_title = '';
            var wpseo_preview_twitter_desc = '';
            var wpseo_preview_twitter_image = '';

            var wpseo_analyse_dense_ok = '<?php  ?>';
            var wpseo_analyse_dense_nok_less = '<?php _e( 'Hey, try to use your master keyword <b>more</b> often.',
				'wpseo' ); ?>';
            var wpseo_analyse_dense_nok_more = '<?php _e( 'Hey, try to use your master keyword <b>less</b> often.',
				'wpseo' ); ?>';
            var wpseo_analyse_h1_ok = '<?php  ?>';
            var wpseo_analyse_h1_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside main heading.',
				'wpseo' ); ?>';
            var wpseo_analyse_h2_ok = '<?php  ?>';
            var wpseo_analyse_h2_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside further heading(s).',
				'wpseo' ); ?>';
            var wpseo_analyse_1p_ok = '<?php  ?>';
            var wpseo_analyse_1p_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside first paragraph.',
				'wpseo' ); ?>';

            var wpseo_analyse_preview_title_ok = '<?php esc_attr_e( 'You use your master keyword inside your Google title.',
				'wpseo' ); wpSEOde::info_tooltip( __( 'Automatically generated Title can only be rechecked after saving!',
				'wpseo' ) ); ?>';
            var wpseo_analyse_preview_title_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside your Google title.',
				'wpseo' ); wpSEOde::info_tooltip( __( 'Automatically generated Title can only be rechecked after saving!',
				'wpseo' ) ); ?>';
            var wpseo_analyse_preview_desc_ok = '<?php esc_attr_e( 'You use your master keyword inside your Google description.',
				'wpseo' ); wpSEOde::info_tooltip( __( 'Automatically generated Description can only be rechecked after saving!',
				'wpseo' ) ); ?>';
            var wpseo_analyse_preview_desc_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside your Google description.',
				'wpseo' ); wpSEOde::info_tooltip( __( 'Automatically generated Description can only be rechecked after saving!',
				'wpseo' ) ); ?>';

            var wpseo_analyse_image_name_ok = '<?php  ?>';
            var wpseo_analyse_image_name_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside the filename of a picture.',
				'wpseo' ); ?>';
            var wpseo_analyse_image_alt_ok = '<?php  ?>';
            var wpseo_analyse_image_alt_nok = '<?php esc_attr_e( 'You don\'t use your master keyword inside alternative text for a picture.',
				'wpseo' ); ?>';
        </script>

		<?php  ?>
        <table>
            <tr>
                <th>
                    <label for="_wpseo_edit_sitemap"><?php esc_html_e( 'Analysis', 'wpseo' ); ?></label>
                </th>
            </tr>
            <tr>
                <td><label for="_wpseo_edit_keyword_0"><?php _e( 'Haupt-Keyword', 'wpseo' ); ?>:</label><br/>
                    <span id="_wpseo_edit_keyword_0_pp" data-pc="0"
                          style="position: absolute; height: 24px; padding: 2px;"></span>
                    <input type="text" name="_wpseo_edit_keyword_0" id="_wpseo_edit_keyword_0"
                           style="padding-left: 35px;"
                           value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpseo_edit_keyword_0', true ) ) ?>"
                           autocomplete="off"/><br/>
                    <span id="_wpseo_edit_keyword_0_info"></span>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="_wpseo_edit_keyword_0_result"></div>
                </td>
            </tr>
        </table>
        <br/>

		<?php if ( $options['desc_manually'] || $options['title_manually'] || $options['open_graph'] || $twitter_site_account = $options['twitter_site_account'] ) { ?>
            <div id="tabs-wpseo-preview">
                <ul class="category-tabs">
					<?php if ( $options['desc_manually'] || $options['title_manually'] ) { ?>
                        <li><a href="#wpseo-google"><?php _e( 'Google', 'wpseo' ); ?></a></li>
					<?php } ?>
					<?php if ( $options['open_graph'] ) { ?>
                        <li><a href="#wpseo-og"><?php _e( 'Open Graph', 'wpseo' ); ?></a></li>
					<?php } ?>
					<?php if ( $twitter_site_account = $options['twitter_site_account'] ) { ?>
                        <li><a href="#wpseo-twitter">Twitter</a></li>
					<?php } ?>
                </ul>
                <div id="wpseo-google">

					<?php 
					if ( $options['title_manually'] ) { ?>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                                        <span><?php esc_html_e( 'Words', 'wpseo' ) ?>
                                            : <span>0</span> / <?php esc_html_e( 'Chars', 'wpseo' ) ?>: <span
                                                    title="<?php esc_attr_e( 'Recommended',
														'wpseo' ) ?>">70</span> - <span
                                                    title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                                    title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">70</span></span>
                                        <label for="_wpseo_edit_title"><?php esc_html_e( 'Pagetitle',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="_wpseo_edit_title" id="_wpseo_edit_title"
                                           value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpseo_edit_title',
										       true ) ) ?>" autocomplete="off"/>
                                </td>
                            </tr>
                        </table>
					<?php } ?>

					<?php 
					if ( $options['desc_manually'] ) { ?>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                                        <span><?php esc_html_e( 'Words', 'wpseo' ) ?>
                                            : <span>0</span> / <?php esc_html_e( 'Chars', 'wpseo' ) ?>: <span
                                                    title="<?php esc_attr_e( 'Recommended', 'wpseo' ) ?>"
                                                    id="_wpseo_edit_description_chars_limit">150</span> - <span
                                                    title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                                    title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">150</span></span>
                                        <label for="_wpseo_edit_description"><?php esc_html_e( 'Description',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea name="_wpseo_edit_description" id="_wpseo_edit_description" cols="50"
                                              rows="2"><?php echo esc_textarea( get_post_meta( $post->ID,
		                                    '_wpseo_edit_description', true ) ) ?></textarea>
                                </td>
                            </tr>
                        </table>
					<?php } ?>

					<?php if ( $options['desc_manually'] || $options['title_manually'] ) { ?>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <label for="_wpseo_edit_description"><?php esc_html_e( 'Preview',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <iframe id="_wpseo_google_preview" src="about:blank"
                                                style="width: 100%; height: 120px;"></iframe>
                                    </div>
                                    <div><input type="checkbox" id="_wpseo_google_preview_date"
                                                value="1"/> <?php esc_html_e( 'Show Date', 'wpseo' ); ?><br><br>
                                        <input type="hidden" id="_wpseo_google_preview_type" value="desktop"><input
                                                type="button" id="_wpseo_google_preview_type_desktop"
                                                onclick="jQuery('#_wpseo_google_preview_type').val('desktop');"
                                                class="button" value="Desktop"> <input
                                                type="button" id="_wpseo_google_preview_type_mobile"
                                                onclick="jQuery('#_wpseo_google_preview_type').val('mobile');"
                                                class="button" value="Mobile"></div>
                                </td>
                            </tr>
                        </table>
					<?php } ?>

                </div>
                <div id="wpseo-og">

					<?php
					if ( $options['open_graph'] ) {
						if ( $options['open_graph_manually'] ) {
							?>
                            <table>
                                <tr>
                                    <td class="ignore">
                                        <input type="checkbox" name="_wpseo_edit_opengraph" id="_wpseo_edit_opengraph"
                                               value="1" <?php checked( get_post_meta( $post->ID,
											'_wpseo_edit_opengraph', true ), 1 ) ?> />
                                    </td>
                                    <th>
                                        <label for="_wpseo_edit_opengraph"><?php esc_html_e( 'No output of Open Graph',
												'wpseo' ) ?></label>
                                    </th>
                                </tr>
                            </table>
						<?php }
						if ( $options['open_graph_date_disable'] ) {
							?>
                            <table>
                                <tr>
                                    <td class="ignore">
                                        <input type="checkbox" name="_wpseo_edit_opengraph_date_disable"
                                               id="_wpseo_edit_opengraph_date_disable"
                                               value="1" <?php checked( get_post_meta( $post->ID,
											'_wpseo_edit_opengraph_date_disable', true ), 1 ) ?> />
                                    </td>
                                    <th>
                                        <label for="_wpseo_edit_opengraph_date_disable"><?php esc_html_e( 'No output of Open Graph Date',
												'wpseo' ) ?></label>
                                    </th>
                                </tr>
                            </table>
						<?php }
						if ( $options['open_graph_title_manually'] ) {
							?>
                            <table>
                                <tr>
                                    <th>
                                        <div>
                                            <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                                            <span><?php esc_html_e( 'Words', 'wpseo' ) ?>
                                                : <span>0</span> / <?php esc_html_e( 'Chars', 'wpseo' ) ?>: <span
                                                        title="<?php esc_attr_e( 'Recommended', 'wpseo' ) ?>">70</span> - <span
                                                        title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                                        title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">70</span></span>
                                            <label for="_wpseo_edit_og_title">Open Graph <?php esc_html_e( 'Pagetitle',
													'wpseo' ) ?></label>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" name="_wpseo_edit_og_title" id="_wpseo_edit_og_title"
                                               value="<?php echo esc_attr( get_post_meta( $post->ID,
											       '_wpseo_edit_og_title', true ) ) ?>" autocomplete="off"/>
                                    </td>
                                </tr>
                            </table>
						<?php } ?>
						<?php
						if ( $options['open_graph_description_manually'] ) {
							?>
                            <table>
                                <tr>
                                    <th>
                                        <div>
                                            <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                                            <span><?php esc_html_e( 'Words', 'wpseo' ) ?>
                                                : <span>0</span> / <?php esc_html_e( 'Chars', 'wpseo' ) ?>: <span
                                                        title="<?php esc_attr_e( 'Recommended', 'wpseo' ) ?>">150</span> - <span
                                                        title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                                        title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">150</span></span>
                                            <label for="_wpseo_edit_og_description">Open
                                                Graph <?php esc_html_e( 'Description', 'wpseo' ) ?></label>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <textarea name="_wpseo_edit_og_description" id="_wpseo_edit_og_description"
                                                  cols="50" rows="2"><?php echo esc_textarea( get_post_meta( $post->ID,
		                                        '_wpseo_edit_og_description', true ) ) ?></textarea>
                                    </td>
                                </tr>
                            </table>
						<?php } ?>
						<?php
						if ( $options['open_graph_image_manually'] ) {
							?>
                            <table>
                                <tr>
                                    <th>
                                        <div>
                                            <label for="_wpseo_edit_og_image"><?php esc_html_e( 'Open Graph previewimage',
													'wpseo' ) ?></label>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>

										<?php
										wpSEOde_Tax::wpseo_image_upload( '_wpseo_edit_og_image', '_wpseo_edit_og_image',
											esc_html__( 'Select or upload image', 'wpseo' ),
											esc_attr( get_post_meta( $post->ID, '_wpseo_edit_og_image', true ) ) );
										?>
                                    </td>
                                </tr>
                            </table>
						<?php } ?>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <label for="_wpseo_facebook_preview"><?php esc_html_e( 'Facebook Preview',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <iframe id="_wpseo_facebook_preview" src="about:blank"
                                                style="width: 100%; height: 340px;"></iframe>
                                    </div>
                                </td>
                            </tr>
                        </table>
					<?php } ?>


                </div>
                <div id="wpseo-twitter">

					<?php
					
					if ( $twitter_site_account = $options['twitter_site_account'] && $options['twitter_cards_manually'] ) { ?>
                        <table>
                            <tr>
                                <td class="ignore">
                                    <input type="checkbox" name="_wpseo_edit_twittercard" id="_wpseo_edit_twittercard"
                                           value="1" <?php checked( get_post_meta( $post->ID, '_wpseo_edit_twittercard',
										true ), 1 ) ?> />
                                </td>
                                <th>
                                    <label for="_wpseo_edit_twittercard"><?php esc_html_e( 'No output of Twittercard',
											'wpseo' ) ?></label>
                                </th>
                            </tr>
                        </table>
					<?php } ?>
					<?php 
					if ( $twitter_site_account = $options['twitter_site_account'] && $options['twitter_authorship_manually'] ) { ?>
                        <table>
                            <tr>
                                <td class="ignore">
                                    <input type="checkbox" name="_wpseo_edit_twittercard_authorship"
                                           id="_wpseo_edit_twittercard_authorship"
                                           value="1" <?php checked( get_post_meta( $post->ID,
										'_wpseo_edit_twittercard_authorship', true ), 1 ) ?> />
                                </td>
                                <th>
                                    <label for="_wpseo_edit_twittercard_authorship"><?php esc_html_e( 'No output of author in Twittercard',
											'wpseo' ) ?></label>
                                </th>
                            </tr>
                        </table>
					<?php } ?>
					<?php
					if ( $twitter_site_account = $options['twitter_site_account'] ) { ?>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                                        <span><?php esc_html_e( 'Words', 'wpseo' ) ?>
                                            : <span>0</span> / <?php esc_html_e( 'Chars', 'wpseo' ) ?>: <span
                                                    title="<?php esc_attr_e( 'Recommended',
														'wpseo' ) ?>">70</span> - <span
                                                    title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                                    title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">70</span></span>
                                        <label for="_wpseo_edit_twittercard_title">Twittercard <?php esc_html_e( 'Pagetitle',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" name="_wpseo_edit_twittercard_title"
                                           id="_wpseo_edit_twittercard_title"
                                           value="<?php echo esc_attr( get_post_meta( $post->ID,
										       '_wpseo_edit_twittercard_title', true ) ) ?>" autocomplete="off"/>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                                        <span><?php esc_html_e( 'Words', 'wpseo' ) ?>
                                            : <span>0</span> / <?php esc_html_e( 'Chars', 'wpseo' ) ?>: <span
                                                    title="<?php esc_attr_e( 'Recommended',
														'wpseo' ) ?>">150</span> - <span
                                                    title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                                    title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">150</span></span>
                                        <label for="_wpseo_edit_twittercard_description">Twittercard <?php esc_html_e( 'Description',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea name="_wpseo_edit_twittercard_description"
                                              id="_wpseo_edit_twittercard_description" cols="50"
                                              rows="2"><?php echo esc_textarea( get_post_meta( $post->ID,
		                                    '_wpseo_edit_twittercard_description', true ) ) ?></textarea>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <label for="_wpseo_edit_twittercard_image"><?php esc_html_e( 'Twittercard previewimage',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>

									<?php
									wpSEOde_Tax::wpseo_image_upload( '_wpseo_edit_twittercard_image',
										'_wpseo_edit_twittercard_image',
										esc_html__( 'Select or upload image', 'wpseo' ),
										esc_attr( get_post_meta( $post->ID, '_wpseo_edit_twittercard_image', true ) ) );
									?>
                                </td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <th>
                                    <div>
                                        <label for="_wpseo_twitter_preview"><?php esc_html_e( 'Twittercard Preview',
												'wpseo' ) ?></label>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <iframe id="_wpseo_twitter_preview" src="about:blank"
                                                style="width: 100%; height: 130px;"></iframe>
                                    </div>
                                </td>
                            </tr>
                        </table>
					<?php } ?>


                </div>
            </div>
		<?php } ?>

		<?php 
		if ( $options['key_manually'] ) { ?>
            <table>
                <tr>
                    <th>
                        <div>
                            <strong><?php esc_html_e( 'Please note the limit', 'wpseo' ) ?></strong>
                            <span><?php esc_html_e( 'Words', 'wpseo' ) ?>: <span>0</span> / <?php esc_html_e( 'Chars',
									'wpseo' ) ?>: <span title="<?php esc_attr_e( 'Recommended', 'wpseo' ) ?>">70</span> - <span
                                        title="<?php esc_attr_e( 'Typed', 'wpseo' ) ?>">0</span> = <span
                                        title="<?php esc_attr_e( 'Left', 'wpseo' ) ?>">70</span></span>
                            <label for="_wpseo_edit_keywords"><?php echo( $options['key_news'] ? 'News Keywords' : 'Keywords' ) ?></label>
                        </div>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="_wpseo_edit_keywords" id="_wpseo_edit_keywords"
                               value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpseo_edit_keywords',
							       true ) ) ?>" autocomplete="off"/>
                    </td>
                </tr>
            </table>
		<?php } ?>

		<?php 
		if ( $options['sitemap_manually'] ) { ?>
            <table>
                <tr>
                    <th>
                        <label for="_wpseo_edit_sitemap"><?php esc_html_e( 'Sitemap', 'wpseo' ) ?></label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <select name="_wpseo_edit_sitemap">
                            <option value=""><?php echo sprintf( '%s:&nbsp;(%s)', esc_attr__( 'Predefined', 'wpseo' ),
									__( 'include in Sitemap', 'wpseo' ) ) ?></option>
                            <option disabled="disabled">---</option>
                            <option value="1" <?php selected( get_post_meta( $post->ID, '_wpseo_edit_sitemap', '1' ),
								'1' ) ?>><?php _e( 'include in Sitemap', 'wpseo' ); ?></option>
                            <option value="2" <?php selected( get_post_meta( $post->ID, '_wpseo_edit_sitemap', '2' ),
								'2' ) ?>><?php _e( 'exclude from Sitemap', 'wpseo' ); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
		<?php } ?>

		<?php 
		if ( $options['noindex_manually'] ) { ?>
            <table>
                <tr>
                    <th>
                        <label for="_wpseo_edit_robots"><?php esc_html_e( 'Robots', 'wpseo' ) ?></label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <select name="_wpseo_edit_robots">
                            <option value=""><?php echo sprintf( '%s:&nbsp;%s&nbsp;(%s)',
									esc_attr__( 'Predefined', 'wpseo' ), esc_html__( wpSEOde_Vars::get( 'meta_robots',
										( ( isset( $options['noindex_age'] ) && $options['noindex_age'] == '1' && abs( current_time( 'timestamp' ) ) - get_the_time( 'U' ) > (int) apply_filters( 'wpseo_set_noindex_age',
												6 ) * 30 * 86400 ) ? '4' : $options['noindex_single'] ) ), 'wpseo' ),
									esc_html__( wpSEOde_Vars::get( 'meta_robots_desc',
										( ( isset( $options['noindex_age'] ) && $options['noindex_age'] == '1' && abs( current_time( 'timestamp' ) ) - get_the_time( 'U' ) > (int) apply_filters( 'wpseo_set_noindex_age',
												6 ) * 30 * 86400 ) ? '4' : $options['noindex_single'] ) ),
										'wpseo' ) ) ?></option>
                            <option disabled="disabled">---</option>
							<?php foreach ( wpSEOde_Vars::get( 'meta_robots' ) as $k => $v ) { if( $k == 0 ) { continue; } ?>
                                <option value="<?php echo esc_attr( $k ) ?>" <?php selected( get_post_meta( $post->ID,
									'_wpseo_edit_robots', true ), $k ) ?>>
									<?php echo sprintf( '%s&nbsp;(%s)', esc_html__( $v, 'wpseo' ),
										esc_html__( wpSEOde_Vars::get( 'meta_robots_desc', $k ), 'wpseo' ) ) ?>
                                </option>
							<?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
		<?php } ?>

		<?php 
		if ( $options['canonical_manually'] ) { ?>
            <table>
                <tr>
                    <th>
                        <label for="_wpseo_edit_canonical"><?php esc_html_e( 'Canonical URL', 'wpseo' ) ?></label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="_wpseo_edit_canonical" id="_wpseo_edit_canonical"
                               value="<?php echo esc_url( get_post_meta( $post->ID, '_wpseo_edit_canonical',
							       true ) ) ?>"/>
                    </td>
                </tr>
            </table>
		<?php } ?>

		<?php 
		if ( $options['redirect_manually'] ) { ?>
            <table>
                <tr>
                    <th>
                        <label for="_wpseo_edit_redirect"><?php esc_html_e( 'Redirect URL', 'wpseo' ) ?></label>
                    </th>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="_wpseo_edit_redirect" id="_wpseo_edit_redirect"
                               value="<?php echo esc_url( get_post_meta( $post->ID, '_wpseo_edit_redirect',
							       true ) ) ?>"/>
                    </td>
                </tr>
            </table>
		<?php } ?>
		<?php 
		if ( $options['authorship_manually'] ) { ?>
            <table>
                <tr>
                    <td class="ignore">
                        <input type="checkbox" name="_wpseo_edit_authorship" id="_wpseo_edit_authorship"
                               value="1" <?php checked( get_post_meta( $post->ID, '_wpseo_edit_authorship', true ),
							1 ) ?> />
                    </td>
                    <th>
                        <label for="_wpseo_edit_authorship"><?php esc_html_e( 'No output of Google+ Authorship',
								'wpseo' ) ?></label>
                    </th>
                </tr>
            </table>
		<?php } ?>

		<?php 
		if ( $options['ignore_manually'] ) { ?>
            <table>
                <tr>
                    <td class="ignore">
                        <input type="checkbox" name="_wpseo_edit_ignore" id="_wpseo_edit_ignore"
                               value="1" <?php checked( get_post_meta( $post->ID, '_wpseo_edit_ignore', true ),
							1 ) ?> />
                    </td>
                    <th>
                        <label for="_wpseo_edit_ignore"><?php esc_html_e( 'No optimization', 'wpseo' ) ?></label>
                    </th>
                </tr>
            </table>
		<?php } 
	}


	

	public static function add_column_labels( $columns ) {
		
		if ( ! apply_filters( 'wpseo_add_meta_boxes', true ) ) {
			return $columns;
		}

		
		$options = wpSEOde_Options::get();

		
		if ( $options['title_manually'] ) {
			$columns['wpseo_title'] = esc_html__( 'Pagetitle', 'wpseo' );
		}

		
		if ( $options['desc_manually'] ) {
			$columns['wpseo_desc'] = esc_html__( 'Description', 'wpseo' );
		}

		
		if ( $options['key_manually'] ) {
			$columns['wpseo_keywords'] = esc_html__( 'Keywords', 'wpseo' );
		}

		
		if ( $options['noindex_manually'] ) {
			$columns['wpseo_robots'] = esc_html__( 'Robots', 'wpseo' );
		}

		
		if ( $options['canonical_manually'] ) {
			$columns['wpseo_canonical'] = esc_html__( 'Canonical URL', 'wpseo' );
		}

		
		if ( $options['redirect_manually'] ) {
			$columns['wpseo_redirect'] = esc_html__( 'Redirect URL', 'wpseo' );
		}

		
		if ( $options['ignore_manually'] ) {
			$columns['wpseo_ignore'] = esc_html__( 'Blacklist', 'wpseo' );
		}

		return $columns;
	}


	

	public static function add_column_values( $column, $post_id ) {
		
		if ( empty( $post_id ) ) {
			return;
		}

		
		if ( ! apply_filters( 'wpseo_add_meta_boxes', true ) ) {
			return;
		}

		switch ( $column ) {
			
			case 'wpseo_keyword_0':
				if ( $keyword_0 = get_post_meta( $post_id, '_wpseo_edit_keyword_0', true ) ) {
					echo esc_html( $keyword_0 );
				}
				break;

			
			case 'wpseo_title':
				$sTitle = get_post_meta( $post_id, '_wpseo_edit_title', true );
				if ( empty( $sTitle ) ) {
					echo '<span style="color:red;" title="' . esc_attr__( 'No Title set (automatic generation)!',
							'wpseo' ) . '">&#9679;</span>';
				} elseif ( strlen( $sTitle ) < 50 ) {
					echo '<span style="color:red;" title="' . esc_attr__( 'Title shorter than recommended!',
							'wpseo' ) . '">&#9679;</span>';
				} else {
					echo '<span style="color:green;" title="' . esc_attr__( 'Title OK', 'wpseo' ) . '">&#9679;</span>';
				}
				if ( $sTitle ) {
					echo '&nbsp;<span class="txtval">' . esc_html( $sTitle ) . '</span>';
				}
				break;

			
			case 'wpseo_desc':
				$sDesc = get_post_meta( $post_id, '_wpseo_edit_description', true );
				if ( empty( $sDesc ) ) {
					echo '<span style="color:red;" title="' . esc_attr__( 'No Description set (automatic generation)!',
							'wpseo' ) . '">&#9679;</span>';
				} elseif ( strlen( $sDesc ) < 130 ) {
					echo '<span style="color:red;" title="' . esc_attr__( 'Description shorter than recommended!',
							'wpseo' ) . '">&#9679;</span>';
				} else {
					echo '<span style="color:green;" title="' . esc_attr__( 'Description OK',
							'wpseo' ) . '">&#9679;</span>';
				}
				if ( $sDesc ) {
					echo '&nbsp;<span class="txtval">' . esc_html( $sDesc ) . '</span>';
				}
				break;

			
			case 'wpseo_keywords':
				if ( $keywords = get_post_meta( $post_id, '_wpseo_edit_keywords', true ) ) {
					echo '<span class="txtval">' . esc_html( $keywords ) . '</span>';
				}
				break;

			
			case 'wpseo_robots':
				if ( $robots = get_post_meta( $post_id, '_wpseo_edit_robots', true ) ) {
					echo '<span class="txtval">' . esc_html( wpSEOde_Vars::get( 'meta_robots', $robots ) ) . '</span>';
				}
				break;

			
			case 'wpseo_canonical':
				if ( $canonical = get_post_meta( $post_id, '_wpseo_edit_canonical', true ) ) {
					echo '<span class="txtval">' . make_clickable( esc_url( $canonical ) ) . '</span>';
				}
				break;

			
			case 'wpseo_redirect':
				if ( $redirect = get_post_meta( $post_id, '_wpseo_edit_redirect', true ) ) {
					echo '<span class="txtval">' . make_clickable( esc_url( $redirect ) ) . '</span>';
				}
				break;

			
			case 'wpseo_ignore':
				if ( get_post_meta( $post_id, '_wpseo_edit_ignore', true ) ) {
					echo '+';
				}
				break;

			default:
				break;
		}
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
                            var image_url = uploaded_image.toJSON().url;
                            $('#<?php echo $id;?>').val(image_url).trigger('change');
                        });
                });
            });
        </script>
		<?php
	}

	public static function post_add_quick_edit( $sColumn, $post_type ) {
		if ( ! in_array( $sColumn, array( 'wpseo_title', 'wpseo_desc' ) ) || wpSEOde_Cache::get( 'quickedit' ) ) {
			return;
		}
		global $post;
		$options = wpSEOde_Options::get();
		wpSEOde_Cache::set( 'quickedit', true );
		$sTitle = get_post_meta( $post->ID, '_wpseo_edit_title', true );
		$sDescription = get_post_meta( $post->ID, '_wpseo_edit_description', true );
		?>
        <br style="clear: both;">
        <fieldset class="inline-edit-col-left">
            <legend class="inline-edit-legend">wpSEO</legend>
			<?php
			if ( $options['title_manually'] ) {
				?>
                <div class="inline-edit-col">
                    <label>
                        <span class="title"><?php esc_html_e( 'Pagetitle', 'wpseo' ) ?></span>
                        <span class="input-text-wrap">
					<input type="text" value="<?php esc_attr_e( $sTitle ); ?>" name="_wpseo_edit_title">
				</span>
                    </label>
                </div>
				<?php
			}
			if ( $options['desc_manually'] ) {
				?>
                <div class="inline-edit-col">
                    <label>
                        <span class="title"><?php esc_html_e( 'Description', 'wpseo' ) ?></span>
                        <span class="input-text-wrap">
					<input type="text" value="<?php esc_attr_e( $sDescription ); ?>" name="_wpseo_edit_description">
				</span>
                    </label>
                </div>
				<?php
			}
			?>
        </fieldset>
		<?php
	}
}
<?php



defined( 'ABSPATH' ) OR exit;



class wpSEOde_Tax
{


	

	public static function init()
	{
		
		if ( wpSEOde_Feedback::get('critical') || wpSEOde_License::expired() ) {
			return;
		}


        
		
        if ( ! $tax = self::_get_current_tax() ) {
            return;
        }

        
        if ( ! current_user_can( $tax->cap->assign_terms ) ) {
            return;
        }

        
        self::add_actions( $tax->name );

		wp_enqueue_style(
			'wpseo-meta',
			wpSEOde::plugin_url('css/meta.min.css'),
			false,
			wpSEOde::get_plugin_data('Version')
		);
		wp_enqueue_style(
			'jquery-ui-wpseo',
			wpSEOde::plugin_url('css/jquery-ui.min.css'),
			false,
			wpSEOde::get_plugin_data('Version')
		);
		wp_enqueue_style(
			'jquery-ui-structure-wpseo',
			wpSEOde::plugin_url('css/jquery-ui.structure.min.css'),
			array( 'jquery-ui-wpseo' ),
			wpSEOde::get_plugin_data('Version')
		);
		wp_enqueue_style(
			'jquery-ui-theme-wpseo',
			wpSEOde::plugin_url('css/jquery-ui.theme.min.css'),
			array( 'jquery-ui-wpseo' ),
			wpSEOde::get_plugin_data('Version')
		);
		wp_enqueue_script(
			'wpseo-meta',
			wpSEOde::plugin_url('js/meta.min.js'),
			array('jquery', 'jquery-ui-tabs'),
			wpSEOde::get_plugin_data('Version')
		);

	}

    
    public static function add_actions( $sTax )
    {

		
		add_action(
			$sTax. '_edit_form_fields',
			array(
				__CLASS__,
				'add_fields'
			)
		);

        add_action(
            $sTax. '_add_form_fields',
            array(
                __CLASS__,
                'add_creation_fields'
            )
        );

		
        add_action(
			'edit_' .$sTax,
			array(
				__CLASS__,
				'save_fields'
			)
		);

        add_action(
            'create_' .$sTax,
            array(
                __CLASS__,
                'create_fields'
            )
        );
    }

    

    private static function _get_current_tax()
    {
        if ( ! empty($_POST['taxonomy']) ) {
            $tax = $_POST['taxonomy'];
        } elseif ( ! empty($GLOBALS['taxnow']) ) {
            $tax = $GLOBALS['taxnow'];
        } else {
            return false;
        }

        if ( sanitize_title_with_dashes($tax) !== $tax ) {
            return false;
        }

        if ( ! taxonomy_exists($tax) ) {
            return false;
        }

        return get_taxonomy($tax);
    }


    

	private static function meta_data_name($term, $suffix = null)
	{
        if(is_object($term))
        {
            return sprintf(
                'wpseo_%s_%d%s',
                $term->taxonomy,
                $term->term_id,
                ( $suffix ? '_' .$suffix : '' )
            );
        }
        else
        {
            return false;
        }
	}


	

	public static function get_meta_data($term, $suffix = null)
	{
		return get_option(
			self::meta_data_name($term, $suffix)
		);
	}


	

	private static function update_meta_data($term, $suffix = null, $value)
	{
		
		$field = self::meta_data_name($term, $suffix);

		if ( empty($value) ) {
			delete_option($field);
		} else {
			update_option( $field, $value );
		}
	}


    

    private static function create_meta_data($taxonomy,$term_id, $suffix = null, $value)
    {
        if ( !empty($value) ) {

            
            $field = sprintf(
                'wpseo_%s_%d%s',
                $taxonomy,
                $term_id,
                ( $suffix ? '_' .$suffix : '' )
            );

            update_option( 'wp_seo_debug_value', $field );


            update_option( $field, $value );
        }
    }


	

	public static function add_fields($term)
	{
        
        $options = wpSEOde_Options::get(); ?>
        <script type="text/javascript">
            var wpseo_preview_css = [ '<?php echo wpSEOde::plugin_url('css/preview.min.css'); ?>', '<?php esc_attr_e( includes_url('css/dashicons.min.css') ); ?>' ];
            var wpseo_preview_type = 'tax';

            var wpseo_ajax_error = '<?php esc_attr_e( 'Autogeneration not available for preview', 'wpseo' ); ?> <?php wpSEOde::help_icon(999999); ?>';
            var wpseo_preview_title = '';
            var wpseo_preview_desc = '';
            var wpseo_preview_og_title = '';
            var wpseo_preview_og_desc = '';
            var wpseo_preview_og_image = '';
            var wpseo_preview_twitter_title = '';
            var wpseo_preview_twitter_desc = '';
            var wpseo_preview_twitter_image = '';
        </script>
        <?php

        if ( $options['tax_title_manually'] || $options['tax_manually'] || $options['open_graph'] || $options['twitter_site_account'] != '' )
        {
?>
        <tr class="form-field">
            <td colspan="2">
                <div id="tabs-wpseo-preview">
                    <ul class="category-tabs">
                    <?php if ( $options['tax_title_manually'] || $options['tax_manually'] ) { ?>
                        <li><a href="#wpseo-google"><?php _e( 'Google', 'wpseo' ); ?></a></li>
                    <?php } ?>
                    <?php if ( $options['open_graph'] ) { ?>
                        <li><a href="#wpseo-og"><?php _e( 'Open Graph', 'wpseo' ); ?></a></li>
                    <?php } ?>
                    <?php if ( $options['twitter_site_account'] != '' ) { ?>
                        <li><a href="#wpseo-twitter">Twitter</a></li>
                    <?php } ?>
                    </ul>
                    <table id="wpseo-google" class="form-table">
                    <?php if ( $options['tax_title_manually'] ) { ?>
                        <tr class="form-field">
                            <th scope="row" valign="top">
                                <label>
                                    <?php esc_html_e('Title', 'wpseo') ?>
                                </label>
                            </th>
                            <td>
                                <input id="tag-wpseo-title" type="text" name="wpseo_tax_title" value="<?php echo esc_attr( self::get_meta_data($term, 'title') ) ?>">
                                <br />
                                <span class="description"><?php esc_html_e('The use of the title can be edited under wpSEO -> Pagetitle.', 'wpseo') ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if ( $options['tax_manually'] ) { ?>
                        <tr class="form-field">
                            <th scope="row" valign="top">
                                <label>
                                    <?php esc_html_e('Short description', 'wpseo') ?>
                                </label>
                            </th>
                            <td>
                                <textarea id="tag-wpseo-desc" type="text" size="40" rows="5" name="wpseo_tax_short"><?php echo esc_attr( self::get_meta_data($term, null) ) ?></textarea>
                                <br />
                                <span class="description"><?php esc_html_e('The short description can be used in wpSEO plugin.', 'wpseo') ?></span>
                            </td>
                        </tr>
                    <?php } ?>
                    <?php if( $options['tax_title_manually'] || $options['tax_manually'] ) { ?>
                        <tr class="form-field">
                            <th scope="row" valign="top" colspan="2">
                                <label for="_wpseo_edit_description"><?php esc_html_e('Preview', 'wpseo') ?>:</label><br />
                                <iframe id="_wpseo_google_preview" src="about:blank" style="width: 100%; height: 120px;"></iframe><br />
                                <input type="checkbox" id="_wpseo_google_preview_date" value="1" /> <?php esc_html_e( 'Show Date', 'wpseo' ); ?><br><br>
                                <input type="hidden" id="_wpseo_google_preview_type" value="desktop"><input type="button" id="_wpseo_google_preview_type_desktop" onclick="jQuery('#_wpseo_google_preview_type').val('desktop');" class="button" value="Desktop"> <input type="button"  id="_wpseo_google_preview_type_mobile" onclick="jQuery('#_wpseo_google_preview_type').val('mobile');" class="button" value="Mobile">
                            </th>
                        </tr>
                    <?php } ?>
                    </table>

                    <table id="wpseo-og" class="form-table">
                        <?php if ( $options['open_graph'] ) { ?>
                        <?php
                        if($options['open_graph_manually']==1)
                        {
                            ?>
                                <tr class="form-field">
                                    <th scope="row" valign="top">
                                        <label for="tag-wpseo-og">
                                            <?php esc_html_e('No output of Open Graph', 'wpseo') ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input id="tag-wpseo-og" type="checkbox" name="wpseo_tax_og_disable" value="1" <?php echo (esc_attr( self::get_meta_data($term, 'og_disable')?'checked="checked"':'' )) ?>">
                                    </td>
                                </tr>
                            <?php
                        }
                        if($options['open_graph_date_disable']==1)
                        {
                            ?>
                                <tr class="form-field">
                                    <th scope="row" valign="top">
                                        <label for="tag-wpseo-og-date">
                                            <?php esc_html_e('No output of Open Graph Date', 'wpseo') ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input id="tag-wpseo-og-date" type="checkbox" name="wpseo_tax_og_date_disable" value="1" <?php echo (esc_attr( self::get_meta_data($term, 'og_date_disable')?'checked="checked"':'' )) ?>">
                                    </td>
                                </tr>
                            <?php
                        }
                            ?>
                        <?php
                        if ( $options['open_graph_title_manually'] ) {
                        ?>
                        <tr class="form-field">
                            <th scope="row" valign="top">
                                <label for="tag-wpseo-og-title">
                                    <?php esc_html_e('Open Graph title', 'wpseo') ?>
                                </label>
                            </th>
                            <td>
                            <input id="tag-wpseo-og-title" type="text" size="40" name="wpseo_tax_og_title" value="<?php echo esc_attr( self::get_meta_data($term, 'og_title') ) ?>">
                            <br />
                            <span class="description"><?php esc_html_e('This title will be used on Facebook & Google+', 'wpseo') ?></span>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php
                        if ( $options['open_graph_description_manually'] ) {
                        ?>
                        <tr class="form-field">
                        <th scope="row" valign="top">
                            <label for="tag-wpseo-og-desc">
                                <?php esc_html_e('Open Graph description', 'wpseo') ?>
                            </label>
                        </th>
                        <td>
                            <textarea id="tag-wpseo-og-desc" type="text" size="40" rows="5" name="wpseo_tax_og_desc"><?php echo esc_attr( self::get_meta_data($term, 'og_desc') ) ?></textarea>
                            <br />
                            <span class="description"><?php esc_html_e('This description will be used on Facebook & Google+', 'wpseo') ?></span>
                        </td>
                        </tr>
                        <?php } ?>
                        <?php
                        if ( $options['open_graph_image_manually'] ) {
                        ?>
                        <tr class="form-field">
                            <th scope="row" valign="top">
                                <label for="tag-wpseo-og-image">
                                    <?php esc_html_e('Open Graph previewimage', 'wpseo') ?>
                                </label>
                            </th>
                            <td>

                                <?php
                                wpSEOde_Tax::wpseo_image_upload('tag-wpseo-og-image','wpseo_tax_og_image',esc_html__('Select or upload image', 'wpseo'),self::get_meta_data($term, 'og_image'));
                                ?>
                                <br />
                                <span class="description"><?php esc_html_e('This image will be used on Facebook & Google+', 'wpseo') ?></span>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr class="form-field">
                            <th scope="row" valign="top" colspan="2">
                                <label for="_wpseo_facebook_preview"><?php esc_html_e('Facebook Preview', 'wpseo') ?></label><br />
                                <iframe id="_wpseo_facebook_preview" src="about:blank" style="width: 100%; height: 340px;"></iframe>
                            </th>
                        </tr>
                        <?php } ?>
                    </table>
                    <table id="wpseo-twitter" class="form-table">
                        <?php if ( $options['twitter_site_account']!='' ) {
                            if($options['twitter_cards_manually']==1)
                            {
                                ?>
                                <tr class="form-field">
                                    <th scope="row" valign="top">
                                        <label for="tag-wpseo-twittercard">
                                            <?php esc_html_e('No output of Twittercard', 'wpseo') ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input id="tag-wpseo-twittercard" type="checkbox" name="wpseo_tax_twittercard_disable" value="1" <?php echo (esc_attr( self::get_meta_data($term, 'twittercard_disable')?'checked="checked"':'' )) ?>">
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr class="form-field">
                                <th scope="row" valign="top">
                                    <label for="tag-wpseo-twittercard-title">
                                        <?php esc_html_e('Twittercard title', 'wpseo') ?>
                                    </label>
                                </th>
                                <td>
                                    <input id="tag-wpseo-twittercard-title" type="text" size="40" name="wpseo_tax_twittercard_title" value="<?php echo esc_attr( self::get_meta_data($term, 'twittercard_title') ) ?>">
                                    <br />
                                    <span class="description"><?php esc_html_e('This title will be used on Twitter', 'wpseo') ?></span>
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row" valign="top">
                                    <label for="tag-wpseo-twittercard-desc">
                                        <?php esc_html_e('Twittercard description', 'wpseo') ?>
                                    </label>
                                </th>
                                <td>
                                    <textarea id="tag-wpseo-twittercard-desc" type="text" size="40" rows="5" name="wpseo_tax_twittercard_desc"><?php echo esc_attr( self::get_meta_data($term, 'twittercard_desc') ) ?></textarea>
                                    <br />
                                    <span class="description"><?php esc_html_e('This description will be used on Twitter', 'wpseo') ?></span>
                                </td>
                            </tr>

                            <tr class="form-field">
                                <th scope="row" valign="top">
                                    <label for="tag-wpseo-twittercard-image">
                                        <?php esc_html_e('Twittercard previewimage', 'wpseo') ?>
                                    </label>
                                </th>
                                <td>

                                    <?php
                                    wpSEOde_Tax::wpseo_image_upload('tag-wpseo-twittercard-image','wpseo_tax_twittercard_image',esc_html__('Select or upload image', 'wpseo'),self::get_meta_data($term, 'twittercard_image'));
                                    ?>
                                    <br />
                                    <span class="description"><?php esc_html_e('This image will be used on Twitter', 'wpseo') ?></span>
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row" valign="top" colspan="2">
                                    <label for="_wpseo_twitter_preview"><?php esc_html_e('Twittercard Preview', 'wpseo') ?></label><br />
                                    <iframe id="_wpseo_twitter_preview" src="about:blank" style="width: 100%; height: 130px;"></iframe>
                                </th>
                            </tr>
                        <?php } ?>
                    </table>
            </td>
        </tr>
<?php
        }

        if ( $options['canonical_manually'] ) { ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label>
                        <?php esc_html_e('Canonical', 'wpseo') ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="wpseo_tax_canonical" value="<?php echo esc_attr( self::get_meta_data($term, 'canonical') ) ?>">
                    <br />
                    <span class="description"><?php esc_html_e('The canonical URL', 'wpseo') ?></span>
                </td>
            </tr>
        <?php }

        if ( $options['redirect_manually'] ) { ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label>
                        <?php esc_html_e('Redirect', 'wpseo') ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="wpseo_tax_redirect" value="<?php echo esc_attr( self::get_meta_data($term, 'redirect') ) ?>">
                    <br />
                    <span class="description"><?php esc_html_e('Redirect to this URL', 'wpseo') ?></span>
                </td>
            </tr>
        <?php }

        if ( $options['tax_robots_manually'] ) { ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label>
                        <?php esc_html_e('Robots', 'wpseo') ?>
                    </label>
                </th>
                <td>
                    <select name="wpseo_tax_robots">
                        <option value=""></option>
                        <?php foreach (wpSEOde_Vars::get('meta_robots') as $k => $v) { ?>
                            <option value="<?php echo esc_attr($k) ?>" <?php selected( self::get_meta_data($term, 'robots'), $k ) ?>>
                                <?php echo sprintf('%s&nbsp;(%s)', $v, esc_html__( wpSEOde_Vars::get('meta_robots_desc', $k), 'wpseo')) ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        <?php }
    }

    

    public static function add_creation_fields($term)
    {
        
        $options = wpSEOde_Options::get(); ?>
        <script type="text/javascript">
            var wpseo_preview_css = [ '<?php echo wpSEOde::plugin_url('css/preview.min.css'); ?>', '<?php esc_attr_e( includes_url('css/dashicons.min.css') ); ?>' ];
            var wpseo_preview_type = 'tax';

            var wpseo_ajax_error = '<?php esc_attr_e( 'Autogeneration not available for preview', 'wpseo' ); ?> <?php wpSEOde::help_icon(999999); ?>';
            var wpseo_preview_title = '';
            var wpseo_preview_desc = '';
            var wpseo_preview_og_title = '';
            var wpseo_preview_og_desc = '';
            var wpseo_preview_og_image = '';
            var wpseo_preview_twitter_title = '';
            var wpseo_preview_twitter_desc = '';
            var wpseo_preview_twitter_image = '';
        </script>

		<div class="wpseo">
			<div id="tabs-wpseo-preview">
				<ul class="category-tabs">
				<?php if ( $options['tax_title_manually'] || $options['tax_manually'] ) { ?>
					<li><a href="#wpseo-google"><?php _e( 'Google', 'wpseo' ); ?></a></li>
				<?php } ?>
				<?php if ( $options['open_graph'] ) { ?>
					<li><a href="#wpseo-og"><?php _e( 'Open Graph', 'wpseo' ); ?></a></li>
				<?php } ?>
				<?php if ( $options['twitter_site_account'] != '' ) { ?>
					<li><a href="#wpseo-twitter">Twitter</a></li>
				<?php } ?>
				</ul>

				<div id="wpseo-google">
					<?php if ( $options['tax_title_manually'] ) { ?>
						<div class="form-field">
								<label for="tag-wpseo-title">
									<?php esc_html_e('Title', 'wpseo') ?>
								</label>

								<input id="tag-wpseo-title" type="text" size="40" name="wpseo_tax_title" value="<?php echo esc_attr( self::get_meta_data($term, 'title') ) ?>">
								<br />
								<span class="description"><?php esc_html_e('The use of the title can be edited under wpSEO -> Pagetitle.', 'wpseo') ?></span>

						</div>
					<?php }

					if ( $options['tax_manually'] ) { ?>
						<div class="form-field">
								<label for="tag-wpseo-desc">
									<?php esc_html_e('Short description', 'wpseo') ?>
								</label>
								<textarea id="tag-wpseo-desc" type="text" size="40" rows="5" name="wpseo_tax_short"><?php echo esc_attr( self::get_meta_data($term, null) ) ?></textarea>
								<br />
								<span class="description"><?php esc_html_e('The short description can be used in wpSEO plugin.', 'wpseo') ?></span>

						</div>
					<?php }

					if( $options['tax_title_manually'] || $options['tax_manually'] ) { ?>
						<script type="text/javascript">
							var wpseo_preview_css = [ '<?php echo wpSEOde::plugin_url('css/preview.min.css'); ?>', '<?php esc_attr_e( includes_url('css/dashicons.min.css') ); ?>' ];
							var wpseo_preview_type = 'tax';
						</script>
						<div class="form-field">
							<label for="_wpseo_edit_description"><?php esc_html_e('Preview', 'wpseo') ?>:</label>
						</div>
						<div class="form-field"><iframe id="_wpseo_google_preview" src="about:blank" style="width: 100%; height: 120px;"></iframe></div>
						<div class="form-field"><input type="checkbox" id="_wpseo_google_preview_date" value="1" /> <?php esc_html_e( 'Show Date', 'wpseo' ); ?><br><br>
						<input type="hidden" id="_wpseo_google_preview_type" value="desktop"><input type="button" id="_wpseo_google_preview_type_desktop" onclick="jQuery('#_wpseo_google_preview_type').val('desktop');" class="button" value="Desktop"> <input type="button" id="_wpseo_google_preview_type_desktop1712" onclick="jQuery('#_wpseo_google_preview_type').val('desktop1712');" class="button" value="Desktop (<?php esc_attr_e( 'long', 'wpseo' ); ?>)"> <input type="button"  id="_wpseo_google_preview_type_mobile" onclick="jQuery('#_wpseo_google_preview_type').val('mobile');" class="button" value="Mobile"></div>
					<?php } ?>
				</div>

				<div id="wpseo-og">
					<?php if ( $options['open_graph'] ) {
					if($options['open_graph_manually']==1)
					{
						?>
						<div class="form-field">
							<label for="tag-wpseo-og">
								<?php esc_html_e('No output of Open Graph', 'wpseo') ?>
							</label>
							<input id="tag-wpseo-og" type="checkbox" name="wpseo_tax_og" value="1" <?php echo (esc_attr( self::get_meta_data($term, 'og_disable')?'checked="checked"':'' )) ?>">
						</div>
						<?php
					}
					if($options['open_graph_date_disable']==1)
					{
						?>
						<div class="form-field">
							<label for="tag-wpseo-og-date">
								<?php esc_html_e('No output of Open Graph Date', 'wpseo') ?>
							</label>
							<input id="tag-wpseo-og-date" type="checkbox" name="wpseo_tax_og_date" value="1" <?php echo (esc_attr( self::get_meta_data($term, 'og_date_disable')?'checked="checked"':'' )) ?>">
						</div>
						<?php
					}
					?>
					<?php
					if ( $options['open_graph_title_manually'] ) {
					?>
					<div class="form-field">
						<label for="tag-wpseo-og-title">
							<?php esc_html_e('Open Graph title', 'wpseo') ?>
						</label>

						<input id="tag-wpseo-og-title" type="text" size="40" name="wpseo_tax_og_title" value="<?php echo esc_attr( self::get_meta_data($term, 'og_title') ) ?>">
						<br />
						<span class="description"><?php esc_html_e('This title will be used on Facebook & Google+', 'wpseo') ?></span>

					</div>
					<?php } ?>
					<?php
					if ( $options['open_graph_description_manually'] ) {
					?>
					<div class="form-field">
						<label for="tag-wpseo-og-desc">
							<?php esc_html_e('Open Graph description', 'wpseo') ?>
						</label>
						<textarea id="tag-wpseo-og-desc" type="text" size="40" rows="5" name="wpseo_tax_og_desc"><?php echo esc_attr( self::get_meta_data($term, 'og_desc') ) ?></textarea>
						<br />
						<span class="description"><?php esc_html_e('This description will be used on Facebook & Google+', 'wpseo') ?></span>

					</div>
					<?php } ?>
					<?php
					if ( $options['open_graph_image_manually'] ) {
					?>
					<div class="form-field">
						<label for="tag-wpseo-og-image">
							<?php esc_html_e('Open Graph previewimage', 'wpseo') ?>
						</label>
						<?php
						wpSEOde_Tax::wpseo_image_upload('tag-wpseo-og-image','wpseo_tax_og_image',esc_html__('Select or upload image', 'wpseo'));
						?>
					</div>
					<?php } ?>
					<div class="form-field">
						<label for="_wpseo_facebook_preview"><?php esc_html_e('Facebook Preview', 'wpseo') ?></label>
						<iframe id="_wpseo_facebook_preview" src="about:blank" style="width: 100%; height: 340px;"></iframe>
					</div>
					<?php } ?>
				</div>

				<div id="wpseo-twitter">
					<?php if ( $options['twitter_site_account']!='' ) {
						if($options['twitter_cards_manually']==1)
						{
							?>
							<div class="form-field">
									<label for="tag-wpseo-twittercard">
										<?php esc_html_e('No output of Twittercard', 'wpseo') ?>
									</label>
									<input id="tag-wpseo-twittercard" type="checkbox" name="wpseo_tax_twittercard" value="1" <?php echo (esc_attr( self::get_meta_data($term, 'twittercard_disable')?'checked="checked"':'' )) ?>">
							</div>
						<?php
						}
						?>
						<div class="form-field">
							<label for="tag-wpseo-twittercard-title">
								<?php esc_html_e('Twittercard title', 'wpseo') ?>
							</label>

							<input id="tag-wpseo-twittercard-title" type="text" size="40" name="wpseo_tax_twittercard_title" value="<?php echo esc_attr( self::get_meta_data($term, 'twittercard_title') ) ?>">
							<br />
							<span class="description"><?php esc_html_e('This title will be used on Twitter', 'wpseo') ?></span>

						</div>
						<div class="form-field">
							<label for="tag-wpseo-twittercard-desc">
								<?php esc_html_e('Twittercard description', 'wpseo') ?>
							</label>
							<textarea id="tag-wpseo-twittercard-desc" type="text" size="40" rows="5" name="wpseo_tax_twittercard_desc"><?php echo esc_attr( self::get_meta_data($term, 'twittercard_desc') ) ?></textarea>
							<br />
							<span class="description"><?php esc_html_e('This description will be used on Twitter', 'wpseo') ?></span>

						</div>

						<div class="form-field">
							<label for="tag-wpseo-twittercard-image">
								<?php esc_html_e('Twittercard previewimage', 'wpseo') ?>
							</label>
							<?php
								wpSEOde_Tax::wpseo_image_upload('tag-wpseo-twittercard-image','wpseo_tax_twittercard_image',esc_html__('Select or upload image', 'wpseo'));
							?>
						</div>
						<div class="form-field">
							<label for="_wpseo_twitter_preview"><?php esc_html_e('Twittercard Preview', 'wpseo') ?></label>
							<iframe id="_wpseo_twitter_preview" src="about:blank" style="width: 100%; height: 130px;"></iframe>
						</div>
					<?php } ?>
				</div>
			</div>

			<?php

			if ( $options['canonical_manually'] ) { ?>
				<div class="form-field">
					<label for="tag-wpseo-canonical"c>
						<?php esc_html_e('Canonical', 'wpseo') ?>
					</label>

					<input id="tag-wpseo-canonical" type="text" size="40" name="wpseo_tax_canonical" value="<?php echo esc_attr( self::get_meta_data($term, 'canonical') ) ?>">
					<br />
					<span class="description"><?php esc_html_e('The canonical URL', 'wpseo') ?></span>

				</div>
			<?php }

			if ( $options['redirect_manually'] ) { ?>
				<div class="form-field">
					<label for="tag-wpseo-redirect">
						<?php esc_html_e('Redirect', 'wpseo') ?>
					</label>

					<input id="tag-wpseo-redirect" type="text" name="wpseo_tax_redirect" value="<?php echo esc_attr( self::get_meta_data($term, 'redirect') ) ?>">
					<br />
					<span class="description"><?php esc_html_e('Redirect to this URL', 'wpseo') ?></span>

				</div>
			<?php }

			if ( $options['tax_robots_manually'] ) { ?>
				<div class="form-field">
						<label>
							<?php esc_html_e('Robots', 'wpseo') ?>
						</label>
						<select name="wpseo_tax_robots">
							<option value=""></option>
							<?php foreach (wpSEOde_Vars::get('meta_robots') as $k => $v) { ?>
								<option value="<?php echo esc_attr($k) ?>" <?php selected( self::get_meta_data($term, 'robots'), $k ) ?>>
									<?php echo sprintf('%s&nbsp;(%s)', $v, esc_html__( wpSEOde_Vars::get('meta_robots_desc', $k), 'wpseo')) ?>
								</option>
							<?php } ?>
						</select>
				</div>
			<?php }
		echo '</div>';
    }


	

	public static function save_fields($term_id)
	{


		
		if ( empty($_POST['taxonomy']) OR ! taxonomy_exists($_POST['taxonomy']) ) {
			return $term_id;
		}



		
		if ( ! $term = get_term($term_id, $_POST['taxonomy']) ) {
			return $term_id;
		}

        
        if ( empty($term->taxonomy) OR ! taxonomy_exists($term->taxonomy) ) {
            return $term_id;
        }

        
        $tax = get_taxonomy($term->taxonomy);

        
        if ( ! current_user_can( $tax->cap->assign_terms ) ) {
            return;
        }

        
        if ( isset($_POST['wpseo_tax_title']) ) {




            self::update_meta_data(
                $term,
                'title',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_title'] ) )
            );
        }

		
		if ( isset($_POST['wpseo_tax_short']) ) {
			self::update_meta_data(
				$term,
				null,
				sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_short'] ) )
			);
		}

        
        if ( isset($_POST['wpseo_tax_canonical']) ) {
            self::update_meta_data(
                $term,
                'canonical',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_canonical'] ) )
            );
        }


        
        if ( isset($_POST['wpseo_tax_redirect']) ) {
            self::update_meta_data(
                $term,
                'redirect',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_redirect'] ) )
            );
        }

		
		if ( isset($_POST['wpseo_tax_robots']) ) {
			self::update_meta_data(
				$term,
				'robots',
				(int)$_POST['wpseo_tax_robots']
			);
		}

        if ( isset($_POST['wpseo_tax_og_disable']) ) {
            self::update_meta_data(
                $term,
                'og_disable',
                (int)1
            );
        }

        if ( isset($_POST['wpseo_tax_og_date_disable']) ) {
            self::update_meta_data(
                $term,
                'og_date_disable',
                (int)1
            );
        }

        if ( isset($_POST['wpseo_tax_og_title']) ) {
            self::update_meta_data(
                $term,
                'og_title',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_og_title'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_og_desc']) ) {
            self::update_meta_data(
                $term,
                'og_desc',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_og_desc'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_og_image']) ) {
            self::update_meta_data(
                $term,
                'og_image',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_og_image'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_twittercard_disable']) ) {
            self::update_meta_data(
                $term,
                'twittercard_disable',
                (int)1);

        }

        if ( isset($_POST['wpseo_tax_twittercard_title']) ) {
            self::update_meta_data(
                $term,
                'twittercard_title',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_twittercard_title'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_twittercard_desc']) ) {
            self::update_meta_data(
                $term,
                'twittercard_desc',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_twittercard_desc'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_twittercard_image']) ) {
            self::update_meta_data(
                $term,
                'twittercard_image',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_twittercard_image'] ) )
            );
        }



		return $term_id;
	}

    

    public static function create_fields($term_id)
    {
        update_option('wpseo_debug',$term_id);


        
        if ( empty($_POST['taxonomy']) OR ! taxonomy_exists($_POST['taxonomy']) ) {
            return $term_id;
        }



        
        if ( ! $term = get_term($term_id, $_POST['taxonomy']) ) {
            return $term_id;
        }

        
        if ( empty($term->taxonomy) OR ! taxonomy_exists($term->taxonomy) ) {
            return $term_id;
        }

        
        $tax = get_taxonomy($term->taxonomy);

        
        if ( ! current_user_can( $tax->cap->assign_terms ) ) {
            return;
        }

        
        if ( isset($_POST['wpseo_tax_title']) ) {




            self::update_meta_data(
                $term,
                'title',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_title'] ) )
            );
        }

        
        if ( isset($_POST['wpseo_tax_short']) ) {
            self::update_meta_data(
                $term,
                null,
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_short'] ) )
            );
        }


        
        if ( isset($_POST['wpseo_tax_canonical']) ) {
            self::update_meta_data(
                $term,
                'canonical',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_canonical'] ) )
            );
        }

        
        if ( isset($_POST['wpseo_tax_robots']) ) {
            self::update_meta_data(
                $term,
                'robots',
                (int)$_POST['wpseo_tax_robots']
            );
        }

        if ( isset($_POST['wpseo_tax_og_disable']) ) {
            self::update_meta_data(
                $term,
                'og_disable',
                (int)1
            );
        }

        if ( isset($_POST['wpseo_tax_og_date_disable']) ) {
            self::update_meta_data(
                $term,
                'og_date_disable',
                (int)1
            );
        }

        if ( isset($_POST['wpseo_tax_og_title']) ) {
            self::update_meta_data(
                $term,
                'og_title',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_og_title'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_og_desc']) ) {
            self::update_meta_data(
                $term,
                'og_desc',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_og_desc'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_og_image']) ) {
            self::update_meta_data(
                $term,
                'og_image',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_og_image'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_twittercard_disable']) ) {
            self::update_meta_data(
                $term,
                'twittercard_disable',
                (int)1);

        }

        if ( isset($_POST['wpseo_tax_twittercard_title']) ) {
            self::update_meta_data(
                $term,
                'twittercard_title',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_twittercard_title'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_twittercard_desc']) ) {
            self::update_meta_data(
                $term,
                'twittercard_desc',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_twittercard_desc'] ) )
            );
        }

        if ( isset($_POST['wpseo_tax_twittercard_image']) ) {
            self::update_meta_data(
                $term,
                'twittercard_image',
                sanitize_text_field( stripslashes_deep( $_POST['wpseo_tax_twittercard_image'] ) )
            );
        }

        return $term_id;
    }

    public static function wpseo_image_upload($id='',$name='',$button_text='Upload',$value='')
    {
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        ?>
        <div>
            <input type="text" name="<?php echo $name;?>" id="<?php echo $id;?>" class="regular-text" value="<?php echo esc_attr($value) ?>">
            <input type="button" name="upload-btn" id="<?php echo $id;?>-upload-btn" class="button-secondary" value="<?php echo $button_text;?>">

        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#<?php echo $id;?>-upload-btn').click(function(e) {
                    e.preventDefault();
                    var image = wp.media({
                        title: 'Upload Image',
                        multiple: false
                    }).open()
                        .on('select', function(e){
                            var uploaded_image = image.state().get('selection').first();
                            var image_url = uploaded_image.toJSON().url;
                            $('#<?php echo $id;?>').val(image_url).trigger('change');
                        });
                });
            });
        </script>
    <?php
    }
}
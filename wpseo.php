<?php
/*
Plugin Name: wpSEO
Text Domain: wpseo
Domain Path: /lang
Description: Powerful and reliable Plugin for search engine optimization and metadata formatting. A Swiss Army Knife of SEO with innovation from Germany.
Author: Team wpSEO
Author URI: https://wpseo.de
Version: 4.6.1
*/


/* Quit */
defined( 'ABSPATH' ) or exit;

/* detect Yoast SEO (WPSEO_FILE), All in One SEO Pack (AIOSEOP_PLUGIN_DIR), DELUCKS SEO (DPC_PLUGIN_FILE) */
if ( defined( 'WPSEO_FILE' ) || defined( 'AIOSEOP_PLUGIN_DIR' ) || defined( 'DPC_PLUGIN_FILE' ) ) {
	add_action(
		'all_admin_notices',
		'wpseo_alternatives'
	);
}

define( 'WPSEODE_DIR', dirname( __FILE__ ) );
define( 'WPSEODE_FILE', __FILE__ );
define( 'WPSEODE_BASE', plugin_basename( __FILE__ ) );

require_once sprintf(
	'%s/inc/_%s.class.php',
	WPSEODE_DIR,
	( is_admin() ? 'be' : 'fe' )
);

spl_autoload_register(
	'wpseode_autoload'
);

add_action(
	'plugins_loaded',
	array(
		'wpSEOde',
		'init'
	)
);

register_activation_hook(
	__FILE__,
	array(
		'wpSEOde',
		'install'
	)
);
register_uninstall_hook(
	__FILE__,
	array(
		'wpSEOde',
		'uninstall'
	)
);


function wpseode_autoload( $class ) {
	$available = array(
		'wpSEOde_Base'       => 'base',
		'wpSEOde_Dashboard'  => 'dashboard',
		'wpSEOde_Feedback'   => 'feedback',
		'wpSEOde_GUI'        => 'gui',
		'wpSEOde_License'    => 'license',
		'wpSEOde_Meta'       => 'meta',
		'wpSEOde_Suggest'    => 'suggest',
		'wpSEOde_Options'    => 'options',
		'wpSEOde_Output'     => 'output',
		'wpSEOde_Slug'       => 'slug',
		'wpSEOde_Tax'        => 'tax',
		'wpSEOde_Tools'      => 'tools',
		'wpSEOde_Transients' => 'transients',
		'wpSEOde_Update'     => 'update',
		'wpSEOde_Cache'      => 'cache',
		'wpSEOde_User'       => 'user',
		'wpSEOde_Vars'       => 'vars',
		'wpSEOde_Rewrite'    => 'rewrite',
		'wpSEOde_Sitemap'    => 'sitemap',
		'wpSEOde_Ajax'       => 'ajax'
	);

	if ( isset( $available[ $class ] ) ) {
		require_once(
		sprintf(
			'%s/inc/%s.class.php',
			WPSEODE_DIR,
			$available[ $class ]
		)
		);
	}
}

function wpseo_alternatives() {
	echo sprintf(
		'<div class="notice notice-error is-dismissible"><p>wpSEO: %s: %s</p></div>',
		esc_html__( 'Attention', 'wpseo' ),
		esc_html__( 'Several SEO plugins in use. Unexpected output behavior possible!', 'wpseo' )
	);
}

<?php

if ( \Borlabs\Factory::get( 'Cache\Config' )->cacheActivated() && \Borlabs\Factory::get( 'Cache\Config' )->get( 'fragmentCaching' ) == true ) {
	
	add_action( 'wp_footer', array( 'wpSEOde_Output', 'modify_output' ), 10 );
}

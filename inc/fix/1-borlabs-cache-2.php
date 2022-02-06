<?php

if ( \Borlabs\Factory::get( 'Cache\Config' )->cacheActivated() && \Borlabs\Factory::get( 'Cache\Config' )->get( 'fragmentCaching' ) == true ) {
	$borlabsCacheActive = true;
	
	$data = ob_get_clean();
}

<?php
if ( \Borlabs\Factory::get( 'Cache\Config' )->cacheActivated() && \Borlabs\Factory::get( 'Cache\Config' )->get( 'fragmentCaching' ) == true ) {
	ob_start();
	$borlabsCacheActive = true;
}

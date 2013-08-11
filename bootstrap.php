<?php

/**
 *	Paths.
 */

define( 'NJ_DS', DIRECTORY_SEPARATOR );
define( 'NJ_ROOT', dirname( __FILE__ ) . NJ_DS );
define( 'NJ_LIB', NJ_ROOT . 'lib' . NJ_DS );
define( 'NJ_ENTRIES', NJ_ROOT . 'entries' . NJ_DS );
define( 'NJ_COMPILED', NJ_ROOT . 'compiled' . NJ_DS );
define( 'NJ_THEMES', NJ_ROOT . 'themes' . NJ_DS );
define( 'NJ_PLUGINS', NJ_ROOT . 'plugins' . NJ_DS );



/**
 *	URLs.
 */

$path = $_SERVER['SCRIPT_NAME'];
$path = substr( $path, 0, strrpos( $path, '/' ) ?: 0 );
$http = ( empty( $_SERVER['HTTPS']) || ( @$_SERVER['HTTPS'] === 'off' ));

define( 'NJ_ROOT_URL', ( $http ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $path );
define( 'NJ_THEMES_URL', NJ_ROOT_URL . '/themes' );

unset( $path, $http );



/**
 *	Autoload.
 */

spl_autoload_register( function( $className ) {

	$path = NJ_LIB . str_replace( '\\', NJ_DS, $className ) . '.php';

	if ( file_exists( $path )) {
		require_once $path;
	}
});

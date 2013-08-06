<?php

require_once 'vendor/autoload.php';



/**
 *	Paths.
 */

define( 'UL_DS', DIRECTORY_SEPARATOR );
define( 'UL_ROOT', dirname( __FILE__ ) . UL_DS );
define( 'UL_LIB', UL_ROOT . 'lib' . UL_DS );
define( 'UL_ENTRIES', UL_ROOT . 'entries' . UL_DS );
define( 'UL_COMPILED', UL_ROOT . 'compiled' . UL_DS );
define( 'UL_THEMES', UL_ROOT . 'themes' . UL_DS );



/**
 *	URLs.
 */

$path = $_SERVER['SCRIPT_NAME'];
$path = substr( $path, 0, strrpos( $path, '/' ) ?: 0 );
$http = ( empty( $_SERVER['HTTPS']) || ( @$_SERVER['HTTPS'] === 'off' ));

define( 'UL_ROOT_URL', ( $http ? 'http://' : 'https://' ) . $_SERVER['HTTP_HOST'] . $path );
define( 'UL_THEMES_URL', UL_ROOT_URL . '/themes' );

unset( $path, $http );



/**
 *	Autoload.
 */

spl_autoload_register( function( $className ) {
	$path = UL_LIB . str_replace( '\\', UL_DS, $className ) . '.php';

	if ( file_exists( $path )) {
		require_once $path;
	}
});

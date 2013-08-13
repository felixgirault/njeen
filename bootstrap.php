<?php

use Njeen\Utility\Autoload;



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

require_once NJ_LIB . 'Utility' . NJ_DS . 'Autoload.php';

Autoload::setup( NJ_LIB, 'Njeen' );
Autoload::setup( NJ_PLUGINS, 'Njeen\\Plugin' );

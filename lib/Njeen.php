<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen;

use Njeen\Di\Container as Di;
use Njeen\Entry\Collection as EntryCollection;
use Njeen\Entry\Compiler as EntryCompiler;
use Njeen\Routing\Router;
use Njeen\Utility\Html;
use DirectoryIterator;



/**
 *
 *
 *	@package Njeen
 */

class Njeen {

	/**
	 *	Dependency injection container.
	 *
	 *	@var Di
	 */

	protected static $_Di = null;



	/**
	 *	Runs Njeen.
	 */

	public static function run( ) {

		try {
			self::_setupDi( );
			self::_loadPlugins( );
			self::_compile( );

			echo self::$_Di->get( 'Njeen.Blog' )->page(
				self::$_Di->get( 'Njeen.Router' ),
				self::$_Di->get( 'Njeen.Entries' )
			);
		} catch ( Exception $Exception ) {
			var_dump( $Exception );
		}
	}



	/**
	 *	Configures dependency injection.
	 */

	protected static function _setupDi( ) {

		self::$_Di = new Di( array(
			'Njeen.Settings' => Di::unique( function( ) {
				return new Settings( NJ_ROOT . 'settings.json' );
			}),
			'Njeen.Theme' => Di::unique( function( $Di ) {
				return new Theme(
					$Di->get( 'Njeen.Settings' )->theme
				);
			}),
			'Njeen.Router' => Di::unique( function( $Di ) {
				return new Router(
					$Di->get( 'Njeen.Settings' )->router
				);
			}),
			'Njeen.Compiler' => Di::unique( function( $Di ) {
				return new EntryCompiler( );
			}),
			'Njeen.Entries' => Di::unique( function( $Di ) {
				return new EntryCollection(
					array_keys(
						$Di->get( 'Njeen.Settings' )->router['entries']
					)
				);
			}),
			'Njeen.Html' => Di::unique( function( $Di ) {
				return new Html( );
			}),
			'Njeen.Blog' => Di::unique( function( $Di ) {
				return new Blog(
					$Di->get( 'Njeen.Theme' ),
					$Di->get( 'Njeen.Settings' )->blog
				);
			})
		));
	}



	/**
	 *	Loads plugins.
	 */

	protected static function _loadPlugins( ) {

		$Directory = new DirectoryIterator( NJ_PLUGINS );

		foreach ( $Directory as $File ) {
			if ( $File->isDir( )) {
				$name = $File->getBasename( );
				$path = $File->getPath( )
					. NJ_DS . $name
					. NJ_DS . 'Plugin.php';

				if ( file_exists( $path )) {
					require_once( $path );

					$class = "Njeen\\Plugin\\$name\\Plugin";

					$Plugin = new $class( );
					$Plugin->setup( self::$_Di );
				}
			}
		}
	}



	/**
	 *	Compiles entries if necessary.
	 */

	protected static function _compile( ) {

		$Entries = self::$_Di->get( 'Njeen.Entries' );

		if ( $Entries->shouldCompile( )) {
			$Entries->compile( self::$_Di->get( 'Njeen.Compiler' ));
		}
	}
}

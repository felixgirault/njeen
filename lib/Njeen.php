<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen;

use Njeen\Di\Container as Di;
use Njeen\Plugin\Collection as PluginCollection;
use Njeen\Entry\Collection as EntryCollection;
use Njeen\Entry\Compiler as EntryCompiler;
use Njeen\Routing\Router;
use Njeen\Helper\Html;



/**
 *	Njeen.
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
			self::_compile( );

			self::$_Di->get( 'Njeen.Plugins' )->load( self::$_Di );

			echo self::$_Di->get( 'Njeen.Blog' )->page(
				self::$_Di->get( 'Njeen.Router' )->request( ),
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
			'Njeen.Plugins' => Di::unique( function( $Di ) {
				return new PluginCollection( );
			}),
			'Njeen.Html' => Di::unique( function( $Di ) {
				return new Html( );
			}),
			'Njeen.Theme' => Di::unique( function( $Di ) {
				$Theme = new Theme(
					$Di->get( 'Njeen.Settings' )->theme
				);

				$Theme->setAll( array(
					'Theme' => $Theme,
					'Html' => $Di->get( 'Njeen.Html' ),
					'Router' => $Di->get( 'Njeen.Router' ),
					'Entries' => $Di->get( 'Njeen.Entries' )
				));

				return $Theme;
			}),
			'Njeen.Blog' => Di::unique( function( $Di ) {
				$Theme = $Di->get( 'Njeen.Theme' );
				$Blog = new Blog(
					$Theme,
					$Di->get( 'Njeen.Settings' )->blog
				);

				$Theme->set( 'Blog', $Blog );

				return $Blog;
			})
		));
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

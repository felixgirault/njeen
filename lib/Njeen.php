<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Njeen {

	/**
	 *
	 */

	protected static $_Di = null;



	/**
	 *
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
	 *
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
				return new Compiler( );
			}),
			'Njeen.Entries' => Di::unique( function( $Di ) {
				return new EntryCollection(
					array_keys(
						$Di->get( 'Njeen.Settings' )->router['entries']
					)
				);
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
	 *
	 */

	protected static function _loadPlugins( ) {

		$Directory = new DirectoryIterator( NJ_PLUGINS );

		foreach ( $Directory as $Plugin ) {
			if ( $Plugin->isDir( )) {
				$name = $Plugin->getBasename( );
				$class = $name . 'Plugin';
				$path = $Plugin->getPath( )
					. NJ_DS . $name
					. NJ_DS . $class . '.php';

				if ( file_exists( $path )) {
					require_once( $path );
					$class::setup( self::$_Di );
				}
			}
		}
	}



	/**
	 *
	 */

	protected static function _compile( ) {

		$Entries = self::$_Di->get( 'Njeen.Entries' );

		if ( $Entries->shouldCompile( )) {
			$Entries->compile( self::$_Di->get( 'Njeen.Compiler' ));
		}
	}
}

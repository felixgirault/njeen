<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

use dflydev\markdown\MarkdownExtraParser;



/**
 *
 */

class Blog {

	/**
	 *	Settings.
	 *
	 *	@var Settings
	 */

	protected $_settings = null;



	/**
	 *	Meta informations.
	 *
	 *	@var Settings
	 */

	protected $_index = null;



	/**
	 *
	 */

	protected $_Theme = null;



	/**
	 *	Compilers.
	 *
	 *	@var array
	 */

	protected $_compilers = array(
		'Ul::compileMarkdown'
	);



	/**
	 *	Constructor.
	 */

	public function __construct( ) {

		$this->_settings = FileSystem::readJson( UL_ROOT . 'settings.json' );
		$this->_index = FileSystem::readJson( UL_COMPILED . 'index.json' );

		$this->_Theme = new Theme( $this->_settings['blog']['theme']);

		$this->_compile( );
	}



	/**
	 *	Destructor.
	 */

	public function __destruct( ) {

		FileSystem::writeJson( UL_COMPILED . 'index.json', $this->_index );
	}



	/**
	 *	Destructor.
	 */

	public function __get( $name ) {

		return isset( $this->_settings['blog'][ $name ])
			? $this->_settings['blog'][ $name ]
			: '';
	}



	/**
	 *
	 */

	protected function _compile( ) {

		foreach ( $this->_settings['entries']['types'] as $type => $path ) {
			$directory = UL_ENTRIES . $type;

			FileSystem::ensureDirectoryExists( $directory );
			$Iterator = new DirectoryIterator( $directory );

			foreach ( $Iterator as $Entry ) {
				if ( $Entry->getExtension( ) !== 'md' ) {
					continue;
				}

				$id = $Entry->getBasename( '.md' );
				$mtime = $Entry->getMTime( );

				if (
					isset( $this->_index[ $type ][ $id ])
					&& ( $this->_index[ $type ][ $id ] === $mtime )
				) {
					continue;
				}

				$this->_index[ $type ][ $id ] = $mtime;

				$Entry = new Entry( $type, $id, Entry::raw );
				$Entry->compile( $this->_compilers );
				$Entry->save( );
			}
		}
	}



	/**
	 *
	 */

	public static function compileMarkdown( $text ) {

		static $Parser = null;

		if ( $Parser === null ) {
			$Parser = new MarkdownExtraParser( );
		}

		return $Parser->transformMarkdown( $text );
	}



	/**
	 *
	 */

	public function page( ) {

		$this->_Theme->set( 'page', $this->_renderPage( ));
		$this->_Theme->set( 'Theme', $this->_Theme );
		$this->_Theme->set( 'Blog', $this );

		return $this->_Theme->part( 'layout' );
	}



	/**
	 *
	 */

	protected function _renderPage( ) {

		$request = $_SERVER['REQUEST_URI'];

		if ( $request == '/' ) {
			return $this->_Theme->part( 'home' );
		}

		foreach ( $this->_settings['entries']['types'] as $type => $path ) {
			$listPattern = '#^' . $path . '/?$#';

			if ( preg_match( $listPattern, $request )) {
				return $this->_Theme->part( "$type/index" );
			}

			$singlePattern = '#^' . $path . '/(?<id>' . $this->_settings['entries']['id'] . ')$#';

			if ( preg_match( $singlePattern, $request, $matches )) {
				$id = $matches['id'];

				if ( isset( $this->_index[ $type ][ $id ])) {
					$this->_Theme->set( 'Entry', new Entry( $type, $id ));

					return $this->_Theme->part( "$type/single" );
				}
			}
		}

		return $this->error( 404 );
	}



	/**
	 *
	 */

	public function error( $code ) {

		http_response_code( $code );

		return $this->_Theme->hasPart( "errors/$code" )
			? $this->_Theme->part( "errors/$code" )
			: '';
	}
}

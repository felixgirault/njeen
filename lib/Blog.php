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

	use Configurable;



	/**
	 *	Meta informations.
	 *
	 *	@var Settings
	 */

	protected $_index = null;



	/**
	 *
	 */

	protected $_Router = null;



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
		'Blog::compileMarkdown'
	);



	/**
	 *	Constructor.
	 */

	public function __construct( $vars, $Router ) {

		$this->_vars = $vars;
		$this->_index = FileSystem::readJson( NJ_COMPILED . 'index.json' );
		$this->_Router = $Router;
		$this->_Theme = new Theme( $this->_vars['theme']);

		$this->_compile( );
	}



	/**
	 *	Destructor.
	 */

	public function __destruct( ) {

		FileSystem::writeJson( NJ_COMPILED . 'index.json', $this->_index );
	}



	/**
	 *	Destructor.
	 */

	public function __get( $name ) {

		return isset( $this->_vars[ $name ])
			? $this->_vars[ $name ]
			: '';
	}



	/**
	 *
	 */

	protected function _compile( ) {

		foreach ( $this->_Router->entryTypes( ) as $type ) {
			$directory = NJ_ENTRIES . $type;

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
				$Entry->setDefaults( $this->defaults );
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

		$this->_Theme->set( 'Router', $this->_Router );
		$this->_Theme->set( 'Theme', $this->_Theme );
		$this->_Theme->set( 'Blog', $this );

		$this->_Theme->set( 'page', $this->_renderPage( ));

		return $this->_Theme->part( 'layout' );
	}



	/**
	 *
	 */

	protected function _renderPage( ) {

		$Request = $this->_Router->request( );
		$page = '';

		switch ( $Request->type ) {
			case 'home':
				$page = $this->_Theme->part( 'home' );
				break;

			case 'index':
				$type = $Request->data['type'];
				$page = $this->_Theme->hasPart( "$type/index" )
					? $this->_Theme->part( "$type/index" )
					: $this->_Theme->part( 'entries/index' );
				break;

			case 'single':
				$type = $Request->data['type'];
				$id = $Request->data['id'];

				if ( isset( $this->_index[ $type ][ $id ])) {
					$this->_Theme->set( 'Entry', new Entry( $type, $id ));
					$page = $this->_Theme->hasPart( "$type/single" )
						? $this->_Theme->part( "$type/single" )
						: $this->_Theme->part( 'entries/single' );
				}
				break;

			case 'error':
				$page = $this->error( $Request->data['code']);
				break;
		}

		return $page;
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

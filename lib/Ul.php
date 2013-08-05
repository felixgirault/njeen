<?php

use dflydev\markdown\MarkdownExtraParser;



/**
 *
 */

class Ul {

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

	protected $_meta = null;



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

		$this->_settings = new Settings( UL_ROOT . 'settings.json' );
		$this->_meta = new Settings( UL_COMPILED . 'meta.json' );

		$this->_Theme = new Theme( $this->_settings['theme']);

		$this->_compile( );
	}



	/**
	 *	Destructor.
	 */

	public function __destruct( ) {

		$this->_meta->save( );
	}



	/**
	 *
	 */

	protected function _compile( ) {

		foreach ( $this->_settings['entries'] as $type => $path ) {
			$directory = UL_ENTRIES . $type;

			if ( !is_dir( $directory )) {
				mkdir( $directory );
			}

			$Iterator = new DirectoryIterator( $directory );

			foreach ( $Iterator as $Entry ) {
				if ( $Entry->getExtension( ) !== 'md' ) {
					continue;
				}

				$id = $Entry->getBasename( '.md' );
				$mtime = $Entry->getMTime( );

				if ( isset( $this->_meta[ $type ][ $id ])) {
					if ( $this->_meta[ $type ][ $id ]['mtime'] === $mtime ) {
						continue;
					}
				}

				$this->_meta[ $type ][ $id ]['mtime'] = $mtime;
				$contents = file_get_contents( $Entry->getPathname( ));

				list( $header, $text ) = preg_split( '/\n\s*\n/mi', $contents, 2 );

				$metas = explode( PHP_EOL, $header );

				foreach ( $metas as $meta ) {
					list( $key, $value ) = explode( ':', $meta, 2 );
					$this->_meta[ $type ][ $id ][ $key ] = trim( $value );
				}

				foreach ( $this->_compilers as $compiler ) {
					if ( is_callable( $compiler )) {
						$text = call_user_func( $compiler, $text );
					}
				}

				file_put_contents(
					UL_COMPILED . $type . UL_DS . $id . '.html',
					$text
				);
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

		$page = $this->_renderPage( );
		$this->_Theme->set( 'page', $page );

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

		foreach ( $this->_settings['entries'] as $type => $path ) {
			$listPattern = '#^' . $path . '/?$#';
var_dump( $request, $listPattern, preg_match( $listPattern, $request ));
			if ( preg_match( $listPattern, $request )) {
				return $this->_Theme->part( "$type/index" );
			}

			$singlePattern = '#^' . $path . '/(?<id>[a-zA-Z0-9-]+)$#';

			if ( preg_match( $singlePattern, $request, $matches )) {
				$id = $matches['id'];

				if ( isset( $this->_meta[ $type ][ $id ])) {
					$this->_Theme->set( $this->_meta[ $type ][ $id ]);
					$this->_Theme->set(
						'body',
						file_get_contents( UL_COMPILED . $type . UL_DS . $id . '.html' )
					);

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

		return $this->_Theme->part( "errors/$code" );
	}
}

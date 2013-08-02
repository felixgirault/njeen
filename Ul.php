<?php

require_once 'vendor/autoload.php';

use dflydev\markdown\MarkdownExtraParser;



/**
 *	Paths.
 */

define( 'UL_DS', DIRECTORY_SEPARATOR );
define( 'UL_ROOT', dirname( __FILE__ ) . UL_DS );
define( 'UL_ENTRIES', UL_ROOT . 'entries' . UL_DS );
define( 'UL_COMPILED', UL_ROOT . 'compiled' . UL_DS );
define( 'UL_THEMES', UL_ROOT . 'themes' . UL_DS );



/**
 *
 */

class Ul {

	/**
	 *	Settings.
	 *
	 *	@var array
	 */

	protected $_settings = array( );



	/**
	 *	Meta informations.
	 *
	 *	@var array
	 */

	protected $_meta = array( );



	/**
	 *
	 */

	protected $_vars = array( );



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

		$this->_settings = $this->_loadJson( UL_ROOT . 'settings.json' );
		$this->_meta = $this->_loadJson( UL_COMPILED . 'meta.json' );

		$this->_compile( );
	}



	/**
	 *	Destructor.
	 */

	public function __destruct( ) {

		$this->_saveJson( UL_COMPILED . 'meta.json', $this->_meta );
	}



	/**
	 *	Loads and returns a JSON document.
	 *
	 *	@param string $path Path to the file.
	 *	@return array JSON data.
	 */

	protected function _loadJson( $path ) {

		if ( !file_exists( $path )) {
			return array( );
		}

		$contents = file_get_contents( $path );
		$data = json_decode( $contents, true );

		if ( $data === null ) {
			throw new Exception( "Error parsing JSON file: $path." );
		}

		return $data;
	}



	/**
	 *	Saves a JSON document.
	 *
	 *	@param string $path Path to the file.
	 *	@param array $data Data to encode.
	 */

	protected function _saveJson( $path, $data ) {

		file_put_contents( $path, json_encode( $data, JSON_PRETTY_PRINT ));
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

	public function has( $name ) {

		return isset( $this->_vars[ $name ]);
	}



	/**
	 *
	 */

	public function get( $name, $default ) {

		return $this->has( $name )
			? $this->_vars[ $name ]
			: $default;
	}



	/**
	 *
	 */

	public function set( $name, $value = null ) {

		if ( is_array( $name )) {
			$this->_vars = array_merge( $this->_vars, $name );
		} else {
			$this->_vars[ $name ] = $value;
		}
	}



	/**
	 *
	 */

	public function page( ) {

		$page = $this->_renderPage( );
		$this->set( 'page', $page );

		return $this->render( 'layout' );
	}



	/**
	 *
	 */

	protected function _renderPage( ) {

		$request = $_SERVER['REQUEST_URI'];

		if ( $request == '/' ) {
			return $this->render( 'home' );
		}

		foreach ( $this->_settings['entries'] as $type => $path ) {
			$listPattern = '#^' . $path . '/?$#';
var_dump( $request, $listPattern, preg_match( $listPattern, $request ));
			if ( preg_match( $listPattern, $request )) {
				return $this->render( $type );
			}

			$singlePattern = '#^' . $path . '/(?<id>[a-zA-Z0-9-]+)$#';

			if ( preg_match( $singlePattern, $request, $matches )) {
				$id = $matches['id'];

				if ( isset( $this->_meta[ $type ][ $id ])) {
					$this->set( $this->_meta[ $type ][ $id ]);
					$this->set(
						'body',
						file_get_contents( UL_COMPILED . $type . UL_DS . $id . '.html' )
					);

					return $this->render( $type );
				}
			}
		}

		return $this->error( 404 );
	}



	/**
	 *
	 */

	public function error( $code ) {

		return $this->render( $code );
	}



	/**
	 *
	 */

	public function render( $___fileName ) {

		extract( $this->_vars, EXTR_SKIP );
		ob_start( );

		include UL_THEMES . $this->_settings['theme'] . UL_DS . $___fileName . '.php';

		return ob_get_clean( );
	}
}

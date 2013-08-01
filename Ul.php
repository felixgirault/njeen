<?php

require_once 'vendor/autoload.php';

use dflydev\markdown\MarkdownExtraParser;



/**
 *	Paths.
 */

define( 'UL_DS', DIRECTORY_SEPARATOR );
define( 'UL_ROOT', dirname( __FILE__ ) . UL_DS );
define( 'UL_ARTICLES', UL_ROOT . 'articles' . UL_DS );
define( 'UL_COMPILED', UL_ROOT . 'compiled' . UL_DS );
define( 'UL_COMPILED_ARTICLES', UL_COMPILED . 'articles' . UL_DS );
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
	 *	Callbacks.
	 *
	 *	@var array
	 */

	protected $_callbacks = array(
		'afterCompilation' => null
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

		$Parser = new MarkdownExtraParser( );
		$Iterator = new DirectoryIterator( UL_ARTICLES );

		foreach ( $Iterator as $Article ) {
			if ( $Article->getExtension( ) !== 'md' ) {
				continue;
			}

			$id = $Article->getBasename( '.md' );
			$mtime = $Article->getMTime( );

			if ( isset( $this->_meta[ $id ])) {
				if ( $this->_meta[ $id ]['mtime'] === $mtime ) {
					continue;
				}
			}

			$this->_meta[ $id ]['mtime'] = $mtime;
			$contents = file_get_contents( $Article->getPathname( ));

			list( $header, $markdown ) = preg_split( '/\n\s*\n/mi', $contents, 2 );

			$metas = explode( PHP_EOL, $header );

			foreach ( $metas as $meta ) {
				list( $key, $value ) = explode( ':', $meta, 2 );
				$this->_meta[ $id ][ $key ] = trim( $value );
			}

			$html = $Parser->transformMarkdown( $markdown );

			if ( is_callable( $this->_callbacks['afterCompilation'])) {
				$html = call_user_func( $this->_callbacks['afterCompilation'], $html );
			}

			file_put_contents( UL_COMPILED_ARTICLES . $id . '.html', $html );
		}
	}



	/**
	 *
	 */

	public function page( ) {

		return $this->_render(
			'layout',
			array(
				'page' => $this->_renderPage( )
			)
		);
	}



	/**
	 *
	 */

	protected function _renderPage( ) {

		$path = $_SERVER['REQUEST_URI'];
		//$request = array( );

		if ( $path == '/' ) {
			return $this->_render( 'home' );
		}

		$articlesPattern = '#^' . $this->_settings['articlesPath'] . '/?$#i';

		if ( preg_match( $articlesPattern, $path )) {
			return $this->_render( 'articles' );
		}

		$articlePattern = '#^' . $this->_settings['articlesPath'] . '/(?<id>.*)$#i';

		if ( preg_match( $articlePattern, $path, $matches )) {
			$id = $matches['id'];

			if ( isset( $this->_meta[ $id ])) {
				return $this->_renderArticle( $id );
			}
		}

		return $this->_render( '404' );
	}



	/**
	 *
	 */

	protected function _renderArticle( $id ) {

		$vars = $this->_meta[ $id ];
		$vars['body'] = file_get_contents( UL_COMPILED_ARTICLES . $id . '.html' );

		return $this->_render( 'article', $vars );
	}



	/**
	 *
	 */

	protected function _render( $___fileName, $___vars = array( )) {

		extract( $___vars, EXTR_SKIP );
		ob_start( );

		include UL_THEMES
			. $this->_settings['theme']
			. DIRECTORY_SEPARATOR
			. $___fileName
			. '.php';

		return ob_get_clean( );
	}
}

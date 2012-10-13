<?php

require_once 'markdown/markdown.php';

define( 'UL_ROOT', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
define( 'UL_ARTICLES', UL_ROOT . 'articles' . DIRECTORY_SEPARATOR );
define( 'UL_THEMES', UL_ROOT . 'themes' . DIRECTORY_SEPARATOR );



/**
 *
 */

class Ul {

	/**
	 *
	 */

	protected $_settings = array( );



	/**
	 *
	 */

	protected $_articles = array( );



	/**
	 *
	 */

	public function __construct( ) {

		$this->_settings = $this->_loadJson( 'settings' );
		$this->_listArticles( );
	}



	/**
	 *
	 */

	protected function _loadJson( $path ) {

		$contents = @file_get_contents( UL_ROOT . $path . '.json' );

		if ( $contents !== false ) {
			$json = json_decode( $contents, true );

			if ( $json !== null ) {
				return $json;
			}
		}

		return array( );
	}



	/**
	 *
	 */

	protected function _listArticles( ) {

		$files = scandir( UL_ARTICLES );

		foreach ( $files as $file ) {
			if ( strpos( $file, '.md' ) !== false ) {
				$this->_articles[ ] = basename( $file, '.md' );
			}
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
		$request = array( );

		if ( $path == '/' ) {
			return $this->_render( 'home' );
		}

		$articlesPattern = '#^' . $this->_settings['articlesPath'] . '/?$#i';

		if ( preg_match( $articlesPattern, $path )) {
			return $this->_render( 'articles' );
		}

		$articlePattern = '#^' . $this->_settings['articlesPath'] . '/(.*)$#i';

		if ( preg_match( $articlePattern, $path, $matches )) {
			$article = $matches[ 1 ];

			if ( in_array( $article, $this->_articles )) {
				return $this->_renderArticle( $article );
			}
		}

		return $this->_render( '404' );
	}



	/**
	 *
	 */

	protected function _renderArticle( $article ) {

		$path = UL_ARTICLES . $article . '.md';
		$markdown = file_get_contents( $path );

		if ( $markdown === false ) {
			return $this->_render( '404' );
		}

		$vars = $this->_loadJson( 'articles' . DIRECTORY_SEPARATOR . $article );

		if ( !isset( $vars['title'])) {
			$vars['title'] = $article;
		}

		$vars['markdown'] = Markdown( $markdown );

		return $this->_render( 'article', $vars );
	}



	/**
	 *
	 */

	protected function _render( $___fileName, $___vars = array( )) {

		extract( $___vars, EXTR_SKIP );
		ob_start( );

		include UL_THEMES . $this->_settings['theme']
			. DIRECTORY_SEPARATOR . $___fileName . '.php';

		return ob_get_clean( );
	}
}

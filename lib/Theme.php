<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *	Theme.
 */

class Theme extends Configurable {

	/**
	 *	Theme name.
	 *
	 *	@var string
	 */

	public $name = '';



	/**
	 *	Constructor.
	 *
	 *	@param string $name Theme name.
	 */

	public function __construct( $name ) {

		$this->name = $name;

		if ( !is_dir( $this->path( ))) {
			throw new Exception( );
		}

		if ( !$this->hasPart( 'layout' )) {
			throw new Exception( );
		}
	}



	/**
	 *	Returns the path to a file inside the theme directory.
	 *
	 *	@param string $file File path relatively to the theme directory.
	 *	@return string File path.
	 */

	public function path( $file = false ) {

		$path = NJ_THEMES . $this->name;

		if ( is_string( $file )) {
			$path .= NJ_DS . str_replace( '/', NJ_DS, $file );
		}

		return $path;
	}



	/**
	 *	Returns the URL of a file inside the theme directory.
	 *
	 *	@param string $file File URL relatively to the theme directory.
	 *	@return string File URL.
	 */

	public function url( $file ) {

		return NJ_THEMES_URL . '/' . $this->name . '/' . $file;
	}



	/**
	 *	Tells if a part exists.
	 *
	 *	@param string $path Part path.
	 *	@return If the part exists.
	 */

	public function hasPart( $path ) {

		return file_exists( $this->path( $path . '.php' ));
	}



	/**
	 *	Renders and returns a part.
	 *
	 *	@param string $___path Part path.
	 *	@param array $___vars Additional vars to make available in the part.
	 *	@return string Rendered part.
	 */

	public function part( $___path, array $___vars = array( )) {

		extract( array_merge( $this->vars, $___vars ), EXTR_SKIP );
		ob_start( );

		include $this->path( $___path . '.php' );

		return ob_get_clean( );
	}
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Theme {

	use Configurable;



	/**
	 *
	 */

	public $name = '';



	/**
	 *
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
	 *
	 */

	public function path( $file = false ) {

		$path = NJ_THEMES . $this->name;

		if ( is_string( $file )) {
			$path .= NJ_DS . str_replace( '/', NJ_DS, $file );
		}

		return $path;
	}



	/**
	 *
	 */

	public function url( $file ) {

		return NJ_THEMES_URL . '/' . $this->name . '/' . $file;
	}



	/**
	 *
	 */

	public function hasPart( $path ) {

		return file_exists( $this->path( $path . '.php' ));
	}



	/**
	 *
	 */

	public function part( $___path, array $___vars = array( )) {

		extract( array_merge( $this->_vars, $___vars ), EXTR_SKIP );
		ob_start( );

		include $this->path( $___path . '.php' );

		return ob_get_clean( );
	}
}

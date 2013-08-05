<?php

/**
 *
 */



/**
 *
 */

class Theme {

	/**
	 *
	 */

	protected $_name = '';



	/**
	 *
	 */

	protected $_vars = array( );



	/**
	 *
	 */

	public function __construct( $name ) {

		$this->_name = $name;
	}



	/**
	 *
	 */

	public function path( $file ) {

		return UL_THEMES . $this->_name
			. UL_DS . str_replace( '/', UL_DS, $file );
	}



	/**
	 *
	 */

	public function url( $file ) {

		return UL_THEMES_URL . $this->_name . '/' . $file;
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

	public function get( $name, $default = null ) {

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

	public function part( $___fileName ) {

		extract( $this->_vars, EXTR_SKIP );
		ob_start( );

		include $this->path( $___fileName . '.php' );

		return ob_get_clean( );
	}
}

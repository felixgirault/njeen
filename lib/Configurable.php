<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

trait Configurable {

	/**
	 *
	 */

	protected $_vars = array( );



	/**
	 *
	 */

	public function __get( $name ) {

		return $this->get( $name );
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

	public function setDefaults( array $defaults ) {

		$this->_vars += $defaults;
	}
}

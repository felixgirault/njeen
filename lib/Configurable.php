<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Configurable implements ArrayAccess {

	/**
	 *
	 */

	public $vars = array( );



	/**
	 *
	 */

	public function __isset( $name ) {

		return $this->has( $name );
	}



	/**
	 *
	 */

	public function __get( $name ) {

		return $this->get( $name );
	}



	/**
	 *
	 */

	public function __set( $name, $value ) {

		$this->set( $name, $value );
	}



	/**
	 *
	 */

	public function has( $name ) {

		return isset( $this->vars[ $name ]);
	}



	/**
	 *
	 */

	public function get( $name, $default = null ) {

		return isset( $this->vars[ $name ])
			? $this->vars[ $name ]
			: $default;
	}



	/**
	 *
	 */

	public function set( $name, $value ) {

		$this->vars[ $name ] = $value;
	}



	/**
	 *
	 */

	public function setAll( array $vars ) {

		$this->vars = $vars;
	}



	/**
	 *
	 */

	public function setDefaults( array $defaults ) {

		$this->vars += $defaults;
	}



	/**
	 *
	 */

	public function merge( array $vars ) {

		$this->vars = array_merge( $this->vars, $name );
	}



	/**
	 *
	 */

	public function remove( $name ) {

		unset( $this->vars[ $name ]);
	}



	/**
	 *
	 */

	public function offsetExists( $offset ) {

		return $this->has( $offset );
	}



	/**
	 *
	 */

	public function offsetGet( $offset ) {

		return $this->get( $offset );
	}



	/**
	 *
	 */

	public function offsetSet( $offset, $value ) {

		$this->set( $offset, $value );
	}



	/**
	 *
	 */

	public function offsetUnset( $offset ) {

		$this->remove( $offset );
	}
}

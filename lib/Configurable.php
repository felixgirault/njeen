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
	 *	Variables.
	 *
	 *	@var array
	 */

	public $vars = array( );



	/**
	 *	@see has( )
	 */

	public function __isset( $name ) {

		return $this->has( $name );
	}



	/**
	 *	@see get( )
	 */

	public function __get( $name ) {

		return $this->get( $name );
	}



	/**
	 *	@see set( )
	 */

	public function __set( $name, $value ) {

		$this->set( $name, $value );
	}



	/**
	 *	Tells if a variable is available.
	 *
	 *	@param string $name Variable name.
	 *	@param boolean If the variable is available.
	 */

	public function has( $name ) {

		return isset( $this->vars[ $name ]);
	}



	/**
	 *	Returns a variable.
	 *
	 *	@param string $name Variable name.
	 *	@param mixed $default Default value if the variable doesn't exist.
	 *	@return mixed Variable value.
	 */

	public function get( $name, $default = null ) {

		return isset( $this->vars[ $name ])
			? $this->vars[ $name ]
			: $default;
	}



	/**
	 *	Sets a variable.
	 *
	 *	@param string $name Variable name.
	 *	@param mixed $value Variable value.
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
	 *	Removes a variable.
	 *
	 *	@param string $name Variable name.
	 */

	public function setDefaults( array $defaults ) {

		$this->vars += $defaults;
	}



	/**
	 *	Removes a variable.
	 *
	 *	@param string $name Variable name.
	 */

	public function merge( array $vars ) {

		$this->vars = array_merge( $this->vars, $name );
	}



	/**
	 *	Removes a variable.
	 *
	 *	@param string $name Variable name.
	 */

	public function remove( $name ) {

		unset( $this->vars[ $name ]);
	}



	/**
	 *	@see has( )
	 */

	public function offsetExists( $offset ) {

		return $this->has( $offset );
	}



	/**
	 *	@see get( )
	 */

	public function offsetGet( $offset ) {

		return $this->get( $offset );
	}



	/**
	 *	@see set( )
	 */

	public function offsetSet( $offset, $value ) {

		$this->set( $offset, $value );
	}



	/**
	 *	@see remove( )
	 */

	public function offsetUnset( $offset ) {

		$this->remove( $offset );
	}
}

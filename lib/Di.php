<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *	A simple dependency injection container.
 *	Inspired by Pimple (https://github.com/fabpot/Pimple).
 */

class Di extends Configurable {

	/**
	 *	Property filters.
	 *
	 *	@var array
	 */

	protected $_filters = array( );



	/**
	 *
	 */

	public function __construct( array $vars = array( )) {

		$this->vars = $vars;
	}



	/**
	 *	Adds a filter for the given property.
	 *
	 *	@param string $property Property name.
	 *	@param Closure $Filter Filter.
	 */

	public function addFilter( $property, Closure $Filter ) {

		$this->_filters[ $property ][ ] = $Filter;
	}



	/**
	 *	Returns the value of the given property.
	 *
	 *	@param string $name Property name.
	 *	@param mixed $default Default value to be returned in case the property
	 *		doesn't exists.
	 *	@return mixed The property value, or the result of the closure execution
	 *		if the property is a closure, or $default.
	 */

	public function get( $name, $default = null ) {

		$value = $default;

		if ( $this->has( $name )) {
			$value = $this->vars[ $name ];

			if ( $value instanceof Closure ) {
				$value = $value( $this );
			}

			if ( isset( $this->_filters[ $name ])) {
				foreach ( $this->_filters[ $name ] as $Filter ) {
					$Filter( $this, $value );
				}
			}
		}

		return $value;
	}



	/**
	 *	Prevents overwriting.
	 */

	public function set( $name, $value ) {

		if ( !$this->has( $name )) {
			parent::set( $name, $value );
		}
	}



	/**
	 *	Returns a wrapper that memoizes the result of the given closure.
	 *
	 *	@param Closure $Factory Closure to wrap.
	 *	@return Closure Wrapper.
	 */

	public static function unique( Closure $Factory ) {

		return function( $Di ) use ( $Factory ) {
			static $result = null;

			if ( $result === null ) {
				$result = $Factory( $Di );
			}

			return $result;
		};
	}



	/**
	 *
	 *
	 *	@param Closure $Factory Closure to wrap.
	 *	@return Closure Wrapper.
	 */

	public static function closure( Closure $Closure ) {

		return function( $Di ) use ( $Closure ) {
			return $Closure;
		};
	}
}

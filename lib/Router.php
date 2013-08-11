<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Router extends Configurable {

	/**
	 *
	 */

	protected $_rootUrl = '';



	/**
	 *	Translates between settings and theme entry types.
	 */

	protected $_map = array( );



	/**
	 *
	 */

	public function __construct( array $vars, $rootUrl = NJ_ROOT_URL ) {

		$this->_rootUrl = $rootUrl;
		$this->vars = $vars;

		foreach ( $this->vars['entries'] as &$path ) {
			$path = rtrim( $path, '/' );
		}
	}



	/**
	 *
	 */

	public function __call( $type, $arguments ) {

		if ( !isset( $this->entries[ $type ])) {
			throw new BadMethodCallException(
				"The '$type' entry type doesn't exist."
			);
		}

		return $this->single( $type, array_shift( $arguments ));
	}



	/**
	 *
	 */

	public function request( ) {

		$request = $_SERVER['REQUEST_URI'];

		if ( $request == '/' ) {
			return Request::home( );
		}

		foreach ( $this->entries as $type => $path ) {
			$listPattern = '#^' . $path . '/?$#';

			if ( preg_match( $listPattern, $request )) {
				return Request::index( $type );
			}

			$singlePattern = '#^' . $path . '/(?<id>' . $this->id . ')$#';

			if ( preg_match( $singlePattern, $request, $matches )) {
				return Request::single( $type, $matches['id']);
			}
		}

		return Request::error( 404 );
	}



	/**
	 *
	 */

	public function entryTypes( ) {

		return array_keys( $this->entries );
	}



	/**
	 *
	 */

	public function home( ) {

		return $this->_rootUrl;
	}



	/**
	 *
	 */

	public function index( $type ) {

		return $this->_rootUrl . $this->entries[ $type ];
	}



	/**
	 *
	 */

	public function single( $type, $id ) {

		return $this->_rootUrl . $this->entries[ $type ] . '/' . $id;
	}
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Routing;

use Njeen\Configurable;



/**
 *	Router.
 *
 *	@package Njeen.Routing
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

		return count( $arguments )
			? $this->single( $type, array_shift( $arguments ))
			: $this->index( $type );
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
	 *	Returns an URL pointing to the home page.
	 */

	public function home( ) {

		return $this->_rootUrl;
	}



	/**
	 *	Returns an URL pointing to a entries index.
	 *
	 *	@param string $type Entry type.
	 *	@return string URL.
	 */

	public function index( $type ) {

		return $this->_rootUrl . $this->entries[ $type ];
	}



	/**
	 *	Returns an URL pointing to a single entry.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@return string URL.
	 */

	public function single( $type, $id ) {

		return $this->_rootUrl . $this->entries[ $type ] . '/' . $id;
	}
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Routing;

use Njeen\Configurable;



/**
 *	Enumeration of request types.
 *
 *	@package Njeen.Routing
 */

class RequestType {

	const home = 'home';
	const index = 'index';
	const single = 'single';
	const error = 'error';
}



/**
 *	Request.
 *
 *	@package Njeen.Routing
 */

class Request extends Configurable {

	/**
	 *	Request type.
	 *
	 *	@var string
	 */

	protected $_type = '';



	/**
	 *	Constructor.
	 *
	 *	@param string $type Request type.
	 *	@param array $vars Request vars.
	 */

	public function __construct( $type, array $vars = array( )) {

		$this->_type = $type;
		$this->vars = $vars;
	}



	/**
	 *	Returns the request type.
	 *
	 *	@return string Type.
	 */

	public function type( ) {

		return $this->_type;
	}



	/**
	 *	Builds and returns a Request for the home page.
	 *
	 *	@return Request Request.
	 */

	public static function home( ) {

		return new Request( RequestType::home );
	}



	/**
	 *	Builds and returns a Request for an entries index page.
	 *
	 *	@param string $type Entry type.
	 *	@return Request Request.
	 */

	public static function index( $type ) {

		return new Request( RequestType::index, compact( 'type' ));
	}



	/**
	 *	Builds and returns a Request for a single entry page.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@return Request Request.
	 */

	public static function single( $type, $id ) {

		return new Request( RequestType::single, compact( 'type', 'id' ));
	}



	/**
	 *	Builds and returns a Request for an error page.
	 *
	 *	@param int $code HTTP code.
	 *	@return Request Request.
	 */

	public static function error( $code ) {

		return new Request( RequestType::error, compact( 'code' ));
	}
}

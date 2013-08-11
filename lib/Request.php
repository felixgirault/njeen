<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class RequestType {

	const home = 'home';
	const index = 'index';
	const single = 'single';
	const error = 'error';
}



/**
 *
 */

class Request extends Configurable {

	/**
	 *
	 */

	protected $_type = '';



	/**
	 *
	 */

	public function __construct( $type, array $vars = array( )) {

		$this->_type = $type;
		$this->vars = $vars;
	}



	/**
	 *
	 */

	public function type( ) {

		return $this->_type;
	}



	/**
	 *
	 */

	public static function home( ) {

		return new Request( RequestType::home );
	}



	/**
	 *
	 */

	public static function index( $type ) {

		return new Request( RequestType::index, compact( 'type' ));
	}



	/**
	 *
	 */

	public static function single( $type, $id ) {

		return new Request( RequestType::single, compact( 'type', 'id' ));
	}



	/**
	 *
	 */

	public static function error( $code ) {

		return new Request( RequestType::error, compact( 'code' ));
	}
}

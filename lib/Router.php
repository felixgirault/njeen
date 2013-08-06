<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Request {

	public $type = '';
	public $data = array( );

	public function __construct( $type, array $data = array( )) {

		$this->type = $type;
		$this->data = $data;
	}
}



/**
 *
 */

class Router {

	use Configurable;



	/**
	 *
	 */

	public function __construct( array $vars = array( )) {

		$this->_vars = $vars;
	}



	/**
	 *
	 */

	public function request( ) {

		$request = $_SERVER['REQUEST_URI'];

		if ( $request == '/' ) {
			return new Request( 'home' );
		}

		foreach ( $this->entries as $type => $path ) {
			$listPattern = '#^' . $path . '/?$#';

			if ( preg_match( $listPattern, $request )) {
				return new Request( 'index', array( 'type' => $type ));
			}

			$singlePattern = '#^' . $path . '/(?<id>' . $this->id . ')$#';

			if ( preg_match( $singlePattern, $request, $matches )) {
				return new Request(
					'single',
					array(
						'type' => $type,
						'id' => $matches['id']
					)
				);
			}
		}

		return new Request( 'error', array( 'code' => 404 ));
	}



	/**
	 *
	 */

	public function home( ) {

		return UL_ROOT_URL;
	}



	/**
	 *
	 */

	public function index( $type ) {

		return UL_ROOT_URL . '/' . $type;
	}



	/**
	 *
	 */

	public function entry( $type, $id ) {

		return UL_ROOT_URL . '/' . $type . '/' . $id;
	}



	/**
	 *
	 */

	public function entryTypes( ) {

		return array_keys( $this->entries );
	}
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Compiler {

	/**
	 *	Compilation steps.
	 *
	 *	@var array
	 */

	protected $_steps = array( );



	/**
	 *
	 */

	public function __construct( array $steps = array( )) {

		$this->_steps = $steps;
	}



	/**
	 *
	 */

	public function addStep( Closure $Step ) {

		$this->_steps[ ] = $Step;
	}



	/**
	 *
	 */

	public function compile( &$Entry ) {

		foreach ( $this->_steps as $step ) {
			$step( $Entry );
		}
	}
}

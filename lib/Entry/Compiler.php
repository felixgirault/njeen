<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Entry;

use Closure;



/**
 *	A compiler for entries.
 *
 *	@package Njeen.Entry
 */

class Compiler {

	/**
	 *	Compilation steps.
	 *
	 *	@var array
	 */

	protected $_steps = array( );



	/**
	 *	Constructor.
	 *
	 *	@param array $steps Compilation steps.
	 */

	public function __construct( array $steps = array( )) {

		$this->_steps = $steps;
	}



	/**
	 *	Adds a new compilation step.
	 *
	 *	@param Closure( &Entry ) $Step Compilation step.
	 */

	public function addStep( Closure $Step ) {

		$this->_steps[ ] = $Step;
	}



	/**
	 *	Compiles the given entry.
	 *
	 *	@param Entry $Entry Entry to compile.
	 */

	public function compile( &$Entry ) {

		foreach ( $this->_steps as $step ) {
			$step( $Entry );
		}
	}
}

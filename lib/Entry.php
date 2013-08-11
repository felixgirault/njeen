<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *	A blog entry.
 */

class Entry extends Configurable {

	/**
	 *
	 */

	public $type = '';



	/**
	 *
	 */

	public $id = '';



	/**
	 *
	 */

	public $body = '';



	/**
	 *
	 */

	public function __construct( $type, $id, array $vars, $body ) {

		$this->type = $type;
		$this->id = $id;
		$this->vars = $vars;
		$this->body = $body;
	}
}

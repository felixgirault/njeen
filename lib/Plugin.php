<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen;



/**
 *	An interface for plugins.
 *
 *	@package Njeen
 */

interface Plugin {

	/**
	 *	Hooks the plugin to Njeen through dependency injection.
	 *
	 *	@param Di $Di Dependency injection container.
	 */

	public function setup( &$Di );

}

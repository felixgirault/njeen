<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Plugin;

use Njeen\Di\Container as Di;
use DirectoryIterator;



/**
 *	A collection of plugins.
 *
 *	@package Njeen.Plugin
 */

class Collection {

	/**
	 *
	 */

	protected $_plugins = array( );



	/**
	 *
	 */

	public function load( Di &$Di ) {

		$Directory = new DirectoryIterator( NJ_PLUGINS );

		foreach ( $Directory as $File ) {
			if ( $File->isDir( )) {
				$name = $File->getBasename( );
				$class = "Njeen\\Plugin\\$name\\Plugin";

				if ( class_exists( $class )) {
					$Plugin = new $class( );
					$Plugin->setup( $Di );

					$this->_plugins[ ] = $Plugin;
				}
			}
		}
	}
}

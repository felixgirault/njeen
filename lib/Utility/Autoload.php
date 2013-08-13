<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Utility;



/**
 *	A simple PSR-0 compliant class loader.
 *
 *	@package Njeen.Utility
 */

class Autoload {

	/**
	 *	Sets autoload up on the given path.
	 *
	 *	@param string $basePath Base include path for all class files.
	 *	@param string $prefix
	 */

	public static function setup( $basePath, $prefix = '' ) {

		$basePath = rtrim( $basePath, NJ_DS );

		spl_autoload_register( function( $className ) use ( $basePath, $prefix ) {
			if ( strpos( $className, $prefix ) === 0 ) {
				$className = substr( $className, strlen( $prefix ));
			}

			$path = $basePath
				. NJ_DS
				. str_replace( '\\', NJ_DS, $className )
				. '.php';

			if ( file_exists( $path )) {
				require_once $path;
			}
		});
	}
}

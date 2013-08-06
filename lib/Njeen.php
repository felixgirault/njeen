<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Njeen {

	public static function run( ) {

		$settings = FileSystem::readJson( NJ_ROOT . 'settings.json' );

		$Router = new Router( $settings['router']);
		$Blog = new Blog( $settings['blog'], $Router );

		echo $Blog->page( );
	}
}

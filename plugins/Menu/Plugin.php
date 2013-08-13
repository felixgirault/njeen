<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Plugin\Menu;

use Njeen\Plugin as NjeenPlugin;
use Njeen\Plugin\Menu\Helper\Menu;
use Njeen\Di\Container as Di;



/**
 *
 */

class Plugin implements NjeenPlugin {

	/**
	 *
	 */

	public function setup( &$Di ) {

		$Di->set( 'Menu.Menu', Di::unique( function( $Di ) {
			return new Menu( $Di->get( 'Njeen.Settings' )->menu );
		}));

		$Di->addFilter( 'Njeen.Theme', function( $Di, &$Theme ) {
			$Theme->set( 'Menu', $Di->get( 'Menu.Menu' ));
		});
	}
}

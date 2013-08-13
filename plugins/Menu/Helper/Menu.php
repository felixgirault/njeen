<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Plugin\Menu\Helper;

use Njeen\Configurable;
use IteratorAggregate;
use ArrayIterator;



/**
 *
 */

class MenuItem {

	public $text;
	public $title;
	public $url;

	public function __construct( $text, $title, $url ) {

		$this->text = $text;
		$this->title = $title;
		$this->url = $url;
	}
}



/**
 *	Menu.
 *
 *	@package Njeen.Plugin.Menu.Helper
 */

class Menu extends Configurable implements IteratorAggregate {

	/**
	 *
	 */

	protected $_items = array( );



	/**
	 *
	 */

	public function __construct( array $definitions = array( )) {

		foreach ( $definitions as $text => $definition ) {
			$this->_items[ ] = new MenuItem(
				$text,
				isset( $definition['title'])
					? $definition['title']
					: $text,
				''
			);
		}
	}



	/**
	 *
	 */

	public function hasItems( ) {

		return !empty( $this->_items );
	}



	/**
	 *
	 */

	public function getIterator( ) {

		return new ArrayIterator( $this->_items );
	}
}

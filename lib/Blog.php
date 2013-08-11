<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

use dflydev\markdown\MarkdownExtraParser;



/**
 *
 */

class Blog extends Configurable {

	/**
	 *
	 */

	protected $_Theme = null;



	/**
	 *	Constructor.
	 */

	public function __construct( Theme $Theme, array $vars ) {

		$this->_Theme = $Theme;
		$this->vars = $vars;
	}



	/**
	 *
	 */

	public function page( Router $Router, EntryCollection $Entries ) {

		$this->_Theme->vars = array(
			'Blog' => $this,
			'Theme' => $this->_Theme,
			'Router' => $Router,
			'Entries' => $Entries
		);

		return $this->_Theme->part(
			'layout',
			array(
				'page' => $this->_renderPage( $Router->request( ), $Entries )
			)
		);
	}



	/**
	 *
	 */

	protected function _renderPage( Request $Request, EntryCollection $Entries ) {

		$page = '';

		switch ( $Request->type( )) {
			case RequestType::home:
				$page = $this->_Theme->part( 'home' );
				break;

			case RequestType::index:
				$type = $Request->type;
				$page = $this->_Theme->hasPart( "$type/index" )
					? $this->_Theme->part( "$type/index" )
					: $this->_Theme->part( 'entries/index' );
				break;

			case RequestType::single:
				$type = $Request->type;
				$id = $Request->id;

				if ( $Entries->exists( $type, $id )) {
					$Entry = $Entries->loadCompiled( $type, $id );
					$this->_Theme->set( 'Entry', $Entry );

					$page = $this->_Theme->hasPart( "$type/single" )
						? $this->_Theme->part( "$type/single" )
						: $this->_Theme->part( 'entries/single' );
				}
				break;

			case RequestType::error:
				$page = $this->error( $Request->code );
				break;
		}

		return $page;
	}



	/**
	 *
	 */

	public function error( $code ) {

		http_response_code( $code );

		return $this->_Theme->hasPart( "errors/$code" )
			? $this->_Theme->part( "errors/$code" )
			: '';
	}
}

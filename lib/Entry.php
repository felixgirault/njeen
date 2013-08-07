<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Entry {

	use Configurable;



	/**
	 *
	 */

	const raw = 'raw';
	const compiled = 'compiled';



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

	public function __construct( $type, $id, $format = self::compiled ) {

		$this->type = $type;
		$this->id = $id;

		switch ( $format ) {
			case self::raw:
				$this->_loadRaw( );
				break;

			case self::compiled:
			default:
				$this->_loadCompiled( );
				break;
		}
	}



	/**
	 *
	 */

	protected function _loadRaw( ) {

		$contents = FileSystem::readFile(
			NJ_ENTRIES . $this->type . NJ_DS . $this->id . '.md'
		);

		list( $header, $this->body ) = preg_split( '/\n\s*\n/mi', $contents, 2 );

		$lines = explode( PHP_EOL, $header );
		$meta = array( );

		foreach ( $lines as $line ) {
			list( $key, $value ) = explode( ':', $line, 2 );
			$meta[ trim( $key )] = trim( $value );
		}

		$this->set( $meta );
	}



	/**
	 *
	 */

	protected function _loadCompiled( ) {

		$path = NJ_COMPILED . $this->type . NJ_DS . $this->id;

		$this->set( FileSystem::readJson( $path . '.json' ));
		$this->body = FileSystem::readFile( $path . '.html' );
	}



	/**
	 *
	 */

	public function compile( $compilers ) {

		foreach ( $compilers as $compiler ) {
			if ( is_callable( $compiler )) {
				$this->body = call_user_func( $compiler, $this->body );
			}
		}
	}



	/**
	 *
	 */

	public function save( ) {

		$path = NJ_COMPILED . $this->type . NJ_DS . $this->id;

		FileSystem::writeJson( $path . '.json', $this->_vars );
		FileSystem::writeFile( $path . '.html', $this->body );
	}
}

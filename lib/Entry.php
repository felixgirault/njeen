<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class Entry {

	/**
	 *
	 */

	const raw = 'raw';
	const compiled = 'compiled';



	/**
	 *
	 */

	protected $_type = '';



	/**
	 *
	 */

	protected $_id = '';



	/**
	 *
	 */

	protected $_meta = array( );



	/**
	 *
	 */

	protected $_body = '';



	/**
	 *
	 */

	public function __construct( $type, $id, $format = self::compiled ) {

		$this->_type = $type;
		$this->_id = $id;

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

	public function __get( $name ) {

		if ( $name === 'body' ) {
			return $this->_body;
		}

		return isset( $this->_meta[ $name ])
			? $this->_meta[ $name ]
			: '';
	}



	/**
	 *
	 */

	protected function _loadRaw( ) {

		$body = FileSystem::readFile(
			UL_ENTRIES . $this->_type . UL_DS . $this->_id . '.md'
		);

		list( $header, $this->_body ) = preg_split( '/\n\s*\n/mi', $body, 2 );

		$lines = explode( PHP_EOL, $header );

		foreach ( $lines as $line ) {
			list( $key, $value ) = explode( ':', $line, 2 );
			$this->_meta[ trim( $key )] = trim( $value );
		}
	}



	/**
	 *
	 */

	protected function _loadCompiled( ) {

		$path = UL_COMPILED . $this->_type . UL_DS . $this->_id;

		$this->_meta = FileSystem::readJson( $path . '.json' );
		$this->_body = FileSystem::readFile( $path . '.html' );
	}



	/**
	 *
	 */

	public function compile( $compilers ) {

		foreach ( $compilers as $compiler ) {
			if ( is_callable( $compiler )) {
				$this->_body = call_user_func( $compiler, $this->_body );
			}
		}
	}



	/**
	 *
	 */

	public function save( ) {

		$path = UL_COMPILED . $this->_type . UL_DS . $this->_id;

		FileSystem::writeJson( $path . '.json', $this->_meta );
		FileSystem::writeFile( $path . '.html', $this->_body );
	}
}

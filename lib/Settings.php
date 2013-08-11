<?php

/**
 *
 */



/**
 *
 */

class Settings extends Configurable {

	/**
	 *
	 */

	protected $_path;



	/**
	 *
	 */

	public function __construct( $path, $load = true ) {

		$this->_path = $path;

		if ( $load ) {
			$this->load( );
		}
	}



	/**
	 *
	 */

	public function load( ) {

		$this->vars = FileSystem::readJson( $this->_path );
	}



	/**
	 *
	 */

	public function save( ) {

		FileSystem::writeJson( $this->_path, $this->vars );
	}



	/**
	 *
	 */

	public function lastModification( ) {

		return file_exists( $this->_path )
			? filemtime( $this->_path )
			: 0;
	}
}

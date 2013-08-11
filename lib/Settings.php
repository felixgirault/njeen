<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *	Settings.
 */

class Settings extends Configurable {

	/**
	 *	Settings file path.
	 *
	 *	@var string
	 */

	protected $_path;



	/**
	 *	Constructor.
	 *
	 *	@param string $path File path.
	 *	@param boolean $load Whether to load the file or not.
	 */

	public function __construct( $path, $load = true ) {

		$this->_path = $path;

		if ( $load ) {
			$this->load( );
		}
	}



	/**
	 *	Loads settings.
	 */

	public function load( ) {

		$this->vars = FileSystem::readJson( $this->_path );
	}



	/**
	 *	Saves settings.
	 *
	 *	@return boolean If the settings were succesfully saved.
	 */

	public function save( ) {

		return FileSystem::writeJson( $this->_path, $this->vars );
	}



	/**
	 *	Returns the last modification time of the settings file.
	 *
	 *	@return int Modification time.
	 */

	public function lastModification( ) {

		return file_exists( $this->_path )
			? filemtime( $this->_path )
			: 0;
	}
}

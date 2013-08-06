<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class FileSystem {

	/**
	 *
	 */

	public static function ensureDirectoryExists( $directory ) {

		if ( !is_dir( $directory )) {
			mkdir( $directory, 777, true );
		}
	}



	/**
	 *
	 */

	public static function readFile( $path ) {

		return file_get_contents( $path ) ?: 'toto';
	}



	/**
	 *
	 */

	public static function writeFile( $path, $contents ) {

		self::ensureDirectoryExists( dirname( $path ));
		file_put_contents( $path, $contents );
	}



	/**
	 *	Loads and returns a JSON document.
	 *
	 *	@param string $path Path to the file.
	 *	@return boolean Whether the file .
	 */

	public function readJson( $path ) {

		$data = null;

		if ( file_exists( $path )) {
			$contents = FileSystem::readFile( $path );
			$data = json_decode( $contents, true );
		}

		return $data ?: array( );
	}



	/**
	 *	Saves a JSON document.
	 */

	public function writeJson( $path, $data ) {

		FileSystem::writeFile( $path, json_encode( $data, JSON_PRETTY_PRINT ));
	}
}

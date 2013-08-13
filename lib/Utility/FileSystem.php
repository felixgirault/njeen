<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Utility;

use SplFileObject;



/**
 *	A simple interface to the file system.
 *
 *	@package Njeen.Utility
 */

class FileSystem {

	/**
	 *	Ensures that a directory exists by creating it if necessary.
	 *
	 *	@param string $path Path to the directory.
	 */

	public static function ensureDirectoryExists( $path ) {

		if ( !is_dir( $path )) {
			mkdir( $path, 0777, true );
		}
	}



	/**
	 *	Writes contents into a file.
	 *
	 *	@param string $path Path to the file.
	 *	@return string File contents.
	 */

	public static function readFile( $path ) {

		try {
			$File = new SplFileObject( $path, 'r' );
		} catch ( RuntimeException $Exception ) {
			return '';
		}

		$File->flock( LOCK_SH );
		$lines = array( );

		while ( !$File->eof( )) {
			$lines[ ] = $File->fgets( );
		}

		$File->flock( LOCK_UN );
		return implode( '', $lines );
	}



	/**
	 *	Writes contents into a file.
	 *
	 *	@param string $path Path to the file.
	 *	@param string $contents Contents.
	 *	@return boolean If the file was written succesfully.
	 */

	public static function writeFile( $path, $contents ) {

		self::ensureDirectoryExists( dirname( $path ));

		$File = new SplFileObject( $path, 'c' );
		$File->flock( LOCK_EX );

		$success = $File->ftruncate( 0 )
			&& $File->fwrite( $contents )
			&& $File->fflush( );

		$File->flock( LOCK_UN );

		chmod( $File->getRealPath( ), 0664 );
		return $success;
	}



	/**
	 *	Loads and returns a JSON document.
	 *
	 *	@param string $path Path to the file.
	 *	@return boolean Whether the file .
	 */

	public static function readJson( $path ) {

		$data = null;

		if ( file_exists( $path )) {
			$contents = FileSystem::readFile( $path );
			$data = json_decode( $contents, true );
		}

		return $data ?: array( );
	}



	/**
	 *	Saves a JSON document.
	 *
	 *	@param string $path Path to the file.
	 *	@param mixed $data Json data.
	 */

	public static function writeJson( $path, $data ) {

		$json = json_encode( $data, JSON_PRETTY_PRINT );

		return ( $json === false )
			? false
			: FileSystem::writeFile( $path, $json );
	}
}

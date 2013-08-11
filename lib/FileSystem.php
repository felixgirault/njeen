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
			mkdir( $directory, 0777, true );
		}
	}



	/**
	 *
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
	 *
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
	 */

	public static function writeJson( $path, $data ) {

		FileSystem::writeFile( $path, json_encode( $data, JSON_PRETTY_PRINT ));
	}
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *
 */

class EntryCollection {

	/**
	 *
	 */

	protected $_types = '';



	/**
	 *
	 */

	protected $_treshold = 0;



	/**
	 *
	 */

	protected $_raw = '';



	/**
	 *
	 */

	protected $_compiled = '';



	/**
	 *
	 */

	protected $_index = '';



	/**
	 *
	 */

	public function __construct(
		$types,
		$treshold = 60,
		$raw = NJ_ENTRIES,
		$compiled = NJ_COMPILED
	) {
		$this->_types = $types;
		$this->_treshold = $treshold;
		$this->_raw = $raw;
		$this->_compiled = $compiled;

		$this->_index = new Settings( $compiled . 'index.json' );
	}



	/**
	 *
	 */

	public function shouldCompile( ) {

		$diff = time( ) - $this->_index->lastModification( );
		return $diff > $this->_treshold;
	}



	/**
	 *
	 */

	public function compile( Compiler $Compiler ) {

		$index = array( );

		foreach ( $this->_types as $type ) {
			$directory = $this->_raw . $type;

			if ( is_dir( $directory )) {
				$Directory = new DirectoryIterator( $directory );

				foreach ( $Directory as $File ) {
					$extension = $File->getExtension( );
					$id = $File->getBasename( ".$extension" );
					$mtime = $File->getMTime( );

					if (
						isset( $this->_index[ $type ][ $id ])
						&& ( $this->_index[ $type ][ $id ] === $mtime )
					) {
						$index[ $type ][ $id ] = $this->_index[ $type ][ $id ];
					} else {
						$index[ $type ][ $id ] = $mtime;

						$Entry = $this->loadRaw( $type, $id, $extension );
						$Compiler->compile( $Entry );
						$this->saveEntry( $Entry );
					}
				}
			}
		}

		$this->_index->setAll( $index );
		$this->_index->save( );
	}



	/**
	 *
	 */

	public function exists( $type, $id ) {

		return isset( $this->_index[ $type ][ $id ]);
	}



	/**
	 *
	 */

	public function loadRaw( $type, $id, $extension ) {

		$contents = FileSystem::readFile(
			$this->_raw . $type . NJ_DS . $id . '.' . $extension
		);

		list( $header, $body ) = preg_split( '/\n\s*\n/mi', $contents, 2 );

		$lines = explode( PHP_EOL, $header );
		$meta = array( );

		foreach ( $lines as $line ) {
			list( $key, $value ) = explode( ':', $line, 2 );
			$meta[ trim( $key )] = trim( $value );
		}

		return new Entry( $type, $id, $meta, $body );
	}



	/**
	 *
	 */

	public function loadCompiled( $type, $id ) {

		$path = $this->_compiled . $type . NJ_DS . $id;

		return new Entry(
			$type,
			$id,
			FileSystem::readJson( $path . '.json' ),
			FileSystem::readFile( $path . '.html' )
		);
	}



	/**
	 *
	 */

	public function save( $Entry ) {

		$path = $this->_compiled . $Entry->type . NJ_DS . $Entry->id;

		FileSystem::writeJson( $path . '.json', $Entry->meta );
		FileSystem::writeFile( $path . '.html', $Entry->body );
	}
}

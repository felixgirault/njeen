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

	protected $_Index = '';



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

		$this->_Index = new Settings( $compiled . 'index.json' );
	}



	/**
	 *
	 */

	public function shouldCompile( ) {

		$diff = time( ) - $this->_Index->lastModification( );
		return $diff > $this->_treshold;
	}



	/**
	 *
	 */

	public function compile( Compiler $Compiler ) {

		$index = array( );

		foreach ( $this->_types as $type ) {
			$directory = $this->_raw . $type;

			try {
				$Directory = new DirectoryIterator( $directory );
			} catch ( UnexpectedValueException $Exception ) {
				continue;
			}

			foreach ( $Directory as $File ) {
				if ( $File->isDir( )) {
					continue;
				}

				$extension = $File->getExtension( );
				$id = $File->getBasename( ".$extension" );
				$mtime = $File->getMTime( );

				if (
					isset( $this->_Index[ $type ][ $id ])
					&& ( $this->_Index[ $type ][ $id ] === $mtime )
				) {
					$index[ $type ][ $id ] = $this->_Index[ $type ][ $id ];
				} else {
					$index[ $type ][ $id ] = $mtime;

					$Entry = $this->loadRaw( $type, $id, $extension );
					$Compiler->compile( $Entry );
					$this->save( $Entry );
				}
			}
		}

		$this->_Index->setAll( $index );
		$this->_Index->save( );
	}



	/**
	 *
	 */

	public function exists( $type, $id ) {

		return isset( $this->_Index[ $type ][ $id ]);
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
		$vars = array( );

		foreach ( $lines as $line ) {
			list( $key, $value ) = explode( ':', $line, 2 );
			$vars[ trim( $key )] = trim( $value );
		}

		return new Entry( $type, $id, $vars, $body );
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

		FileSystem::writeJson( $path . '.json', $Entry->vars );
		FileSystem::writeFile( $path . '.html', $Entry->body );
	}
}

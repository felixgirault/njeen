<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *	A collection of entries.
 */

class EntryCollection {

	/**
	 *	Entry types.
	 *
	 *	@var array
	 */

	protected $_types = array( );



	/**
	 *	Compilation treshold.
	 *
	 *	@var int
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
	 *	Entries index.
	 *
	 *	@var Settings
	 */

	protected $_Index = null;



	/**
	 *	Constructor.
	 *
	 *	@param array $types Entry types.
	 *	@param int $treshold Compilation treshold.
	 */

	public function __construct( array $types, $treshold = 60 ) {
		$this->_types = $types;
		$this->_treshold = $treshold;

		$this->_Index = new Settings( NJ_COMPILED . 'index.json' );
	}



	/**
	 *	Tells if a compilation should be made.
	 *
	 *	@return boolean If a compilation should be made.
	 */

	public function shouldCompile( ) {

		$diff = time( ) - $this->_Index->lastModification( );
		return $diff > $this->_treshold;
	}



	/**
	 *	Compiles entries.
	 *
	 *	@param Compiler $Compiler Entry compiler.
	 */

	public function compile( Compiler $Compiler ) {

		$index = array( );

		foreach ( $this->_types as $type ) {
			$directory = NJ_ENTRIES . $type;

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
					isset( $this->_Index[ $type ][ $id ]['modification'])
					&& ( $this->_Index[ $type ][ $id ]['modification'] === $mtime )
				) {
					$index[ $type ][ $id ] = $this->_Index[ $type ][ $id ];
				} else {
					$Entry = $this->_loadRaw( $type, $id, $extension );
					$Compiler->compile( $Entry );
					$this->save( $Entry );

					$index[ $type ][ $id ] = array(
						'creation' => $Entry->creation,
						'modification' => $Entry->modification
					);
				}
			}
		}

		$this->_Index->setAll( $index );
		$this->_Index->save( );
	}



	/**
	 *	Tells if an entry exists.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@return boolean If the entry exists.
	 */

	public function exists( $type, $id ) {

		return isset( $this->_Index[ $type ][ $id ]);
	}



	/**
	 *	Loads a raw entry.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@param string $extension File extension.
	 *	@return Entry Entry.
	 */

	public function _loadRaw( $type, $id, $extension ) {

		$path = NJ_ENTRIES . $type . NJ_DS . $id . '.' . $extension;
		list( $vars, $body ) = $this->_parse( FileSystem::readFile( $path ));

		$vars['creation'] = filectime( $path );
		$vars['modification'] = filemtime( $path );

		return new Entry( $type, $id, $vars, $body );
	}



	/**
	 *	Parses entry contents.
	 *
	 *	@param string $contents Entry contents.
	 *	@return array( $vars, $body ) Entry's vars and body.
	 */

	protected function _parse( $contents ) {

		list( $header, $body ) = preg_split( '/\n\s*\n/mi', $contents, 2 );

		$lines = explode( PHP_EOL, $header );
		$vars = array( );

		foreach ( $lines as $line ) {
			list( $key, $value ) = explode( ':', $line, 2 );
			$vars[ trim( $key )] = trim( $value );
		}

		return array( $vars, $body );
	}



	/**
	 *	Loads a compiled entry.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@return Entry Entry.
	 */

	public function load( $type, $id ) {

		$path = NJ_COMPILED . $type . NJ_DS . $id;

		return new Entry(
			$type,
			$id,
			FileSystem::readJson( $path . '.json' ),
			FileSystem::readFile( $path . '.html' )
		);
	}



	/**
	 *	Saves the given entry.
	 *
	 *	@param Entry $Entry Entry.
	 *	@return If the entry was succesfully saved.
	 */

	public function save( $Entry ) {

		$path = NJ_COMPILED . $Entry->type . NJ_DS . $Entry->id;

		return FileSystem::writeJson( $path . '.json', $Entry->vars )
			&& FileSystem::writeFile( $path . '.html', $Entry->body );
	}
}

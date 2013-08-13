<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Entry;

use Njeen\Settings;
use DirectoryIterator;



/**
 *	A collection of entries.
 *
 *	@package Njeen.Entry
 */

class Collection {

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

				$id = $File->getBasename( '.' . $File->getExtension( ));
				$mtime = $File->getMTime( );

				if (
					isset( $this->_Index[ $type ][ $id ]['modification'])
					&& ( $this->_Index[ $type ][ $id ]['modification'] === $mtime )
				) {
					$index[ $type ][ $id ] = $this->_Index[ $type ][ $id ];
				} else {
					$Entry = new Entry( $type, $id, false );
					$Entry->loadRaw( $File->getRealPath( ));
					$Compiler->compile( $Entry );
					$Entry->save( );

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
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen;

use Njeen\Utility\FileSystem;



/**
 *	A blog entry.
 *
 *	@package Njeen
 */

class Entry extends Configurable {

	/**
	 *	Entry type.
	 *
	 *	@var string
	 */

	public $type = '';



	/**
	 *	Entry id.
	 *
	 *	@var string
	 */

	public $id = '';



	/**
	 *	Entry body.
	 *
	 *	@var string
	 */

	public $body = '';



	/**
	 *	Constructor.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@param boolean $load Whether to load the entry or not.
	 */

	public function __construct( $type, $id, $load = true ) {

		$this->type = $type;
		$this->id = $id;

		if ( $load ) {
			$this->load( );
		}
	}



	/**
	 *	Loads a raw entry.
	 *
	 *	@param string $type Entry type.
	 *	@param string $id Entry id.
	 *	@param string $extension File extension.
	 *	@return Entry Entry.
	 */

	public function loadRaw( $path ) {

		$contents = FileSystem::readFile( $path );
		list( $this->vars, $this->body ) = $this->_parse( $contents );

		$this->creation = filectime( $path );
		$this->modification = filemtime( $path );
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
	 *	Loads the entry.
	 */

	public function load( ) {

		$path = NJ_COMPILED . $this->type . NJ_DS . $this->id;

		$this->vars = FileSystem::readJson( $path . '.json' );
		$this->body = FileSystem::readFile( $path . '.html' );
	}



	/**
	 *	Saves the entry.
	 *
	 *	@return If the entry was succesfully saved.
	 */

	public function save( ) {

		$path = NJ_COMPILED . $this->type . NJ_DS . $this->id;

		return FileSystem::writeJson( $path . '.json', $this->vars )
			&& FileSystem::writeFile( $path . '.html', $this->body );
	}
}

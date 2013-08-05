<?php

/**
 *
 */



/**
 *
 */

class Settings implements ArrayAccess, IteratorAggregate {

	/**
	 *
	 */

	protected $_path = '';



	/**
	 *
	 */

	protected $_data = array( );



	/**
	 *	Constructor.
	 */

	public function __construct( $path ) {

		$this->_path = $path;

		if ( !$this->load( )) {
			throw new Exception( "Error parsing JSON file: $path." );
		}
	}



	/**
	 *	Loads and returns a JSON document.
	 *
	 *	@param string $path Path to the file.
	 *	@return boolean Whether the file .
	 */

	public function load( ) {

		if ( !file_exists( $this->_path )) {
			return false;
		}

		$contents = file_get_contents( $this->_path );
		$this->_data = json_decode( $contents, true );

		return ( $this->_data !== null );
	}



	/**
	 *	Saves a JSON document.	 *
	 */

	public function save( ) {

		file_put_contents(
			$this->_path,
			json_encode(
				$this->_data,
				JSON_PRETTY_PRINT
			)
		);
	}



	/**
	 *
	 */

	public function offsetSet( $offset, $value ) {

		if ( $offset === null ) {
			$this->_data[ ] = $value;
		} else {
			$this->_data[ $offset ] = $value;
		}
	}



	/**
	 *
	 */

	public function offsetExists( $offset ) {

		return isset( $this->_data[ $offset ]);
	}



	/**
	 *
	 */

	public function offsetUnset( $offset ) {

		unset( $this->_data[ $offset ]);
	}



	/**
	 *
	 */

	public function offsetGet( $offset ) {

		return isset( $this->_data[ $offset ])
			? $this->_data[ $offset ]
			: null;
	}



	/**
	 *
	 */

	public function getIterator( ) {

		return new ArrayIterator( $this->_data );
	}
}

<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */



/**
 *	An utility class to build HTML tags.
 */

class Html {

	/**
	 *	A list of self-closing HTML tags.
	 *
	 *	@var array
	 */

	public static $selfClosingTags = array(
		'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input',
		'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
	);



	/**
	 *	Builds and return an <a> tag.
	 *
	 *	@param string $text Link text.
	 *	@param string $href Link URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public static function link( $text, $href, array $attributes = array( )) {

		$attributes['href'] = $href;
		return self::tag( 'a', $text, $attributes );
	}



	/**
	 *	Builds and return an accessible <a> tag.
	 *
	 *	@param string $text Link text.
	 *	@param string $title Link title.
	 *	@param string $href Link URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public static function aLink( $text, $title, $href, array $attributes = array( )) {

		$attributes['title'] = $title;
		return self::link( $text, $href, $attributes );
	}



	/**
	 *	Builds and return an <img> tag.
	 *
	 *	@param string $src Image URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public static function image( $src, array $attributes = array( )) {

		$attributes['src'] = $src;
		return self::tag( 'img', $text, $attributes );
	}



	/**
	 *	Builds and return an accessible <img> tag.
	 *
	 *	@param string $src Image URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public static function aImage( $alt, $src, array $attributes = array( )) {

		$attributes['alt'] = $alt;
		return self::image( $src, $attributes );
	}



	/**
	 *	Builds and return an HTML tag.
	 *
	 *	@param string $name Tag name.
	 *	@param string $contents Tag contents.
	 *	@param array $attributes Tag attributes.
	 *	@return string Tag.
	 */

	public static function tag( $name, $contents = '', array $attributes = array( )) {

		// attributes

		$attributesString = '';

		foreach ( $attributes as $attrName => $attrValue ) {
			if ( is_array( $attrValue )) {
				$attrValue = implode( ' ', $attrValue );
			}

			if ( is_numeric( $attrName )) {
				$attrName = $attrValue;
			}

			$attributesString .= ' ' . $attrName .'="' . $attrValue . '"';
		}

		// tag

		$tag = '<' . $name . $attributesString;

		if ( in_array( $name, self::$selfClosingTags )) {
			$tag .= ' />';
		} else {
			$tag .= '>' . $contents . '</' . $name . '>';
		}

		return $tag;
	}
}

<?php

/**
 *
 */

class Html {

	/**
	 *
	 */

	public static $selfClosingTags = array(
		'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input',
		'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
	);



	/**
	 *
	 */

	public static function link( $text, $url, array $attributes = array( )) {

		$attributes['href'] = $url;
		return self::tag( 'a', $attributes, $text );
	}



	/**
	 *
	 */

	public static function aLink( $text, $title, $url, array $attributes = array( )) {

		$attributes['title'] = $title;
		return self::link( $text, $url, $attributes );
	}



	/**
	 *
	 */

	public static function tag( $name, $attributes, $contents = '' ) {

		$attributesString = '';

		foreach ( $attributes as $attributeName => $attributeValue ) {
			if ( is_numeric( $attributeName )) {
				$attributeName = $attributeValue;
			}

			$attributesString .= ' ' . $attributeName .'="' . $attributeValue . '"';
		}

		$tag = '<' . $name . $attributesString;

		if ( in_array( $name, self::$selfClosingTags )) {
			$tag .= ' />';
		} else {
			$tag .= '>' . $contents . '</' . $name . '>';
		}

		return $tag;
	}
}

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
		return self::tag( 'a', $text, $attributes );
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

	public static function tag( $name, $contents = '', array $attributes = array( )) {

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

		$tag = '<' . $name . $attributesString;

		if ( in_array( $name, self::$selfClosingTags )) {
			$tag .= ' />';
		} else {
			$tag .= '>' . $contents . '</' . $name . '>';
		}

		return $tag;
	}
}

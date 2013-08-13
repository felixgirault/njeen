<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Helper;

use Njeen\Configurable;



/**
 *	A helper class to build HTML tags.
 *
 *	@package Njeen.Helper
 */

class Html extends Configurable {

	/**
	 *
	 */

	public $vars = array(
		// a list of self-closing HTML tags
		'selfClosingTags' => array(
			'area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input',
			'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'
		),
		// the date format for time( )
		'dateFormat' => 'F j, Y'
	);



	/**
	 *	Builds and return an <a> tag.
	 *
	 *	@param string $text Link text.
	 *	@param string $href Link URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public function link( $text, $href, array $attributes = array( )) {

		$attributes['href'] = $href;
		return $this->tag( 'a', $text, $attributes );
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

	public function aLink( $text, $title, $href, array $attributes = array( )) {

		$attributes['title'] = $title;
		return $this->link( $text, $href, $attributes );
	}



	/**
	 *	Builds and return an <img> tag.
	 *
	 *	@param string $src Image URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public function image( $src, array $attributes = array( )) {

		$attributes['src'] = $src;
		return $this->tag( 'img', $attributes );
	}



	/**
	 *	Builds and return an accessible <img> tag.
	 *
	 *	@param string $src Image URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public function aImage( $alt, $src, array $attributes = array( )) {

		$attributes['alt'] = $alt;
		return $this->image( $src, $attributes );
	}



	/**
	 *	Builds and return an <img> tag.
	 *
	 *	@param string $src Image URL.
	 *	@param array $attributes HTML attributes.
	 *	@return string Tag.
	 */

	public function time( $timestamp, $pubdate = true, array $attributes = array( )) {

		$attributes['datetime'] = date( DATE_W3C, $timestamp );

		if ( $pubdate ) {
			$attributes['pubdate'] = 'pubdate';
		}

		return $this->tag(
			'time',
			date( $this->dateFormat, $timestamp ),
			$attributes
		);
	}



	/**
	 *	Builds and return an HTML tag.
	 *	If the tag has no contents, attributes can be passed as the second
	 *	parameter.
	 *
	 *	@param string $name Tag name.
	 *	@param string $contents Tag contents.
	 *	@param array $attributes Tag attributes.
	 *	@return string Tag.
	 */

	public function tag( $name, $contents = '', array $attributes = array( )) {

		if ( is_array( $contents )) {
			$attributes = $contents;
			$contents = '';
		}

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

		if ( in_array( $name, $this->selfClosingTags )) {
			$tag .= ' />';
		} else {
			$tag .= '>' . $contents . '</' . $name . '>';
		}

		return $tag;
	}
}

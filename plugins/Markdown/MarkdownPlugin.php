<?php

/**
 *
 */

use dflydev\markdown\MarkdownExtraParser;



/**
 *
 */

class MarkdownPlugin {

	/**
	 *
	 */

	public static function setup( &$Di ) {

		$Di->set( 'Markdown.Parser', Di::unique( function( $Di ) {
			return new MarkdownExtraParser( );
		}));

		$Di->addFilter( 'Njeen.Compiler', function( $Di, &$Compiler ) {
			$Compiler->addStep( function( &$Entry ) use ( $Di ) {
				$Parser = $Di->get( 'Markdown.Parser' );
				$Entry->body = $Parser->transformMarkdown( $Entry->body );
			});
		});
	}
}

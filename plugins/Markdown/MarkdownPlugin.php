<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

require_once dirname( __FILE__ ) . NJ_DS . 'vendor' . NJ_DS . 'autoload.php';

use dflydev\markdown\MarkdownExtraParser;



/**
 *
 */

class MarkdownPlugin implements Plugin {

	/**
	 *
	 */

	public function setup( &$Di ) {

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

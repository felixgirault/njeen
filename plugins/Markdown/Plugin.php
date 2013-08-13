<?php

/**
 *	@author Félix Girault <felix.girault@gmail.com>
 *	@license FreeBSD License (http://opensource.org/licenses/BSD-2-Clause)
 */

namespace Njeen\Plugin\Markdown;

use Njeen\Plugin as NjeenPlugin;
use Njeen\Di\Container as Di;
use dflydev\markdown\MarkdownExtraParser;



/**
 *
 */

class Plugin implements NjeenPlugin {

	/**
	 *
	 */

	public function setup( &$Di ) {

		require_once dirname( __FILE__ )
			. NJ_DS . 'vendor'
			. NJ_DS . 'autoload.php';

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

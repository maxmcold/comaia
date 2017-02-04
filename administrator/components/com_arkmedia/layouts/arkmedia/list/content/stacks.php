<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper;

defined( '_JEXEC' ) or die;

$viewobj		= $displayData;
$event			= $displayData->event;
$stacks			= $displayData->items;
$options 		= $displayData->content;
$id				= Helper::css( 'content' );

// Root Container
echo '<div id="' . $id . '">';

// Loop Through Stacks
foreach( $stacks as $name => $stack )
{
	// Stack Container
	echo '<div data-stack="' . $name . '">';

		// Stack Content Container
		echo '<div data-stack-content>';

		// Well-Formed Stack With Valid Path (invalid & empty directories are handled in JS)
		if( isset( $stack->items ) && isset( $stack->items[$stack->path] ) && $stack->path )
		{
			// Render All Folders & Files in All Available Views
			foreach( $options->get( 'views' ) as $view )
			{
				// View Container
				echo '<div id="' . $name . '-' . $view . '" class="' . Helper::css( 'grid.row' ) . '" data-view="' . $view . '">';

				// Stack HTML Will be Injected Here

				// Close View
				echo '</div>';
			}//end foreach
		}//end if

		// Close Stack Content
		echo '</div>';

		// Stack Message Container
		echo '<div data-stack-message>';

		// Stack Message Insertion Container
		echo '<div data-stack-message-insert></div>';

		// Insert Messages as Hidden Scripts
		echo Helper::layout( 'message.stack', (object)array( 'stack' => $name, 'view' => $viewobj ) );

		// Insert Custom Messages as Hidden Scripts
		$event->trigger( 'onArkRenderStackMessage', array( &$displayData ) );

		// Close Stack Message
		echo '</div>';

	// Close Stack
	echo '</div>';
}//end foreach

// Close Root
echo '</div>';

// Render One Set of View Templates
foreach( $options->get( 'views' ) as $view )
{
	echo Helper::layout( 'list.content.' . $view . '.stack', (object)array( 'view' => $view, 'options' => $options ) );

	echo Helper::layout( 'list.content.' . $view . '.back', (object)array( 'view' => $view, 'options' => $options ) );

	echo Helper::layout( 'list.content.' . $view . '.folder', (object)array( 'view' => $view, 'options' => $options ) );

	echo Helper::layout( 'list.content.' . $view . '.file', (object)array( 'view' => $view, 'options' => $options ) );
}//end foreach

?>
<script id="tmpl-content" type="text/x-jsrender">
	{^{include tmpl=~getContentTemplate( 'stack' ) /}}
</script>
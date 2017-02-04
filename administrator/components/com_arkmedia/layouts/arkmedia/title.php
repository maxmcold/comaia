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

$stacks		= $displayData->items;
$id			= Helper::css( 'title' );

// Root Container
echo '<div id="' . $id . '">';

// Loop Through Stacks
foreach( $stacks as $name => $stack )
{
	// Has the Stack Opted Out of the Path Check?
	$checkpath = (bool)$stack->params->get( 'register-path', true );

	// Well-Formed Stack 
	if( $stack->path || !$checkpath )
	{
		// Stack Container
		echo '<h2 data-stack="' . $name . '">';

		// Stack HTML Will be Injected Here

		// Close Stack
		echo '</h2>';
	}//end if
}//end foreach

// Close Root
echo '</div>';
?>
<script id="tmpl-title" type="text/x-jsrender">
	{^{:stack.title}}
	{^{if file.value}}
		<small>
			[{{:file.value}}]
		</small>
	{{else folders.length + files.length}}
		<small>
			({^{:folders.length + files.length}} <?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_SELECTED_LBL' ); ?>)
		</small>
	{{else stack.subtitle}}
		<small>
			[{^{:stack.subtitle}}]
		</small>
	{{/if}}
</script>
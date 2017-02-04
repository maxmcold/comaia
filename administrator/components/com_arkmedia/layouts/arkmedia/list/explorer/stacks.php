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

$stacks			= $displayData->items;
$options 		= $displayData->explorer;
$id				= Helper::css( 'explorer' );
$css_stack		= Helper::css( 'ark.scrollbar.container' );
$css_overlay	= array( 'overlay', Helper::css( 'ark.var.hide' ) );
$css_tree		= array( 'tree', Helper::css( 'ark.scrollbar.content' ) );

// Root Container
echo '<div id="' . $id . '">';

// Disable Overlay
echo '<div class="' . implode( "\n", $css_overlay ) . '"></div>';

// Loop Through Stacks
foreach( $stacks as $name => $stack )
{
	// Well-Formed Stack
	// @note	There is No Ability to Opt-Out of Path Check (like title) as Path is Needed
	if( $stack->path )
	{
		// Stack Container
		echo '<div class="' . $css_stack . '" data-stack="' . $name . '">';

		// Stack HTML Will be Injected Here

		// Close Stack
		echo '</div>';
	}//end if
}//end foreach

// Close Root
echo '</div>';

?>
<script id="tmpl-explorer" type="text/x-jsrender">
	<ul class="<?php echo implode( "\n", $css_tree ); ?>">{^{tree init=true path=path /}}</ul>
</script>

<?php echo Helper::layout( 'list.explorer.stack' ); ?>

<?php echo Helper::layout( 'list.explorer.folder' ); ?>

<?php echo Helper::layout( 'list.explorer.file' ); ?>
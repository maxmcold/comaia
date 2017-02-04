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
$options 		= $displayData->breadcrumb;
$id				= Helper::css( 'breadcrumb' );
$css 			= Helper::css( 'box.breadcrumb.container' ) . chr( 32 ) . Helper::css( 'box.breadcrumb.small' );

// Root Container
echo '<div id="' . $id . '">';

// Loop Through Stacks
foreach( $stacks as $name => $stack )
{
	// Well-Formed Stack
	// @note	There is No Ability to Opt-Out of Path Check (like title) as it is Too Integral Here
	if( $stack->path )
	{
		// Stack Container
		echo '<div data-stack="' . $name . '">';

		// Stack HTML Will be Injected Here

		// Close Stack
		echo '</div>';
	}//end if
}//end foreach

// Close Root
echo '</div>';
?>
<script id="tmpl-breadcrumb" type="text/x-jsrender">
	<ol class="<?php echo $css; ?>">

		{{!-- /* Loop Active Stack Path */ --}}
		{^{breadcrumb}}

			{{!-- /* If Config Render FullPath */ --}}
			{^{if ~canBreadcrumb()}}
				{^{if ~isDisabledBreadcrumb(~root.file.value)}}
					<li>{^{beautify:name}}</li>
				{{else}}
					<li><a href="javascript:void(0);" data-stack-control="path" data-link="data-path{:path}">{^{beautify:name}}</a></li>
				{{/if}}
			{{/if}}

		{{/breadcrumb}}

	</ol>
</script>
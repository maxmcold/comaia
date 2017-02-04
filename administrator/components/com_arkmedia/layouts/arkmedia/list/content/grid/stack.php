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

$view			= $displayData->view;
$options 		= $displayData->options;
?>
<script id="tmpl-content-<?php echo $view;?>-stack" type="text/x-jsrender">
	<div class="<?php echo Helper::css( 'grid.column.medium.12' ); ?>">

		{{!-- /* Render Back Button */ --}}
		{^{if ~root.base != ~root.active.path tmpl=~getContentTemplate( 'back' ) /}}

		{{!-- /* Get Child Folders */ --}}
		{^{props ~root.stack}}
			{^{if ~isChildFolder( key, prop.name, ~root.active.path ) tmpl=~getContentTemplate( 'folder' ) /}}
		{{/props}}

		{{!-- /* Get Current Folder */ --}}
		{^{props ~root.stack}}
			{^{if key == ~root.active.path}}
				{^{props prop.items tmpl=~getContentTemplate( 'file' ) /}}
			{{/if}}
		{{/props}}
	</div>
</script>
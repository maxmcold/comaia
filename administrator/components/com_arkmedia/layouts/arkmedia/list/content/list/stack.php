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

$css_grid		= array( Helper::css( 'grid.column.medium.12' ), Helper::css( 'box.table.responsive' ) );
$css_table		= array( Helper::css( 'box.table.container' ), Helper::css( 'box.table.striped' ), Helper::css( 'box.table.hover' ), Helper::css( 'box.table.condensed' ) );
?>
<script id="tmpl-content-<?php echo $view;?>-stack" type="text/x-jsrender">
	<div class="<?php echo implode( chr( 32 ), $css_grid ); ?>" data-label="<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_RESPONSIVE_LBL' ); ?>">
		<table class="<?php echo implode( chr( 32 ), $css_table ); ?>">
			<thead>
				<tr>
					<th class="<?php echo Helper::css( 'text.align.left' ); ?>">
						<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_FILENAME_TTL' ); ?>
					</th>
					<th width="3%">
					</th>
					<th class="<?php echo Helper::css( 'text.align.center' ); ?>" width="20%">
						<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_FILEDIMENSIONS_TTL' ); ?>
					</th>
					<th class="<?php echo Helper::css( 'text.align.center' ); ?>" width="20%">
						<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_LASTMODIFIED_TTL' ); ?>
					</th>
					<th class="<?php echo Helper::css( 'text.align.right' ); ?>" width="20%">
						<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_FILESIZE_TTL' ); ?>
					</th>
				</tr>
			</thead>
			<tbody>

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

			</tbody>
		</table>
	</div>
</script>
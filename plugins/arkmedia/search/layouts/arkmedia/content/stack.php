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

$base 		= $displayData->base;
$name 		= $displayData->name;

$css_grid	= array( Helper::css( 'grid.column.medium.12' ), Helper::css( 'box.table.responsive' ) );
$css_row	= array( Helper::css( 'grid.row' ) );
$css_table	= array( Helper::css( 'box.table.container' ), Helper::css( 'box.table.striped' ), Helper::css( 'box.table.hover' ), Helper::css( 'box.table.condensed' ) );
?>
<script id="tmpl-content-<?php echo $name;?>-stack" type="text/x-jsrender">
	{{!-- /* Add View Data to Help With Styling */ --}}
	<div data-view="list">
		<div class="<?php echo implode( chr( 32 ), $css_grid ); ?>" data-label="<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_RESPONSIVE_LBL' ); ?>">
			<div class="<?php echo implode( chr( 32 ), $css_row ); ?>">
				<table class="<?php echo implode( chr( 32 ), $css_table ); ?>">
					<thead>
						<tr>
							<th class="<?php echo Helper::css( 'text.align.left' ); ?>">
								<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_FILENAME_TTL' ); ?>
							</th>
						</tr>
					</thead>
					<tbody>

						{{!-- /* Render Back Button */ --}}
						{{include tmpl=~getTemplate( 'back' ) /}}

						{{!-- /* Get Folders */ --}}
						{^{for results.folders tmpl=~getTemplate( 'folder' ) /}}

						{{!-- /* Get Files */ --}}
						{^{for results.files tmpl=~getTemplate( 'file' ) /}}

					</tbody>
				</table>
			</div>
		</div>
	</div>
</script>
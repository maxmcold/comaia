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

$view 		= $displayData->view;
$options 	= $displayData->options;

$css_folder	= array( 'folder', Helper::css( 'ark.var.block' ) );
$css_badge	= array( Helper::css( 'box.badge' ), Helper::css( 'align.right' ) );
$data 		= array( 'data-link' => 'title{:prop.name} data-path{:key} data-select{:prop.name}', 'data-stack-control' => 'path' );
?>
<script id="tmpl-content-<?php echo $view;?>-folder" type="text/x-jsrender">
	<tr>
		<td colspan="4">
			<a href="javascript:void(0);" class="<?php echo implode( chr( 32 ), $css_folder ); ?>" <?php echo JArrayHelper::toString( $data ); ?>>
				<?php echo Helper::html( 'icon.icomoon', 'checkmark', array( 'attrs' => array( 'data-action' => 'check', 'data-skip' => 'true' ) ) ); ?>
				<?php echo Helper::html( 'icon.icomoon', 'folder' ); ?>
				{^{:prop.name}}
			</a>
		</td>
		<td>
			{^{if prop.loaded}}
				<span class="<?php echo implode( chr( 32 ), $css_badge ); ?>">{^{:prop.folders + prop.count}}</span>
			{{else}}
				{{!-- /* @todo	Add a "Load This Count" Icon Like: <span class="<?php echo implode( chr( 32 ), $css_badge ); ?>"><i class="icon-loop2"></i></span> */ --}}
			{{/if}}
		</td>
	</tr>
</script>
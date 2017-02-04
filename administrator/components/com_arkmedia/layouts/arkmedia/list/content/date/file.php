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

$css_root		= '#' . Helper::css( 'content' ) . chr( 32 ) . '[data-view="date"]';
$css_tooltip	= '.' . Helper::css( 'joomla.tooltip' );
$css_container	= '#' . Helper::css( 'root' );
$css_grid		= array( 'file', 'image', 'loading', Helper::css( 'align.left' ), Helper::css( 'ark.var.relative' ), Helper::css( 'joomla.tooltip' ) );

$attrs 			= array( 'alt{:item.name}', 'title{:item.name}', "src{:item.preview.thumbnail + '&size=80x80'}" );

// @note	There is a Bug in J3.2 < Bootstrap.Tooltip That Casts $params['container'] as an (int)
// 			Luckily at This Point the "data-container" is Picked Up Otherwise 0 Would be Passed.
// Initialise Tooltip as Delegate Event (so inserted elements gain tooltip ability)
JHTML::_( 'bootstrap.tooltip', $css_root, array( 'selector' => $css_tooltip, 'container' => $css_container ) );

?>
<script id="tmpl-content-<?php echo $view;?>-file" type="text/x-jsrender">
	<div 
		class="<?php echo implode( chr( 32 ), $css_grid ); ?>" 
		data-link="data-select{:item.name} data-edit{:item.name} title{:~fileTooltip(item.name,item.size)}"
		data-toggle="tooltip"
		data-container="<?php echo $css_container; ?>"
		data-placement="top">
			<?php echo Helper::html( 'icon.icomoon', 'checkmark', array( 'attrs' => array( 'data-action' => 'check', 'data-skip' => 'true' ) ) ); ?>
			<?php echo Helper::html( 'icon.icomoon', 'warning', array( 'attrs' => array( 'data-action' => 'error', 'data-skip' => 'true' ) ) ); ?>
			{{!-- /* Empty Attributes are Populated by Data Link */ --}}
			{{if item.preview.thumbnail}}
				<img 
					data-link="<?php echo implode( chr( 32 ), $attrs ); ?>"
					onload="jQuery( document ).trigger( 'thumbnail:request:load', { el : this } );"
					onerror="jQuery( document ).trigger( 'thumbnail:request:error', { el : this } );"
					alt="" 
					title="" />
			{{else}}
				<i data-link="class{:item.preview.icon}}"></i>
			{{/if}}
	</div>
</script>
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

// Render Folder Preview Template
echo Helper::layout( 'list.content.' . $view . '.folderpreview', (object)array( 'view' => $view, 'options' => $options ) );

$css_grid	= array( Helper::css( 'align.left' ) );
$css_item	= array( 'folder', Helper::css( 'box.well.container' ), Helper::css( 'box.well.small' ), Helper::css( 'ark.var.block' ), Helper::css( 'ark.var.overflow' ) );
$css_image	= array( 'image' );
$css_title	= array( 'title', Helper::css( 'ark.text.overflow' ), Helper::css( 'text.align.center' ), Helper::css( 'ark.var.relative' ) );
$css_icon	= array( Helper::css( 'ark.icon.caret.container' ), Helper::css( 'ark.icon.caret.right' ) );
$css_label	= array( Helper::css( 'text.muted' ), Helper::css( 'text.align.center' ), Helper::css( 'ark.text.upper' ) );
?>
<script id="tmpl-content-<?php echo $view;?>-folder" type="text/x-jsrender">
	<div class="<?php echo implode( chr( 32 ), $css_grid ); ?>">
		<div class="<?php echo implode( chr( 32 ), $css_item ); ?>" data-stack-control="path" data-link="data-path{:key} data-select{:prop.name}">

			{{!-- /* Image Container */ --}}
			<div class="<?php echo implode( chr( 32 ), $css_image ); ?>">
				<?php echo Helper::html( 'icon.icomoon', 'folder', array( 'attrs' => array( 'data-state' => 'closed' ) ) ); ?>
				<?php echo Helper::html( 'icon.icomoon', 'folder-open', array( 'attrs' => array( 'data-state' => 'open' ) ) ); ?>
				<?php if( $options->get( 'enable-thumbnails', true ) ) : ?>
					{{include tmpl=~getContentTemplate( 'preview' ) /}}
				<?php endif; ?>
			</div>

			{{!-- /* Text Container */ --}}
			<div class="<?php echo implode( chr( 32 ), $css_title ); ?>">
				{^{:prop.name}}
				<i class="<?php echo implode( chr( 32 ), $css_icon ); ?>"></i>
			</div>

			{{!-- /* Label */ --}}
			<div class="<?php echo implode( chr( 32 ), $css_label ); ?>">
				<small><?php echo JText::_( ARKMEDIA_JTEXT . 'FOLDER_FOLDER_LBL' ); ?></small>
			</div>
		</div>
	</div>
</script>
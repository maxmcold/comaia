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

$view		= $displayData->view;
$options 	= $displayData->options;

$css_grid	= array( Helper::css( 'align.left' ) );
$css_item	= array( 'file',  Helper::css( 'box.well.container' ), Helper::css( 'box.well.small' ), Helper::css( 'ark.var.overflow' ) );
$css_image	= array( 'image', 'loading', 'vertical-align-parent' );
$css_title	= array( 'title' );
$css_plain 	= array( Helper::css( 'ark.text.overflow' ), Helper::css( 'text.align.center' ), Helper::css( 'ark.var.relative' ) );
$css_off 	= array_merge( $css_plain, array( 'off' ) );
$css_on		= array( 'rename', 'on', Helper::css( 'ark.var.overflow' ) );
$css_on_grid= Helper::css( 'grid.gutter.clear.both' );
$css_on_1	= array( Helper::css( 'grid.column.medium.10' ), Helper::css( 'grid.column.small.10' ), Helper::css( 'grid.column.mini.10' ), $css_on_grid );
$css_on_2	= array( Helper::css( 'grid.column.medium.2' ), Helper::css( 'grid.column.small.2' ), Helper::css( 'grid.column.mini.2' ), $css_on_grid, Helper::css( 'text.align.right' ) );
$css_edit	= array( Helper::css( 'form.control' ), Helper::css( 'form.size.small' ) );
$css_label	= array( Helper::css( 'text.muted' ), Helper::css( 'text.align.center' ) );

$attrs 		= array( 'alt{:prop.name}', 'title{:prop.name}', "src{:prop.preview.thumbnail + '&size=100x100x7'}" );

// Render Actions Template
echo Helper::layout( 'list.content.' . $view . '.actions', (object)array( 'view' => $view, 'options' => $options ) );
?>
<script id="tmpl-content-<?php echo $view;?>-file" type="text/x-jsrender">
	<div class="<?php echo implode( chr( 32 ), $css_grid ); ?>">
		<div class="<?php echo implode( chr( 32 ), $css_item ); ?>" data-link="data-select{:prop.name} data-edit{:prop.name}">

			{{!-- /* Image Container */ --}}
			<div class="<?php echo implode( chr( 32 ), $css_image ); ?>">
				{{include tmpl=~getContentTemplate( 'actions' ) /}}

				{{!-- /* Empty Attributes are Populated by Data Link */ --}}
				{{if prop.preview.thumbnail}}
					<img 
						class="vertical-align-element" 
						data-link="<?php echo implode( chr( 32 ), $attrs ); ?>"
						onload="jQuery( document ).trigger( 'thumbnail:request:load', { el : this } );"
						onerror="jQuery( document ).trigger( 'thumbnail:request:error', { el : this } );"
						alt="" 
						title="" />
				{{else}}
					<i data-link="class{:prop.preview.icon}"></i>
				{{/if}}
				<?php echo Helper::html( 'icon.icomoon', 'warning', array( 'attrs' => array( 'data-action' => 'error', 'data-skip' => 'true' ) ) ); ?>
				<span class="vertical-align-helper"></span>
		</div>

			{{!-- /* Monitor File Name Edit State */ --}}
			{^{rename ~key=key ~prop=prop}}
				{{!-- /* Text Container */ --}}
				<div class="<?php echo implode( chr( 32 ), $css_title ); ?>">
					<?php if( Helper::actions( 'ark.action.rename' ) ) : ?>
						<div class="<?php echo implode( chr( 32 ), $css_off ); ?>" data-link="visible{:!rename.active}">
							{^{:~prop.name}}
							<?php echo Helper::html( 'icon.icomoon', 'pencil', array( 'attrs' => array( 'data-bubble' => 'off', 'data-action' => 'rename' ) ) ); ?>
						</div>
						<div class="<?php echo implode( chr( 32 ), $css_on ); ?>" data-link="visible{:rename.active}">
							<div class="<?php echo implode( chr( 32 ), $css_on_1 ); ?>">
								<input type="text" class="<?php echo implode( chr( 32 ), $css_edit ); ?>" name="filename" value="" data-link="~prop.name onBeforeChange=~rename" data-bubble="off" data-key="{{:~key}}" />
							</div>
							<div class="<?php echo implode( chr( 32 ), $css_on_2 ); ?>">
								{{!-- /* Note - Buttons Have Hidden data-key, data-new & data-old Attributes */ --}}
								<?php echo Helper::html( 'icon.icomoon', 'checkmark' . chr( 32 ) . Helper::css( 'text.success' ), array( 'attrs' => array( 'data-bubble' => 'off', 'data-action' => 'save' ) ) ); ?>
								<?php echo Helper::html( 'icon.icomoon', 'close' . chr( 32 ) . Helper::css( 'text.danger' ), array( 'attrs' => array( 'data-bubble' => 'off', 'data-action' => 'cancel' ) ) ); ?>
							</div>
						</div>
					<?php else : ?>
						<div class="<?php echo implode( chr( 32 ), $css_plain ); ?>">
							{^{:~prop.name}}
						</div>
					<?php endif; ?>
				</div>

				{{!-- /* File Size */ --}}
				<div class="<?php echo implode( chr( 32 ), $css_label ); ?>" data-link="visible{:!rename.active}">
					<small>
						{{if ~prop.size}}
							{^{:~prop.size.x}} x {^{:~prop.size.y}}
						{{else}}
							{^{:~bytes(~prop.filesize)}}
						{{/if}}
					</small>
				</div>
			{{/rename}}
		</div>
	</div>
</script>
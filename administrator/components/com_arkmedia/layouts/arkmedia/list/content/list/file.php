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

$css_item	= array( 'file', 'off', Helper::css( 'ark.var.block' ) );
$css_on		= array( 'rename', 'on', Helper::css( 'align.left' ) );
$css_group	= array( Helper::css( 'form.input.group' ) );
$css_edit	= array( Helper::css( 'form.control' ), Helper::css( 'form.size.small' ) );
$css_save	= array( 'checkmark', Helper::css( 'text.success' ), Helper::css( 'form.input.addon' ) );
$css_cancel	= array( 'close', Helper::css( 'text.danger' ), Helper::css( 'form.input.addon' ) );
$css_preload= array( Helper::css( 'align.left' ) );
$css_actions= array( Helper::css( 'text.align.center' ) );
$css_size	= array( Helper::css( 'text.muted' ), Helper::css( 'text.align.center' ) );
$css_date	= array( Helper::css( 'text.muted' ), Helper::css( 'text.align.center' ) );
$css_bytes	= array( Helper::css( 'text.muted' ), Helper::css( 'text.align.right' ) );

// Render Actions Template
echo Helper::layout( 'list.content.' . $view . '.actions', (object)array( 'view' => $view, 'options' => $options ) );

?>
<script id="tmpl-content-<?php echo $view;?>-file" type="text/x-jsrender">
	{{!-- /* Monitor File Name Edit State */ --}}
	{^{rename ~key=key ~prop=prop}}
		<tr>
			{{!-- /* File Name */ --}}
			<td>
				<a href="javascript:void(0);" class="<?php echo implode( chr( 32 ), $css_item ); ?>" data-link="data-select{:~prop.name} data-edit{:~prop.name} visible{:!rename.active}">
					<?php echo Helper::html( 'icon.icomoon', 'checkmark', array( 'attrs' => array( 'data-action' => 'check', 'data-skip' => 'true' ) ) ); ?>
					<i data-link="class{:~prop.preview.icon}"></i>
					{^{:~prop.name}}
				</a>
				<div class="<?php echo implode( chr( 32 ), $css_on ); ?>" data-link="visible{:rename.active}">
					<div class="<?php echo implode( chr( 32 ), $css_group ); ?>">
						{{!-- /* Note - Buttons Have Hidden data-key, data-new & data-old Attributes */ --}}
						<input type="text" class="<?php echo implode( chr( 32 ), $css_edit ); ?>" name="filename" value="" data-link="~prop.name onBeforeChange=~rename" data-key="{{:~key}}" />
						<?php echo Helper::html( 'icon.icomoon', implode( chr( 32 ), $css_save ), array( 'attrs' => array( 'data-action' => 'save') ) ); ?>
						<?php echo Helper::html( 'icon.icomoon', implode( chr( 32 ), $css_cancel ), array( 'attrs' => array( 'data-action' => 'cancel' ) ) ); ?>
					</div>
				</div>
			</td>

			{{!-- /* Actions */ --}}
			<td class="<?php echo implode( chr( 32 ), $css_actions ); ?>">
				{^{if !rename.active tmpl=~getContentTemplate( 'actions' ) /}}
			</td>

			{{!-- /* File Size */ --}}
			<td class="<?php echo implode( chr( 32 ), $css_size ); ?>">
				{{if ~prop.size}}
					{^{:~prop.size.x}} x {^{:~prop.size.y}}
				{{else}}
					<?php echo JText::_( ARKMEDIA_JTEXT . 'BLANK' ); ?>
				{{/if}}
			</td>

			<td class="<?php echo implode( chr( 32 ), $css_date ); ?>">
				{^{:~date(~prop.modified, '<?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_LIST_MODIFIEDJS_LBL' ); ?>')}}
			</td>

			<td class="<?php echo implode( chr( 32 ), $css_bytes ); ?>">
				{^{:~bytes(~prop.filesize)}}
			</td>
		</tr>
	{{/rename}}
</script>
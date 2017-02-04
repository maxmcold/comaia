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

$id 			= basename( __FILE__, '.php' );
$css_container	= array( Helper::css( 'box.panel.container' ) );
$css_inner		= array( Helper::css( 'box.panel.body' ), Helper::css( 'text.align.center' ) );
$css_loading	= array( 'loading', Helper::css( 'ark.var.block' ) );
$css_failed		= array( 'failed', Helper::css( 'ark.var.hide' ), Helper::css( 'joomla.tooltip' ) );
$css_preview	= array( 'preview', Helper::css( 'ark.var.hide' ) );
$css_unload		= array( 'failed' );

?>
<script id="tmpl-edit-<?php echo $id; ?>" type="text/x-jsrender">
	<div id="<?php echo Helper::css( 'edit.' . $id ); ?>">
		<div class="<?php echo implode( chr( 32 ), $css_container ); ?>">
			<div class="<?php echo implode( chr( 32 ), $css_inner ); ?>">
				{{!-- /* When Unloading Edit Mode the File Reference is Destroyed Leading to a Network Error if the Image is Allowed to Render */ --}}
				{^{if file.value}}
					<div class="<?php echo implode( chr( 32 ), $css_loading ); ?>">
						<?php echo Helper::html( 'icon.icomoon', 'spinner' ); ?>
					</div>
					<div class="<?php echo implode( chr( 32 ), $css_failed ); ?>" title="<?php echo JText::_( ARKMEDIA_JTEXT . 'EDIT_PREVIEW_NOIMAGE_TIP' ); ?>">
						<?php echo Helper::html( 'icon.icomoon', "{{:~icon( 'camera' )}}" ); ?>
					</div>
					<div class="<?php echo implode( chr( 32 ), $css_preview ); ?>">
						{{:~preview( file.value )}}
					</div>
				{{else}}
					{{!-- /* @see [#1227] */ --}}
					<div class="<?php echo implode( chr( 32 ), $css_unload ); ?>">
						<?php echo Helper::html( 'icon.icomoon', "{{:~icon( 'camera' )}}" ); ?>
					</div>				
				{{/if}}
			</div>
		</div>
	</div>
</script>
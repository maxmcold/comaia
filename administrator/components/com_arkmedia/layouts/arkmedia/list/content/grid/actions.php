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

$view				= $displayData->view;
$options 			= $displayData->options;
$actions 			= Helper::actions();
$anyactions			= ( $actions->get( 'ark.action.edit' ) || $actions->get( 'ark.action.copy' ) || $actions->get( 'ark.action.move' ) || $actions->get( 'ark.action.remove' ) );

// Don't Bother Rendering Stuff if There is No Permissions to Anything
if( $anyactions )
{
	$css_container	= array( 'actions' );
	$css_labels		= array( Helper::css( 'align.left' ) );
	$css_label		= array( Helper::css( 'tooltip' ), 'fade' );
	$css_actions	= array( Helper::css( 'button.group' ), Helper::css( 'button.align.vertical' ), Helper::css( 'align.right' ) );
	$css_root		= '.actions';
	$css_tip 		= '.' . Helper::css( 'tooltip' );

	// Bootstrap's Didn't Quite Work for us so Use Our Own Parent/Tooltip (restrict to content for better performance)
	// Mainly Because we Need to Insert the Tooltip Locally for Styling & the JS Template System Causes Duplication Issues Here.
	Helper::html( 'actions.tooltip', null, '#' . Helper::css( 'content' ) );

	$data 			= array( 'container' => $css_root, 'data-bubble' => 'off', 'data-link' => 'data-name{:prop.name}' );
	$attrs 			= array( 'colour' => 'plain', 'size' => 'mini', 'extra' => Helper::css( 'tooltip' ), 'data' => $data );
	$edit_attrs 	= array( 'title' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_EDIT_LBL' ), 'data' => array( 'data-action' => 'edit', 	'target' => $css_tip . "[data-value='edit']" ), 	'icon' => 'pencil' );
	$copy_attrs 	= array( 'title' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_COPY_LBL' ), 'data' => array( 'data-action' => 'copy', 	'target' => $css_tip . "[data-value='copy']" ), 	'icon' => 'copy' );
	$move_attrs 	= array( 'title' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_MOVE_LBL' ), 'data' => array( 'data-action' => 'move', 	'target' => $css_tip . "[data-value='move']" ), 	'icon' => 'scissors' );
	$kill_attrs 	= array( 'title' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_KILL_LBL' ), 'data' => array( 'data-action' => 'remove', 	'target' => $css_tip . "[data-value='remove']" ), 	'icon' => 'close' );
}//end if
?>
<script id="tmpl-content-<?php echo $view; ?>-actions" type="text/x-jsrender">
	<?php if( $anyactions ) : ?>
		<div class="<?php echo implode( chr( 32 ), $css_container ); ?>">
			<div class="<?php echo implode( chr( 32 ), $css_labels ); ?>">
				<?php if( $actions->get( 'ark.action.edit' ) ) : ?>
					<div class="<?php echo implode( chr( 32 ), $css_label ); ?>" data-value="edit"><?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_EDIT_LBL' ); ?></div>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.copy' ) ) : ?>
					<div class="<?php echo implode( chr( 32 ), $css_label ); ?>" data-value="copy"><?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_COPY_LBL' ); ?></div>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.move' ) ) : ?>
					<div class="<?php echo implode( chr( 32 ), $css_label ); ?>" data-value="move"><?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_MOVE_LBL' ); ?></div>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.remove' ) ) : ?>
					<div class="<?php echo implode( chr( 32 ), $css_label ); ?>" data-value="remove"><?php echo JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_KILL_LBL' ); ?></div>
				<?php endif; ?>
			</div>
			<div class="<?php echo implode( chr( 32 ), $css_actions ); ?>">
				<?php if( $actions->get( 'ark.action.edit' ) ) : ?>
					<?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $edit_attrs ) ); ?>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.copy' ) ) : ?>
					<?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $copy_attrs ) ); ?>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.move' ) ) : ?>
					<?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $move_attrs ) ); ?>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.remove' ) ) : ?>
					<?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $kill_attrs ) ); ?>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>
</script>
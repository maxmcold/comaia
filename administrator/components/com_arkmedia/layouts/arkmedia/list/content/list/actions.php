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
$actions 		= Helper::actions();
$anyactions		= ( $actions->get( 'ark.action.rename' ) || $actions->get( 'ark.action.edit' ) || $actions->get( 'ark.action.copy' ) || $actions->get( 'ark.action.move' ) || $actions->get( 'ark.action.remove' ) );

// Don't Bother Rendering Stuff if There is No Permissions to Anything
if( $anyactions )
{
	$css_actions= array( 'actions', Helper::css( 'button.group' ) );
	$css_btn	= array( Helper::css( 'button.button' ), Helper::css( 'button.type.default' ), Helper::css( 'button.size.mini' ), Helper::css( 'button.dropdown.toggle' ) );
	$css_menu	= array( Helper::css( 'button.dropdown.menu' ) );

	$data 		= array( 'data-link' => 'data-name{:~prop.name}' );
	$attrs 		= array( 'class' => '', 'data' => $data );
	$name_attrs = array( 'text' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_RENAME_LBL' ), 	'data' => array( 'data-action' => 'rename' ), 	'icon' => 'font', 'prefixes' => array( 'icon' => 'glyphicon glyphicon-' ) );
	$edit_attrs = array( 'text' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_EDIT_LBL' ), 	'data' => array( 'data-action' => 'edit' ), 	'icon' => 'pencil' );
	$copy_attrs = array( 'text' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_COPY_LBL' ), 	'data' => array( 'data-action' => 'copy' ), 	'icon' => 'copy' );
	$move_attrs = array( 'text' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_MOVE_LBL' ), 	'data' => array( 'data-action' => 'move' ), 	'icon' => 'scissors' );
	$kill_attrs = array( 'text' => JText::_( ARKMEDIA_JTEXT . 'FILE_GRID_KILL_LBL' ), 	'data' => array( 'data-action' => 'remove' ), 	'icon' => 'close' );
}
?>
<script id="tmpl-content-<?php echo $view;?>-actions" type="text/x-jsrender">
	<?php if( $anyactions ) : ?>
		<div class="<?php echo implode( chr( 32 ), $css_actions ); ?>">
			<button type="button" class="<?php echo implode( chr( 32 ), $css_btn ); ?>" data-toggle="dropdown">
				<?php echo Helper::html( 'icon.icomoon', 'caret', array( 'prefix' => '' ) ); ?>
			</button>
			<ul class="<?php echo implode( chr( 32 ), $css_menu ); ?>" role="menu">
				<?php if( $actions->get( 'ark.action.rename' ) ) : ?>
					<li><?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $name_attrs ) );?></li>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.edit' ) ) : ?>
					<li><?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $edit_attrs ) );?></li>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.copy' ) ) : ?>
					<li><?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $copy_attrs ) );?></li>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.move' ) ) : ?>
					<li><?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $move_attrs ) );?></li>
				<?php endif; ?>
				<?php if( $actions->get( 'ark.action.remove' ) ) : ?>
					<li><?php echo Helper::html( 'button.a', array_merge_recursive( $attrs, $kill_attrs ) );?></li>
				<?php endif; ?>
			</ul>
		</div>
	<?php endif; ?>
</script>
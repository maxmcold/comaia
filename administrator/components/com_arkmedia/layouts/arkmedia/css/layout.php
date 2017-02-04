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

// Build Layout Classes (Some Elements Have Multiple Types to Allow for Resizing the Layout)
$displayData->css 						= new stdClass;
$displayData->css->root 				= array( Helper::browser( 'css' ), Helper::template( 'css' ) );
$displayData->css->row 					= array( Helper::css( 'grid.row' ) );
$displayData->css->container 			= array( Helper::css( 'grid.container.fluid' ) );
$displayData->css->top					= $displayData->css->row;
$displayData->css->bottom				= $displayData->css->row;
$displayData->css->toolbar				= array( Helper::css( 'grid.column.medium.10' ), Helper::css( 'grid.column.medium.offset.2' ) );
$displayData->css->left					= new stdClass;
$displayData->css->left->right			= array( 'col-left', Helper::css( 'grid.column.medium.1' ), Helper::css( 'grid.column.small.1' ), Helper::css( 'grid.column.custom' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->left->mixed			= array( 'col-left', Helper::css( 'grid.column.medium.2' ), Helper::css( 'grid.column.small.2' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->left->middle			= array( 'col-left', Helper::css( 'grid.column.medium.4' ), Helper::css( 'grid.column.small.4' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->right				= new stdClass;
$displayData->css->right->right			= array( 'col-right', Helper::css( 'grid.column.medium.11' ), Helper::css( 'grid.column.small.11' ), Helper::css( 'grid.column.custom' ), Helper::css( 'grid.gutter.clear.left' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->right->mixed			= array( 'col-right', Helper::css( 'grid.column.medium.10' ), Helper::css( 'grid.column.small.10' ), Helper::css( 'grid.gutter.clear.left' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->right->middle		= array( 'col-right', Helper::css( 'grid.column.medium.8' ), Helper::css( 'grid.column.small.8' ), Helper::css( 'grid.gutter.clear.left' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->navigation			= new stdClass;
$displayData->css->navigation->right	= array( 'col-nav', Helper::css( 'grid.column.medium.12' ), Helper::css( 'grid.column.small.12' ), Helper::css( 'grid.gutter.clear.both' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->navigation->mixed	= array( 'col-nav', Helper::css( 'grid.column.medium.2' ), Helper::css( 'grid.column.small.12' ), Helper::css( 'grid.gutter.clear.both' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->navigation->middle	= array( 'col-nav', Helper::css( 'grid.column.medium.1' ), Helper::css( 'grid.column.small.12' ), Helper::css( 'grid.gutter.clear.both' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->explorer				= new stdClass;
$displayData->css->explorer->right		= array( 'col-explorer', Helper::css( 'grid.column.medium.0' ), Helper::css( 'responsive.hidden.mini' ), Helper::css( 'grid.gutter.clear.both' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->explorer->mixed		= array( 'col-explorer', Helper::css( 'grid.column.medium.10' ), Helper::css( 'grid.column.small.12' ), Helper::css( 'responsive.hidden.mini' ), Helper::css( 'grid.gutter.clear.both' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->explorer->middle		= array( 'col-explorer', Helper::css( 'grid.column.medium.11' ), Helper::css( 'grid.column.small.12' ), Helper::css( 'responsive.hidden.mini' ), Helper::css( 'grid.gutter.clear.both' ), Helper::css( 'ark.var.transition' ) );
$displayData->css->bubble				= new stdClass;
$displayData->css->bubble->right		= array( 'icon-play3', 'bubble', Helper::css( 'responsive.hidden.large' ), Helper::css( 'responsive.hidden.medium' ), Helper::css( 'responsive.hidden.small' ), Helper::css( 'responsive.hidden.mini' ) );
$displayData->css->bubble->mixed		= array( 'icon-play3', 'bubble', Helper::css( 'responsive.hidden.small' ), Helper::css( 'responsive.hidden.mini' ) );
$displayData->css->bubble->middle		= array( 'icon-play3', 'bubble', Helper::css( 'responsive.hidden.small' ), Helper::css( 'responsive.hidden.mini' ) );

$displayData->css->content				= new stdClass;
$displayData->css->content->containerid	= array( Helper::css( 'containers.content.container' ) );
$displayData->css->content->container	= array( Helper::css( 'box.well.container' ), Helper::css( 'ark.var.relative' ) );
$displayData->css->content->row			= $displayData->css->row;
$displayData->css->content->topid		= array( Helper::css( 'containers.content.top' ) );
$displayData->css->content->top			= array( Helper::css( 'box.panel.container' ), Helper::css( 'box.panel.small' ) );
$displayData->css->content->topbody		= array( Helper::css( 'box.panel.body' ) );
$displayData->css->content->breadcrumb 	= array( Helper::css( 'grid.column.medium.9' ), Helper::css( 'grid.column.small.7' ), Helper::css( 'responsive.hidden.mini' ) );
$displayData->css->content->helpbar 	= array( Helper::css( 'grid.column.medium.3' ), Helper::css( 'grid.column.small.5' ), Helper::css( 'grid.column.mini.12' ) );
$displayData->css->content->bottomid	= array( Helper::css( 'containers.content.bottom' ) );
$displayData->css->content->bottom 		= array( Helper::css( 'box.panel.container' ) );
$displayData->css->content->bottombody	= array( Helper::css( 'box.panel.body' ) );

$displayData->css->edit					= new stdClass;
$displayData->css->edit->container		= array( Helper::css( 'ark.var.hide' ) );
$displayData->css->edit->row			= $displayData->css->row;
$displayData->css->edit->actions		= array( Helper::css( 'grid.column.small.12' ) );
$displayData->css->edit->preview		= array( Helper::css( 'grid.column.large.9' ), Helper::css( 'grid.column.medium.8' ), Helper::css( 'grid.column.small.12' ) );
$displayData->css->edit->settings		= array( Helper::css( 'grid.column.large.3' ), Helper::css( 'grid.column.medium.4' ), Helper::css( 'grid.column.small.12' ) );
$displayData->css->edit->subactions		= array( Helper::css( 'grid.column.small.12' ), Helper::css( 'text.align.right' ) );

// Collapse Classes
foreach( $displayData->css as $key => $val )
{
	$displayData->css->{ $key } 		= Helper::html( 'text.implode', $val );
}//end foreach

// Build Resize Data Attributes Whilst We're Here
$displayData->data 						= new stdClass;
$resize_types							= array( 'right', 'mixed', 'middle' );
$resize_elements 						= array( 'left', 'right', 'navigation', 'explorer', 'bubble' );

// Assign the Element's CSS to Each Respective Resize Type
foreach( $resize_elements as $item )
{
	// Set Basic Data Attribute
	$displayData->data->{ $item } 								= array();
	$displayData->data->{ $item }['data-action-value']			= 'resize';

	// Add Resize Data Attributes
	foreach( $resize_types as $type )
	{
		$displayData->data->{ $item }['data-resize-' . $type] 	= $displayData->css->{ $item }->{ $type };
	}//end foreach

	// Collapse Attributes to String
	$displayData->data->{ $item } 								= JArrayHelper::toString( $displayData->data->{ $item } );
}//end foreach
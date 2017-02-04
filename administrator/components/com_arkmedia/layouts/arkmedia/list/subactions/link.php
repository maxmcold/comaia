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

// @required ark-copylink.min.js
$doc					= JFactory::getDocument();
$element				= $displayData->element;

$css_root				= '#' . Helper::css( 'actions' );
$params					= array();
$params['id']			= $displayData->id;
$params['title']		= chr( 32 );
$params['bootstrap']	= 'tooltip';
$params['extra']		= Helper::css( 'tooltip' );
$params['data']			= array( 'container' => '#' . $params['id'], 'trigger' => 'manual' );
$params['text']			= ( $element['text'] ) 			? JText::_( (string)$element['text'] )		: '';
$params['icon']			= ( $element['icon'] ) 			? (string)$element['icon'] 					: '';
$params['colour']		= ( $element['colour'] ) 		? (string)$element['colour'] 				: '';

$tooltip				= array();
$tooltip['html']		= true;
$tooltip['selector']	= $params['data']['container'];
$tooltip['container']	= $params['data']['container'];
$tooltip['trigger']		= $params['data']['trigger']; // Manually trigger tooltip so we can control click event order

JHTML::_( 'bootstrap.tooltip', $css_root, $tooltip );

// Render Button
echo Helper::html( 'button.a', $params );

$js = 'jQuery( document ).ready( function( $ )
	{
		jQuery.fn.copylink(
		{
			css		: {
						control			: "#' . $displayData->formControl . '_copylink",
						path			: { selector : "#action-copy-link", value : "action-copy-link" }
					  },
			data	: {
						title			: "data-original-title"
					  },
			html 	: {
						messages 		: {
											success : "' . JText::_( ARKMEDIA_JTEXT . 'MSG_COPYLINK_SUCCESS', true ) . '",
										  },
						errors			: {
											fail : "' . JText::_( ARKMEDIA_JTEXT . 'MSG_COPYLINK_FAIL', true ) . '"
										  }
					  }
		});
	});';

$doc->addScriptDeclaration( $js );
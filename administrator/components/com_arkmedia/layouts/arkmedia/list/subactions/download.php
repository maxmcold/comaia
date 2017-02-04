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

// @required ark-filedownload.min.js
$doc					= JFactory::getDocument();
$element				= $displayData->element;

$css_root				= '#' . Helper::css( 'actions' );
$params					= array();
$params['id']			= $displayData->id;
$params['text']			= ( $element['text'] ) 			? JText::_( (string)$element['text'] )		: '';
$params['icon']			= ( $element['icon'] ) 			? (string)$element['icon'] 					: '';
$params['colour']		= ( $element['colour'] ) 		? (string)$element['colour'] 				: '';

// @note 	The download attr is a HTML5 Feature that Triggers the Download Window.
// 			It Currently Has Poor Support So Unsupported Browsers Will Fall Back to the
// 			Target Attribute & Just Open the File.
// 			Alternatively We Could Detect in JS:
// 			if( 'download' in document.createElement( 'a' ) )
$params['download']		= '';
$params['target']		= '_blank';

// Render Button
echo Helper::html( 'button.a', $params );

$js = 'jQuery( document ).ready( function( $ )
	{
		jQuery.fn.filedownload(
		{
			css		: {
						control			: "#' . $displayData->formControl . '_filedownload"
					  },
			data	: {
						link			: "href",
						name			: "download",
						name			: "download"
					  }
		});
	});';

$doc->addScriptDeclaration( $js );
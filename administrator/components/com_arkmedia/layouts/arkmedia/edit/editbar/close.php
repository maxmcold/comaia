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

// Get Container Data
$doc					= JFactory::getDocument();
$element				= $displayData->element;
$id						= 'html-' . basename( __DIR__ ) . '-' . basename( __FILE__, '.php' );

// Get Element Data
$options				= array();
$options['id']			= $id;

// Set Element Data
foreach( $element->attributes() as $key => $val )
{
	switch( $key )
	{
		case 'icon' : 
		case 'iconextra' : 
		case 'colour' : 
		case 'extra' : 
		case 'disabled' : 
		case 'active' : 
			$options[$key] = (string)$val;
			break;

		case 'text' : 
		case 'title' : 
			$options[$key] = JText::_( (string)$val );
			break;
	}//end switch
}//end foreach

// Set Option Data
foreach( $element->children() as $option )
{
	$key 	= (string)$option['key'];
	$subkey = (string)$option['subkey'];
	$value 	= (string)$option['value'];

	// Setup Sub Array if Not Yet Done
	if( !isset( $options[$key] ) )
	{
		$options[$key] = array();
	}//end if

	// Add Helpful Tooltip
	if( $subkey === 'toggle' && $value === 'tooltip' )
	{
		$css_root			= '#' . Helper::css( 'containers.content.edit' );
		$css_tooltip		= '#' . $id . '.' . Helper::css( 'joomla.tooltip' );
		$css_container		= '#' . Helper::css( 'root' );
		$options['extra'] = $options['extra'] . chr( 32 ) . Helper::css( 'joomla.tooltip' );

		// Initialise Tooltip
		JHTML::_( 'bootstrap.tooltip', $css_root, array( 'selector' => $css_tooltip, 'container' => $css_container ) );
	}//end if

	// Set Entry
	$options[$key][$subkey] = $value;
}//end foreach

// Initialise JS Logic
$js = <<<JS
	jQuery( document ).ready( function( $ )
	{
		// Link Button
		$( document ).on( 'click.edit', '#$id', function( ev )
		{
			$( document ).triggerHandler( 'edit:request:exit' );
		});
	});
JS;

// Add JS
$doc->addScriptDeclaration( $js );

// Render Button
echo Helper::html( 'button.a', $options );
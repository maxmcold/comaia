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

// @required ark-upload.min.js
$app		= JFactory::getApplication();
$element	= $displayData->element;
$parents	= $element->xpath( 'parent::*' );
$parent		= ( $parents && count( $parents ) ) ? current( $parents ) : array();

$formid		= (string)$parent['id'];
$toggle 	= ( $element['togglename'] ) 	? (string)$element['togglename'] 	: 'toggle';
$name 		= ( $element['fieldname'] ) 	? (string)$element['fieldname'] 	: 'file';
$stack 		= ( $element['stackname'] ) 	? (string)$element['stackname'] 	: 'stack';
$path 		= ( $element['pathname'] ) 		? (string)$element['pathname'] 		: 'path';

$text		= ( $element['text'] ) 			? (string)$element['text'] 			: '';
$icon		= ( $element['icon'] ) 			? (string)$element['icon'] 			: '';
$colour		= ( $element['colour'] ) 		? (string)$element['colour'] 		: '';

// Render Button
echo Helper::html( 'button.a', array( 'text' => JText::_( $text ), 'id' => $displayData->id, 'icon' => $icon, 'colour' => $colour ) );
?>
<script>
	jQuery( document ).ready( function( $ )
	{
		jQuery.fn.upload(
		{
			css		: {
						form		: '#<?php echo $formid; ?>',
						toggle		: '#<?php echo $displayData->formControl . '_' . $toggle; ?>',
						field		: '#<?php echo $displayData->formControl . '_' . $name; ?>',
						stack		: '#<?php echo $displayData->formControl . '_' . $stack; ?>',
						path		: '#<?php echo $displayData->formControl . '_' . $path; ?>',
						control		: '#<?php echo $displayData->id; ?>',
					  },
			html 	: {
						errors		: {
										path 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_FILENOPATHUPLOAD_FAIL', true ); ?>',
										field 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_FILENOFILEUPLOAD_FAIL', true ); ?>'
									  }
					  }
		});
	});
</script>
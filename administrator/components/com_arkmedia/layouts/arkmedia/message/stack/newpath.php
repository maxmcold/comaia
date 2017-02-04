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

$stack 						= $displayData->stack;
$view 						= $displayData->view;
$buttons 					= (array)$displayData->buttons;
$file						= basename( __FILE__, '.php' );

// Create New Folder Button
$form 		= $view->model->getForm( 'hidden' );
$rootbtn 	= ( $form instanceof JForm ) ? $form->getField( 'plant' ) : false;
$create 	= JText::_( ARKMEDIA_JTEXT . 'XML_HIDDEN_PLANT_LBL' );

if( $rootbtn )
{
	// Store Original Href
	$href	= $form->getFieldAttribute( 'plant', 'href' );

	// Make Sure ID is Unique
	$rootbtn->__set( 'id', $rootbtn->__get( 'element' ) . '_' . $stack );

	// Sprintf the Stack Name & Render Button
	$create = sprintf( $rootbtn->input, $stack );

	// Reset the Href Back to Prevent Repeat Buttons to Have Recursive Attributes Set
	$form->setFieldAttribute( 'plant', 'href', $href );
}//end if

$message					= new stdClass;
$message->title 			= JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_' . strtoupper( $file ) . '_TTL' );
$message->message['text'] 	= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_CONTENT_' . strtoupper( $file ) . '_DESC', '{{:path}}' );
$message->message['sub'] 	= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_CONTENT_' . strtoupper( $file ) . '_SUB', $create );
$message->icon 				= 'folder-open';

// Set Global Buttons
if( $buttons )
{
	$message->button = $buttons;
}//end if
?>
<script id="tmpl-content-<?php echo $stack; ?>-message-<?php echo $file; ?>" type="text/x-jsrender">
	<?php echo Helper::layout( 'message.generic', (object)array( 'messages' => array( $message ) ) ); ?>
</script>
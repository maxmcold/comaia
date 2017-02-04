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

// Get Path to Stack's Sub Messages & Get List of Available Messages
$path					= dirname( __FILE__ ) . ARKMEDIA_DS . basename( __FILE__, '.php' );
$messages 				= JFolder::files( $path, '.php' );
$displayData->buttons 	= array();

// Add Config Button
if( Helper::actions( 'core.admin' ) )
{
	$displayData->buttons = array(
			(object)array( 
			'text' => JText::_( ARKMEDIA_JTEXT . 'BTN_CONFIGURATION' ), 
			'href' => JRoute::_( 'index.php?option=com_config&view=component&component=' . ARKMEDIA_COMPONENT, false ),
			'icon' => 'cog'
		)
	);
}//end if

// Render Messages (strip file extension from name)
foreach( $messages as $message )
{
	echo Helper::layout( 'message.stack.' . basename( $message, '.php' ), $displayData );
}//end foreach
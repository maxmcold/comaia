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

// DON'T ALLOW DIRECT ACCESS TO INITIATOR
define( '_ARKMEDIA_EXEC', true );

// INITIATE COMPONENT ASSETS
require_once( 'initiate.php' );

// LOG IN CHECK
if( JFactory::getUser()->guest )
{
	throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTLOGGEDIN_FAIL' ) );
}//end if

// ACCESS CHECK
if( !Helper::actions( 'core.manage' ) ) 
{
	throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISED_FAIL' ) );
}//end if

// MEDIA LIBRARY CHECK
if( !JFile::exists( ARKMEDIA_MEDIA . 'index.html' ) ) 
{
	throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_INSTALL_MEDIA_FAIL' ) );
}//end if

// Include Dependancies
jimport( 'joomla.application.component.controller' );

$controller	= JControllerLegacy::getInstance( ARKMEDIA_COMPONENT_ID, array( 'base_path' => JPATH_COMPONENT_ADMINISTRATOR ) );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
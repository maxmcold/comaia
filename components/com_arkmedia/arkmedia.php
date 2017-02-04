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

// Get Component Name
$component = JFactory::getApplication()->input->get( 'option', false, 'cmd' );
$extension = str_replace( 'com_', '', $component );

if( $component )
{
	// Load Admin Language
	JFactory::getLanguage()->load( $component, JPATH_ADMINISTRATOR, null, false, true );

	// Load Admin Toolbar Helper (for view->addToolbar())
	require_once JPATH_ROOT  . chr( 47 ) . 'administrator' . chr( 47 ) . 'includes' . chr( 47 ) . 'toolbar.php';

	// Load JForm Paths (for model->getForm())
	JForm::addFormPath( JPATH_COMPONENT_ADMINISTRATOR . chr( 47 ) . 'models' . chr( 47 ) . 'forms' );
	JForm::addFieldPath( JPATH_COMPONENT_ADMINISTRATOR . chr( 47 ) . 'models' . chr( 47 ) . 'fields' );

	// Run Back-End Component
	require_once( JPATH_COMPONENT_ADMINISTRATOR . chr( 47 ) . $extension . '.php' );

	// Add Front End JS
	Helper::html( 'ark.js', 'ark-frontend.min' );
}//end if
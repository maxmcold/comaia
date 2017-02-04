<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'message' );

/**
 * This Field Renders a Message to the User if there are no Stack Plugins Enabled.
 */
class ArkFormFieldStackStatus extends ArkFormFieldMessage
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'stackstatus';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// If There is at Least One Ark Media Plugin Installed & Enabled Then No Need to Render Message
		return ( !JPluginHelper::isEnabled( ARKMEDIA_PLUGIN_STACK ) ) ? parent::getInput() : '';
	}//end function
}//end class
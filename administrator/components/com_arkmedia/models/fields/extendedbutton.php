<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper;

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'button' );

/**
 * This Field Renders a Normal Bootstrap Button but Adds Additional Joomla Functionality.
 *
 * Extra:
 * 1. Append the Current URL as a Redirect Parameter to the Button's Href Attr.
 * 2. Allow Buttons to be Disabled in the Front-End/Back-End.
 * 3. Allow Buttons to be Disabled Based on a User Action/Permission.
 * 4. Append a GET Session Token to the Button's Href Attr.
 * 5. Allow for Switching Tooltip Container "root" for the Ark's Root ID Selector.
 */
class ArkFormFieldExtendedButton extends ArkFormFieldButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'extendedbutton';

	/**
	 * Joomla Application Object.
	 *
	 * @var    object
	 */
	protected $app;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   11.1
	 */
	public function setup( SimpleXMLElement $element, $value, $group = null )
	{
		$this->app 	= JFactory::getApplication();
		$only 		= ( isset( $element['only'] ) ) 	? (string)$element['only'] 		: false;
		$action 	= ( isset( $element['action'] ) ) 	? (string)$element['action'] 	: false;

		// 2. BE / FE Only Button?
		if( ( $only && $only === 'site' && $this->app->isAdmin() ) || ( $only && $only === 'admin' && !$this->app->isAdmin() ) )
		{
			return false;
		}//end if

		// 3. Access Enabled Button?
		if( $action && !Helper::actions( $action ) )
		{
			return false;
		}//end if

		// Continue as Normal
		return parent::setup( $element, $value, $group );
	}//end function

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// Add Redirect Data
		$this->addRedirect();

		// Add Token Data
		$this->addToken();

		return parent::getInput();
	}//end function

	/**
	 * Method to Add Redirect Data to the Element.
	 *
	 * @return	void
	 */
	protected function addRedirect()
	{
		// 1. If a URL Append a Redirect URL to the End
		if( $this->element['redirect'] && $this->element['href'] )
		{
			$root 					= JURI::root();
			$subfolder 				= JURI::root( true ) . chr( 47 );
			$redirect 				= $this->app->input->server->get( 'REQUEST_URI', false, 'string' );

			// Strip SubFolder/Leading Slash
			if( substr( $redirect, 0, strlen( $subfolder ) ) == $subfolder )
			{
				$redirect 			= substr( $redirect, strlen( $subfolder ) );
			}//end if

			$key					= ( isset( $this->element['key'] ) ) ? (string)$this->element['key'] : 'return';
			$this->element['href'] .= '&' . $key . '=' . base64_encode( $root . $redirect );
		}//end if
	}//end function

	/**
	 * Method to Add Token Data to the Element.
	 *
	 * @return	void
	 */
	protected function addToken()
	{
		// 4. If a URL Append a Session Token to the End
		if( $this->element['token'] && $this->element['href'] )
		{
			$this->element['href'] .= '&' . JSession::getFormToken() . '=1';
		}//end if
	}//end function

	/**
	 * Method to Set the Tooltip Defaults if Basic Tooltip is Found
	 *
	 * @param	object  $options	Display Options
	 *
	 * @return	void
	 */
	protected function setTooltip( $options )
	{
		// Is Tooltip Enabled?
		if( isset( $options->data ) && isset( $options->data['toggle'] ) && $options->data['toggle'] == 'tooltip' )
		{
			// 5. Convert the Data Container From "root" to the Root Ark ID
			if( isset( $options->data['container'] ) && $options->data['container'] == 'root' )
			{
				$options->data['container'] = '#' . Helper::css();
			}//end if
		}//end if

		// Continue With Main Tooltip Logic
		return parent::setTooltip( $options );
	}//end function
}//end class
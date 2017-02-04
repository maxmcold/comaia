<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

/**
 * This Field Renders a Session Token into the Form.
 */
class ArkFormFieldToken extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'token';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		return JHTML::_( 'form.token' );
	}//end function

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		return '';
	}//end function
}//end class
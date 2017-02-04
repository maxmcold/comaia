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

/**
 * This Field Renders a Joomla Layout Based on the Dot Separated Path Passed in the XML.
 * e.g. <field type="layout" layout="list.subactions.actionname" name="fieldname" class="data-for-layout" />
 */
class ArkFormFieldLayout extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'layout';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		if( isset( $this->element['layout'] ) )
		{
			$options = array();

			// Set Layout Path Relative to the XML File
			if( isset( $this->element['path'] ) )
			{
				$options = array( 'base' => ARKMEDIA_ROOT . ARKMEDIA_DS . (string)$this->element['path'], 'client' => 'auto' );
			}//end if

			// Render Layout (don't pass $this as plain as reference is lost in translation?!)
			return Helper::layout( $this->element['layout'], (object)get_object_vars( $this ), $options );
		}//end if
	}//end function
}//end class
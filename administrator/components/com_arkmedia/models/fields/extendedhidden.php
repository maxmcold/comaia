<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'hidden' );

/**
 * This Field Adds Missing Functionality that Needs to be Present in the Joomla's Hidden Field, such as Data Attributes.
 * We Use Duck Punching to Avoid Overriding/Duplicating the Original Functions.
 */
class ArkFormFieldExtendedHidden extends JFormFieldHidden
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'extendedhidden';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$data = array();

		// Get the Input's Data Attributes
		foreach( $this->element->attributes() as $key => $val )
		{
			// If a Data Attribute (at start of string) Add to Input
			if( strpos( $key, 'data-' ) === 0 )
			{
				$data[$key] = (string)$val;
			}//end if
		}//end foreach

		return str_replace( ' />', chr( 32 ) . JArrayHelper::toString( $data ) . ' />', parent::getInput() );
	}//end function
}//end class
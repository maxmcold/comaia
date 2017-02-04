<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'list' );

/**
 * This Field Renders a Normal List but Adds Additional Functionality.
 *
 * Extra:
 * 1. Support Data Attributes.
 * 2. Make up for the Fact that you can't select multiple options to by default.
 */
class ArkFormFieldExtendedList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'extendedlist';

	/**
	 * Custom Param to Monitor Whether the Field is Multiple or Not
	 *
	 * @var		bool
	 */
	protected $isMultiple;

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
	 */
	public function setup( SimpleXMLElement $element, $value, $group = null )
	{
		$this->isMultiple = (string)$element['multiple'] === 'true';

		// 2. If a Multiple Field an the Value/Default is a CSV then Split it up so the JHTML::select Can Handle it
		if( $this->isMultiple && is_string( $value ) && strpos( $value, ',' ) !== false )
		{
			$value = explode( ',', $value );
		}//end if

		return parent::setup( $element, $value, $group );
	}//end function

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

		// 1. Get the Input's Data Attributes
		foreach( $this->element->attributes() as $key => $val )
		{
			// If a Data Attribute (at start of string) Add to Input
			if( strpos( $key, 'data-' ) === 0 )
			{
				// Translate the Attributes Before Setting? (opt in)
				$data[$key] = ( $this->translateAttributes === true && strpos( (string)$val, ARKMEDIA_JTEXT ) !== false ) ? JText::_( (string)$val ) : (string)$val;
			}//end if
		}//end foreach

		// Attributes to Add?
		if( count( $data ) )
		{
			return str_replace( '<select ', '<select ' . JArrayHelper::toString( $data ) . chr( 32 ), parent::getInput() );
		}
		else
		{
			return parent::getInput();
		}//end if
	}//end function
}//end class
<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'extendedhidden' );

/**
 * This Field Allows for Multiple Hidden Input Values by Rendering Multiple Hidden Inputs With the Same Name.
 */
class ArkFormFieldMultipleHidden extends ArkFormFieldExtendedHidden
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'multiplehidden';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var    boolean
	 * 
	 * @since  11.1
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get a control group with label and input.
	 *
	 * @param   array  $options  Options to be passed into the rendering of the field
	 *
	 * @return  string  A string containing the html for the control group
	 *
	 * @since   3.2
	 */
	public function renderField($options = array())
	{
		// Prevent Label Render
		$options['hiddenLabel'] = true;

		// Continue Render as Usual
		return parent::renderField( $options );
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
		$html = array();

		// Set Each Option/Value as a Separate Input
		foreach( $this->element->children() as $option )
		{
			if( $option['value'] )
			{
				$this->setValue( (string)$option['value'] );

				$html[] = parent::getInput();
			}//end if
		}//end foreach

		return implode( "\n", $html );
	}//end function
}//end class
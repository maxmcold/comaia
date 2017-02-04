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
 * This Field Renders a Normal Bootstrap Button But Sets Defaults
 * to Allow Styling Button as a UI Resizer Button.
 */
class ArkFormFieldResizeButton extends ArkFormFieldButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'resizebutton';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// Unless Something is Set Make Button Plain
		if( !isset( $this->element['colour'] ) )
		{
			$this->element['colour'] = 'plain';
		}//end if

		// Add Toggle Attribute if Not Already Present
		if( !count( $this->element->xpath( 'option[@key="data" and @subkey="action"]' ) ) )
		{
			$opt_toggle = $this->element->addChild( 'option' );
			$opt_toggle->addAttribute( 'key', 'data' );
			$opt_toggle->addAttribute( 'subkey', 'action' );
			$opt_toggle->addAttribute( 'value', 'resize' );
		}//end if

		// Add Type Attribute if Not Already Present (use field name before it is changed to the group)
		if( !count( $this->element->xpath( 'option[@key="data" and @subkey="type"]' ) ) )
		{
			$opt_type = $this->element->addChild( 'option' );
			$opt_type->addAttribute( 'key', 'data' );
			$opt_type->addAttribute( 'subkey', 'type' );
			$opt_type->addAttribute( 'value', $this->fieldname );
		}//end if

		// Unless Something is Set Make Text Ready for Resize HTML
		if( !isset( $this->element['text'] ) )
		{
			$html 					= array();
			$html[] 				= '<div class="one ' . Helper::css( 'align.left' ) . '"></div>';
			$html[] 				= '<div class="two ' . Helper::css( 'align.left' ) . '"></div>';
			$this->element['html'] 	= true;
			$this->element['text'] 	= implode( "\n", $html ); // Add Columns
		}//end if

		return parent::getInput();
	}//end function
}//end class
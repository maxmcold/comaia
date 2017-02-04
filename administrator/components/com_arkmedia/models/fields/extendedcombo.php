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

JFormHelper::loadFieldClass( 'combo' );

/**
 * This Field Renders a Normal Combo Field But Provides Extra Features.
 *
 * Extra:
 * 1. Fix Class Attribute: https://github.com/joomla/joomla-cms/pull/5451 (fixed J3.4 +)
 * 2. Support a Placeholder/Hint.
 * 3. Support Data Attributes.
 * 4. Support Config Set Options. options="config:parameter-name".
 */
class ArkFormFieldExtendedCombo extends JFormFieldCombo
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'extendedcombo';

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
		$html = parent::getInput();
		$hint = $this->translateHint ? JText::_( $this->hint ) : $this->hint;

		// 1. Fix J3.2 & J3.3 Syntax Error
		$html = str_replace( 'class=combobox"', 'class="combobox' . chr( 32 ), $html );

		// 2. Support a Placeholder/Hint
		if( $hint )
		{
			$html = str_replace( ' />', chr( 32 ) . 'placeholder="' . $hint . '" />', $html );
		}//end if

		// 3. Get the Input's Data Attributes
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
			return str_replace( ' />', chr( 32 ) . JArrayHelper::toString( $data ) . ' />', $html );
		}
		else
		{
			return $html;
		}//end if
	}//end function

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options 	= array();
		
		// 4. Grab Options From Configuration
		$configopt 	= ( isset( $this->element['options'] ) ) ? (string)$this->element['options'] : false;

		if( strpos( $configopt, 'config:' ) !== false )
		{
			// Get Configuration Value (strip empty(explode key) entries & get config value)
			$config 		= current( array_filter( explode( 'config:', $configopt ) ) );
			$opts			= Helper::params( $config );

			if( is_array( $opts ) )
			{
				foreach( $opts as $option )
				{
					$options[] = JHtml::_( 'select.option', $option );
				}//end foreach
			}//end if
		}//end if

		return array_merge( $options, parent::getOptions() );
	}//end function
}//end class
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

jimport( 'joomla.form.formfield' );

/**
 * This Field is a JForm Partner of the JHTML Button
 * Which Allows for JHTML Buttons Inside JForm.
 * This Field Also Adds Extra Functionality.
 *
 * Extra:
 * 1. Allow Skipping of Rendering if this Field Isn't in the Config.
 * 2. Set the Active State of the Field Based on a Configuration Option.
 * 3. Translation of Text Options.
 * 4. Interpret & Set a Field Tooltip.
 */
class ArkFormFieldButton extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'button';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		if( $this->omit() )
		{
			return;
		}//end if

		// Set Basic Options (allow for overriding)
		$options 		= new stdClass;
		$options->id 	= $this->id;
		$options->name 	= $this->name;
		$options->value = $this->value;
		// Keep Track of Manually Set Options
		$skip			= array_keys( (array)$options );

		// Set Active State
		$this->setActive();

		// Set all Form Options to Safe Array
		foreach( $this->element->attributes() as $key => $val )
		{
			// Skip Manually Set Attr's as JForm May Alter Them (e.g. $this->id: 'jform_fieldid', $element['id']: 'fieldid')
			if( !in_array( $key, $skip ) )
			{
				$options->{ (string)$key } = (string)$val;
			}//end if
		}//end foreach

		// Blank out Text if Explicitly Specified
		if( isset( $options->text ) && !$options->text )
		{
			$options->text = chr( 32 );
		}//end if

		// Translate Text Fields
		$this->translate( $options );

		// Set Group Name as Name (e.g. if group of radio's)
		if( isset( $options->group ) )
		{
			// JForm Drops Fields With the Same Name so Use this as Workaround
			$this->__set( 'name', $options->group );

			$options->name = $this->name;
		}//end if

		// Some Button Options are Arrays so Need Passing as Child Nodes (<option key="data" subkey="name" value="jform">)
		foreach( $this->element->children() as $option )
		{
			// Only Set if All Required Data is Available
			if( (string)$option['key'] && (string)$option['subkey'] && (string)$option['value'] )
			{
				// Add Key if Already Set || Create Array (objects not supported)
				if( isset( $options->{ (string)$option['key'] } ) )
				{
					$options->{ (string)$option['key'] }[(string)$option['subkey']] = (string)$option['value'];
				}
				else
				{
					$options->{ (string)$option['key'] } = array( (string)$option['subkey'] => (string)$option['value'] );
				}//end if
			}//end if
		}//end foreach

		// Set Tooltip
		$this->setTooltip( $options );

		// Render Button
		switch( ( isset( $options->tag ) ? $options->tag : null ) )
		{
			default :
			case 'a' :
				return Helper::html( 'button.a', $options );

			case 'radio' : 				// <input type="radio" />
			case 'input.radio' : 		// <input type="radio" />
			case 'checkbox' : 			// <input type="checkbox" />
			case 'input.checkbox' : 	// <input type="checkbox" />
				return Helper::html( 'button.' . str_replace( 'input.', '', $options->tag ), $options );

			case 'input' : 				// <input type="button" />
			case 'button' :				// <button type="button">
				return Helper::html( 'button.' . $options->tag, $options );

			case 'submit' :				// <input type="submit" />
			case 'input.submit' :		// <input type="submit" />
				$options->type = str_replace( 'input.', '', $options->tag );
				return Helper::html( 'button.input', $options );

			case 'button.submit' :		// <button type="submit">
			case 'button.reset' :		// <button type="reset">
			case 'button.button' :		// <button type="button">
				$options->type = str_replace( 'button.', '', $options->tag );
				return Helper::html( 'button.button', $options );
		}//end switch
	}//end function

	/**
	 * Method to translate language strings.
	 *
	 * @param	object  $options	Display Options
	 *
	 * @return	void
	 */
	protected function translate( $options )
	{
		$language		= JFactory::getLanguage();
		$translate_keys = array( 'text', 'title', 'alt' );

		if( !isset( $options->language ) || $options->language !== '0' )
		{
			foreach( $options as $key => $val )
			{
				// Translate Text? (if specifically set or string is all uppercase)
				if( isset( $options->{ $key } ) && in_array( $key, $translate_keys ) && JString::strtoupper( $options->{ $key } ) == $options->{ $key } && $language->hasKey( $options->{ $key } ) )
				{
					$options->{ $key } = JText::_( $val );
				}//end if
			}//end foreach
		}//end if
	}//end function

	/**
	 * Method to Dynamically Remove XML Options Based on a Configuration Parameter. 
	 * The Prefix Takes a Filter Type of "array"/"string" to Determine Whether the Option 
	 *
	 * @note 	This Function Must Be Called Before the "Name"/"FieldName" is Modified
	 *
	 * @usage 	omit="config:param-name"
	 *
	 * @return	bool	Don't Render This Field?
	 */
	protected function omit()
	{
		$omit = ( isset( $this->element['omit'] ) ) ? (string)$this->element['omit'] : false;

		// Have We Got a Config Option to Filter On?
		if( strpos( $omit, 'config:' ) !== false )
		{
			// Get Configuration Value (strip empty(explode key) entries & get config value)
			$config 		= current( array_filter( explode( 'config:', $omit ) ) );
			$option			= Helper::params( $config );

			if( $config )
			{
				// If The Config Option Isn't Related to This Name Then Don't Render This Field
				if( is_array( $option ) && in_array( $this->fieldname, $option ) )
				{
					return false;
				}
				elseif( $option == $this->fieldname )
				{
					return false;
				}//end if

				return true;
			}//end if
		}//end if

		return false;
	}//end function

	/**
	 * Method to Set the Active State of the Button if it isn't as simple as active="true".
	 * This Handles default="true" && active="config:param-name" Which Can Also Set the Active State.
	 * Default Config Values Can Also be Set if They Don't Exist: active="config:param-name:default."
	 *
	 * @note 	This Function Must Be Called Before the "Name"/"FieldName" is Modified
	 *
	 * @todo 	If Fieldset is Bootstraped & a Field is Set to Active/Default Then Re-Clicking the Button Won't Re-Activate (double event)
	 *
	 * @return	void
	 */
	protected function setActive()
	{
		$active = ( isset( $this->element['active'] ) ) ? (string)$this->element['active'] : $this->default;

		// Is Active State Dependent On a Configuration Option?
		if( strpos( $active, 'config:' ) !== false )
		{
			// Get Configuration Value (strip empty(explode key) entries & get config value)
			$config 		= current( array_filter( explode( 'config:', $active ) ) );
			$default 		= false;

			// Catch Default Value (if a third option is in the string use it as the default)
			if( strpos( $config, ':' ) !== false )
			{
				$config 	= explode( ':', $config );
				$default 	= $config[1];
				$config 	= $config[0]; // Flatten Config Value (once default is extracted)
			}//end if

			// If There is a Config Value && The Config Option == This Name Then Activate
			$active = ( count( $config ) && Helper::params( $config, $default ) == $this->fieldname ) ? true : false;
		}//end if

		// Set Back or Clear (in case it has been updated by the default or config value)
		if( $active || $active == 'true' )
		{
			$this->element['active'] = 'true';
		}
		else
		{
			unset( $this->element['active'] );
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
		if( isset( $options->data ) && isset( $options->data['toggle'] ) && $options->data['toggle'] == 'tooltip' )
		{
			// Fire Tooltip
			JHTML::_( 'bootstrap.tooltip' );

			$options->class = ( isset( $options->class ) ) ? $options->class : null;
			$options->extra = ( isset( $options->extra ) ) ? $options->extra : null;

			// If the Joomla Tooltip Class Has Not Been Added, Then be Helpful and Add it
			if( !in_array( Helper::css( 'joomla.tooltip' ), array( $options->class, $options->extra ) ) )
			{
				if( !$options->extra )
				{
					$options->extra = Helper::css( 'joomla.tooltip' );
				}
				else
				{
					$options->extra = $options->extra . chr( 32 ) . Helper::css( 'joomla.tooltip' );
				}//end if
			}//end if
		}//end if
	}//end function
}//end class
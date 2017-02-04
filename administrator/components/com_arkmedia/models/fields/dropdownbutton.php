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
 * This Field Renders a Normal Bootstrap Button but Adds Dropdown Functionality.
 */
class ArkFormFieldDropDownButton extends ArkFormFieldButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'dropdownbutton';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		$options = $this->getOptions();

		if( count( $options ) )
		{
			// Set Dropdown Attributes
			$extra 						= ( isset( $this->element['extra'] ) ) ? (string)$this->element['extra'] . chr( 32 ) : '';
			$this->element['html'] 		= true;
			$this->element['tag'] 		= 'button'; // Needs to be a Button for Some Reason
			$this->element['type'] 		= 'button'; // Set the <button type=""> Away From $this->type
			$this->element['bootstrap'] = 'dropdown';
			$this->element['extra'] 	= $extra . Helper::css( 'button.dropdown.toggle' );
			$this->element['posttext'] 	= Helper::html( 'icon.uikit', 'caret-down' );

			// Build Dropdown Items
			$menus = array();

			foreach( $options as $option )
			{
				$menus[] 	= '<li>' . Helper::html( 'button.a', $option ) . '</li>';
			}//end foreach

			return '<div class="' . Helper::css( 'button.group' ) . '" role="group">
						' .  parent::getInput() . '
						<ul class="' . Helper::css( 'button.dropdown.menu' ) . '" role="menu">
							' . implode( "\n", $menus ) . '
						</ul>
					</div>';
		}
		else
		{
			return parent::getInput();
		}//end if
	}//end function

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$options = array();

		// Some Element Options/Children are Button Options so Catch Our Items then Strip them Out
		foreach( $this->element->children() as $option )
		{
			// Only Catch if All Required Data is Available
			if( (string)$option['key'] === 'menu' && (string)$option['text'] )
			{
				$attrs 			= new stdClass;
				$attrs->class 	= '';
				$attrs->text 	= JText::_( (string)$option['text'] );
				$attrs->data 	= array();

				if( (string)$option['html'] )
				{
					$attrs->html = true;
				}//end if

				if( (string)$option['link'] )
				{
					$attrs->link = (string)$option['link'];
				}//end if

				// Icon Available?
				if( (string)$option['icon'] )
				{
					$attrs->icon = (string)$option['icon'];

					if( (string)$option['icongroup'] )
					{
						$attrs->prefixes = array( 'icon' => (string)$option['icongroup'] );
					}//end if

					if( (string)$option['iconextra'] )
					{
						$attrs->iconextra = (string)$option['iconextra'];
					}//end if
				}//end if

				// Get the Options's Data Attributes
				foreach( $option->attributes() as $key => $val )
				{
					// If a Data Attribute (at start of string) Add to Attributes
					if( strpos( $key, 'data-' ) === 0 )
					{
						$attrs->data[$key] = (string)$val;
					}//end if
				}//end foreach

				$options[] = $attrs;
			}//end if
		}//end foreach

		return $options;
	}//end function
}//end class
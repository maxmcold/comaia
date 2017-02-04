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

JFormHelper::loadFieldClass( 'groupedlist' );

/**
 * This Field Renders a Normal Grouped List but Adds Additional Functionality.
 *
 * Extra:
 * 1. Makes up for the Fact that you Can't Select Multiple Options to be Selected by Default.
 * 2. Adds the Functionality to Select all by Passing an Asterisk (*) as the Default.
 * 3. Allow Custom/Manually Typed Options.
 * 4. Support Data Attributes.
 * 5. Make Option Values HTML Safe (groupedlist hardcodes option.key.toHtml="false").
 * 6. Allow Group Disabling With Config Values & Bools. group-GROUPVAL="config:parameter-name", group-g2="0".
 * 7. Support Config Set Group & Options. options="config:parameter-name".
 */
class ArkFormFieldListMultiple extends JFormFieldGroupedList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'listmultiple';

	/**
	 * Custom Param (like $translateLabel & $translateHint) to Decide Whether to Translate the Data Attr's.
	 *
	 * @var		bool
	 */
	protected $translateAttributes = true;

	/**
	 * Custom Param to Monitor Whether the Field is Multiple or Not
	 *
	 * @var		bool
	 */
	protected $isMultiple;

	/**
	 * Custom Param to Monitor Whether the Field Allows Custom Entries or Not
	 *
	 * @var		bool
	 */
	protected $isCustom;

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

		// 1. If a Multiple Field an the Value/Default is a CSV then Split it up so the JHTML::select Can Handle it
		if( $this->isMultiple && is_string( $value ) && strpos( $value, ',' ) !== false )
		{
			$value = explode( ',', $value );
		}
		elseif( $this->isMultiple && $value === '*' ) // 2. Handle Asterisks
		{
			$value = array();

			foreach( $element->children() as $key => $val )
			{
				// Handle OptGroups
				if( $key === 'group' )
				{
					// Get <Group> Options
					foreach( $val as $option )
					{
						$value[] = (string)$option['value'];
					}//end foreach
				}
				else
				{
					$value[] = (string)$val['value'];
				}//end if
			}//end foreach
		}//end if

		return parent::setup( $element, $value, $group );
	}//end function

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$this->isCustom = (string)$this->element['custom'] === 'true';

		// 3. Allow Custom/Manually Typed Options
		if( $this->isCustom )
		{
			$this->addJS();
		}//end if

		$data = array();

		// 4. Get the Input's Data Attributes
		foreach( $this->element->attributes() as $key => $val )
		{
			// If a Data Attribute Add to Input
			if( strpos( $key, 'data-' ) !== false )
			{
				// Translate the Attributes Before Setting? (opt out)
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

	/**
	 * Method to get the field option groups.
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		// 7. Add Group & Options From Configuration
		$configopt 	= ( isset( $this->element['options'] ) ) ? (string)$this->element['options'] : false;

		if( strpos( $configopt, 'config:' ) !== false )
		{
			// Get Configuration Value (strip empty(explode key) entries & get config value)
			$config = current( array_filter( explode( 'config:', $configopt ) ) );
			$opts	= Helper::params( $config );
			$label	= ( isset( $this->element['data-configgroup'] ) ) ? (string)$this->element['data-configgroup'] : '';

			if( is_array( $opts ) )
			{
				// Add a Holding Group for the Options
				$child = $this->element->addChild( 'group' );
				$child->addAttribute( 'value', 'config' );
				$child->addAttribute( 'label', $label );

				// Add the Custom Options
				foreach( $opts as $option )
				{
					$child->addChild( 'option', $option );
				}//end foreach
			}//end if
		}//end if

		// 3 & 4. Add Custom Entries That Were Added from Last Save
		if( $this->isMultiple && $this->isCustom )
		{
			$children = array();

			// Grab Element Option Values
			foreach( $this->element->children() as $option )
			{
				if( (string)$option['value'] )
				{
					$children[] = (string)$option['value'];
				}//end if
			}//end foreach

			// Loop Through Stored Values Looking for Custom (non XML) Entries
			if( $this->value )
			{
				// Not always an array
				if( !is_array( $this->value ) )
				{
					$this->value = (array)$this->value;
				}//end if

				foreach( $this->value as $val )
				{
					// If Value Isn't In XML Options Add it as a New Custom Option
					if( $val && !in_array( $val, $children ) )
					{
						$this->element->addChild( 'option', $val );
					}//end if
				}//end foreach
			}//end if
		}//end if

		// 6. Disable Groups
		$this->_disable( $this->element->children() );

		// Get parsed list
		$groups = parent::getGroups();
		$html 	= isset( $this->element['html'] );

		// 5. Make Option Values HTML Safe
		if( $html )
		{
			foreach( $groups as $group )
			{
				foreach( $group as $option )
				{
					$option->value = htmlspecialchars( $option->value, ENT_COMPAT, 'UTF-8' );
				}//end foreach
			}//end foreach
		}//end if

		return $groups;
	}//end function

	/**
	 * 6. Disable Groups if numeric bool: '0' or if Specified in Config Param.
	 *
	 * @note	Because Unsetting Inside a Loop Screws Up the Index We Re-Initiate the Loop Until They've All Been Processed.
	 *
	 * @param 	object	$groups		SimpleXMLElement List of Groups
	 *
	 * @return	void
	 */
	protected function _disable( $groups )
	{
		for( $i = 0; $i < count( $groups ); $i++ )
		{
			$groupval = (string)$groups[$i]['value'];
			$groupopt = ( isset( $this->element['group-' . $groupval] ) ) ? (string)$this->element['group-' . $groupval] : null;

			if( is_numeric( $groupopt ) && !$groupopt )
			{
				unset( $groups[$i] );

				return $this->_disable( $groups );
			}
			elseif( strpos( $groupopt, 'config:' ) !== false )
			{
				if( !Helper::params( current( array_filter( explode( 'config:', $groupopt ) ) ) ) )
				{
					unset( $groups[$i] );

					return $this->_disable( $groups );
				}//end if
			}//end if
		}//end for
	}//end function

	/**
	 * Method to Add Field JS.
	 *
	 * @note	Logic Borrowed From Joomla, With Customisations
	 *
	 * @see 	libraries/cms/html/tag.php->ajaxfield();
	 *
	 * @return	void
	 */
	protected function addJS()
	{
		// 3. Support Custom Options
		$doc = JFactory::getDocument();
		$js  = "jQuery( document ).ready( function( $ )
			{
				// Add & Append Select Option Function
				var addOption = function( val )
				{
					var option = $('<option>').text( val ).val( val ).attr( 'selected', 'selected' );

					$( '#" . $this->id . "' ).append( option );
				};

				// Update Chosen Select List Function
				var updateList = function( el, ev )
				{
					el.value = '';
					$( '#" . $this->id . "' ).trigger( 'liszt:updated' );
					ev.preventDefault();
				};

				// Add Custom Entry on Enter
				$( document ).on( 'keyup', '#" . $this->id . "_chzn input', function( ev )
				{
					var val = this.value;

					// Option is 1 or More Chars and Enter Pressed
					if( val.length && ( ev.which === 13 || ev.which === 188 ) )
					{
						// Check Custom Option Doesn't Already Exists
						var option = $( '#" . $this->id . " option' ).filter(function(){ return $( this ).html() === val; });

						// Select Existing Option or A New Custom Option?
						if( option.text() !== '' )
						{
							option.attr( 'selected', 'selected' );
						}
						else
						{
							addOption( val );
						}//end if

						// Repopulate List
						updateList( this, ev );
					}
				});

				// Add Click Event to No-Entry Drop Down
				$( document ).on( 'click', '#" . $this->id . "_chzn .no-results', function( ev )
				{
					// Add the Custom Option
					addOption( $( this ).find( 'span' ).html() );

					// Repopulate List
					updateList( this, ev );
				});
			});";

		$doc->addScriptDeclaration( $js );
	}//end function
}//end class
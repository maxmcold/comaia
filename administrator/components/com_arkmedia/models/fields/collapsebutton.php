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
 * This Field Renders a Button Field That Works in Partnership With Other CollapseButtons
 * In Order to Create an Accordion Logic, but Unlike Bootstrap's Version the HTML is:
 * DOM -> button(controller) & DOM -> container(target). As Opposed to Bootstrap's:
 * #accordion > .panel.panel-default <a data-toggle="collapse"> etc.. (Aka Too Specific)
 */
class ArkFormFieldCollapseButton extends ArkFormFieldButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'collapsebutton';

	/**
	 * @var		array	Array Containing JS Loaded Tracker
	 */
	protected static $loaded = array();

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		$this->target 		= ( isset( $this->element['target'] ) ) ? (string)$this->element['target'] : '#' . $this->fieldname;
		$this->groupname 	= ( isset( $this->element['group'] ) ) 	? (string)$this->element['group'] : null;
		$this->unique 		= ( (string)$this->element['unique'] == 'true' || (string)$this->element['tag'] == 'radio' ) ? true : false;
		$this->toggle 		= 'collapse';

		// Add Bootstrap Collapse/Accordion Logic
		if( $this->target )
		{
			// If Unique is Set or Button is a Radio Then Add Custom Switcher Logic 
			// to Behave Like Accordion But Not Require the Same Markup.
			// A Group Value Must Be Present Otherwise We Are Just a Normal Collapse.
			if( $this->unique && $this->groupname )
			{
				// Alter Toggle State
				$this->toggle = 'ark-collapse';

				// Add Toggle Attribute if Not Already Present
				if( !count( $this->element->xpath( 'option[@key="data" and @subkey="group"]' ) ) )
				{
					$opt_group = $this->element->addChild( 'option' );
					$opt_group->addAttribute( 'key', 'data' );
					$opt_group->addAttribute( 'subkey', 'group' );
					$opt_group->addAttribute( 'value', $this->groupname );
				}//end if

				// Add Custom JS
				$this->addJS();
			}//end if

			// Add Custom Active Attribute if Not Already Present (in case button is active as Joomla adds extra classes to actives)
			if( !count( $this->element->xpath( 'option[@key="css" and @subkey="active"]' ) ) )
			{
				$opt_active = $this->element->addChild( 'option' );
				$opt_active->addAttribute( 'key', 'css' );
				$opt_active->addAttribute( 'subkey', 'active' );
				$opt_active->addAttribute( 'value', Helper::css( 'button.active' ) );
			}//end if

			// Add Toggle Attribute if Not Already Present
			if( !count( $this->element->xpath( 'option[@key="data" and @subkey="toggle"]' ) ) )
			{
				$opt_toggle = $this->element->addChild( 'option' );
				$opt_toggle->addAttribute( 'key', 'data' );
				$opt_toggle->addAttribute( 'subkey', 'toggle' );
				$opt_toggle->addAttribute( 'value', $this->toggle );
			}//end if

			// Add Target Attribute if Not Already Present
			if( !count( $this->element->xpath( 'option[@key="data" and @subkey="target"]' ) ) )
			{
				$opt_target = $this->element->addChild( 'option' );
				$opt_target->addAttribute( 'key', 'data' );
				$opt_target->addAttribute( 'subkey', 'target' );
				$opt_target->addAttribute( 'value', $this->target );
			}//end if
		}//end if

		return parent::getInput();
	}//end function

	/**
	 * Method to Add Field JS.
	 *
	 * @return	void
	 */
	protected function addJS()
	{
		// Only Load Once
		if( isset( static::$loaded[$this->groupname] ) )
		{
			return;
		}//end if

		static::$loaded[$this->groupname] = true;

		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		// @required ark-collapse.min.js
		$js = 'jQuery( document ).ready( function( $ )
				{
					jQuery.fn.arkcollapse(
					{
						group		: "' . $this->groupname . '",
						permission	: "' . ( isset( $this->element['permission'] ) ? (string)$this->element['permission'] : '' ) . '" 
					});
				});';

		$doc->addScriptDeclaration( $js );
	}//end function

	/**
	 * Method to Set the Active State of the Button if it isn't as simple as active="true".
	 * This Handles default="true" && active="config:param-name" Which Can Also Set the Active State
	 * Default Config Values Can Also be Set if They Don't Exist: active="config:param-name:default."
	 *
	 * @note 	This Function Must Be Called Before the "Name"/"FieldName" is Modified
	 *
	 * @return	void
	 */
	protected function setActive()
	{
		// Let Parent do the Hard Work and We'll Carry On Afterwards
		parent::setActive();

		$active = ( isset( $this->element['active'] ) ) ? (string)$this->element['active'] : null;

		// If This Button is Active & We're Using Our Custom JS 
		// Then Clear the Active State as We'll Add it Through Data Attrs Instead
		// (This is so the Buttons Aren't Confused/Fired Twice by Bootstrap)
		if( ( $active || $active == 'true' ) && $this->unique && $this->groupname )
		{
			// Add Active Attribute if Not Already Present
			if( !count( $this->element->xpath( 'option[@key="data" and @subkey="active"]' ) ) )
			{
				$opt_active = $this->element->addChild( 'option' );
				$opt_active->addAttribute( 'key', 'data' );
				$opt_active->addAttribute( 'subkey', 'active' );
				$opt_active->addAttribute( 'value', 'true' );
			}//end if

			// Unset to Stop Parent Adding Active as a Class When This Function is Re-Called
			unset( $this->element['active'] );
		}//end if
	}//end function
}//end class
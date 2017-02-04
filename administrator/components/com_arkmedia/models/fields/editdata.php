<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperEdit;

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'hidden' );

/**
 * This Field Adds Hidden Fields to the Form That Contain Edit Logic Request Data.
 */
class ArkFormFieldEditData extends JFormFieldHidden
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'editdata';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		Helper::add( 'helper', 'edit' );

		$app 					= JFactory::getApplication();
		$redirect				= array();
		$html 					= array();
		$fieldname				= $this->__get( 'fieldname' ); // Get Fieldname for Namespacing (for loop will update this otherwise)
		$formcontrol			= $this->formControl; // Get Form control Before We Clear it

		// Get Edit Params (stack, path & edit-file data not included)
		$redirect['editor'] 	= HelperEdit::getEditor();
		$redirect['editorname'] = HelperEdit::getField();
		$redirect['edit'] 		= HelperEdit::getEdit();
		$redirect['editwait'] 	= HelperEdit::isEditWait();
		$redirect['editquick'] 	= HelperEdit::isEditQuick();
		$redirect['stacklock'] 	= (int)HelperEdit::isStackLock(); // Requires Casting to Preserve Falsy Values
		$redirect['stacks'] 	= HelperEdit::getStacks( true );

		// Allow XML Overrides
		$redirect['editor'] 	= ( isset( $this->element['editor'] ) ) 	? (string)$this->element['editor'] 		: $redirect['editor'];
		$redirect['editorname'] = ( isset( $this->element['editorname'] ) ) ? (string)$this->element['editorname'] 	: $redirect['editorname'];
		$redirect['edit'] 		= ( isset( $this->element['edit'] ) ) 		? (string)$this->element['edit'] 		: $redirect['edit'];
		$redirect['editwait'] 	= ( isset( $this->element['editwait'] ) ) 	? (string)$this->element['editwait'] 	: $redirect['editwait'];
		$redirect['editquick'] 	= ( isset( $this->element['editquick'] ) ) 	? (string)$this->element['editquick'] 	: $redirect['editquick'];
		$redirect['stacklock'] 	= ( isset( $this->element['stacklock'] ) ) 	? (string)$this->element['stacklock'] 	: $redirect['stacklock'];
		$redirect['stacks'] 	= ( isset( $this->element['stacks'] ) ) 	? (string)$this->element['stacks'] 		: $redirect['stacks'];

		// Clear Form Control so That Inputs Sit Outside JForm
		$this->formControl 		= null;

		// Strip Out Empty/Unset/Non-Zero Values)
		$redirect = array_filter( $redirect, 'strlen' );

		// Create a Hidden Input for Each Edit Value
		foreach( $redirect as $key => $val )
		{
			// Set the Data & Ensure ID is Unique
			$this->__set( 'id', $fieldname . '_' . $key );
			$this->__set( 'name', $key );
			$this->setValue( $val );

			$html[] = parent::getInput();
		}//end foreach

		return implode( "\n", $html );
	}//end function
}//end class
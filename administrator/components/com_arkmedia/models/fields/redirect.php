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
 * This Field Adds a Hidden Field to the Form That Contains a URL Query String of Redirect Query Parameters.
 */
class ArkFormFieldRedirect extends JFormFieldHidden
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'redirect';

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

		// Get Edit Params (stack, path & edit-file data not included)
		$redirect['editor'] 	= HelperEdit::getEditor();
		$redirect['editorname'] = HelperEdit::getField();
		$redirect['edit'] 		= HelperEdit::getEdit();
		$redirect['editwait'] 	= HelperEdit::isEditWait();
		$redirect['editquick'] 	= HelperEdit::isEditQuick();
		$redirect['stacklock'] 	= (int)HelperEdit::isStackLock(); // Requires Casting to Preserve Falsy Values
		$redirect['stacks'] 	= HelperEdit::getStacks( true );
		$redirect['callback'] 	= HelperEdit::getCallback();

		// Allow XML Overrides
		$redirect['editor'] 	= ( isset( $this->element['editor'] ) ) 	? (string)$this->element['editor'] 		: $redirect['editor'];
		$redirect['editorname'] = ( isset( $this->element['editorname'] ) ) ? (string)$this->element['editorname'] 	: $redirect['editorname'];
		$redirect['edit'] 		= ( isset( $this->element['edit'] ) ) 		? (string)$this->element['edit'] 		: $redirect['edit'];
		$redirect['editwait'] 	= ( isset( $this->element['editwait'] ) ) 	? (string)$this->element['editwait'] 	: $redirect['editwait'];
		$redirect['editquick'] 	= ( isset( $this->element['editquick'] ) ) 	? (string)$this->element['editquick'] 	: $redirect['editquick'];
		$redirect['stacklock'] 	= ( isset( $this->element['stacklock'] ) ) 	? (string)$this->element['stacklock'] 	: $redirect['stacklock'];
		$redirect['stacks'] 	= ( isset( $this->element['stacks'] ) ) 	? (string)$this->element['stacks'] 		: $redirect['stacks'];
		$redirect['callback'] 	= ( isset( $this->element['callback'] ) ) 	? (string)$this->element['callback'] 	: $redirect['callback'];

		// Add Additional Params
		$redirect['tmpl']		= $app->input->get( 'tmpl', false, 'cmd' );

		// Set String to Field Value (after stripping out empty/unset values)
		$this->setValue( JURI::buildQuery( array_filter( $redirect, 'strlen' ) ) );

		return parent::getInput();
	}//end function
}//end class
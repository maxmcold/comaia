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

JFormHelper::loadFieldClass( 'extendedbutton' );

/**
 * This Field Renders a Normal Bootstrap Button but Includes Delete JS Logic as Well.
 */
class ArkFormFieldDeleteButton extends ArkFormFieldExtendedButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'deletebutton';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		$this->addJS();

		return parent::getInput();
	}//end function

	/**
	 * Method to Add Field JS.
	 *
	 * @return	void
	 */
	protected function addJS()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();

		// @required ark-deleteitems.min.js

		// Get Hidden Input Element ID's or Guess Them
		$stack 		= ( $this->element['stackname'] ) 	? (string)$this->element['stackname'] 	: 'stack';
		$path 		= ( $this->element['pathname'] ) 	? (string)$this->element['pathname'] 	: 'path';
		$folders 	= ( $this->element['foldersname'] ) ? (string)$this->element['foldersname'] : 'folders';
		$files 		= ( $this->element['filesname'] ) 	? (string)$this->element['filesname'] 	: 'files';
		$js 		= 'var deleteitems;

					jQuery( document ).ready( function( $ )
					{
						deleteitems = jQuery.fn.deleteitems(
						{
							css		: {
										form		: "#action-delete-items",
										stack		: "#' .  $this->formControl . '_' . $stack . '",
										path		: "#' .  $this->formControl . '_' . $path . '",
										folders		: "#' .  $this->formControl . '_' . $folders . '",
										files		: "#' .  $this->formControl . '_' . $files . '",
										control		: "#' . $this->id . '"
									  },
							html 	: {
										errors		: {
														path 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETENOPATH_FAIL', true ) . '",
														confirm	: {
																	all 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETE_CONFIRM', true ) . '",
																	folders : "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETEFOLDERS_CONFIRM', true ) . '",
																	folder 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETEFOLDER_CONFIRM', true ) . '",
																	files 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETEFILES_CONFIRM', true ) . '",
																	file 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETEFILE_CONFIRM', true ) . '"
																  },
														select 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_DELETENOSELECTED_FAIL', true ) . '"
													  }
									  },
							confirm	: ' . (int)Helper::params( 'confirm-delete' ) . '
						});
					});';

		$doc->addScriptDeclaration( $js );
	}//end function
}//end class
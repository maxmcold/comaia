<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperEdit, Ark\Media\HelperVersion;

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'button' );

/**
 * This Field Renders a Normal Bootstrap Button but Includes Insert File JS Logic as Well.
 */
class ArkFormFieldInsertButton extends ArkFormFieldButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'insertbutton';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		Helper::add( 'helper', 'edit' );
		Helper::add( 'helper', 'version' );

		$this->addJS();

		// Update Button to Alt Text When Not in Full Edit Mode
		if( !HelperEdit::isEditFull() )
		{
			$this->element['text'] = ( $this->element['fulltext'] ) ? (string)$this->element['fulltext'] : $this->element['text'];
			$this->element['icon'] = ( $this->element['fullicon'] ) ? (string)$this->element['fullicon'] : $this->element['icon'];

			if( HelperVersion::isBasic() )
			{
				$this->element['text'] = ( $this->element['basictext'] ) ? (string)$this->element['basictext'] : $this->element['text'];
				$this->element['icon'] = ( $this->element['basicicon'] ) ? (string)$this->element['basicicon'] : $this->element['icon'];
			}//end if
		}//end if

		// Prevent invalid attributes being added to the element
		unset( $this->element['fulltext'] );
		unset( $this->element['fullicon'] );
		unset( $this->element['basictext'] );
		unset( $this->element['basicicon'] );

		return parent::getInput();
	}//end function

	/**
	 * Method to Add Field JS.
	 *
	 * @return	void
	 */
	protected function addJS()
	{
		// If Initiated by an Editor Continue With Insert Logic Else Hide Button & Bail
		$edit			= HelperEdit::isEditMode();
		$doc 			= JFactory::getDocument();
		$message		= JText::_( ARKMEDIA_JTEXT . 'MSG_INSERTNOSELECTED_FAIL', true );

		if( !HelperEdit::isEditFull() )
		{
			$message	= JText::_( ARKMEDIA_JTEXT . 'MSG_INSERTNOSELECTEDALT_FAIL', true );
		}//end if

		// @required ark-insertfile.min.js

		// Get Hidden Input Element ID's or Guess Them
		$js = 'var insertfile;

			jQuery( document ).ready( function( $ )
			{
				insertfile = jQuery.fn.insertfile(
				{
					css		: {
								control		: "#' . $this->id . '",
								loading		: "#' .  $this->formControl . '_inserting"
							  },
					html 	: {
								errors		: {
												select : "' . $message . '"
											  }
							  },
					render	: ' . (int)$edit . '
				});
			});';

		$doc->addScriptDeclaration( $js );
	}//end function
}//end class
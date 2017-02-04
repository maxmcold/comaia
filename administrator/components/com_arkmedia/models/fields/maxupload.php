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

JFormHelper::loadFieldClass( 'text' );

/**
 * This Field Populates the Default to the Server Max Upload Default if No Entry is Present.
 */
class ArkFormFieldMaxUpload extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'maxupload';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// Get Lowest Upload Value as the Maximum Upload
		$upload = Helper::html( 'text.bytes', ini_get( 'upload_max_filesize' ) );
		$post 	= Helper::html( 'text.bytes', ini_get( 'post_max_size' ) );
		$memory = Helper::html( 'text.bytes', ini_get( 'memory_limit' ) );
		$min	= min( array_filter( array( $upload, $post, $memory ) ) ); // Filter Out Null Values
		$suffix	= '';

		// Valid Value Found?
		if( $min )
		{
			$limit		= JHTML::_( 'number.bytes', $min, 'MB' );

			// Translate JText Value
			if( isset( $this->element['suffix'] ) && JFactory::getLanguage()->hasKey( $this->element['suffix'] ) )
			{
				$suffix = chr( 32 ) . JText::sprintf( $this->element['suffix'], $limit );
			}
			else
			{
				$suffix = $limit;
			}//end if

			// Set the Value to the Server Max if No Value is Entered
			if( !$this->value )
			{
				// Set Megabyte Version
				$this->setValue( ( $min / 1024 / 1024 ) );
			}//end if
		}//end if

		return parent::getInput() . '<span class="help-block">' . $suffix . '</span>';
	}//end function
}//end class
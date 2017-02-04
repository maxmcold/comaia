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

/**
 * This Field Renders a Simple Message Element. 
 * Unlike Joomla's "Note" Field this Renders in the Input Position Rather than the Label Position.
 */
class ArkFormFieldMessage extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'message';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		// Places to Look for Text
		$fields 	= array( 'message', 'title', 'text', 'label', 'description' );
		$message 	= false;
		$attrs		= array();

		foreach( $fields as $field )
		{
			if( isset( $this->element[$field] ) )
			{
				$message = (string)$this->element[$field];

				break;
			}//end if
		}//end foreach

		$message		= ( $this->translateLabel ) 			? JText::_( $message ) 						: $message;
		$tag			= ( $this->element['tag'] ) 			? (string)$this->element['tag']				: 'div';
		$attrs['id']	= ( $this->element['id'] ) 				? (string)$this->element['id'] 				: $this->id;
		$attrs['class']	= ( $this->element['class'] ) 			? (string)$this->element['class'] 			: false;
		$attrs['style']	= ( $this->element['style'] ) 			? (string)$this->element['style'] 			: false;

		// Add Icon if Passed
		if( $this->element['icon'] )
		{
			$message = Helper::html( 'icon.icomoon', (string)$this->element['icon'] ) . chr( 32 ) . $message;
		}//end if

		return '<' . $tag . chr( 32 ) . JArrayHelper::toString( array_filter( $attrs ) ) . '>' . $message . '</' . $tag . '>';
	}//end function

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 *
	 * @return  string  The field label markup.
	 */
	protected function getLabel()
	{
		return '';
	}//end function
}//end class
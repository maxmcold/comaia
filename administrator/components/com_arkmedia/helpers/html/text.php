<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

/**
 * Render Text Elements & Items.
 *
 * @usage		JHTML::_( ARKMEDIA_HTML_ID . 'text.tab' );
 * 				Helper::html( 'text.tab', 2 );
 * 				Helper::html( 'text.implode', array( 'class1', 'class2' ) );
 * 				Helper::html( 'text.bytes', '128M' );
 */
abstract class MediaHtmlText
{
	/**
	 * Return a Tab Index.
	 *
	 * @param	int		$count		The Amount of Tabs to Return
	 *
	 * @return  string	HTML Tabs
	 */
	public static function tab( $count = 1 )
	{
		return str_repeat( JFactory::getDocument()->_getTab(), (int)$count );
	}//end function

	/**
	 * Implode an Array/Multidimensional Array to a $glue Separated String.
	 *
	 * @param	mixed	$obj		Array of Strings or Object of Array of Strings
	 * @param	string	$glue		Option Glue String Otherwise Space is Used
	 *
	 * @return  mixed	HTML String/Object of Strings
	 */
	public static function implode( $obj, $glue = null )
	{
		// Data Integrity Check
		if( is_scalar( $obj ) )
		{
			return;
		}//end if

		if( is_object( $obj ) )
		{
			foreach( $obj as $key => $val )
			{
				$obj->{ $key } = static::implode( $val, $glue );
			}//end foreach

			return $obj;
		}
		else
		{
			return implode( ( $glue ? $glue : chr( 32 ) ), (array)$obj );
		}
	}//end function

	/**
	 * Convert a Server Value to Bytes. e.g. '8M' = 8388608
	 * Unlimited/-1 Values are Also Handled.
	 *
	 * @param	string	$value		The Ini Directive Value
	 *
	 * @return  int		Byte Integer
	 */
	public static function bytes( $value = 0 )
	{
		switch( substr( $value, -1 ) )
		{
			case 'B' : case 'b' : 
				return (int)$value;

			case 'K' : case 'k' :
				return (int)$value * 1024;

			case 'M' : case 'm' : 
				return (int)$value * 1024 * 1024;

			case 'G' : case 'g' :
				return (int)$value * 1024 * 1024 * 1024;

			default :
				return ( $value !== '-1' && $value !== -1 ) ? $value : 0;
		}//end switch
	}//end function
}//end class
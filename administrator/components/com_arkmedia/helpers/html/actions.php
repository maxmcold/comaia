<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper;

defined( '_JEXEC' ) or die;

/**
 * Render File Action Logic Snippets.
 *
 * @usage		JHTML::_( ARKMEDIA_HTML_ID . 'actions.tooltip' );
 * 				Helper::html( 'actions.tooltip' );
 */
abstract class MediaHtmlActions
{
	/**
	 * Add JS Custom Tooltip Logic as Bootstrap's Version Was too Complex.
	 * This Version Delegates & Looks for Elements With a Tooltip Class, a data-container Attribute AND a data-target Attribute.
	 * Then Works Backwards From the Tooltip to the Container (unlike bootstrap) and Then Looks for the Labels Within the Parent Container.
	 *
	 * @note 	The Labels are Found Using the data-target Attribute.
	 * 			It is Possible to Initiate this Logic Without Passing Any Arguments & Using Data Attributes Only
	 *
	 * @param	string	$selector	The CSS Selector to Use to Locate to Tooltip Buttons (this holds details on the rest of the necessary selectors)
	 * @param	string	$container	The CSS Selector to Restrict the Tooltip Search to (although 'a' parent, this is not 'the' parent we search for labels in)
	 *
	 * @return  void
	 */
	public static function tooltip( $selector = null, $container = null )
	{
		$doc 	= JFactory::getDocument();
		$space 	= '.arktooltip';
		$tip 	= ( $selector ) 	?: '.' . Helper::css( 'tooltip' ) . '[data-container][data-target]';
		$el 	= ( $container ) 	?: '#' . Helper::css( 'root' );

		$js 	= 'jQuery( document ).ready( function( $ )
				{
					$( "' . $el . '" ).on( "mouseenter' . $space . '", "' . $tip . '", function( ev )
					{
						var container = $( this ).parents( $( this ).data( "container" ) ).get( 0 );

						if( container )
						{
							$( container ).find( $( this ).data( "target" ) ).addClass( "in" );
						}//end if
					});

					$( "' . $el . '" ).on( "mouseleave' . $space . '", "' . $tip . '", function( ev )
					{
						var container = $( this ).parents( $( this ).data( "container" ) ).get( 0 );

						if( container )
						{
							$( container ).find( $( this ).data( "target" ) ).removeClass( "in" );
						}//end if
					});
				});';

		$doc->addScriptDeclaration( $js );
	}//end function
}//end class
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

$messages		= $displayData->messages;
$css_root		= Helper::css( 'message' ); // Same As Content Message But Class Rather Than ID
$css_panel		= array( Helper::css( 'box.panel.container' ), Helper::css( 'box.panel.small' ) );
$css_body		= array( Helper::css( 'box.panel.body' ) );
$css_message	= array( Helper::css( 'box.jumbo.container' ) );
$css_sub		= array( Helper::css( 'text.muted' ) );
$css_icon		= array( Helper::css( 'text.muted' ) );

// Root Container
echo '<div class="' . $css_root . '">';

// Render Messages
foreach( $messages as $message )
{
	// Panel Container
	echo '<div class="' . implode( chr( 32 ), $css_panel ) . '">';

	// Panel Body
	echo '<div class="' . implode( chr( 32 ), $css_body ) . '">';

	// Message
	echo '<div class="' . implode( chr( 32 ), $css_message ) . '">';

	if( $message->title )
	{
		echo '<h1>';

		// Icon
		if( isset( $message->icon ) )
		{
			echo '<span class="' . implode( chr( 32 ), $css_icon ) . '">';
			echo Helper::html( 'icon.icomoon', $message->icon );
			echo '</span>';
			echo chr( 32 );
		}//end if

		// Title
		echo $message->title;

		echo '</h1>';
	}//end if

	if( $message->message )
	{
		// If Array Loop Through (if string only one row will render)
		foreach( (array)$message->message as $key => $text )
		{
			// Render Faint Row or Normal Row?
			if( $key === 'sub' )
			{
				echo '<p class="' . implode( chr( 32 ), $css_sub ) . '">' . $text . '</p>';
			}
			else
			{
				echo '<p>' . $text . '</p>';
			}//end if
		}//foreach
	}//end if

	if( isset( $message->button ) )
	{
		// Can Pass One Button (object) or Array of Buttons
		if( is_object( $message->button ) )
		{
			$message->button = array( $message->button );
		}//end if

		$count = count( $message->button );

		if( $count )
		{
			echo '<p>';

			foreach( $message->button as $i => $button )
			{
				if( $button->text )
				{
					$button->colour = ( isset( $button->colour ) && $button->colour ) ? $button->colour : 'primary';
					$button->size 	= ( isset( $button->size ) && $button->size ) ? $button->size : 'large';
					$options		= array(
						'text' 		=> ( isset( $button->text ) ? $button->text : '' ),
						'href' 		=> ( isset( $button->href ) ? $button->href : '' ),
						'colour' 	=> ( isset( $button->colour ) ? $button->colour : '' ),
						'size' 		=> ( isset( $button->size ) ? $button->size : '' ),
						'icon' 		=> ( isset( $button->icon ) ? $button->icon : '' ),
						'target' 	=> ( isset( $button->target ) ? $button->target : '' )
					);

					echo Helper::html( 'button.a', $options );
					echo ( ( $i + 1 ) < $count ) ? chr( 32 ) : '';
				}//end if
			}//end foreach

			echo '</p>';
		}//end if
	}//end if

	// Close Message
	echo '</div>';

	// Close Body
	echo '</div>';

	// Close Panel
	echo '</div>';
}//end foreach

// Close Root
echo '</div>';
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

Helper::html( 'ark.bootstrap' );
Helper::html( 'ark.framework', array( 'js' => false ) );

$message	= (object)array( 'title' => JText::_( ARKMEDIA_JTEXT . 'MSG_WAIT_INFO' ), 'message' => JText::_( ARKMEDIA_JTEXT . 'MSG_BROWSER_IE8_INFO' ), 'icon' => 'IE' );
$id			= Helper::css();

// Root Container
echo '<div id="' . $id . '">';

// Render Message
echo Helper::layout( 'message.generic', (object)array( 'messages' => array( $message ) ) );

// Close Root
echo '</div>';
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

$messages	= Helper::contentMessage();
$id			= Helper::css( 'message' );

// Root Container
echo '<div id="' . $id . '">';

// Render Content Messages
echo Helper::layout( 'message.generic', (object)array( 'messages' => $messages ) );

// Close Root
echo '</div>';
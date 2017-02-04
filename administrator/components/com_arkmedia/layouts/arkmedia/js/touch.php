<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

// @note	Touch Events Moved From Separate Library to UIKit.
// @see		http://zeptojs.com/#touch

// Prevent User Zooming as Some Logic is Hindered With it Enabled (initial-scale = retain orientation zooming)
JFactory::getDocument()->setMetaData( 'viewport', 'width=device-width,user-scalable=no,initial-scale=1' );
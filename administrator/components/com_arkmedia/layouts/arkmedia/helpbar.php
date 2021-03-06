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

$helpbar 		= $displayData->helpbar;
$form 			= $helpbar->form;
$options 		= $helpbar->options;
$params			= new JRegistry;
$css			= new JRegistry;

// Set Layout/Display Parameters
$css->set( 'container', array( Helper::css( 'button.toolbar' ), Helper::css( 'align.right' ) ) );
$css->set( 'group', array( Helper::css( 'button.group' ) ) );
$params->set( 'id', Helper::css( 'helpbar' ) );
$params->set( 'role', 'toolbar' );
$params->set( 'css', $css );

// Render Generic JForm Layout
echo Helper::layout( 'jform', (object)array( 'form' => $form, 'options' => $options, 'params' => $params ) );
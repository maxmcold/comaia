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

$subactions 	= $displayData->subactions;
$form 			= $subactions->form;
$options 		= $subactions->options;
$params			= new JRegistry;
$css			= new JRegistry;
$tags			= new JRegistry;

// Set Layout/Display Parameters
$css->set( 'container', array( Helper::css( 'form.container.inline' ) ) );
$css->set( 'group', array( Helper::css( 'form.group' ) ) );
$tags->set( 'group', 'form' );
$params->set( 'id', Helper::css( 'subactions' ) );
$params->set( 'role', 'toolbar' );
$params->set( 'css', $css );
$params->set( 'tags', $tags );

// Render Generic JForm Layout
echo Helper::layout( 'jform', (object)array( 'form' => $form, 'options' => $options, 'params' => $params ) );
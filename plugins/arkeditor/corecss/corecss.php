<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://www.webxsolution.com
# Terms of Use: An extension that is derived from the ARK editor will only be allowed under the following conditions: http://arkextensions.com/products/ark-editor#terms-of-use
# ------------------------------------------------------------------------*/ 
defined('_JEXEC') or die;

/**
 *Ark  Editor Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  ArkEditor.FontAwesome
 */
class PlgArkEditorCoreCss extends JPlugin
{
	public function onBeforeInstanceLoaded(&$params) {		if (JFactory::getApplication()->isAdmin())			return;				$document = JFactory::getDocument();		$document->addStyleSheet(JURI::base().'plugins/arkeditor/corecss/corecss/css/arkeditor.min.css');	}		
	public function onInstanceLoaded(&$params) {}
}
<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperVersion;

defined( '_JEXEC' ) or die;

Helper::add( 'helper', 'version' );

// This File is for Header Data Only (code is not echo'd)
$app 			= JFactory::getApplication();
$doc 			= JFactory::getDocument();
$permissions 	= JHtml::getJSObject( Helper::actions()->getProperties() );

// @required uikit.min.js
// @required noty.min.js
// @required ark-base.min.js
Helper::html( 'ark.js', 'ark.min' );

$js = 'jQuery( document ).ready( function( $ )
		{
			jQuery.fn.arkoptions(
			{
				css			: {
								root 			: "#' . Helper::css() . '",
								actions 		: "#' . Helper::css( 'actions' ) . '",
								content 		: "#' . Helper::css( 'content' ) . '",
								editbox 		: "#' . Helper::css( 'edit.container' ) . '",
								bootstrap		: {
													button 		: { value : "' . Helper::css( 'button.button' ) . '", selector : ".' . Helper::css( 'button.button' ) . '" },
													active		: { value : "' . Helper::css( 'button.active' ) . '", selector : ".' . Helper::css( 'button.active' ) . '" }
												  }
							  },
				html 		: {
								dash 			: "' . JText::_( ARKMEDIA_JTEXT . 'WRAP_DASH' ) . '",
								comma 			: "' . JText::_( ARKMEDIA_JTEXT . 'WRAP_COMMA' ) . '",
								quote 			: "' . htmlspecialchars( JText::_( ARKMEDIA_JTEXT . 'WRAP_QUOTES' ) ) . '",
								date 			: "' . JText::_( ARKMEDIA_JTEXT . 'DATEJS' ) . '",
								messages 		: {
													loading 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_JS_LOADING', true ) . '",
													feature		: {
																	soon 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_FEATURESOON_INFO', true ) . '",
																	upgrade : "' . JText::_( ARKMEDIA_JTEXT . 'MSG_FEATUREUPGRADE_INFO', true ) . '",
																	plugin 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_FEATUREPLUGIN_INFO', true ) . '"
																  }
												  },
								errors			: {
													generic		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_JS_GENERIC', true ) . '",
													data		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_JS_DATA', true ) . '",
													form 		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_JS_FORM', true ) . '",
													permission 	: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_JS_PERMISSION', true ) . '",
													support		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_JS_SUPPORT', true ) . '",
													action 		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_ACTION_FAIL', true ) . '",
													cancel 		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_ACTION_CANCEL', true ) . '",
													path 		: "' . JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONNOPATH_FAIL', true ) . '"
												  },
								ds 				: "' . addslashes( ARKMEDIA_DS ) . '",
								url 			: "' . JURI::root() . '"
							  },
				version		: {
								number			: "' . HelperVersion::version() . '",
								pro				: ' . (int)HelperVersion::isPro() . ',
								basic			: ' . (int)HelperVersion::isBasic() . '
							  },
				view		: "' . Helper::params( 'view-default' ) . '",
				text		: {
								prefix			: "' . ARKMEDIA_JTEXT . '"
							  },
				confirmbox	: {
								yes				: { text : "' . JText::_( ARKMEDIA_JTEXT . 'BTN_OK', true ) . '" },
								no				: { text : "' . JText::_( ARKMEDIA_JTEXT . 'BTN_CANCEL', true ) . '" }
							  },
				promptbox	: {
								yes				: { text : "' . JText::_( ARKMEDIA_JTEXT . 'BTN_OK', true ) . '" },
								no				: { text : "' . JText::_( ARKMEDIA_JTEXT . 'BTN_CANCEL', true ) . '" }
							  },
				debug		: ' . (int)$app->getCfg( 'debug' ) . ',
				permissions	: ' . $permissions . '
			});
		});';

$doc->addScriptDeclaration( $js );
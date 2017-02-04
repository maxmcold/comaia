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
defined( '_ARKMEDIA_EXEC' ) or die;

// THIS FILE ALLOWS US TO INITIATE MOST OF THE ARK AND CAN BE CALLED BY COMPONENTS, PLUGINS, FIELDS etc.
// Note - 	There Are Currently 3 Cache Subsidiaries to this Ark Extension.
// 			1: Server - Cache of Stacks/File Tree. (temporary - manually cleared on each page load or cleared in Joomla or Global Configuration time expires)
// 			2: Server - Cache of Thumbnail Generations. (permanent - unless cleared in Joomla or a change is made to a file)
// 			3: Client - Cache of Entire Stack in Browser sessionStorage. (per browser tab)

// Set Up Defines
define( 'ARKMEDIA_DS', 					chr( 47 ) );								// SYSTEM DIRECTORY SEPARATOR (@see [#2877])
define( 'ARKMEDIA_ROOT', 				str_replace( '\\', '/', JPATH_ROOT ) );		// NORMALISED SYSTEM INSTALLATION (@see [#2877])

define( 'ARKMEDIA_COMPONENT', 			'com_arkmedia' );							// COMPONENT NAME (used for defines, requires, namespacing cache & sessions etc.)
define( 'ARKMEDIA_COMPONENT_ID', 		'arkmedia' );								// COMPONENT ID (used for namespacing files, requires etc.)
define( 'ARKMEDIA_HTML_ID', 			'MediaHTML' );								// JHTML ID (used to prevent conflicts between ARK extensions)

define( 'ARKMEDIA_PLUGIN_STACK', 		ARKMEDIA_COMPONENT_ID );					// STACK PLUGINS FOLDER
define( 'ARKMEDIA_PLUGIN_CONNECTOR', 	ARKMEDIA_COMPONENT_ID . '-connector' );		// CONNECTOR PLUGINS FOLDER
define( 'ARKMEDIA_PLUGIN_FEATURE', 		ARKMEDIA_COMPONENT_ID . '-feature' );		// FEATURE PLUGINS FOLDER
define( 'ARKMEDIA_JTEXT', 				'COM_ARKMEDIA_' );							// JTEXT NAMESPACE
define( 'ARKMEDIA_PACKAGE', 			'pkg_' . ARKMEDIA_COMPONENT_ID . 'suite' );	// PACKAGE NAME

define( 'ARKMEDIA_FE', 					ARKMEDIA_ROOT . ARKMEDIA_DS . 'components' . ARKMEDIA_DS . ARKMEDIA_COMPONENT . ARKMEDIA_DS );
define( 'ARKMEDIA_BE', 					ARKMEDIA_ROOT . ARKMEDIA_DS . 'administrator' . ARKMEDIA_DS . 'components' . ARKMEDIA_DS . ARKMEDIA_COMPONENT . ARKMEDIA_DS );
define( 'ARKMEDIA_PLUGIN_STACKS', 		ARKMEDIA_ROOT . ARKMEDIA_DS . 'plugins' . ARKMEDIA_DS . ARKMEDIA_PLUGIN_STACK . ARKMEDIA_DS );
define( 'ARKMEDIA_PLUGIN_CONNECTORS', 	ARKMEDIA_ROOT . ARKMEDIA_DS . 'plugins' . ARKMEDIA_DS . ARKMEDIA_PLUGIN_CONNECTOR . ARKMEDIA_DS );
define( 'ARKMEDIA_PLUGIN_FEATURES', 	ARKMEDIA_ROOT . ARKMEDIA_DS . 'plugins' . ARKMEDIA_DS . ARKMEDIA_PLUGIN_FEATURE . ARKMEDIA_DS );
define( 'ARKMEDIA_MEDIA', 				ARKMEDIA_ROOT . ARKMEDIA_DS . 'media' . ARKMEDIA_DS . ARKMEDIA_COMPONENT_ID . ARKMEDIA_DS );
define( 'ARKMEDIA_HELPERS', 			ARKMEDIA_BE . 'helpers' . ARKMEDIA_DS );
define( 'ARKMEDIA_EXCEPTIONS',			ARKMEDIA_BE . 'helpers' . ARKMEDIA_DS . 'exceptions' . ARKMEDIA_DS );
define( 'ARKMEDIA_HTML', 				ARKMEDIA_BE . 'helpers' . ARKMEDIA_DS . 'html' . ARKMEDIA_DS );
define( 'ARKMEDIA_LAYOUTS', 			ARKMEDIA_BE . 'layouts' . ARKMEDIA_DS . ARKMEDIA_COMPONENT_ID . ARKMEDIA_DS );
define( 'ARKMEDIA_CACHE', 				ARKMEDIA_MEDIA . 'cache' . ARKMEDIA_DS ); // Ignore JPATH_CACHE and Use Custom Location to Avoid Security Software Interfering
define( 'ARKMEDIA_CACHE_STACKS',		ARKMEDIA_COMPONENT . '.stacks' );
define( 'ARKMEDIA_CACHE_THUMBS',		ARKMEDIA_COMPONENT . '.thumbs' );

define( 'ARKMEDIA_FE_URL', 				JURI::root() . 'components' . ARKMEDIA_DS . ARKMEDIA_COMPONENT . ARKMEDIA_DS );
define( 'ARKMEDIA_BE_URL',				JURI::root() . 'administrator' . ARKMEDIA_DS . 'components' . ARKMEDIA_DS . ARKMEDIA_COMPONENT . ARKMEDIA_DS );
define( 'ARKMEDIA_MEDIA_URL',			JURI::root() . 'media' . ARKMEDIA_DS . ARKMEDIA_COMPONENT_ID . ARKMEDIA_DS );
define( 'ARKMEDIA_CACHE_URL',			ARKMEDIA_MEDIA_URL . 'cache' . ARKMEDIA_DS );

// Add FileSystem (currently just in case)
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file' );

// Ensure BE language is loaded (when not in component context)
JFactory::getLanguage()->load( ARKMEDIA_COMPONENT, JPATH_ADMINISTRATOR );

// Add Helper
require_once ARKMEDIA_HELPERS . 'ark.php';

// Add Stack Plugins
Helper::add( 'plugin', ARKMEDIA_PLUGIN_STACK );

// Add Connector Plugins
Helper::add( 'plugin', ARKMEDIA_PLUGIN_CONNECTOR );

// Add Any Feature Plugins
Helper::add( 'plugin', ARKMEDIA_PLUGIN_FEATURE );

// Add HTML Path
Helper::add( 'html' );

// Add Stack Helper
Helper::add( 'helper', 'stack' );
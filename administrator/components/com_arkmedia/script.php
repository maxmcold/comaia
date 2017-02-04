<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

class com_arkmediaInstallerScript
{
	/**
	 * Method to Install the Package.
	 *
	 * @note 	These Custom Attributes Will Wipe Any Previously Stored Params in the DB.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function install( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to Uninstall the Package.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function uninstall( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to Update the Package.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function update( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to Run Before an install/update/uninstall Method.
	 *
	 * @param	string	$route		Which action is happening (install || uninstall || discover_install).
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function preflight( $route, JAdapterInstance $adapter )
	{
	
	}//end function

	/**
	 * Method to Run After an install/update/uninstall Method.
	 *
	 * @param	string	$route		Which action is happening (install || uninstall || discover_install).
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function postflight( $route, JAdapterInstance $adapter )
	{
		// Fresh Install Only (to save destroying existing permissions)
		if( $route === 'install' )
		{
			$app 	= JFactory::getApplication();
			$asset 	= JTable::getInstance( 'Asset' );
			$class 	= str_replace( 'InstallerScript', '', __CLASS__ );
			$path 	= JPATH_ROOT . chr( 47 ) . 'administrator' . chr( 47 ) . 'components' . chr( 47 ) . $class . chr( 47 );

			// Get Asset Entry
			if( $asset->loadByName( $class ) )
			{
				// Load Our Access File
				$xml = simplexml_load_file( $path . 'access.xml' );

				if( $xml instanceof SimpleXMLElement )
				{
					// Grab Entries With Defaults
					$actions 	= $xml->xpath( '//action[@name][@default]' );
					$rules 		= new stdClass;

					// Add Action Entry & Assign Defaults to the Entry
					foreach( $actions as $action )
					{
						// Defaults Are CSV (Assign to '1' to Signify Active Permission)
						$defaults = explode( ',', (string)$action['default'] );
						$rules->{ (string)$action['name'] } = array_fill_keys( $defaults, 1 );
					}//end foreach

					// Set & Store Default Permissions
					$asset->rules = json_encode( $rules );

					if( !$asset->store() )
					{
						$app->enQueueMessage( JText::_( JString::strtoupper( $class ) . '_MSG_PERMISSION_INSTALL_FAIL' ), 'warning' );
					}//end if
				}
				else
				{
					$app->enQueueMessage( JText::_( JString::strtoupper( $class ) . '_MSG_PERMISSION_INSTALL_FAIL' ), 'warning' );
				}//end if
			}
			else
			{
				$app->enQueueMessage( JText::_( JString::strtoupper( $class ) . '_MSG_PERMISSION_INSTALL_FAIL' ), 'warning' );
			}//end if
		}//end if
	}//end function
}//end class
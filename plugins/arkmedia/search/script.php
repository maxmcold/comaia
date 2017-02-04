<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

class plgArkMediaSearchInstallerScript
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
		// Fresh Install Only (to save destroying existing parameters)
		// Issue: Basically Fields With Defaults are Inserted into the DB but Fields Without Defaults Aren't So We'll Manually Insert Them
		if( $route === 'install' )
		{
			$manifest 		= $adapter->getParent()->getManifest();
			$plugingroup	= (string)$manifest->attributes()->group; // Get Group
			$pluginname 	= str_replace( array( 'plg', 'installerscript', $plugingroup ), '', JString::strtolower( __CLASS__ ) ); // Get Plugin Name from Class

			if( ( $manifest instanceof SimpleXMLElement ) && isset( $manifest->config->fields->fieldset ) )
			{
				// Load Plugin Up
				$plugin 	= JTable::getInstance( 'extension' );
				$pluginid 	= $plugin->find( array( 'type' => 'plugin', 'folder' => $plugingroup, 'element' => $pluginname ) );

				if( $pluginid )
				{
					$plugin->load( $pluginid );
					$plugin->params = new JRegistry( $plugin->params );
					$altered		= false;

					foreach( $manifest->config->fields->fieldset as $fieldset )
					{
						// Loop Parameters
						foreach( $fieldset as $field )
						{
							// If a Param Has No Default But Has Options Set Then Save These Options as Defaults to the Parameter for the Component to Access
							if( $field->attributes()->default === null && count( $field->children() ) )
							{
								$defaults = array();

								// Find Options & Set them to the Parameter
								foreach( $field->children() as $option )
								{
									$defaults[] = (string)$option->attributes()->value;
									$altered	= true;
								}//end foreach

								if( count( $defaults ) )
								{
									$plugin->params->set( $field->attributes()->name, $defaults );
								}//end if
							}//end if
						}//end foreach

						// If Changes Were Made Then Save Them
						if( $altered )
						{
							$plugin->params = $plugin->params->toString();
							$plugin->store();
						}//end of
					}//end foreach
				}//end if
			}//end if
		}//end if
	}//end function
}//end class
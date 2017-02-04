<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

class ArkMediaInstallerScript
{
	/**
	 * Method to install the package
	 *
	 * $adapter		(class)		The object responsible for running this script
	 */
	public function install( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to uninstall the package
	 *
	 * $adapter		(class)		The object responsible for running this script
	 */
	public function uninstall( JAdapterInstance $adapter )
	{
		// ON UNINSTALL EMPTY OUT THE FOLDERS READY FOR DELETION DUE
		// TO JOOMLA REFUSING TO DELETE NON-EMPTY DIRECTORIES.
		$manifest 	= $adapter->get( 'manifest' );
		$files		= $manifest->fileset->children();

		// GET FILES
		if( isset( $files ) )
		{
			// LOOP THROUGH THE ROOT FILES ELEMENTS LOOKING FOR FOLDERS
			foreach( $files as $item )
			{
				// GET THE TARGET LOCATION
				$target = (string)$item->attributes()->target;

				// IF THERE ARE FOLDERS PRESENT IN THE FILES ELEMENT
				if( $target && isset( $item->folder ) )
				{
					// LOOP THROUGH FOLDERS
					foreach( $item->folder as $folder )
					{
						// BUILD THE FOLDER LOCATION
						$location = JPath::clean( JPATH_ROOT . chr( 47 ) . $target . chr( 47 ) . $folder );

						// IF THE FOLDER EXISTS CLEAR IT OUT
						if( JFolder::exists( $location ) )
						{
							// GET ALL OF THE AVAILABLE DOCUMENTS
							$docs = JFolder::files( $location );

							// LOOP THROUGH THE DOCUMENTS
							foreach( $docs as $doc )
							{
								// DELETE EACH FILE TO ALLOW THE JOOMLA TO CORRECTLY PERFORM A FULL UNINSTALL
								JFile::delete( $location . chr( 47 ) . $doc );
							}//end foreach
						}//end if
					}//end foreach
				}//end if
			}//end foreach
		}//end if
	}//end function

	/**
	 * Method to update the package
	 *
	 * $adapter		(class)		The object responsible for running this script
	 */
	public function update( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * $route		(string)	Which action is happening (install || uninstall || discover_install)
	 * $adapter		(class)		The object responsible for running this script
	 */
	public function preflight( $route, JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * $route		(string)	Which action is happening (install || uninstall || discover_install)
	 * $adapter		(class)		The object responsible for running this script
	 */
	public function postflight( $route, JAdapterInstance $adapter )
	{

	}//end function
}//end class
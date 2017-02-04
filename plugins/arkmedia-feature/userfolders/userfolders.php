<?php
/**
 * @version     1.12.1
 * @package     com_arkhive
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperStack, Ark\Media\HelperFileSystem;

defined( '_JEXEC' ) or die();

class plgArkMediaFeatureUserFolders extends JPlugin
{
	/**
	 * @var		string	Plugin Name (for namespacing, config retrieval & more!)
	 */
	protected $name = 'userfolders';

	/**
	 * Constructor
	 */
	public function __construct( &$subject, $config )
	{
		// Load Language
		JFactory::getLanguage()->load( 'plg_' . ARKMEDIA_PLUGIN_FEATURE . '_' . $this->name, JPATH_ADMINISTRATOR );

		$this->base 	= ARKMEDIA_ROOT . ARKMEDIA_DS;
		$this->plugin 	= str_replace( $this->base, '', ARKMEDIA_PLUGIN_FEATURES . $this->name . ARKMEDIA_DS );
		$this->jtext 	= 'PLG_' . JString::strtoupper( ARKMEDIA_PLUGIN_FEATURE ) . '_' . JString::strtoupper( $this->name ) . '_';

		parent::__construct( $subject, $config );
	}//end function

	/**
	 * Catch Main Ark Stack Path Setter in Order to Add User Folder Restriction.
	 *
	 * @return	void
	 */
	public function onArkAfterStackPath()
	{
		// Are User Folders Active?
		if( !$this->params->get( 'active', true ) )
		{
			return;
		}//end if

		// Get User Data
		$app 		= JFactory::getApplication();
		$user 		= JFactory::getUser();
		$groups		= $user->getAuthorisedGroups();
		$whitelist 	= $this->params->get( 'whitelist', array() );
		$type		= $this->params->get( 'type', 'username' );
		$userfolder	= $this->params->get( 'path', 'users' ); // @todo	Plumb & Filter in this Parameter?

		// Super Users Are Automatically Exempt
		if( $user->get( 'isRoot' ) )
		{
			return;
		}//end if

		// User is Part of a Whitelisted Group
		if( $groups && count( $whitelist ) )
		{
			foreach( $groups as $groupid )
			{
				if( in_array( $groupid, $whitelist ) )
				{
					return;
				}//end if
			}//end foreach
		}//end if

		// Calculate Folder Name
		switch( $type )
		{
			case 'id' :
			case 'username' :
				$folder = $user->{ $type };
				break;

			default :
				// Thrown Error
				return $this->_error( JText::_( $this->jtext . 'MSG_NOTYPE_FAIL' ), true );
				break;
		}//end switch

		foreach( HelperStack::get() as $name => $stack )
		{
			// Well-Formed Stack
			// @note	There is No Ability to Opt-Out of Path Check (like title) as Path is Needed
			if( $stack->path )
			{
				// Build Path (stack path may already contain the user folder)
				// @note	$subpath is the Old Un-Grouped System, Whereas $grouppath
				// 			Is the New Grouped User Folder System.
				$path 		= $stack->path;
				$subpath 	= ARKMEDIA_DS . $folder;

				// First, Check User Folder Hasn't Already Been Added By Another User Folder Plugin.
				// Check the Subpath Against the Last Occurence of the User Folder in the Stack Path
				if( strrpos( $stack->path, $subpath ) !== ( strlen( $stack->path ) - strlen( $subpath ) ) )
				{
					// Make Fullpath
					$path 	.= $subpath;
				}//end if

				// Build Full Paths
				$fullpath	= $this->base . $path;

				// If the User Folder Doesn't Exist Update to New Sub Foldered User Folder System
				if( !JFolder::exists( $fullpath ) )
				{
					// Insert the Group Folder Before the User's Folder
					$grouppath	= ARKMEDIA_DS . $userfolder . $subpath;

					// Update Path & Fullpath
					$path 		= preg_replace( '#' . preg_quote( $subpath ) . '$#', $grouppath, $path );
					$fullpath	= $this->base . $path;
				}//end if

				// Create User Folder if it Doesn't Exist Yet
				if( !JFolder::exists( $fullpath ) )
				{
					if( !JFolder::create( $fullpath ) )
					{
						// Don't Give Sensitive Directory Information to Front-End People!
						if( $app->isAdmin() )
						{
							$message = JText::sprintf( $this->jtext . 'MSG_NOCREATEADMIN_FAIL', $fullpath );
						}
						else
						{
							$message = JText::sprintf( $this->jtext . 'MSG_NOCREATE_FAIL', $stack->title );
						}//end if

						// Non-Fatal So Continue With Other Stacks
						$this->_error( $message, $name );

						// Skip Stack
						continue;
					}//end if
				}//end if

				// Filter Path
				$newpath = HelperFileSystem::filter( $path, 'folder' );

				// Set New Path Data
				HelperStack::set( $name, 'path', $newpath );
				HelperStack::set( $name, 'segments', HelperFileSystem::split( $newpath ) );
			}//end if
		}//end foreach
	}//end function

	/**
	 * An Error Has Occured So Throw the Error Message & Sabotage the Stack Path.
	 * This Prevents Users From Defaulting Back to the Root Media Folder Locations.
	 * Otherwise User Folders Would be Circumvented.
	 *
	 * @param	string	$message	The Message to Throw
	 * @param	mixed	$stack		Name of the Stack to Clear or True to Clear All (omit to prevent path clearing)
	 *
	 * @return	void
	 */
	protected function _error( $message = '', $stack = null )
	{
		// Throw Error
		Helper::message( $message, 'error' );

		// Clear Stack Path to Show Blank Folder Rather than Default to Root Folder
		if( $stack )
		{
			// Clear All Stacks Or Just Provided Stack
			if( is_bool( $stack ) )
			{
				foreach( HelperStack::get() as $name => $stackdata )
				{
					HelperStack::set( $name, 'path', '' );
				}//end foreach
			}
			else
			{
				HelperStack::set( $stack, 'path', '' );
			}//end if
		}//end if
	}//end function
}//end class
<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

namespace Ark\Media;

// Add Joomla Classes
use JFactory;

defined( '_JEXEC' ) or die;

/**
 * Ark Media Manager Edit Mode Mediator
 *
 * @see 	Also Refer to fields/redirect.php Which Passes Edit Data to the Controller Via JForm.
 */
class HelperEdit
{
	/**
	 * @var		bool	Edit Mode Active Flag
	 */
	protected static $editmode 		= null;

	/**
	 * @var		bool	Edit Full Active Flag
	 */
	protected static $editfull 		= null;

	/**
	 * @var		bool	On-Page-Load Initialise Edit Mode Flag
	 */
	protected static $editwait		= null;

	/**
	 * @var		bool	Skip Edit Mode Straight to Insert
	 */
	protected static $editquick		= null;

	/**
	 * @var		bool	Render Edit Settings Panel (opt out)
	 */
	protected static $editsettings	= null;

	/**
	 * @var		bool	Whether to Disable Stack Lock (opt in)
	 */
	protected static $stacklock		= null;

	/**
	 * @var		bool	Was the Passed File a base64 Image?
	 */
	protected static $base64		= null;

	/**
	 * @var		string	Name of Editor that Initiated the Ark Media Manager
	 */
	protected static $editor 		= null;

	/**
	 * @var		string	ID of the Editor Field that Initiated the Ark Media Manager
	 */
	protected static $field 		= null;

	/**
	 * @var		string	Pipe Separated List of Stacks to Enable
	 */
	protected static $stacks		= null;

	/**
	 * @var		string	Name of Stack to Active on Load/that File Resides in
	 */
	protected static $stack			= null;

	/**
	 * @var		string	Path to Active on Load/that File Resides in
	 */
	protected static $path 			= null;

	/**
	 * @var		string	Name of the File that is Currently Being Edited (new file if blank)
	 */
	protected static $file 			= null;

	/**
	 * @var		string	Raw Value of static::$file Before it is Parsed
	 */
	protected static $edit 			= null;

	/**
	 * @var		string	Custom Callback Function to Look Call
	 */
	protected static $callback		= null;

	/**
	 * Check Whether the Component Has Been Initialised in Edit Mode or Plain Browse/Manage Mode With Edit Mode Disabled.
	 *
	 * @note	Quick Insert Mode, Although is in Edit Mode, Skips the Edit Screen.
	 * 
	 * @usage	HelperEdit::isEditMode();
	 *
	 * @return  bool 	Edit Mode Flag
	 */
	public static function isEditMode()
	{
		if( !isset( static::$editmode ) )
		{
			static::$editmode = true;
		}//end if

		return static::$editmode;
	}//end function

	/**
	 * Check Whether the Component Has Been Initialised in Edit Full Mode. Options are:
	 *
	 * - Edit Full		Insert & Edit Capabilities.
	 * - Edit (Simple)	Edit Only Capabilities, No Insert.
	 * - Edit Quick		No Insert or Edit (directly inserts back to parent window, but technically edit mode active).
	 *
	 * @usage	HelperEdit::isEditFull();
	 *
	 * @return  bool 	Edit Full Flag
	 */
	public static function isEditFull()
	{
		if( !isset( static::$editfull ) )
		{
			static::$editfull = JFactory::getApplication()->input->get( 'editor', false, 'bool' );
		}//end if

		return static::$editfull;
	}//end function

	/**
	 * Check Whether a Flag Exists Which Will Prevent the Opening of the Edit Mode onLoad.
	 * 
	 * @note 	Edit Mode Can Still be Active see: HelperEdit::isEditMode(); but if this Flag Exists & a File Option Exists,
	 * 			Do Not Open Straight to the File. This is Useful for Keeping Editor Reference to the Current File but Being Able to Remain in the List View.
	 *
	 * @usage	HelperEdit::isEditWait();
	 *
	 * @return  bool 	Edit Wait Flag
	 */
	public static function isEditWait()
	{
		if( !isset( static::$editwait ) )
		{
			static::$editwait = JFactory::getApplication()->input->get( 'editwait', false, 'bool' );
		}//end if

		return static::$editwait;
	}//end function

	/**
	 * Check Whether a Flag Exists Which Will Force the Edit Mode to Skip Straight to Insertion.
	 * This Allows for Selecting a File to "Quick" Insert Straight Into Your Content.
	 * 
	 * @note 	This Option Supersedes the Edit Wait Logic.
	 *
	 * @usage	HelperEdit::isEditQuick();
	 *
	 * @return  bool 	Edit Quick Flag
	 */
	public static function isEditQuick()
	{
		if( !isset( static::$editquick ) )
		{
			static::$editquick = JFactory::getApplication()->input->get( 'editquick', false, 'bool' );
		}//end if

		return static::$editquick;
	}//end function

	/**
	 * Check Whether a Flag Exists Which Will Inform the Edit Mode to Display the Settings/Attributes.
	 *
	 * @note	This is an Opt Out Method Because Most Scenarios With Active Edit Mode Also Want Edit Settings.
	 *
	 * @usage	HelperEdit::isEditSettings();
	 *
	 * @return  bool 	Edit Settings Flag
	 */
	public static function isEditSettings()
	{
		if( !isset( static::$editsettings ) )
		{
			static::$editsettings = JFactory::getApplication()->input->get( 'editsettings', true, 'bool' );
		}//end if

		return static::$editsettings;
	}//end function

	/**
	 * Whether Stack Lock is Disabled & Free Stack Switching is Enabled (opt in).
	 *
	 * @note	This Parameter is Currently Only Applicable When the Ark is in Edit Mode,
	 * 			As it is the Edit Class that Forces the Stack Lock in the 1st Place.
	 * 			Therefore if Edit Mode is Active then Force Stack Lock By Default,
	 * 			Otherwise Allow Free Stack Changing if No Edit Mode & No Specific Lock Setting.
	 *
	 * @usage	HelperEdit::isStackLock();
	 *
	 * @return  bool 	Lock Flag
	 */
	public static function isStackLock()
	{
		if( !isset( static::$stacklock ) )
		{
			static::$stacklock = JFactory::getApplication()->input->get( 'stacklock', static::isEditMode(), 'bool' );
		}//end if

		return static::$stacklock;
	}//end function

	/**
	 * Was the Passed File a base64 Image?
	 *
	 * @note	This Flag is Set in When Parsing the File in getFile().
	 *
	 * @usage	HelperEdit::isBase64();
	 *
	 * @return  bool 	Base64 Flag
	 */
	public static function isBase64()
	{
		if( !isset( static::$base64 ) )
		{
			static::getFile();
		}//end if

		return static::$base64;
	}//end function

	/**
	 * Get the Name of Editor that Initiated the Ark Media Manager.
	 *
	 * @usage	HelperEdit::getEditor();
	 *
	 * @return  string 	Name of Editor
	 */
	public static function getEditor()
	{
		if( !isset( static::$editor ) )
		{
			static::$editor = JFactory::getApplication()->input->get( 'editor', false, 'cmd' );
		}//end if

		return static::$editor;
	}//end function

	/**
	 * Get the ID of the Editor Field that Initiated the Ark Media Manager.
	 *
	 * @usage	HelperEdit::getField();
	 *
	 * @return  string 	ID of Editor
	 */
	public static function getField()
	{
		if( !isset( static::$field ) )
		{
			static::$field = JFactory::getApplication()->input->get( 'editorname', false, 'cmd' );
		}//end if

		return static::$field;
	}//end function

	/**
	 * Get the Pipe Separated List of Stacks to Enable.
	 * Either Not Set, URL Set as String; stack|another|more or Already as Array in AJAX
	 *
	 * @param	bool	$implode	Re-Collapse the Stack List to a String (opt in).
	 *
	 * @usage	HelperEdit::getStacks();
	 *
	 * @return  array 	Stack Names
	 */
	public static function getStacks( $implode = false )
	{
		$delimiter 				= '|';

		if( !isset( static::$stacks ) )
		{
			static::$stacks 	= JFactory::getApplication()->input->get( 'stacks', array(), 'array' );

			// String Set Through URL in Which Case Retrieve Result From JFilter's Array
			if( count( static::$stacks ) === 1 & strpos( current( static::$stacks ), $delimiter ) !== false )
			{
				static::$stacks = array_filter( explode( $delimiter, current( static::$stacks ) ) );
			}//end if
		}//end if

		return ( $implode ) ? implode( $delimiter, static::$stacks ) : static::$stacks;
	}//end function

	/**
	 * Get the Name of Stack to Active on Load/that File Resides in.
	 *
	 * @usage	HelperEdit::getStack();
	 *
	 * @return  string 	Stack Name
	 */
	public static function getStack()
	{
		if( !isset( static::$stack ) )
		{
			$app = JFactory::getApplication();

			static::$stack 		= $app->input->get( 'stack', false, 'cmd' );

			// Look in JForm
			if( !static::$stack )
			{
				$form 			= $app->input->get( 'jform', array(), 'array' );
				static::$stack 	= ( count( $form ) && isset( $form['stack'] ) ) ? $form['stack'] : false;
			}//end if
		}//end if

		return static::$stack;
	}//end function

	/**
	 * Get the Path to Active on Load/that File Resides in.
	 *
	 * @usage	HelperEdit::getPath();
	 *
	 * @return  string 	File Path
	 */
	public static function getPath()
	{
		if( !isset( static::$path ) )
		{
			// Retrieve URL Encoded Path (non encode path's are also supported)
			static::$path = HelperFileSystem::filter( urldecode( JFactory::getApplication()->input->get( 'path', false, 'string' ) ), 'folder', true );

			// Still No Path? Look in the File Name for the Path there
			if( !static::$path )
			{
				static::getFile();
			}//end if
		}//end if

		return static::$path;
	}//end function

	/**
	 * Get the Name of the File that is Currently Being Edited. 
	 * If in Edit Mode but no File is set then we are in New File Mode.
	 * If in Edit Mode & DO Have a File then on Load Skip Directly to the File.
	 *
	 * @usage	HelperEdit::getFile();
	 *
	 * @return  string 	Filename
	 */
	public static function getFile()
	{
		if( !isset( static::$file ) )
		{
			static::$file 	= urldecode( static::getEdit() );
			static::$base64	= false;
//var_dump(static::$file );
			// @see php.net/preg_quote for special characters list
			preg_match( '#data\:image/[a-zA-Z]{1,4};base64#i', static::$file, $matches );

			// If a base64 image clear the file to treat as a new/no file scenario
			// @todo	Determine where to throw a useful alert to tell users it's base64
			if( count( $matches ) )
			{
				static::$file 	= null;
				static::$base64 = true;
			}//end if

			// Check for a Path in the File name
			$index = strrpos( static::$file, ARKMEDIA_DS );

			// If the File Path was Passed in as well as the file name split the two up (this will override the $path parameter if set in the URL as well)
			if( $index !== false )
			{
				// Add the First Part as the Path
				static::$path = HelperFileSystem::filter( substr( static::$file, 0, $index ), 'folder', true );

				// Remove DS & Query Strings
				static::$file = current( explode( '?', substr( static::$file, $index + 1 ) ) );

				// Add the Last Part as the File Name (minus the Directory Separator)
				static::$file = HelperFileSystem::filter( static::$file, 'file', true );
			}//end if

			// Clear 0 Values From Being Converted to Strings
			if( !static::$file )
			{
				static::$file = false;
			}//end if
		}//end if

		return static::$file;
	}//end function

	/**
	 * Get the Name/Path of the File that is Currently Being Edited. The Data Is URL Decoded By getFile().
	 * 
	 * @note 	This is a Sibling Function to HelperEdit::getFile(); as They Both Handle the Same Parameter.
	 * 			But the Difference is that this Function Returns the Raw Value Whereas getFile(); Performs Path Parsing Checks.
	 *
	 * @usage	HelperEdit::getEdit();
	 *
	 * @return  string 	Raw Filename/Filepath
	 */
	public static function getEdit()
	{
		if( !isset( static::$edit ) )
		{
			static::$edit = JFactory::getApplication()->input->get( 'edit', false, 'string' );
		}//end if

		return static::$edit;
	}//end function

	/**
	 * Get the Custom Return/Callback Function for Inserting the Editable Item Into the Content.
	 *
	 * @param	string	$default	Default Function Name.
	 * 
	 * @usage	HelperEdit::getCallback();
	 *
	 * @return  string 	Callback Function Name
	 */
	public static function getCallback( $default = false )
	{
		if( !isset( static::$callback ) )
		{
			static::$callback = JFactory::getApplication()->input->get( 'callback', $default, 'cmd' );
		}//end if

		return static::$callback;
	}//end function
}//end class
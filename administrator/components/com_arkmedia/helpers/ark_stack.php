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
use JDispatcher, JRegistry;

// Add PHP Classes
use stdClass, Exception;

defined( '_JEXEC' ) or die;

// Get FileSystem
Helper::add( 'helper', 'filesystem' );

/**
 * Stack Cache & FileSystem Mediator
 */
class HelperStack
{
	/**
	 * @var		object	The Cache Object to Hold the Stack Information
	 */
	protected static $cache 		= null;

	/**
	 * @var		string	The Cache ID That Links to the Stack's Storage Location
	 */
	protected static $cacheid 		= null;

	/**
	 * @var		string	The Cache Group Name to Localise the Stack Information
	 */
	protected static $cachegroup 	= ARKMEDIA_CACHE_STACKS;

	/**
	 * @var		bool	Keep Track of Whether we are Loading from Scratch or from Cache (aka was the cache recently refreshed?)
	 */
	protected static $refreshed 	= null;

	/**
	 * @var		object	JDispatcher/JEvent Handler
	 */
	protected static $event 		= null;

	/**
	 * @var		array	Array Containing All Stack Information ( e.g. array( 'stack1' => object( params, path, items, root ), 'stack2' => object() etc... ) ) 
	 */
	protected static $stacks 		= array();

	/**
	 * @var		array	Internal Array Containing Information Held for Stack Functions (namespaced by method)
	 */
	protected static $loaded 		= array();

	/**
	 * Retrieve & Load All Available Stacks.
	 *
	 * @note	There is No Longer a Secenrio Where $populate is Used as we Favour Loading a Blank, Root Item Set
	 * 			As Opposed to Fully Loading the Root Items. Before This We Competely Traversed the Tree for All Items.
	 *
	 * @param	bool	$reload		Reload Cache/Re-Search Stack Before Returning
	 * @param	bool	$populate	Fill the Stacks With Their Root Items
	 * @param	bool	$simple		When Loading the Root Items, Should They be Loaded or Unloaded (simple)
	 *
	 * @return	void
	 */
	public static function load( $reload = false, $populate = true, $simple = false )
	{
		// Register Class Data
		static::setup();

		// Clear Cache to Force Re-Generation
		if( $reload )
		{
			static::refresh( false );
		}//end if

		// Register Stacks
		static::register();

		// Register Stack Locations/Paths
		static::paths();

		// Load Folders & Files into All Stacks (invert $simple option to convert to $load)
		if( $populate )
		{
			static::loadStacks( !$simple );
		}//end if
	}//end function

	/**
	 * Register Class Data.
	 *
	 * @return	void
	 */
	public static function setup()
	{
		// Aquire Cache if Not Already Found
		if( !static::$cacheid )
		{
			static::$cache 		= Helper::cache();
			static::$cacheid	= md5( 'ark.stack' );
		}//end if

		// Set Refresh Indicator if Cache was Empty
		static::$refreshed		= ( !static::$cache->get( static::$cacheid, static::$cachegroup ) ) ? true : false;
	}//end function

	/**
	 * Register All Available Stacks.
	 *
	 * @see		Stack Objects Consistency:
	 * 
	 * @object	string	Title		The Stacks User Friendly Name.
	 * @object	object	Params		Stack Set-up & Display Options in Registry Format.
	 * @object	array	Items		Stack Directory Items if Loaded in.
	 * @object	string	Path		The Stack's Root Path.
	 * @object	object	Segments	The Breakdown of the Stack's Root Path.
	 *
	 * @return	void
	 */
	public static function register()
	{
		// Prevent Multiple Calls to the Plugins
		if( isset( static::$loaded[__METHOD__] ) )
		{
			return;
		}//end if

		// Get Event Data
		static::$event 	= JDispatcher::getInstance();
		$titles			= array();
		$names			= array();
		$params			= array();

		// Register Plugins
		static::$event->trigger( 'onArkBeforeStackRegister', array( &$titles, &$names, &$params ) );

		// Add Stack Items & Parameters
		for( $i = 0; $i < count( (array)$names ); $i++ )
		{
			// Check to See if Stack is Active
			if( static::isActive( $names[$i], $params[$i] ) )
			{
				static::$stacks[$names[$i]] = new stdClass;
				static::set( $names[$i], 'title', $titles[$i] );
				static::set( $names[$i], 'params', $params[$i] );
			}//end if
		}//end foreach

		// Set Static Flag
		static::$loaded[__METHOD__] = true;

		static::$event->trigger( 'onArkAfterStackRegister', array( array_keys( static::$stacks ) ) );
	}//end function

	/**
	 * Check Whether a Stack is Currently Active.
	 * 
	 * - If a Whitelist is Provided Then Filter Stack Out
	 * - If No Whitelist is Set Then Approve Stack
	 * - If Stack Has Forced it's Registration Then Ignore Whitelist & Approve
	 *
	 * @note	This Process is Kept Separate From the Registration Because There Are Events
	 * 			That Execute Before Stack Registration That Need to Check Stack Activity.
	 * 			This Allows For State Checks Without Any Further Stack Registration.
	 *
	 * @param	string	$stack		The Stack Name to Check
	 * @param	object	$params		Registry Object of Stack Parameters
	 *
	 * @return	void
	 */
	public static function isActive( $stack = null, JRegistry $params = null )
	{
		if( !$stack || !$params )
		{
			return false;
		}//end if

		Helper::add( 'helper', 'edit' );

		// Get Whitelist Options
		$whitelist 		= HelperEdit::getStacks();
		$usewhitelist 	= (bool)count( $whitelist );

		// Check Stack
		if( !$usewhitelist || ( $usewhitelist && in_array( $stack, $whitelist ) ) || $params->get( 'register-force-registry', false ) )
		{
			return true;
		}//end if

		return false;
	}//end function

	/**
	 * Once Stacks Have Been Registered, Find Their Locations/Paths.
	 *
	 * @note 	Checks for a Valid/Existential Folder/Path are Not Performed Here 
	 * 			To Allow the User to see the Invalid Path,
	 * 			Otherwise Clearing the 'path' if Invalid Won't Give the User Any Clues.
	 *
	 * @event	onArkBeforeStackPath
	 * @event	onArkAfterStackPath
	 *
	 * @return	void
	 */
	public static function paths()
	{
		static::$event->trigger( 'onArkBeforeStackPath' );

		$parameter	= 'folder-locations';
		$folders 	= (object)Helper::params( $parameter );

		foreach( static::get() as $name => $stack )
		{
			// Get Path (From Component First then Fallback to Plugin if Not Set)
			// Strip DS & Spaces Here to Prevent Extra Space & DS's Added by User in Config (strip all DS types aka: '/', '\')
			$path		= ( isset( $folders->$name ) && $folders->$name ) ? HelperFileSystem::trim( $folders->$name ) : $stack->params->get( $parameter );
			// Make Path Safe & Server OS Compatible
			$path		= HelperFileSystem::filter( $path, 'folder' );
			// Also Store the Base Path & the Folder Name
			$segments	= HelperFileSystem::split( $path );

			// Set Initial Path
			static::set( $name, 'path', $path );
			static::set( $name, 'segments', $segments );
		}//end foreach

		static::$event->trigger( 'onArkAfterStackPath' );
	}//end function

	/**
	 * Once Stacks Have Been Registered, Load all Available Folders & Files into Them.
	 * 
	 * @param	string	$load		Load the Stack's Items or Just Plant the Base Stack Path Entry?
	 *
	 * @event	onArkBeforeStackLoad
	 * @event	onArkAfterStackLoad
	 *
	 * @return	void
	 */
	public static function loadStacks( $load = true )
	{
		static::$event->trigger( 'onArkBeforeStackLoad' );

		// If Stacks Have Already Been Loaded Return Cache Version
		if( static::$cache->get( static::$cacheid, static::$cachegroup ) )
		{
			// Make Sure Cache is Set to Class Object
			static::$stacks = static::$cache->get( static::$cacheid, static::$cachegroup );

			return;
		}//end if

		// Load Stacks Into Class Object
		foreach( static::get() as $name => $stack )
		{
			static::_loadStack( $name, $load );
		}//end foreach

		// Load Entire Class Stack Object Into Cache
		static::$cache->store( static::get(), static::$cacheid, static::$cachegroup );

		static::$event->trigger( 'onArkAfterStackLoad' );
	}//end function

	/**
	 * Load a Stack's Folders & Files.
	 *
	 * @param	string	$name		The Stack Name to Set
	 * @param	string	$load		Load the Stack's Items or Just Plant the Base Stack Path Entry?
	 *
	 * @return	void
	 */
	protected static function _loadStack( $name, $load )
	{
		$stack = static::get( $name );

		if( $stack->path && $load )
		{
			Helper::add( 'helper', 'edit' );

			$types 			= static::getStackFileTypes( $name );
			$active_stack 	= HelperEdit::getStack();
			$active_path 	= HelperEdit::getPath();

			// Load the Root set of Folders & Files
			HelperFileSystem::load( HelperFileSystem::makePath( $stack->path ), array( 'stack' => $name, 'extfilter' => $types->ext, 'recurse' => false ) );

			// Retrieve the set of Folders & Files
			static::set( $name, 'items', HelperFileSystem::retrieve() );

			// Load the Active Path (otherwise root will be loaded & ajax will load active path & leave the folders in the middle empty)
			if( $active_stack === $name && $active_path )
			{
				// Prevent Loading the Same Data if the Active Path is the Same as the Stack Path
				// Also Check The Active Path Isn't Outside of the Stack to Prevent User's Loading Inaccessible Folders Through the URL
				if( $active_path !== $stack->path && static::find( $name, $active_path ) )
				{
					static::_loadBranch( $name, $active_path );
				}//end if
			}//end if
		}
		elseif( $stack->path && !$load )
		{
			// Set Up Empty Root Folder for Full Ajax Loading Later
			$root = array( $stack->path => HelperFileSystem::template( 'folder', $stack->segments->name ) );

			static::set( $name, 'items', $root );
		}
		else
		{
			// Failed So Set Blank Entry
			static::set( $name, 'items', null );
		}//end if
	}//end function

	/**
	 * Load a Direct Branch to a Stack.
	 *
	 * @param	string	$name		The Stack Name to Set
	 * @param	string	$path		The Stack Path to Load Up (already filtered & normalised by HelperEdit)
	 *
	 * @return	void
	 */
	protected static function _loadBranch( $name, $path )
	{
		$stack = static::get( $name );
		$types = static::getStackFileTypes( $name );
		$parts = explode( ARKMEDIA_DS, $path );

		while( count( $parts ) )
		{
			$childpath = implode( ARKMEDIA_DS, $parts );

			// If The Folder Doesn't Exist or The Record Hasn't Been Loaded Yet
			if( !isset( $stack->items[$childpath] ) || !$stack->items[$childpath]->loaded )
			{
				// Load the Child Path's set of Folders & Files
				HelperFileSystem::load( HelperFileSystem::makePath( $childpath ), array( 'stack' => $name, 'extfilter' => $types->ext, 'recurse' => false ) );

				// Retrieve Folder & Child Folders
				$folders = HelperFileSystem::retrieve();

				// Loop Folders
				// @note	May Contain:
				// 				1. The Loaded Folder ($childpath) & It's Files,
				// 				2. The Loaded Folder's Child Folders (in bare form without their files),
				// 				3. One of These Bare Child Folder's May Be A Previous While Iteration Which was Fully Loaded So Skip.
				foreach( $folders as $folderpath => $folderdata )
				{
					// Because We Are Reverse Looping The Branch This Folder May Have Already Been Added
					if( !isset( $stack->items[$folderpath] ) || !$stack->items[$folderpath]->loaded )
					{
						$stack->items[$folderpath] = $folderdata;
					}//end if
				}//end foreach
			}//end if

			// Clip the End Off of the Parts
			array_pop( $parts );
		}//end while
	}//end function

	/**
	 * Get a Stack or All Current Stacks.
	 *
	 * @param	string  $stack		The Stack Name to Return (Default : All Stacks)
	 *
	 * @return	array	Array of Stacks
	 */
	public static function get( $stack = null )
	{
		return ( !is_null( $stack ) && array_key_exists( $stack, static::$stacks ) ) ? static::$stacks[$stack] : static::$stacks;
	}//end function

	/**
	 * Add/Set an Item to a Stack's Root.
	 *
	 * @param	string	$stack		The Stack Name to Set
	 * @param	string	$option		The Option Key to Set
	 * @param	string	$value		The Value to Set to the Stack
	 *
	 * @return	bool	Set Status
	 */
	public static function set( $stack = null, $option = null, $value = null )
	{
		if( !$stack || !$option )
		{
			return false;
		}//end if

		return static::$stacks[$stack]->$option = $value;
	}//end function

	/**
	 * Set a Stack's Parameter Option.
	 *
	 * @param	string	$stack		The Stack Name to Set
	 * @param	string	$option		The Parameter Option to Set
	 * @param	string	$value		The Value to Set to the Parameter
	 *
	 * @return	bool	Set Status
	 */
	public static function setOption( $stack = null, $option = null, $value = null )
	{
		if( !$stack || !$option || !array_key_exists( $stack, static::get() ) )
		{
			return false;
		}//end if

		return static::$stacks[$stack]->params->set( $option, $value );
	}//end function

	/**
	 * Search Stacks for a Folder/File.
	 *
	 * @param	string	$folder		The Folder to Return From the Stack
	 * @param	string	$file		The File to Return From the Folder/Stack
	 * @param	string	$stack		The Stack Name to Search (Default : All Stacks)
	 *
	 * @todo 	Statically Cache this Call Based on the Function Arguments
	 *
	 * @return	array	Folder/File
	 */
	public static function search( $folder = null, $file = null, $stack = null )
	{
		// If Search is Restricted to a Stack, the Stack Object is Returned Rather than an Array of Stacks
		$stacks = static::get( $stack );
		$stacks = ( !is_array( $stacks ) ) ? array( $stacks ) : $stacks;

		foreach( $stacks as $stackobj )
		{
			if( array_key_exists( $folder, $stackobj->items ) )
			{
				if( is_null( $file ) || !array_key_exists( $file, $stackobj->items[$folder]->items ) )
				{
					return $stackobj->items[$folder];
				}
				else
				{
					return $stackobj->items[$folder]->items[$file];
				}//end if
			}//end if
		}//end foreach

		return false;
	}//end function

	/**
	 * Search for a Folder/File Using the Stack Path. Unlike the search() this Doesn't Traverse the Stack Tree
	 * Looking for the Item, Rather it Checks the Actual Path Exists & Whether it Belongs to the Stack.
	 *
	 * An Opt-Out Check Also Determines Whether the Item Actually Exists. Opting Out of this Check is
	 * Useful For Predicting if an Item Doesn't Yet Exist But is in a Valid Stack/Path Location.
	 *
	 * @param	string	$stack		The Stack Name to Search
	 * @param	string	$folder		The Folder to Return From the Stack
	 * @param	string	$file		The File to Find (if empty only the folder will be search)
	 * @param	string	$exists		Also Check Whether the Path Exists
	 *
	 * @return	bool	Does the Directory Item Exist or is it Valid?
	 */
	public static function find( $stack = null, $folder = null, $file = null, $exists = true )
	{
		$key = md5( serialize( func_get_args() ) );

		// Return Static Version or Set it up if Not in Existence Yet
		if( isset( static::$loaded[__METHOD__] ) && isset( static::$loaded[__METHOD__][$key] ) )
		{
			return static::$loaded[__METHOD__][$key];
		}
		elseif( !isset( static::$loaded[__METHOD__] ) )
		{
			static::$loaded[__METHOD__] = array();
		}//end if

		// Get Stack
		$folder		= HelperFileSystem::normalise( $folder );
		$stackdata 	= static::get( $stack );
		$type		= ( $file ) ? 'file' : 'folder';
		$basepath	= HelperFileSystem::makePath( $folder );
		$path		= ( $file ) ? $basepath . ARKMEDIA_DS . $file : $basepath;

		// Validate Stack
		if( is_object( $stackdata ) )
		{
			// Is the Path the Exact Stack's Path or Does Path Belong to the Stack (relative)?
			if( ( $stackdata->path === $folder || preg_match( '#^' . preg_quote( $stackdata->path . ARKMEDIA_DS ) . '#', $folder ) ) )
			{
				// Does the Item Exist? Or Do We Not Care Because the Root Location is Valid?
				if( $exists && HelperFileSystem::exists( $path, $type ) )
				{
					static::$loaded[__METHOD__][$key] = true;
				}
				elseif( !$exists )
				{
					static::$loaded[__METHOD__][$key] = true;
				}
				else
				{
					static::$loaded[__METHOD__][$key] = false;
				}//end if
			}
			else
			{
				static::$loaded[__METHOD__][$key] = false;
			}//end if
		}
		else
		{
			static::$loaded[__METHOD__][$key] = false;
		}//end if

		return static::$loaded[__METHOD__][$key];
	}//end function

	/**
	 * Return Stacks as a JSON String.
	 *
	 * @note 	To Check Validity Use: return ( json_last_error() == JSON_ERROR_NONE );
	 * @note 	JSON_FORCE_OBJECT is Used to Ensure Blank Item Arrays are Converted to Objects
	 * 			To Avoid Confusing the FE JS Observer When Adding New Items.
	 *
	 * @return	string	JSON String
	 */
	public static function toString()
	{
		return json_encode( static::get(), JSON_FORCE_OBJECT );
	}//end function

	/**
	 * Clear Out All Stack Cache.
	 *
	 * @param	bool	$external	Was this Call/Refresh Initiated Internally or Externally?
	 *
	 * @return	void
	 */
	public static function refresh( $external = true )
	{
		// Register Class Data
		static::setup();

		// Clear Cache
		static::$cache->clean( static::$cachegroup );

		// Reset Cache Indicator 
		// (if called externally as internal clearing happens every page load)
		if( $external )
		{
			static::$refreshed = true;
		}//end if
	}//end function

	/**
	 * Return the Refreshed Status Indicator.
	 *
	 * @return	bool 	Was the Cache Recently Refreshed?
	 */
	public static function refreshed()
	{
		return static::$refreshed;
	}//end function

	/**
	 * Plant the Root Stack Folder in the FS if it Doesn't Exist.
	 * 
	 * @param	string	$name		The Desired Stack Name to Plant.
	 *
	 * @return	bool	Success Status
	 */
	public static function plant( $name = null )
	{
		// Load Up Stack Data
		static::load( true, false );

		$stack 	= static::get( $name );
		$result = false;

		// Valid Stack?
		if( $stack && isset( $stack->path ) && isset( $stack->segments->name ) )
		{
			// Do We Need to Create a FS Reference (can't check stack items because load always adds the root folder)
			if( !HelperFileSystem::exists( HelperFileSystem::makePath( $stack->path ), 'folder' ) )
			{
				try
				{
					$result = HelperFileSystem::makeDir( $stack->segments->base, $stack->segments->name );
				}
				catch( Exception $e )
				{
					// Already False
				}//end try
			}
			else
			{
				// Already Present
				$result = true;
			}//end if
		}//end if

		return $result;
	}//end function

	/**
	 * Get a Stack's Allowed File Types.
	 *
	 * @param	string	$stack		The Stack Name to Set
	 *
	 * @return	object	Object of File Types as Extensions & Mimes
	 */
	public static function getStackFileTypes( $stack = null )
	{
		// Return Static Version or Set it up if Not in Existence Yet
		if( isset( static::$loaded[__METHOD__] ) && isset( static::$loaded[__METHOD__]->{ $stack } ) )
		{
			return static::$loaded[__METHOD__]->{ $stack };
		}
		elseif( !isset( static::$loaded[__METHOD__] ) )
		{
			static::$loaded[__METHOD__] = new stdClass;
		}//end if

		// Set 'Struct'
		$legal 		= (object)array( 'ext' => array(), 'mime' => array() );

		if( !$stack || !array_key_exists( $stack, static::get() ) )
		{
			return $legal;
		}//end if

		$params		= Helper::params();
		$types 		= (array)$params->get( 'allowed-types' );
		$customs	= (array)$params->get( 'custom-allowed-types', array() );

		// Loop Through Known Types (if stack appears in types array) & Build List
		if( isset( $types[$stack] ) )
		{
			foreach( $types[$stack] as $type )
			{
				// Mime Type & Extension Type is Stored in the Param ("image/png,png" || "application/x-zip,[zip,tar,7z]")
				$type = explode( ',', $type, 2 );
				$exts = explode( ',', str_replace( array( '[', ']' ), '', $type[1] ) ); // Extensions Can Be an Array

				// Add Both Upper & Lower Case Versions (should already be lowercase but convert just in case)
				// @note	This Library is Case Insensitive But Part of Joomla's Media Helper Isn't So This Logic Compensates.
				$lower = array_map( function( $ext ){ return strtolower( $ext ); }, $exts );
				$upper = array_map( function( $ext ){ return strtoupper( $ext ); }, $exts );

				// Store Allowed Types
				$legal->mime[] 	= $type[0];
				$legal->ext 	= array_merge( $legal->ext, $lower, $upper );
			}//end foreach
		}//end if

		// Add Custom Types (this parameter comes as an array of parameters, which in turn are JSON strings)
		foreach( $customs as $custom )
		{
			if( $custom )
			{
				// Convert Sub-Parameter
				$custom = json_decode( $custom );

				// Each Sub-Parameter is an Array of Options Which Pair up to a Sibling Parameter (pivot style)
				for( $i = 0; $i < count( $custom->{ 'custom-allowed-types-ext' } ); $i++ )
				{
					// Add Custom Type (if required fields are present & allowed - as it is possible to submit empty values in the config, e.g. blank folder field returns string not array)
					if( $custom->{ 'custom-allowed-types-mime' }[$i] && $custom->{ 'custom-allowed-types-ext' }[$i] && is_array( $custom->{ 'custom-allowed-types-folder' }[$i] ) && in_array( $stack, $custom->{ 'custom-allowed-types-folder' }[$i] ) )
					{
						// Add Both Upper & Lower Case Versions
						$lower = strtolower( $custom->{ 'custom-allowed-types-ext' }[$i] );
						$upper = strtoupper( $custom->{ 'custom-allowed-types-ext' }[$i] );

						$legal->mime[] 	= $custom->{ 'custom-allowed-types-mime' }[$i];
						$legal->ext[] 	= $lower;
						$legal->ext[] 	= $upper;
					}//end if
				}//end for
			}//end if
		}//end foreach

		// Set Static Array
		static::$loaded[__METHOD__]->{ $stack } = $legal;

		return static::$loaded[__METHOD__]->{ $stack };
	}//end function

	/**
	 * Make the Folder/File Name JS Safe for Use in Dot Separated Paths
	 *
	 * @note	The JS Framework Accesses Data Like: folder.items.filename,
	 *			So if We Have: images.items.image.png,
	 *			The File Extension is Interpretted as Another Level Deep.
	 *			images.items['image.png'] Prevents the Data From Remaining Observable,
	 *			So we Are Forced to Have the Key as Slightly Different from the Actual Name.
	 *
	 * @see		[#2302]
	 *
	 * @param	string	$name		The Directory Item Name
	 *
	 * @return	string 	The Directory Item Name
	 */
	public static function getItemKey( $name = null )
	{
		return str_replace( '.', '', $name );
	}//end function
}//end class
<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperStack, Ark\Media\HelperFileSystem, Ark\Media\HelperImage;

defined( '_JEXEC' ) or die;

class ArkMediaModelList extends JModelList
{
	/**
	 * @var		object	JApplication Instance
	 */
	protected $app;

	/**
	 * @var		object	JEvents Class
	 */
	protected $event;

	/**
	 * @var		array	Array Containing Loaded Instances of JForm.
	 */
	protected static $forms 	= array();

	/**
	 * @var		object	JRegistry Object of Component Parameters.
	 */
	protected static $options 	= null;

	/**
	 * Constructor.
	 *
	 * @param	array	An Optional Associative Array of Configuration Settings.
	 *
	 * @return	void
	*/
	public function __construct( $config = array() )
	{
		$this->app 		= JFactory::getApplication();
		$this->event 	= JDispatcher::getInstance();

		parent::__construct( $config );
	}//end function

	/**
	 * Build an Array of Folder & File Stacks.
	 *
	 * @param	bool	$reload		Clear Cache Before Loading
	 *
	 * @return	array	Folder & File Stacks
	 */
	public function getItems( $reload = true )
	{
		// Load Simple File Structure
		HelperStack::load( $reload, true, true );

		// If Cache Was Previously Cleared Set Internal Indicator for Client Cache to be Cleared as well
		// Note - Reloading Cache When Calling the load( $reload = true ) Does Not Count Towards this Indicator
		if( HelperStack::refreshed() )
		{
			$this->app->input->set( 'refresh', true );
		}//end if

		// Return Stacks
		return HelperStack::get();
	}//end function

	/**
	 * Load the Folder & File Stacks as a JSON String.
	 *
	 * @return	string	JSON of Folders & Files
	 */
	public function getJSON()
	{
		return HelperStack::toString();
	}//end function

	/**
	 * Get a List Form File.
	 *
	 * @param	string	$name		The Name of the Form to Retrieve.
	 * @param	bool	$loadData	Whether the Form is to Load in it's Own Data (opt in).
	 * @param	mixed	$namespace	Whether to Namespace Each Form Field With the Stack Name, or String Name of XML Element to Apply Stack to (true|fieldset|field) (opt in).
	 *
	 * @return	JForm	A JForm object on success, false on failure
	 */
	public function getForm( $name = 'list', $loadData = false, $namespace = false )
	{
		// Store Forms Statically [#1187]
		if( !isset( static::$forms[$name] ) )
		{
			$this->event->trigger( 'onArkBeforeForm', array( &$name, &$loadData, &$namespace ) );

			static::$forms[$name] = $this->loadForm( ARKMEDIA_COMPONENT . '.' . $name, $name, array( 'control' => 'jform', 'load_data' => $loadData ) );

			$this->event->trigger( 'onArkAfterForm', array( &$name, &static::$forms[$name], &$namespace ) );
		}//end if

		return ( !empty( static::$forms[$name] ) ) ? static::$forms[$name] : false;
	}//end function

	/**
	 * Build a JForm Object of Toolbar Items & Options.
	 *
	 * @return	object	JForm & Toolbar Display Options
	 */
	public function getToolbarOptions()
	{
		$opts				= new stdClass;
		$opts->form 		= $this->getForm( 'toolbar' );
		$opts->options		= $this->getOptions();

		return $opts;
	}//end function

	/**
	 * Build a JForm Object of Navigation Items & Options.
	 *
	 * @return	object	JForm & Navigation Display Options
	 */
	public function getNavigationOptions()
	{
		$opts				= new stdClass;
		$opts->form 		= $this->getForm( 'navigation' );
		$opts->options		= $this->getOptions();

		return $opts;
	}//end function

	/**
	 * Build an Array of Explorer Display Options.
	 *
	 * @return	object	Explorer Display Options
	 */
	public function getExplorerOptions()
	{
		return $this->getOptions();
	}//end function

	/**
	 * Build an Array of Breadcrumb Display Options.
	 *
	 * @return	object	Breadcrumb Display Options
	 */
	public function getBreadcrumbOptions()
	{
		return $this->getOptions();
	}//end function

	/**
	 * Build a JForm Object of Helpbar Items & Options.
	 *
	 * @return	object	JForm & Helpbar Display Options
	 */
	public function getHelpbarOptions()
	{
		$opts				= new stdClass;
		$opts->form 		= $this->getForm( 'helpbar' );
		$opts->options		= $this->getOptions();

		return $opts;
	}//end function

	/**
	 * Build a JForm Object of Editbar Items & Options.
	 *
	 * @return	object	JForm & Editbar Display Options
	 */
	public function getEditbarOptions()
	{
		$opts				= new stdClass;
		$opts->form 		= $this->getForm( 'editbar' );
		$opts->options		= $this->getOptions();

		return $opts;
	}//end function

	/**
	 * Build a JForm Object of Action Items & Options.
	 *
	 * @return	object	JForm & Action Display Options
	 */
	public function getActionOptions()
	{
		$opts				= new stdClass;
		$opts->form 		= $this->getForm( 'actions' );
		$opts->options		= $this->getOptions();

		return $opts;
	}//end function

	/**
	 * Build a JForm Object of Sub Action Items & Options.
	 *
	 * @return	object	JForm & Action Display Options
	 */
	public function getSubActionOptions()
	{
		$opts				= new stdClass;
		$opts->form 		= $this->getForm( 'subactions' );
		$opts->options		= $this->getOptions();

		return $opts;
	}//end function

	/**
	 * Build an Array of Content Display Options.
	 *
	 * @return	object	Content Display Options
	 */
	public function getContentOptions()
	{
		// Search for All Available Views 
		// ------------------------------
		// We Currently Only Search Component Layouts
		// Because we Can't get Access to JLayoutFile's $includePaths
		// Which Holds All the Override Paths Which we Could Search as Well :(
		$options			= array( 'views' => array() );
		$views 				= JFolder::folders( ARKMEDIA_LAYOUTS . 'list' . ARKMEDIA_DS . 'content' );
		$active				= (array)static::$options->get( 'view-list' ); // Default/Blank Returns String

		// Check Type as JFolder Can Return false on Error
		if( is_array( $views ) )
		{
			foreach( $views as $view )
			{
				// If it is Enabled and Has a Basic Stack Allow it to be Added
				if( in_array( $view, $active ) && JFile::exists( ARKMEDIA_LAYOUTS . 'list' . ARKMEDIA_DS . 'content' . ARKMEDIA_DS . $view . ARKMEDIA_DS . 'stack.php' ) )
				{
					$options['views'][] = $view;
				}//end if
			}//end foreach
		}//end if

		// Merge Options
		$opts 				= $this->getOptions();
		$opts->merge( new JRegistry( $options ) );

		return $opts;
	}//end function

	/**
	 * Grab the Components Parameters Once For Layouts to Use.
	 * The Views Parameter is Analysed Here as it Needs to Happen Quite Early on 
	 * Due to Few Different Classes Relying on this Parameter.
	 *
	 * @note	Apart From static::getContentOptions(); Only the Actions Form's Collapse Button
	 * 			Has Special Awareness of the View List Parameter.
	 *
	 * @todo 	Depreciate Need for Multiple Cloned Option Instances in Layout Functions
	 *
	 * @return	object	JRegistry Object of Component Parameters
	 */
	public function getOptions()
	{
		if( !isset( static::$options ) )
		{
			static::$options = Helper::params();

			// Search the List of Active Views for Our Default
			$view	= static::$options->get( 'view-default' );
			$views	= (array)static::$options->get( 'view-list' ); // Default/Blank Returns String
			$key	= array_search( $view, $views );

			// Re-Assign Default View if Not Enabled (false = not set, 0-2 = array key)
			if( $key === false )
			{
				// Re-Assign New Default
				static::$options->set( 'view-default', current( $views ) );
			}//end if
		}//end if

		return clone static::$options;
	}//end function

	/**
	 * Take the Passed Thumbnail Path & Stream it to the Browser.
	 *
	 * @param	string	$path		The Thumbnail Cache Path
	 *
	 * @return	void
	 */
	public function streamThumbnail( $path = null )
	{
		// Get Image Mediator
		Helper::add( 'helper', 'image' );

		// Stream the Thumbnail
		HelperImage::stream( $path );
	}//end function

	/**
	 * Find an Image to Render by Looking for Cache Version or Creating a Thumbnail & Then Caching it.
	 *
	 * @param	string	$stack		The Stack the File Resides in.
	 * @param	string	$path		The Path to the File
	 * @param	string	$file		The File Name to Locate
	 * @param	string	$size		The Thumbnail Square Size With Option Resize Constant ID (e.g. 100x100 || 100x100x7)
	 * @param	mixed	$extra		Extra Thumbnail Data, Mainly For Interception Events
	 *
	 * @return	string	Thumbnail Image Location
	 */
	public function getThumbnail( $stack = null, $path = null, $filename = null, $size = null, $extra = null )
	{
		$result = false;

		$this->event->trigger( 'onArkBeforeThumbnail', array( &$stack, &$path, &$filename, &$size, &$extra, &$result ) );

		// Allow Total Event Interception 
		if( $result )
		{
			return $result;
		}//end if

		if( $stack && $path && $filename )
		{
			// Load Basic Stack From Cache
			HelperStack::load( false, false );

			// Validate Stack & Path
			if( HelperStack::find( $stack, $path, $filename ) )
			{
				// Get Image Mediator
				Helper::add( 'helper', 'image' );

				// Load in the Image for Manipulating
				$image = HelperImage::load( $filename, $path );

				// Render the Image Thumbnail
				if( $image )
				{
					$chunks 	= explode( 'x', $size );

					// Look for Compression Ratio in 3rd Parameter
					if( count( $chunks ) == 3 )
					{
						$thumb 	= HelperImage::thumbnail( $chunks[0] . 'x' . $chunks[1], $chunks[2] );
					}
					else
					{
						$thumb 	= HelperImage::thumbnail( $size );
					}//end if

					// Successful Thumbnail Creation
					if( $thumb )
					{
						$result = $thumb->file;
					}//end if
				}//end if
			}//end if
		}//end if

		$this->event->trigger( 'onArkAfterThumbnail', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Find an Icon to Render by for the File by Looking.
	 *
	 * @note	This Method is Currently No Longer in Use as FileSystem Now Handles Icons.
	 *
	 * @param	string	$stack		The Stack the File Resides in.
	 * @param	string	$path		The Path to the File
	 * @param	string	$file		The File Name to Locate
	 * @param	string	$format		The Return Type (just class name or DOM element?), (Default : Element)
	 * @param	mixed	$extra		Extra Thumbnail Data, Mainly For Interception Events
	 *
	 * @return	string	Icon Class Name/Element
	 */
	public function getMimeIcon( $stack = null, $path = null, $filename = null, $format = null, $extra = null )
	{
		$result = false;

		$this->event->trigger( 'onArkBeforeIcon', array( &$stack,  &$path, &$filename, &$format, &$extra, &$result ) );

		// Allow Total Event Interception 
		if( $result )
		{
			return $result;
		}//end if

		if( $stack && $path && $filename )
		{
			// Load Basic Stack From Cache
			HelperStack::load( false, false );

			// Validate Stack & Path
			if( HelperStack::find( $stack, $path, $filename ) )
			{
				// Find File Info
				$ext 	= HelperFileSystem::extension( $path . ARKMEDIA_DS . $filename );

				// Not an Image so Return the Relevant Icon
				$result = Helper::html( 'icon.mime', $ext, array( 'format' => $format ) );
			}//end if
		}//end if

		$this->event->trigger( 'onArkAfterIcon', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Refresh All Ark Media List Cache (Stack & Thumbnail).
	 *
	 * @return	void
	 */
	public function refresh()
	{
		$this->event->trigger( 'onArkBeforeRefresh' );

		// Get Image Mediator
		Helper::add( 'helper', 'image' );

		// Clear Stack Cache
		HelperStack::refresh();

		// Clear Thumbnail Cache
		HelperImage::clearCache();

		$this->event->trigger( 'onArkAfterRefresh' );
	}//end function

	/**
	 * Plant the Root Stack Folder in the FS.
	 * 
	 * @param	string	$stack		The Desired Stack Name to Plant.
	 *
	 * @return	bool	Success Status
	 */
	public function plant( $stack = null )
	{
		return HelperStack::plant( $stack );
	}//end function

	/**
	 * Load a Folder's Child Data.
	 *
	 * @param	string	$stack		The Desired Stack Name the Path Resides.
	 * @param	string	$path		The Path to Load Up.
	 * @param	string	$branch		Load the Full Branch or Just the Leaf (and shallow children).
	 *
	 * @return	object 	File Name if Upload Succeeded, False on Failure
	 */
	public function path( $stack, $path, $branch )
	{
		$this->event->trigger( 'onArkBeforePath', array( &$stack, &$path, &$branch ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Validate Stack & Path
		if( HelperStack::find( $stack, $path ) )
		{
			// Load Up Folder Data
			$types 		= HelperStack::getStackFileTypes( $stack );
			$options 	= array( 'stack' => $stack, 'extfilter' => $types->ext, 'recurse' => false );

			// Load Up Full Branch to Requested Folder?
			if( $branch )
			{
				// Split Into Folders (now filter/normalise path for successful exploding)
				$paths 	= explode( ARKMEDIA_DS, HelperFileSystem::normalise( $path ) );

				// Build Up Path Again
				$base 	= array();
				$result = array();

				while( $folder = array_shift( $paths ) )
				{
					$base[]		= $folder;
					$folderpath = HelperFileSystem::makePath( implode( ARKMEDIA_DS, $base ) );

					HelperFileSystem::load( $folderpath, $options );

					// Add Folder & Direct Children to Results
					//  @note	If a Child Folder is Part of the Active Path it will be Fully Loaded in the Next Iteration
					$result = array_merge( $result, HelperFileSystem::retrieve() );
				}//end while

				// Set Back to false on Failure
				if( !count( $result ) )
				{
					$result = false;
				}//end if
			}
			else
			{
		 		// Just Load the Requested Folder & Shallow Children
				HelperFileSystem::load( HelperFileSystem::makePath( $path ), $options );

				$result = HelperFileSystem::retrieve();
		 	}//end if
		}
		else
		{
			// Is the Path Not Yet Created or is it Completely Invalid
			if( HelperStack::find( $stack, $path, null, false ) )
			{
				$stackdata = HelperStack::get( $stack );

				// Does the Stack's Root Folder Need Creating or is it a Nested But Uncreated Folder?
				// @note	We Currently Don't Provide the Ability to Auto-Create Nested Folders so Throw Overly Harsh Error :)
				if( $stackdata->path === HelperFileSystem::filter( $path, 'folder', true ) )
				{
					$result = array( 'status' => 'newpath' );
				}
				else
				{
					$result = array( 'status' => 'nestedpath' );
				}//end if
			}
			else
			{
				$result = array( 'status' => 'nopath' );
			}//end if
		}//end if

		$this->event->trigger( 'onArkAfterPath', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Load a Folder/File's Observer Data.
	 *
	 * @todo 	Plumb in Folder Call (currently not used).
	 *
	 * @param	string	$stack		The Desired Stack Name the Path Resides.
	 * @param	string	$path		The Path to Load Up.
	 * @param	string	$name		The File Name to Load.
	 * @param	string	$type		Whether to Look for a Folder or a File.
	 *
	 * @return	object 	Folder/File Data on Succeeded, False on Failure
	 */
	public function item( $stack, $path, $name, $type = null )
	{
		$this->event->trigger( 'onArkBeforeItem', array( &$stack, &$path, &$name, &$type ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		switch( $type )
		{
			default :
			case 'file' :
				// Validate Stack, Path & Filename
				if( HelperStack::find( $stack, $path, $name ) )
				{
					$fullpath 	= HelperFileSystem::makePath( $path ) . ARKMEDIA_DS . $name;
					$types 		= HelperStack::getStackFileTypes( $stack );
					$file		= HelperFileSystem::file( $fullpath, array( 'stack' => $stack, 'path' => $path ) );

					// Is the File Extension Allowed?
					if( $file && in_array( $file->ext, $types->ext ) )
					{
						$result = $file;
					}//end if
				}//end if
				break;

			case 'folder' :
				// Validate Stack & Path
				if( HelperStack::find( $stack, $path ) )
				{

				}//end if
				break;
		}//end switch

		$this->event->trigger( 'onArkAfterItem', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Uploads a File to the Provided Directory (if it exists in the Stack for security).
	 *
	 * @param	array	$form		The Form Details Containing the Stack Name & Upload Path
	 * @param	array	$file		The File Form Details Containing the File Data
	 *
	 * @return	mixed 	File Name if Upload Succeeded, False on Failure
	 */
	public function file( $form, $file )
	{
		$this->event->trigger( 'onArkBeforeFile', array( &$form, &$file ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Validate Stack & Path
		if( HelperStack::find( $form['stack'], $form['path'] ) )
		{
			// Get Stack's Allowed File Types
			$filters 	= HelperStack::getStackFileTypes( $form['stack'] );

			// Upload File
			$filename 	= HelperFileSystem::uploadFile( $form['path'], $file, $filters, true );

			// If Success Clear Stack for New Thumbnail & File to be Generated
			if( $filename )
			{
				// Clear Stack & Client Cache
				$this->refresh();

				$result = $filename;
			}//end if
		}
		else
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		$this->event->trigger( 'onArkAfterFile', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Create a New Folder in the Provided Directory (if it exists in the Stack for security).
	 *
	 * @param	array	$form		The Form Details Containing the Stack Name, Folder Name & Creation Path
	 *
	 * @return	mixed 	Folder Name if Creation Succeeded, False on Failure
	 */
	public function folder( $form )
	{
		$this->event->trigger( 'onArkBeforeFolder', array( &$form ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Validate Stack & Path
		if( HelperStack::find( $form['stack'], $form['path'] ) )
		{
			// Create Folder
			$name 	= ( $form['folder'] ) ?: JText::_( ARKMEDIA_JTEXT . 'FOLDER_NEWFOLDER_LBL' );
			$folder = HelperFileSystem::makeDir( $form['path'], $name, true );

			// If Success Clear Stack for New Folder to be Populated
			if( $folder )
			{
				// Clear Stack Cache
				HelperStack::refresh();

				$result = $folder;
			}//end if
		}
		else
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		$this->event->trigger( 'onArkAfterFolder', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Deletes Folders & Files from the Provided Directory (if it exists in the Stack for security).
	 *
	 * @param	array	$form		The Form Details Containing the Stack Name, Path & Selected Items
	 *
	 * @return	mixed 	Deleted Item Names if Success, False on Failure
	 */
	public function delete( $form )
	{
		$this->event->trigger( 'onArkBeforeDelete', array( &$form ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Validate Stack & Path
		if( HelperStack::find( $form['stack'], $form['path'] ) )
		{
			$folders 	= ( isset( $form['folders'] ) ) ? (array)$form['folders'] 	: array();
			$files 		= ( isset( $form['files'] ) ) 	? (array)$form['files'] 	: array();
			$items 		= array();

			if( !( count( $folders ) + count( $files ) ) )
			{
				throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_DELETENOSELECTED_FAIL' ) );
			}//end if

			// Delete Folders & Files
			foreach( array_merge( $folders, $files ) as $item )
			{
				if( HelperFileSystem::delete( $form['path'], $item ) )
				{
					$items[] = $item;
				}//end if
			}//end foreach

			// If Success Clear Stack for Removed Items to be Cleared
			if( count( $items ) )
			{
				// Clear Stack Cache
				HelperStack::refresh();

				// Remove Any Empty Values (empty values should throw an exception really and not get here)
				$result = array_filter( $items );
			}//end if
		}
		else
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		$this->event->trigger( 'onArkAfterDelete', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Duplicate Folders & Files in the Provided Directory (if it exists in the Stack for security).
	 *
	 * @param	array	$form		The Form Details Containing the Stack Name, Path, Folders & Files
	 *
	 * @return	mixed 	Array of (key)Old Names => (val)New Names if Success, False on Failure
	 */
	public function copy( $form )
	{
		$this->event->trigger( 'onArkBeforeCopy', array( &$form ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Validate Stack & Path
		if( HelperStack::find( $form['stack'], $form['path'] ) )
		{
			$folders 	= ( isset( $form['folders'] ) ) ? (array)$form['folders'] 	: array();
			$files 		= ( isset( $form['files'] ) ) 	? (array)$form['files'] 	: array();
			$items 		= array();

			if( !( count( $folders ) + count( $files ) ) )
			{
				throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_COPYNOSELECTED_FAIL' ) );
			}//end if

			// Copy Folders & Files
			foreach( array_merge( $folders, $files ) as $item )
			{
				$name = HelperFileSystem::copy( $form['path'], $item );

				if( $name )
				{
					$items[$item] = $name;
				}//end if
			}//end foreach

			// If Success Clear Stack for Copied Items to be Regenerated
			if( count( $items ) )
			{
				// Clear Stack Cache
				HelperStack::refresh();

				// Remove Any Empty Values (empty values should throw an exception really and not get here)
				$result = array_filter( $items );
			}//end if
		}
		else
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		$this->event->trigger( 'onArkAfterCopy', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Move Folders & Files From One Stack Directory to Another (if they both exist in the Stack for security).
	 *
	 * @param	array	$form		The Form Details Containing the Stack Name, Paths, Folders & Files
	 *
	 * @return	mixed 	Array of (key)Old Names => (val)New Names if Success, False on Failure
	 */
	public function move( $form )
	{
		$this->event->trigger( 'onArkBeforeMove', array( &$form ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Find Path Data in Stack (expecting the folder's data)
		$path 	= HelperFileSystem::filter( $form['path'], 'folder', true );
		$extra	= HelperFileSystem::filter( $form['extra'], 'folder', true );
		$from 	= HelperStack::find( $form['stack'], $path );
		$to 	= HelperStack::find( $form['stack'], $extra );

		if( $from && $to )
		{
			$folders 	= ( isset( $form['folders'] ) ) ? (array)$form['folders'] 	: array();
			$files 		= ( isset( $form['files'] ) ) 	? (array)$form['files'] 	: array();
			$items 		= array();

			if( !( count( $folders ) + count( $files ) ) )
			{
				throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_COPYNOSELECTED_FAIL' ) );
			}//end if

			// Move Folders & Files
			foreach( array_merge( $folders, $files ) as $item )
			{
				$name = HelperFileSystem::move( $path, $extra, $item );

				if( $name )
				{
					$items[] = (object)array( 'from' => $path, 'to' => $extra, 'name' => $name );
				}//end if
			}//end foreach

			// If Success Clear Stack for Copied Items to be Regenerated
			if( count( $items ) )
			{
				// Clear Stack Cache
				HelperStack::refresh();

				$result = $items;
			}//end if
		}
		else
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		$this->event->trigger( 'onArkAfterMove', array( &$result ) );

		return $result;
	}//end function

	/**
	 * Rename Folders & Files in the Provided Directory (if it exists in the Stack for security).
	 *
	 * @param	array	$form		The Form Details Containing the Stack Name, Path, Folders & Files
	 *
	 * @return	mixed 	Array of (key)Old Names => (val)New Names if Success, False on Failure
	 */
	public function rename( $form )
	{
		$this->event->trigger( 'onArkBeforeRename', array( &$form ) );

		$result = false;

		// Load Basic Stack From Cache
		HelperStack::load( false, false );

		// Validate Stack & Path
		if( HelperStack::find( $form['stack'], $form['path'] ) )
		{
			$folders 	= ( isset( $form['folders'] ) ) ? (array)$form['folders'] 	: array();
			$files 		= ( isset( $form['files'] ) ) 	? (array)$form['files'] 	: array();
			$items 		= array();

			if( !( count( $folders ) + count( $files ) ) )
			{
				throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_RENAMENOSELECTED_FAIL' ) );
			}//end if

			// Rename Folders & Files
			foreach( array_merge( $folders, $files ) as $item )
			{
				$name = HelperFileSystem::rename( $form['path'], $item, $form['extra'] );

				if( $name )
				{
					$items[$item] = $name;
				}//end if
			}//end foreach

			// If Success Clear Stack for Renamed Items to be Regenerated
			if( count( $items ) )
			{
				// Clear Stack Cache
				HelperStack::refresh();

				// Remove Any Empty Values (empty values should throw an exception really and not get here)
				$result = array_filter( $items );
			}//end if
		}
		else
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		$this->event->trigger( 'onArkAfterRename', array( &$result ) );

		return $result;
	}//end function
}//end class
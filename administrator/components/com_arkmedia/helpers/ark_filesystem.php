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
use JText, JFolder, JFile, JPath, JArrayHelper, JHelperMedia, JString, JFactory, JHTML, JRoute, JRegistry;

// Add PHP Classes
use stdClass, Exception;

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

/**
 * Extended JFile & JFolder Logic
 */
class HelperFileSystem
{
	/**
	 * @var		array	Temporary Array to Store Folders & Files When Traversing
	 */
	protected static $tree 		= array();

	/**
	 * @var		const	The Root Directory of this Installation
	 */
	protected static $root 		= null;

	/**
	 * @var		string	Path to the Current Working Directory
	 */
	protected static $path 		= null;

	/**
	 * @var		array	Options to Define How the Search is Performed
	 */
	protected static $options 	= null;

	/**
	 * @var		bool	Flag to Check Whether the Folders & Files Have Been Sorted Yet (to avoid repeating)
	 */
	protected static $sorted 	= null;

	/**
	 * Load Folders & Files From a Given Directory into the Temporary Tree for Retrieval.
	 *
	 * @param	string	$path		The Path to Traverse to Find Folders & Files
	 * @param	array	$options	Options to Determine How to Perform the Search
	 * @param	bool	$parent		Internal Flag to Detect for the Root Directory When Recursing
	 * 
	 * @return	void
	 */
	public static function load( $path, $options = array(), $parent = null )
	{
		// Set Parent on First Time Round & Clear Temporary Variables From Previous Searches
		if( !$parent )
		{
			// Inherit JFileSystem Behaviour
			@set_time_limit( ini_get( 'max_execution_time' ) );

			$path								= static::n( $path );
			static::$sorted 					= null;
			static::$tree 						= array();
			static::$options 					= array();
			static::$path 						= static::t( str_replace( static::r(), '', $path ), false );

			// Validate Path
			if( !JFolder::exists( $path ) )
			{
				return;
			}//end if

			// We Need to Make Sure that the Root/Parent Folder Gets Added to the Tree.
			// If the Root Has Sub Folders then the While Loop Will Create the Entry for Us, But if there are no Children then the "While" Does not Create the Root.
			// So We Create it on First Load to Cater for Both Scenarios. (at this point we only have the name).
			$parentname 						= ( strpos( static::$path, ARKMEDIA_DS ) !== false ) ? substr( strrchr( static::$path, ARKMEDIA_DS ), 1 ) : static::$path;
			static::$tree[static::$path] 		= static::template( 'folder', $parentname );
			static::$tree[static::$path]->loaded= true;
		}//end if

		// Set Options
		if( $options != static::$options || !count( static::$options ) )
		{
			static::$options['stack'] 			= ( isset( $options['stack'] ) ) 			? $options['stack'] 		: null; // Most of the FileSystem is Stack Agnostic But We Need It For Thumbnails
			static::$options['recurse'] 		= ( isset( $options['recurse'] ) ) 			? $options['recurse'] 		: true;
			static::$options['exclude'] 		= ( isset( $options['exclude'] ) ) 			? $options['exclude'] 		: array( '.', '..', '.svn', 'CVS', '.DS_Store', '__MACOSX' );
			static::$options['folderfilter'] 	= ( isset( $options['folderfilter'] ) ) 	? $options['folderfilter'] 	: array( '^\..*', '.*~' ); // Remove Dot Folders (hidden), Remove Spaced Names?
			static::$options['filefilter'] 		= ( isset( $options['filefilter'] ) ) 		? $options['filefilter'] 	: array( '^\..*', '.*~' ); // Remove Tilda's, Dot Names (hidden), Remove Spaced Names?
			static::$options['extfilter'] 		= ( isset( $options['extfilter'] ) ) 		? $options['extfilter'] 	: array(); // Only Allow Files With the Following Extensions
			static::$options['findfiles'] 		= ( isset( $options['findfiles'] ) ) 		? $options['findfiles'] 	: true;
			static::$options['filterascii'] 	= ( isset( $options['filterascii'] ) ) 		? $options['filterascii'] 	: Helper::params( 'filter-ascii', true );
			static::$options['thumbnails'] 		= ( isset( $options['thumbnails'] ) ) 		? $options['thumbnails'] 	: Helper::params( 'enable-thumbnails', true );
		}//end if

		// Read the Source Directory
		if( !( $handle = @opendir( $path ) ) )
		{
			return;
		}//end if

		// Collapse Folder Filters Down
		if( is_array( static::$options['folderfilter'] ) && count( static::$options['folderfilter'] ) )
		{
			static::$options['folderfilter'] = '/(' . implode( '|', static::$options['folderfilter'] ) . ')/';
		}//end if

		// Collapse File Filters Down
		if( is_array( static::$options['filefilter'] ) && count( static::$options['filefilter'] ) )
		{
			static::$options['filefilter'] = '/(' . implode( '|', static::$options['filefilter'] ) . ')/';
		}//end if

		// Retrieve Directory Item
		while( ( $file = readdir( $handle ) ) !== false )
		{
			// Need to Check Against Bad File Names/Characters? e.g.: "€", "Ãâ€š‚£_!.png", "�", "|'¬"¦`.gif"
			// Server & Browsers Seem to Support These Characters Though?
			// if( $file != utf8_encode( $file ) ) { $file = utf8_encode( $file ); }

			// Compute the Fullpath & Relative Path
			$fullpath 	= $path . ARKMEDIA_DS . $file;
			$relpath	= static::t( str_replace( static::r(), '', $path ), false );

			// Compute the isDir Flag
			$isDir 		= is_dir( $fullpath );
			$filter 	= ( $isDir ) ? static::$options['folderfilter'] : static::$options['filefilter'];

			// Check for Non ASCII Characters in Filename
			// @note 	Names That Contain Just Non-ASCII Chars Cause null Key Names (e.g. "£" or "€.png")
			// 			Which in Turn Breaks in the Client When Collapsed to JSON String.
			if( static::$options['filterascii'] && !mb_detect_encoding( $file, 'ASCII', true ) )
			{
				// Throw Error
				$errname = ( $isDir ) ? 'MSG_FILEINVALIDFOLDERNAME_FAIL' : 'MSG_FILEINVALIDFILENAME_FAIL';
				$errpath = $relpath . ARKMEDIA_DS . $file;
				Helper::message( JText::sprintf( ARKMEDIA_JTEXT . $errname, $errpath ), 'warning' );

				// Skip This Directory Item
				continue;
			}//end if

			// Filter Out Dots in Folder Names
			// @note	This is a Quick Fix to a More Serious Issue. A Full Review of Folder & File Filtering is Required.
			// @note	This Also Unnecessarily Catches '.', '..' Folders As Well
			if( $isDir && preg_match( '#\.#', $file ) )
			{
				// Throw Error
				$errpath = $relpath . ARKMEDIA_DS . $file;
				Helper::message( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFOLDERNAME_FAIL', $errpath ), 'warning' );

				// Skip This Directory Item
				continue;
			}//end if

			// Perform Filters Before Continuing
			if( !in_array( $file, static::$options['exclude'] ) && ( empty( $filter ) || !preg_match( $filter, $file ) ) )
			{
				if( $isDir )
				{
					// RelPath: The Parent Folder Path, DirPath: The Relative Directory Path
					$dirpath = $relpath . ARKMEDIA_DS . $file;

					// Create Directory Object for Populating (only have name at the mo)
					static::$tree[$dirpath] 				= static::template( 'folder', $file );

					// If the Parent Folder Hasn't Been Created Yet (traversed) Create it Now so that we can Tell it we are a Child Folder
					// @note 	Finding Files Before the Folder Has Been Added Also Creates the Folder First (before it is traversed)
					// 			If We Create the Folder Here Let the Later File Fill in the Name to Save
					if( !isset( static::$tree[$relpath] ) )
					{
						// If Name is a Detected Path Grab the Last Folder Otherwise Assume the Path is the Folder Name
						static::$tree[$relpath] 			= static::template();
						static::$tree[$relpath]->name 		= ( strpos( $relpath, ARKMEDIA_DS ) !== false ) ? substr( strrchr( $relpath, ARKMEDIA_DS ), 1 ) : $relpath;
					}//end if

					// Increase Parent's Folder Count Now that we Have Found Another Child Folder
					static::$tree[$relpath]->folders++;
				}
				elseif( static::$options['findfiles'] )
				{
					$extension = static::extension( $fullpath, true ); // @todo	If Separate Case Handling is Required; Add a Config File Info Param & Switch Here

					// If the Child File has Been Traversed Before it's Parent Folder Create the Folder First
					if( !isset( static::$tree[$relpath] ) )
					{
						// If Name is a Detected Path Grab the Last Folder Otherwise Assume the Path is the Folder Name
						static::$tree[$relpath] 			= static::template();
						static::$tree[$relpath]->name 		= ( strpos( $path, ARKMEDIA_DS ) !== false ) ? substr( strrchr( $path, ARKMEDIA_DS ), 1 ) : $path;
					}//end if

					if( !count( static::$options['extfilter'] ) || in_array( $extension, static::$options['extfilter'] ) )
					{
						// Make Key JS Safe
						$key = HelperStack::getItemKey( $file );

						// Setup Image Specific Data
						$mime = $x = $y = $thumbnail = null;

						// Calculate Icon
						$icon = Helper::html( 'icon.mime', $extension, array( 'format' => 'class' ) );

						// Store File Dimensions
						if( $size = @getimagesize( $fullpath ) )
						{
							$mime 	= $size['mime'];
							$x 		= $size[0];
							$y 		= $size[1];

							// Calculate Icon
							if( static::$options['thumbnails'] )
							{
								$thumbnail = JRoute::_( 'index.php?option=' . ARKMEDIA_COMPONENT . '&task=list.thumbnail&stack=' . static::$options['stack'] . '&path=' . $relpath . '&file=' . $file, false );
							}//end if
						}//end if

						// Store File (extension is now lowercase regardless of original)
						static::$tree[$relpath]->count++;
						static::$tree[$relpath]->items[$key] = static::template( 'file', $file, $extension, filesize( $fullpath ), filemtime( $fullpath ), $mime, $x, $y, $thumbnail, $icon );
					}
					else
					{
						// @todo Log Illegal Files for Reference? static::$tree[$relpath]->illegal++;
					}//end if
				}//end if

				// Search Recursively
				if( $isDir && static::$options['recurse'] )
				{
					// This Directory is About to be Loaded
					static::$tree[$relpath]->loaded = true;

					// Recurse
					static::load( $fullpath, static::$options, $file );
				}//end if
			}//end if
		}//end while

		// Tidy & Finish
		closedir( $handle );

		return;
	}//end function

	/**
	 * Alias Function to Return a Folder/File Struct.
	 *
	 * @param	string	$type		The Template Type
	 * 
	 * @param	mixed	...			Variadic Params For Alias Function
	 *
	 * @usage	HelperFileSystem::template( 'folder' );
	 * 			HelperFileSystem::template( 'file' );
	 * 			HelperFileSystem::template( 'folder', 'folder-name', 3 );
	 *
	 * @return	object	Folder Object for Populating
	 */
	public static function template( $type = null )
	{
		$args = func_get_args();

		// Remove Type Flag Before Passing To Sub Functions
		if( count( $args ) )
		{
			array_shift( $args );
		}//end if

		switch( $type )
		{
			default :
			case 'folder' :
				return call_user_func_array( array( get_called_class(), 'folderTemplate' ), $args );

			case 'file' :
				return call_user_func_array( array( get_called_class(), 'fileTemplate' ), $args );
		}//end switch
	}//end function

	/**
	 * Return a Class Folder Template Struct.
	 *
	 * @param	string	$name		The Folder Name
	 * @param	int		$count		The Folder's File Count
	 * @param	int		$folders	The Folder's Child Folder Count
	 * @param	array	$items		The Folder's Child Files/Items
	 * @param	bool	$loaded		The Folder's Child Load Status
	 *
	 * @note	If we Ever Want the Folder Names Rather Than a Simple Count Then Make Folders an Array
	 *			Then Change static::$tree[$parentpath]->folders++; to: static::$tree[$parentpath]->folders[] = $file;
	 *
	 * @return	object	Folder Object for Populating
	 */
	public static function folderTemplate( $name = null, $count = 0, $folders = 0, $items = array(), $loaded = false )
	{
		return (object)array( 'name' => $name, 'count' => (int)$count, 'folders' => (int)$folders, 'items' => $items, 'loaded' => (bool)$loaded );
	}//end function

	/**
	 * Return a Class File Template Struct.
	 *
	 * @param	string	$name		The File Name
	 * @param	string	$ext		The File's Extension
	 * @param	int		$filesize	The File's Filesize
	 * @param	int		$modified	The File's Modified Date as Timestamp
	 * @param	bool	$mime		The File's Mime Type if Image
	 * @param	bool	$x			The File's Width if Image
	 * @param	bool	$y			The File's Height if Image
	 * @param	string	$thumbnail	The File's Thumbnail Path if Image
	 * @param	string	$icon		The File's Mime Icon
	 *
	 * @usage	HelperFileSystem::template( 'file' );				// Get Blank File Template Without Size Array
	 * 			HelperFileSystem::template( 'file', true );			// Get Blank File Template With Size Array
	 * 			HelperFileSystem::template( 'file', 'file-name' );	// Get Semi Complete File Template Without Size Array
	 *
	 * @todo	Apply This Function to the static::load() Rather Than Manually Setup the Object?
	 *
	 * @return	array	File Object for Populating
	 */
	public static function fileTemplate( $name = null, $ext = null, $filesize = 0, $modified = null, $mime = null, $x = null, $y = null, $thumbnail = null, $icon = null )
	{
		$template = array( 'name' => $name, 'ext' => $ext, 'filesize' => (int)$filesize, 'modified' => (int)$modified, 'preview' => array( 'thumbnail' => $thumbnail, 'icon' => $icon ) );

		// Get Blank Size or Provided Size
		if( $name === true || ( $mime && $x && $y ) )
		{
			$template['size'] = array( 'x' => $x, 'y' => $y, 'mime' => $mime );

			// If Blank Size Option Reset Name From Boolean
			if( is_bool( $name ) )
			{
				$template['name'] = null;
			}//end if
		}//end if

		return $template;
	}//end function

	/**
	 * Load Folders & Files From a Given Directory into the Temporary Tree for Retrieval.
	 *
	 * @param	bool	$sort		Sort the Folders Alphabetically (Default : true)
	 *
	 * @return	array	List of Folders Containing Their Files
	 */
	public static function retrieve( $sort = true )
	{
		// Sort Folders?
		if( $sort && !static::$sorted )
		{
			ksort( static::$tree );

			static::$sorted = true;
		}//end if

		return static::$tree;
	}//end function

	/**
	 * Queues a File For Download.
	 *
	 * @todo	Workout the Joomla Way.
	 *
	 * @param	string  $path		The Path to the File
	 * @param	string  $file		The Filename
	 *
	 * @return	void
	 */
	public static function download( $path = null, $file = null )
	{
		$fullpath 	= HelperFileSystem::makePath( $path ) . ARKMEDIA_DS . $file;
		$app		= JFactory::getApplication();

		if( JFile::exists( $fullpath ) )
		{
			$mime	= 'application/octet-stream';
			$size	= filesize( $fullpath );
			$header = 'attachment; filename="' . $file . '"; creation-date="' . JFactory::getDate()->toRFC822() . '"';

			// @todo	Get the Joomla Way Working
			$app->setHeader( 'Content-type', $mime, true );
			$app->setHeader( 'Content-length', $size, true );
			$app->setHeader( 'Content-disposition', $header, true );

			header( 'Content-type: ' . $mime );
			header( 'Content-length: ' . $size );
			header( 'Content-disposition: ' .$header );

			readfile( $fullpath );

			jexit();
		}//end if

		return false;
	}//end function

	/**
	 * Load a Files Data/Information.
	 *
	 * @see 	static::load() for File Template.
	 *
	 * @param	string  $path		The Path to the File
	 * @param	array  $opts		File Set-up Options
	 *
	 * @return	object	File Information, False on Failure
	 */
	public static function file( $path = null, $opts = array() )
	{
		// Normalise Path as basename() Doesn't Always Work with Both DS's in $path
		$path 		= static::n( $path );
		$options 	= new JRegistry( $opts );

		if( JFile::exists( $path ) )
		{
			// Setup Image Specific Data
			$mime = $x = $y = $thumbnail = null;

			// Set File Data
			$file 	= basename( $path );
			$ext 	= static::extension( $path );
			$icon 	= Helper::html( 'icon.mime', $ext, array( 'format' => 'class' ) );
			$stack	= $options->get( 'stack', '' );
			$relpath= $options->get( 'path', '' ); // relative stack path

			// Store File Dimensions
			if( $size = @getimagesize( $path ) )
			{
				$mime 	= $size['mime'];
				$x 		= $size[0];
				$y 		= $size[1];

				// Calculate Icon
				if( Helper::params( 'enable-thumbnails', true ) )
				{
					$thumbnail = JRoute::_( 'index.php?option=' . ARKMEDIA_COMPONENT . '&task=list.thumbnail&stack=' . $stack . '&path=' . $relpath . '&file=' . $file, false );
				}//end if
			}//end if

			return (object)static::template( 'file', $file, $ext, filesize( $path ), filemtime( $path ), $mime, $x, $y, $thumbnail, $icon );
		}//end if

		return false;
	}//end function

	/**
	 * Filter a Path/File to Make Safe.
	 *
	 * @note	JFolder/JFile::makeSafe is no Good as it Preserves Spaces. We Only Strip Invalid Characters
	 * 			As Opposed to Only Listing a Limited/Incomplete Amount of Valid Characters.
	 *
	 * @see 	https://kb.acronis.com/content/39790
	 *
	 * @param	string	$path		The Path to Filter
	 * @param	string	$type		The Type of Path to Filter
	 * @param	bool  	$trim		Whether to Trim DS's from the Path
	 * @param	bool  	$normalise	Whether to Switch the DS's if they are Invalid
	 *
	 * @return	string	The Current Working Directory Path
	 */
	public static function filter( $path = null, $type = null, $trim = false, $normalise = true )
	{
		// Ensure Separator is Correct
		if( $normalise )
		{
			$path = static::n( $path );
		}//end if

		switch( $type )
		{
			case 'folder' :
				// Catch Drive Letter (PREG_SPLIT_NO_EMPTY = remove blank 1st array entry, PREG_SPLIT_DELIM_CAPTURE = return drive letter)
				$segments	= preg_split( '#^([A-Za-z]{1}:)#', $path, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
				$drive		= '';

				// Preserve Drive Letter as Follow Regex Strips Out the Colon
				if( count( $segments ) === 2 )
				{
					$drive	= array_shift( $segments );
					$path	= array_shift( $segments );
				}//end if

				// @note	Same as JFolder::makeSafe With the Added \s to Preserve Spaces (escape hyphen to prevent regex issues)
				$regex 		= array( '#[^A-Za-z0-9_\\\/\(\)\[\]\{\}\#\$\^\+\.\'~`!@&=;,\-\s]#' );
				$path 		= $drive . preg_replace( $regex, '', $path );
				break;

			case 'file' :
				// Remove Preceeding & Trailing Full Stops
				$path 		= trim( $path, '.' );

				// Set Up Regex
				$regex		= array();
				$characters	= Helper::params( 'filter-filename', array() );
				$entities	= array( '[quot]', '[gt]', '[lt]' );
				$unentities	= array( '"', '>', '<' );

				// Convert Placeholders to the Entities
				// @note	HTML Entity Characters Were Causing Issues Saving to the DB,
				// 			& RePopulating the List so we USe Placeholders Instead.
				$characters	= str_replace( $entities, $unentities, $characters );

				// Remove Double Full Stops
				$regex[]	= '#(\.){2,}#';

				// Add Config Defined Illegal Characters
				if( count( $characters ) )
				{
					$regex[]= '#[' . preg_quote( implode( '', $characters ) ) . ']#';
				}//end if

				$path 		= trim( preg_replace( $regex, '', $path ) );
				break;
		}//end switch

		return ( $trim ) ? static::t( $path, false ) : $path;
	}//end function

	/**
	 * Check & Make a Full Path from the Passed Relative Path.
	 *
	 * @note	JPath::Check() Performs a JPath::clean() Which Unconverts Our DS's!
	 *			So We Have 2 Reasons to Normalise; 1: Passed in $path is Dodgy, 2: Undo JPath's Conversion.
	 *
	 * @param	string	$path		The Path to Check/Make
	 * @param	bool  	$normalise	Whether to Switch the DS's (disabling is not recommended)
	 *
	 * @return	string	A Filtered Absolute Path
	 */
	public static function makePath( $path = null, $normalise = true )
	{
		// Avoid Duplicate Roots
		$path = str_replace( static::r(), '', $path );

		if( $path )
		{
			try
			{
				// Check Path Validity/Security & Perform Basic DS Filtering
				$path = JPath::check( static::r() . ARKMEDIA_DS . static::t( $path, false ) );

				// Convert DS's (recommended)
				if( $normalise )
				{
					$path = static::n( $path );
				}//end if

				return $path;
			}
			catch( Exception $e )
			{
				// JPath Will Throw Exception if:
				// 	- Relative Path,
				// 	- Path Snooping Out of Bounds,
				// 	- Path isn't a String.
			}//end try
		}//end if

		return null;
	}//end function

	/**
	 * Create a Folder in the Provided Path Renaming if Duplicated.
	 *
	 * @param	string	$path		The Path to Create the Folder in (excluding folder name)
	 * @param	string	$folder		The Name of the Folder to Create
	 * @param	string	$rename		Add a TimeStamp to the End of the Folder if it Already Exists
	 *
	 * @todo 	Move Filter Array From Here, Load(), uploadFile() & Newfolder.js Class to Somewhere Unified.
	 *
	 * @return	mixed 	Folder Name if Creation Succeeded, False on Failure
	 */
	public static function makeDir( $path, $folder, $rename = false )
	{
		// Filter Spaces, Tildas & Leading Dots
		$filters 	= array( '^\..*', '.*~', '\s' );
		$regex		= '/(' . implode( '|', $filters ) . ')/';

		// Filter Folder Name
		if( preg_match( $regex, $folder ) )
		{
			return false;
		}//end if

		// Build Path & Avoid Duplicate Roots
		$fullpath = static::makePath( $path ) . ARKMEDIA_DS;

		// Avoid Duplicates
		if( $rename && JFolder::exists( $fullpath . $folder ) )
		{
			$folder = static::uniqueName( $fullpath, $folder, 'folder' );
		}//end if

		return ( JFolder::create( $fullpath . $folder ) ) ? $folder : false;
	}//end function

	/**
	 * Uploads a File From it's Temporary Location in the Provided Path Renaming if Duplicated.
	 *
	 * @param	string	$path		The Path to Create the Folder in (excluding folder/file name)
	 * @param	array	$file		The File Information to Transfer & Upload
	 * @param	object	$filters	File Filter Types (ext & mime) to Check the Uploaded File
	 * @param	string	$rename		Add a TimeStamp to the End of the File if it Already Exists
	 *
	 * @todo 	Move Filter Array From Here, Load(), makeDir() & Newfolder.js Class to Somewhere Unified.
	 *
	 * @return	mixed 	File Name if Upload Succeeded, False on Failure
	 */
	public static function uploadFile( $path, $file, $filters, $rename = false )
	{
		// Test File Integrity
		if( !is_array( $file ) || !count( $file ) || !( $file = JArrayHelper::toObject( $file ) ) )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDITEM_FAIL' ) );
		}//end if

		// Filters Must Always be Passed?
		if( !is_object( $filters ) || !count( $filters->ext ) || !count( $filters->mime ) )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILENOFILTERSUPLOAD_FAIL' ) );
		}//end if

		// Test File Errors
		if( $file->error )
		{
			throw new Exception( static::getFileError( $file->error ) );
		}//end if

		// Test File Size
		if( !static::testFileSize( $file->size ) )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILETOOLARGE_FAIL' ) );
		}//end if

		// Filter Name
		$file->name	= static::filter( $file->name, 'file' );

		// Test File Name
		if( !$file->name )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDNAME_FAIL' ) );
		}//end if

		// Get Media Helper
		$media 			= new JHelperMedia;

		// Switch Names for JHelperMedia's Benefit
		// @note	We Avoid JFile::makeSafe() But JHelperMedia Doesn't So We Do Our Own Checks & Trick the Media :)
		// @see		static::filter(); Function Notes.
		$file->_name 	= $file->name;
		$file->name		= JFile::makeSafe( $file->name );

		// Set Up Params for Media Helper
		Helper::set( 'upload_extensions', implode( ',', $filters->ext ) ); // Pass Through to Media Filters
		Helper::set( 'upload_mime', implode( ',', $filters->mime ) ); // Pass Through to Media Filters
		Helper::set( 'restrict_uploads', true ); // Ensure Media Filters Fully
		Helper::set( 'check_mime', true ); // Ensure Media Filters Fully

		// Utilise Media Helper for Additional Filtering
		if( !$media->canUpload( (array)$file, ARKMEDIA_COMPONENT ) )
		{
			// Helper Adds It's Own Messages
			throw new Exception();
		}//end if

		// Switch Back to Our Name After JHelperMedia Checks
		$file->name = $file->_name;

		// Build Path & Avoid Duplicate Roots
		$fullpath 	= static::makePath( $path ) . ARKMEDIA_DS;

		// Avoid Duplicates
		if( JFile::exists( $fullpath . $file->name ) )
		{
			if( !$rename )
			{
				throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEEXISTS_FAIL' ) );
			}//end if

			$file->name = static::uniqueName( $fullpath, $file->name, 'file' );
		}//end if

		return ( JFile::upload( $file->tmp_name, $fullpath . $file->name ) ) ? $file->name : false;
	}//end function

	/**
	 * Uploads a File From a base64 String in the Provided Path Renaming if Duplicated. 
	 * String Must Have Prefixing: data:image/png;base64,
	 *
	 * @param	string	$path		The Path to Create the Folder in (including folder/file name)
	 * @param	string	$data		The Base64 File Information to Upload
	 * @param	string	$name		The Filename to Create/Override
	 * @param	bool	$override	Override File if Exists or Save as Duplicate
	 *
	 * @todo 	Move Filter Array From Here, Load(), makeDir() & Newfolder.js Class to Somewhere Unified.
	 *
	 * @return	mixed 	File Name if Upload Succeeded, False on Failure
	 */
	public static function uploadBase64( $path, $data, $name, $override = false )
	{
		// Build Path & Avoid Duplicate Roots
		$basepath = static::makePath( $path );
		$fullpath = $basepath . ARKMEDIA_DS . $name;

		// Check Path
		if( !is_dir( $basepath ) )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDEREXISTS_FAIL' ) );
		}//end if

		// If File Exists & Not Allowed to Override then Save as Duplicate
		if( JFile::exists( $fullpath ) && !$override )
		{
			$name 		= static::uniqueName( $basepath . ARKMEDIA_DS, $name, 'file' );
			$fullpath 	= $basepath . ARKMEDIA_DS . $name;
		}//end if

		// Strip Prefixed Base64 Data: "data:image/png;base64,"
		$segments = explode( ',', $data );

		if( !$segments[1] )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDITEM_FAIL' ) );
		}//end if

		// Decode Data
		$output = base64_decode( $segments[1] );

		return ( JFile::write( $fullpath, $output ) ) ? $name : false;
	}//end function

	/**
	 * Deletes a Folder or File From the Provided Path.
	 *
	 * @param	string	$path		The Path to Search for the Folder/File (excluding folder/file name)
	 * @param	string	$name		The Folder/File Name
	 *
	 * @return	bool 	Delete Status
	 */
	public static function delete( $path, $name )
	{
		if( !$name )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDNODATA_FAIL' ) );
		}//end if

		// Build Path & Avoid Duplicate Roots
		$fullpath = static::makePath( $path ) . ARKMEDIA_DS . $name;

		// Folder or File?
		if( is_dir( $fullpath ) )
		{
			// Test Folder Integrity (normalise already performed by makePath)
			if( static::filter( $fullpath, 'folder', false, false ) != $fullpath )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFOLDER_FAIL', $name ) );
			}//end if

			// Test Folder Existence
			if( !JFolder::exists( $fullpath ) )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILENOFOLDER_FAIL', $name ) );
			}
			else
			{
				return JFolder::delete( $fullpath );
			}//end if
		}
		elseif( is_file( $fullpath ) )
		{
			// Test File Integrity
			if( static::filter( $name, 'file' ) != $name )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFILE_FAIL', $name ) );
			}//end if

			// Test File Existence
			if( !JFile::exists( $fullpath ) )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILENOFILE_FAIL', $name ) );
			}
			else
			{
				return JFile::delete( $fullpath );
			}//end if
		}
		else
		{
			throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDITEM_FAIL', $name ) );
		}//end if
	}//end function

	/**
	 * Duplicate a Folder or File From the Provided Path.
	 *
	 * @param	string	$path		The Path to Search for the Folder/File (excluding folder/file name)
	 * @param	string	$name		The Folder/File Name
	 *
	 * @return	bool 	Duplication Status
	 */
	public static function copy( $path, $name )
	{
		if( !$name )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDNODATA_FAIL' ) );
		}//end if

		// Build Path & Avoid Duplicate Roots
		$basepath = static::makePath( $path ) . ARKMEDIA_DS;
		$fullpath = $basepath . $name;

		// Folder or File?
		if( is_dir( $fullpath ) )
		{
			// Test Folder Integrity (normalise already performed by makePath)
			if( static::filter( $fullpath, 'folder', false, false ) != $fullpath )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFOLDER_FAIL', $name ) );
			}//end if

			// Try to Build a Unique New Name
			$newname = static::uniqueName( $basepath, $name, 'folder' );

			// Duplicate Folder Now that we Know we Have a Unique Name
			return ( JFolder::copy( $fullpath, $basepath . $newname ) ) ? $newname : false;
		}
		elseif( is_file( $fullpath ) )
		{
			// Test File Integrity
			if( static::filter( $name, 'file' ) != $name )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFILE_FAIL', $name ) );
			}//end if

			// Try to Build a Unique New Name
			$newname = static::uniqueName( $basepath, $name, 'file' );

			// Duplicate File Now that we Know we Have a Unique Name
			return ( JFile::copy( $fullpath, $basepath . $newname ) ) ? $newname : false;
		}
		else
		{
			throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDITEM_FAIL', $name ) );
		}//end if
	}//end function

	/**
	 * Move a Folder or File Between the Two Provided Paths.
	 *
	 * @param	string	$from		The Path the Folder/File Current Resides (excluding folder/file name)
	 * @param	string	$to			The Destination Path for the Folder/File (excluding folder/file name)
	 * @param	string	$name		The Directory Item Name
	 * @param	bool	$rename		Rename the Item if it Exists in the Destination Folder?
	 *
	 * @return	bool 	Duplication Status
	 */
	public static function move( $from, $to, $name, $rename = true )
	{
		if( !$name )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDNODATA_FAIL' ) );
		}//end if

		// Build Path & Avoid Duplicate Roots
		$frompath 	= static::makePath( $from ) . ARKMEDIA_DS;
		$topath 	= static::makePath( $to ) . ARKMEDIA_DS;

		// Folder or File?
		if( is_dir( $frompath . $name ) )
		{
			// Test Folder Integrity (normalise already performed by makePath)
			if( static::filter( $frompath, 'folder', false, false ) != $frompath )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFOLDER_FAIL', $name ) );
			}//end if

			// Avoid Duplicates
			if( JFolder::exists( $topath . $name ) )
			{
				if( !$rename )
				{
					throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEEXISTSITEM_FAIL', $name ) );
				}//end if

				$newname = static::uniqueName( $topath, $name, 'folder' );
			}
			else
			{
				$newname = $name;
			}//end if

			// Move Folder Now that we Know we Have a Unique Name
			return ( JFolder::move( $frompath . $name, $topath . $newname ) ) ? $newname : false;
		}
		elseif( is_file( $frompath . $name ) )
		{
			// Test File Integrity
			if( static::filter( $name, 'file' ) != $name )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFILE_FAIL', $name ) );
			}//end if

			// Avoid Duplicates
			if( JFile::exists( $topath . $name ) )
			{
				if( !$rename )
				{
					throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEEXISTSITEM_FAIL', $name ) );
				}//end if

				$newname = static::uniqueName( $topath, $name, 'file' );
			}
			else
			{
				$newname = $name;
			}//end if

			// Move File Now that we Know we Have a Unique Name
			return ( JFile::move( $frompath . $name, $topath . $newname ) ) ? $newname : false;
		}
		else
		{
			throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDITEM_FAIL', $name ) );
		}//end if
	}//end function

	/**
	 * Rename a Folder or File From the Provided Path. There Technically Isn't a Rename Function So Use Move!
	 *
	 * @param	string	$path		The Path to Search for the Folder/File (excluding folder/file name)
	 * @param	string	$old		The Old Folder/File Name
	 * @param	string	$new		The Folder/File Name
	 * @param	bool	$rename		Rename the Item if it Exists in the Destination Folder?
	 *
	 * @return	bool 	Duplication Status
	 */
	public static function rename( $path, $old, $new, $rename = true )
	{
		if( !$old || !$new )
		{
			throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDNODATA_FAIL' ) );
		}//end if

		// Build Path & Avoid Duplicate Roots
		$basepath 	= static::makePath( $path ) . ARKMEDIA_DS;
		$oldpath	= $basepath . $old;
		$newpath 	= $basepath . $new;

		// Folder or File?
		if( is_dir( $oldpath ) )
		{
			// Test Folder Integrity (normalise already performed by makePath)
			if( static::filter( $newpath, 'folder', false, false ) != $newpath )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFOLDER_FAIL', $new ) );
			}//end if

			// Avoid Duplicates
			if( JFolder::exists( $newpath ) )
			{
				if( !$rename )
				{
					throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEEXISTSITEM_FAIL', $new ) );
				}//end if

				$newname = static::uniqueName( $basepath, $new, 'folder' );
			}
			else
			{
				$newname = $new;
			}//end if

			// "Move" Folder Now that we Know we Have a Unique Name
			return ( JFolder::move( $oldpath, $basepath . $newname ) ) ? $newname : false;
		}
		elseif( is_file( $oldpath ) )
		{
			// Test File Integrity
			if( static::filter( $new, 'file' ) != $new )
			{
				throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDFILE_FAIL', $new ) );
			}//end if

			// Avoid Duplicates
			if( JFile::exists( $newpath ) )
			{
				if( !$rename )
				{
					throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEEXISTSITEM_FAIL', $new ) );
				}//end if

				$newname = static::uniqueName( $basepath, $new, 'file' );
			}
			else
			{
				$newname = $new;
			}//end if

			// "Move" File Now that we Know we Have a Unique Name
			return ( JFile::move( $oldpath, $basepath . $newname ) ) ? $newname : false;
		}
		else
		{
			throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILEINVALIDITEM_FAIL', $old ) );
		}//end if
	}//end function

	/**
	 * This Function Returns the File's Extension. It Optionally Returns the Extension as a Lower Case Value.
	 *
	 * @note 	JFile::getExt() Returns the Whole Path if the File has No Extension. We Handle That Here.
	 *
	 * @note	JFolder & JFile are DS Agnostic and As Such Don't Require Normalising.
	 *
	 * @see 	http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_item_id=28443
	 * 			http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=32&tracker_item_id=18997
	 *
	 * @param	string	$file		The File Name/Relative/Full Path
	 * @param	bool	$lower		Whether to Convert the Extension to Lower Case
	 *
	 * @return	string	The File Extension
	 */
	public static function extension( $file, $lower = false )
	{
		$ext = JFile::getExt( $file );

		// Fix Joomla Issue :/
		if( strpos( $file, '.' ) === false )
		{
			$ext = null;
		}//end if

		// Regulate Extension
		if( $lower )
		{
			$ext = JString::strtolower( $ext );
		}//end if

		return $ext;
	}//end function

	/**
	 * This Function Checks the Existence of a Folder or File Using JFolder & JFile.
	 *
	 * @note	JFolder & JFile are DS Agnostic and As Such Don't Require Normalising.
	 *
	 * @param	string	$path		The Path to Check
	 * @param	string	$type		Whether the Directory Item is a Folder or a File
	 *
	 * @return	bool	Existential Flag
	 */
	public static function exists( $path, $type = null )
	{
		switch( $type )
		{
			case 'folder' :
				return JFolder::exists( $path );

			default :
			case 'file' :
				return JFile::exists( $path );
		}//end switch
	}//end function

	/**
	 * This Function Takes a Folder/File Path & Splits it into 2 Parts; The Base Path & The Folder/File Name.
	 *
	 * @note	We Let the Caller Normalise the Path.
	 *
	 * @param	string	$path		The Path to Split
	 *
	 * @return	object	List of Path Segments
	 */
	public static function split( $path = null )
	{
		$segments 		= new stdClass;
		$segments->name = basename( $path );
		$segments->base = ( dirname( $path ) !== '.' ) ? dirname( $path ) : '';
		$segments->glue = ARKMEDIA_DS;

		return $segments;
	}//end function

	/**
	 * This Function Returns the Uploaded File's Attached Error.
	 *
	 * @param	int		$error		The File's Error Status
	 *
	 * @return	string	The Error Message, if Any
	 */
	public static function getFileError( $error = null )
	{
		switch( $error )
		{
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE: 
			case UPLOAD_ERR_PARTIAL: 
			case UPLOAD_ERR_NO_FILE: 
			case UPLOAD_ERR_NO_TMP_DIR: 
			case UPLOAD_ERR_CANT_WRITE: 
			case UPLOAD_ERR_EXTENSION: 
				return JText::_( ARKMEDIA_JTEXT . 'MSG_FILEERROR' . $error . '_FAIL' );

			default: 
				return JText::_( ARKMEDIA_JTEXT . 'MSG_FILEERRORUNKNOWN_FAIL' );
		}//end if
	}//end function

	/**
	 * This Function Tests Whether the Requested File is Within Safe File Size Limits
	 *
	 * @param	int		$size		The File's Size
	 * @param	bool	$server		Whether to Test the Server Post Size
	 *
	 * @return	bool	True if Safe, False on Argument Failure
	 */
	public static function testFileSize( $size = null, $server = true )
	{
		$app	= JFactory::getApplication();
		$upload = (int)Helper::params( 'upload-max' ) * 1024 * 1024;
		$length = $app->input->server->get( 'CONTENT_LENGTH', false, 'int' );
		$max 	= Helper::html( 'text.bytes', ini_get( 'upload_max_filesize' ) );
		$post 	= Helper::html( 'text.bytes', ini_get( 'post_max_size' ) );
		$memory = Helper::html( 'text.bytes', ini_get( 'memory_limit' ) );

		if( $server && $upload && $length > $upload )
		{
			return false;
		}
		elseif( $server && ( !$max || $length > $max ) )
		{
			return false;
		}
		elseif( $server && ( !$post || $length > $post ) )
		{
			return false;
		}
		elseif( $server && ( !$memory || $length > $memory ) )
		{
			return false;
		}
		elseif( $size && $upload && $size > $upload )
		{
			return false;
		}//end if

		return true;
	}//end function

	/**
	 * This Function Takes the Folder/File & Generates a Uniquely Incremented Name to Avoid Conflicts
	 *
	 * @todo  	Implement JString::increment() as it Does Half of What We're Doing!
	 * 
	 * @param	string	$path		The Path to the Directory Item (excluding folder/file name)
	 * @param	string	$name		The Folder/File's Name
	 * @param	bool	$type		Whether to the Directory Item is a Folder or a File
	 *
	 * @return	string	New Unique Name
	 */
	public static function uniqueName( $path = null, $name = null, $type = 'file' )
	{
		// Try to Build a Unique New Name
		$i 					= 1; 		// "Copy Number"
		$sig 				= '';		// "Copy Signature"
		$ext 				= '';		// Item Extension (if applicable)
		$prename			= $name;	// Item Name Without Extension (if applicable)
		$matches			= array();

		switch( $type )
		{
			case 'folder' :
				$sig 		= '-%s';
				$class 		= 'JFolder';
				break;

			default :
			case 'file' :
				$prename	= JFile::stripExt( $name );
				$ext 		= static::extension( $name );
				$sig 		= ( $ext ) ? '-%s.' : '-%s'; // Files With No Extension Need no Trailing Dot
				$class 		= 'JFile';
				break;
		}//end switch

		// If the Item Already has a Copy Signature at the End, Use this Rather Than Appending a New One
		// This Means:
		// item-1.ext 		=> item-1.ext & NOT item-1-1.ext (avoid multiple signatures)
		// item.ext 		=> item-1.ext (add copy signatures)
		// item-1-1.ext 	=> item-1-1.ext & NOT item-1-1-1.ext (item already contains multiple signatures, avoid more)
		// item-031.ext 	=> item-031.ext & NOT item-031-1.ext (avoid multiple signatures - note leading zeros will be dropped in loop)
		// it-1.em.ext 		=> it-1.em-1.ext (doesn't affect signatures in file name)
		// item 			=> item-1 (lack of extension doesn't affect signatures)
		if( preg_match( '#' . sprintf( $sig, '([\d]+)' ) . $ext . '$#', $name, $matches ) )
		{
			// Keep Original Name to Avoid Multiple Copy Signatures
			$newname 	= $name;
			// Set Found Copy Number to Current Number (to save while loop having to loop up to this point)
			$i 			= $matches[1];
		}
		else
		{
			// Add Copy Signature
			$newname 	= $prename . sprintf( $sig, $i ) . $ext;
		}//end if

		// Intiate Loop Till we have a Unique Signature/File Name
		while( call_user_func( array( $class, 'exists' ), $path . $newname ) )
		{
			// Increment Copy Number
			$newname = preg_replace( '#' . sprintf( $sig, $i ) . $ext . '$#', sprintf( $sig, ++$i ) . $ext, $newname );
		}//end while

		return $newname;
	}//end function

	/**
	 * This Function Retrieves a Normalised DS Safe Root Installation Value.
	 *
	 * @see		Initiator File For Same Root Setter Logic. Making This Depreciated?
	 *
	 * @return	string	Root Path
	 */
	public static function getRoot()
	{
		// Get Cached Value
		if( !static::$root )
		{
			static::$root = static::normalise( JPATH_ROOT );
		}//end if

		return static::$root;
	}//end function

	/**
	 * This Function Takes a Path & Ensures that IIS DS Separators Are Forced to *nix Formats.
	 *
	 * @note 	As of [#2877] All DS's Have Been Converted to Forced Forward Slashes.
	 *
	 * @param	string	$path		The Path to Convert
	 *
	 * @return	string	Converted Path
	 */
	public static function normalise( $path = null )
	{
		return str_replace( '\\', '/', $path );
	}//end function

	/**
	 * This Function Takes a Path & Trims All DS Types & Spaces From the Start & End.
	 *
	 * @see		See JPath::clean() for Similar Implementation!
	 *
	 * @todo	Should We Regex This As 'path/folder \/' Will Filter But 'path/folder \/ ' Will Fail.
	 *
	 * @param	string	$path		The Path to Convert
	 * @param	bool	$spaces		Strip All Space Types?
	 * @param	bool	$ds			Strip All DS Types
	 *
	 * @return	string	Converted Path
	 */
	public static function trim( $path = null, $spaces = true, $ds = true )
	{
		if( $ds )
		{
			$path = trim( $path, chr( 47 ) . chr( 92 ) );
		}//end if

		if( $spaces )
		{
			$path = trim( $path );
		}//end if

		return $path;
	}//end function

	/**
	 * Alias Function to getRoot().
	 *
	 * @return	string	Path
	 */
	public static function r()
	{
		return call_user_func_array( array( get_called_class(), 'getRoot' ), func_get_args() );
	}//end function

	/**
	 * Alias Function to normalise().
	 *
	 * @return	string	Path
	 */
	public static function n()
	{
		return call_user_func_array( array( get_called_class(), 'normalise' ), func_get_args() );
	}//end function

	/**
	 * Alias Function to trim().
	 *
	 * @return	string	Path
	 */
	public static function t()
	{
		return call_user_func_array( array( get_called_class(), 'trim' ), func_get_args() );
	}//end function
}//end class
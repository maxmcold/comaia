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
use JImage, JFile, JFolder, JFilterInput, JURI, JHTML;

// Add PHP Classes
use stdClass, Exception;

defined( '_JEXEC' ) or die;

jimport( 'joomla.image.image' );

/**
 * Stack File/Image Mediator
 */
class HelperImage
{
	/**
	 * @var		string	Instance of Joomla's Image Library Helper
	 */
	protected static $image 	= null;

	/**
	 * @var		array	List of Valid Image Types
	 */
	protected static $types 	= array( IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP );

	/**
	 * @var		array	Array of Currently Generated Thumbnails
	 */
	protected static $thumbs 	= array();

	/**
	 * Load the Image in for Editing.
	 *
	 * @param	string	$file	The Name of the File or the Relative Path
	 * @param	string	$path	If the File is Just the File Name Then Pass the Relative Location too.
	 *
	 * @return	bool	Success Status
	 */
	public static function load( $file, $path = null )
	{
		$location = ( is_null( $path ) ) ? ARKMEDIA_ROOT . ARKMEDIA_DS . $file : ARKMEDIA_ROOT . ARKMEDIA_DS . $path . ARKMEDIA_DS . $file;

		// Load Image if it is an Image
		if( static::isImage( $location ) )
		{
			// Bump Up Memory if Needed as Large Images Throw Fatal Errors
			static::isProdigious( $location, true );

			// Now Load Image (JImage->loadFile() can't handle large files)
			static::$image = new JImage( $location );

			return true;
		}//end if

		return false;
	}//end function

	/**
	 * Check to See if a File is an Image or Not.
	 *
	 * @param	string	$path	The Full File Path to Test
	 *
	 * @return	bool	Is Image Status
	 */
	public static function isImage( $path )
	{
		$image = @getimagesize( $path );

		return ( is_array( $image ) && in_array( $image[2], static::$types ) ) ? true : false;
	}//end function

	/**
	 * Check to See if the Image is too Massive to Handle. 
	 * A "Fatal error: Allowed memory size xxx bytes exhausted" Can Occur When Large Proportion
	 * Or Large Filesize Images are Read by imagecreatefrompng/imagecreatefromjpeg etc.
	 * If there won't be Enough Memory to Handle the Read Process Then Increase the Memory Limit if Allowed.
	 *
	 * @note	Not All Images Have Channels or Bits so Assume 3 Channels (RGB, CMYK = 4), & 8 Bits Per Pixel for Rough Calculation.
	 * 
	 * @see		http://php.net/manual/en/function.getimagesize.php#refsect1-function.getimagesize-returnvalues
	 *
	 * @param	string	$path	The Full File Path to Test
	 * @param	bool	$handle	Whether to Increase the Memory to Handle the Gap or Just Return the Findings
	 *
	 * @return	bool	Is the Image too Large to Handle
	 */
	public static function isProdigious( $path, $handle = false )
	{
		$info 		= getimagesize( $path );
		$width 		= $info[0];
		$height 	= $info[1];
		$bits 		= ( isset( $info['channels'] ) ) ? $info['bits'] : 8;
		$channels 	= ( isset( $info['channels'] ) ) ? $info['channels'] : 3;
		$demand 	= round( ( $width * $height * $bits * $channels / 8 + pow( 2, 16 ) ) * 1.8 );
		$quota		= Helper::html( 'text.bytes', ini_get( 'memory_limit' ) );
		$current	= memory_get_usage( true ); // Take Into Account the Current Mem Used

		// Too Big to Handle?
		if( ( $demand + $current ) >= $quota )
		{
			// Increase the Limit to Cope -> What the Image Needs ($d) + What this Script has Currently Used ($c) + the Original Limit ($q)
			if( $handle )
			{
				ini_set( 'memory_limit', ( $demand + $current + $quota ) . 'B' );
			}//end if

			return true;
		}//end if

		return false;
	}//end function

	/**
	 * Generate a Thumbnail From the Loaded Image.
	 *
	 * SCALE_FILL		1 	Shrink/Stretch/Fit Both Axes to the Exact Size Constraints (Will Stretch if not Square).
	 * SCALE_INSIDE		2 	Shrink/Stretch/Fit Until Widest Axis is Within Respective Size Constraint Whilst Retaining Proportions (Overly Rectangular Images Will Look Small).
	 * SCALE_OUTSIDE	3 	Same As 2 But Until Shortest Axis is Within Respective Size Constraint (Will Better Fill Constraint but May Crop Opposite Edge).
	 * CROP				4 	Don't Resize But Crop to Centre of Image to Within Size Constraints (Large Images Cropped Small Will Show V.Small Segment of Image), (Overlap Backgrounds Are Added).
	 * CROP_RESIZE		5 	Shrink/Stretch Both Axis to Touch Size Constraints Then Crop Excess (Small Overly Rectangular Images Will Only Show Small Segment of Centre).
	 * SCALE_FIT		6 	Same As 2 But Centre's Image & Adds Overlap Background Colour (Overlap Backgrounds Not Added for Transparent Images).
	 * INTELLIGENT		7 	Hovis Best of Both: SCALE_INSIDE/CROP_RESIZE. To get the Accurateness of '5' but if Overly Rectangular Fallback to '2'.
	 *
	 * @see		[#3890]	For cache controlled thumbnail system with inline thumbs folder option.
	 *
	 * @param	string	$size	The Size of the Thumbnail to Return (It Must Appear in the List of Thumb Sizes)
	 * @param	int		$method	The Scaling Method for the Thumbnail (See Above Algorithms)
	 *
	 * @return	object	The Requested Thumbnail
	 */
	public static function thumbnail( $size = '100x100', $method = 5 )
	{
		// Validate Method to Ensure Always Integer
		if( !is_int( $method ) && is_numeric( $method ) )
		{
			$method = (int)$method;
		}
		elseif( !is_numeric( $method ) )
		{
			$method = 5;
		}//end if

		// Set thumbnail temp path to cache folder (even if AM & system caching is off)
		// This is to prevent thumbnails being generated in "thumbs" directories next to each thumbnail
		$tmpfolder	= ARKMEDIA_CACHE . ARKMEDIA_CACHE_THUMBS . ARKMEDIA_DS . 'tmp';

		$separator	= '-'; // Original ':' Doesn't Work For IIS
		$path 		= static::$image->getPath();
		$cachename 	= md5( dirname( $path ) ) . $separator . $size . $separator . basename( $path );
		$cachepath 	= ARKMEDIA_CACHE . ARKMEDIA_CACHE_THUMBS . ARKMEDIA_DS . $cachename;
		$cacheurl 	= ARKMEDIA_CACHE_URL . ARKMEDIA_CACHE_THUMBS . ARKMEDIA_DS . $cachename;

		// If There is a Thumbnail Available then Create a Thumbnail Object & Return it
		if( JFile::exists( $cachepath ) )
		{
			return (object)array( 
									'file' 			=> $cachename, 
									'path' 			=> $cachepath, 
									'url' 			=> $cacheurl, 
									'properties' 	=> static::$image->getImageFileProperties( $cachepath )
								);
		}//end if

		// Ensure that the temporary folder is created
		if( !JFolder::exists( $tmpfolder ) )
		{
			JFolder::create( $tmpfolder );
		}//end if

		// Get Original Images Dimensions for Ratio Calculation
		$props 	= static::properties();
		$x 		= $props->width;
		$y 		= $props->height;

		$ratio	= round( (float)( max( $x, $y ) / min( $x, $y ) ), 1 );

		// If Custom Method We Decide What Ratio is Best for the Thumbnail
		if( (int)$method === 7 )
		{
			// Calculate the Ratio
			// -------------------
			// If it is Passed a Predetermined Ratio Point then Change Methods.
			// If the Image is Too Rectangular Don't Use the Mimimal Crop/Resize But a Full Shrink/Resize
			// Most Square/Just-Off-Square Images Came to 1.1 ~ 1.3 Rectangular Images Came to 2.9 ~ 4.9
			// Beware of Massively Rectangular Images that Can Come to 200.0 ~ 1500.0.
			// These Throw Notices/Warnings Or Fatal Memory Limit Errors so Need Handling.
			$method = ( $ratio > 1.5 ) ? 2 : 5;
		}//end if

		// If the Ratio is Large Attempt to Switch the Method
		// @note	The CROP_RESIZE Method "Appears" to be Able to Better Handle Larger Ratio's Without Throwing Notices.
		// 			This is Because it Doesn't Use JImage->prepareDimensions() Which Can Interfere With the Axis Values.
		// 			Images of 600x2 or 550x1 Dimensions May Throw Warnings With the resize() as the Height/Width is ReCalculated to Zero (600x0).
		// 			But Images of 550x3, 550x4 Aren't Affected.
		if( $ratio > 200.0 )
		{
			$method = 5;

			// If the Ratio is Massive, Bail Out Completely as the Script May Throw Fatal Memory Limit Exceeded Errors
			// 	@note	Long Images of 10,000x50, 30,000x20 etc May Throw Fatal Error.
			if( $ratio > 1000.0 )
			{
				return null;
			}//end if
		}//end if

		// Sanitise User Input
		$filter	= new JFilterInput;
		$size 	= $filter->clean( $size, 'alnum' );
		$thumb 	= null;

		// Catch Potential Exceptions (lots of areas where one can be thrown in this process)
		try
		{
			$thumbnails = static::$image->createThumbs( $size, (int)$method, $tmpfolder );
		}
		catch( Exception $e )
		{
			$thumbnails = array();
		}//end try

		if( count( $thumbnails ) )
		{
			$thumbnail 			= current( $thumbnails );
			$thumb 				= new stdClass;
			$thumb->file 		= $cachename;
			$thumb->path 		= $thumbnail->getPath();
			$thumb->url 		= str_replace( ARKMEDIA_ROOT . ARKMEDIA_DS, JURI::root(), $thumbnail->getPath() );
			$thumb->properties 	= $thumbnail->getImageFileProperties( $thumbnail->getPath() );

			// Move & Rename the File from the Temp Location
			// ---------------------------------------------
			// Ideally We'd Want the Thumbnail Creation to Generate a Unique Name or Allow us to Name it for Placing Directly in the Cache.
			// But it Won't so we Generate to a tmp Directory Then Rename & Move it. (doing so in the root may affect duplicates?).
			if( JFile::move( $thumb->path, $cachepath ) )
			{
				// If the Thumbnail Successfully Moved then Update the Thumbnail Path
				$thumb->path 	= $cachepath;
				$thumb->url 	= $cacheurl;
			}//end if
		}//end if

		return $thumb;
	}//end function

	/**
	 * Get Image Properties From the Loaded Image.
	 *
	 * @return	object 	An Object of Image Properties & Dimensions
	 */
	public static function properties()
	{
		return ( !is_null( static::$image ) ) ? static::$image->getImageFileProperties( static::$image->getPath() ) : new stdClass;
	}//end function

	/**
	 * Get the Image Path From the Loaded Image.
	 *
	 * @param	bool	$url	Whether to Return a Full File Path or a URL Path
	 *
	 * @return	string	The Current Image's Path
	 */
	public static function path( $url = false )
	{
		$path = ( !is_null( static::$image ) ) ? static::$image->getPath() : false;

		if( $url )
		{
			$path = str_replace( ARKMEDIA_ROOT . ARKMEDIA_DS, JURI::root(), $path );
		}//end if

		return $path;
	}//end function

	/**
	 * Wipe Out the Thumbnail Cache for a Fresh Start.
	 *
	 * @return	void
	 */
	public static function clearCache()
	{
		if( JFolder::exists( ARKMEDIA_CACHE . ARKMEDIA_CACHE_THUMBS ) )
		{
			JFolder::delete( ARKMEDIA_CACHE . ARKMEDIA_CACHE_THUMBS );
		}//end if
	}//end function

	/**
	 * Take the Passed Thumbnail Path & Stream it to the Browser.
	 * 
	 * @param	string	$path		The Thumbnail Cache Path/Name
	 *
	 * @return	void
	 */
	public static function stream( $path = null )
	{
		// Add Cache Path (also protects from unwanted $path's being passed)
		$fullpath = ARKMEDIA_CACHE . ARKMEDIA_CACHE_THUMBS . ARKMEDIA_DS . $path;

		// Validate the File
		if( HelperFileSystem::exists( $fullpath, 'file' ) && is_readable( $fullpath ) && static::isImage( $fullpath ) )
		{
			// Get the File Mime Type
			// @see	http://php.net/manual/en/function.image-type-to-mime-type.php
			if( function_exists( 'exif_imagetype' ) )
			{
				$mime = image_type_to_mime_type( exif_imagetype( $fullpath ) );
			}
			else
			{
				// Incorrectly Setup Servers May Not Have exif Enabled so Try Something Else :/
				// @see	http://php.net/exif_imagetype#100734
				$size = getimagesize( $fullpath );
				$mime = ( $size ) ? $size['mime'] : false;
			}//end if

			// Check the Mime Type
			if( $mime && $mime !== 'application/octet-stream' )
			{
				// Open the File
				header( 'Content-type: ' . $mime );
				header( 'Content-length: ' . filesize( $fullpath ) );

				$file = @fopen( $fullpath, 'rb' );

				// Stream the File
				if( $file )
				{
					fpassthru( $file );
				}//end if
			}//end if
		}//end if

		jexit();
	}//end function
}//end class
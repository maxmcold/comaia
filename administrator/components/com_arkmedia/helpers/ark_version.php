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
use JFile, JText;

defined( '_JEXEC' ) or die;

/**
 * Ark Media Manager Version Mediator
 */
class HelperVersion
{
	/**
	 * @var		bool	Whether the Version Data Has Been Loaded Yet
	 */
	protected static $loaded;

	/**
	 * @var		object	The Loaded Ark Manifest
	 */
	protected static $manifest;

	/**
	 * @var		string	The Name of this Extension
	 */
	protected static $extension;

	/**
	 * @var		string	The Full Version Number (major.minor.patch.pre-release)
	 */
	protected static $version;

	/**
	 * @var		int		The Major Version Number
	 */
	protected static $major;

	/**
	 * @var		int		The Minor Version Number
	 */
	protected static $minor;

	/**
	 * @var		int		The Patch Version Number
	 */
	protected static $patch;

	/**
	 * @var		string	The Pre-Release Label (e.g. beta|alpha etc)
	 */
	protected static $suffix;

	/**
	 * @var		bool	Whether the Package is a Pro Package
	 */
	protected static $pro;

	/**
	 * @var		bool	Whether the Package is a Basic Package
	 */
	protected static $basic;

	/**
	 * @var		string	The Extension Release Date
	 *
	 * @todo	Plumb this Date into the Manifest.
	 */
	protected static $date;

	/**
	 * Return the Extension Name.
	 *
	 * @usage	HelperVersion::extension();
	 *
	 * @return  string 	Extension Name
	 */
	public static function extension()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$extension;
	}//end function

	/**
	 * Return the Full Version Number.
	 *
	 * @usage	HelperVersion::version();
	 *
	 * @return  string 	Version Number
	 */
	public static function version()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$version;
	}//end function

	/**
	 * Return the Full Major Number.
	 *
	 * @usage	HelperVersion::major();
	 *
	 * @return  string 	Major Number
	 */
	public static function major()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$major;
	}//end function

	/**
	 * Return the Full Minor Number.
	 *
	 * @usage	HelperVersion::minor();
	 *
	 * @return  string 	Minor Number
	 */
	public static function minor()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$minor;
	}//end function

	/**
	 * Return the Full Patch Number.
	 *
	 * @usage	HelperVersion::patch();
	 *
	 * @return  string 	Patch Number
	 */
	public static function patch()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$patch;
	}//end function

	/**
	 * Return the Full Pre-Release Label.
	 *
	 * @usage	HelperVersion::suffix();
	 *
	 * @return  string 	Pre-Release Label
	 */
	public static function suffix()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$suffix;
	}//end function

	/**
	 * Check Whether the Whether the Package is a Pro Package or Not.
	 *
	 * @usage	HelperVersion::isPro();
	 *
	 * @return  bool 	Pro Package Flag Flag
	 */
	public static function isPro()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$pro;
	}//end function

	/**
	 * Check Whether the Whether the Package is a Basic Package or Not.
	 *
	 * @usage	HelperVersion::isBasic();
	 *
	 * @return  bool 	Basic Package Flag Flag
	 */
	public static function isBasic()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$basic;
	}//end function

	/**
	 * Get the Pro Label.
	 *
	 * @usage	HelperVersion::pro();
	 *
	 * @return  string 	Pro Package Flag Flag
	 */
	public static function pro()
	{
		return JText::_( ARKMEDIA_JTEXT . 'VERSION_PRO' );
	}//end function

	/**
	 * Get the Basic Label.
	 *
	 * @usage	HelperVersion::basic();
	 *
	 * @return  string 	Basic Package Flag Flag
	 */
	public static function basic()
	{
		return JText::_( ARKMEDIA_JTEXT . 'VERSION_BASIC' );
	}//end function

	/**
	 * Retrieve the Manifest Object.
	 *
	 * @usage	HelperVersion::manifest();
	 *
	 * @return  object 	The Manifest XML Object
	 */
	public static function manifest()
	{
		if( !static::$loaded )
		{
			static::_load();
		}//end if

		return static::$manifest;
	}//end function

	/**
	 * Load the Version Data into the Available Class Variables.
	 *
	 * @return  void
	 */
	protected static function _load()
	{
		// Load in the Manifest
		if( !static::$manifest )
		{
			static::_manifest();
		}//end if

		if( static::$manifest )
		{
			// Set Extension
			if( isset( static::$manifest->packager ) )
			{
				static::$extension = (string)static::$manifest->packager;
			}//end if

			// Set Version Data
			if( isset( static::$manifest->version ) )
			{
				switch( (string)static::$manifest->version->attributes()->type )
				{
					case 'free' :
						static::$pro 	= false;
						static::$basic 	= true;
						break;

					case 'pro' :
						static::$pro 	= true;
						static::$basic 	= false;
						break;
				}//end switch

				// Set Full Version
				static::$version = (string)static::$manifest->version;

				// Sut Sub Versions
				list( static::$major, static::$minor, static::$patch ) = explode( '.', static::$version );

				// Is Pre-Release Present? Separate Out
				if( !is_numeric( static::$patch ) )
				{
					static::$suffix = preg_replace( '#[^a-zA-Z]#', '', static::$patch );
					static::$patch 	= preg_replace( '#[^0-9]#', '', static::$patch );
				}//end if
			}//end if
		}//end if

		// Set Loaded (even if it has failed)
		static::$loaded = true;
	}//end function

	/**
	 * Load the Extensions Manifest.
	 *
	 * @return  bool 	Was the Manifest Loaded?
	 */
	protected static function _manifest()
	{
		// Try Package First
		$file 		= JPATH_MANIFESTS . ARKMEDIA_DS . 'packages' . ARKMEDIA_DS . ARKMEDIA_PACKAGE . '.xml';

		// If Only Component Available Use this Manifest (not all data available)
		if( !JFile::exists( $file ) )
		{
			$file 	= ARKMEDIA_BE . ARKMEDIA_COMPONENT_ID . '.xml';
		}//end if

		if( JFile::exists( $file ) )
		{
			static::$manifest = simplexml_load_file( $file );

			if( static::$manifest )
			{
				return true;
			}//end if
		}//end if

		return false;
	}//end function
}//end class
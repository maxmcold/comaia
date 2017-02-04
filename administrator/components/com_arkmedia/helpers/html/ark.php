<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperVersion;

defined( '_JEXEC' ) or die;

/**
 * Render Ark Framework Elements.
 *
 * @usage		JHTML::_( ARKMEDIA_HTML_ID . 'ark.framework' );
 * 				Helper::html( 'ark.framework', array( 'overrides' => false ) );
 * 				Helper::html( 'ark.bootstrap', array( 'js' => false, 'fonts' => false ) );
 * 				Helper::html( 'ark.css', 'ark-file.min' );
 * 				Helper::html( 'ark.js', 'ark-file.min' );
 */
abstract class MediaHtmlArk
{
	/**
	 * @var		array	Internal Array Monitoring Layout Loads
	 */
	protected static $loaded 			= array();

	/**
	 * @var		array	Internal Array to manage dependency order of JS files
	 */
	protected static $js				= array();

	/**
	 * Load Ark Framework CSS.
	 *
	 * @note	Use static call caching to prevent multiple layout loads.
	 *
	 * @param	array	$options	Options to determine which media assets are loaded in
	 *
	 * @return	void
	 */
	public static function framework( $options = array() )
	{
		// Merge Options With Defaults
		$prototype	= array( 'overrides' => true, 'js' => true, 'ark' => true, 'fonts' => true );
		$options 	= (object)array_merge( $prototype, (array)$options );

		if( isset( $options->overrides ) && $options->overrides !== false )
		{
			static::js();
			static::css();
		}//end if

		if( isset( $options->js ) && $options->js !== false && !isset( static::$loaded[__METHOD__] ) )
		{
			// Ensure jQuery is Present
			JHTML::_( 'jquery.framework' );
			Helper::layout( 'js.base' );

			static::$loaded[__METHOD__] = true;
		}//end if

		if( isset( $options->ark ) && $options->ark !== false )
		{
			static::css( 'ark.min' );
		}//end if

		if( isset( $options->fonts ) && $options->fonts !== false )
		{
			static::css( 'ark-logo.min' );
			static::css( 'arkmime.min' );
		}//end if
	}//end function

	/**
	 * Load Bootstrap Framework CSS.
	 *
	 * @param	object	$options	Options to determine which media assets are loaded in
	 *
	 * @return	void
	 */
	public static function bootstrap( $options = array() )
	{
		// Merge Options With Defaults
		$prototype	= array( 'css' => true, 'js' => true, 'fonts' => true );
		$options 	= (object)array_merge( $prototype, (array)$options );

		if( $options->css !== false )
		{
			static::css( 'arkstrap.min' );
		}//end if

		if( $options->js !== false )
		{
			JHTML::_( 'bootstrap.framework' );
		}//end if

		if( $options->fonts !== false )
		{
			static::css( 'arkmoon.min' );
		}//end if
	}//end function

	/**
	 * Load UIKit Framework CSS.
	 *
	 * @param	object	$options	Options to determine which media assets are loaded in
	 *
	 * @return	void
	 */
	public static function uikit( $options = array() )
	{
		// Merge Options With Defaults
		$prototype	= array( 'css' => true, 'js' => true, 'fonts' => true );
		$options 	= (object)array_merge( $prototype, (array)$options );

		if( $options->css !== false )
		{
			static::css( 'uikit.min' );
		}//end if

		// Icons & Styles Come Together
		if( $options->js !== false && $options->fonts !== false )
		{
			// Ensure jQuery is Present
			JHTML::_( 'jquery.framework' );
			static::js( 'uikit.min' );
		}//end if
	}//end function

	/**
	 * Load a CSS Media File.
	 *
	 * @see		static::js() @note;
	 *
	 * @param	string	$file		File Name
	 * @param	string	$folder		Path to File
	 * @param	array	$attribs	Attributes to be added to the stylesheet
	 * @param	bool	$relative	Path to File is Relative to /media Folder
	 * @param	bool	$version	Append The Extension's Version Number to Clear Cache on Update
	 *
	 * @return	void
	 */
	public static function css( $file = 'overrides', $folder = 'arkmedia/', $attribs = array(), $relative = true, $version = true )
	{
		$path = JHTML::stylesheet( $folder . $file . '.css', $attribs, $relative, $version );

		if( $path && $version )
		{
			Helper::add( 'helper', 'version' );

			JFactory::getDocument()->addStylesheetVersion( $path, HelperVersion::version() );
		}//end if
	}//end function

	/**
	 * Load a JS Media File.
	 *
	 * @note	We use JHTML::script() to find the relative file path for us
	 * 			To allow for media folder interpretting.
	 *
	 * @param	string	$file			File Name
	 * @param	string	$folder			Path to File
	 * @param	bool	$framework		Load in Joomla's Framework?
	 * @param	bool	$relative		Path to File is Relative to /media Folder
	 * @param	string	$id				DB folder & element ID for other plugins to use as a dependency (format: 'folder:element')
	 * @param	array	$dependencies	Array of master JS file IDs that must load before this script (will adopt the master $version value)
	 * @param	bool	$version		Append The Extension's Version Number to Clear Cache on Update
	 *
	 * @return	void
	 */
	public static function js( $file = 'overrides', $folder = 'arkmedia/', $framework = false, $relative = true, $id = null, $dependencies = array(), $version = true )
	{
		$path = JHTML::script( $folder . $file . '.js', $framework, $relative, true );

		if( $path )
		{
			// Does this file rely on other files?
			if( count( $dependencies ) )
			{
				foreach( $dependencies as $dependency )
				{
					if( !isset( static::$js[$dependency] ) )
					{
						static::$js[$dependency] = array();
					}//end if

					static::$js[$dependency][] = $path;
				}//end foreach

				return;
			}//end if

			// Add script
			static::_js( $path, $version );

			// Does this file have other dependent files waiting for it to load?
			if( isset( static::$js[$id] ) )
			{
				foreach( static::$js[$id] as $dependency )
				{
					static::_js( $dependency, $version );
				}//end foreach
			}//end if
		}//end if
	}//end function

	/**
	 * Add a JS Media File to the Document.
	 *
	 * @note	Unfortunately JHTML::script() doesn't allow for any version insertion so
	 * 			We must manually handle inclusion with addScriptVersion();
	 *
	 * @param	string	$path			Path to File
	 * @param	bool	$version		Append The Extension's Version Number to Clear Cache on Update
	 *
	 * @return	void
	 */
	protected static function _js( $path = null, $version = null )
	{
		$doc = JFactory::getDocument();

		if( $version )
		{
			Helper::add( 'helper', 'version' );

			$doc->addScriptVersion( $path, HelperVersion::version() );
		}
		else
		{
			$doc->addScript( $path );
		}//end if
	}//end function
}//end class
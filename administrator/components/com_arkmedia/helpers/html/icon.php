<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

/**
 * Render an IcoMoon/Glyphicon HTML Icon.
 *
 * @usage		JHTML::_( ARKMEDIA_HTML_ID . 'icon.icomoon' );
 * 				Helper::html( 'icon.icomoon', 'upload' );
 * 				Helper::html( 'icon.uikit', 'bars' );
 * 				Helper::html( 'icon.glyphicon', 'calendar' );
 * 				Helper::html( 'icon.mime', 'css', array( 'format' => 'class' ) );
 * 				Helper::html( 'icon.uikit', 'search', array( 'extra' => 'uk-text-muted' ) );
 * 				Helper::html( 'icon.uikit', 'close', array( 'justify' => true, 'extra' => array( 'uk-text-danger', 'uk-hidden' ) ) );
 */
abstract class MediaHtmlIcon
{
	/**
	 * @var		array	Array of original $options Before They Are Overriden for Instantiating
	 */
	protected static $defaults 		= array( 'icomoon' => null, 'uikit' => null, 'glyphicon' => null, 'mime' => null );

	/**
	 * @var		array	A Collection $options That Define How the Icon is Rendered
	 */
	protected static $options 		= array( 
		'icomoon' 	=> array(
						'prefix'	=> 'icon-',
						'extra'		=> false,
						'attrs'		=> array()
		),

		'uikit' 	=> array(
						'prefix'	=> 'uk-icon-',
						'justify'	=> false,
						'extra'		=> false,
						'extras'	=> array( 
												'justify' => 'uk-icon-justify'
											),
						'attrs'		=> array()
		),

		'glyphicon' => array(
						'prefix'	=> 'glyphicon glyphicon-',
						'extra'		=> false,
						'attrs'		=> array()
		),

		'mime' 		=> array(
						'prefix'	=> 'arkmime-',
						'extra'		=> false,
						'attrs'		=> array(),
						'format' 	=> false
		)
	);

	/**
	 * Return an IcoMoon Icon.
	 *
	 * @param	string	$icon		The String Name of the Icon
	 * @param	mixed	$options	Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function icomoon( $icon = null, $options = array() )
	{
		static::setOptions( $options, __FUNCTION__ );

		$options 	= static::getOptions( __FUNCTION__ );
		$attrs 		= array_merge( array( 'class' => $options->get( 'prefix' ) . $icon ), (array)$options->get( 'attrs' ) );

		// Add extra classes
		if( $options['extra'] )
		{
			$attrs['class'] .= chr( 32 ) . ( is_array( $options['extra'] ) ? implode( chr( 32 ), $options['extra'] ) : $options['extra'] );
		}//end if

		return '<i ' . JArrayHelper::toString( $attrs ) . '></i>';
	}//end function

	/**
	 * Return a UIkit Icon.
	 *
	 * @param	string	$icon		The String Name of the Icon
	 * @param	mixed	$options	Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function uikit( $icon = null, $options = array() )
	{
		static::setOptions( $options, __FUNCTION__ );

		$options 	= static::getOptions( __FUNCTION__ );
		$attrs 		= array_merge( array( 'class' => $options->get( 'prefix' ) . $icon ), (array)$options->get( 'attrs' ) );

		// Add justification
		if( $options->get( 'justify' ) )
		{
			$attrs['class'] .= chr( 32 ) . $options['extras']->justify;
		}//end if

		// Add extra classes
		if( $options['extra'] )
		{
			$attrs['class'] .= chr( 32 ) . ( is_array( $options['extra'] ) ? implode( chr( 32 ), $options['extra'] ) : $options['extra'] );
		}//end if

		return '<i ' . JArrayHelper::toString( $attrs ) . '></i>';
	}//end function

	/**
	 * Return a GlyphIcon Icon.
	 *
	 * @param	string	$icon		The String Name of the Icon
	 * @param	mixed	$options	Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function glyphicon( $icon = null, $options = array() )
	{
		static::setOptions( $options, __FUNCTION__ );

		$options 	= static::getOptions( __FUNCTION__ );
		$attrs 		= array_merge( array( 'class' => $options->get( 'prefix' ) . $icon ), (array)$options->get( 'attrs' ) );

		// Add extra classes
		if( $options['extra'] )
		{
			$attrs['class'] .= chr( 32 ) . ( is_array( $options['extra'] ) ? implode( chr( 32 ), $options['extra'] ) : $options['extra'] );
		}//end if

		return '<i ' . JArrayHelper::toString( $attrs ) . '></i>';
	}//end function

	/**
	 * Return a Mime Icon as a HTML Image or Just the Path to the File (Relative to the Media Folder or Template Override).
	 *
	 * @param	string	$mime		The String Name of the File Extension Type
	 * @param	mixed	$options	Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function mime( $mime = null, $options = array() )
	{
		static::setOptions( $options, __FUNCTION__ );

		$options 	= static::getOptions( __FUNCTION__ );
		$format		= $options->get( 'format' );

		switch( JString::strtolower( $mime ) )
		{
			// Automatic/Unknown (Set to Default if a Mime Icon Cannot be Found)
			default :
				$icon = 'unknown';
				break;

			// Font Formats
			case 'eot' : case 'woff' : 
				$icon = 'ttf';
				break;

			// AI Formats
			case 'ai' : case 'eps' :
				$icon = 'ai';
				break;

			// Flash Formats
			case 'fla' : case 'as' : case 'asc' : case 'f4v' : case 'jsfl' : case 'swc' : case 'swz' : 
				$icon = 'fla';
				break;

			// PSD Formats
			case 'psd' : case 'psb' :
				$icon = 'psd';
				break;

			// Adobe Formats
			case 'br' : case 'dw' : case 'indd' : case 'pdf' : case 'ppj' :
				$icon = $mime;
				break;

			// System Formats
			case 'cmd' : case 'app' : case 'dll' : case 'exe' : case 'bat' :
				$icon = 'cmd';
				break;

			// CSS Formats
			case 'css' : case 'less' : case 'scss' : case 'sass' :
				$icon = 'css';
				break;

			// Plain Text Formats
			case 'txt' : case 'log' :
				$icon ='txt';
				break;

			// Rich Text Formats
			case 'doc' : case 'docx' : case 'msg' : case 'pages' : case 'rtf' :
				$icon ='doc';
				break;

			// Audio Formats
			case 'mp3' : case 'aac' : case 'aiff' : case 'm4a' : case 'm4p' : case 'mid' : case 'midi' : case 'ogg' : case 'wav' : case 'wma' :
				$icon = 'mp3';
				break;

			// Video Formats
			case 'mp4' : case 'avi' : case 'divx' : case 'f4v' : case 'flv' : case 'hdmov' : case 'm4v' : case 'mov' : case 'movie' : case 'mpg' : case 'mpeg' : case 'swf' : case 'vob' : case 'wmv' : case 'asf' : case 'xvid' :
				$icon = 'mp4';
				break;

			// Open Office Formats
			case 'odf' : case 'odt' : case 'ods' : case 'odp' :
				$icon = 'odf';
				break;

			// Raster Image Formats
			case 'bmp' : case 'gif' : case 'jpg' : case 'jpeg' : case 'png' : case 'tif' : case 'tiff' : case 'ico' : case 'icon' :
			// Vector Image Formats
			case 'svg' :
				$icon = 'png';
				break;

			// HTML Formats
			case 'html' : case 'htm' : case 'xhtml' : 
				$icon = 'html';
				break;

			// XML Formats
			case 'xml' : 
				$icon = 'xml';
				break;

			// Code Formats
			case 'js' : case 'asp' : case 'aspx' : case 'c' : case 'pl' : case 'py' : case 'php' :
				$icon = 'js';
				break;

			// Compressed Formats
			case 'zip' : case '7z' : case 'dmg' : case 'gz' : case 'gzip' : case 'pkg' : case 'rar' : case 'tar' : case 'tar.gz' : case 'tgz' : case 'zipx' :
				$icon = 'zip';
				break;

			// Spreadsheet Formats
			case 'xls' : case 'xlsx' : case 'csv' :
				$icon = 'xls';
				break;

			// PowerPoint Formats
			case 'ppt' : case 'pptx' :
				$icon = 'ppt';
				break;

			// Other Formats
			case 'bak' : case 'tmp' : case 'old' :
				$icon = 'blank';
				break;
		}//end switch

		$attrs = array_merge( array( 'class' => $options->get( 'prefix' ) . $icon ), (array)$options->get( 'attrs' ) );

		// Add extra classes
		if( $options['extra'] )
		{
			$attrs['class'] .= chr( 32 ) . ( is_array( $options['extra'] ) ? implode( chr( 32 ), $options['extra'] ) : $options['extra'] );
		}//end if

		// Return in the Format Requested (Default : Icon)
		switch( $format )
		{
			case 'class':
				return $options->get( 'prefix' ) . $icon;

			default:
			case 'icon':
				return '<i ' . JArrayHelper::toString( $attrs ) . '></i>';
		}//end switch
	}//end function

	/**
	 * Set Options Globally For Use in Functions.
	 *
	 * @param	array  	$options	Array of Options to Set
	 * @param	string  $function	The Function Name to Namespace the Options to
	 *
	 * @return  void
	 */
	protected static function setOptions( Array $options = array(), $function = null )
	{
		// Store Defaults to Avoid Multiple Calls Using Existing Options
		if( is_null( static::$defaults[$function] ) )
		{
			static::$defaults[$function] 	= static::$options[$function];
		}//end if

		// Ensure Options Are Defaulted
		static::$options[$function] 		= static::$defaults[$function];

		// Set Options
		foreach( $options as $key => $val )
		{
			// Exclude Invalid Keys
			if( in_array( $key, array_keys( static::$options[$function] ) ) )
			{
				// Merge Arrays || Add Plain Value
				if( is_array( $val ) && is_array( static::$options[$function][$key] ) )
				{
					static::$options[$function][$key] = array_merge( static::$options[$function][$key], $val );
				}
				elseif( !is_null( $val ) )
				{
					// null === !isset()
					// Use false or '' to clear a global property
					static::$options[$function][$key] = $val;
				}//end if
			}//end if
		}//end foreach
	}//end function

	/**
	 * Get Options Globally For Use in Functions (& externally).
	 *
	 * @param	string  $function	The Function Name of the Namespace that the Options Belong to
	 *
	 * @return  object 	An Object List of Icon Options
	 */
	public static function getOptions( $function = null )
	{
		return new JRegistry( static::$options[$function] );
	}//end function
}//end class
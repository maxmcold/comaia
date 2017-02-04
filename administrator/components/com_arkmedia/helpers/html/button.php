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
 * Render a link HTML tag.
 *
 * @usage		JHTML::_( ARKMEDIA_HTML_ID . 'button.a' );
 * 				Helper::html( 'button.a', array( 'text' => 'BUTTON TEXT', 'icon' => 'search', 'bootstrap' => true, 'data' => array( 'id' => '34' ) ) )
 * 				Helper::html( 'button.a', array( 'text' => 'BUTTON TEXT', 'href' => 'www.fish.com', 'onclick' => 'doFunc();', 'style' => 'width : 100%;', 'title' => 'Different to TEXT' ) )
 * 				Helper::html( 'button.a', array( 'text' => 'BUTTON TEXT', 'icon' => 'cog', 'colour' => 'info', 'size' => 'large', 'extra' => 'addition_classes' ) )
 *				Helper::html( 'button.input', array( 'name' => 'form[name]', 'value' => 'option1' ) )
 *				Helper::html( 'button.input', array( 'text' => 'form[name]', 'value' => 'option1', 'type' => 'submit' ) )
 *				Helper::html( 'button.button', array( 'name' => 'form[name]', 'value' => 'option1', 'id' => 'button' ) )
 */
abstract class MediaHtmlButton
{
	/**
	 * @var		object  Object of Original $_options & $_priority Before They Are Overriden For Instantiating
	 */
	protected static $_defaults 	= array( 'options' => null, 'priority' => null );

	/**
	 * @var		array  A Collection on $_options That Are HTML Input Specific Attributes
	 */
	protected static $_input_attrs 	= array( 'type', 'value', 'name', 'autocomplete' );

	/**
	 * @var		array  A Collection on $_options That Aren't HTML Attributes
	 */
	protected static $_non_attrs 	= array( 
		'framework',
		'bootstrap',
		'active',
		'text',
		'pretext',
		'posttext',
		'label',
		'icon',
		'iconextra',
		'extra',
		'css',
		'prefixes',
		'suffixes',
		'permission',
		'action',
		'group',
		'colour',
		'size',
		'tag',
		'html'
	);

	/**
	 * @var		array  An Array Of Priority Settings/Options That Need Handling Before the Rest of the $_options
	 */
	protected static $_priority 	= array(
		'framework' 	=> 'bootstrap',
		'html' 			=> false
	);

	/**
	 * @var		array  A Collection $_options That Define How The Button Is Rendered
	 *
	 * @note	JRegistry Doesn't Allow Merging Data Over Empty Arrays so Set Them to null.
	 */
	protected static $_options 		= array(
		'bootstrap' 	=> false,
		'active' 		=> false,
		'text' 			=> null,
		'pretext' 		=> null,
		'posttext' 		=> null,
		'label' 		=> null,
		'value' 		=> null,
		'name' 			=> null,
		'id' 			=> null,
		'href' 			=> 'javascript:void(0);',
		'autocomplete' 	=> 'off',
		'disabled' 		=> null,
		'class' 		=> null,
		'icon' 			=> null,
		'iconextra' 	=> null,
		'extra' 		=> null,
		'style' 		=> null,
		'onclick' 		=> null,
		'target' 		=> null,
		'title' 		=> null,
		'alt' 			=> null,
		'type' 			=> 'button',
		'data' 			=> null, // Array
		'css'			=> null, // Array
		'prefixes'		=> null, // Array
		'suffixes'		=> array( 
							'label' => '_lbl'
						),
	);

	/**
	 * @var		array  A Collection of Framework Options to be Merged Into $_options Once the Framework Has Been Decided
	 */
	protected static $_frameworks	= array(
		'bootstrap' 	=> array( 
			'prefixes' 		=> array( 
				'data' 		=> 'data-',
				'button' 	=> 'btn btn-',
				'size' 		=> 'btn-',
				'icon' 		=> 'icon-'
			),
			'css' 			=> array( 
				'active' 	=> 'active'
			)
		),
		'uikit' 		=> array( 
			'prefixes' 		=> array( 
				'data' 		=> 'data-', // Although 'data-uk-' Is Available This Impeeds Our Ability To Use Custom Data Attrs
				'button' 	=> 'uk-button uk-button-',
				'size' 		=> 'uk-button-',
				'icon' 		=> 'uk-icon-'
			),
			'css' 			=> array( 
				'active' 	=> 'uk-active'
			)
		)
	);

	/**
	 * Return an <a> Tag.
	 *
	 * @param	mixed  $options		Array/Object of Display Options
	 *
	 * @return  string	HTML tag
	 */
	public static function a( $options = array() )
	{
		static::_setOptions( $options, array( 'type', 'alt' ) );
		static::_bootstrap();

		$attrs = array_filter( static::_getOptions( array_merge( static::$_input_attrs, static::$_non_attrs, array( 'alt' ) ) ) );

		return '<a ' . JArrayHelper::toString( $attrs ) . '>' . static::_text() . '</a>';
	}//end function

	/**
	 * Return a <button> Tag.
	 *
	 * @param	mixed  $options		Array/Object of Display Options
	 *
	 * @return  string	HTML tag
	 */
	public static function button( $options = array() )
	{
		static::_setOptions( $options, array( 'href', 'target' ) );
		static::_bootstrap();

		// Validate Type
		if( !in_array( static::get( 'type' ), array( 'button', 'submit', 'reset' ) ) )
		{
			static::remove( 'type' );
		}//end if

		$attrs = array_filter( static::_getOptions( array_merge( static::$_non_attrs, array( 'href', 'autocomplete' ) ) ) );

		return '<button ' . JArrayHelper::toString( $attrs ) . '>' . static::_text() . '</button>';
	}//end function

	/**
	 * Return an <input> Tag.
	 *
	 * @param	mixed  $options		Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function input( $options = array() )
	{
		static::_setOptions( $options, array( 'href', 'icon', 'target' ) );
		static::_bootstrap();

		$attrs = array_filter( static::_getOptions( array_merge( static::$_non_attrs, array( 'href', 'icon' ) ) ) );

		return '<input ' . JArrayHelper::toString( $attrs ) . ' />';
	}//end function

	/**
	 * Return a <radio> Tag.
	 *
	 * @param	mixed  $options		Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function radio( $options = array() )
	{
		static::_setOptions( $options, array( 'href', 'type', 'target' ) );
		static::_bootstrap();
		static::set( 'type', __FUNCTION__ );

		return static::_label( $options );
	}//end function

	/**
	 * Return a <checkbox> Tag.
	 *
	 * @param	mixed  $options		Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	public static function checkbox( $options = array() )
	{
		static::_setOptions( $options, array( 'href', 'type', 'target' ) );
		static::_bootstrap();
		static::set( 'type', __FUNCTION__ );

		return static::_label( $options );
	}//end function

	/**
	 * Return a <label> Tag Wrapped Round an <input>.
	 *
	 * @param	mixed  $options		Array/Object of display options
	 *
	 * @return  string	HTML tag
	 */
	protected static function _label( $options = array() )
	{
		$data 	= static::get( 'data', array(), false, true ); // Need Array for JArrayHelper
		$label	= array( 'title' => static::get( 'title' ), 'class' => static::get( 'class' ), 'for' => static::get( 'id' ), 'id' => static::get( 'id' ) . static::get( 'suffixes.label' ), 'data' => $data );
		$attrs 	= array_filter( static::_getOptions( array_merge( static::$_non_attrs, array( 'href', 'class', 'data', 'alt', 'title' ) ) ) );

		return '<label ' . JArrayHelper::toString( $label ) . '><input ' . JArrayHelper::toString( $attrs ) . ' />' . static::_text() . '</label>';
	}//end function

	/**
	 * Test Whether Data is a Loopable Item Such as an Array or Object.
	 *
	 * @param	string  $data		Data to Check
	 *
	 * @return  bool	Is a Loopable Item
	 */
	protected static function _loopable( $data = null )
	{
		return is_object( $data ) || is_array( $data );
	}//end function

	/**
	 * Return a Global Option (If Key Appears in Defaults).
	 *
	 * @note	Casting is Opt-In Because Most Common Usage is: get( 'opt', false );
	 * 			This Scenario Doesn't Want Non-Default Values to be Cast.
	 *
	 * @param	string  $option		Option Key to Find & Return
	 * @param	string  $default	Option Default if No Key is Found
	 * @param	string  $filter		Filter the Input Value
	 * @param	bool 	$cast		Boolean to Cast Return Data (opt-in)
	 *
	 * @return  mixed	Global Option Value
	 */
	public static function get( $option = null, $default = null, $filter = true, $cast = false )
	{
		$value = static::$_options->get( $option, $default );

		// Filter Return Value?
		if( $filter )
		{
			if( static::_loopable( $value ) )
			{
				foreach( $value as &$val )
				{
					$val = static::_special( $val );
				}//end foreach
			}
			else
			{
				$value = static::_special( $value );
			}//end if
		}//end if

		// Smart Cast Return Data By Analysing the $default Type
		if( $cast )
		{
			if( is_int( $default ) && !is_int( $value ) )
			{
				$value = (int)$value;
			}//end if

			if( is_string( $default ) && !is_string( $value ) )
			{
				$value = (string)$value;
			}//end if

			if( is_bool( $default ) && !is_bool( $value ) )
			{
				$value = (bool)$value;
			}//end if

			if( is_array( $default ) && !is_array( $value ) )
			{
				$value = (array)$value;
			}//end if

			if( is_object( $default ) && !is_object( $value ) )
			{
				$value = (object)$value;
			}//end if
		}//end if

		return $value;
	}//end function

	/**
	 * Set a Global Option (If Key Appears in Defaults).
	 *
	 * @param	string  $option		Option Key to Set
	 * @param	string  $value		Value to Set/Clear
	 * @param	bool  	$new		Prevent New Keys Being Added
	 *
	 * @return  mixed	The Set Value or false on Failure
	 */
	public static function set( $option = null, $value = null, $new = false )
	{
		// Prevent Adding of New Keys if Forbidden
		if( $new || ( !$new && static::exists( $option ) ) )
		{
			return static::$_options->set( $option, $value );
		}//end if

		return false;
	}//end function

	/**
	 * Check if a Global Option Is Set.
	 *
	 * @note	Although JRegistry Has an Exists Method it Fails When Looking for Keys With null Values.
	 * 			So We Must Do Additional Checks.
	 * 			e.g. $reg = array( 'key' => null ); $reg->exists( 'key' ) === false;
	 * 			
	 * @todo	Our Checks Will Currently Fail on Blank Sub Options
	 *
	 * @param	string  $option		Option Key to Check
	 *
	 * @return  bool	Set Status
	 */
	public static function exists( $option = null )
	{
		// Perform Basic Key/Path Check
		if( static::$_options->exists( $option ) )
		{
			return true;
		}//end if

		// If Path Check Failed Manually Check Array
		return array_key_exists( $option, static::$_options->toArray() );
	}//end function

	/**
	 * Remove a Global Option.
	 *
	 * @note	JRegistry Has No Removal Method So Unless We Get the Whole Data to Unset Then Re-Insert,
	 * 			We Must Perform a Soft Unset By Setting the Value to Null.
	 *
	 * @param	string  $option		Option Key to Unset
	 * @param	bool 	$soft		Soft Delete an Option (opt-in)
	 *
	 * @return  void
	 */
	public static function remove( $option = null, $soft = false )
	{
		if( !$option )
		{
			return;
		}//end if

		if( $soft )
		{
			static::set( $option, null, false );
		}
		else
		{
			// Get Options
			$options = static::$_options->toArray();

			// Search & Remove Option
			static::_remove( explode( '.', $option ), $options );

			// Re-Build Registry :(
			static::$_options = new JRegistry( $options );
		}//end if
	}//end function

	/**
	 * Recursively Search & Remove a Global Option.
	 *
	 * @param	string  $segments	Option Key Segments to Traverse
	 *
	 * @return  void
	 */
	protected static function _remove( $segments = array(), &$options = array() )
	{
		$segment = array_shift( $segments );

		// Key Doesn't Exist
		if( !isset( $options[$segment] ) )
		{
			return;
		}//end if

		// If Last Branch Remove Otherwise Keep Traversing
		if( !count( $segments ) )
		{
			unset( $options[$segment] );
		}
		else
		{
			static::_remove( $segments, $options[$segment] );
		}//end if
	}//end function

	/**
	 * Return All Global Options.
	 *
	 * @param	array  	$exclude	Optional Array of keys to exclude returning
	 *
	 * @return  array	Global Options Array
	 */
	protected static function _getOptions( $exclude = array() )
	{
		$options = static::$_options->toArray();

		// Strip Exclusion Options If Instructed
		if( count( $exclude ) )
		{
			$options = array_diff_key( $options, array_flip( $exclude ) );
		}//end if

		return $options;
	}//end function

	/**
	 * Set Options Globally For Use in Functions.
	 *
	 * @note	When Merging Registry's Recursively, JRegistry Doesn't Allow Merging Data Over Empty Arrays.
	 * 			Whereas When Not Recursively, Entire Sub $options Are Overwritten.
	 * 			To Get Round This All $default Keys Must be Non-Empty Arrays or Null.
	 *
	 * @param	mixed  	$options	Array/Object of Options To Set
	 * @param	array  	$exclude	Array of Keys To Exclude Setting If They Appear In Options
	 *
	 * @return  void
	 */
	protected static function _setOptions( $options, $exclude = array() )
	{
		// Store Defaults to Avoid Multiple Calls Using Existing Options
		if( is_null( static::$_defaults['options'] ) )
		{
			static::$_defaults['options'] 	= static::$_options;
			static::$_defaults['priority'] 	= static::$_priority;
		}//end if

		// Ensure Options Are Defaulted
		static::$_options 	= new JRegistry( static::$_defaults['options'] );
		static::$_priority 	= new JRegistry( static::$_defaults['priority'] );

		// Cast Options For Use in Array Functions
		$options			= (array)$options;

		// Set Priority Options First (trim non-priority keys from $options)
		$priority			= array_intersect_key( $options, static::$_priority->toArray() );

		// Merge User Override $options Over Defaults
		static::$_priority->merge( new JRegistry( $priority ), true );

		// Set Framewok Options
		static::_framework();

		// Strip Out Exclusion Options
		$options = array_diff_key( $options, array_flip( $exclude ) );

		// Merge User Override $options Over Defaults
		static::$_options->merge( new JRegistry( $options ), true );

		// Get Prefixes
		$datakey 			= static::get( 'prefixes.data' );
		$iconkey 			= static::get( 'prefixes.icon' );
		$dataopts 			= clone static::get( 'data', (object)array(), false, true );

		// Format Data (prepend data attr)
		// @note	We Need Cloned Object as We Add Options that Shouldn't be Iterated Over
		foreach( $dataopts as $key => $val )
		{
			$prekey			= str_replace( $datakey, '', $key );
			$oldkey 		= 'data.' . $prekey;
			$newkey 		= 'data.' . $datakey . $prekey;

			// Set New Prepended Attr
			static::set( $newkey, $val, true );

			// Ensure Non Prepended Values are Unset to Avoid Duplication
			static::remove( $oldkey );
		}//end foreach

		// Cast Options
		$options			= (object)$options;

		// Set Additional Class Options (if class options hasn't been disabled/cleared || if the class isn't the default button class)
		if( !isset( $options->class ) || ( isset( $options->class ) && static::get( 'class' ) && strpos( (string)static::get( 'prefixes.button' ), static::get( 'class' ) ) !== false ) )
		{
			// Set Colour
			if( !in_array( 'colour', $exclude ) )
			{
				static::_setColour( $options );
			}//end if

			// Set Size
			if( !in_array( 'size', $exclude ) )
			{
				static::_setSize( $options );
			}//end if

			// Set Active
			if( !in_array( 'active', $exclude ) )
			{
				static::_setActive( $options );
			}//end if
		}//end if

		// Get HTML Switch
		$html	= static::$_priority['html'];

		// Get Data Fallbacks
		$text	= static::get( 'text', false, !$html );
		$title	= static::get( 'title', false, !$html );

		// Build Text Data Fallbacks
		$text	= ( !$text && static::get( 'label', false, !$html ) ) ? static::get( 'label', '', !$html ) : $text;
		$text	= ( !$text && static::get( 'name', false, !$html ) ) ? static::get( 'name', '', !$html ) : $text;
		$text	= ( !$text && static::get( 'value', false, !$html ) ) ? static::get( 'value', '', !$html ) : $text;

		// Build Alt Attr Fallbacks
		$title	= ( !$title && $text ) ? strip_tags( $text ) : strip_tags( $title );
		$alt	= ( !static::get( 'alt', false, !$html ) && $title ) ? strip_tags( $title ) : strip_tags( static::get( 'alt', '', !$html ) );
		
		// Build Class Fallbacks
		$class	= ( static::get( 'extra' ) ) ? trim( trim( static::get( 'class' ) ) . chr( 32 ) . static::get( 'extra' ) ) : static::get( 'class' );
		$icon	= ( static::get( 'icon' ) ) ? $iconkey . str_replace( $iconkey, '', static::get( 'icon' ) ) : static::get( 'icon' );

		// Set Updated Data
		static::set( 'text', $text );
		static::set( 'title', $title );
		static::set( 'alt', $alt );
		static::set( 'class', $class );
		static::set( 'icon', $icon );
	}//end function

	/**
	 * Set Button Colour as CSS Class.
	 *
	 * @param	object  $options	Object of Options To Set
	 *
	 * @return  void
	 */
	protected static function _setColour( $options )
	{
		$prefix = static::get( 'prefixes.button' );

		switch( ( isset( $options->colour ) ? $options->colour : null ) )
		{
			default :
			case false : 
			case '' :
			case 'default' :
				$colour = 'default';
				break;

			case 'plain' : // Custom
				$colour = 'default plain';
				break;

			case 'primary' :
			case 'link' :
				$colour = $options->colour;
				break;

			case 'success' : case 'green' :
				$colour = 'success';
				break;

			case 'info' : case 'blue' : case 'lightblue' : case 'light-blue' : case 'aqua' :
				$colour = 'info';
				break;

			case 'warning' : case 'orange' : case 'amber' : case 'yellow' :
				$colour = 'warning';
				break;

			case 'danger' : case 'red' :
				$colour = 'danger';
				break;
		}//end switch

		static::set( 'class', trim( trim( static::get( 'class' ) ) . chr( 32 ) . $prefix . $colour ) );
	}//end function

	/**
	 * Set Button Size as CSS Class.
	 *
	 * @param	object  $options	Object of Options To Set
	 *
	 * @return  void
	 */
	protected static function _setSize( $options )
	{
		$prefix = static::get( 'prefixes.size' );

		switch( ( isset( $options->size ) ? $options->size : null ) )
		{
			default : return;

			case 'block' :
				$size = $options->size;
				break;

			case 'large' : case 'lg' :
				$size = 'lg';
				break;

			case 'small' : case 'sm' :
				$size = 'sm';
				break;

			case 'mini' : case 'xs' :
				$size = 'xs';
				break;
		}//end switch

		static::set( 'class', trim( trim( static::get( 'class' ) ) . chr( 32 ) . $prefix . $size ) );
	}//end function

	/**
	 * Set Button Active State in CSS Class.
	 *
	 * @param	object  $options	Object Of Options To Set
	 *
	 * @return  void
	 */
	protected static function _setActive( $options )
	{
		$prefix = static::get( 'css.active' );

		if( isset( $options->active ) && $options->active )
		{
			static::set( 'class', trim( trim( static::get( 'class' ) ) . chr( 32 ) . static::get( 'css.active' ) ) );
		}//end if
	}//end function

	/**
	 * Initiate Bootstrap jQuery on Button.
	 *
	 * @return  void
	 */
	protected static function _bootstrap()
	{
		$bootstrap = static::get( 'bootstrap' );

		if( $bootstrap )
		{
			// Determine the Bootstrap Type
			switch( $bootstrap )
			{
				default :
					$toggle = 'button';
					break;

				// Supported Bootstraps (some require/use further data attr's to be set)
				case 'modal' :		// Uses: data-target="#ID" data-keyboard="true"
				case 'dropdown' :	// Uses: data-target="#ID"
				case 'tab' :
				case 'pill' :
				case 'tooltip' :	// Uses: data-placement="top" data-animation="true" data-html="true" data-title="TITLE" data-container="#ID"
				case 'popover' :	// Uses: data-placement="top" data-animation="true" data-html="true" data-title="TITLE" data-content="TEXT" data-container="#ID"
				case 'collapse' :	// Uses: data-parent="#ID" || data-target="#ID"
					$toggle = $bootstrap;
					break;
			}//end switch

			// Set Relevant Data Attr (Will be out-prioritised if 'data-toggle' already passed in)
			static::set( 'data', array_merge( array( 'data-toggle' => $toggle ), static::get( 'data', array(), false, true ) ) );
		}//end if
	}//end function

	/**
	 * Build the Buttons Text Value.
	 *
	 * @return  string	HTML Text Entry
	 */
	protected static function _text()
	{
		$html		= array();
		$text		= trim( static::get( 'text', false, !static::$_priority->get( 'html' ) ) );

		// Add Pretext
		if( static::get( 'pretext' ) )
		{
			$html[]	= static::get( 'pretext', false, !static::$_priority->get( 'html' ) );
		}//end if

		// Add Text Icon
		if( static::get( 'icon' ) )
		{
			$extra	= ( static::get( 'iconextra' ) ) ? chr( 32 ) . static::get( 'iconextra' ) : '';

			$html[]	= '<i class="' . static::get( 'icon' ) . $extra . '"></i>';
		}//end if

		// Add Main Text (trimmed to prevent just whitespace entries)
		if( $text )
		{
			$html[]	= $text;
		}//end if

		// Add Suffix/Post Text
		if( static::get( 'posttext' ) )
		{
			$html[]	= static::get( 'posttext', false, !static::$_priority->get( 'html' ) );
		}//end if

		return implode( chr( 32 ), $html );
	}//end function

	/**
	 * HTML Special Chars Text.
	 *
	 * @param	string  	$text		The Text To Make Safe
	 * @param	constant  	$flag		A Flags Which Specifies How To Handle Quotes, Invalid Code etc
	 * @param	string  	$encoding	Defines Encoding Used In Conversion
	 *
	 * @return  void
	 */
	protected static function _special( $text, $flag = ENT_COMPAT, $encoding = 'UTF-8' )
	{
		return ( is_string( $text ) ) ? htmlspecialchars( $text, $flag, $encoding ) : $text;
	}//end function

	/**
	 * Set Framework Options to the Root Options Before User Options Are Merged in.
	 *
	 * @return  void
	 */
	protected static function _framework()
	{
		$framework = static::$_priority->get( 'framework' );

		if( isset( static::$_frameworks[$framework] ) )
		{
			$options = static::$_frameworks[$framework];

			static::$_options->merge( new JRegistry( $options ), true );
		}//end if
	}//end function
}//end class
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
use JHTML, JFile, JFactory, JObject, JAccess, JApplicationWebClient, JBrowser, JForm, JRegistry, JComponentHelper, JLayoutHelper, JTable, JString, JFilterInput, JPluginHelper, JEventDispatcher, JCache;

// Add PHP Classes
use stdClass, SimpleXMLElement;

defined( '_JEXEC' ) or die;

/**
 * Root Ark Helper File.
 */
class Helper
{
	/**
	 * @var		array	Internal Array Containing Helper/Asset Load Status'
	 */
	protected static $loaded 			= array( 'exception' => array(), 'helper' => array(), 'plugin' => array() );

	/**
	 * @var		object	Internal JForm Object Containing the Component's XML Data
	 */
	protected static $xml 				= null;

	/**
	 * @var		object	Internal JRegistry Object Containing the Component's Params (once merged with XML options)
	 */
	protected static $params			= null;

	/**
	 * @var		array	Internal Array Containing Messages to Display in the Content
	 */
	protected static $contentmessages 	= array();

	/**
	 * @var		object	List of Actions the Current User Can/Can't Perform
	 */
	protected static $actions 			= null;

	/**
	 * @var		array	The Cache Class Reference & Active State
	 */
	protected static $cache 			= array( 'active' => null, 'data' => null );

	/**
	 * Load in a CSS Library Asset
	 *
	 * @param	string	$path		What Array Class/ID to Load as a Dot Separated Path (Default : 'root')
	 * @param	string	$default	A Default Value if the Path is not Found or Blank
	 *
	 * @usage	Helper::css();								// 'ark'
	 * 			Helper::css( 'clear' );						// 'clearfix'
	 * 			Helper::css( 'button.align.vertical' );		// 'btn-group-vertical'
	 * 			Helper::css( 'button' );					// array( 'button' => etc... );
	 *
	 * @return  string	Class/ID Name
	 */
	public static function css( $path = 'root', $default = null )
	{
		static::add( 'helper', 'css' );

		return HelperCSS::selector( $path, $default );
	}//end function

	/**
	 * Add an Ark Helper Asset to the Current Process.
	 *
	 * @param	string	$type		String of Include Type
	 * @param	string	$file		Option Sub Type/File
	 *
	 * @usage	Helper::add( 'helper', 'image' );
	 * 			Helper::add( 'exception', 'batch' );
	 * 			Helper::add( 'html' );
	 * 			Helper::add( 'plugin', 'customPluginGroup' );
	 *
	 * @return	void
	 */
	public static function add( $type = null, $file = null )
	{
		switch( $type )
		{
			case 'exception' :
				if( !isset( static::$loaded[$type][$file] ) )
				{
					static::$loaded[$type][$file] = true;

					$path = ARKMEDIA_EXCEPTIONS . $file . '.php';

					if( JFile::exists( $path ) )
					{
						require_once( $path );
					}//end if
				}//end if

				return;

			default :
			case 'html' :
				return JHTML::addIncludePath( ARKMEDIA_HTML );

			case 'helper' :
				if( !isset( static::$loaded[$type][$file] ) )
				{
					static::$loaded[$type][$file] = true;

					$path = ARKMEDIA_HELPERS . 'ark_' . $file . '.php';

					if( JFile::exists( $path ) )
					{
						require_once( $path );
					}//end if
				}//end if

				return;

			// @note	JPluginHelper Doesn't Strip Out Hyphens When it Comes to Initialising the Plugin Class.
			// 			So We Must Manually & Individually Load in Plugin Groups that Have Hyphenated Class Names.
			case 'plugin' :
				if( !isset( static::$loaded[$type][$file] ) )
				{
					static::$loaded[$type][$file] = true;

					if( strpos( $file, '-' ) === false )
					{
						JPluginHelper::importPlugin( $file );
					}
					else
					{
						$plugins 	= JPluginHelper::getPlugin( $file );
						$event		= JEventDispatcher::getInstance();

						foreach( $plugins as $plugin )
						{
							JPluginHelper::importPlugin( $file, $plugin->name, false );

							$class = 'plg' . str_replace( '-', '', $file ) . $plugin->name;

							if( class_exists( $class ) )
							{
								new $class( $event, (array)$plugin );
							}//end if
						}//end foreach
					}//end if
				}//end if

				return;
		}//end switch
	}//end function

	/**
	 * Retrieve the Component's XML Manifest for Sniffing.
	 *
	 * @usage	Helper::xml();
	 *
	 * @return  object	SimpleXMLElement Object Containing the Component's XML Data
	 */
	public static function xml()
	{
		// Load Components XML File
		if( !isset( static::$xml ) )
		{
			$file = ARKMEDIA_BE . 'config.xml';
			$form = JForm::getInstance( ARKMEDIA_COMPONENT_ID, $file, array(), true, '/config' );

			if( $form instanceof JForm )
			{
				static::$xml = $form->getXML()->config;
			}//end if
		}//end if

		// Load Components XML Options (as Key => Default) to JRegistry Then Merge the Current Params Over the Top
		if( !isset( static::$params ) )
		{
			// Load Stack Data (before accessing data)
			HelperStack::register();

			// Create Basic Options
			static::$params = new JRegistry();

			// Load Stack Specific Defaults in.
			// On Install of the Suite the Config has not been Saved Yet,
			// So we Must Manually Add in the Stack Specific Config Options
			foreach( HelperStack::get() as $stack => $stackdata )
			{
				foreach( $stackdata->params->toArray() as $key => $val )
				{
					$entry = array( $stack => $val );

					// Because Component Params Haven't Been Loaded in Yet we may need to Declare/Setup the Parameter
					// Otherwise Add the Next Stack Value to the Parameter
					if( static::$params->exists( $key ) )
					{
						static::$params->set( $key, array_merge( static::$params->get( $key ), $entry ) );
					}
					else
					{
						static::$params->def( $key, $entry );
					}//end if
				}//end foreach
			}//end foreach

			// If Config XML Found Add it's Options & Defaults as a Base (in case no current options have been populated yet)
			// This Sets Non-Stack Specific Defaults (both of which are overriden by merging the saved params when they become available)
			if( static::$xml && ( static::$xml instanceof SimpleXMLElement ) )
			{
				foreach( static::$xml->children() as $fieldset )
				{
					foreach( $fieldset as $fields )
					{
						$default = (string)$fields['default'];

						// If the Default is the Asterisk Wildcard Replace Default With it's Options (as we do this behaviour for lists)
						if( $default === '*' )
						{
							$default = array();

							foreach( $fields->children() as $option )
							{
								$default[] = (string)$option['value'];
							}//end foreach
						}//end if

						static::$params->def( (string)$fields['name'], $default );
					}//end foreach
				}//end foreach
			}//end if

			// Merge Current Component Options Over the Top
			static::$params->merge( JComponentHelper::getParams( ARKMEDIA_COMPONENT ) );
		}//end if

		return static::$xml;
	}//end function

	/**
	 * Retrieve the Component's Parameters or a Single Parameter Option.
	 *
	 * @param	string	$option		String of Include Type
	 * @param	string	$default	Default Value if Option is Not Found
	 *
	 * @usage	Helper::params();
	 * 			Helper::params( 'option', $default );
	 *
	 * @note 	This Function Also Looks to the Config XML's Defaults so if a Value is Set Here the Manual Default is not Returned
	 *
	 * @todo 	Move This(Params()), XML() & Set() to a Separate Parameter/Option Helper as: HelperOpts::get(), HelperOpts::set(), HelperOpts::all()
	 *
	 * @return  mixed	JRegistry Object of Component Parameters or Param Value
	 */
	public static function params( $option = null, $default = null )
	{
		// Load Components Parameters
		if( !isset( static::$params ) )
		{
			// Load Manifest & Options
			static::xml();
		}//end if

		return ( $option ) ? static::$params->get( $option, $default ) : static::$params;
	}//end function

	/**
	 * Set a Single Component Parameter. Also if the Parameter Doesn't Exist in the DB Set it There too.
	 * This can Happen on First Install of the Suite Without Saving Any Configuration.
	 *
	 * @param	string	$option		Parameter Name
	 * @param	string	$value		Parameter Desired New Value
	 *
	 * @usage	Helper::set( 'option', 'value' );
	 * 			Helper::set( 'option', 'value', false );
	 *
	 * @return  void
	 */
	public static function set( $option = null, $value = null, $store = false )
	{
		// Load Components Parameters
		if( !isset( static::$params ) )
		{
			// Load Manifest & Options
			static::xml();
		}//end if

		// Set the External Reference as well In Case JComponentHelper::getParams() is Called Directly (e.g. MediaHelper)
		JComponentHelper::getParams( ARKMEDIA_COMPONENT )->set( $option, $value );

		// Set Parameter
		static::$params->set( $option, $value );
	}//end function

	/**
	 * Set a Joomla Error/Notification Message. If an Ajax Call Then Suppress Message Unless Told Otherwise.
	 *
	 * @param	string	$message	The Message to Throw
	 * @param	string	$type		The Message Type/Severity
	 * @param	object	$options	Message Display Options
	 *
	 * @usage	Helper::message( 'message', 'error' );
	 * 			Helper::message( 'message', 'error', (object)['persist' => true] );
	 *
	 * @return  void
	 */
	public static function message( $message = null, $type = 'error', $options = null )
	{
		// Defaults
		if( is_null( $options ) )
		{
			$options = new stdClass;
		}//end if

		// If Persist is Set Then This Message Will Display Even in Ajax Calls
		$app 	= JFactory::getApplication();
		$skip 	= ( ( !isset( $options->persist ) || !$options->persist ) && $app->input->get( 'ajax', 0, 'int' ) ) ? true : false;

		if( !$skip )
		{
			$app->enQueueMessage( $message, $type );
		}//end if
	}//end function

	/**
	 * Set/Get Messages to be Displayed in Large Content Alert Boxes.
	 *
	 * @param	string	$title		The Message Title
	 * @param	string	$message	The Message Content
	 * @param	object	$button		The Message Readmore Button Options ('text', 'href', 'colour', 'target')
	 *
	 * @usage	Helper::contentMessage( 'Title!', 'Message!', (object)['text' => 'readmore', 'href' => 'http://www.url.co.uk'] );
	 * 			Helper::contentMessage();
	 *
	 * @return  array 	The Message Queue
	 */
	public static function contentMessage( $title = null, $message = null, $button = array() )
	{
		if( $title || $message )
		{
			static::$contentmessages[] = (object)array( 'title' => $title, 'message' => $message, 'button' => (object)$button );
		}//end if

		return static::$contentmessages;
	}//end function

	/**
	 * Attempt to Retrieve the Browser Information & Return in the Desired Format.
	 *
	 * @param	string	$format		The Return Type. An Object of Browser Info or Calculate a Unique CSS String?
	 *
	 * @usage	Helper::browser();
	 * 			Helper::browser( 'css' );
	 *
	 * @return  mixed 	Format Dependent, Object of Browser Data or CSS String.
	 */
	public static function browser( $format = 'object' )
	{
		jimport( 'joomla.environment.browser' );

		$client 	= new JApplicationWebClient();
		$navigator 	= JBrowser::getInstance();
		$platform 	= $navigator->getPlatform();
		$browser 	= $navigator->getBrowser();
		$major 		= $navigator->getMajor();
		$minor 		= $navigator->getMinor();

		// Prevent IE11 Masquerading as FireFox by Detecting the Platform (not the $client->browser...)
		if( $browser === 'mozilla' && $client->engine == JApplicationWebClient::TRIDENT )
		{
			$browser 	= 'msie';
			$major 		= '11';
		}//end if

		// Return Data
		switch( $format )
		{
			default :
			case 'object':
				return (object)array( 'platform' => $platform, 'browser' => $browser, 'major' => $major, 'minor' => $minor );

			case 'css':
				return 'nav-platform-' . $platform . chr( 32 ) . 'nav-browser-' . $browser . chr( 32 ) . 'nav-major-' . $major;
		}//end switch
	}//end function

	/**
	 * Attempt to Retrieve the Template Information & Return in the Desired Format.
	 *
	 * @param	string	$format		The Return Type. An Object of Info or Calculate a Unique CSS String?
	 *
	 * @usage	Helper::template();
	 * 			Helper::template( 'css' );
	 *
	 * @return  mixed 	Format Dependent, Object of Data or CSS String.
	 */
	public static function template( $format = 'object' )
	{
		$app 	= JFactory::getApplication();
		$tpl 	= $app->getTemplate();
		$filter	= JFilterInput::getInstance();
		$ext 	= JTable::getInstance( 'extension' );
		$id 	= $ext->find( array( 'name' => $tpl ) );
		$club 	= '';

		// Load Template Data
		if( $ext->load( (int)$id ) )
		{
			// Get the Theme Club Name (kae safe for use in CSS)
			$reg 	= new JRegistry( $ext->get( 'manifest_cache' ) );
			$club 	= $filter->clean( JString::strtolower( $reg->get( 'author' ) ), 'word' );
		}//end if

		// Return Data
		switch( $format )
		{
			default :
			case 'object':
				return (object)array( 'id' => $id, 'template' => $tpl, 'club' => $club );

			case 'css':
				return 'tmpl-id-' . $id . chr( 32 ) . 'tmpl-template-' . $tpl . chr( 32 ) . 'tmpl-club-' . $club;
		}//end switch
	}//end function

	/**
	 * Check Whether Caching is Enabled or Not. Caching Can be Forced on by the Ark Media Global Options Regardless of Whether Joomla's is Enabled or Not.
	 *
	 * @usage	Helper::caching();
	 *
	 * @return  bool 	Caching Enabled Flag
	 */
	public static function caching()
	{
		if( !isset( static::$cache['active'] ) )
		{
			$arkcache 		= static::params( 'system-caching' );
			$joomlacache	= JFactory::getApplication()->getCfg( 'caching' );

			// If "Use Global" is Set Defer to Joomla's Setting
			static::$cache['active'] = ( $arkcache === 2 ) ? (bool)$joomlacache : (bool)$arkcache;
		}//end if

		return static::$cache['active'];
	}//end function

	/**
	 * Return Ark Media's Cache Object, Don't go Directly to JFactory Because We May Need to Force Caching on First.
	 *
	 * @note 	Manually Handle Cache to Set Custom Options Rather Than Use: JFactory::getCache( ARKMEDIA_COMPONENT, 'output' );
	 *
	 * @usage	Helper::cache();
	 *
	 * @return  object 	Joomla JCache Object
	 */
	public static function cache()
	{
		// Has Cache Been Initialised?
		if( !isset( static::$cache['data'] ) )
		{
			$options 					= array();
			$options['defaultgroup'] 	= ARKMEDIA_COMPONENT;
			$options['cachebase'] 		= ARKMEDIA_CACHE;

			// Ensure Caching is Forced On
			if( static::caching() )
			{
				$options['caching'] 	= true;
			}//end if

			static::$cache['data'] 		= JCache::getInstance( 'output', $options );
		}//end if

		return static::$cache['data'];
	}//end function

	/**
	 * An Abbreviated Version Of Accessing the Custom Namespaced JHTML Classes.
	 * 
	 * @param	string	$class		The JHTML Class Path to Call.
	 *
	 * @usage	Helper::html( 'icon.uikit', 'cog' );
	 *			JHTML::_( ARKMEDIA_HTML_ID . '.icon.uikit', $arg1, $arg2 );
	 *
	 * @return  string 	HTML of JHTML String
	 */
	public static function html( $class )
	{
		// Get JHTML Arguments
		$args = func_get_args();

		// Remove Un-Namespaced JHTML Method
		array_shift( $args );

		// Add Namespaced JHTML Method
		array_unshift( $args, ARKMEDIA_HTML_ID . '.' . $class );

		// Call: JHTML::_( ARKMEDIA_HTML_ID . '.' . $class, $args );
		return call_user_func_array( array( 'JHTML', '_' ), $args );
	}//end function

	/**
	 * Render a JLayout File. Use this Mediator Function to Ensure the Administrator Component is Always Loaded Up.
	 * Otherwise Front-End Loading Fails. Also Layouts Are Automatically Namespaced to Avoid Template Override Collisions.
	 *
	 * @see		[#2379] For Why Base Path is Mandatory.
	 * 
	 * @param	string	$layout		The Layout Name to Load.
	 * @param	mixed	$data		The Data to Pass to the Layout.
	 * @param	array	$options	Layout Setup Options.
	 *
	 * @usage	Helper::layout( 'layout', $data );
	 *
	 * @return  string 	HTML of JLayout String
	 */
	public static function layout( $layout, $data = null, $options = array() )
	{
		$base 	= ( isset( $options['base'] ) ) 	? $options['base'] 				: ARKMEDIA_BE . 'layouts';
		$client = ( isset( $options['client'] ) ) 	? $options['client'] 			: 'admin';
		$suffix = ( isset( $options['suffix'] ) ) 	? array( $options['suffix'] ) 	: array();

		return JLayoutHelper::render( ARKMEDIA_COMPONENT_ID . '.' . $layout, $data, $base, array( 'client' => $client, 'suffixes' => $suffix ) );
	}//end function

	/**
	 * Get a List of Extension-Wide Actions that the Current User is Permitted/Not Permitted to Perform.
	 *
	 * @param	string	$cmd		If Provided an Action Will be Tested.
	 *
	 * @note 	Joomla Have a Very Helpful: JHelperContent::getActions( ARKMEDIA_COMPONENT ); Which Does All This for us.
	 * 			However Joomla Keep Changing the Parameter Order so we'll Wait Till the Next Major Release to Switch Across to it.
	 *
	 * @usage	Helper::actions();
	 * 			Helper::actions( 'core.manage' );
	 *
	 * @return	mixed	List of Actions & Accessible Boolean Flags if No Action is Provided, Otherwise an Accessible Flag is Returned
	 */
	public static function actions( $cmd = null )
	{
		// Load Actions
		if( is_null( static::$actions ) )
		{
			$user				= JFactory::getUser();
			static::$actions	= new JObject;
			$actions 			= JAccess::getActionsFromFile( ARKMEDIA_BE . 'access.xml' );

			foreach( $actions as $action )
			{
				static::$actions->set( $action->name, $user->authorise( $action->name, ARKMEDIA_COMPONENT ) );
			}//end foreach
		}//end if

		return ( $cmd ) ? static::$actions->get( $cmd ) : static::$actions;
	}//end function
}//end class
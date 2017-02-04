<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperStack, Ark\Media\HelperFileSystem;

defined( '_JEXEC' ) or die();

class plgArkMediaSearch extends JPlugin
{
	/**
	 * @var		string	Stack Name (for namespacing, config retrieval & more!)
	 */
	protected $name = 'search';

	/**
	 * @var		bool	Is the Stack Active or Has a Process Disabled it?
	 */
	protected $active;

	/**
	 * @var		string	The Root of the Joomla Installation Including Trailing DS
	 */
	protected $base;

	/**
	 * @var		string	The Relative Path to this Plugin
	 */
	protected $plugin;

	/**
	 * @var		string	Stack Title (for display in HTML)
	 */
	protected $title;

	/**
	 * @var		string	Base for JText
	 */
	protected $jtext;

	/**
	 * Constructor
	 */
	public function __construct( &$subject, $config )
	{
		// Load Language for Title
		JFactory::getLanguage()->load( 'plg_' . ARKMEDIA_COMPONENT_ID . '_' . $this->name, JPATH_ADMINISTRATOR );

		$this->base 	= ARKMEDIA_ROOT . ARKMEDIA_DS;
		$this->plugin 	= str_replace( $this->base, '', ARKMEDIA_PLUGIN_STACKS . $this->name . ARKMEDIA_DS );
		$this->jtext 	= 'PLG_' . JString::strtoupper( ARKMEDIA_COMPONENT_ID ) . '_' . JString::strtoupper( $this->name ) . '_';
		$this->title 	= JText::_( $this->jtext . 'XML_NAVIGATION_LBL' );

		parent::__construct( $subject, $config );
	}//end function

	/**
	 * Register Eligibility for Inclusion into the Stack Loading Process
	 * Each Stack Must Provide a Name & Parameter Options to be Merged into Ark Media's
	 *
	 * @param	array	$titles		A Referenced Array of Stack Titles (for HTML output)
	 * @param	array	$names		A Referenced Array of Stack Names (for JS, PHP & other namespacing purposes)
	 * @param	array	$params		A Referenced Array of JRegistry Plugin Parameters
	 *
	 * @return	void
	 */
	public function onArkBeforeStackRegister( &$titles = array(), &$names = array(), &$params = array() )
	{
		$titles[] 	= $this->title;
		$names[] 	= $this->name;
		$params[] 	= $this->params;
	}//end function

	/**
	 * After Registration, if Our Stack is Not in this List Then We Need to Disabled.
	 * This Event is Fired Very Early on in the Process to Ensure That Subsequent Events Can be Disabled.
	 *
	 * @note	Although We Track this Option Like the Other Stacks We Actually Have a Param to Force Ignore this Anyway.
	 *
	 * @param	array	$stacks		List of Active Stacks
	 *
	 * @return	void
	 */
	public function onArkAfterStackRegister( $stacks = array() )
	{
		$this->active = in_array( $this->name, $stacks );
	}//end function

	/**
	 * Catch All Ark Form Events to Add Additional JForm/Items to the Forms
	 *
	 * @param	string	$name		The Current Form Name
	 * @param	object	$form		The Form Object to Manipulate
	 * @param	bool	$namespace	Whether to Namespace Each Form Field With the Stack Name (opt in)
	 *
	 * @return	void
	 */
	public function onArkAfterForm( &$name = '', &$form = null, $namespace = false )
	{
		if( !$this->active ) return;

		$file = $this->base . $this->plugin . 'forms/' . $name . '.xml';

		// If We Have Our Own Additions Then Add Them
		if( JFile::exists( $file ) )
		{
			$xml = simplexml_load_file( $file );

			// Set Each Plugin Field/Node With a Namespace Attribute (for the loader to know who merged which field)
			// @see	[#2631] For Add to Entire JForm Rather Than Merged Form Only.
			if( $namespace )
			{
				if( $xml instanceof SimpleXMLElement )
				{
					// Add a Namespace Attribute to Each XML Node
					switch( $namespace )
					{
						default :
						case 'field' :
							$nodes = $xml->xpath( '//field' );
							break;

						case 'fieldset' :
							$nodes = $xml->xpath( '//' . $namespace );
							break;
					}//end switch

					if( count( $nodes ) )
					{
						foreach( $nodes as $node )
						{
							$node['data-stack'] = $this->name;
						}//end foreach
					}//end if
				}//end if
			}//end if

			// Now Merge the Two Forms
			$form->load( $xml );
		}//end if
	}//end function

	/**
	 * Catch Main Ark JS Renderer & Add Our JS/Layout.
	 *
	 * @param	object	$view		The View Object.
	 *
	 * @return	void
	 */
	public function onArkAfterRenderJS( &$view = null )
	{
		if( !$this->active ) return;

		$options = (object)array( 'base' => $this->base, 'name' => $this->name, 'plugin' => $this->plugin, 'title' => $this->title, 'jtext' => $this->jtext );
		$layouts = $this->base . $this->plugin . 'layouts';

		echo Helper::layout( 'js.' . $this->name, $options, array( 'base' => $layouts, 'client' => 'auto' ) );
	}//end function

	/**
	 * Catch Content Renderer & Insert Our Stack Layouts.
	 *
	 * @param	object	$view		The View Object.
	 *
	 * @return	void
	 */
	public function onArkAfterRenderFiles( &$view = null )
	{
		if( !$this->active ) return;

		$base = $this->base . $this->plugin . 'layouts';

		// Load Stack Content Layouts
		echo Helper::layout( 'content.stack', (object)array( 'base' => $base, 'name' => $this->name ), array( 'base' => $base, 'client' => 'auto' ) );

		echo Helper::layout( 'content.back', (object)array( 'base' => $base, 'name' => $this->name ), array( 'base' => $base, 'client' => 'auto' ) );

		echo Helper::layout( 'content.folder', (object)array( 'base' => $base, 'name' => $this->name ), array( 'base' => $base, 'client' => 'auto' ) );

		echo Helper::layout( 'content.file', (object)array( 'base' => $base, 'name' => $this->name ), array( 'base' => $base, 'client' => 'auto' ) );
	}//end function

	/**
	 * Catch Controller Task Initiations for Intercepting Custom Tasks.
	 *
	 * @param	object	$ctrl		The Controller Object.
	 *
	 * @method	ajax
	 *
	 * @return	void
	 */
	public function onArkBeforeTask( &$ctrl = null )
	{
		if( !HelperStack::isActive( $this->name, $this->params ) ) return;

		$app 	= JFactory::getApplication();
		$task 	= $app->input->get( 'task', false, 'cmd' );

		// Catch Our Task
		if( $task === $this->name )
		{
			$stack 	= $app->input->get( 'stack', false, 'cmd' );
			$text 	= $app->input->get( 'text', false, 'string' );
			$data	= (object)array( 'folders' => array(), 'files' => array(), 'count' => (object)array( 'folders' => 0, 'files' => 0 ) );

			// Check the Data
			if( $stack && $text && Helper::actions( 'ark.action.search' ) )
			{
				// Load Basic Stack From Cache
				HelperStack::load( false, false );

				// Use Root Path (no need for path validation)
				$stackdata 	= HelperStack::get( $stack );
				$stackpath 	= HelperFileSystem::makePath( $stackdata->path );

				// Load Up Folder Data
			 	$types 		= HelperStack::getStackFileTypes( $stack );

			 	HelperFileSystem::load( $stackpath, array( 'stack' => $stack, 'extfilter' => $types->ext, 'recurse' => true ) );

			 	$results 	= HelperFileSystem::retrieve();

			 	// Perform Search on the Folders (use stri to ensure a case insensitive searches)
				// @note	Only 1 Match Need be Found (the highlighter can do the hard work of finding all instances later)
			 	foreach( $results as $path => $folder )
			 	{
	 				// Remove the Folder Name (and trailing DS) From the End of String Only to Prevent Folders that Appear Twice Being Stripped
					$base = preg_replace( '#' . preg_quote( ARKMEDIA_DS . $folder->name, '#' ) . '$#', '', $path );

					// Search Folder Name (don't search root stack folder, do search it's files though)
					if( $path !== $stackdata->path && stripos( $folder->name, $text ) !== false )
					{
						$data->folders[] = (object)array( 'name' => $folder->name, 'basepath' => $base, 'fullpath' => $path );

						$data->count->folders++;
					}//end if

					// Search Through the Folder's Files
					foreach( $folder->items as $filekey => $filedata )
					{
						if( stripos( $filedata['name'], $text ) !== false )
						{
							$data->files[] = (object)array( 'name' => $filedata['name'], 'basepath' => $base, 'fullpath' => $path, 'icon' => $filedata['preview']['icon'] );

							$data->count->files++;
						}//end if
					}//end foreach
			 	}//end foreach
			}//end if

			echo json_encode( $data );

			jexit();
		}//end if
	}//end function
}//end class
<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper;

defined( '_JEXEC' ) or die();

class plgArkMediaImages extends JPlugin
{
	/**
	 * @var		string	Stack Name (for namespacing, config retrieval & more!)
	 */
	protected $name = 'images';

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
	 * Constructor
	 */
	public function __construct( &$subject, $config )
	{
		$this->base 	= ARKMEDIA_ROOT . ARKMEDIA_DS;
		$this->plugin 	= str_replace( $this->base, '', ARKMEDIA_PLUGIN_STACKS . $this->name . ARKMEDIA_DS );

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
		// Load Language for Title
		JFactory::getLanguage()->load( 'plg_' . ARKMEDIA_COMPONENT_ID . '_' . $this->name, JPATH_ADMINISTRATOR );

		$titles[] 	= JText::_( 'PLG_' . JString::strtoupper( ARKMEDIA_COMPONENT_ID ) . '_' . JString::strtoupper( $this->name ) . '_XML_NAVIGATION_LBL' );
		$names[] 	= $this->name;
		$params[] 	= $this->params;
	}//end function

	/**
	 * After Registration, if Our Stack is Not in this List Then We Need to Disabled.
	 * This Event is Fired Very Early on in the Process to Ensure That Subsequent Events Can be Disabled.
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
	 * @see		[#2250] For Auto Loading of Plugin JS Files.
	 *
	 * @return	void
	 */
	public function onArkAfterRenderJS( &$view = null )
	{
		if( !$this->active ) return;

		$options = (object)array( 'base' => $this->base, 'name' => $this->name, 'plugin' => $this->plugin, 'params' => $this->params );
		$layouts = $this->base . $this->plugin . 'layouts';

		echo Helper::layout( 'js.' . $this->name, $options, array( 'base' => $layouts, 'client' => 'auto' ) );
	}//end function
}//end class
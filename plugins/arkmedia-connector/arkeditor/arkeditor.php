<?php
/**
 * @version     1.12.1
 * @package     com_arkhive
 * @copyright   Copyright (C) 2014. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperStack, Ark\Media\HelperEdit, Ark\Media\HelperFileSystem;

defined( '_JEXEC' ) or die();

class plgArkMediaConnectorArkEditor extends JPlugin
{
	/**
	 * @var		string	Actual Editor Name As Named in the Plugin Itself
	 */
	protected $name = 'arkeditor';

	/**
	 * @var		string	Editor Name As it Appears in the URL Get Parameter
	 */
	protected $value = 'ckeditor';

	/**
	 * Constructor
	 */
	public function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}//end function

	/**
	 * Catch Main Ark Stack Path Setter in Order to Reset it if in Editor Context.
	 *
	 * @return	void
	 */
	public function onArkAfterStackPath()
	{
		Helper::add( 'helper', 'edit' );

		$enabled		= HelperEdit::isEditMode() && HelperEdit::isEditFull();
		$editor			= HelperEdit::getEditor();
		
		// Continue if in an Editor Instance & it is This Plugin's Editor
		if( $enabled && JString::strtolower( $editor ) === $this->value )
		{
			$params 	= JComponentHelper::getParams( 'com_' . $this->name );

			// Set All Applicable Stack Paths
			// @note	We Could Just Set the Active Stack But Stack Switching in the Editor May be a Future Feature
			foreach( HelperStack::get() as $name => $stack )
			{
				$path 		= false;

				// Update Base Path for Supported Stacks With the Editor's Param Version
				switch( $name )
				{
					case 'images' :
						$path = HelperFileSystem::trim( $params->get( 'imagePath', false ) );
						break;

					case 'documents' :
						$path = HelperFileSystem::trim( $params->get( 'filePath', false ) );
						break;
				}//end switch

				// Set Editor's Path Otherwise Fall Back to Component Version That is Already Set
				if( $path )
				{
					HelperStack::set( $name, 'path', HelperFileSystem::filter( $path, 'folder' ) );
				}//end if
			}//end foreach
		}//end if
	}//end function
}//end class
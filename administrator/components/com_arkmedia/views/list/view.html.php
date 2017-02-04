<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper;

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

class ArkMediaViewList extends JViewLegacy
{
	/**
	 * The Application Class.
	 *
	 * @var		object
	 */
	public $app;

	/**
	 * The Document Class.
	 *
	 * @var		object
	 */
	public $doc;

	/**
	 * The Event Class.
	 *
	 * @var		object
	 */
	public $event;

	/**
	 * The List Model Class.
	 *
	 * @var		object
	 */
	public $model;

	/**
	 * The CSS Class Attributes List.
	 *
	 * @var		object
	 */
	public $css;

	/**
	 * The Layout Data Attributes List.
	 *
	 * @var		object
	 */
	public $data;

	/**
	 * The Full List of Directory Items.
	 *
	 * @var		object
	 */
	public $items;

	/**
	 * The JSON Value of Directory Items.
	 *
	 * @var		string
	 */
	public $json;

	/**
	 * Execute and Display a Template Script.
	 *
	 * @param   string  $tpl  The Name of the Template File to Parse.
	 *
	 * @return  mixed  A String if Successful, Otherwise an Error Object.
	 */
	public function display( $tpl = null )
	{
		// @TODO: Support This Browser...
		$browser = Helper::browser();

		if( $browser->browser === 'msie' && $browser->major == 8 )
		{
			echo Helper::layout( 'message.browser', $this );

			$this->addToolbar();

			return;
		}//end if

		$this->app			= JFactory::getApplication();
		$this->doc			= JFactory::getDocument();
		$this->event		= JDispatcher::getInstance();
		$this->model		= $this->getModel();

		$this->items		= $this->get( 'Items' );
		$this->json			= $this->get( 'JSON' );
		$this->toolbar		= $this->get( 'ToolbarOptions' );
		$this->navigation	= $this->get( 'NavigationOptions' );
		$this->explorer		= $this->get( 'ExplorerOptions' );
		$this->breadcrumb	= $this->get( 'BreadcrumbOptions' );
		$this->helpbar		= $this->get( 'HelpbarOptions' );
		$this->editbar		= $this->get( 'EditbarOptions' );
		$this->actions		= $this->get( 'ActionOptions' );
		$this->subactions	= $this->get( 'SubActionOptions' );
		$this->content		= $this->get( 'ContentOptions' );

		// Add Toolbar
		$this->addToolbar();

		// Render Template
		parent::display( $tpl );
	}//end function

	/**
	 * Add Page Title & Toolbar.
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( ARKMEDIA_COMPONENT ), ARKMEDIA_COMPONENT_ID );

		if( Helper::actions( 'core.admin' ) )
		{
			JToolBarHelper::preferences( ARKMEDIA_COMPONENT );
		}//end if
	}//end function
}//end class
<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

class ArkMediaController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			$cachable	If true, the view output will be cached
	 * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 */
	public function display( $cachable = false, $urlparams = false )
	{
		$app	= JFactory::getApplication();
		$view	= $app->input->get( 'view', 'list', 'cmd' );

		$app->input->set( 'view', $view );

		parent::display( $cachable, $urlparams );

		return $this;
	}//end function
}//end class
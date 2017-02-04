<?php
/**
 * @version     1.0.0a
 * @package     com_arkhive
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'calendar' );

/**
 * This Field Renders a Normal File Field But Provides Extra Features.
 *
 * Extra:
 * 1. Support Data Attributes.
 * 2. Covert Styling to Bootstrap 3.
 */
class ArkFormFieldExtendedCalendar extends JFormFieldCalendar
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'extendedcalendar';


	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// @todo	Plumb in this File
		return parent::getInput();
	}//end function
}//end class
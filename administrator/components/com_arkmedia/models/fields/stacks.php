<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\HelperStack;

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'extendedlist' );

/**
 * This Field Lists the Installed Stacks
 */
class ArkFormFieldStacks extends ArkFormFieldExtendedList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'stacks';

	/**
	 * Custom Param (like $translateLabel & $translateHint) to Decide Whether to Translate the Data Attr's.
	 *
	 * @var		bool
	 */
	protected $translateAttributes = true;

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initiate Ark Component Assets
		defined( '_ARKMEDIA_EXEC' ) or define( '_ARKMEDIA_EXEC', true );

		// Stay DS Sensitive
		require_once( str_replace( 'models' . DIRECTORY_SEPARATOR . 'fields', '', dirname( __FILE__ ) ) . 'initiate.php' );

		// Register Stacks
		HelperStack::register();

		$options 	= array();
		$field 		= $this->__get( 'fieldname' );

		// Load Stacks to Options
		foreach( HelperStack::get() as $name => $data )
		{
			// Has the Stack Opted-Out?
			if( $data->params->get( 'register-' . $field, true ) )
			{
				$options[] = JHtml::_('select.option', $name, ucwords( $name ) );
			}//end if
		}//end if

		// Merge With JForm XML Options
		return array_merge( parent::getOptions(), $options );
	}//end function
}//end class
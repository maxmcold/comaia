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

JFormHelper::loadFieldClass( 'text' );

/**
 * This Field Finds the Installed Stacks & Adds a Text Field for Each
 * Which Allows you to Select a Location/Parameter for Each Stack.
 *
 * @todo 	Merge This File With "stackfiletypes" as They Are Virtually the Same. Called "stackfield"
 * 			Rather Than Extending the Desired Field Perhaps Create a New JForm Instance for Each Field?
 */
class ArkFormFieldStackLocations extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'stacklocations';

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 */
	public function setup( SimpleXMLElement $element, $value, $group = null )
	{
		// Initiate Ark Component Assets
		defined( '_ARKMEDIA_EXEC' ) or define( '_ARKMEDIA_EXEC', true );

		// Stay DS Sensitive
		require_once( str_replace( 'models' . DIRECTORY_SEPARATOR . 'fields', '', dirname( __FILE__ ) ) . 'initiate.php' );

		// Register Stacks
		HelperStack::register();

		// Set Group Name
		$group = (string)$element['name'];

		// Continue Set-Up as Usual
		return parent::setup( $element, $value, $group );
	}//end function

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$lang = JFactory::getLanguage();
		$html = array();
		$name = $this->__get( 'fieldname' );
		$vals = (array)$this->value;

		// Render Stacks
		foreach( HelperStack::get() as $stack => $data )
		{
			// Has the Stack Opted-Out?
			if( !$data->params->get( 'register-' . $name, true ) )
			{
				continue;
			}//end if
			
			// Get Details
			$default 	= ( isset( $vals[$stack] ) && $vals[$stack] ) ? $vals[$stack] : $data->params->get( $name );
			$label		= str_replace( 'LBL', 'OPTS', (string)$this->element['label'] );

			// Set Temporary Data
			$this->__set( 'fieldname', $stack );
			$this->__set( 'name', $stack );
			$this->__set( 'id', $stack );
			$this->setValue( $default ); // avoid type casting

			// Grab Rendered Input
			$html[] = ( $lang->hasKey( $label ) ) ? '<span class="help-block">' . JText::sprintf( $label, ucwords( $stack ) ) . '</span>' : '';
			$html[] = parent::getInput();
			$html[] = '<br /><br />';
		}//end if

		// Finish
		return implode( "\n", $html );
	}//end function
}//end class
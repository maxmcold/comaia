<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperVersion;

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'message' );

/**
 * This Field Renders the Suite's Version Number.
 */
class ArkFormFieldVersion extends ArkFormFieldMessage
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'version';

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

		Helper::add( 'helper', 'version' );

		// Continue Set-Up as Usual
		return parent::setup( $element, $value, $group );
	}//end function

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 */
	protected function getInput()
	{
		$data 	= array();
		$data[] = HelperVersion::extension();
		$data[] = ( HelperVersion::isPro() ? HelperVersion::pro() : ( HelperVersion::isBasic() ? HelperVersion::basic() : '' ) );
		$data[] = HelperVersion::version();

		// Add Changelog Data
		if( isset( $this->element['changelog'] ) || isset( $this->element['help'] ) || isset( $this->element['docs'] ) )
		{
			$data[] = '<br />';
		}//end if

		// Add Changelog Data
		if( isset( $this->element['changelog'] ) )
		{
			$data[] = $this->_link( (string)$this->element['changelog'], 'changelog' );
		}//end if

		// Add Help Data
		if( isset( $this->element['help'] ) )
		{
			$data[] = $this->_link( (string)$this->element['help'], 'help' );
		}//end if

		// Add Docs Data
		if( isset( $this->element['docs'] ) )
		{
			$data[] = $this->_link( (string)$this->element['docs'], 'docs' );
		}//end if

		$this->element['label'] = Helper::html( 'text.implode', $data );

		return parent::getInput();
	}//end function

	/**
	 * Method to Render a Link Element.
	 *
	 * @param	string	$href	The Link.
	 * @param	string	$text	The JText Suffix.
	 * @param	string	$icon	The Link Icon.
	 *
	 * @return	string	Link HTML
	 */
	protected function _link( $href, $text, $icon = false )
	{
		$text 	= '<small>' . JText::_( ARKMEDIA_JTEXT . 'VERSION_' . JString::strtoupper( $text ) ) . '</small>';
		$opts 	= array( 'text' => $text, 'href' => $href, 'target' => '_blank', 'html' => true );

		// Set Styles
		if( isset( $this->element['css'] ) && (string)$this->element['css'] === 'label' )
		{
			$opts['class'] 	= Helper::css( 'box.label.container' ) . chr( 32 ) . Helper::css( 'box.label.default' );
		}
		else
		{
			$opts['size'] 	= 'mini';
			$opts['extra'] 	= 'btn-mini';
		}//end if

		if( $icon )
		{
			$opts['icon'] 	= $icon;
		}//end if

		return Helper::html( 'button.a', $opts );
	}//end function
}//end class
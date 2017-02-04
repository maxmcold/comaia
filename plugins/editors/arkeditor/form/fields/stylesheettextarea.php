<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('textarea');


/**
 * Form Field class for the Joomla Platform.
 * Supports a multi line area for entry of plain text
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 *
 */
class JFormFieldStylesheetTextarea extends JFormFieldTextarea 
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'Stylesheettextarea';
	
	/**
	 * The height of the textarea.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $height;

	/**
	 * The width of the textarea.
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $width;
	
	

	
	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'height':
			case 'width':
				$this->$name = (string) $value;
				break;
			default:
				parent::__set($name, $value);
		}
	}

	

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 * @since   3.2
	 */	
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$this->height      = $this->element['height'] ? (string) $this->element['height'] : '340px';
			$this->width       = $this->element['width'] ? (string) $this->element['width'] : '100%';

		}

		return $result;
	}	
		
		
		
	/**
	 * Method to get the textarea field input markup.
	 * Use the rows and columns attributes to specify the dimensions of the area.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	 
	protected function getInput()
	{
		// Initialize some field attributes.
		static $identifier; 
		
		if(!isset($identifier))
		{
			JHTML::_('behavior.modal');		
			$identifier = 0;
		}
		$identifier++; 

		// Initialize JavaScript field attributes.
		$button	= 
		'<br clear="left"/><br /><input type="button" class="btn" id="modal'.$identifier.'" href="../plugins/editors/arkeditor/form/fields/modals/typography.php?e_id='.$this->id.'" rel="{handler: \'iframe\' , size: {x:640, y:480}}" value="Expand View"/>
<script type="text/javascript">
window.addEvent(\'domready\', function()
{
	var dialog = document.getElementById("modal'.$identifier.'");
	dialog.addEvent("click",function()
	{
		SqueezeBox.fromElement(dialog,	{ parse: \'rel\'});
	});	
	
}); 
</script>';
		
		if($this->value)
			$this->value = base64_decode($this->value);
		

        $textarea = parent::getInput();
		
		
		return 	str_replace('<textarea ','<textarea style="overflow:auto; min-width:50%;height:'.$this->height.';width:'.$this->width.';" ',$textarea) . $button;
	}
}	
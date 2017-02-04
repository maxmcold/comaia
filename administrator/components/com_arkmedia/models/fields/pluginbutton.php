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

JFormHelper::loadFieldClass( 'button' );

/**
 * This Field Renders a Normal Bootstrap Button Linking to the Plugin's Parameters Page.
 */
class ArkFormFieldPluginButton extends ArkFormFieldButton
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'pluginbutton';

	/**
	 * The Plugins to Render.
	 *
	 * @var		string
	 */
	protected $items = array();

	/**
	 * Whether to Limit the Plugins List.
	 *
	 * @var		bool
	 */
	protected $restrict;

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
		// Set Restriction Flag
		$this->restrict = ( isset( $this->element['restrict'] ) ) ? (int)$this->element['restrict'] : true;

		// Get Plugins
		$this->getPlugins();

		// Set Data
		$html = array();
		$name = $this->fieldname;

		// Build HTML
		foreach( $this->items as $item )
		{
			// Set Temp Data
			$this->__set( 'fieldname', $name . $item->id );
			$this->__set( 'name', $name . $item->id );
			$this->__set( 'id', $name . $item->id );
			$this->element['href'] = $item->link;
			$this->element->attributes()->label = $item->name;

			// Render Button
			$html[] = parent::getInput();
		}//end foreach

		return implode( chr( 32 ), $html );
	}//end function

	/**
	 * Method to Grab All Qualified Plugins.
	 *
	 * @return	void
	 */
	protected function getPlugins()
	{
		$xml = HelperVersion::manifest();

		if( $xml )
		{
			// Get Plugins from the Manifest
			$plugins 	= $xml->xpath( '//files/file[@type="plugin" and @group and @id]' );
			$table 		= JTable::getInstance( 'extension' );

			foreach( $plugins as $plugin )
			{
				// Get the Plugin Data
				$pluginname 	= (string)$plugin->attributes()->id;
				$plugingroup 	= (string)$plugin->attributes()->group;
				$pluginid 		= $table->find( array( 'type' => 'plugin', 'folder' => $plugingroup, 'element' => $pluginname ) );
				$pluginlink		= JRoute::_( 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $pluginid, false );
				$pluginlabel	= ucwords( $pluginname );
				$pluginskip		= false;

				// Try to Get Plugin Manifest to Restrict to Param Only Plugins
				if( $this->restrict )
				{
					$pluginfile = JPATH_PLUGINS . ARKMEDIA_DS . $plugingroup . ARKMEDIA_DS . $pluginname . ARKMEDIA_DS . $pluginname . '.xml';

					if( JFile::exists( $pluginfile ) )
					{
						$pluginxml = simplexml_load_file( $pluginfile );

						if( $pluginxml )
						{
							$pluginparams = $pluginxml->xpath( '//config/fields/fieldset/field[not(@type="hidden") and not(@type="ark.multiplehidden")]' );

							// If the Plugin Has Parameters (that aren't hidden) Then Allow it to Be Displayed
							if( count( $pluginparams ) )
							{
								$pluginlabel = (string)$pluginxml->name;
							}
							else
							{
								$pluginskip = true;
							}//end if
						}//end if
					}//end if
				}//end if

				// Add Item
				if( !$pluginskip )
				{
					$this->items[] = (object)array( 'id' => $pluginname, 'name' => $pluginlabel, 'link' => $pluginlink );
				}//end if
			}//end foreach
		}//end if
	}//end function
}//end class
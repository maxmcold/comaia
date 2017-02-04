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

/**
 * This Field Renders a List of Plugin Links Linking to the Plugin's Parameters Page.
 */
class ArkFormFieldPluginLinks extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'pluginlinks';

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
	 * Method to get the field input markup for a spacer.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		return chr( 32 );
	}//end function

	/**
	 * Method to get the field label markup for a spacer.
	 * Use the label text or name from the XML element as the spacer or
	 * Use a hr="true" to automatically generate plain hr markup
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   11.1
	 */
	protected function getLabel()
	{
		// Set Restriction Flag
		$this->restrict = ( isset( $this->element['restrict'] ) ) ? (int)$this->element['restrict'] : true;

		// Get Plugins
		$this->getPlugins();

		// Set Data
		$html = array();
		$text = ( isset( $this->element['prefix'] ) ) ? (string)$this->element['prefix'] : ARKMEDIA_JTEXT;

		// Build HTML
		foreach( $this->items as $item )
		{
			// Render Row
			$html[] = '<tr>
							<td style="text-align : center;">
								<a href="' . $item->link . '" target="_blank"><i class="icon-share-alt"></i></a>
							</td>
							<td>
								<a href="' . $item->link . '">' . $item->name . '</a>&nbsp;
							</td>
							<td>' . $item->group . '</td>
							<td>' . $item->id . '</td>
							<td style="text-align : center;">
								' . ( $item->state ?
									'<i class="icon-checkmark text-success"></i>'
									:
									'<i class="icon-cancel text-error"></i>'
								) . '
							</td>
						</tr>';
		}//end foreach

		if( count( $html ) )
		{
			return '<table class="table table-striped">
						<thead>
							<tr>
								<th width="15">' . JText::_( $text . 'LINK_TTL' ) . '</th>
								<th width="400">' . JText::_( $text . 'NAME_TTL' ) . '</th>
								<th width="150">' . JText::_( $text . 'GROUP_TTL' ) . '</th>
								<th width="50">' . JText::_( $text . 'ID_TTL' ) . '</th>
								<th width="50">' . JText::_( $text . 'STATUS_TTL' ) . '</th>
							</tr>
						</thead>
						<tbody>
							' . implode( "\n", $html ) . '
						</tbody>
					</table>';
		}//end if

		return;
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
				$pluginstate	= JPluginHelper::isEnabled( $plugingroup, $pluginname );
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
					$this->items[] = (object)array( 
						'state' => $pluginstate,
						'name' 	=> $pluginlabel,
						'group' => $plugingroup,
						'link' 	=> $pluginlink,
						'id' 	=> $pluginid
					);
				}//end if
			}//end foreach
		}//end if
	}//end function
}//end class
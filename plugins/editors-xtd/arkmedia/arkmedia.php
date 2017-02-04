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

class PlgButtonArkMedia extends JPlugin
{
	/**
	 * @var		object	Application object
	 */
	public $app;

	/**
	 * @var		string	Ark Extension Name
	 */
	protected $name		= 'com_arkmedia';

	/**
	 * @var		string	Default Ark Icon CSS Name
	 */
	protected $icon		= 'arkmedia';

	/**
	 * @var		string	Default Media Button Name to Disable
	 */
	protected $default	= 'image';

	/**
	 * @var		bool	Load the Language File on Instantiation.
	 */
	protected $autoloadLanguage = true;

	/**
	 * @var		bool	Flag Portraying Asset Load Tracker
	 */
	protected static $loaded;

	/**
	 * Display An Ark Extension Button.
	 *
	 * @param	string	$name	The Name of the Button to Display.
	 * @param	string	$asset	The Name of the Asset Being Edited.
	 * @param	int		$author	The ID of the Author Owning the Asset Being Edited.
	 *
	 * @return	array	A Two Element Array of (imageName, textToInsert) or false if not Authorised.
	 */
	public function onDisplay( $name, $asset, $author )
	{
		$user 		= JFactory::getUser();
		$extension 	= $this->app->input->get( 'option' );

		// Bail out for ajax calls to prevent harming our wonderful Ark Menu Extension
		if( $extension === 'com_ajax' )
		{
			return;
		}//end if

		if( !$asset )
		{
			$asset = $extension;
		}//end if

		if( 
			$user->authorise( 'core.edit', $asset )
			|| $user->authorise( 'core.create', $asset )
			|| ( count( $user->getAuthorisedCategories( $asset, 'core.create' ) ) > 0 )
			|| ($user->authorise( 'core.edit.own', $asset ) && $author == $user->id )
			|| ( count( $user->getAuthorisedCategories( $extension, 'core.edit') ) > 0 )
			|| ( count( $user->getAuthorisedCategories( $extension, 'core.edit.own') ) > 0 && $author == $user->id )
		)
		{
			$loader	= JPATH_ADMINISTRATOR . chr( 47 ) . 'components' . chr( 47 ) . $this->name . chr( 47 ) . 'initiate.php';

			// Load Ark
			if( JFile::exists( $loader ) )
			{
				// Ensure Default Media Button is Disabled (if allowed)
				// @note	This Change Won't Be Reflected Until After the Page Has Re-Loaded.
				if( $this->params->get( 'disable-default', true ) && JPluginHelper::isEnabled( $this->_type, $this->default ) )
				{
					$table 		= JTable::getInstance( 'extension' );
					$pluginid 	= $table->find( array( 'type' => 'plugin', 'folder' => $this->_type, 'element' => $this->default ) );

					// LOAD RECORD BY EXTENSION NAME
					if( $pluginid && $table->load( $pluginid ) )
					{
						$table->publish( null, 0, $user->id );
						$table->store();
					}//end if
				}//end if

				// Initiate Ark
				defined( '_ARKMEDIA_EXEC' ) or define( '_ARKMEDIA_EXEC', true );

				require_once $loader;

				// Build Media URL
				$key = '[editor]';
				$url = array(
					'option' 		=> $this->name,
					'editor' 		=> $this->_type,
					'editorname' 	=> $key, // Use Placeholder Instead of $name
					'edit' 			=> '0',
					'stacklock'		=> '0',
					'asset' 		=> $asset,
					'author' 		=> $author,
					'language' 		=> JFactory::getLanguage()->getTag(),
					'tmpl' 			=> 'component'
				);

				// Restrict Button to Stack
				if( $this->params->get( 'folder-default', false ) )
				{
					$url['stack'] 	= $this->params->get( 'folder-default' );
				}//end if

				// Build Window Options
				$parameters = array(
					'location' 		=> 'no',
					'menubar' 		=> 'no',
					'toolbar' 		=> 'no',
					'dependent' 	=> 'yes',
					'minimizable' 	=> 'no',
					'modal' 		=> 'yes',
					'alwaysRaised' 	=> 'yes',
					'resizable' 	=> 'yes',
					'scrollbars' 	=> 'yes'
				);

				// Set Window Dimensions
				switch( $this->params->get( 'window-dimensions', false ) )
				{
					default :
					case 'auto' :
						// Leave Unset For Browser Calculation
						break;

					case 'calc' :
						$parameters['fullcreen'] 	= 'yes';
						$parameters['width'] 		= '\' + screen.width + \'';
						$parameters['height']		= '\' + screen.height + \'';
						break;

					case 'custom' :
						$x = (int)$this->params->get( 'window-x', false );
						$y = (int)$this->params->get( 'window-y', false );

						if( $x && $y )
						{
							$parameters['width'] 	= $x;
							$parameters['height']	= $y;
						}//end if
						break;
				}//end switch

				// Collapse to URL Query String (ensure sprintf's aren't encoded)
				$url = JRoute::_( 'index.php?' . JURI::buildQuery( $url ), false );

				// Collapse Window String
				$parameters = JArrayHelper::toString( $parameters, '=', ',' );

				// Strip Double Quotes Added By JString & Convert Singles to JS Doubles
				$parameters = ( str_replace( array( '"', '\'' ), array( '', '"' ), $parameters ) );

				// Only Load Assets Once
				if( !static::$loaded )
				{
					// Load Asset Path
					$basepath	= ARKMEDIA_ROOT . ARKMEDIA_DS;
					$pluginpath = str_replace( $basepath, '', dirname( __FILE__ ) . ARKMEDIA_DS );
					$options 	= (object)array(
									'name' 			=> $this->_name,
									'plugin' 		=> $pluginpath,
									'parameters' 	=> $parameters,
									'id' 			=> ARKMEDIA_COMPONENT_ID,
									'url' 			=> $url,
									'key' 			=> $key
								);
					$layouts 	= $basepath . $pluginpath . 'layouts';

					// Render Assets
					echo Helper::layout( 'js.xtd', $options, array( 'base' => $layouts, 'client' => 'auto' ) );

					// Update Flag
					static::$loaded = true;
				}//end if

				$colour				= $this->params->get( 'colour', 'primary' );
				$button 			= new JObject;
				$button->modal 		= false;
				$button->id			= $name;
				$button->class		= Helper::css( 'button.button' ) . chr( 32 ) . Helper::css( 'button.type.' . $colour );
				$button->link 		= '#'; // Can't javascript:void Till Joomla Stops JRouting the Link
				$button->onclick 	= "return jQuery( document ).triggerHandler( 'xtd:request:window', { editor : '" . $name . "' } );";// Return False to Cancel Page Submission
				$button->text 		= $this->params->get( 'text', JText::_( 'PLG_EDITORS-XTD_' . ARKMEDIA_COMPONENT_ID .'_BTN' ) );
				$button->name 		= $this->params->get( 'icon', $this->icon );

				return $button;
			}//end if
		}//end if

		return false;
	}//end function
}//end class
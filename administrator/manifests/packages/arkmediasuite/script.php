<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

class pkg_ArkMediaSuiteInstallerScript
{
	/**
	 * @var		object	Adapter Root
	 */
	protected $root 		= null;

	/**
	 * @var		bool	Is the Package Being Upgraded? (pre & post flight)
	 */
	protected $isUpgrade 	= null;

	/**
	 * @var		string	The package type (pro|basic) (post flight only)
	 */
	protected $type 		= null;

	/**
	 * @var		array	DRY location for extension name
	 */
	protected $extension	= array( 'name' => 'com_arkmedia', 'language' => 'COM_ARKMEDIA', 'media' => 'arkmedia' );

	/**
	 * @var		array	Collection of Extensions to Handle
	 */
	protected $extensions 	= array( 'plugin' => array(), 'module' => array(), 'component' => array(), 'template' => array(), 'language' => array() );

	/**
	 * Method to get the Root of the Adapter Instance.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function setRoot( JAdapterInstance $adapter )
	{
		if( is_null( $this->root ) )
		{
			$this->root = method_exists( $adapter, 'getParent' ) ? $adapter->getParent() : $adapter->parent;
		}//end if

		return ( $this->root );
	}//end function

	/**
	 * Method to set a Flag as to Whether the Package is an Upgrade from Basic to Pro or Not
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function setUpgrade( JAdapterInstance $adapter )
	{
		$this->setRoot( $adapter );

		if( $this->root )
		{
			// Get Package Name
			$manifest 	= $this->root->get( 'manifest' );
			$name 		= ( isset( $manifest->packagename ) ) ? (string)$manifest->packagename : false;

			// Get Installed Manifest Path
			$paths 		= $this->root->get( 'paths' );
			$path 		= ( isset( $paths['extension_root'] ) ) ? $paths['extension_root'] : false;

			if( $path && $name )
			{
				// Go Back One Directory
				$xmlfile = str_replace( $name, '', $path ) . 'pkg_' . $name . '.xml';

				// Get Manifest
				if( JFile::exists( $xmlfile ) )
				{
					$form = JForm::getInstance( __CLASS__, $xmlfile, array(), true, '/extension' );

					// Find & Set Package Type Flag
					if( $form instanceof JForm )
					{
						$xml 				= $form->getXML()->extension;
						$current_version 	= $xml->version->attributes();
						$new_version 		= $manifest->version->attributes();
						$current 			= (string)$current_version['type'];
						$new 				= (string)$new_version['type'];
						$this->isUpgrade 	= ( $current === 'free' && $new === 'pro' );
					}//end if
				}//end if
			}//end if
		}//end if

		return ( $this->isUpgrade );
	}//end function

	/**
	 * Method to set a the package type after the manifest has been installed free|pro.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function setType( JAdapterInstance $adapter )
	{
		$this->setRoot( $adapter );

		if( $this->root )
		{
			// Get Package Name
			$manifest 	= $this->root->get( 'manifest' );
			$version	= $manifest->version->attributes();
			$this->type	= (string)$version['type'];
		}//end if

		return $this->type;
	}//end function

	/**
	 * Method to Install the Package.
	 *
	 * @note 	These Custom Attributes Will Wipe Any Previously Stored Params in the DB.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function install( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to Uninstall the Package.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function uninstall( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to Update the Package.
	 *
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function update( JAdapterInstance $adapter )
	{

	}//end function

	/**
	 * Method to Run Before an install/update/uninstall Method.
	 *
	 * @param	string	$route		Which action is happening (install || uninstall || discover_install).
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function preflight( $route, JAdapterInstance $adapter )
	{
		// Set Package Flag for Later
		// @note 	This won't work on fresh installs as the manifest isn't copied across yet.
		// 			So don't rely on previous data matching for this scenario.
		$this->setUpgrade( $adapter );

		// Perform Script Checks If Fresh Install, Update/Upgrade (from Basic to Pro)
		// @note	If Update/Upgrade Only Check New Extensions In Order to Preserve Existing Custom Settings
		if( $route === 'install' || $route === 'update' )
		{
			// Get Current Manifest
			$manifest = ( $this->setRoot( $adapter ) ) ? $this->root->get( 'manifest' ) : new stdClass;

			// Loop Through Files to Get & Set the Extension Data
			foreach( $manifest->files->children() as $file )
			{
				// Get the XML Element Attributes
				$attributes = $file->attributes();
				$type		= (string)$attributes->type;

				switch( $type )
				{
					case 'plugin' :
						// If it's an Extension with a Custom Position or Enabled State
						if( $attributes->id && ( isset( $attributes->order ) || isset( $attributes->enabled ) ) )
						{
							$extension 			= new stdClass;
							$extension->id 		= (string)$attributes->id;					// Plugin Name/Element Column Value
							$extension->order 	= (string)$attributes->order;				// (int)
							$extension->enabled = (string)$attributes->enabled;				// 1 || 0
							$extension->group 	= (string)$attributes->group;				// Plugin Group to Allow for Later Saving
							$extensionkey		= $extension->group . '-' . $extension->id;	// ID Alone Isn't Unique so Build Unique Array Key

							// Add Entry
							$this->extensions[$type][$extensionkey] = $extension;
						}//end if
						break;
				}//end switch
			}//end foreach

			// Now Remove Extensions that are Already Installed Leaving New Extensions Only
			if( $route === 'update' )
			{
				// Get Existing/Previous Manifest
				$manifest = simplexml_load_file( JPATH_MANIFESTS . chr( 47 ) . 'packages' . chr( 47 ) . basename( $this->root->getPath( 'manifest' ) ) );

				// Loop Through Files to Unset if they are Already Installed
				foreach( $manifest->files->children() as $file )
				{
					// Get the XML Element Attributes
					$attributes = $file->attributes();
					$type		= (string)$attributes->type;

					switch( $type )
					{
						case 'plugin' :
							// Rebuild Unique Key
							$extensionid = (string)$attributes->group . '-' . (string)$attributes->id;

							if( isset( $this->extensions[$type][$extensionid] ) )
							{
								// Remove Entry
								unset( $this->extensions[$type][$extensionid] );
							}//end if
							break;
					}//end switch
				}//end foreach
			}//end if
		}//end if
	}//end function

	/**
	 * Method to Run After an install/update/uninstall Method.
	 *
	 * @param	string	$route		Which action is happening (install || update || uninstall || discover_install).
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function postflight( $route, JAdapterInstance $adapter )
	{
		// Set Package Type for Later
		// @note	We can't run this in preflight because fresh install's don't have the manifest installed yet
		$this->setType( $adapter );

		$app = JFactory::getApplication();
		$dbo = JFactory::getDBO();

		// If There are Custom Plugin Orders/States Alter the SQL Record if Found
		foreach( $this->extensions['plugin'] as $plugin )
		{
			$row 		= JTable::getInstance( 'extension' );
			$pluginid 	= $row->find( array( 'type' => 'plugin', 'folder' => $plugin->group, 'element' => $plugin->id ) );

			// Load Record by Extension Name
			if( $pluginid && $row->load( $pluginid ) )
			{
				// Set State
				if( $plugin->enabled )
				{
					$row->publish( false, (int)$plugin->enabled );
				}//end if

				// Store Changes
				$row->store();

				// Set Order
				if( $plugin->order )
				{
					$row->ordering = $plugin->order;

					// Store Changes
					$row->store();

					// Compact Ordering
					$row->reorder( $dbo->qn( 'folder' ) . ' = ' . $dbo->q( $plugin->group ) );
				}//end if
			}//end if
		}//end foreach

		// @bug J3 > Doesn't Allow for Adding Files to the Package Installer for Adding Features on Update/Upgrade so Suppress Messages
		$queue = $app->getMessageQueue();

		if( count( $queue ) )
		{
			foreach( $queue as $key => $message )
			{
				// If We Find a Message Referring to the Above Bug
				if( JText::_( 'JLIB_INSTALLER_ERROR_NOTFINDXMLSETUPFILE' ) === $message['message'] && $message['type'] === 'warning' )
				{
					// Can't Get Hold of Queue Thanks to JLogger so Hide All Warning Messages (no unique css class)
					echo '<style>
							#system-message-container .alert { display : none; }
							#system-message-container .alert-block,
							#system-message-container .alert-success,
							#system-message-container .alert-info,
							#system-message-container .alert-danger,
							#system-message-container .alert-error { display : block; }
						</style>';

					break;
				}//end if
			}//end foreach
		}//end if

		// Show pro, basic, upgrade install message
		$this->welcome( $route, $adapter );
	}//end function

	/**
	 * Method to Display a Welcome Message After an install/update/uninstall Method.
	 *
	 * @param	string	$route		Which action is happening (install || update || uninstall || discover_install).
	 * @param	class	$adapter	The Object Responsible for Running this Script.
	 */
	public function welcome( $route, JAdapterInstance $adapter )
	{
		// Specifically load the system extension language and force a language reset to ensure newly added strings are pulled through
		JFactory::getLanguage()->load( $this->extension['name'] . '.sys', JPATH_BASE, null, true );

		$message 	= JText::_( $this->extension['language'] . '_XML_' . strtoupper( $this->type ) . '_TXT' );
		$icon 		= '<i class="icon-delete text-error"></i>';
		$proicon	= '<i class="icon-ok"></i>';
		$label 		= '<span class="label label-warning pull-right">' . JText::_( $this->extension['language'] . '_XML_UPGRADE_BTN' ) . '</span>';
		$name		= JFactory::getUser()->name;
		$title		= '<h1>' . JText::_( $this->extension['language'] . '_XML_INSTALL_TTL' ) . '</h1>';
		$subtitle	= '';

		// Set to upgrade message
		if( $this->isUpgrade )
		{
			$message = JText::_( $this->extension['language'] . '_XML_UPGRADE_TXT' );
		}//end if

		// Tick pro features
		if( $this->type === 'pro' )
		{
			$icon 	= $proicon;
			$label 	= '';
		}//end if

		// Demote titles
		if( $name )
		{
			$title		= '<h1>' . JText::sprintf( $this->extension['language'] . '_XML_NAME_TTL', $name ) . '</h1>';
			$subtitle	= '<h2>' . JText::_( $this->extension['language'] . '_XML_INSTALL_TTL' ) . '</h2>';
		}//end if

		// Only Display on Installer Screen (not config screen), Colour Required for Tooltips
		echo '<link rel="stylesheet" href="../media/' . $this->extension['media'] . '/css/ark-logo.min.css" type="text/css" />
			<style>
				.ark-install-box .ark-logo
				{
					width 				: auto;
					height 				: auto;
					margin 				: 0px;

					color 				: #0099CC;
					font-size 			: 150px;
				}

				.ark-install-box 				{ display : none; }
				.com_installer .ark-install-box { display : block; }
				.ark-install-box h1 			{ color : #333333; font-size : 60px; }
				.ark-install-box h2 			{ color : #333333; font-size : 40px; }

				.com_installer .ark-install-box
				{
					padding				: 0px;
					position 			: relative;

					border				: 1px solid #cdcdcd;
					border-radius		: 0px;
					background-color	: #ffffff;
				}

				.com_installer .ark-header
				{
					padding				: 60px 20px;
					background-color	: #f5f5f5;
				}

				.com_installer .ark-content
				{
					padding				: 60px;
					background-color	: #ffffff;
				}

				.com_installer .ark-content i,
				.com_installer .ark-content .label
				{
					margin				: 10px 20px 10px 10px;
				}

				.ark-install-box .btn.btn-large
				{
					width 				: 33.3333%;
					position			: absolute;
					left 				: 0px;
					bottom 				: 0px;
					box-sizing			: border-box;

					color				: #444444;
					border				: 0px none;
					border-top			: 1px solid #cdcdcd;
					border-radius		: 0px;
					background-color	: #ffffff;
					background-image	: none;
					box-shadow			: 0px none;
				}

				.ark-install-box .btn.btn-large:hover
				{
					color				: #333333;
					background-color	: #eeeeee;
				}

				.ark-install-box .btn:nth-of-type(1) { left : 0% }
				.ark-install-box .btn:nth-of-type(2) { left : 33.3333% }
				.ark-install-box .btn:nth-of-type(3) { left : 66.6666% }
			</style>';

		echo '<div class="ark-install-box hero-unit">
				<div class="ark-header">
					<i class="ark-logo icon-' . $this->extension['media'] . '"></i>
					' . $title . '
					' . $subtitle . '
					<p>' . $message . '</p>
				</div>
				<div class="ark-content">
					<table class="table table-striped">
						<tr>
							<td>
								' . $proicon . JText::_( $this->extension['language'] . '_XML_ALL_OPT1' ) . '
							</td>
						</tr>
						<tr>
							<td>
								' . $proicon . JText::_( $this->extension['language'] . '_XML_ALL_OPT2' ) . '
							</td>
						</tr>
						<tr>
							<td>
								' . $proicon . JText::_( $this->extension['language'] . '_XML_ALL_OPT3' ) . '
							</td>
						</tr>
						<tr>
							<td>
								' . $proicon . JText::_( $this->extension['language'] . '_XML_ALL_OPT4' ) . '
							</td>
						</tr>
						<tr>
							<td>
								' . $proicon . JText::_( $this->extension['language'] . '_XML_ALL_OPT5' ) . '
							</td>
						</tr>
						<tr>
							<td>
								' . $icon . JText::_( $this->extension['language'] . '_XML_PRO_OPT1' ) . $label . '
							</td>
						</tr>
						<tr>
							<td>
								' . $icon . JText::_( $this->extension['language'] . '_XML_PRO_OPT2' ) . $label . '
							</td>
						</tr>
						<tr>
							<td>
								' . $icon . JText::_( $this->extension['language'] . '_XML_PRO_OPT3' ) . $label . '
							</td>
						</tr>
						<tr>
							<td>
								' . $icon . JText::_( $this->extension['language'] . '_XML_PRO_OPT4' ) . $label . '
							</td>
						</tr>
						<tr>
							<td>
								' . $icon . JText::_( $this->extension['language'] . '_XML_PRO_OPT5' ) . $label . '
							</td>
						</tr>
					</table>
				</div>
				<a class="btn btn-large" href="index.php?option=com_config&amp;view=component&amp;component=' . $this->extension['name'] . '"><i class="icon-cog"></i> Config</a>
				<a class="btn btn-large" href="http://docs.arkextensions.com" target="_blank"><i class="icon-book"></i> Docs</a>
				<a class="btn btn-large" href="index.php?option=' . $this->extension['name'] . '"><i class="icon-play text-info"></i> Start!</a>
			</div>';
	}//end function
}//end class
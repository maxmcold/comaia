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

class PlgQuickIconArkMedia extends JPlugin
{
	/**
	 * @var		string	Ark Extension Name
	 */
	protected $name	= 'com_arkmedia';

	/**
	 * @var		string	Ark Extension JText Entry
	 */
	protected $key	= 'COM_ARKMEDIA';

	/**
	 * @var		string	Ark Icon CSS Name
	 *
	 * @todo 	Make This Option a Plugin Parameter for Users to Pick Their Own Icon
	 */
	protected $icon	= 'arkmedia';

	/**
	 * @var		string	Error Message If the Ark Extension Isn't Installed
	 *
	 * @todo 	JText this Option or Make Plugin Parameter
	 */
	protected $msg	= 'Ark Media Not Installed!';

	/**
	 * Add an Ark Icon.
	 *
	 * @param	string	$context	The calling context
	 *
	 * @return	array	A list of icon definition associative arrays, consisting of the
	 * 					keys link, image, text and access.
	 */
	public function onGetIcons( $context )
	{
		if( $context != $this->params->get( 'context', 'mod_quickicon' ) )
		{
			return;
		}//end if

		// Load Language
		$lang 	= JFactory::getLanguage();
		$lang->load( $this->name );

		// Check Extension
		$ark 	= $lang->hasKey( $this->key );
		$loader	= JPATH_ADMINISTRATOR . chr( 47 ) . 'components' . chr( 47 ) . $this->name . chr( 47 ) . 'initiate.php';

		// Setup Icon
		$link 	= ( $ark ) ? 'index.php?option=' . $this->name : '#';
		$icon 	= ( $ark ) ? $this->icon : 'warning-circle';
		$text 	= ( $ark ) ? JText::_( $this->key ) : $this->msg;

		// Load Styles
		if( $ark && JFile::exists( $loader ) )
		{
			// Initiate Ark
			defined( '_ARKMEDIA_EXEC' ) or define( '_ARKMEDIA_EXEC', true );

			require_once $loader;

			// Load Logo Font
			Helper::html( 'ark.css', 'ark-logo.min' );
		}//end if

		return array(
			array(
				'link' 		=> $link,
				'image' 	=> $icon,
				'text' 		=> $text,
				'access' 	=> array( 'core.manage', $this->name ),
				'group' 	=> 'MOD_QUICKICON_CONTENT'
			)
		);
	}//end function
}//end class
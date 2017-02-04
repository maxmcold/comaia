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

jimport( 'joomla.application.component.controlleradmin' );

class ArkMediaControllerList extends JControllerAdmin
{
	/**
	 * @var    object  JApplication Class
	 */
	protected $app;

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 */
	public function __construct( $config = array() )
	{
		$this->app 		= JFactory::getApplication();
		$this->event 	= JDispatcher::getInstance();

		parent::__construct( $config );

		// Fire Start Event (after controller has been setup)
		$this->event->trigger( 'onArkBeforeTask', array( &$this ) );
	}//end function

	/**
	 * 'Destructor' to Fire 'After' Event Before Ending Controller Process.
	 *
	 * @param	string	$type		The Type of End Process to Run (redirect|ajax).
	 * @param	mixed	$return		The Data to Return or URL to Redirect to.
	 * @param	mixed	$event		The Data to Pass to the Event.
	 */
	protected function _end( $type = null, $return = null, $event = null )
	{
		// Pass Ajax Data if No Event Data
		if( !$event && $type === 'ajax' )
		{
			$event = $return;
		}//end if

		// Fire End Event
		$this->event->trigger( 'onArkAfterTask', array( &$this, $event ) );

		switch( $type )
		{
			default :
			case 'redirect' :
				return $this->setRedirect( $return );

			case 'ajax' :
				echo $return;

				jexit();
				break;

			case 'source' :
				jexit();
				break;
		}//end switch
	}//end function

	/**
	 * Take the Passed Thumb File and Stream the Image to the Browser.
	 *
	 * @note	This Function is Bypassed if Streaming is Disabled as Thumbnails are Loaded Directly.
	 * 
	 * @param	string	$path		A Form Option - The Thumbnail Cache Path.
	 *
	 * @method	source
	 *
	 * @return	void
	 */
	public function thumbnail()
	{
		// Get Request Data
		$stack 	= $this->app->input->get( 'stack', false, 'cmd' );
		$path 	= $this->app->input->get( 'path', false, 'string' );
		$file 	= $this->app->input->get( 'file', false, 'string' );
		$size 	= $this->app->input->get( 'size', false, 'string' );
		$extra 	= $this->app->input->get( 'extra', false, 'string' );

		// Build/Retrieve Thumbnail
		$model	= $this->getModel( 'list' );
		$thumb 	= $model->getThumbnail( $stack, $path, $file, $size, $extra );

		// Stream Thumbnail
		if( $thumb )
		{
			$model->streamThumbnail( $thumb );
		}//end if

		$this->_end( 'source' );
	}//end function

	/**
	 * Load a Folder's Child Data & Return in JSON Form.
	 * 
	 * @param	string	$stack		A Form Option - The Desired Stack Name the Path Resides.
	 * @param	string	$path		A Form Option - The Path to Load Up.
	 * @param	string	$branch		A Form Option - Load the Full Branch or Just the Leaf (and shallow children).
	 *
	 * @method	ajax
	 *
	 * @return	json	Directory Item Return Data
	 */
	public function path()
	{
		$stack 	= $this->app->input->get( 'stack', false, 'cmd' );
		$path 	= $this->app->input->get( 'path', false, 'string' ); // Path Filter Strips Out Spaces...
		$branch = $this->app->input->get( 'branch', false, 'bool' );
		$model	= $this->getModel( 'list' );
		$data 	= $model->path( $stack, $path, $branch );

		// Render Data
		// @see		See Stack Helper for Conversion Details
		$result = ( is_array( $data ) ) ? json_encode( $data, JSON_FORCE_OBJECT ) : '{}';

		$this->_end( 'ajax', $result );
	}//end function

	/**
	 * This Task Refreshes the Server Cache (which after redirect refreshes the client cache as well).
	 *
	 * @return	void
	 */
	public function refresh()
	{
		// Check Permissions
		if( Helper::actions( 'ark.ui.refresh' ) )
		{
			// By Clearing the Server Cache the Next Load we see that it has Been Cleared & Schedule the Client Cache to be Cleared as well
			$this->getModel( 'list' )->refresh();

			$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_REFRESH_SUCCESS' ), 'message' );
		}
		else
		{
			$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDREFRESH_FAIL' ), 'warning' );
		}//end if

		$redirect = base64_decode( $this->app->input->get( 'redirect', false, 'base64' ) );

		$this->_end( 'redirect', ( $redirect ?: JURI::base() . 'index.php?option=' . ARKMEDIA_COMPONENT ) );
	}//end function

	/**
	 * Create the Passed Stack's Root Folder if it Doesn't Exist.
	 * 
	 * @param	string	$stack		A Form Option - The Desired Stack Name.
	 *
	 * @return	void
	 */
	public function plant()
	{
		JSession::checkToken( 'get' ) or jexit( JText::_( 'JINVALID_TOKEN' ) );

		// Check Permissions
		if( Helper::actions( 'ark.action.folder' ) )
		{
			$stack = $this->app->input->get( 'stack', false, 'cmd' );

			if( $stack )
			{
				try
				{
					if( $this->getModel( 'list' )->plant( $stack ) )
					{
						$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_PLANT_SUCCESS' ), 'message' );
					}
					else
					{
						$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_PLANT_FAIL' ), 'warning' );
					}//end if
				}
				catch( Exception $e )
				{
					$this->app->enQueueMessage( ( $e->getMessage() ?: JText::_( ARKMEDIA_JTEXT . 'MSG_PLANT_FAIL' ) ), 'warning' );
				}//end try
			}
			else
			{
				$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_PLANTNOSTACK_FAIL' ), 'warning' );
			}//end if
		}
		else
		{
			$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDPLANT_FAIL' ), 'warning' );
		}//end if

		$redirect = base64_decode( $this->app->input->get( 'redirect', false, 'base64' ) );

		$this->_end( 'redirect', ( $redirect ?: JURI::base() . 'index.php?option=' . ARKMEDIA_COMPONENT ) );
	}//end function

	/**
	 * This Task Uploads a File to the Currently Active Directory.
	 * 
	 * @param	string	$stack		A Form Option - The Desired Stack Name the Path Resides.
	 * @param	string	$path		A Form Option - The Path to Create the Folder in.
	 * @param	string	$file		A Form Option - The Desired File to Upload.
	 *
	 * @return	void
	 */
	public function file()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		$form 		= $this->app->input->get( 'jform', array(), 'array' );
		$files 		= $this->app->input->files->get( 'jform', array(), 'array' );
		$model		= $this->getModel( 'list' );
		$redirect 	= ( $form['redirect'] ) ? '&' . $form['redirect'] : '';
		$file 		= null;

		// Only Continue if Stack, Path Were Provided
		if( $form['stack'] && $form['path'] && $files['file'] )
		{
			// Upload File
			try
			{
				// Check Permissions
				if( !Helper::actions( 'ark.action.upload' ) )
				{
					throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDUPLOAD_FAIL' ) );
				}//end if

				$file = $model->file( $form, $files['file'] );
			}
			catch( Exception $e )
			{
				// Add Generic Message First
				if( $e->getMessage() )
				{
					$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_FILE_FAIL' ), 'warning' );
				}//end if

				// Add Specific Message
				$this->app->enQueueMessage( ( $e->getMessage() ?: JText::_( ARKMEDIA_JTEXT . 'MSG_FILE_FAIL' ) ), 'warning' );
			}//end try

			// Set Status Message
			if( $file )
			{
				$this->app->enQueueMessage( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILESPRINTF_SUCCESS', $file, $form['path'] ), 'message' );

				// Check for Renames
				if( $file !== $files['file']['name'] )
				{
					$this->app->enQueueMessage( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILERENAME_SUCCESS', $files['file']['name'], $file ), 'message' );
				}//end if
			}
			elseif( !isset( $e ) ) // If Exception a Message Has Already Been Set
			{
				$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_FILE_PARTIAL' ), 'info' );
			}//end if
		}
		else
		{
			$this->app->enQueueMessage( ( !$files['file'] ? JText::_( ARKMEDIA_JTEXT . 'MSG_FILENOFILEUPLOAD_FAIL' ) : JText::_( ARKMEDIA_JTEXT . 'MSG_FILENOPATHUPLOAD_FAIL' ) ), 'warning' );
		}//end if

		$url = JURI::base() . 'index.php?option=' . ARKMEDIA_COMPONENT . '&stack=' . $form['stack'] . '&path=' . urlencode( $form['path'] ) . $redirect;

		$this->_end( 'redirect', $url, $file );
	}//end function

	/**
	 * This Task Creates a New Folder in the Currently Active Directory.
	 * 
	 * @param	string	$stack		A Form Option - The Desired Stack Name the Path Resides.
	 * @param	string	$path		A Form Option - The Path to Create the Folder in.
	 * @param	string	$folder		A Form Option - The Desired Name of the Folder (optional).
	 *
	 * @return	void
	 */
	public function folder()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		$form 		= $this->app->input->get( 'jform', array(), 'array' );
		$model		= $this->getModel( 'list' );
		$redirect 	= ( $form['redirect'] ) ? '&' . $form['redirect'] : '';
		$folder 	= null;

		// Only Continue if Path Was Provided
		if( $form['stack'] && $form['path'] )
		{
			// Upload File
			try
			{
				// Check Permissions
				if( !Helper::actions( 'ark.action.folder' ) )
				{
					throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDFOLDER_FAIL' ) );
				}//end if

				// Create Folder
				$folder = $model->folder( $form );
			}
			catch( Exception $e )
			{
				// Add Generic Message First
				if( $e->getMessage() )
				{
					$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDER_FAIL' ), 'warning' );
				}//end if

				// Add Specific Message
				$this->app->enQueueMessage( ( $e->getMessage() ?: JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDER_FAIL' ) ), 'warning' );
			}//end try

			// Set Status Message
			if( $folder )
			{
				$this->app->enQueueMessage( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FOLDERSPRINTF_SUCCESS', $folder, $form['path'] ), 'message' );

				// Check for Renames (blank names are auto named)
				if( $form['folder'] && $folder !== $form['folder'] )
				{
					$this->app->enQueueMessage( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_FILERENAME_SUCCESS', $form['folder'], $folder ), 'message' );
				}//end if
			}
			elseif( !isset( $e ) ) // If Exception a Message Has Already Been Set
			{
				$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDER_FAIL' ), 'error' );
			}//end if
		}
		else
		{
			$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDERNOPATH_FAIL' ), 'warning' );
		}//end if

		$url = JURI::base() . 'index.php?option=' . ARKMEDIA_COMPONENT . '&stack=' . $form['stack'] . '&path=' . urlencode( $form['path'] ) . $redirect;

		$this->_end( 'redirect', $url, $folder );
	}//end function

	/**
	 * This Task Deletes Folders & Files from the Currently Active Directory.
	 * 
	 * @param	string	$stack		A Form Option - The Desired Stack Name the Path Resides.
	 * @param	string	$path		A Form Option - The Path to Create the Folder in.
	 * @param	string	$folders	A Form Option - The Desired Folders to Delete.
	 * @param	string	$files		A Form Option - The Desired Files to Delete.
	 *
	 * @return	void
	 */
	public function delete()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		$form 		= $this->app->input->get( 'jform', array(), 'array' );
		$model		= $this->getModel( 'list' );
		$redirect 	= ( $form['redirect'] ) ? '&' . $form['redirect'] : '';

		// Only Continue if Stack, Path & Items Were Provided
		if( $form['stack'] && $form['path'] && ( $form['folders'] || $form['files'] ) )
		{
			// Delete Items
			try
			{
				// Check Permissions
				if( !Helper::actions( 'ark.action.remove' ) )
				{
					throw new Exception( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDDELETE_FAIL' ) );
				}//end if

				// Decode Items & Process
				$form['folders'] 	= json_decode( $form['folders'] );
				$form['files'] 		= json_decode( $form['files'] );
				$items 				= $model->delete( $form );
			}
			catch( Exception $e )
			{
				// Add Generic Message First
				if( $e->getMessage() )
				{
					$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_DELETE_FAIL' ), 'warning' );
				}//end if

				// Add Specific Message
				$this->app->enQueueMessage( ( $e->getMessage() ?: JText::_( ARKMEDIA_JTEXT . 'MSG_DELETE_FAIL' ) ), 'warning' );
			}//end try

			// Set Status Message
			if( $items && count( $items ) )
			{
				$this->app->enQueueMessage( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_DELETESPRINTF_SUCCESS', $form['path'], implode( '</li><li>', $items ) ), 'message' );
			}
			elseif( !isset( $e ) ) // If Exception a Message Has Already Been Set
			{
				$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_DELETE_PARTIAL' ), 'info' );
			}//end if
		}
		else
		{
			// Add Generic Message First
			$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_DELETE_FAIL' ), 'warning' );

			if( !$form['stack'] || !$form['path'] )
			{
				$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_DELETENOPATH_FAIL' ), 'warning' );
			}
			elseif( !$form['folders'] || !$form['files'] )
			{
				$this->app->enQueueMessage( JText::_( ARKMEDIA_JTEXT . 'MSG_DELETENOSELECTED_FAIL' ), 'warning' );
			}//end if
		}//end if

		$url = JURI::base() . 'index.php?option=' . ARKMEDIA_COMPONENT . '&stack=' . $form['stack'] . '&path=' . urlencode( $form['path'] ) . $redirect;

		$this->_end( 'redirect', $url, $items );
	}//end function

	/**
	 * This Task Performs an Action on a Directory Item Both of Which are Defined in the Form Data
	 * 
	 * @param	bool	$ajax		Whether this Call is an Ajax Call or a Standard Redirect.
	 * @param	string	$stack		A Form Option - The Desired Stack Name the Path Resides.
	 * @param	string	$path		A Form Option - The Path the Item Resides.
	 * @param	string	$item		A Form Option - The Item to Perform the Action on.
	 * @param	string	$action		A Form Option - The Action to Perform.
	 *
	 * @method	ajax || standard
	 * 
	 * @return	mixed 	If Ajax: Result, If Standard: void.
	 */
	public function action()
	{
		JSession::checkToken() or jexit( JText::_( 'JINVALID_TOKEN' ) );

		$ajax 		= $this->app->input->get( 'ajax', 0, 'int' );
		$form 		= $this->app->input->get( 'jform', array(), 'array' );
		$model		= $this->getModel( 'list' );
		$result 	= (object)array( 'status' => false, 'messages' => array() );
		$items 		= false;
		$redirect 	= ( $form['redirect'] ) ? '&' . $form['redirect'] : '';

		// Only Continue if Stack, Path, Item & Action Were Provided
		if( $form['stack'] && $form['path'] && $form['item'] && $form['action'] )
		{
			// Try Action
			try
			{
				// Check Permissions
				if( !Helper::actions( 'ark.action.' . $form['action'] ) )
				{
					throw new Exception( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDACTION_FAIL', JText::_( ARKMEDIA_JTEXT . 'ACTIONS_' . JString::strtoupper( $form['action'] ) . '_LBL' ) ) );
				}//end if

				switch( $form['action'] )
				{
					default : 
						$items 				= false;
						break;

					case 'copy' :
						$form['files'] 		= $form['item'];
						$items 				= $model->copy( $form );

						 // Result = Array of key (old name) => val (new name)
						if( is_array( $items ) )
						{
							$result->status	= true;
							$message 		= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_ACTIONCOPY_SUCCESS', implode( ', ', array_keys( $items ) ), implode( ', ', array_values( $items ) ), $form['path'] );
						}//end if
						break;

					case 'move' :
						// Build Item for Move & Process
						$form['files'] 		= $form['item'];
						$items 				= $model->move( $form );

						if( is_array( $items ) )
						{
							// @feature : To Redirect to the destination path rather than original path uncomment this line: $form['path'] = $item->to;
							$item 			= current( $items );
							$message 		= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_ACTIONMOVE_SUCCESS', $item->name, $item->from, $item->to );
							$result->status	= true;

							// Inform of a File Rename
							if( $item->name != $form['item'] )
							{
								Helper::message( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_ACTIONMOVERENAME_INFO', $form['item'], $item->name ), 'message', (object)array( 'persist' => true ) );
							}//end if
						}//end if
						break;

					case 'remove' :
						// Build Item for Delete & Process
						$form['files'] 		= $form['item'];
						$items				= $model->delete( $form );

						if( is_array( $items ) )
						{
							$result->status	= true;
							$message 		= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_ACTIONKILL_SUCCESS', implode( ', ', $items ), $form['path'] );
						}//end if
						break;

					case 'rename' :
						// Build Item for Rename & Process
						$form['files'] 		= $form['item'];
						$items				= $model->rename( $form );

						// Result = Array of key (old name) => val (new name)
						if( is_array( $items ) )
						{
							$result->status	= true;
							$message 		= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_ACTIONRENAME_SUCCESS', implode( ', ', array_keys( $items ) ), implode( ', ', array_values( $items ) ), $form['path'] );
						}//end if
						break;
				}//end switch
			}
			catch( Exception $e )
			{
				// Add Generic Message First
				if( $e->getMessage() )
				{
					Helper::message( JText::_( ARKMEDIA_JTEXT . 'MSG_ACTION_FAIL' ), 'warning', (object)array( 'persist' => true ) );
				}//end if

				// Add Specific Message
				Helper::message( ( $e->getMessage() ?: JText::_( ARKMEDIA_JTEXT . 'MSG_ACTION_FAIL' ) ), 'warning', (object)array( 'persist' => true ) );
			}//end try

			$action = JText::_( ARKMEDIA_JTEXT . 'ACTIONS_' . JString::strtoupper( $form['action'] ) . '_LBL' );

			// Set Status Message
			if( $items && count( $items ) )
			{
				Helper::message( $message, 'message', (object)array( 'persist' => true ) );
			}
			elseif( !isset( $e ) ) // If Exception a Message Has Already Been Set
			{
				Helper::message( JText::sprintf( ARKMEDIA_JTEXT . 'MSG_ACTION_PARTIAL', $action ), 'info', (object)array( 'persist' => true ) );
			}//end if
		}
		else
		{
			// Add Generic Message First
			Helper::message( JText::_( ARKMEDIA_JTEXT . 'MSG_ACTION_FAIL' ), 'warning', (object)array( 'persist' => true ) );

			if( !$form['stack'] || !$form['path'] )
			{
				Helper::message( JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONNOPATH_FAIL' ), 'warning', (object)array( 'persist' => true ) );
			}
			elseif( !$form['item'] )
			{
				Helper::message( JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONNOITEM_FAIL' ), 'warning', (object)array( 'persist' => true ) );
			}//end if
		}//end if

		if( $ajax )
		{
			// Collect Queue
			$result->messages = $this->app->getMessageQueue();

			$this->_end( 'ajax', json_encode( $result ), $items );
		}
		else
		{
			$url = JURI::base() . 'index.php?option=' . ARKMEDIA_COMPONENT . '&stack=' . $form['stack'] . '&path=' . urlencode( $form['path'] ) . $redirect;

			$this->_end( 'redirect', $url, $items );
		}//end if
	}//end function
}//end class
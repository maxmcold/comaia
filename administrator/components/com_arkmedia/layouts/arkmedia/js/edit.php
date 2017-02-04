<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperEdit;

defined( '_JEXEC' ) or die;

Helper::add( 'helper', 'edit' );

$view			= $displayData;
$app 			= $view->app;
$options 		= $view->content;
$form 			= $view->model->getForm( 'edit', false, true );
$fields			= $form->getXml()->xpath( '//field' );
$datamap		= array();

// If Initiated by an Editor Allow Edit Mode
$enabled		= HelperEdit::isEditMode();
$full			= HelperEdit::isEditFull();
$wait			= HelperEdit::isEditWait();
$quick			= HelperEdit::isEditQuick();
$settings		= HelperEdit::isEditSettings();
$stacklock		= HelperEdit::isStackLock();
$base64			= HelperEdit::isBase64();
$editor			= HelperEdit::getEditor();
$field			= HelperEdit::getField();
$stacks			= HelperEdit::getStacks();
$stack 			= HelperEdit::getStack();
$path 			= addslashes( HelperEdit::getPath() ); // Make JS Safe
$file			= HelperEdit::getFile();
$callback		= HelperEdit::getCallback( 'jInsertEditorText' );

// Create Delegate Tooltips for Lazy Initialising
$css_root		= '#' . Helper::css( 'edit.container' );
$css_tooltip	= '.' . Helper::css( 'joomla.tooltip' );
$css_container	= '#' . Helper::css( 'root' );

// Check Permissions
if( !Helper::actions( 'ark.insert.manage' ) )
{
	// If Initiated by an Editor Throw Error, Otherwise Die Quietly
	if( $editor )
	{
		Helper::message( JText::_( ARKMEDIA_JTEXT . 'MSG_PERMISSION_NOTAUTHORISEDINSERT_FAIL' ), 'warning' );
	}//end if

	return;
}//end if

// If the Edited File is Base64 Inform User (file has since been cleared by the HelperEdit)
// @note	This Notice Could be Thrown in Multiple Locations But Here Seemed More Logical.
// 			Files to Check the Edit File Option Are: layouts.js.observer, layouts.js.edit & HelperStack::_loadStack
if( $base64 )
{
	Helper::message( JText::_( ARKMEDIA_JTEXT . 'MSG_EDITBASE64_FAIL' ), 'info' );
}//end if

// @required ark-edit.min.js
JHTML::_( 'bootstrap.tooltip', $css_root, array( 'selector' => $css_tooltip, 'container' => $css_container ) );
JHTML::_( 'formbehavior.chosen' );

// Get Field Data Attributes for Data Mapping to Stack Plugins
foreach( $fields as $element )
{
	$attr_fieldname = (string)$element['name'];
	$attr_attribute = (string)$element['data-attribute'];
	$attr_stackname = ( isset( $element['data-stack'] ) ) ? (string)$element['data-stack'] : '';
	$attr_converter = ( isset( $element['data-converter'] ) ) ? (string)$element['data-converter'] : 'full';
	$attr_separator = ( isset( $element['data-separator'] ) ) ? (string)$element['data-separator'] : chr( 32 );
	$attr_omissions = ( isset( $element['data-omissions'] ) ) ? (string)$element['data-omissions'] : '';

	// Check Required Data
	if( $attr_fieldname && $attr_attribute )
	{
		$attr_omissions	= array_filter( explode( ',', $attr_omissions ) ); // Format Omissions
		$datamap[$attr_fieldname] = (object)array(
										'fieldname' => $attr_fieldname,
										'attribute' => $attr_attribute,
										'converter' => $attr_converter, // How the Attr Value Should be Interpreted
										'separator' => $attr_separator, // How Multiple Values are Concatenated
										'stack' 	=> $attr_stackname, // Whether this Field is Restricted to a Stack
										'omissions' => $attr_omissions, // List of Entries to Exclude
										'options' 	=> array()
									);

		// Add Child XML Options
		foreach( $element->children() as $child )
		{
			$val = (string)$child['value'];

			// Check the Child Has a Value (default/blank values are handled by JS)
			if( $val )
			{
				$datamap[$attr_fieldname]->options[] = $val;
			}//end if
		}//end foreach
	}//end if
}//end foreach
?>
<script>
	var edit;

	jQuery( document ).ready( function( $ )
	{
		edit = jQuery.fn.edit(
		{
			css			: {
							contenttopbar	: '#<?php echo Helper::css( 'containers.content.top' ); ?>',
							editbar 		: '#<?php echo Helper::css( 'containers.content.edit' ); ?>',
							actionbar 		: '#<?php echo Helper::css( 'actions' ); ?>',
							subactionbar 	: '#<?php echo Helper::css( 'subactions' ); ?>',
							editactionbar 	: '#<?php echo Helper::css( 'edit.actions' ); ?>',
							preview			: {
												container 	: '#<?php echo Helper::css( 'edit.preview' ); ?>',
												holder 		: '.preview',
												preview 	: '*',
												loading		: '.loading',
												failed		: '.failed'
											  },
							settings 		: {
												container 	: '#<?php echo Helper::css( 'edit.settings' ); ?>',
												fieldset 	: '.<?php echo Helper::css( 'form.fieldset' ); ?>',
												group 		: '.<?php echo Helper::css( 'form.group' ); ?>',
												field 		: '.<?php echo Helper::css( 'form.control' ); ?>'
											  }
						  },
			html 		: {
							messages		: {
												save 		: "<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_EDITSAVE_SUCCESS', true ); ?>"
											  },
							errors			: {
												invalid 	: "<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONINVALIDPATH_FAIL', true ); ?>",
												nofile 		: "<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_EDITNOFILE_FAIL', true ); ?>",
												noparent 	: "<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_INSERTNOPARENT_FAIL', true ); ?>",
												filelost 	: "<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_INSERTFILELOST_FAIL', true ); ?>"
											  }
						  },
			enabled		: <?php echo (int)$enabled; ?>,
			full		: <?php echo (int)$full; ?>,
			wait		: <?php echo (int)$wait; ?>,
			quick		: <?php echo (int)$quick; ?>,
			settings	: <?php echo (int)$settings; ?>,
			lock		: <?php echo (int)$stacklock; ?>,
			edit 		: {
							editor			: '<?php echo $editor; ?>',
							field			: '<?php echo $field; ?>',
							stacks			: <?php echo json_encode( $stacks ); ?>,
							stack			: '<?php echo $stack; ?>',
							path			: '<?php echo $path; ?>',
							file			: '<?php echo $file; ?>',
							callback		: '<?php echo $callback; ?>'
						  },
			xml 		: <?php echo JHTML::getJSObject( $datamap ); ?>,
			layout 		: 'right',
			permission 	: {
							insert			: 'ark.insert.manage',
							settings		: 'ark.insert.settings'
						  }
		});
	});
</script>
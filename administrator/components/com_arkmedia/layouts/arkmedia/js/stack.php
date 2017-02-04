<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperStack, Ark\Media\HelperEdit;

defined( '_JEXEC' ) or die;

// @required ark-stack.min.js

// Register Stacks (should already be loaded so no performance hit here...)
HelperStack::register();
Helper::add( 'helper', 'edit' );

$active_url = HelperEdit::getStack();
$active_opt = ( $active_url ) ?: Helper::params( 'folder-default' );
$stack_data	= HelperStack::get();
$stacks 	= array_keys( $stack_data );

// If No Stack Plugins Show Error
if( !count( $stacks ) )
{
	$message_ttl = JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_NOSTACKSFOUND_TTL' );
	$message_txt = JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_NOSTACKSFOUND_DESC' );
	$message_sub = JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_NOSTACKSFOUND_SUB' );
	$message_btn = (object)array( 'text' => JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_NOSTACKSFOUND_BTN' ), 'href' => 'index.php?option=com_plugins&filter_folder=' . ARKMEDIA_PLUGIN_STACK, 'icon' => 'power-cord' );

	Helper::contentMessage( $message_ttl, array( 'text' => $message_txt, 'sub' => $message_sub ), $message_btn );
}//end if

// If:
// - The Stack in the URL Does Not Exist 
// - A Config Value Hasn't Been Set
// - A Config Value is Set But Isn't a Currently Valid/Enabled Stack
// Then Switch to One we Know is Okay (the first one we find in the array)
if( ( $active_url && !in_array( $active_url, $stacks ) ) || !$active_opt || ( $active_opt && !in_array( $active_opt, $stacks ) ) )
{
	$active_opt = current( $stacks );
}//end if

?>
<script>
	var stack;

	jQuery( document ).ready( function( $ )
	{
		stack 		= jQuery.fn.stack(
		{
			css		: {
						control			: '[data-stack-control]'
					  },
			active	: '<?php echo $active_opt; ?>'
		});
	});
</script>
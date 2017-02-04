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

$stack 						= $displayData->stack;
$buttons 					= (array)$displayData->buttons;
$file						= basename( __FILE__, '.php' );

$message					= new stdClass;
$message->title 			= JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_' . strtoupper( $file ) . '_TTL' );
$message->message['text'] 	= JText::_( ARKMEDIA_JTEXT . 'MSG_CONTENT_' . strtoupper( $file ) . '_DESC' );
$message->message['sub'] 	= JText::sprintf( ARKMEDIA_JTEXT . 'MSG_CONTENT_' . strtoupper( $file ) . '_SUB', '{{:path}}' );
$message->icon 				= 'spinner';
?>
<script id="tmpl-content-<?php echo $stack; ?>-message-<?php echo $file; ?>" type="text/x-jsrender">
	<div class="<?php echo Helper::css( 'text.align.center' ); ?>">
		<?php echo Helper::layout( 'message.generic', (object)array( 'messages' => array( $message ) ) ); ?>
	</div>
</script>
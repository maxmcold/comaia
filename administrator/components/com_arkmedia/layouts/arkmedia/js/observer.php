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

$json 	= $displayData->json;
$app	= $displayData->app;

if( !$json )
{
	return;
}//end if

Helper::add( 'helper', 'edit' );

// Alternatively Could Include Joomla/Mootools-More Date.format()?
// @required moment.min.js
// @required jsviews.min.js
// @required ark-observer.min.js

// Set Defaults (if any)
$options 			= array();
$options['stack'] 	= HelperEdit::getStack();
$options['path'] 	= HelperEdit::getPath();

?>
<script>
	var observer;

	jQuery( document ).ready( function( $ )
	{
		observer 	= jQuery.fn.observer(
		{
			html 	: {
						ajax		: {
										item 		: '<?php echo JRoute::_( 'index.php?option=' . ARKMEDIA_COMPONENT . '&task=list.path', false ); ?>'
									  },
						errors		: {
										malform 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_OBSERVERAJAX_FAIL', true ); ?>',
										fail 		: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_OBSERVERAJAX_FAIL', true ); ?>',
										ajax 		: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_OBSERVERAJAX_FAIL', true ); ?>'
									  }
					  },
			stacks	: <?php echo $json; ?>,
			defaults: <?php echo JHtml::getJSObject( $options ); ?>
		});
	});
</script>
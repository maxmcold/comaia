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

$app 		= $displayData->app;
$options 	= $displayData->content;

// @required ark-list.min.js
?>
<script>
	var list;

	jQuery( document ).ready( function( $ )
	{
		list	= jQuery.fn.list(
		{
			css		: {
						view			: '[data-view]',
						path			: '[data-stack-control]',
						edit			: '[data-edit]',
						form			: {
											form	: '#action-list',
											stack	: '#jform_actionstack',
											path	: '#jform_actionpath',
											item	: '#jform_actionitem',
											action	: '#jform_actionaction',
											extra	: '#jform_actionextra'
										  }
					  },
			html 	: {
						move			: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONMOVE_PENDING', true ); ?>',
						messages		: {
											removeconfirm	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONREMOVE_CONFIRM', true ); ?>',
											renamepartial	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONRENAME_PARTIAL', true ); ?>'
										  },					
						errors			: {
											invalidpath 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONINVALIDPATH_FAIL', true ); ?>',
											renameerror 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONRENAMEERROR_FAIL', true ); ?>',
											renameempty 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONRENAMEEMPTY_FAIL', true ); ?>',
											renamesame 		: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_ACTIONRENAMESAME_FAIL', true ); ?>'
										  }
					  },
			confirm : {
						remove 			: <?php echo (int)Helper::params( 'confirm-delete' ); ?>
					  }
		});
	});
</script>
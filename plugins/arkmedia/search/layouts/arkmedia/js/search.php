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

$name 	= $displayData->name;
$title 	= $displayData->title;
$jtext 	= $displayData->jtext;
$folder = Helper::html( 'icon.icomoon', 'folder' );
$file 	= Helper::html( 'icon.icomoon', 'file4' );
$count 	= str_replace( array( '{{folder}}', '{{file}}' ), array( $folder, $file ), JText::_( $jtext . 'HTML_COUNT_SPRINTF', true ) );

// Add CSS
Helper::html( 'ark.css', 'ark-' . $name . '.min', $displayData->plugin . 'css' . ARKMEDIA_DS, array(), false );

// Add JS
Helper::html( 'ark.js', 'ark-' . $name . '.min', $displayData->plugin . 'js' . ARKMEDIA_DS, false, false );

?>
<script>
	jQuery( document ).ready( function( $ )
	{
		jQuery.fn.arkstack<?php echo $name; ?>(
		{
			css			: {
							stack			: '#<?php echo Helper::css( 'content' ); ?> [data-stack="<?php echo $name; ?>"]',
							control			: '[data-action="search"]',
							toggle			: '[data-action-value="toggle"]',
							preloader		: '[data-action-value="preloader"]',
							field			: '[data-action-value="search"]',
							clearer			: '[data-action-value="clear"]',
							templates		: {
												root 		: '#tmpl-content-<?php echo $name; ?>-stack',
												back 		: '#tmpl-content-<?php echo $name; ?>-back',
												folder 		: '#tmpl-content-<?php echo $name; ?>-folder',
												file 		: '#tmpl-content-<?php echo $name; ?>-file'
											  }
						  },
			html 		: {
							ajax			: '<?php echo JRoute::_( 'index.php?option=' . ARKMEDIA_COMPONENT . '&task=list.' . $name, false ); ?>',
							count			: '<?php echo $count; ?>',
							errors			: {
												malform 	: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_SEARCHAJAX_FAIL', true ); ?>',
												fail 		: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_SEARCHAJAX_FAIL', true ); ?>',
												ajax 		: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_SEARCHAJAX_FAIL', true ); ?>'
											  }
						  },
			view		: {
							name 			: 'views',	// Collapser JForm Group Name (keep paired to "group" value in actions.xml)
							after			: 'list',	// Search's View Name (keep paired to "data-value" value in actions.xml)
							revert			: true		// Revert to Original View on Link or Stay in Search's View?
						  },
			stack		: '<?php echo $name; ?>',
			title		: '<?php echo $title; ?>',
			permission	: 'ark.action.search'
		});
	});
</script>
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
$options 	= $displayData->explorer;

$css_scrolls= '#' . Helper::css() . chr( 32 ) . '#' . Helper::css( 'explorer' ) . chr( 32 ) . '.' . Helper::css( 'ark.scrollbar.container' );
$css_scroll	= $css_scrolls . '[data-stack="%s"]';

// @required ark-explorer.min.js
// @required nanoscroller.min.js
// @required nanoscroller.min.css
?>
<script>
	var explorer;

	jQuery( document ).ready( function( $ )
	{
		explorer	= jQuery.fn.explorer(
		{
			css		: {
						explorer 		: '#<?php echo Helper::css( 'explorer' ); ?>',
						overlay			: '.overlay',
						folder			: '.folder',
						file			: '.file',
						toggle			: '.toggle',
						path			: '[data-stack-control]',
						templates		: {
											root : '#tmpl-explorer',
											tree : '#tmpl-explorer-tree'
										  }
					  },
			html 	: {
						msg_scope 		: '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_FILESCOPE', true ); ?>'
					  },
			scroll 	: {
						all 			: '<?php echo $css_scrolls; ?>',
						one 			: '<?php echo $css_scroll; ?>'
					  },
			root	: <?php echo (int)$options->get( 'root-folder', true ); ?>
		});

		// Initialise jQuery Scrollbar 
		// Delay Slightly to Give the Explorer a Chance to Load
		// Otherwise the Initial Scrollbar is Incorrect
		// An Actual .delay( 100 ) is Not Necessary as Simply Queuing is Enough to Delay the Load
		$( '<?php echo $css_scrolls; ?>' ).queue( function()
		{
			$(this).nanoScroller(
			{
				iOSNativeScrolling	: true,
				alwaysVisible		: false
			}).dequeue();
		});
	});
</script>
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

$class = Helper::css( 'ark.icon.preloader.cursor' );
?>
<style>
	html.<?php echo $class; ?>,
	html.<?php echo $class; ?> *
	{
		cursor: wait !important;
	}
</style>
<script>
	// @note 	This Logic Will Not Work for Synchronous Ajax Calls.
	// @see		Also Available is: ajaxError(), ajaxComplete() & ajaxSuccess();
	jQuery( document ).ready( function( $ )
	{
		$( document ).ajaxStart( function()
		{
			$( 'html' ).addClass( '<?php echo $class; ?>' );
		});

		$( document ).ajaxStop( function()
		{
			$( 'html' ).removeClass( '<?php echo $class; ?>' ); 
		});

		// Try to Remove Class in the Event of a Fatal Error
		window.onerror = function( msg, url, line )
		{
			if( $( 'html' ).hasClass( '<?php echo $class; ?>' ) )
			{
				$( 'html' ).removeClass( '<?php echo $class; ?>' ); 
			}//end if
		}
	});
</script>
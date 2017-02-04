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

// @required ark-title.min.js
?>
<script>
	var title;

	jQuery( document ).ready( function( $ )
	{
		title	= jQuery.fn.title(
		{
			css		: {
						title 			: '#<?php echo Helper::css( 'title' ); ?>',
						template		: '#tmpl-title'
					  }
		});
	});
</script>
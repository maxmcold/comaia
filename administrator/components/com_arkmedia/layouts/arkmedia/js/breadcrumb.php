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
$options 	= $displayData->breadcrumb;

// @required ark-breadcrumb.min.js
?>
<script>
	var breadcrumb;

	jQuery( document ).ready( function( $ )
	{
		breadcrumb	= jQuery.fn.breadcrumb(
		{
			css		: {
						breadcrumb 		: '#<?php echo Helper::css( 'breadcrumb' ); ?>',
						stack			: '[data-stack]',
						template		: '#tmpl-breadcrumb'
					  },
			beautify: 'folder-beautify',
			fullpath: <?php echo (int)$options->get( 'breadcrumb-fullpath' ); ?>
		});
	});
</script>
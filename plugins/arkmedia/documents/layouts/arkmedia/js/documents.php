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
$css 	= $displayData->plugin . 'css' . ARKMEDIA_DS;
$js 	= $displayData->plugin . 'js' . ARKMEDIA_DS;

// @required ark-edit.min.js
Helper::html( 'ark.js', 'ark-' . $name . '.min', $js, false, false );
Helper::html( 'ark.css', 'ark-' . $name . '.min', $css, array(), false );
?>
<script>
	jQuery( document ).ready( function( $ )
	{
		jQuery.fn.arkstack<?php echo $name; ?>(
		{
			stack		: '<?php echo $name; ?>'
		});
	});
</script>
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
$path 	= $displayData->plugin . 'js' . ARKMEDIA_DS;
$params = $displayData->params;

// If upload plugin is installed/active wait till it loads 1st
$dependencies = ( JPluginHelper::isEnabled( ARKMEDIA_PLUGIN_FEATURE, 'upload' ) ) ? array( ARKMEDIA_PLUGIN_FEATURE . ':' . 'upload' ) : array();

// @required ark-edit.min.js
Helper::html( 'ark.js', 'ark-' . $name . '.min', $path, false, false, ARKMEDIA_PLUGIN_STACK . ':' . $name, $dependencies );
?>
<script>
	jQuery( document ).ready( function( $ )
	{
		jQuery.fn.arkstack<?php echo $name; ?>(
		{
			stack		: '<?php echo $name; ?>',
			suffix		: '<?php echo $params->get( 'suffix', 'id' ); ?>'
		});
	});
</script>
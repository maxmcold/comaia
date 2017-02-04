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

$name 		= $displayData->name;
$js 		= $displayData->plugin . 'js' . ARKMEDIA_DS;
$parameters = $displayData->parameters;
$id 		= $displayData->id;
$url 		= $displayData->url;
$key 		= $displayData->key;

// Add Root Library JS (not the rest of the library)
Helper::html( 'ark.framework', array( 'overrides' => false, 'ark' => false, 'fonts' => false ) );

// Load Logo Font & XTD Class
Helper::html( 'ark.css', 'ark-logo.min' );
Helper::html( 'ark.js', 'ark-' . $name . '.min', $js, false, false );
?>
<script>
	jQuery( document ).ready( function( $ )
	{
		jQuery.fn.arkeditorxtd(
		{
			parameters	: '<?php echo $parameters; ?>',
			id			: '<?php echo $id; ?>',
			url			: '<?php echo $url; ?>',
			key			: '<?php echo $key; ?>'
		});
	});

// Define In Case The Editor Doesn't Define
if( typeof jGetEditorSelectedImage !== 'function' )
{
	var jGetEditorSelectedImage = function( editorname )
	{
		return null;
	}
}//end if
</script>
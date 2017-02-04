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

// @required ark-resizer.min.js
$app 		= $displayData->app;
$options 	= $displayData->content;
$xml 		= Helper::xml();
$types		= array();

// Take All Types From the Buttons
if( $xml && ( $xml instanceof SimpleXMLElement ) )
{
	$field	= $xml->xpath( '//field[@name="layout-default"]' );

	// Did we get the Config Option? Null if not
	if( is_array( $field ) )
	{
		foreach( current( $field )->children() as $option )
		{
			$types[] = (string)$option['value'];
		}//end foreach
	}//end if
}//end if

?>
<script>
	var resizer;

	jQuery( document ).ready( function( $ )
	{
		resizer	= jQuery.fn.resizer(
		{
			css			: {
							control			: '[data-action="resize"]',
							value			: '[data-action-value="resize"]'
						  },
			active		: '<?php echo Helper::params( 'layout-default' ); ?>',
			types		: <?php echo json_encode( $types ); ?>,
			permission	: 'ark.ui.resize'
		});
	});
</script>
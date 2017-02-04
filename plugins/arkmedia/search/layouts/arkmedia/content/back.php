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

$base 		= $displayData->base;
$name 		= $displayData->name;

$css_back	= array( 'back', Helper::css( 'ark.var.block' ) );
$text		= JText::_( ARKMEDIA_JTEXT . 'FOLDER_BACK_LBL' );
?>
<script id="tmpl-content-<?php echo $name;?>-back" type="text/x-jsrender">
	<tr>
		<td>
			<?php echo Helper::html( 'button.a', array( 'text' => $text, 'title' => chr( 32 ), 'icon' => 'arrow-left2', 'class' => implode( chr( 32 ), $css_back ) ) ); ?>
		</td>
	</tr>
</script>
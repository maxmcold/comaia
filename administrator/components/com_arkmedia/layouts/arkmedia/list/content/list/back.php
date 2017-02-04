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

$view 		= $displayData->view;
$options 	= $displayData->options;

$css_back	= array( 'back', Helper::css( 'ark.var.block' ) );
$data 		= array( 'link' => 'data-path{:back}', 'stack-control' => 'path' );
$text		= JText::_( ARKMEDIA_JTEXT . 'FOLDER_BACK_LBL' );
?>
<script id="tmpl-content-<?php echo $view;?>-back" type="text/x-jsrender">
	{^{back}}
		<tr>
			<td colspan="5">
				<?php echo Helper::html( 'button.a', array( 'text' => $text, 'title' => chr( 32 ), 'data' => $data, 'icon' => 'arrow-left2', 'class' => implode( chr( 32 ), $css_back ) ) ); ?>
			</td>
		</tr>
	{{/back}}
</script>
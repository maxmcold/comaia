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

$base = $displayData->base;
$name = $displayData->name;

$css_item	= array( 'file', Helper::css( 'ark.var.block' ) );
$css_path	= array( Helper::css( 'text.muted' ) );
$data 		= array( 'data-link' => 'title{:name} data-stack{:~root.stack.value} data-path{:fullpath} data-name{:name}', 'data-stack-control' => 'path', 'data-select-control' => 'files' );
?>
<script id="tmpl-content-<?php echo $name;?>-file" type="text/x-jsrender">
	<tr>
		<td>
			<a href="javascript:void(0);" class="<?php echo implode( chr( 32 ), $css_item ); ?>" <?php echo JArrayHelper::toString( $data ); ?>>
				<i data-link="class{:icon}"></i>
				<span class="<?php echo implode( chr( 32 ), $css_path ); ?>">{{:fullpath}}<?php echo ARKMEDIA_DS; ?></span>{{:~search( name, ~root.results.term )}}
			</a>
		</td>
	</tr>
</script>
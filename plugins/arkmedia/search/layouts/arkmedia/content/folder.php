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

$css_folder	= array( 'folder', Helper::css( 'ark.var.block' ) );
$css_path	= array( Helper::css( 'text.muted' ) );
$data 		= array( 'data-link' => 'title{:name} data-stack{:~root.stack.value} data-path{:basepath} data-name{:name}', 'data-stack-control' => 'path', 'data-select-control' => 'folders' );
?>
<script id="tmpl-content-<?php echo $name;?>-folder" type="text/x-jsrender">
	<tr>
		<td>
			<a href="javascript:void(0);" class="<?php echo implode( chr( 32 ), $css_folder ); ?>" <?php echo JArrayHelper::toString( $data ); ?>>
				<?php echo Helper::html( 'icon.icomoon', 'folder' ); ?>
				<span class="<?php echo implode( chr( 32 ), $css_path ); ?>">{{:basepath}}<?php echo ARKMEDIA_DS; ?></span>{{:~search( name, ~root.results.term )}}
			</a>
		</td>
	</tr>
</script>
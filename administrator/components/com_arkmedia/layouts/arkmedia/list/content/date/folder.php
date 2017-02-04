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

$view 			= $displayData->view;
$options 		= $displayData->options;
$date 			= JText::_( ARKMEDIA_JTEXT . 'FILE_DATE_ORDERJS_LBL' );

$css_container	= array( 'folder', Helper::css( 'box.panel.container' ), Helper::css( 'box.panel.small' ) );
$css_body		= array( Helper::css( 'box.panel.body' ) );
?>
<script id="tmpl-content-<?php echo $view;?>-folder" type="text/x-jsrender">
	<div class="<?php echo implode( chr( 32 ), $css_container ); ?>" data-toggle="collapse" data-target="" data-link="data-target{:'#' + ~target}">
		<div class="<?php echo implode( chr( 32 ), $css_body ); ?>">
			<?php echo Helper::html( 'icon.icomoon', 'caret', array( 'prefix' => '' ) ); ?>
			{{:~date( item.modified, '<?php echo $date; ?>' )}}
		</div>
	</div>
</script>
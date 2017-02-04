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
$file			= basename( __FILE__, '.php' );
$label 			= JText::_( ARKMEDIA_JTEXT . 'FILE_' . strtoupper( $view ) . '_' . strtoupper( $file ) . '_LBL' );

$css_container	= array( Helper::css( 'box.panel.container' ), Helper::css( 'box.panel.small' ) );
$css_body		= array( Helper::css( 'box.panel.body' ) );
?>
<script id="tmpl-content-<?php echo $view;?>-empty" type="text/x-jsrender">
	<div class="<?php echo implode( chr( 32 ), $css_container ); ?>">
		<div class="<?php echo implode( chr( 32 ), $css_body ); ?>">
			<?php echo Helper::html( 'icon.icomoon', 'caret', array( 'prefix' => '' ) ); ?>
			<?php echo $label; ?>
		</div>
	</div>
</script>
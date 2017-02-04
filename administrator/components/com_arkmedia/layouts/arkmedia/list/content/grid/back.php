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

$css_grid	= array( Helper::css( 'align.left' ) );
$css_item	= array( 'back', Helper::css( 'box.well.container' ), Helper::css( 'box.well.small' ), Helper::css( 'ark.var.overflow' ) );
$css_button	= array( Helper::css( 'text.align.center' ) );
$css_label	= array( Helper::css( 'text.muted' ), Helper::css( 'ark.text.upper' ) );
$text		= '<div class="' . implode( chr( 32 ), $css_label ) . '"><small>' . JText::_( ARKMEDIA_JTEXT . 'FOLDER_BACK_LBL' ) . '</small></div>';
?>
<script id="tmpl-content-<?php echo $view;?>-back" type="text/x-jsrender">
	{^{back}}
		<div class="<?php echo implode( chr( 32 ), $css_grid ); ?>">
			<div class="<?php echo implode( chr( 32 ), $css_item ); ?>" data-stack-control="path" data-link="data-path{:back}">

				{{!-- /* Height Space */ --}}
				<div><?php echo JText::_( ARKMEDIA_JTEXT . 'SPACE' ); ?></div>

				{{!-- /* Button */ --}}
				<?php echo Helper::html( 'button.a', array( 'text' => $text, 'html' => true, 'icon' => 'arrow-left3', 'class' => implode( chr( 32 ), $css_button ) ) );?>

				{{!-- /* Height Space */ --}}
				<div><?php echo JText::_( ARKMEDIA_JTEXT . 'SPACE' ); ?></div>

			</div>
		</div>
	{{/back}}
</script>
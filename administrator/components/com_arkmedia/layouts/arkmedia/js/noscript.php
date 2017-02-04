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

$css = array( Helper::css( 'box.alert.container' ), Helper::css( 'box.alert.danger' ), Helper::css( 'ark.var.block' ) );

?>
<noscript class="<?php echo implode( chr( 32 ), $css ); ?>">
	<?php echo Helper::html( 'icon.icomoon', 'warning' ); ?>
	<?php echo JText::_( ARKMEDIA_JTEXT . 'NOJS' ); ?>
</noscript>
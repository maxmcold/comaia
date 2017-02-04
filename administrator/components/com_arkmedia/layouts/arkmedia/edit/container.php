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

JHtml::_( 'behavior.keepalive' );

$view = $displayData;

// Root Container
echo '<div id="' . Helper::css( 'edit.container' ) . '" class="' . $view->css->edit->container . '">';

// HTML Will be Injected Here

// Close Root
echo '</div>';

?>
<script id="tmpl-edit" type="text/x-jsrender">
	<div class="<?php echo $view->css->edit->row; ?>">
		<div class="<?php echo $view->css->edit->actions; ?>">
			{{include tmpl=~getTemplate( 'actions' ) /}}
		</div>
	</div>
	<div class="<?php echo $view->css->edit->row; ?>">
		<div class="<?php echo $view->css->edit->preview; ?>">
			{{include tmpl=~getTemplate( 'preview' ) /}}
		</div>
		<div class="<?php echo $view->css->edit->settings; ?>">
			{{include tmpl=~getTemplate( 'settings' ) /}}
		</div>
	</div>
	<div class="<?php echo $view->css->edit->row; ?>">
		<div class="<?php echo $view->css->edit->subactions; ?>">
			{{include tmpl=~getTemplate( 'subactions' ) /}}
		</div>
	</div>
</script>

<?php echo Helper::layout( 'edit.actions', $view ); ?>

<?php echo Helper::layout( 'edit.preview', $view ); ?>

<?php echo Helper::layout( 'edit.settings', $view ); ?>

<?php echo Helper::layout( 'edit.subactions', $view ); ?>
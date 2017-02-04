<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

use Ark\Media\Helper, Ark\Media\HelperEdit;

defined( '_JEXEC' ) or die;

$view	= $displayData;
$id 	= basename( __FILE__, '.php' );
$form 	= $view->model->getForm( 'editsubactions', false, 'fieldset' );
$params	= new JRegistry;
$css	= new JRegistry;

// Remove Unnecessary Sub Actions
if( !HelperEdit::isEditFull() )
{
	$form->removeField( 'insert' );
	$form->removeField( 'close' );
}//end if

// Set Layout/Display Parameters
$css->set( 'group', array( Helper::css( 'button.group' ) ) );
$params->set( 'id', Helper::css( 'edit.' . $id ) );
$params->set( 'role', 'toolbar' );
$params->set( 'css', $css );
?>
<script id="tmpl-edit-<?php echo $id; ?>" type="text/x-jsrender">
	<?php echo Helper::layout( 'jform', (object)array( 'form' => $form, 'params' => $params ) ); ?>
</script>
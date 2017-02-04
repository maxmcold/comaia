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

$view	= $displayData;
$id 	= basename( __FILE__, '.php' );
$form 	= $view->model->getForm( 'editactions', false, 'fieldset' );
$params	= new JRegistry;
$css	= new JRegistry;
$tags	= new JRegistry;

// Set Layout/Display Parameters
$css->set( 'container', array( Helper::css( 'button.toolbar' ) ) );
$css->set( 'group', array( Helper::css( 'button.group' ) ) );
$params->set( 'id', Helper::css( 'edit.' . $id ) );
$params->set( 'role', 'toolbar' );
$params->set( 'css', $css );

// If No Fields Then Hide Container (for margin/padding purposes)
if( !count( $form->getXml()->xpath( '//field' ) ) )
{
	$css->set( 'container', array_merge( $css->get( 'container' ), array( Helper::css( 'ark.var.hide' ) ) ) );
}//end if
?>
<script id="tmpl-edit-<?php echo $id; ?>" type="text/x-jsrender">
	<?php echo Helper::layout( 'jform', (object)array( 'form' => $form, 'params' => $params ) ); ?>
</script>
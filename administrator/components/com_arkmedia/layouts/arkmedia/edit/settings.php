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

$id 		= basename( __FILE__, '.php' );

if( HelperEdit::isEditFull() && HelperEdit::isEditSettings() )
{
	$view	= $displayData;
	$form 	= $view->model->getForm( 'edit', false, true );
	$params	= new JRegistry;
	$css	= new JRegistry;
	$tags	= new JRegistry;

	// Set Field Defaults (these are config defined defaults)
	$default_class = Helper::params( 'class-default' );
	$default_style = Helper::params( 'style-default' );

	if( $default_class )
	{
		$form->setValue( 'class', false, $default_class );
	}//end if

	if( $default_style )
	{
		$form->setValue( 'style', false, $default_style );
	}//end if

	$css->set( 'container', array( Helper::css( 'form.container.horizontal' ) ) );
	$css->set( 'group', array( Helper::css( 'form.fieldset' ) ) );
	$css->set( 'row', array( Helper::css( 'form.group' ) ) );
	$tags->set( 'container', 'form' );
	$tags->set( 'heading', 'h4' );
	$params->set( 'id', Helper::css( 'edit.' . $id ) );
	$params->set( 'role', 'form' );
	$params->set( 'tags', $tags );
	$params->set( 'css', $css );
}//end if
?>
<script id="tmpl-edit-<?php echo $id; ?>" type="text/x-jsrender">
	<?php if( HelperEdit::isEditFull() && HelperEdit::isEditSettings() ) : ?>
		<?php echo Helper::layout( 'jform', (object)array( 'form' => $form, 'params' => $params ) ); ?>
	<?php endif; ?>
</script>
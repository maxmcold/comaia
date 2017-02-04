<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

namespace Ark\Media;

// Add Joomla Classes
use JRegistry;

defined( '_JEXEC' ) or die;

/**
 * CSS Logic Resides Here & in HTML/ark.php
 */
class HelperCSS
{
	/**
	 * @var    array  Ark/Joomla/Bootstrap/UIkit Classes/IDs Stored Here
	 */	
	protected static $_css 	= array(
		// Ark Styles
		'root' 			=> 'ark',
		'toolbar' 		=> 'ark-toolbar',
		'search' 		=> 'ark-search',
		'navigation' 	=> 'ark-nav',
		'explorer' 		=> 'ark-explorer',
		'title'			=> 'ark-title',
		'breadcrumb'	=> 'ark-breadcrumb',
		'helpbar'		=> 'ark-helpbar',
		'actions'		=> 'ark-actions',
		'subactions'	=> 'ark-subactions',
		'content'		=> 'ark-content',
		'message'		=> 'ark-message',
		'alert'			=> 'ark-alert',
		'edit'			=> array(
									// edit.container
									'container' => 'ark-edit',
									'actions' 	=> 'ark-edit-actions',
									'preview' 	=> 'ark-edit-preview',
									'settings' 	=> 'ark-edit-settings',
									'subactions'=> 'ark-edit-subactions'
								),
		'containers'	=> array(
									// containers.content
									'content' 	=> array(
															// containers.content.container
															'container'	=> 'ark-container-content-container',
															'top'		=> 'ark-container-content-top',
															'edit'		=> 'ark-container-content-edit',
															'bottom'	=> 'ark-container-content-bottom'
														)
								),
		'tooltip' 		=> 'ark-tooltip',
		// Joomla Styles
		'joomla' 		=> array(
									'tooltip' 	=> 'hasTooltip',
									'modal' 	=> 'modal'
								),
		// Bootstrap Styles
		'clear' 		=> 'clearfix',
		'grid' 			=> array( 
									'container' => array(
															// grid.container.fixed
															'fixed'		=> 'container',
															'fluid'		=> 'container-fluid'
														),
									'row' 		=> 'row',
									'gutter'	=> array(
															// grid.gutter.clear.both
															'clear'		=> array(
																					'both'	=> 'no-gutter', // custom
																					'left'	=> 'no-gutter-left', // custom
																					'right'	=> 'no-gutter-right' // custom
																				)
														),
									'column' 	=> array(
															// grid.column.large.2 || grid.column.large.prefix || grid.column.large.offset.2 || grid.column.large.offset.prefix
															'custom'	=> 'col-custom', // custom (indicates a custom column width)
															'large'		=> array(
																					'prefix' => 'col-lg-',
																					'col-lg-0', 'col-lg-1', 'col-lg-2', 'col-lg-3', 'col-lg-4', 'col-lg-5', 'col-lg-6',
																					'col-lg-7', 'col-lg-8', 'col-lg-9', 'col-lg-10', 'col-lg-11', 'col-lg-12',
																					'offset' => array(
																										'prefix' => 'col-lg-offset',
																										'col-lg-offset-0', 'col-lg-offset-1', 'col-lg-offset-2', 'col-lg-offset-3', 'col-lg-offset-4', 
																										'col-lg-offset-5', 'col-lg-offset-6', 'col-lg-offset-7', 'col-lg-offset-8', 'col-lg-offset-9', 
																										'col-lg-offset-10', 'col-lg-offset-11', 'col-lg-offset-12'
																									)
																				),
															'medium'	=> array(
																					'prefix' => 'col-md-',
																					'col-md-0', 'col-md-1', 'col-md-2', 'col-md-3', 'col-md-4', 'col-md-5', 'col-md-6',
																					'col-md-7', 'col-md-8', 'col-md-9', 'col-md-10', 'col-md-11', 'col-md-12',
																					'offset' => array(
																										'prefix' => 'col-md-offset',
																										'col-md-offset-0', 'col-md-offset-1', 'col-md-offset-2', 'col-md-offset-3', 'col-md-offset-4', 
																										'col-md-offset-5', 'col-md-offset-6', 'col-md-offset-7', 'col-md-offset-8', 'col-md-offset-9', 
																										'col-md-offset-10', 'col-md-offset-11', 'col-md-offset-12'
																									)
																				),
															'small'		=> array(
																					'prefix' => 'col-sm-',
																					'col-sm-0', 'col-sm-1', 'col-sm-2', 'col-sm-3', 'col-sm-4', 'col-sm-5', 'col-sm-6',
																					'col-sm-7', 'col-sm-8', 'col-sm-9', 'col-sm-10', 'col-sm-11', 'col-sm-12',
																					'offset' => array(
																										'prefix' => 'col-sm-offset',
																										'col-sm-offset-0', 'col-sm-offset-1', 'col-sm-offset-2', 'col-sm-offset-3', 'col-sm-offset-4', 
																										'col-sm-offset-5', 'col-sm-offset-6', 'col-sm-offset-7', 'col-sm-offset-8', 'col-sm-offset-9', 
																										'col-sm-offset-10', 'col-sm-offset-11', 'col-sm-offset-12'
																									)
																				),
															'mini'		=> array(
																					'prefix' => 'col-xs-',
																					'col-xs-0', 'col-xs-1', 'col-xs-2', 'col-xs-3', 'col-xs-4', 'col-xs-5', 'col-xs-6',
																					'col-xs-7', 'col-xs-8', 'col-xs-9', 'col-xs-10', 'col-xs-11', 'col-xs-12',
																					'offset' => array(
																										'prefix' => 'col-xs-offset',
																										'col-xs-offset-0', 'col-xs-offset-1', 'col-xs-offset-2', 'col-xs-offset-3', 'col-xs-offset-4', 
																										'col-xs-offset-5', 'col-xs-offset-6', 'col-xs-offset-7', 'col-xs-offset-8', 'col-xs-offset-9', 
																										'col-xs-offset-10', 'col-xs-offset-11', 'col-xs-offset-12'
																									)
																				)
														)
								),
		'responsive' 	=> array( 
									'visible' 	=> array(
															// responsive.visible.large
															'large' 	=> 'visible-lg', // desktops
															'medium' 	=> 'visible-md', // desktops
															'small' 	=> 'visible-sm', // tablets
															'mini' 		=> 'visible-xs' // phones
														),
									'hidden' 	=> array(
															// responsive.hidden.large
															'large' 	=> 'hidden-lg', // desktops
															'medium' 	=> 'hidden-md', // desktops
															'small' 	=> 'hidden-sm', // tablets
															'mini' 		=> 'hidden-xs' // phones
														),
									'screen'	=> 'sr-only'
								),
		'box' 			=> array( 
									// box.badge
									'badge'		=> 'badge',
									'alert'		=> array(
															// box.alert.container
															'container'	=> 'alert',
															'dismiss'	=> 'alert-dismissible',
															'close'		=> 'close',
															'success' 	=> 'alert-success',
															'info' 		=> 'alert-info',
															'warning' 	=> 'alert-warning',
															'danger' 	=> 'alert-danger'
														),
									'label'		=> array(
															// box.label.container
															'container'	=> 'label',
															'default' 	=> 'label-default',
															'primary' 	=> 'label-primary',
															'success' 	=> 'label-success',
															'info' 		=> 'label-info',
															'warning' 	=> 'label-warning',
															'danger' 	=> 'label-danger'
														),
									'jumbo'		=> array(
															// box.jumbo.container
															'container'	=> 'jumbotron'
														),
									'breadcrumb'=> array(
															// box.breadcrumb.container
															'container'	=> 'breadcrumb',
															'active'	=> 'active',
															'small'		=> 'breadcrumb-sm' // custom
														),
									'panel' 	=> array(
															// box.panel.container
															'container'	=> 'panel panel-default',
															'header'	=> 'panel-heading',
															'body'		=> 'panel-body',
															'footer'	=> 'panel-footer',
															'small'		=> 'panel-sm', // custom
															'type' 		=> array(
																					// box.panel.type.success
																					'success'	=> 'panel-success', // custom
																					'danger'	=> 'panel-danger' // custom
																				)
														),
									'well' 		=> array(
															// box.well.container
															'container'	=> 'well',
															'large'		=> 'well-lg',
															'small'		=> 'well-sm'
														),
									'list' 		=> array(
															// box.list.container
															'container'	=> 'list-group',
															'item'		=> 'list-group-item',
															'header'	=> 'list-group-item-heading',
															'body'		=> 'list-group-item-text',
															'active'	=> 'active',
															'disabled'	=> 'disabled',
															'type' 		=> array(
																					// box.list.type.success
																					'success'	=> 'list-group-item-success',
																					'info'		=> 'list-group-item-info',
																					'warning'	=> 'list-group-item-warning',
																					'danger'	=> 'list-group-item-danger'
																				)
														),
									'table' 	=> array(
															// box.table.container
															'container'	=> 'table',
															'striped'	=> 'table-striped',
															'hover'		=> 'table-hover',
															'condensed'	=> 'table-condensed',
															'responsive'=> 'table-responsive'
														)
								),
		'align' 		=> array(
									// align.center
									'left' 		=> 'pull-left',
									'center' 	=> 'center-block',
									'right' 	=> 'pull-right'
								),
		'collapse' 		=> array(
									// collapse.container
									'container' => 'collapse',
									'active' 	=> 'in',
									'transition'=> 'collapsing'
								),
		'animate'		=> array(
									// animate.active
									'active' 	=> 'in',
									'fade' 		=> 'fade'
								),
		'progress'		=> array(
									// progress.container
									'container' => 'progress',
									'bar' 		=> 'progress-bar',
									'striped' 	=> 'progress-striped',
									'active' 	=> 'active',
									'type' 		=> array(
															// progress.type.success
															'success'	=> 'progress-bar-success',
															'info'		=> 'progress-bar-info',
															'warning'	=> 'progress-bar-warning',
															'danger'	=> 'progress-bar-danger'
														)
								),
		'text' 			=> array(
									// text.muted
									'muted' 	=> 'text-muted',
									'primary' 	=> 'text-primary',
									'success' 	=> 'text-success',
									'info' 		=> 'text-info',
									'warning' 	=> 'text-warning',
									'danger' 	=> 'text-danger',
									'align' 	=> array( 
													// text.align.center
													'left' 		=> 'text-left',
													'center' 	=> 'text-center',
													'right' 	=> 'text-right',
													'justify' 	=> 'text-justify'
												)
								),
		'form' 			=> array( 
									// form.control
									'container' => array(
													// form.container.inline
													'inline' 	=> 'form-inline',
													'horizontal'=> 'form-horizontal'
												),
									'fieldset' 	=> 'form-set', // custom
									'group' 	=> 'form-group',
									'control'	=> 'form-control',
									'label'		=> 'control-label',
									'size' 		=> array(
													// form.size.large
													'large'		=> 'input-lg',
													'small'		=> 'input-sm'
												),
									'input'		=> array(
													// form.input.group
													'group'		=> 'input-group',
													'addon'		=> 'input-group-addon',
													'button'	=> 'input-group-btn'
												)
								),
		'button'		=> array(
									// button.button
									'button' 	=> 'btn',
									'toolbar' 	=> 'btn-toolbar',
									'group' 	=> 'btn-group',
									'active' 	=> 'active',
									'file' 		=> 'btn-file', // custom
									'type' 		=> array(
															// button.type.primary
															'plain'		=> 'btn-plain',
															'primary'	=> 'btn-primary',
															'default'	=> 'btn-default',
															'success'	=> 'btn-success',
															'info'		=> 'btn-info',
															'warning'	=> 'btn-warning',
															'danger'	=> 'btn-danger',
															'link'		=> 'btn-link'
														),
									'align'		=> array(
															// button.align.vertical
															'vertical'	=> 'btn-group-vertical',
															'justified'	=> 'btn-group-justified'
														),
									'size' 		=> array(
															// button.size.large
															'large'		=> 'btn-lg',
															'small'		=> 'btn-sm',
															'mini'		=> 'btn-xs'
														),
									'radius' 	=> array(
															// button.radius.right
															'right'		=> 'btn-radius-right', // custom
															'left'		=> 'btn-radius-left' // custom
														),
									'dropdown' 	=> array(
															// button.dropdown.toggle
															'toggle'	=> 'dropdown-toggle',
															'menu'		=> 'dropdown-menu'
														)
								),
		// UIKit Styles
		'uk'			=> array(
									'grid'		=> array(
															// uk.grid.flex
															'flex' 		=> array(
																					// uk.grid.flex.container
																					'container'	=> 'uk-flex',
																					'inline'	=> 'uk-flex-inline',
																					'item'		=> array(
																											// uk.grid.flex.item.none
																											'none'	=> 'uk-flex-item-none',
																											'auto'	=> 'uk-flex-item-auto'
																										)
																				)
														),
									'margin'	=> array(
															// uk.margin.no
															'no' 		=> array(
																					// uk.margin.no.all
																					'all'	=> 'uk-margin-remove',
																					'top'	=> 'uk-margin-top-remove',
																					'bottom'=> 'uk-margin-bottom-remove'
																				),
															'large' 	=> array(
																					// uk.margin.large.x
																					'x'		=> 'uk-margin-large-left uk-margin-large-right',
																					'y'		=> 'uk-margin-large',
																					'top'	=> 'uk-margin-large-top',
																					'right'	=> 'uk-margin-large-right',
																					'bottom'=> 'uk-margin-large-bottom',
																					'left'	=> 'uk-margin-large-left'
																				),
															'medium' 	=> array(
																					// uk.margin.medium.x
																					'x'		=> 'uk-margin-left uk-margin-right',
																					'y'		=> 'uk-margin',
																					'top'	=> 'uk-margin-top',
																					'right'	=> 'uk-margin-right',
																					'bottom'=> 'uk-margin-bottom',
																					'left'	=> 'uk-margin-left'
																				),
															'small' 	=> array(
																					// uk.margin.small.x
																					'x'		=> 'uk-margin-small-left uk-margin-small-right',
																					'y'		=> 'uk-margin-small',
																					'top'	=> 'uk-margin-small-top',
																					'right'	=> 'uk-margin-small-right',
																					'bottom'=> 'uk-margin-small-bottom',
																					'left'	=> 'uk-margin-small-left'
																				)
														),
									'sort'		=> array(
															// uk.sort.container
															'container'	=> 'uk-sortable',
															'item'		=> 'uk-sortable-item',
															'holder'	=> 'uk-sortable-placeholder'
														)
								),
		// Custom Styles
		'ark'			=> array(
									'var'		=> array(
															// ark.var.overflow
															'hide'		=> 'var-hide',
															'block'		=> 'var-block',
															'overflow'	=> 'var-overflow',
															'relative'	=> 'var-rel',
															'transition'=> 'var-transition'
														),
									'text'		=> array(
															// ark.text.overflow
															'overflow'	=> 'text-overflow',
															'upper'		=> 'text-upper',
															'middle'	=> 'text-middle'
														),
									'icon'		=> array(
															'preloader'	=> array(
																					// ark.icon.preloader.cursor
																					'cursor'	=> 'loading',
																					'animate'	=> 'preload-animate'
																				),
															'caret'		=> array(
																					// ark.icon.caret.container
																					'container'	=> 'caret',
																					'left'		=> 'caret-left',
																					'right'		=> 'caret-right'
																				)
														),
									'scrollbar'	=> array(
															// ark.scrollbar.container
															'container'	=> 'nano',
															'content'	=> 'nano-content'
														)
								)
	);

	/**
	 * Load in a CSS Library Asset ( Can be called by: Helper::css() || HelperCSS::selector() )
	 *
	 * @param	string  $path		What Array Class/ID to Load as a Dot Separated Path (Default : 'root')
	 * @param	string  $default	A Default Value if the Path is not Found or Blank
	 *
	 * @usage		Helper::css();								// 'ark'
	 * 				Helper::css( 'clear' );						// 'clearfix'
	 * 				Helper::css( 'button.align.vertical' );		// 'btn-group-vertical'
	 * 				Helper::css( 'button' );					// array( 'button' => etc... );
	 *
	 * @return  string	Class/ID Name
	 */
	public static function selector( $path = 'root', $default = null )
	{
		$id 	= ARKMEDIA_COMPONENT_ID . '-css';
		$reg 	= JRegistry::getInstance( $id );

		// Load CSS in if Instance is Empty
		if( !$reg->exists( 'root' ) )
		{
			$reg->loadArray( static::$_css );
		}//end if

		return $reg->get( $path, $default );
	}//end function
}//end class
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

// @TODO: Split This File into Separate Sub Layouts for Rendering e,g, form.jform, form.fieldset, form.field
$form 	= $displayData->form;		// JForm
$params = $displayData->params;		// Layout Parameters

// Data Integrity
if( !$params->get( 'css' ) )
{
	$params->set( 'css', new JRegistry );
}//end if

if( !$params->get( 'tags' ) )
{
	$params->set( 'tags', new JRegistry );
}//end if

// Begin Form Rendering
if( $form && ( $form instanceof JForm ) )
{
	$container 	= array( 
							'id' 	=> $params->get( 'id' ),
							'class' => Helper::html( 'text.implode', $params->get( 'css' )->get( 'container' ) ),
							'role' 	=> $params->get( 'role' )
						);

	// Root Container
	echo '<' . $params->get( 'tags' )->get( 'container', 'div' ) . chr( 32 ) . JArrayHelper::toString( array_filter( array_merge( $container, (array)$params->get( 'attrs', array() ) ) ) ) . '>';

	// Render JForm Fields Grouped by Their Fieldsets
	foreach( $form->getFieldsets( $params->get( 'group', null ) ) as $fieldsets )
	{
		// Define Defaults for Strict Compliance
		$defaults 				= array( 	'id', 		'class', 		'name', 	'labels', 		'columns',
											'align', 	'visible', 		'hidden', 	'bootstrap', 	'toggle', 
											'method', 	'enctype', 		'files', 	'action', 		'role',
											'label',	'data-stack',	'icon',		'permission'
										);
		$fieldsets 				= (object)array_merge( array_fill_keys( $defaults, null ), (array)$fieldsets );

		$group_attrs			= array();
		$group_class			= array_merge( (array)$params->get( 'css' )->get( 'group' ), array( $fieldsets->{ 'class' } ) );
		$heading_class			= (array)$params->get( 'css' )->get( 'heading' );
		$row_attrs				= array();
		$row_class				= array_merge( array( Helper::css( 'grid.row' ) ), (array)$params->get( 'css' )->get( 'row' ) );

		// Check Permissions
		if( $fieldsets->{ 'permission' } && !Helper::actions( $fieldsets->{ 'permission' } ) )
		{
			continue;
		}//end if

		// Add Alignment Classes
		switch( $fieldsets->{ 'align' } )
		{
			case 'left' :
			case 'right' :
				$group_class[]	= Helper::css( 'align.' . $fieldsets->{ 'align' } );
				break;

			case 'vertical' :
				// Unset Root Group Class
				if( ( $group_key = array_search( Helper::css( 'button.group' ), $group_class ) ) !== false )
				{
					unset( $group_class[$group_key] );
				}//end if

				$group_class[]	= Helper::css( 'button.align.' . $fieldsets->{ 'align' } );
				break;

			case 'justified' :
				$group_class[]	= Helper::css( 'button.align.' . $fieldsets->{ 'align' } );
				break;
		}//end switch

		// Add Responsive Classes
		if( $fieldsets->{ 'visible' } || $fieldsets->{ 'hidden' } )
		{
			// Visible or Hidden?
			$responsive_switch	= ( $fieldsets->{ 'visible' } ) ? 'visible' : 'hidden';

			// Add Multiple Sizes With a Comma
			foreach( explode( ',', $fieldsets->{ $responsive_switch } ) as $device )
			{
				$group_class[]	= Helper::css( 'responsive.' . $responsive_switch . '.' . $device );
			}//end foreach
		}//end if

		$group_attrs['class'] 	= implode( chr( 32 ), array_filter( $group_class ) );

		// Add Bootstrap JS Switch
		if( $fieldsets->{ 'bootstrap' } == 'true' )
		{
			$group_attrs['data-toggle']	= ( $fieldsets->{ 'toggle' } ) ? $fieldsets->{ 'toggle' } : 'buttons';
		}//end if

		// Add Fieldset ID
		if( $fieldsets->{ 'id' } )
		{
			$group_attrs['id'] = $fieldsets->{ 'id' };
		}//end if

		// Add Fieldset Namespace if Provided
		if( $fieldsets->{ 'data-stack' } )
		{
			$group_attrs['data-stack'] = $fieldsets->{ 'data-stack' };
		}//end if

		// Calculate Label/Field Widths if Passed (format: columns="[12,11,10,9]:[12,11,10,9]" or: columns="[12,11]")
		if( $fieldsets->{ 'columns' } )
		{
			// Split Label & Field Columns if Both are Set
			$columns 		= ( strpos( $fieldsets->{ 'columns' }, ':' ) !== false ) ? explode( ':', $fieldsets->{ 'columns' } ) : $fieldsets->{ 'columns' };

			// Assign Columns to Labels & Fields (or to both if only one set passed)
			$columns_labels = ( is_array( $columns ) ) ? $columns[0] : $columns;
			$columns_fields = ( is_array( $columns ) ) ? $columns[1] : $columns;

			// Split Columns to Individual Responsive Settings (remove square brackets as these were a visual aid)
			$columns_labels = explode( ',', str_replace( array( '[', ']' ), '', $columns_labels ) );
			$columns_fields = explode( ',', str_replace( array( '[', ']' ), '', $columns_fields ) );

			// Get Column Classes
			$bootstrap_cols = Helper::css( 'grid.column' );
			$classes_labels = array();
			$classes_fields = array();

			// Now For Each "columns" Value Passed Loop Through the Various Sizes & MAtch it up to a Class
			// e.g. [12,11,10,9] => large-12, medium-11, small-10, mini-9
			// e.g. [12,12] => large-12, medium-12 
			foreach( array( 'large', 'medium', 'small', 'mini' ) as $key => $val )
			{
				if( isset( $bootstrap_cols->{ $val } ) )
				{
					$class_labels 		= ( count( $columns_labels ) ) ? array_shift( $columns_labels ) : null;
					$classes_labels[] 	= ( $class_labels && isset( $bootstrap_cols->{ $val }->{ $class_labels } ) ) ? $bootstrap_cols->{ $val }->{ $class_labels } : null;

					$class_fields 		= ( count( $columns_fields ) ) ? array_shift( $columns_fields ) : null;
					$classes_fields[] 	= ( $class_fields && isset( $bootstrap_cols->{ $val }->{ $class_fields } ) ) ? $bootstrap_cols->{ $val }->{ $class_fields } : null;
				}//end if
			}//end foreach
		}//end if

		// Is Fieldset Being Used as a Form? (if action is passed we'll convert Fieldset Group to a Form Group)
		if( $fieldsets->{ 'action' } )
		{
			// Force Form Data
			$params->get( 'tags' )->set( 'group', 'form' );
			$group_attrs['action']	= JRoute::_( $fieldsets->{ 'action' }, false );
			$group_attrs['method']	= ( $fieldsets->{ 'method' } ) ?: 'post';
			$group_attrs['enctype']	= ( $fieldsets->{ 'enctype' } ) ?: false; // Allow for Full Setting or Lazy files="true" Version
			$group_attrs['enctype']	= ( $fieldsets->{ 'files' } ) ? 'multipart/form-data' : $group_attrs['enctype'];
			$group_attrs['name']	= $fieldsets->name;
			$group_attrs['role']	= 'form';
		}//end if

		// Group Container
		echo '<' . $params->get( 'tags' )->get( 'group', 'div' ) . chr( 32 ) . JArrayHelper::toString( array_filter( $group_attrs ) ) . '>';

		// Add Fieldset Label
		if( $fieldsets->{ 'label' } )
		{
			echo '<' . $params->get( 'tags' )->get( 'heading', 'div' ) . ' class="' . Helper::html( 'text.implode', $heading_class ) . '">';

			// Add Icon if Passed
			if( $fieldsets->{ 'icon' } )
			{
				echo Helper::html( 'icon.icomoon', $fieldsets->{ 'icon' } ) . chr( 32 );
			}//end if

			// If All Caps JText Label
			echo ( JString::strtoupper( $fieldsets->{ 'label' } ) === $fieldsets->{ 'label' } ) ? JText::_( $fieldsets->{ 'label' } ) : $fieldsets->{ 'label' };

			echo '</' . $params->get( 'tags' )->get( 'heading', 'div' ) . '>';
		}//end if

		// Render Fields
		foreach( $form->getFieldset( $fieldsets->name ) as $fields )
		{
			if( $fields->input )
			{
				// Is this a Hidden Field?
				$hidden 		= ( strpos( $fields->__get( 'type' ), 'hidden' ) !== false );
				$tmp_classes 	= array();
				$tmp_attrs 		= array();

				// If the Field is Namespaced then Add the Attribute to the Row for JS to Access
				if( $fields->getAttribute( 'data-stack' ) )
				{
					$tmp_attrs['data-stack'] = $fields->getAttribute( 'data-stack' );
				}//end if

				// Hide Hidden Rows
				if( $hidden )
				{
					$tmp_classes[] = Helper::css( 'ark.var.hide' );
				}//end if

				// Field Labels are Opt Out
				if( !$fieldsets->{ 'labels' } || $fieldsets->{ 'labels' } == 'true' )
				{
					// Add Label Class if Fieldset is Column Based
					if( $fieldsets->{ 'columns' } )
					{
						$fields->__set( 'labelclass', Helper::css( 'form.label' ) );
						$row_classes 	= Helper::html( 'text.implode', array_merge( $tmp_classes, $row_class ) );
						$row_attributes = JArrayHelper::toString( array_merge( $tmp_attrs, $row_attrs ) );

						// Row Container
						echo '<' . $params->get( 'tags' )->get( 'row', 'div' ) . ' class="' . $row_classes . '"' . chr( 32 ) . $row_attributes . '>';

						// Label Column Container
						echo '<' . $params->get( 'tags' )->get( 'label', 'div' ) . ' class="' . Helper::html( 'text.implode', array_filter( $classes_labels ) ) . '">';
					}//end if

					// Render Label (if input field isn't 'hidden'/'extendedhidden')
					if( !$hidden )
					{
						echo $fields->label;
					}//end if

					// Close Label Column
					echo ( $fieldsets->{ 'columns' } ) ? '</' . $params->get( 'tags' )->get( 'label', 'div' ) . '>' : '';
				}//end if

				// Field Column
				echo ( $fieldsets->{ 'columns' } ) ? '<' . $params->get( 'tags' )->get( 'field', 'div' ) . ' class="' . Helper::html( 'text.implode', array_filter( $classes_fields ) ) . '">' : '';

				// Render Input
				echo $fields->input;

				// Close Columns
				if( $fieldsets->{ 'columns' } )
				{
					// Close Field Column
					echo '</' . $params->get( 'tags' )->get( 'field', 'div' ) . '>';

					// Close Row
					echo '</' . $params->get( 'tags' )->get( 'row', 'div' ) . '>';
				}//end if
			}//end if
		}//end foreach

		// Close Group
		echo '</' . $params->get( 'tags' )->get( 'group', 'div' ) . '>';
	}//end foreach

	// Close Root
	echo '</' . $params->get( 'tags' )->get( 'container', 'div' ) . '>';
}//end if
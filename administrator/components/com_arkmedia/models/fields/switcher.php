<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensins.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'radio' );

/**
 * This Field Renders a Radio List in Which Each Option Can Toggle Other Form Fields.
 *
 * @usage	<field name="f1" type="ark.switcher" switch="disable" repeatable="true" class="btn-group">
 *					<option id="fieldname1" value="val1">LBL</option>
 *					<option id="fieldname2" value="val2">LBL</option>
 *			</field>
 * @result	The "f1" Radio Disables/Enables 2 Fields in a Repeatable Field Window
 * @usage	<field name="f1" type="ark.switcher" class="btn-group" default="val1">
 *					<option id="fieldname1" value="val1">LBL</option>
 *					<option id="fieldname2,fieldname3" value="val2">LBL</option>
 *			</field>
 * @result	The "f1" Radio Shows/Hides 3 Fields, With fieldname2 & fieldname3 Being Hidden On Load
 */
class ArkFormFieldSwitcher extends JFormFieldRadio
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'switcher';

	/**
	 * @var		array	List of Fields to Switch Between
	 */
	protected $fields = array();

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$html = parent::getInput();

		$this->addJS();

		return $html;
	}//end function

	/**
	 * Method to get the field options for radio buttons.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getOptions()
	{
		$options = array();

		// Get the Field IDs From Each Option
		foreach( $this->element->children() as $option )
		{
			// Only add <option /> elements.
			if( $option->getName() === 'option' && isset( $option['id'] ) )
			{
				// Check for CSV Values (multiple fields can be switched trhough one option)
				$ids = explode( ',', (string)$option['id'] );

				foreach( $ids as $id )
				{
					$field = $this->form->getField( $id, $this->group );

					// Did We Find the Field?
					if( $field && $field->id )
					{
						// Store the ID & Option's Value for Later Pairing
						$this->fields[$field->id] = (string)$option['value'];
					}//end if
				}//end foreach
			}//end if
		}//end foreach

		return parent::getOptions();
	}//end function

	/**
	 * Method to Add Field JS.
	 *
	 * @return	void
	 */
	protected function addJS()
	{
		// Are We Inside an Incremental Repeatable Field?
		$repeat	= ( isset( $this->element['repeatable'] ) ) ? 1 : 0;
		// Disable or Hide Inactive Fields?
		$type 	= ( isset( $this->element['switch'] ) ) ? (string)$this->element['switch'] : 'hide';
		// Build ID (if repeatable use "regex" to find element as there are "-3" increment labels at the end)
		$id 	= ( isset( $this->element['repeatable'] ) ) ? '[id^="' . $this->id . '"]:not(input)' : '#' . $this->id;
		$js 	= "jQuery( document ).ready( function( $ )
				{
					var opts 	= " . json_encode( $this->fields ) . ";
					var type 	= '" . $type . "';
					var repeat 	= " . $repeat . ";

					// Repeatable Fields Need a Chance to Load First (not necessary for normal switchers)
					window.setTimeout( function()
					{
						// Get the Switcher (or collection of switchers if repeatable)
						var field 	= $( '" . $id . "' );
						var active	= field.find( 'input:checked' );

						/**
						 * Field Switcher Function
						 */
						function switcher( el )
						{
							var v = el.value; // Get Input Value
							var i = el.id.split( '-' ).pop(); // Get Potential Increment Val if Repeatable
							var g = ( repeat ) ? 'td' : '.control-group'; // Decide Group Element

							// Loop Options to Find Our Match
							$.each( opts, function( id, val )
							{
								// Add Increment Number to ID
								if( repeat )
								{
									id += '-' + i;
								}//end if

								// Disable or Hide?
								if( type === 'hide' )
								{
									// Get Row Instead
									var el = $( '#' + id ).closest( g );

									// Show or Hide?
									el.toggle( ( val === v ? true : false ) );
								}
								else if( type === 'disable' )
								{
									// Get Field
									var el = $( '#' + id );

									// Show or Hide?
									el.prop( 'disabled', function( j, value ){ return ( val === v ) ? false : true; });
								}//end if
							});
						}

						// Add Click to Field
						$( document ).on( 'click', '" . $id . " input', function( ev )
						{
							switcher( this );
						});

						// Default Switch On Load?
						active.each( function( i, el )
						{
							switcher( el );
						});

						// Extra Repeatable Logic
						if( repeat )
						{
							// Find Container & Data Field Which Contains the Required Data Attrs
							var modal 	= field.first().closest( '.modal' );
							var data	= $( '[data-modal-element=\"#' + modal.attr( 'id' ) + '\"]' );

							// Although the Above Delegate Event Handles the Initial Repeatable Open, Closing & Reopening Must be Re-Hanlded
							// Use Data Field to Find Modal Initiator Button
							$( document ).on( 'click', data.attr( 'data-bt-modal-open' ), function( ev )
							{
								modal.find( '" . $id . " input:checked' ).each( function( i, el )
								{
									switcher( el );
								});
							});

							// If Repeatable Monitor 'add' Buttons to Set Defaults of Newly Created Rows 
							// Subsequent Clicks Are Handled by Above Delegate Event
							modal.on( 'click', '.add', function( ev )
							{
								// Allow New Row to be Created
								window.setTimeout( $.proxy( function()
								{
									// If Master Btn; New Rows Appear at End, if Sub Btn; New Rows Appear After Sub's Row
									if( $( this ).closest( 'thead' ).length )
									{
										var newrow = modal.find( 'table tbody tr:last-child' );
									}
									else
									{
										var newrow = $( this ).closest( 'tr' ).next();
									}//end if

									switcher( newrow.find( '" . $id . " input:checked' ).get( 0 )  );
								}, this ), 50 );
							});
						}//end if
					}, ( repeat ? 50 : 0 ) );
				});";

		JFactory::getDocument()->addScriptDeclaration( $js );
	}//end function
}//end class
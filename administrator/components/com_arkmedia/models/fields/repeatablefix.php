<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( 'JPATH_PLATFORM' ) or die;

JFormHelper::loadFieldClass( 'repeatable' );

/**
 * This Field Provides Fixes for Joomla's Repeatable Field.
 * 
 * Fix 1: JRepeatable Requires Fieldsets to Work But in the Global Config These are Displayed Outside of the Repeatable Field so Need Hiding.
 * Fix 2: The Modal Close Button Doubles up the Fields (so we hide it till it's fixed). See: http://joomlacode.org/gf/project/joomla/tracker/?action=TrackerItemEdit&tracker_id=8103&tracker_item_id=32311
 * Fix 3: On Clicking a New Row if There is a Chosen List Field it Requires Re-Initialisation Due a Joomla Bug Which Fires: resetChosen() in it's media/system/js/repeatable-uncompressed.js Which Fails.
 * Fix 4: On Modal Popup Bootstrap/Joomla Radio Fields Aren't Initialised.
 */
class ArkFormFieldRepeatableFix extends JFormFieldRepeatable
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'repeatablefix';

	/**
	 * Document Object.
	 *
	 * @var		object
	 */
	protected $doc;

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 */
	public function setup( SimpleXMLElement $element, $value, $group = null )
	{
		// Initiate Parent First so we Have Our Element Data Configured
		if( !parent::setup( $element, $value, $group ) )
		{
			return false;
		}//end if

		$this->doc = JFactory::getDocument();

		// If We Have the Fieldset Name Provide Hacks/Fixes :s
		if( $this->fieldname )
		{
			$this->addCSS();
		}//end if

		$this->addJS();

		return true;
	}//end function

	/**
	 * Method to Add Field CSS.
	 *
	 * @return	void
	 */
	protected function addCSS()
	{
		// Setup CSS
		$css 	= array();
		// Fix 1
		$css[] 	= '.nav [href="#' . $this->fieldname . '_modal"] { display : none; }';
		// Fix 2
		$css[] 	= '#' . $this->id . '_modal_table + .form-actions .btn-link { display : none; }';

		$this->doc->addStyleDeclaration( implode( "\n", $css ) );
	}//end function

	/**
	 * Method to Add Field JS.
	 *
	 * @note	Logic "Borrowed" From Joomla, With Customisations.
	 *
	 * @see 	administrator/templates/isis/js/template.js
	 *
	 * @return	void
	 */
	protected function addJS()
	{
		// Fix 3
		$js = 'jQuery( document ).ready( function( $ )
				{
					var $table = $( "#' . $this->id . '_modal_table" );

					$table.on( "click", ".add", function( ev )
					{
						// Row Not Created Yet
						$( this ).delay( 0 ).queue( function()
						{
							var $list = $table.find( "select:not(.chzn-done)" ).last();

							if( $list.length )
							{
								// Chosen Incorrectly Referencing Original Element so Reset Elements to Current/Fake Elements
								$list.data().chosen.container = $( "<div>" );
								$list.data().chosen.form_field_jq = $list;

								// Destroy & Rebirth
								$list.data().chosen.destroy().chosen();
							}//end if

							$( this ).dequeue;
						});
					});
				});';

		$this->doc->addScriptDeclaration( $js );

		// Fix 4
		$js = "jQuery( document ).ready( function( $ )
				{
					// Delegate Original Radio Logic
					$( document ).on( 'click', '.btn-group label:not(.active)', function( ev )
					{
						var label = $( this );
						var input = $( '#' + label.attr( 'for' ) );

						if( !input.prop( 'checked' ) )
						{
							label.closest( '.btn-group' ).find( 'label' ).removeClass( 'active btn-success btn-danger btn-primary' );

							if( input.val() == '' )
							{

								label.addClass( 'active btn-primary' );
							}
							else if( input.val() == 0 )
							{
								label.addClass( 'active btn-danger' );
							}
							else
							{
								label.addClass( 'active btn-success' );
							}//end if

							input.prop( 'checked', true );
							input.trigger( 'change' );
						}
					});

					// Delegate Default Setting
					$( document ).on( 'click', '#" . $this->id . "_button', function( ev )
					{
						$( '.btn-group input' ).each( function()
						{
							var input = $( this );
							var label = $( 'label[for=' + input.attr( 'id' ) + ']' );

							label.removeClass( 'active btn-success btn-danger btn-primary' );

							if( $( this ).prop( 'checked' ) )
							{
								label.addClass( 'active' );

								if( input.val() == '' )
								{
									label.addClass( 'btn-primary' );
								}
								else if( input.val() == 0 )
								{
									label.addClass( 'btn-danger' );
								}
								else
								{
									label.addClass( 'btn-success' );
								}//end if
							}//end if
						});
					});
				});";

		$this->doc->addScriptDeclaration( $js );
	}//end function
}//end class
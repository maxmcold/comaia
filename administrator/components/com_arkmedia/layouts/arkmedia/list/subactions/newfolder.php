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

// @required ark-newfolder.min.js
$app							= JFactory::getApplication();
$element						= $displayData->element;
$parents						= $element->xpath( 'parent::*' );
$parent							= ( $parents && count( $parents ) ) ? current( $parents ) : array();
$formid							= (string)$parent['id'];
$tmplid							= 'tmpl-subactions-newfolder';
$id								= 'html-subactions-newfolder';

$stack_attrs					= array();
$path_attrs						= array();
$readonly_attrs					= array();

$stack_attrs['id']				= $displayData->formControl . '_folderstack';
$stack_attrs['name']			= $displayData->formControl . '[stack]';
$stack_attrs['data-link']		= 'stack.value';
$stack_attrs['required']		= ( $displayData->required ) 	? 'required' 			: false;

$path_attrs['id']				= $displayData->id;
$path_attrs['name']				= $displayData->name;
$path_attrs['data-link']		= 'path.path';
$path_attrs['required']			= ( $displayData->required ) 	? 'required' 			: false;

$readonly_attrs['data-link']	= 'path.path';
$readonly_attrs['id']			= $displayData->formControl . '_read' . $displayData->fieldname;
$readonly_attrs['name']			= $displayData->formControl . '[read' . $displayData->fieldname . ']';
$readonly_attrs['class']		= ( $displayData->class ) 		? $displayData->class	: false;
$readonly_attrs['readonly']		= ( $displayData->readonly ) 	? 'readonly' 			: false;
$readonly_attrs['disabled']		= ( $displayData->disabled ) 	? 'disabled' 			: false;
$readonly_attrs['size']			= ( $displayData->size ) 		? $displayData->size 	: false;

// HTML Container
echo '<span id="' . $id . '">';

// HTML Will be Injected Here

// Close HTML
echo '</span>';
?>
<script>
	var newfolder;

	jQuery( document ).ready( function( $ )
	{
		newfolder	= jQuery.fn.newfolder(
		{
			css		: {
						container		: '#<?php echo $id; ?>',
						form			: '#<?php echo $formid; ?>',
						toggle			: '#<?php echo $displayData->formControl; ?>_foldertoggle',
						control			: '#<?php echo $displayData->formControl; ?>_new_folder',
						field			: '#<?php echo $displayData->formControl; ?>_folderfolder',
						template		: '#<?php echo $tmplid; ?>'
					  },
			html 	: {
						errors			: {
											invalid : '<?php echo JText::_( ARKMEDIA_JTEXT . 'MSG_FOLDERINVALID_FAIL', true ); ?>'
										  }
					  }
		});
	});
</script>
<script id="<?php echo $tmplid; ?>" type="text/x-jsrender">
{^{folder}}
	<input type="hidden" <?php echo JArrayHelper::toString( array_filter( $stack_attrs ) ); ?> />
	<input type="hidden" <?php echo JArrayHelper::toString( array_filter( $path_attrs ) ); ?> />
	<input type="text" <?php echo JArrayHelper::toString( array_filter( $readonly_attrs ) ); ?> />
{{/folder}}
</script>
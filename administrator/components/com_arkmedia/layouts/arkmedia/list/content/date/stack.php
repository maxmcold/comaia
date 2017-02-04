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

$view			= $displayData->view;
$options 		= $displayData->options;

// Render Part Empty Folder Template
echo Helper::layout( 'list.content.' . $view . '.empty', (object)array( 'view' => $view, 'options' => $options ) );

$date 			= JText::_( ARKMEDIA_JTEXT . 'FILE_DATE_ORDERJS_LBL' );
$collapseid 	= '-' . $view . '-sub-';

$css_grid		= array( Helper::css( 'grid.column.medium.12' ) );
$css_collapse	= array( Helper::css( 'collapse.container' ), Helper::css( 'collapse.active' ), Helper::css( 'clear' ) );
?>
<script id="tmpl-content-<?php echo $view;?>-stack" type="text/x-jsrender">
	<div class="<?php echo implode( chr( 32 ), $css_grid ); ?>">
		{{!-- /* Get Current Folder */ --}}
		{^{props ~root.stack}}
			{{!-- /* Find Path */ --}}
			{^{if key == ~root.active.path}}
				{{!-- /* Protect Against Blank Folders */ --}}
				{^{if prop.count}}
					{{!-- /* Async Loading Causes "chronologise" to Not Render So Wait Till Root Folder is Loaded First */ --}}
					{^{if prop.loaded}}
						{{!-- /* Order & Render Files by Date */ --}}
						{^{chronologise prop.items index="modified" direction="desc" ~prop=prop}}

							{{!-- /* Render Out Folder/Category if Unique Date */ --}}
							{{if !~previous(i, items) || ~date(item.modified, '<?php echo $date; ?>') != ~date(~previous(i, items).item.modified, '<?php echo $date; ?>')}}
								{{!-- /* Close Previous Collapse Container (unless first array item) */ --}}
								{{if i}}
									</div>
								{{/if}}

								{{!-- /* Wrap in if statement as "item.modified" is lost in data-link otherwise */ --}}
								{^{if item.modified}}

									{{!-- /* Render Out Folder */ --}}
									{{for ~target=~root.name + '<?php echo $collapseid; ?>' + item.modified tmpl=~getContentTemplate( 'folder' ) /}}

									{{!-- /* Open Collapse Container */ --}}
									<div data-link="id{:~root.name + '<?php echo $collapseid; ?>' + item.modified}" class="<?php echo implode( chr( 32 ), $css_collapse ); ?>">
								{{/if}}
							{{/if}}

							{{!-- /* Render Out File */ --}}
							{{include tmpl=~getContentTemplate( 'file' ) /}}

							{{!-- /* Close Last Collapse Container */ --}}
							{{if i + 1 == ~prop.count}}
								</div>
							{{/if}}
						{{/chronologise}}
					{{/if}}
				{{!-- /* Handle Folders With Child Folders But No Files (appear blank) */ --}}
				{{else !prop.count && prop.folders}}
					{{include prop tmpl=~getContentTemplate( 'empty' ) /}}
				{{/if}}
			{{/if}}
		{{/props}}
	</div>
</script>
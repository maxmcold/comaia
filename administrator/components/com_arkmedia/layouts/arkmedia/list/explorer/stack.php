<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

/*
	Variable Usage:
		{^{:path}} 								- In Root
		{^{:~root.path}}						- Inside {{if}}, {{for}} etc.

	But We Modify Nested {^{:path}} Which Doesn't Update {^{:~root.path}} So:
		{^{:path}} 								- In Root
		{^{:~path}}								- Inside {{if}}, {{for}} etc. If Passed Twice: {{props stack ~path=path}} {{if ~path=~path}}

	Or Use #parent & Add a ".parent" for Each Layer Down:
		{{:#parent.data.path}}					- In Root
		{{:#parent.parent.data.path}} 			- Inside {{tag1}}
		{{:#parent.parent.parent.data.path}} 	- Inside {{tag1}} {{tag2}} etc.

	@see 	: http://borismoore.github.io/jsrender/demos/step-by-step/11_accessing-parent-data.html
*/
?>
<script id="tmpl-explorer-tree" type="text/x-jsrender">
	{{!-- /* Loop Whole Stack Looking for Current Folder */ --}}

	{{props ~root.stack ~path=path ~isinit=isinit}}

		{{!-- /* Render if Current Folder */ --}}
		{{if key == ~path ~path=~path ~isinit=~isinit}}

			{{!-- /* Render Current Folder (Unless Show Root is Off in Which Case Don't Render the Root Folder) */ --}}
			{{if ~canRenderFolder() tmpl="#tmpl-explorer-folder" /}}

			{{!-- /* If Open Render Children (or the root folder) */ --}}
			{^{if (prop.expanded && (prop.count > 0 || prop.folders > 0))}}
				<li>
					<ul data-link="class{:~isAutoExpanded() ? 'root' : ''}">

						{{!-- /* Loop Whole Stack Looking for Sub Folders */ --}}
						{^{props ~root.stack}}

							{{!-- /* Render Child Folders Recursively */ --}}
							{{if ~isChildFolder( key, prop.name, ~path )}}
								{^{tree init=false key=key /}}
							{{/if}}

						{{/props}}

						{{!-- /* Render the Current Folder's Files */ --}}
						{^{props prop.items tmpl="#tmpl-explorer-file" /}}
					</ul>
				</li>
			{{/if}}
		{{/if}}
	{{/props}}
</script>
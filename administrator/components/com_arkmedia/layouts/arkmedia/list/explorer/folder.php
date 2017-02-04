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

// Set Icon Display Options
$params_toggle	= array(  
							'class' 	=> 'toggle' . chr( 32 ) .  Helper::css( 'text.muted' ), 
							'icon' 		=> 'caret',
							'prefixes'	=> array( 'icon' => '' ),
							'data' 		=> array( 'link' => "class{merge:!prop.expanded toggle='collapsed'} class{merge:!prop.expanded toggle='" . Helper::css( 'text.muted' ) . "'} data-path{:key}" ) 
						);
$params_folder	= array( 'data-link' => "class{:prop.expanded ? 'icon-folder-open' : 'icon-folder'}" );

// Build Icons
$icon_toggle 	= Helper::html( 'button.a', $params_toggle );
$icon_disabled 	= Helper::html( 'icon.glyphicon', 'remove disabled' . chr( 32 ) . Helper::css( 'text.muted' ) );
$icon_folder 	= Helper::html( 'icon.icomoon', 'icon-folder', array( 'attrs' => $params_folder ) );
?>
<script id="tmpl-explorer-folder" type="text/x-jsrender">
	<li class="folder">
		{^{if !prop.loaded || prop.folders + prop.count > 0}}
			<?php echo $icon_toggle; ?>
		{{else}}
			<?php echo $icon_disabled; ?>
		{{/if}}
		<a href="javascript:void(0);" data-stack-control="path" data-link="data-select{:prop.name} data-path{:key}">
			<?php echo Helper::html( 'icon.icomoon', 'checkmark', array( 'attrs' => array( 'data-action' => 'check' ) ) ); ?>
			<?php echo $icon_folder; ?>
			<?php echo chr( 32 ); ?>
			{^{:prop.name}}
			<?php if( Helper::params( 'folder-count' ) ) : ?>
				<span class="<?php echo Helper::css( 'text.muted' ); ?>">
					{^{if prop.loaded}}
						{^{if prop.folders && prop.count}}
							({^{:prop.folders + prop.count}})
						{{else prop.folders || prop.count}}
							({^{:prop.folders || prop.count}})
						{{/if}}
					{{/if}}
				</span>
			<?php endif; ?>
		</a>
	</li>
</script>
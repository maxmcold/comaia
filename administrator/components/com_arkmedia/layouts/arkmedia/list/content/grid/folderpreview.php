<?php
/**
 * @version     1.12.1
 * @package     com_arkmedia
 * @copyright   Copyright (C) 2016. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      WebxSolution Ltd - http://www.arkextensions.com
 */

defined( '_JEXEC' ) or die;

$view		= $displayData->view;
$options 	= $displayData->options;

// Generate Thumbnail Information
$prefix 	= 'fx-blur-';
$width		= '100';
$height		= '100';

// @todo Someday Animate These Bad Boys for an Awesome Transition Effect :)
// Build SVG Filter for Blur FX (see http://css-plus.com/2012/03/gaussian-blur/)
?>
<script id="tmpl-content-<?php echo $view;?>-folderpreview" type="text/x-jsrender">
	{^{if prop.count}}

		{{!-- /* Use Custom Tag to Generate Thumbnail */ --}}
		{^{svg prop.items folder=prop.name ~key=key}}

			{{!-- /* Render SVG Preview */ --}}
			<svg class="<?php echo $prefix; ?>container">

				{{!-- /* "image" & "feGaussianBlur" Need to Have Closing Tag Otherwise Template JS Breaks */ --}}
				{{!-- /* Can't Data Link "xlink:href" Tag, Probably Due to Colon, So Manually Link */ --}}
				{{!-- /* Also OnLoad & OnError Events Don't Fire so this Thumbnail Logic isn't Touch by the JS Class */ --}}
				<image 
					id="" 
					data-link="id{:'<?php echo $prefix; ?>' + id}"
					width="<?php echo $width; ?>" 
					height="<?php echo $height; ?>" 
					xlink:href="{{:preview}}&size=<?php echo $width . 'x' . $height; ?>">
				</image>

				<filter id="" data-link="id{:'<?php echo $prefix . 'filter-on-' . $prefix; ?>' + id}">
					<feGaussianBlur stdDeviation="6"></feGaussianBlur>
				</filter>

				<filter id="" data-link="id{:'<?php echo $prefix . 'filter-off-' . $prefix; ?>' + id}">
					<feGaussianBlur stdDeviation="0"></feGaussianBlur>
				</filter>
			</svg>

			{{!-- /* Add SVG Switch CSS */ --}}
			<style>
				#<?php echo $prefix; ?>{{:id}}
				{
					filter:url( #<?php echo $prefix; ?>filter-on-<?php echo $prefix; ?>{{:id}} );
				}

				.image:hover #<?php echo $prefix; ?>{{:id}}
				{
					filter:url( #<?php echo $prefix; ?>filter-off-<?php echo $prefix; ?>{{:id}} );
				}
			</style>
		{{/svg}}
	{{/if}}
</script>
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

$pretext	= Helper::html( 'icon.icomoon', 'checkmark', array( 'attrs' => array( 'data-action' => 'check' ) ) );
$params 	= array( 'pretext' => $pretext, 'text' => '{^{:prop.name}}', 'html' => true, 'title' => chr( 32 ), 'data' => array( 'link' => 'title{:prop.name}' ), 'icon' => 'file4', 'class' => '' );
?>
<script id="tmpl-explorer-file" type="text/x-jsrender">
	<li class="file" data-link="data-select{:prop.name} data-edit{:prop.name} data-path{:~path}">
		<?php echo Helper::html( 'button.a', $params ); ?>
	</li>
</script>
<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
**/
namespace GCore\Admin\Extensions\Chronoforms\Fields\File;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
class File {
	static $title = 'File Field';
	static $cat_id = 'basic';
	static $cat_title = 'Basic';
	static $settings = array(
		'tag' => 'input',
		'type' => 'file',
		'name' => 'file',
		'id' => 'file',
		'label' => 'File Label',
		'sublabel' => '',
		'class' => '',
		'title' => '',
		'style' => ''
	);

	static $configs = array(
		'name' => array('value' => 'file_XNX_', 'label' => 'Field Name', 'type' => 'text', 'class' => 'element_field_name L', 'sublabel' => 'No spaces or special characters.'),
		'id' => array('value' => 'file_XNX_', 'label' => 'Field ID', 'type' => 'text', 'class' => 'L'),
		'label.text' => array('value' => 'File Label', 'label' => 'Label', 'type' => 'text', 'class' => 'L'),
		'label.position' => array('values' => 'left', 'label' => 'Label position', 'type' => 'dropdown', 'options' => array('left' => 'Left', 'top' => 'Top')),
		'sublabel' => array('value' => '', 'label' => 'Sub Label', 'type' => 'text', 'class' => 'L'),
		'class' => array('value' => '', 'label' => 'Class', 'type' => 'text', 'class' => 'L'),
		'title' => array('value' => '', 'label' => 'Title', 'type' => 'text', 'class' => 'L'),
		'style' => array('value' => '', 'label' => 'Style', 'type' => 'text', 'class' => 'L'),
		':multiple' => array('type' => 'dropdown', 'options' => array(0 => 'No', 1 => 'Yes'), 'label' => 'Multiple', 'sublabel' => 'Support selecting multiple files ? your field name should end with "[]"'),
		'params' => array('value' => '', 'label' => 'Extra params', 'type' => 'textarea', 'alt' => 'multiline', 'rows' => 5, 'cols' => 60, 'sublabel' => 'In Multiline format:param_name=param_value'),
		':data-load-state' => array('label' => 'Load state', 'type' => 'dropdown', 'options' => array('' => 'Visible & Enabled', 'hidden' => 'Hidden', 'hidden_parent' => 'Parent hidden', 'disabled' => 'Disabled')),
		':data-tooltip' => array('value' => '', 'label' => 'Tooltip', 'type' => 'textarea', 'rows' => 2, 'cols' => 60),
		'ghost' => array('type' => 'dropdown', 'options' => array(0 => 'No', 1 => 'Yes'), 'label' => 'Enable ghost'),
		'ghost_value' => array('value' => '', 'label' => 'Ghost value', 'type' => 'text'),
	);

	public static function element($data = array()){
		echo \GCore\Helpers\Html::formSecStart('original_element', 'file_origin');
		echo \GCore\Helpers\Html::formLine(self::$settings['name'], array_merge(self::$settings, $data));
		echo \GCore\Helpers\Html::formSecEnd();
	}

	public static function config($data = array(), $k = '_XNX_'){
		echo \GCore\Helpers\Html::formStart('original_element_config single_element_config', 'file_origin_config');
		?>
		<ul class="nav nav-tabs">
			<li><a href="#general-<?php echo $k; ?>" data-g-toggle="tab"><?php echo l_('CF_GENERAL'); ?></a></li>
			<li><a href="#validation-<?php echo $k; ?>" data-g-toggle="tab"><?php echo l_('CF_VALIDATION'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="general-<?php echo $k; ?>" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			foreach(self::$configs as $name => $params){
				$value = \GCore\Libs\Arr::getVal($data, explode('.', $name));
				$field_name = implode('][', explode('.', $name));
				$params['value'] = $value ? (($params['type'] == 'text') ? htmlspecialchars($value, ENT_QUOTES) : $value) : (isset($params['value']) ? $params['value'] : '');
				$params['values'] = $value ? $value : (isset($params['values']) ? $params['values'] : '');
				echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.']['.$field_name.']', $params);
			}
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][type]', array('type' => 'hidden', 'value' => self::$settings['type']));
			echo \GCore\Helpers\Html::input('Form[extras][fields]['.$k.'][container_id]', array('type' => 'hidden', 'id' => 'container_id'.$k, 'value' => '0'));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="validation-<?php echo $k; ?>" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][validation][required]', array('type' => 'dropdown', 'label' => l_('CF_REQUIRED'), 'options' => array('' => l_('NO'), 1 => l_('YES'))));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][validation][image]', array('type' => 'dropdown', 'label' => l_('CF_IMAGE'), 'options' => array('' => l_('NO'), 1 => l_('YES'))));
			echo \GCore\Helpers\Html::formLine('Form[extras][fields]['.$k.'][validation][custom]', array('type' => 'text', 'label' => l_('CF_VALIDATE_CUSTOM'), 'sublabel' => l_('CF_VALIDATE_CUSTOM_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
		</div>
		<?php
		echo \GCore\Helpers\Html::formEnd();
	}
}
?>
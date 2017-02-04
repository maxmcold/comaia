<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die();

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldGraphicLibrary extends \JFormFieldList
{
    protected $type = 'GraphicLibrary';
    
    protected function getOptions()
    {
        $options = array();

        if ($this->isGmagick()) {
            $options[] = JHtml::_('select.option', 'gmagick', 'GraphicsMagick');
        }

        if ($this->isImageMagic()) {
            $options[] = JHtml::_('select.option', 'imagick', 'ImageMagick');
        }

        if ($this->isGD2()) {
            $options[] = JHtml::_('select.option', 'gd2', 'GD2');
        }

        reset($options);
        return $options;
    }

    private function isGD2()
    {
        return function_exists('imagecreatetruecolor');
    }

    private function isImageMagic()
    {
        return extension_loaded('imagick');
    }

    private function isGmagick()
    {
        return extension_loaded('gmagick');
    }
}
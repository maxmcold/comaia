<?php
/**
 * Library for Joomla for creating thumbnails of images
 *
 * @package Mavik Thumb
 * @version 1.0
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @copyright 2012 Vitaliy Marenkov
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('mavik.thumb.graphiclibrary.abstract');
jimport('joomla.filesystem.file');

/**
 * Adapter for Graphic Magic
 */
class MavikThumbGraphicLibraryGmagick extends MavikThumbGraphicLibrary {

    public function __construct()
    {
        $this->checkRequirements();
    }

    /**
     * Check the server requirements
     */
    protected function checkRequirements()
    {
        // Check version of GD
        if (!class_exists('Gmagick')) {
            throw new Exception(JText::_('Library mAvik Thumb needs library ImageMagic'), MavikThumbGenerator::ERROR_LIBRARY_IS_MISSING);
        }
    }

    /**
     * @param MavikThumbInfo $info
     * @param int $x
     * @param int $y
     * @param int $widht
     * @param int $height
     */
    public function createThumbnail(MavikThumbInfo $info, $x, $y, $widht, $height)
    {
        $gmagik = new Gmagick($info->original->path);
        $gmagik->cropimage($widht, $height, $x, $y);
        
        foreach ($info->thumbnails as $ratio => $thumbnail) {
            if ($info->isLess($thumbnail)) {
                $currentGmagik = clone $gmagik;
                $currentGmagik->resizeimage($thumbnail->realWidth, $thumbnail->realHeight, null, 1);
                ob_start();
                echo $currentGmagik;
                JFile::write($thumbnail->path, ob_get_contents());
                ob_end_clean();
            } else {
                unset($info->thumbnails[$ratio]);
            }
        }        
    }
}
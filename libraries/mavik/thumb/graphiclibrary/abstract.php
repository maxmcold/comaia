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

/**
 * Adapter for Graphic Library
 * Parent class
 */
abstract class MavikThumbGraphicLibrary
{
    /**
     * @var int
     */
    protected $quality = 90;

    /**
     * @param MavikThumbInfo $info
     * @param int $x
     * @param int $y
     * @param int $widht
     * @param int $height
     */
    public abstract function createThumbnail(MavikThumbInfo $info, $x, $y, $widht, $height);

    /**
     * Set quality for JPG
     * 
     * @param int $quality
     */
    public function setQuality($quality) {
        $this->quality = $quality;
    }
}

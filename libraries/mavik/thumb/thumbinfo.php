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

defined( '_JEXEC' ) or die;

jimport('mavik.thumb.thumbimageinfo');

/**
 * Information about thumbnail and original image
 */
class MavikThumbInfo {
   
    /**
     * Info about original image
     * 
     * @var MavikThumbImageInfo
     */
    var $original;
    
    /**
     * Info about thumbnail
     * 
     * @var MavikThumbImageInfo
     */    
    var $thumbnail;

    /**
     * Info about thumbnail fo high resolution displays
     * 
     * @var MavikThumbImageInfo[]
     */    
    var $thumbnails;
    
    public function __construct() {
        $this->original = new MavikThumbImageInfo();
        $this->thumbnails = array(1 => new MavikThumbImageInfo());
        $this->thumbnail =& $this->thumbnails[1]; 
    }
    
    /**
     * @param MavikThumbImageInfo $thumbnail
     * @return boolean
     */
    public function isLess(MavikThumbImageInfo $thumbnail)
    {
        return
            $thumbnail->realWidth && $thumbnail->realWidth < $this->original->width ||
            $thumbnail->realHeight && $thumbnail->realHeight < $this->original->height
        ;
    }
}
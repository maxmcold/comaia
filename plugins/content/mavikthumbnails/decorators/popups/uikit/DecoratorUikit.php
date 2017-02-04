<?php
/**
 * @package Joomla
 * @subpackage mavikThumbnails 2
 * @copyright 2014 Vitaliy Marenkov
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 * 
 * Plugin automatic replaces big images to thumbnails.
 */

namespace Plugin\Content\MavikThumbnails;

defined('_JEXEC') or die();

/**
 * Decorator for uikit Lightbox (http://getuikit.com/docs/lightbox.html)
 */
class DecoratorUikit extends DecoratorAbstract
{
    public function __construct(&$plugin)
    {
        $this->type = 'uikit';
        parent::__construct($plugin);
    }
    
    public function addHeader()
    {
        parent::addHeader();
        
        \JHtml::_('jquery.framework');
        $document = \JFactory::getDocument();
        if (JDEBUG) {
            $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/js/uikit.js');
            $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/js/components/lightbox.js');
            $document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/css/uikit.css');
            $document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/css/components/slidenav.css');
        } else {
            $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/js/uikit.min.js');
            $document->addScript('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/js/components/lightbox.min.js');
            $document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/css/uikit.min.css');
            $document->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/uikit/2.23.0/css/components/slidenav.min.css');
        }
    }
}

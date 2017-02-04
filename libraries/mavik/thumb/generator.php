<?php
/**
 * Library for Joomla for creating thumbnails of images
 * 
 * @package Mavik Thumb
 * @author Vitaliy Marenkov <admin@mavik.com.ua>
 * @copyright 2012 Vitaliy Marenkov
 * @license GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

if(!defined('DS')){
        define('DS',DIRECTORY_SEPARATOR);
}

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('mavik.thumb.thumbinfo');

/**
 * Generator of thumbnails
 *
 * <code> 
 * Params {
 *   thumbDir: Directory for thumbnails
 *   subDirs: Create subdirectories in thumbnail derectory
 *   copyRemote: Copy remote images
 *   remoteDir: Directory for copying remote images or info about them
 *   quality: Quality of jpg-images
 *   resizeType: Method of resizing
 *   defaultSize: Use default size ''|'all'|'not_resized'
 *   defaultWidth: Default width
 *   defaultHeight: Default heigh
 *   ratios: Ratios are used for generation of thumbnails for high resolution displays
 * }
 * </code> 
 */
class MavikThumbGenerator {
    
    /**
     * Codes of errors
     */
    const ERROR_DIRECTORY_CREATION = 1;
    const ERROR_FILE_CREATION = 2;
    const ERROR_LIBRARY_IS_MISSING = 3;
    const ERROR_NOT_ENOUGH_MEMORY = 4;    
    const ERROR_UNSUPPORTED_TYPE = 5;

    /**
     * Params
     * 
     * @var array
     */
    var $params = array(
        'thumbDir' => 'images/thumbnails', // Directory for thumbnails
        'subDirs' => true, // Create subdirectories in thumbnail derectory
        'copyRemote' => false, // Copy remote images
        'remoteDir' => 'images/remote', // Directory for copying remote images or info about them
        'quality' => 90, // Quality of jpg-images
        'resizeType' => 'fill', // Method of resizing
        'defaultSize' => '', // Use default size
        'defaultWidth' => null, // Default width
        'defaultHeight' => null, // Default heigh
        'graphicLibrary' => 'gd2',
        'ratios' => [1],
    );

    /**
     * Current Strategy of resizing
     * 
     * @var MavikThumbResizeType
     */
    protected $resizeStrategy;

    /**
     * All used Strategies of resizing
     * 
     * @var array of MavikThumbResizeType
     */
    protected static $resizeStrategies = array();

    /**
     * @var MavikThumbGraphicLibrary
     */
    protected $graphicLibrary;

    /**
     * Initialisation of library
     * 
     * @param array $params
     */
    public function __construct(array $params = array())
    {        
        $this->setParams($params);        
    }     

    /**
     * Set parameters
     *
     * @staticvar boolean $inited
     * @param array $params
     */
    public function setParams($params) {
        static $inited = false;

        // Set all parameters
        $this->params = array_merge($this->params, $params);

        /**
         * Process parameters that require special action
         */

        if(!$inited || isset($params['resizeType'])) {
            $this->setResizeType($this->params['resizeType']);
        }

        if(!$inited || isset($params['thumbDir']) || isset($params['remoteDir'])) {
            $this->checkDirectories();
        }

        if(!$inited || isset($params['graphicLibrary'])) {
            $this->setGraphicLibrary($this->params['graphicLibrary']);
        }

        if(!$inited || isset($params['quality'])) {
            $this->graphicLibrary->setQuality($this->params['quality']);
        }

        $inited = true;
    }

    /**
     * Check and create, if it's need, directories
     */
    protected function checkDirectories()
    {
        $indexFile = '<html><body bgcolor="#FFFFFF"></body></html>';
        $dir = JPATH_SITE.DS.$this->params['thumbDir'];
        if (!JFolder::exists($dir)) {
            if (!JFolder::create($dir)) {
                throw new Exception(JText::sprintf( 'Can\'t create directory', $dir ), self::ERROR_DIRECTORY_CREATION);
            }
            JFile::write($dir.DS.'index.html', $indexFile);
        }
        $dir = JPATH_SITE.DS.$this->params['remoteDir'];
        if (!JFolder::exists($dir)) {
            if (!JFolder::create($dir)) {
                throw new Exception(JText::sprintf( 'Can\'t create directory', $dir ), self::ERROR_DIRECTORY_CREATION);
            }
            JFile::write($dir.DS.'index.html', $indexFile);
        }
    }

    protected function setGraphicLibrary($graphicLibrary)
    {
        jimport("mavik.thumb.graphiclibrary.$graphicLibrary");
        $class = 'MavikThumbGraphicLibrary'.ucfirst($graphicLibrary);
        $this->graphicLibrary = new $class;
    }

    /**
     * Set resize type
     *
     * @deprecated Use setParams. It will be protected.
     * 
     * @param string $type 
     */
    public function setResizeType($type)
    {
        if (empty(self::$resizeStrategies[$type])) {
            jimport("mavik.thumb.resizetype.$type");
            $class = 'MavikThumbResize'.ucfirst($type);
            self::$resizeStrategies[$type] = new $class;
        }
        $this->resizeStrategy = self::$resizeStrategies[$type];
    }

    /**
     * Set default size
     *
     * @deprecated Use setParams
     * 
     * @param string $case ''|'all'|'not_resized'
     * @param int $width
     * @param int $height
     */
    public function setDefaultSize($case, $width, $height)
    {
        $this->params['defaultSize'] = $case;
        $this->params['defaultWidth'] = $width;
        $this->params['defaultHeight'] = $height;
    }

    /**
     * Get thumbnail, create if it not exist
     * 
     * @param string $src Path or URI of image
     * @param int $width Width of thumbnail
     * @param int $height Height of thumbnail
     * @param int $sizeInPixels This parameter for correct working of default sizes
     * @param float $ratio Ratio of real and imaged sizes
     * @return MavikThumbInfo
     */
    public function getThumb($src, $width = 0, $height = 0, $sizeInPixels = true, $ratio = 1)
    {
        $src = urldecode($src);
        $info = $this->getImageInfo($src, $width, $height, $sizeInPixels, $ratio);

        // Is not there thumbnail in cache?
        if($info->thumbnail->path && !$this->thumbExists($info)) {
            $this->testAllocatedMemory($info);
            list($x, $y, $widht, $height) = $this->resizeStrategy->getArea($info);
            $this->graphicLibrary->createThumbnail($info, $x, $y, $widht, $height, $this->params['quality']);
        }
        
        return $info;
    }

    /**
     * Get info about original image and thumbnail
     * 
     * @param string $src Path or url to original image
     * @param type $width Desired width for thumbnail
     * @param type $height Desired height for thumbnail
     * @param float $ratio Ratio of real and imaged sizes
     * @return MavikThumbInfo
     */
    protected function getImageInfo($src, $width, $height, $sizeInPixels = true, $ratio = 1)
    {
        $info = new MavikThumbInfo();
        $this->getOriginalPath($src, $info);
        if (!$info->original->path) {
            return $info;
        }
        $this->getOriginalSize($info);

        if (
            $sizeInPixels && ($width || $height || $this->params['defaultSize']) ||
            $this->params['defaultSize'] == 'all'
        ) {
            $this->setThumbSize($info, $width, $height);
            $this->setThumbRealSize($info, $ratio);
            $this->setThumbPath($info, $info->isLess($info->thumbnail));
        }
        
        return $info;
    }

    /**
     * Get info about URL and path of original image.
     * And copy remote image if it's need.
     * 
     * @param string $src
     * @param MavikThumbInfo
     */
    protected function getOriginalPath($src, MavikThumbInfo $info)
    {
        /*
         *  Is it URL or PATH?
         */
        if(file_exists($src) || file_exists(JPATH_ROOT.DS.$src)) {
            /*
             *  $src IS PATH
             */
            $info->original->local = true;
            $info->original->path = $this->pathToAbsolute($src);
            $info->original->url = $this->pathToUrl($info->original->path);
        } else {
            /*
             *  $src IS URL
             */
            $info->original->local = $this->isUrlLocal($src);
            
            if($info->original->local) {
                /*
                 * Local image
                 */
                $uri = JURI::getInstance($src);
                $query = $uri->getQuery();
                $info->original->url = $uri->getPath() . ($query ? "?{$query}" : '');
                $info->original->path = $this->urlToPath($src);
            } else {
                /*
                 * Remote image
                 */
                $src = $this->fullUrl($src);
                if($this->params['copyRemote'] && $this->params['remoteDir'] ) {
                    $this->copyRemoteFile($src, $info);
                } else {
                    // For remote image path is url
                    $info->original->url = str_replace(' ', '+', $src);
                    $info->original->path = $info->original->url;
                    
                }                
            }
        }
    }

    /**
     * Copy remote file to local directory
     *
     * @param string $src
     * @param MavikThumbInfo $info
     */
    private function copyRemoteFile($src, MavikThumbInfo $info)
    {
        $localFile = $this->getSafeName($src, $this->params['remoteDir'], '', false);
        if (!file_exists($localFile)) {
            // Copy file
            $buffer = file_get_contents($src);
            JFile::write($localFile, $buffer);
            unset($buffer);
        }
        // New url and path
        $info->original->path = $localFile;
        $info->original->url = $this->pathToUrl($localFile);
    }

    /**
     * Get size and type of original image
     * 
     * @param MavikThumbInfo $info
     * @param boolean $recursive
     */
    protected function getOriginalSize(MavikThumbInfo $info, $recursive = false)
    {
        // Get size and type of image. Use info-file for remote image
        $useInfoFile = !$info->original->local && !$this->params['copyRemote'] && $this->params['remoteDir'];
        if($useInfoFile) {
            $infoFile = $this->getSafeName($info->original->url, $this->params['remoteDir'], '', false, 'info');
            
            if(file_exists($infoFile)) {
                $size = unserialize(file_get_contents($infoFile));
                $info->original->size = isset($size['filesize']) ? $size['filesize'] : null;
            }
            
            if (!isset($size[0])) {
                $size = getimagesize($info->original->url);
                $info->original->size = JFilesystemHelper::remotefsize($info->original->url);
                $size['filesize'] = $info->original->size;
                if($useInfoFile) {
                    JFile::write($infoFile, serialize($size));
                }
            }            
        } else {
            $size = @getimagesize($info->original->path);
            $info->original->size = @filesize($info->original->path);
        }

        /**
         * If url point to script, set local=false and call function again
         */
        if (!isset($size[0]) && !$recursive) {
            $info->original->local = false;
            $info->original->url = $this->fullUrl($info->original->url);
            $info->original->path = $info->original->url;
            $size = $this->getOriginalSize($info, true);
        }

        // Put values to $info
        $info->original->width = $size[0];
        $info->original->height = $size[1];
        $info->original->type = $size['mime'];

        return $size; // for recursive
    }

    /**
     * Set thumbanil size
     * 
     * @param MavikThumbInfo $info
     * @param int $width
     * @param int $height
     */
    protected function setThumbSize(MavikThumbInfo $info, $width, $height)
    {
        if ($this->useDefaultSize($info->original, $width, $height)) {
            if (
                $this->params['defaultWidth'] && $info->original->width > $this->params['defaultWidth']
            ) {
                $width = $this->params['defaultWidth'];
            }

            if (
                $this->params['defaultHeight'] && $info->original->height > $this->params['defaultHeight']
            ) {
                $height = $this->params['defaultHeight'];
            }
        }

        // Set widht or height if it is 0
        if ($width == 0) $width = intval($height * $info->original->width / $info->original->height); 
        if ($height == 0) $height = intval($width * $info->original->height / $info->original->width);
        
        $this->resizeStrategy->setSize($info, $width, $height, $this->params);
    }
    
    /**
     * Set real size of thumbnail
     * 
     * @param MavikThumbInfo $info
     * @param type $ratio
     */
    protected function setThumbRealSize(MavikThumbInfo $info, $ratio)
    {
        if ($info->thumbnail->height * $ratio > $info->original->height) {
            $ratio = $info->original->height / $info->thumbnail->height;
        }
        if ($info->thumbnail->width * $ratio > $info->original->width) {
            $ratio = $info->original->width / $info->thumbnail->width;
        }
        $info->thumbnail->realWidth = floor($info->thumbnail->width * $ratio); 
        $info->thumbnail->realHeight = floor($info->thumbnail->height * $ratio);
        
        foreach ($this->params['ratios'] as $ratio) {
            $info->thumbnails[$ratio] = clone $info->thumbnail;
            $info->thumbnails[$ratio]->realWidth *= $ratio;
            $info->thumbnails[$ratio]->realHeight *= $ratio;
        }
    }    
    
    /**
     * Set path and url of thumbnail
     * 
     * @param MavikThumbInfo $info
     * @param bolean $isLess
     */
    protected function setThumbPath(MavikThumbInfo $info, $isLess)
    {
        if (!$isLess) {
            $info->thumbnail->url = $info->original->url;
            return;
        }
        
        $suffix = "-{$this->params['resizeType']}-{$info->thumbnail->realWidth}x{$info->thumbnail->realHeight}";
        
        $info->thumbnail->path = $this->getSafeName($info->original->path, $this->params['thumbDir'], $suffix, $info->original->local);
        $info->thumbnail->url = $this->pathToUrl($info->thumbnail->path);
        $info->thumbnail->local = true;
        
        foreach ($info->thumbnails as $ratio => &$thumbnail) {
            $dir = $this->params['thumbDir'];
            if ($ratio != 1) {
                $dir .= "/@{$ratio}";
            }
            $thumbnail->path = $this->getSafeName($info->original->path, $dir, $suffix, $info->original->local);
            $thumbnail->url = $this->pathToUrl($thumbnail->path);
            $thumbnail->local = true;            
        }
    }   

    /**
     * Get absolute path
     * 
     * @param string $path
     * @return string 
     */
    protected function pathToAbsolute($path)
    {
        // $paht is c:\<path> or \<path> or /<path> or <path>
        if (!preg_match('/^\\\|\/|([a-z]\:)/i', $path)) $path = JPATH_ROOT.DS.$path;
        return realpath($path);
    }

    /**
     * Get URL from absolute path
     * 
     * @param string $path
     * @return string
     */
    protected function pathToUrl($path)
    {
        $base = JURI::base(true);
        $path = $base.substr($path, strlen(JPATH_SITE));
        
        return str_replace(DS, '/', $path);
    }
        
    /**
     * Is URL local?
     * 
     * @param string $url
     * @return boolean
     */
    protected function isUrlLocal($url)
    {
        $siteUri = JFactory::getURI();
        $imgUri = JURI::getInstance($url);

        // If url has query it must be processed as remote
        if ($imgUri->getQuery()) {
            return false;
        }

        $siteHost = $siteUri->getHost();
        $imgHost = $imgUri->getHost();
        // ignore www in host name
        $siteHost = preg_replace('/^www\./', '', $siteHost);
        $imgHost = preg_replace('/^www\./', '', $imgHost);
        
        return (empty($imgHost) || $imgHost == $siteHost);
    }        

    /**
     * Get safe name
     * 
     * @param string $path Path to file
     * @param string $dir Directory for result file
     * @param string $suffix Suffix for name of file (example size for thumbnail)
     * @param string $secondExt New extension
     * @return string 
     */
    protected function getSafeName($path, $dir, $suffix = '', $isLocal = true, $secondExt = null)
    {
        if(!$isLocal) {
            $uri = JURI::getInstance($path);
            $query = $uri->getQuery();
            $queryCode = sha1($query);
            $path = $uri->getHost().$uri->getPath() . ($queryCode ? "_{$queryCode}" : '');
        }
        
        // Absolute path to relative
        if(strpos($path, JPATH_SITE) === 0) $path = substr($path, strlen(JPATH_SITE)+1);

        $lang = JFactory::getLanguage();

        if(!$this->params['subDirs']) {
            // Without subdirs
            $name = str_replace(array('/','\\'), '-', $path);
            $ext = JFile::getExt($name);
            $name = JFile::stripExt($name).$suffix.($ext ? '.'.$ext : '').($secondExt ? '.'.$secondExt : '');
            $path = JPATH_ROOT.DS.$dir.DS.$name; 
        } else {
            // With subdirs
            $name = JFile::getName($path);
            $ext = JFile::getExt($name);
            $name = JFile::stripExt($name).$suffix.($ext ? '.'.$ext : '').($secondExt ? '.'.$secondExt : '');
            $path = JPATH_BASE.DS.$dir.DS.$path;
            $path = str_replace('\\', DS, $path);
            $path = str_replace('/', DS, $path);
            $path = substr($path, 0, strrpos($path, DS));
            if(!JFolder::exists($path)) {
                JFolder::create($path);
                $indexFile = '<html><body bgcolor="#FFFFFF"></body></html>';
                JFile::write($path.DS.'index.html', $indexFile);
            }
            $path = $path . DS . $name;            
        }
        
        return $path;
    }
    
    /**
    * Convert local url to path
    * 
    * @param string $url
    */
    protected static function urlToPath($url)
    {
        $imgUri = JURI::getInstance($url);
        $query = $imgUri->getQuery();
        $path = $imgUri->getPath() . ($query ? "?{$query}" : '');
        $base = JURI::base(true);
        if($base && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        return realpath(JPATH_ROOT.DS.str_replace('/', DS, $path));
    }
    
    /**
     * Does thumbnail exist and is it actual?
     * 
     * @param MavikThumbInfo $info
     * @return boolean
     */
    protected function thumbExists(MavikThumbInfo $info)
    {   
        $originalChangeTime = $this->getOriginalChangeTime($info);
        foreach ($info->thumbnails as $thumbnail) {
            if(
                !JFile::exists($thumbnail->path) ||
                $originalChangeTime > filectime($thumbnail->path)
            ) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @param MavikThumbInfo $info
     * @return int timestamp
     */
    protected function getOriginalChangeTime(MavikThumbInfo $info)
    {
        if ($info->original->local || $this->params['copyRemote']) {
            $timestamp = filectime($info->original->path);
        } else {
            $header = get_headers($info->original->url, 1);
            $timestamp = 0;
            if ($header && strstr($header[0], '200') !== false && !empty($header['Last-Modified'])) {
                try {
                    $changeTime = new \DateTime($header['Last-Modified']);
                    $timestamp = $changeTime->getTimestamp();
                } catch (Exception $e) {}
            }
        }
        return (int) $timestamp;
    }

    /**
     * Image is reduced, increased or not changed
     *
     * @param MavikThumbImageInfo $original
     * @return int
     */
    private function isResized(MavikThumbImageInfo $original, $width, $heigh)
    {
        if ($width && $width < $original->width || $heigh && $heigh < $original->height) {
            return 1;
        } elseif (($original->width == $width || !$width) && ($original->height == $heigh || !$heigh)) {
            return 0;
        } else  {
            return -1;
        }
    }

    /**
     * Use default size
     *
     * @param MavikThumbImageInfo $original
     * @param int $width
     * @param int $heigh
     * @return boolean
     */
    private function useDefaultSize(MavikThumbImageInfo $original, $width, $heigh)
    {
        if (empty($this->params['defaultSize'])) {
            return false;
        } elseif ($this->params['defaultSize'] == 'all') {
            return true;
        } elseif ($this->params['defaultSize'] == 'not_resized') {
            return $this->isResized($original, $width, $heigh) == 0;
        }
    }

    /**
     * Get memory limit (bytes)
     * 
     * @return int
     */
    protected function getMemoryLimit()
    {
        $sizeStr = ini_get('memory_limit');
        switch (substr ($sizeStr, -1))
        {
            case 'M': case 'm': return (int) $sizeStr * 1048576;
            case 'K': case 'k': return (int) $sizeStr * 1024;
            case 'G': case 'g': return (int) $sizeStr * 1073741824;
            default: return (int) $sizeStr;
        }
    }

    /**
     * @param MavikThumbInfo $info
     * @throws Exception
     */
    protected function testAllocatedMemory(MavikThumbInfo $info)
    {
        $allocatedMemory = $this->getMemoryLimit() - memory_get_usage(true);
        $neededMemory = $info->original->width * $info->original->height * 4;
        foreach ($info->thumbnails as $thumbnail) {
            $neededMemory += $thumbnail->width * $thumbnail->height * 4;
        }
        $neededMemory *= 1.25; // +25%
        if ($neededMemory >= $allocatedMemory) {
            throw new Exception(JText::_('Not enough memory'), self::ERROR_NOT_ENOUGH_MEMORY);
        }
    }

    /**
     * @param string $url
     * @return string
     */
    protected function fullUrl($url)
    {
        $uri = new \Joomla\Uri\Uri($url);
        if (!$uri->getHost()) {
            $path = $uri->getPath();
            $query = $uri->getQuery();
            $basePath = JUri::base(true);
            if ($basePath && strpos($path, $basePath) === 0) {
                $path = substr($path, strlen($basePath) + 1);
            }
            return JUri::base() . $path . ($query ? "?{$query}" : '');
        }
        return $url;
    }
}
?>

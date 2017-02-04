<?php
/**
 * @copyright	Copyright © 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan
 * @author mail	james.liu668@gmail.com
 * @website		http://www.joompolitan.com/
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class plgContentLightGallery extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	function onContentPrepare( $context, &$row, &$params, $page=0 )
	{
		$app = JFactory::getApplication();
        // Plugin code goes here.
        // You can access parameters via $this->params.
		
		// expression to search for
		$regex = "#{lightgallery(.*?)}(.*?){/lightgallery}#s";

		// find all instances of plugin and put in $matches
		$matches = array();
		preg_match_all( $regex, $row->text, $matches, PREG_SET_ORDER );
		$num = sizeof($matches);

		for ($i = 0; $i < $num; $i++) { 
			$galleryHTML = $this->generateGallery( $this->params, $matches[$i], $i );
			$row->text = preg_replace("{".$matches[$i][0]."}", $galleryHTML , $row->text, 1);
		}
		
		if($num > 0)
		{
			$currentURL = $this->params->def('websiteBaseURL');
			if ($currentURL == ''){
				$currentURL = 'http://'.$_SERVER['HTTP_HOST'];
			}
			
			$currentURL = $currentURL . '/plugins/content/lightgallery/';
			
            //avoid multiple import
			$galleryJS = "";
            $useJquery = $this->params->def('useJquery');
            if($useJquery == 1) {
                if(!defined('JMS-GALLERY-JQUERY')) {
                    define('JMS-GALLERY-JQUERY', 'true');
                    $galleryJS .= "<script src=\"".$currentURL."js/jquery-1.11.0.min.js\"></script>";
                }
            }
			
			if(!defined('JMS-GALLERY-LIGHTBOX')) {
				define('JMS-GALLERY-LIGHTBOX', 'true');
				$galleryJS .= "<script src=\"".$currentURL."js/lightbox.min.js\"></script>";
				$galleryJS .= "<link href=\"".$currentURL."css/lightbox.css\" rel=\"stylesheet\" />";
				$galleryJS .= "<link href=\"".$currentURL."css/overwrite.css\" rel=\"stylesheet\" />";
			}
			
			$row->text .= $galleryJS;
			
			$anchorTextArray = array(
				"<a style=\"font-size: 8px !important;text-decoration: none  !important; color: #E0E0E0  !important;\" href=\"http://www.joompolitan.com/lightgallery.html\" title\"Free Joomla Lightbox Gallery\">Free Joomla Lightbox Gallery</a>", 
				"<a style=\"font-size: 8px !important;text-decoration: none  !important; color: #E0E0E0  !important;\" href=\"http://www.joompolitan.com/lightgallery.html\" title\"Free Lightbox Gallery\">Free Lightbox Gallery</a>",
				"<a style=\"font-size: 8px !important;text-decoration: none  !important; color: #E0E0E0  !important;\" href=\"http://www.joompolitan.com/\" title\"Joomla Plugins\">Joomla Plugins</a>",
				"<a style=\"font-size: 8px !important;text-decoration: none  !important; color: #E0E0E0  !important;\" href=\"http://www.joompolitan.com/\" title\"Joomla Extensions\">Joomla Extensions</a>",
				"<a style=\"font-size: 8px !important;text-decoration: none  !important; color: #E0E0E0  !important;\" href=\"http://www.joompolitan.com/products.html\" title\"More Joomla Extensions\">More Joomla Extensions</a>");
				
			$row->text .= $anchorTextArray[$currentURL%5];
		}
		
		return true;
	}
	
	private function generateGallery( $params, $options ) {
		$options[1] = str_replace (" ", "&", $options[1]); //replace space with &
		parse_str($options[1], $OptionArray);
		
		//gallery attribute
		$urlstr = array();
		if(empty($OptionArray['type']))
		{
			return "<strong>Light Gallery format is incorrect, type is missing!</strong>";
		}
		else if(empty($OptionArray['path']))
		{
			return "<strong>Light Gallery format is incorrect, path is missing!</strong>";
		}
		
		
		if(empty($OptionArray['previewWidth']))
		{
			$OptionArray['previewWidth'] = "";
		}
		
		if(empty($OptionArray['previewHeight']))
		{
			$OptionArray['previewHeight'] = "";
		}

		if(empty($OptionArray['galleryName']))
		{
			$OptionArray['galleryName'] = "Joompolitan Light Gallery";
		}

		if(!empty($OptionArray['enabled']) && $OptionArray['enabled'] === "false")
		{
			return str_replace ("enabled=false ", "", $options[0]); //remove this flag and config show string
		}

		switch($OptionArray['type'])
		{
			case "url":
				$urlstr[] = trim($OptionArray['path']);
				break;
			case "local":
				$path = trim($OptionArray['path']);
				if(file_exists($path))
				{
					$currentURL = $params->def('websiteBaseURL');
					if ($currentURL == ''){
						$currentURL = 'http://'.$_SERVER['HTTP_HOST'];
					}
					
					if(is_dir($path))
					{
						//loop the path and build image url
						$files = scandir($path);
						$imageExt = array("png", "jpg", "jpeg", "gif", "PNG", "JPG", "JPEG", "GIF");
						foreach($files as $file)
						{
							if(!is_dir($file))
							{
								$file_extension =  pathinfo($file, PATHINFO_EXTENSION);
								if(in_array($file_extension, $imageExt))
								{
									$urlstr[] = $currentURL."/".$path."/".$file;
								}
							}
						}
					}
					else
					{
						$urlstr[] = $currentURL.$path;
					}
				}
				else
				{
					return "<strong>Light Gallery cannot find your image path: <i>$path</i>!</strong>";
				}
				
				break;
			default:
				return $OptionArray['type']."<strong>Light Gallery Format is incorrect, path is missing!</strong>";
		}
		
		//find parameter
		$margin = "10px";
		$galleryText = "";
		foreach($urlstr as $k=>$v)
		{
			$galleryText .= "<a href=\"".$v."\" data-lightbox=\"".$OptionArray['galleryName']."\" data-title=\"".$options[2]."\">"."<img src=\"".$v."\" style=\"margin: ".$margin."; width: ".$OptionArray['previewWidth']."px; height: ".$OptionArray['previewHeight']."px;\">"."</a>";
		}
		return $galleryText;
	}
}
?>
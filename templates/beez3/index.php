<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');

JHtml::_('behavior.framework', true);

// Output as HTML5
$this->setHtml5(true);


JHtml::_('bootstrap.framework');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/md_stylechanger.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/hide.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/respond.src.js');
$this->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/template.js');

require __DIR__ . '/jsstrings.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
	    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/css/general.css" rel="stylesheet" type="text/css" />
	</head>
	<body>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-12 col-sm-12 col-lg-12 no-padding">

                <nav class="navbar">
                    <div class="container-fluid">
                        <!-- Brand and toggle get grouped for better mobile display -->
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu" aria-expanded="false">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="index.php">
                                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/logo.png" alt="logo" class="img-responsive" />
                            </a>
                        </div>

                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="menu">
                            <jdoc:include type="modules" name="menu" headerLevel="3" />
                        </div>
                        <!-- /.navbar-collapse -->
                    </div>
                    <!-- /.container-fluid -->
                </nav>

            </div>
        </div>
    </div>
    <div class='col-xs-12 col-md-12 col-lg-12 no-padding'>
		
		<?php

$app = JFactory::getApplication();

$menu = $app->getMenu();

$menuname = $menu->getActive()->id;
		
		
// echo "<img src=\"".$this->baseurl."/templates/".$this->template."/assets/img/sky.jpg\" class='cover' alt='".$menuname."' />";
echo "<img src=\"".$this->baseurl."/templates/".$this->template."/assets/img/".$menuname.".jpg\" class='cover' alt='".$menuname."' />";



?>
		
		
    </div>
    <!-- pezzo uno -->
    <div id="azienda">
        <div class="container">
            <jdoc:include type="component" />
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-xs-12 col-md-4 col-lg-4 no-padding">
                    <ul>
                        <li>Comaia</li>
                        <li>Via Casilina Nord Km 148,300</li>
                        <li>03040 San Vittore del Lazio - FR</li>
                    </ul>
                </div>
                <div class="col-xs-12 col-md-4 col-lg-4 no-padding">
                    <ul class="list-inline text-center social-bar">
                        <li>
                            <a href="http://www.facebook.com/comaiaonline/" target="_blank">
                                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/social/facebook.png" alt="facebook" class="img-responsive" />
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-xs-12 col-md-4 col-lg-4 no-padding text-right allinea-sinistra">
                    <ul>
                        <li>tel. 0776 344954</li>
                        <li>fax 0776 344968</li>
                        <li>info@comaia.it</li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
	
</html>

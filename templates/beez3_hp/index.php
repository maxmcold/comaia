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

    <div id="slider" class="carousel slide" data-ride="carousel">

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/carousel/carousel1.jpg" alt="Comaia">
            </div>
            <div class="item">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/carousel/carousel2.jpg" alt="Comaia">
            </div>
			            <div class="item">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/carousel/carousel3.jpg" alt="Comaia">
            </div>
            <div class="item">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/carousel/carousel4.jpg" alt="Comaia">
            </div>
            <div class="item">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/carousel/carousel5.jpg" alt="Comaia">
            </div>


        </div>

        <!-- Left and right controls -->
        <a class="left carousel-control" href="#slider" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#slider" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="container" id="brand">
        <div class="row">
            <div class="col-xs-12 col-md-12 col-lg-12">
                <h1 class="text-center font-light text-uppercase">I Nostri Brand</h1>
            </div>
            <div class="col-xs-12 col-md-2 col-lg-2 col-sm-2 partner">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/landini.jpg" class="img-responsive" alt="landini" />
            </div>
            <div class="col-xs-12 col-md-2 col-lg-2 col-sm-2 partner">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/mccormick.jpg" class="img-responsive" alt="mccormick" />
            </div>
            <div class="col-xs-12 col-md-2 col-lg-2 col-sm-2 partner">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/JCB.jpg" class="img-responsive" alt="JCB" />
            </div>
			<div class="col-xs-12 col-md-2 col-lg-2 col-sm-2 partner">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Perkins.jpg" class="img-responsive" alt="Perkins" />
            </div>
			<div class="col-xs-12 col-md-2 col-lg-2 col-sm-2 partner">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Bcs.jpg" class="img-responsive" alt="Bcs" />
            </div>
			<div class="col-xs-12 col-md-2 col-lg-2 col-sm-2 partner">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Kuhn.jpg" class="img-responsive" alt="Kuhn" />
            </div>

        </div>
		 <div class="row">
            <div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2 col-md-offset-3">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/al-ko.jpg" class="img-responsive" alt="al-ko" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/assaloni.jpg" class="img-responsive" alt="assaloni" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/casorzo.jpg" class="img-responsive" alt="casorzo" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/comap.jpg" class="img-responsive" alt="comap" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Echo.jpg" class="img-responsive" alt="Echo" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/emme enne.jpg" class="img-responsive" alt="emme enne" />
            </div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2 col-md-offset-3">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Feraboli.jpg" class="img-responsive" alt="Feraboli" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/gamberini.jpg" class="img-responsive" alt="gamberini" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/garmec.jpg" class="img-responsive" alt="garmec" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Hinowa.jpg" class="img-responsive" alt="hinowa" />
            </div>
            <div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Comaca.jpg" class="img-responsive" alt="Comaca" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/husqvarna.jpg" class="img-responsive" alt="husqvarna" />
            </div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2 col-md-offset-3">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Hymach.jpg" class="img-responsive" alt="Hymach" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Lisam.jpg" class="img-responsive" alt="Lisam" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/mase.jpg" class="img-responsive" alt="mase" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/ocmis.jpg" class="img-responsive" alt="ocmis" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/rinieri.jpg" class="img-responsive" alt="rinieri" />
            </div>
			<div class="col-xs-12 col-md-1 col-lg-1 col-sm-1 partner2">
                <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/partner/Sicma.jpg" class="img-responsive" alt="Sicma" />
            </div>
		</div>
    </div>
</div>
    <div id="azienda">
        <div class="container">
            <div class="row">
                <article class="col-xs-12 col-sm-6 col-md-4 col-lg-4 azienda-testo no-padding">
                    <div class="spazio-int">
                        <h1 class="text-center">Azienda</h1>
                        <p>
                            All'inizio degli anni '50
                            i primi motori diesel dei
                            trattori agricoli nati subito
                            dopo la fine della seconda guerra
                            mondiale, si affermano sul mercato
                            divenendo affidabili, economici, robusti
                            e veloci. E' proprio sull'onda di questa
                            trasformazione tecnologica e culturale, che...
                        </p>
                        <a class="text-uppercase" href="http://www.comaia.it/index.php/l-azienda">Leggi Tutto</a>
                    </div>
                    <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/img-azienda.jpg" class="img-responsive" />
                </article>
                <section class="col-xs-12 col-sm-6 col-md-8 col-lg-8 no-padding">
                    <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/assets/img/trattore.jpg" class="img-responsive" />
                </section>
            </div>
        </div>
    </div>
	<div id="mappa">
    <div class="container">
        <div class="row">
            <div class="no-padding col-xs-12 col-md-12 col-lg-12">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d11962.018466213742!2d13.9131418!3d41.4499695!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x158d37275d2ec774!2sCOMAIA!5e0!3m2!1sit!2sit!4v1480172030125" width="100%" height="200" frameborder="0" style="border:0" allowfullscreen></iframe>
            </div>
        </div>
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

    <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
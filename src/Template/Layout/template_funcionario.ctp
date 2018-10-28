<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @author        Gustavo Souza Gonçalves
 * @date          13/07/2017
 * @project_name  GOTAS
 * @file          src\Template\Layout\template_funcionario.ctp
 * @info          Template para perfil do tipo Funcionário
 */
use Cake\Core\Configure;

header("Access-Control-Allow-Origin: *");
$usuarioLogado = $this->Auth->User();

$titlePage = 'GOTAS - Funcionário ' . $usuarioLogado['nome'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?= $titlePage ?></title>
    <script src="https://maps.googleapis.com/maps/api/js?sensor=true&key=AIzaSyBzwpETAdxu2NQyLLtw16ndZkldjQ5Zqxg" async defer></script>
    <?php

    if (Configure::read('debug')) {
        echo $this->Html->meta('icon');
        echo $this->Html->css('home-rti');
        echo $this->Html->css(['bootstrap/css/bootstrap', 'bootstrap/css/bootstrap-theme']);
        echo $this->Html->css(['font-awesome/css/font-awesome']);
        echo $this->Html->css(['bootstrap-datetimepicker/css/bootstrap-datetimepicker']);

        echo $this->Html->script('jquery/jquery');
        echo $this->Html->script('jquery-Mask/jquery.mask');
        echo $this->Html->script('jquery-barcode-2.0.3/jquery-barcode');
        echo $this->Html->script('printThis-master/printThis');

        echo $this->Html->script('plentz-jquery-maskmoney/dist/jquery.maskMoney');
        echo $this->Html->script('bootstrap/js/bootstrap');
        echo $this->Html->script(['bootstrap-datetimepicker/js/bootstrap-datetimepicker', 'bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.pt-br']);
        echo $this->Html->script(['instascan/instascan.min']);

        echo $this->Html->script('scripts/pages/home');
        echo $this->Html->script('jquery-qrcode/jquery-qrcode.0.14.0');
        echo $this->Html->script('pdf417-gh-pages/pdf417');
        echo $this->Html->script('scripts/util/pdf417_helper');
    } else {
        echo $this->Html->meta('icon');
        echo $this->Html->css('home-rti');
        echo $this->Html->css(['bootstrap/css/bootstrap.min', 'bootstrap/css/bootstrap-theme.min']);
        echo $this->Html->css(['font-awesome/css/font-awesome.min']);
        echo $this->Html->css(['bootstrap-datetimepicker/css/bootstrap-datetimepicker.min']);

        echo $this->Html->script('jquery/jquery.min');
        echo $this->Html->script('jquery-Mask/jquery.mask.min');
        echo $this->Html->script('jquery-barcode-2.0.3/jquery-barcode.min');
        echo $this->Html->script('printThis-master/printThis');
        echo $this->Html->script('plentz-jquery-maskmoney/dist/jquery.maskMoney.min');
        echo $this->Html->script('bootstrap/js/bootstrap.min');
        echo $this->Html->script(['bootstrap-datetimepicker/js/bootstrap-datetimepicker.min', 'bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.pt-br']);
        echo $this->Html->script(['instascan/instascan.min']);
        echo $this->Html->script('scripts/pages/home');
        echo $this->Html->script('jquery-qrcode/jquery-qrcode.0.14.0.min');
        echo $this->Html->script('pdf417-gh-pages/pdf417');
        echo $this->Html->script('scripts/util/pdf417_helper');
    }

    ?><script src='https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.js'></script>
    <?php

    $this->fetch('meta');
    $this->fetch('css');
    $this->fetch('script');

    ?>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $this->Url->build(isset($project_url) ? $project_url : '/'); ?>">
                    <?php
                        if (isset($project_name)) {
                            echo $project_name;
                        } else {
                            echo 'Cake Twitter Bootstrap';
                        }
                    ?>
                </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <?php
                    $default_nav_bar_left = ROOT . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'nav-bar-left.ctp';
                    if (file_exists($default_nav_bar_left)) {
                        ob_start();
                        include $default_nav_bar_left;
                        echo ob_get_clean();
                    } else {
                        echo $this->element('nav-bar-left');
                    }
                    ?>
                    <?php
                    $default_nav_bar_right = ROOT . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'nav-bar-right.ctp';
                    if (file_exists($default_nav_bar_right)) {
                        ob_start();
                        include $default_nav_bar_right;
                        echo ob_get_clean();
                    } else {
                        echo $this->element('nav-bar-right');
                    }
                    ?>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <!-- <section class="container clearfix"> -->
        <?= $this->element('header') ?>
        <?= $this->element('loader') ?>

        <section class="container-content clearfix">
			<div class="row row-container">

				<?= $this->Flash->render() ?>
				<?= $this->fetch('content') ?>

				<?= $this->element('modal_container') ?>
			</div>
			<div class="push"></div>
		</section>

       <?php
        $default_footer = ROOT . DS . 'src' . DS . 'Template' . DS . 'Element' . DS . 'footer.ctp';
        if (file_exists($default_footer)) {
            ob_start();
            include $default_footer;
            echo ob_get_clean();
        } else {
            echo $this->element('footer');
        }
        ?>
    </body>
    </html>

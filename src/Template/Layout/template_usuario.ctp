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
 * @file          src\Template\Layout\template_usuario.ctp
 * @info          Template para perfil do tipo Usuario
 */
use Cake\Core\Configure;

$usuarioLogado = $this->Auth->user();

if (empty($usuarioLogado['nome'])) {
    $cakeDescription = 'GOTAS';
} else {
    $cakeDescription = 'GOTAS - Cliente ' . $usuarioLogado['nome'];
}

?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?= $cakeDescription ?></title>
    <script src="https://maps.googleapis.com/maps/api/js?sensor=true&key=AIzaSyBzwpETAdxu2NQyLLtw16ndZkldjQ5Zqxg" async defer></script>
    <?php

    echo $this->element("../Layout/librarys");
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
                    // if (isset($project_name)) {
                    //     echo $project_name;
                    // } else {
                    //     echo 'Cake Twitter Bootstrap';
                    // }
                    echo "INÍCIO";
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
                <div>
				<?= $this->Flash->render() ?>
				<?= $this->fetch('content') ?>

				<?= $this->element('modal_container') ?>

                </div>
			</div>

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

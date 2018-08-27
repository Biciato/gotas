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
 */
use Cake\Core\Configure;

$cakeDescription = 'GOTAS';
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>

    <?php if (Configure::read('debug')): ?>
    <!-- Carrega extensões em modo debug   -->

    <!-- jQuery -->
    <?=  $this->Html->script(array('jquery/jquery.js','jqueryui/jquery-ui.js', 'jquery-Mask/jquery.mask.js')) ?>

    <!-- Bootstrap -->

    <?= $this->Html->script('bootstrap/js/bootstrap.js') ?>

    <?= $this->Html->css(array('bootstrap/css/bootstrap.css', 'bootstrap-theme.css'))?>

    <?php else: ?>

    <!-- Carrega extensões em modo production -->

    <!-- jQuery -->
    <?=  $this->Html->script(array('jquery/jquery.min.js','jqueryui/jquery-ui.min.js', 'jquery-Mask/jquery.mask.min.js')) ?>

    <!-- Bootstrap -->

    <?= $this->Html->script('bootstrap/js/bootstrap.js') ?>
    <?= $this->Html->css(array('bootstrap/css/bootstrap.min.css', 'bootstrap-theme.min.css'))?>

    <?php endif; ?>


    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="top-bar expanded" data-topbar role="navigation">
        <ul class="title-area large-3 medium-4 columns">
            <li class="name">
                <h1><a href=""><?= $this->fetch('title') ?></a></h1>
            </li>
        </ul>
        <div class="top-bar-section">
            <ul class="right">
            <li><a target="_blank" href="/Usuarios/Register/">Registrar</a></li>
            <li><a target="_blank" href="/Usuarios/Login/">Logar</a></li>
                <!-- <li><a target="_blank" href="http://book.cakephp.org/3.0/">Documentation</a></li>
                <li><a target="_blank" href="http://api.cakephp.org/3.0/">API</a></li> -->
            </ul>
        </div>
    </nav>
    <?= $this->Flash->render() ?>
    <div class="container clearfix">
        <?= $this->fetch('content') ?>
    </div>
    <footer>

    <?php
        if (Configure::read('debug'))
            echo "debug mode on";
    ?>

    </footer>
</body>
</html>

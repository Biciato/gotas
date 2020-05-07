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
 * @author        Gustavo Souza Gonçalves
 * @date          06/07/2017
 * @projectname   GOTAS
 * @url           www.rtibrindes.com.br
 *
 */

use App\Controller\AppController;
use App\Model\Entity\Usuario;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Exception\NotFoundException;

$titlePage = 'GOTAS';

?>
<!doctype html>
<html>

<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $titlePage ?>
    </title>

    <?= $this->Html->meta('icon'); ?>

    <?php
    $this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

    ?>

    <link href="https://fonts.googleapis.com/css?family=Raleway:500i|Roboto:300,400,700|Roboto+Mono" rel="stylesheet">
</head>

<body class="home">

    <!-- <header class="row">
        <div class="header-image"><?= $this->Html->image('rti-logo.png') ?></div>
        <div class="header-title">
            <h1>Bem vindo ao sistema GOTAS</h1>
        </div>
    </header> -->

    <!-- <div class="row"> -->
    <div class="columns col-sm-12">

        <?php
        // $usuarioLogado = $this->request->session()->read('Auth.User');

        // $usuarioAdministrador = $this->request->session()->read('Usuario.AdministradorLogado');
        // $usuarioAdministrar = $this->request->session()->read('Usuario.Administrar');

        // if ($usuarioAdministrar) {
        //     $usuarioLogado = $usuarioAdministrar;
        // }

        // if (!empty($usuarioLogado)) {
        //     if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
        //         $this->extend('dashboard_desenvolvedor');
        //     } else if ($usuarioLogado['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType'] && $usuarioLogado['tipo_perfil'] <= Configure::read('profileTypes')['AdminLocalProfileType']) {
        //         $this->extend('dashboard_administrador');
        //     } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['ManagerProfileType']) {
        //         $this->extend('dashboard_gerente');

        //     } else if ($usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['WorkerProfileType']) {
        //         $this->extend('dashboard_funcionario');

        //     } else {
        //         $this->extend('dashboard_cliente');
        //     }
        // } else {
        ?>

        <!-- <div class="columns col-sm-6">
            <span class="form-label">Para navegar no sistema, é necessário realizar o login.</span>


        </div> -->

        <?php

        // }
        ?>
</body>

</html>

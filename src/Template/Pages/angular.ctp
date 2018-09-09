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
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @author        Gustavo Souza Gonçalves
 * @since         07/09/2018
 * @project_name  GOTAS
 * @filename      src\Template\Layout\angular.ctp
 * @info          Template Angular
 */
use Cake\Core\Configure;


$titlePage = 'GOTAS';

?>
<!DOCTYPE html>
<html lang="pt-br" ng-app="GotasApp">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title><?= $titlePage ?></title>

        <script src="https://maps.googleapis.com/maps/api/js?sensor=true&key=AIzaSyBzwpETAdxu2NQyLLtw16ndZkldjQ5Zqxg" async defer></script>
        <?php
    ?>
    </head>
    <body>

    </body>
</html>

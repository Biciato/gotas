<?php

/**
 * adicionar_genero_brinde.ctp
 *
 * View para genero_brindes/adicionar_genero_brinde
 *
 * @category View
 * @package App\Template\GeneroBrindes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 31/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

$title = __("Adicionar Gênero de Brinde");

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Gênero de Brindes', ['controler' => 'genero_brinde', "action" => "index"], ['class' => 'active']);
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

// Menu Esquerdo
echo $this->element('../GeneroBrindes/left_menu')

?>
<div class="redes form col-lg-9 col-md-8 columns content">

    <?= $this->Form->create($generoBrinde) ?>

    <!-- Formulário de Gênero de Brindes -->
    <?= $this->element("../GeneroBrindes/form_genero_brindes", ["title" => $title]) ?>

    <?= $this->Form->end() ?>
</div>

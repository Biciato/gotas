<?php

/**
 * adicionar_tipo_brinde_rede.ctp
 *
 * View para genero_brindes/adicionar_tipo_brinde_rede/:id
 *
 * @category View
 * @package App\Template\GeneroBrindes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 31/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

$title = __("Adicionar Tipo de Brinde");

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Escolher Rede para Configurar Tipos de Brindes', array("controller" => "tiposBrindesRedes", "action" => "index"));
$this->Breadcrumbs->add('Tipos de Brindes da Rede', array("controller" => "tiposBrindesRedes", "action" => "configurar_tipos_brindes_rede", $rede["id"]));
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

// Menu Esquerdo
echo $this->element('../TiposBrindesRedes/left_menu', array(
    "redesId" => $rede["id"]
))

?>
<div class="redes form col-lg-9 col-md-8 columns content">

    <?= $this->Form->create($tipoBrinde) ?>

    <!-- Formulário de Gênero de Brindes -->
    <?= $this->element("../TiposBrindesRedes/form_tipos_brindes_rede", ["title" => $title]) ?>

    <?= $this->Form->end() ?>
</div>

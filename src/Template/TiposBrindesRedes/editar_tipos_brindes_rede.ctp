<?php

/**
 * editar_tipos_brindes_rede.ctp
 *
 * View para tipos_brindes_redes/editar_tipos_brindes_rede
 *
 * @category View
 * @package App\Template\TiposBrindesRedes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 31/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

$title = __("Editar Tipo de Brinde");

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Escolher Rede para Configurar Tipos de Brindes', array("controller" => "tiposBrindesRedes", "action" => "index"));
$this->Breadcrumbs->add('Tipos de Brindes da Rede', array("controller" => "tiposBrindesRedes", "action" => "configurar_tipos_brindes_rede", $tiposBrindesRede["rede"]["id"]));
$this->Breadcrumbs->add($title, [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

// Menu Esquerdo
echo $this->element('../TiposBrindesRedes/left_menu')

?>
<div class="redes form col-lg-9 col-md-8 columns content">

    <?= $this->Form->create($tiposBrindesRede) ?>

    <!-- Formulário de Tipo de Brindes -->
    <?= $this->element(
        "../TiposBrindesRedes/form_tipos_brindes_rede",
        array(
            "title" => $title,
            "tipoBrinde" => $tiposBrindesRede
        )
    ) ?>

    <?= $this->Form->end() ?>
</div>

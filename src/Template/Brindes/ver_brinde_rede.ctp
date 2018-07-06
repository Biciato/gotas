<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Brindes/ver_brinde_rede.ctp
 * @date     09/08/2017
 */


use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Brindes da Minha Rede', ['controller' => 'brindes', 'action' => 'brindes_minha_rede']);

$this->Breadcrumbs->add('Detalhes de Brinde', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


?>
<?= $this->element('../Brindes/left_menu', ['clientes_id' => $brinde->clientes_id, 'mode' => 'view']) ?>
<div class="brindes view col-lg-9 col-md-8">
    <h3><?= h($brinde->nome) ?></h3>
    <table class="table table-striped table-hover">
        <tr>
            <th>Nome</th>
            <td><?= h($brinde->nome) ?></td>
        </tr>

        <tr>
            <th>Estoque Ilimitado</th>
            <td><?= !empty($brinde) ? $this->Boolean->convertBooleanToString($brinde->ilimitado) : null; ?></td>
        </tr>
        <tr>
            <th>Preco (em gotas):</th>
            <td><?= $this->Number->precision($brinde->preco_padrao, 2) ?></td>
        </tr>

        <?php if ($brinde->equipamento_rti_shower) : ?>

        <tr>
            <th>Equipamento Smart Shower?</th>
            <td><?= $this->Boolean->convertBooleanToString($brinde->equipamento_rti_shower) ?></td>
        </tr>

        <tr>
            <th>Tempo de banho</th>
            <td><?= h(__("{0} minutos", $brinde->tempo_rti_shower)) ?></td>
        </tr>
        <?php endif; ?>

        <tr>
            <th>Imagem do Brinde</th>

            <td><img src="<?= strlen($brinde->nome_img) > 0 ? $brinde->nome_img : '' ?>" alt="<?= strlen($brinde->nome_img) > 0 ? "Imagem do Brinde" : '' ?>" class="imagem-brinde"></td>
        </tr>

    </table>
</div>

<?php
if (Configure::read("debug")) {
    echo $this->Html->css("styles/brindes/ver_brinde_rede");
} else {
    echo $this->Html->css("styles/brindes/ver_brinde_rede");
}

echo $this->fetch("css");

?>

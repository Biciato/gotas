<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/atribuir_gotas_form.ctp
 * @date     06/08/2017
 *
 * Arquivo para atribuir gotas de cliente na view de funcionário
 */

use Cake\Core\Configure;
use Cake\View\Helper\NumberHelper;


$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Atribuição de Gotas', [], ['class' => 'active']);
echo $this->Breadcrumbs->render(['class' => 'breadcrumb']);

?>

<?= $this->element('../Pages/left_menu', ['item_selected' => 'atribuir_gotas']) ?>

<div class="col-lg-9 col-md-8">

<legend>Fechamento de Caixa</legend>

    <?php foreach ($cuponsFuncionariosRetorno as $key => $cupom) : ?>
        <p>
            <h4>Funcionário: <?= $cupom["funcionarioNome"] ?></h4>
            <h5>Periodo: <?= sprintf("%s %s às %s", "De:", $cupom["dataInicio"], $cupom["dataFim"]) ?></h5>

            <h6>Brinde: <?= $cupom["nomeBrinde"]?></h6>

                <li>Total Resgatados: <?= $cupom["totalResgatados"]?> </li>
                <li>Total Usados: <?= $cupom["totalUsados"]?> </li>
                <li>Total Gotas: <?= $cupom["totalGotas"]?> </li>
                <li>Total Dinheiro: <?= $this->Number->currency($cupom["totalDinheiro"])?> </li>
                <li>Total Brindes: <?= $cupom["totalBrindes"]?> </li>
                <li>Total Compras: <?= $cupom["totalCompras"]?> </li>
        </p>

    <?php endforeach; ?>

</div>

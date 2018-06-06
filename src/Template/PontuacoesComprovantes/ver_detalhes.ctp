<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/PontuacoesComprovantes/historico_pontuacoes.ctp
 * @date     08/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Meu Histórico de Pontuações', ['controller' => 'pontuacoes_comprovantes', 'action' => 'historico_pontuacoes']);

$this->Breadcrumbs->add('Detalhes da Pontuação', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>

<?= $this->element('../PontuacoesComprovantes/left_menu') ?> 

<div class="redes form col-lg-9 col-md-8 columns content">
    <legend>Detalhes da Pontuação</legend>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <tr>
            <th scope="col"><?= __('Ponto de Atendimento') ?></th>
            <td><?= h($pontuacao_comprovante->cliente->nome_fantasia) ?></td>
        </tr>
        <tr>
            <th scope="col"><?= __('Chave da Nota Fiscal Eletrônica') ?></th>
            <td><?= h($pontuacao_comprovante->chave_nfe) ?></td>
        </tr>
        <tr>
        <th scope="col"><?= __('Data de Processamento') ?></th>
        <td><?= $pontuacao_comprovante->data->format('d/m/Y H:i:s') ?></td>
        </tr>
    </table>

    <h4>Descrição</h4>
    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('quantidade_multiplicador', ['label' => 'Qte. Abastecida']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('quantidade_gotas', ['label' => 'Qte. Gotas Adquiridas']) ?></th>
                <th scope="col"><?= $this->Paginator->sort('expirado', ['label' => 'Pontos Expirados?']) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pontuacao_comprovante->pontuacoes as $key => $pontuacao) : ?> 
            <tr>
                <td>
                    <?= h($pontuacao->quantidade_multiplicador) ?>
                </td>
                <td>
                    <?= h($pontuacao->quantidade_gotas) ?>
                </td>
                <td>
                    <?= $this->Boolean->convertBooleanToString($pontuacao->expirado) ?>
                </td>
                
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
<div/>
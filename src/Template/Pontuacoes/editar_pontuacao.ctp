<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Pontuacoes/editar_pontuacao.ctp
 * @date     19/10/2017
 */
use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Relatório de Cupons Processados', array("controller" => "pontuacoes", "action" => "relatorio_cupons_processados"));
$this->Breadcrumbs->add('Detalhes do Cupom Fiscal', array("controller" => "pontuacoes", "action" => "detalhes_cupom" , $pontuacao["pontuacoes_comprovante_id"]), ['class' => 'active']);
$this->Breadcrumbs->add('Editar Valor de Pontuação', array(), ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
    <?= $this->element('../Pontuacoes/left_menu', ['controller' => 'pontuacoes', 'action' => 'relatorio_cupons_processados','id' => $pontuacao->id, 'mode' => 'details']) ?>

        <div class="col-lg-9 col-md-10 columns">

            <legend>
                <?= __("Editar Valor de Pontuação")?>
            </legend>

             <table class="table table-striped table-hover">
                <tr>
                    <th>
                        <?= __('Quantidade de Litros Abastecidos') ?>
                    </th>
                    <td>
                        <?= h($this->Number->precision($pontuacao->quantidade_multiplicador, 3)) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?= __('Quantidade de Gotas') ?>
                    </th>
                    <td>
                        <?= h($this->Number->precision($pontuacao->quantidade_gotas, 3)) ?>
                    </td>
                </tr>

                <!-- <tr>
                    <th>
                        <?= __('Valor do Parâmetro de Gotas do Registro') ?>
                    </th>
                    <td>
                        <?= h($this->Number->precision($pontuacao->quantidade_gotas / $pontuacao->quantidade_multiplicador, 2)) ?>
                    </td>
                </tr> -->

                <tr>
                    <th>
                        <?= __('Chave da NFE') ?>
                    </th>
                    <td>
                        <?= h($pontuacao->pontuacoes_comprovante->chave_nfe) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        <?= __('Data de Impressão ') ?>
                    </th>
                    <td>
                        <?= h($pontuacao->data->format('d/m/Y H:i:s')) ?>
                    </td>

                </tr>
            </table>

            <?= $this->Form->create($pontuacao) ?>

            <?= $this->Form->control('quantidade_multiplicador',
                [
                    'type' => 'text',
                    'id' => 'quantidade_multiplicador',
                    'label' => 'Quantidade de Litros Abastecidos'
                ]
            ) ?>


            <?= $this->Form->button(__("{0} Salvar",
                $this->Html->tag('i', '', ['class' => 'fa fa-check'])),
                [
                    'class' => 'btn btn-primary',
                    'type' => 'submit'
                ]
            ) ?>


            <?= $this->Form->end(); ?>
        </div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/pontuacoes/editar_pontuacao') ?>
<?php else: ?>
    <?= $this->Html->script('scripts/pontuacoes/editar_pontuacao.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>

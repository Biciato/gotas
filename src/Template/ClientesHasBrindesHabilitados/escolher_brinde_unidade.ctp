<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/ClientesHasBrindesHabilitados/escolher_brinde_unidade.ctp
 * @date     26/01/2018
 */


use Cake\Routing\Router;
use Cake\Core\Configure;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Unidades da Rede', ['controller' => 'redes', 'action' => 'escolher_unidade_rede', $redesId]);

$this->Breadcrumbs->add('Escolha um Brinde para Resgatar', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']

);

?>

<?= $this->element('../ClientesHasBrindesHabilitados/left_menu', []) ?>

<div class="redes index col-lg-9 col-md-10 columns content">
    <legend>Escolha um Brinde para Resgatar</legend>

    <table class="table table-striped table-hover table-condensed table-responsive">
        <thead>
            <th>
                <?= __("Brinde") ?>
            </th>
            <th>
                <?= __("Valor (em gotas)") ?>
            </th>

            <th>
                <?= __("Ações") ?>
            </th>
        </thead>

        <tbody>

            <?php foreach ($brindes_habilitados as $key => $value) : ?>

            <tr>
                <td>
                    <?= $value->brinde->nome ?>
                </td>
                <td>
                    <?= $value->brinde_habilitado_preco_atual->preco ?>
                </td>
                <td>

                    <?php if ($value->brinde->equipamento_rti_shower) : ?>
                        <?= __("Equipamento Smart Shower é emitido no Posto de Atendimento") ?>
                    <?php else : ?>
                        <?= $this->Html->link(
                            __(
                                "{0} Adquirir",
                                $this->Html->tag('i', '', ['class' => 'fa fa-shopping-cart'])
                            ),
                            [
                                'controller' => 'ClientesHasBrindesHabilitados',
                                'action' => 'resgatar_brinde',
                                $value->id
                            ],
                            [
                                'class' => 'btn btn-primary',
                                'escape' => false
                            ]
                        ) ?>

                    <?php endif; ?>
                </td>
            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>


</div>

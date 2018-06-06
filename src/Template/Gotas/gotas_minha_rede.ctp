<?php

/**
 * @var \App\View\AppView $this
 *
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Gotas/gotas_minha_rede.ctp
 * @date     03/10/2017
 */


use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

$this->Breadcrumbs->add('Cadastro de Gotas de Minha Rede', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
    <?= $this->element('../Gotas/left_menu', ['mode' => 'view', 'go_back_url' => ['controller' => 'pages', 'action' => 'index']]) ?>
        <div class="gotas index col-lg-9 col-md-8 columns content">

        <legend>Cadastro de Gotas de Minha Rede</legend>
        <h4>
            <?= __("Unidade {0}", $cliente) ?>
        </h4>

            <div class="col-lg-12">
                <?= $this->Form->create(
                    'POST',
                    [
                        'url' =>
                            [
                            'controller' => 'Gotas', 'action' => 'gotas_minha_rede'
                        ]
                    ]
                ) ?>
				<?= $this->Form->input(
                    'filtrar_unidade',
                    [
                        'type' => 'select',
                        'id' => 'filtrar_unidade',
                        'label' => "Filtrar por unidade?",
                        'empty' => false,
                        'options' => $unidades_ids
                    ]
                ) ?>
            </div>
            
            <div class="hidden">

            <?= $this->Form->button(
                "Pesquisar",
                [
                    'class' => 'btn btn-primary btn-block',
                    'id' => 'search_button'
                ]
            ) ?>

            <?= $this->Form->end(); ?>
            </div>

                    <table  class="table table-striped table-hover table-responsive table-condensed">
                        <thead>
                            <th>
                                <?= __('Nome da Gota') ?>
                            </th>
                            <th>
                                <?= __('Valor multiplicador') ?>
                            </th>
                            <th>
                                <?= __('Status') ?>
                            </th>
                            <th class="actions">
                                <?= __('Ações') ?>
                                <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                            </th>
                        </thead>
                        <tbody>
                            <?php if (sizeof($gotas) > 0) : ?>
                            <?php foreach ($gotas as $key => $gota) : ?>
                                <tr>
                                    <td>
                                        <?= h($gota->nome_parametro) ?>
                                    </td>
                                   <td>
                                        <?= $this->Number->precision($gota->multiplicador_gota, 2) ?>
                                    </td>
                                    <td>
                                        <?= __($this->Boolean->convertEnabledToString($gota->habilitado))?>
                                    </td>
                                    <td class="actions" style="white-space:nowrap">
                                        <?= $this->Html->link(
                                            __(
                                                '{0}',
                                                $this->Html->tag('i', '', ['class' => 'fa fa-edit'])
                                            ),
                                            [
                                                'action' => 'editar_gota',
                                                $gota->id
                                            ],
                                            [
                                                'class' => 'btn btn-primary btn-xs',
                                                'title' => 'Editar',
                                                'escape' => false
                                            ]
                                        ) ?>

                                       <?php if ($gota->habilitado) : ?> 

                                            <?= $this->Html->link(
                                                __(
                                                    "{0}",
                                                    $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                ),
                                                '#',
                                                [
                                                    'class' => 'btn btn-primary btn-danger btn-xs',
                                                    'title' => 'Desabilitar',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#modal-delete-with-message',
                                                    'data-message' => __(Configure::read('messageDisableQuestion'), $gota->nome_parametro),
                                                    'data-action' => Router::url(
                                                        [
                                                            'controller' => 'gotas',
                                                            'action' => 'desabilitarGota', $gota->id
                                                        ]
                                                    ), 'escape' => false
                                                ],
                                                false
                                            ) ?>
                                        <?php else : ?>
                                            <?= $this->Html->link(
                                                __(
                                                    "{0}",
                                                    $this->Html->tag('i', '', ['class' => 'fa fa-power-off'])
                                                ),
                                                '#',
                                                [
                                                    'class' => 'btn btn-primary btn-primary btn-xs',
                                                    'title' => 'Habilitar',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#modal-confirm-with-message',
                                                    'data-message' => __(Configure::read('messageEnableQuestion'), $gota->nome_parametro),
                                                    'data-action' => Router::url(
                                                        [
                                                            'controller' => 'gotas',
                                                            'action' => 'habilitarGota', $gota->id
                                                        ]
                                                    ), 'escape' => false
                                                ],
                                                false
                                            ) ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan='3'>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <?= h(__("Unidade sem gotas definidas!")) ?>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <?= $this->Html->link(
                                                __(
                                                    "{0} Definir gotas",
                                                    $this->Html->tag('i', '', ['class' => 'fa fa-plus'])
                                                ),
                                                [
                                                    'action' => 'adicionar_gota',
                                                    $cliente->id
                                                ],
                                                [
                                                    'class' => 'btn btn-info',
                                                    'escape' => false
                                                ]
                                            ) ?>
                                        
                                        </div>
                                    </div>
                                    </td>
                                </tr>
                                                    
                            <?php endif; ?>
                            </tbody>
                    </table>
    <div class="paginator">
        <center>
            <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['escape' => false]) ?>
                <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape' => false]) ?>
            <?= $this->Paginator->last(__('último') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/gotas/gotas_minha_rede') ?>
	<?php else : ?> 
    <?= $this->Html->script('scripts/gotas/gotas_minha_rede.min') ?>
<?php endif; ?>

<?= $this->fetch('script') ?>
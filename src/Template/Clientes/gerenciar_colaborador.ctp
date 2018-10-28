<?php 
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/gerenciar_colaborador.ctp
 * @date     27/08/2017
 */

 use Cake\Core\Configure;
 use Cake\Routing\Router;
?>

<?= $this->element('../Clientes/left_menu', ['controller' => 'clientes', 'action' => 'dados_minha_rede', 'usuarioLogado' => $usuarioLogado, 'showAdministratorsNetwork' => false]) ?>

    <div class="clientes index col-lg-9 col-md-10 columns content">

    <legend>
        <?= __("Colaboradores da Unidade [{0}] - Razão Social [{1}]", $cliente->nome_fantasia, $cliente->razao_social) ?>
    </legend>

        <?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'clientes', 'action' => 'gerenciar_colaborador', 'id' => $cliente->id, 'show_filiais' => false]) ?>
        <?php if (sizeof($usuarios->toArray()) > 0): ?>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>
                        <?= $this->Paginator->sort('nome') ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('tipo_perfil') ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('cpf') ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('doc_estrangeiro', ['Label' => 'Documento Estrangeiro']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('email', ['label' => 'E-mail']) ?>
                    </th>
                    <th class="actions">
                        <?= __('Ações') ?>
                        <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td>
                        <?= h($usuario['usuarios']['nome']) ?>
                    </td>
                    <td>
                        <?= h($this->UserUtil->getProfileType((int)$usuario['usuarios']['tipo_perfil']))?>
                    </td>
                    <td>
                        <?= h($this->NumberFormat->formatNumberToCPF($usuario['usuarios']['cpf'])) ?>
                    </td>
                    <td>
                        <?= h(isset($usuario['usuarios']['doc_estrangeiro']) ? $usuario['usuarios']['doc_estrangeiro'] : null )  ?>
                    </td>
                    <td>
                        <?= h($usuario['usuarios']['email']) ?>
                    </td>

                    <td class="actions" style="white-space:nowrap">

                        <?php if ($usuarioLogado['id'] != $usuario['usuarios']['id']): ?>
                        <?php if (is_null($usuario['chu']['id'])){ ?>

                        <?= $this->Html->link(__('{0} Atribuir',
                                $this->Html->tag('i', '', ['class' => 'fa fa-check']) ),
                                '#',
                                [
                                   'class'=>'btn btn-xs btn-primary btn-confirm',
                                   'data-toggle'=> 'modal',
                                    'data-target' => '#modal-confirm-with-message',
                                    'data-message' => 'Atribuir o usuário selecionado para esta unidade?',
                                    'data-action'=> Router::url(
                                        [
                                            'action' => 'gerenciar_colaborador', $cliente->id,
                                            '?' => 
                                            [
	                    	                    'matriz_id' => isset($cliente->matriz_id) ? $cliente->matriz_id : $cliente->id,
	                    	                    'cliente_id' => $cliente->id,
	                    	                    'usuario_id' => $usuario['usuarios']['id'],
                                                'action' => 'add'
	                    	                ]
                                        ]
                                    ),
                                       'escape' => false
                                ],
                            false
                            ); ?>
                      
                            <?php } elseif ($usuario['chu']['clientes_id'] == $cliente['id']) { ?>
                            
                               <?= $this->Html->link(__('{0} Remover',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-trash']) ),
                                    '#',
                                    [
                                    'class'=>'btn btn-xs btn-danger btn-confirm',
                                    'data-toggle'=> 'modal',
                                        'data-target' => '#modal-delete-with-message',
                                        'data-message' => 'Remover o usuário selecionado desta unidade?',
                                        'data-action'=> Router::url(
                                            [
                                                'action' => 'gerenciar_colaborador', $cliente->id,
                                                '?' => 
                                                [
                                                    'matriz_id' => isset($cliente->matriz_id) ? $cliente->matriz_id : $cliente->id,
                                                    'cliente_id' => $cliente->id,
                                                    'usuario_id' => $usuario['usuarios']['id'],
                                                    'action' => 'remove'
                                                ]
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                ); ?>
                            
                            <?php } elseif(!isset($usuario['chu']['clientes_id'])) { ?>

                            <?= $this->Html->link(__('{0} Atribuir',
                                $this->Html->tag('i', '', ['class' => 'fa fa-check']) ),
                                '#',
                                [
                                   'class'=>'btn btn-xs btn-primary btn-confirm',
                                   'data-toggle'=> 'modal',
                                    'data-target' => '#modal-confirm-with-message',
                                    'data-message' => 'Atribuir o usuário selecionado para esta unidade?',
                                    'data-action'=> Router::url(
                                        [
                                            'action' => 'gerenciar_colaborador', $cliente->id,
                                            '?' => 
                                            [
	                    	                    'matriz_id' => isset($cliente->matriz_id) ? $cliente->matriz_id : $cliente->id,
	                    	                    'cliente_id' => $cliente->id,
	                    	                    'usuario_id' => $usuario['usuarios']['id'],
                                                'action' => 'add'
	                    	                ]
                                        ]
                                    ),
                                       'escape' => false
                                ],
                            false
                            ); ?>
                                <?php } else { ?>
                                <?= __("Já alocado em outra unidade!") ?>
                                    <?php if ((($usuarioLogado['tipo_perfil'] <= 1) && ($usuarioLogado['matriz_id'] != $cliente->matriz_id)) || ($usuarioLogado['matriz_id'] == $cliente->matriz_id)){ 
                            echo "<br />";
                            ?>

                                <?= $this->Html->link(__('{0} Remover vínculo de outra unidade',
                                    $this->Html->tag('i', '', ['class' => 'fa fa-trash']) ),
                                    '#',
                                    [
                                    'class'=>'btn btn-xs btn-danger btn-confirm',
                                    'data-toggle'=> 'modal',
                                        'data-target' => '#modal-delete-with-message',
                                        'data-message' => 'Remover o usuário selecionado desta unidade?',
                                        'data-action'=> Router::url(
                                            [
                                               'action' => 'gerenciar_colaborador',
                                                $cliente->id,
                                                '?' => [
                                                    'matriz_id' => isset($cliente->matriz_id) ? $cliente->matriz_id : $cliente->id,
                                                    'cliente_id' => $usuario['chu']['clientes_id'],
                                                    'usuario_id' => $usuario['usuarios']['id'],
                                                    'action' => 'remove'
                                                ]
                                            ]
                                        ),
                                        'escape' => false
                                    ],
                                    false
                                ); ?>
                                <?php }} ?>
                    </td>
                </tr>
                <?php endif; endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
            <center>
                <ul class="pagination">
                    <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                    <?= $this->Paginator->prev('&laquo; ' . __('anterior'), ['escape'=>false]) ?>
                    <?= $this->Paginator->numbers(['escape'=>false]) ?>
                    <?= $this->Paginator->next(__('próximo') . ' &raquo;', ['escape'=>false]) ?>
                    <?= $this->Paginator->last(__('último') . ' >>') ?>
                </ul>
                <p>
                    <?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?>
                </p>
            </center>
        </div>

        <?php endif; ?>

    </div>

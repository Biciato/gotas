<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/lista_administradores.ctp
 * @date     27/08/2017
 */

 use Cake\Routing\Router;
 use Cake\Core\Configure;

$action = $user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType'] ? 'index' : 'dados_minha_rede';

?>
<?= $this->element('../Clientes/left_menu', ['controller' => 'clientes', 'action' => $action, 'user_logged' => $user_logged, 'showAdministratorsNetwork' => true]) ?>

<div class="clientes index col-lg-9 col-md-10 columns content">

    <legend>
        <?= __("Administradores da Unidade [{0}] - Razão Social [{1}]", $cliente->nome_fantasia, $cliente->razao_social) ?>
    </legend>

    <?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'clientes', 'action' => 'lista_administradores', 'id' => $cliente->id]) ?>

    <?php if (sizeof($usuarios->toArray()) > 0) : ?>
         <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>
                        <?= $this->Paginator->sort('nome') ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('cpf', ['label' => 'CPF']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('doc_estrangeiro', ['label' => 'Documento Exterior']) ?>
                    </th>
                    <th>
                        <?= $this->Paginator->sort('email') ?>
                    </th>
                    <th>
                        <?= $this->Form->label('Ações') ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $key => $usuario) : ?>
                    <tr>
                        <td>
                            <?= $usuario->nome ?>
                        </td>
                        <td>
                            <?= $this->NumberFormat->formatNumberToCPF($usuario->cpf) ?>
                        </td>
                        <td>
                            <?= $usuario->doc_estrangeiro ?>
                        </td>
                        <td>
                            <?= $usuario->email ?>
                        </td>
                        <td>
                            <?= $this->Html->link(__('{0} Remover Atribuição',
                                $this->Html->tag('i', '', ['class' => 'fa fa-trash']) ),
                                '#',
                                [
                                   'class'=>'btn btn-xs btn-danger btn-confirm',
                                   'data-toggle'=> 'modal',
                                    'data-target' => '#modal-delete-with-message',
                                    'data-message' => 'Deseja remover o usuário selecionado como Administrador?',
                                    'data-action'=> Router::url(
                                        [
                                            'action' => 'desatribuir_administrador',
                                            '?' =>
                                            [
                                                'matriz_id' => isset($cliente->matriz_id) ?         $cliente->matriz_id : $cliente->id,
                                                'cliente_id' => $cliente->id,
                                                'usuario_id' => $usuario->id
                                            ]
                                        ]
                                    ),
                                       'escape' => false
                                ],
                            false
                            ); ?>
                                
                        </td>
                    </tr>
                <?php endforeach; ?>
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
    <?php else : ?>

    <span class="text-center">A pesquisa não retornou resultados</span>

    <?php endif; ?>
</div>

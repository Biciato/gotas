<?php
/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/Clientes/atribuir_administrador.ctp
 * @date     27/08/2017
 */

 use Cake\Core\Configure;
 use Cake\Routing\Router;

$action = $usuarioLogado['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType'] ? 'view'.'/'. $cliente->id : 'dados_minha_rede';

?>
<?= $this->element('../Clientes/left_menu', ['controller' => 'clientes', 'action' => $action, 'usuarioLogado' => $usuarioLogado]) ?>

<div class="clientes index col-lg-9 col-md-10 columns content">

<legend><?= __('Atribuir Administrador à Rede: {0} ({1})', $cliente->nome_fantasia, $cliente->razao_social) ?></legend>

<?= $this->element('../Usuarios/filtro_usuarios', ['controller' => 'clientes' , 'action' => 'atribuir_administrador', 'id' => $cliente->id, 'show_filiais' => false]) ?>

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
                <th class="actions">
                    <?= __('Ações') ?>
                    <div class="btn btn-xs btn-default right-align call-modal-how-it-works" data-toggle="modal" data-target="#modalLegendIconsSave" target-id="#legenda-icones-acoes" ><span class=" fa fa-book"> Legendas</span></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (sizeof($usuarios->toArray())>0) : ?>
            <?php foreach ($usuarios as $usuario) : ?>
            <tr>
                
                <td>
                    <?= h($usuario->nome) ?>
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

                <td class="actions" style="white-space:nowrap">

                <?= $this->Html->link(__('{0} Confirmar Atribuição',
                            $this->Html->tag('i', '', ['class' => 'fa fa-check']) ),
                          '#',
                          array(
                             'class'=>'btn btn-xs btn-primary btn-confirm',
                             'data-toggle'=> 'modal',
                             'data-target' => '#modal-confirm-with-message',
                             'data-message' => 'Deseja atribuir o usuário selecionado como Administrador?',
                             'data-action'=> Router::url(
                                [
                                    'action' => 'atribuir_administrador',   $cliente->id,
									'?' =>
									[
										'matriz_id' => isset($cliente->matriz_id) ? $cliente->matriz_id : $cliente->id,
										'cliente_id' => $cliente->id,
										'usuario_id' => $usuario->id
									]
                                ]
                             ),
                             'escape' => false),
                        false
                            ); ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else : ?>
            <tr>
            <td colspan="4" class="text-center">
                <?= $this->Html->tag('span', 'A pesquisa não retornou Administradores pendentes para vincular', []) ?>
            </td>
            </tr>
            <?php endif;?>
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
            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registros de  {{count}} total, iniciando no registro {{start}}, terminando em {{end}}')) ?></p>
         </center>
    </div>

</div>

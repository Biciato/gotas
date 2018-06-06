<?php

/**
 * @var \App\View\AppView $this
 */

use Cake\Core\Configure;
use Cake\Routing\Router;


$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'index']);

} else if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminDeveloperProfileType']
    && $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {
    $this->Breadcrumbs->add('Usuários', ['controller' => 'usuarios', 'action' => 'usuarios_rede', $rede->id]);
}

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

$update_password = false;

if ($user_logged['tipo_perfil'] == 0) {
    $controller = 'usuarios';
    $action = 'index';
    $update_password = true;
} else if ($user_logged['tipo_perfil'] >= 1 && $user_logged['tipo_perfil'] <= 3) {
    $controller = 'usuarios';
    $action = 'minha_equipe';
    $update_password = true;
} else {
    $controller = 'pages';
    $action = 'display';
}

?>
<?= $this->element(
    '../Usuarios/left_menu',
    [
        'controller' => $controller,
        'action' => $action,
        'mode' => 'back',
        'update_password' => $update_password
    ]
) ?>
<div class="usuarios view col-lg-9 col-md-10">
    <?= $this->Form->create($usuario) ?>
    <fieldset>
        <legend><?= __('Editar dados de {0}', $usuario->nome) ?></legend>
        
        <?= $this->Form->hidden('id', ['id' => 'usuarios_id']); ?>
        <?= $this->Form->hidden('usuario_logado_tipo_perfil', ['value' => $usuario_logado_tipo_perfil, 'class' => 'usuario_logado_tipo_perfil']); ?>

        <?= $this->Form->hidden(
            'clientes_id',
            [
                'id' => 'clientes_id',
                'value' => isset($cliente) ? $cliente->id : null
            ]
        ); ?>
        
        <?php if (isset($user_logged) && $user_logged['tipo_perfil'] == 0) {
            ?> 
            <div class='col-lg-4'>
                <?php
                if ($usuario->tipo_perfil == Configure::read('profileTypes')['AdminRegionalProfileType']) {

                    echo $this->Form->hidden('tipo_perfil');
                    echo $this->Form->input('tipo_perfil_texto', [
                        'type' => 'text',
                        'label' => 'Tipo de Perfil',
                        'value' => 'Administrador Regional',
                        'readonly' => true
                    ]);
                } else {


                    echo $this->Form->input('tipo_perfil', [
                        'type' => 'select',
                        'options' =>
                            [
                            '' => '',
                            '0' => 'Administradores da RTI / Desenvolvedor',
                            '1' => 'Administradores de uma Rede',
                            '3' => 'Administrador',
                            '4' => 'Gerente',
                            '5' => 'Funcionário',
                            '6' => 'Cliente Final',
                        ]
                    ]);
                }
                

                ?> 
                </div>
                <?php 
                // verifica primeiro se usuário é regional, ele não pode ser realocado se for

                if ($usuario->tipo_perfil == Configure::read('profileTypes')['AdminRegionalProfileType']) {
                    ?>

                    <div class="col-lg-6">
                        <?= $this->Html->tag('span', 'Usuário é Administrador Regional, não pode ser realocado entre unidades!', ['class' => 'text-danger']) ?>
                    </div>

                    <?php 
                } else {

                    ?>

                <div class='col-lg-3 redes_input'>
                    <?php 

                    if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
                        echo $this->Form->input(
                            'redes_id',
                            [
                                'type' => 'select',
                                'id' => 'redes_id',
                                'class' => 'redes_list',
                                'options' => $redes,
                                'value' => isset($rede) ? $rede->id : null,
                                'multiple' => false,
                                'empty' => true,
                                'label' => 'Rede de destino'
                            ]
                        );

                    } else if ($user_logged['tipo_perfil'] >= Configure::read('profileTypes')['AdminNetworkProfileType']
                        && $user_logged['tipo_perfil'] <= Configure::read('profileTypes')['ManagerProfileType']) {

                        echo $this->Form->input(
                            'redes_id',
                            [
                                'type' => 'text',
                                'readonly' => true,
                                'value' => $redes->toArray(),
                                'label' => 'Alocado na rede'
                            ]
                        );

                    }



                    ?>
                </div>
                
                <?php 
            } ?>
                <div class='col-lg-4 redes_input'>
                <?= $this->Form->input(
                    'clientes_id',
                    [
                        'type' => 'select',
                        'id' => 'clientes_rede',
                        'class' => 'clientes_rede',
                        'label' => 'Unidade da Rede'
                    ]
                )
                ?> 
                </div>
            <?php

        } else if ($user_logged['id'] == $usuario['id']) {

            // se o usuário está se editando, ele não pode mudar o perfil à qual ele se encontra 

            echo $this->Form->hidden('tipo_perfil');
            echo $this->Form->input('tipo_perfil_texto', [
                'type' => 'text',
                'label' => 'Tipo de Perfil',
                'value' => Configure::read('profileTypesTranslated')[$usuario['tipo_perfil']],
                'readonly' => true
            ]);



        } else {
            if ($user_logged['tipo_perfil'] == 1) {
                echo $this->Form->input('tipo_perfil', [
                    'type' => 'select',
                    'options' =>
                        [
                        '' => '',
                        '1' => 'Administradores de uma Rede',
                        '2' => 'Administrador',
                        '3' => 'Gerente',
                        '4' => 'Funcionário',
                        '5' => 'Cliente Final',
                    ]
                ]);
            } elseif ($user_logged['tipo_perfil'] == 2) {
                echo $this->Form->input('tipo_perfil', [
                    'type' => 'select',
                    'options' =>
                        [
                        '' => '',
                        '2' => 'Administrador',
                        '3' => 'Gerente',
                        '4' => 'Funcionário',
                        '5' => 'Cliente Final',
                    ]
                ]);
            } elseif ($user_logged['tipo_perfil'] == 3) {
                echo $this->Form->input('tipo_perfil', [
                    'type' => 'select',
                    'options' =>
                        [
                        '' => '',
                        '4' => 'Funcionário',
                        '5' => 'Cliente Final'
                    ]
                ]);
            } elseif ($user_logged['tipo_perfil'] == 3 || is_null($user_logged)) {
                echo $this->Form->hidden('tipo_perfil', ['value' => '5']);
            }
        }

        ?>

        <div class="col-lg-12">

            <?= $this->Form->input('estrangeiro', ['type' => 'checkbox', 'id' => 'alternarEstrangeiro', 'label' => 'Selecione se o usuário for estrangeiro']) ?>

            <div id="doc_estrangeiro_box">

                <?= $this->Form->input('doc_estrangeiro', ['id' => 'doc_estrangeiro', 'label' => 'Documento de Identificação Estrangeira']) ?>
                
                <span id="doc_estrangeiro_validation" class="text-danger validation-message"></span>

            </div>

        </div>

        <div class="col-lg-12">
            <span id="cpf_validation" class="text-danger validation-message"></span>
        </div>
        <div class="form-group col-lg-6" id="cpf_box">
            <?php
            echo $this->Form->input('cpf', ['label' => 'CPF']);
            ?>
            
        </div>

        <div class="form-group col-lg-6">
            <?= $this->Form->input('email'); ?>
                <span id="email_validation" class="text-danger validation-message">
        </div>


        <div class="group-video-capture col-lg-12">

            <div class="col-lg-5">
                <div>
                    <span>Captura de Imagem</span>
                </div>
                <video id="video" autoplay="true" height="300"></video>

                <div class="video-snapshot">
                    <div class="btn btn-primary" id="takeSnapshot">Tirar Foto</div>
                </div>
            </div>

            <div class="col-lg-5">
                <div>
                    <span>Foto Capturada</span>
                </div>
                <div class="video-captured">
                    <canvas width="400" height="300" id="canvas"></canvas>
                </div>

                <div class="video-confirm">
                    <div class="btn btn-primary" id="storeImage">Armazenar</div>
                </div>

            </div>

        </div>

        <?= $this->Form->hidden('doc_invalido', ['id' => 'doc_invalido']) ?>

        <div class="col-lg-5">

            <?= $this->Form->control('nome'); ?>
        </div>

        <div class="col-lg-3">
            <?= $this->Form->input('sexo', [
                'options' =>
                    [
                    '' => '',
                    '1' => 'Masculino',
                    '0' => 'Feminino'
                ]
            ]); ?>
        </div>

            

        <div class="col-lg-4">
                <?= $this->Form->input(
                    'data_nasc',
                    [
                        'class' => 'datepicker-input',
                        'div' =>
                            [
                            'class' => 'form-inline',
                        ],
                        'type' => 'text',
                        'id' => 'data_nasc',
                        'format' => 'd/m/Y',
                        'default' => date('d/m/Y'),
                        'value' => date('d/m/Y'),
                        'label' => 'Data de Nascimento'
                    ]
                ); ?>
        </div>

        <div class="row col-lg-12">

            <div class="col-lg-3">
                
                <?= $this->Form->input('necessidades_especiais', ['label' => 'Portador de Nec. Especiais? ', 'options' => [
                    '' => '',
                    1 => 'Sim',
                    0 => 'Não',
                ]]) ?>
        </div>
        
        <div class="col-lg-3">
            <?= $this->Form->control('telefone'); ?>
        </div>
        
        <div class="col-lg-6">
            <div></div>
            
        </div>
        </div>


        <div class="col-lg-2">
            <?= $this->Form->input(
                'cep',
                [
                    'label' => 'CEP*',
                    'id' => 'cep',
                    'class' => 'cep',
                    'title' => 'CEP do local do cliente. Digite-o para realizar a busca.'
                ]
            );
            ?>
        </div>

        <div class="col-lg-3">
            <?= $this->Form->control('endereco', ['label' => 'Endereço', 'class' => 'endereco']); ?>
        </div>

        <div class="col-lg-2">
            <?= $this->Form->control('endereco_numero', ['label' => 'Número', 'class' => 'numero']); ?>
        </div>
        <div class="col-lg-2">
            <?= $this->Form->control('endereco_complemento', ['label' => 'Complemento', 'class' => 'complemento']); ?>
        </div>
        
        <div class="col-lg-3">

            <?= $this->Form->control('bairro', ['class' => 'bairro']); ?>
        </div>

        <div class="col-lg-4">

            <?= $this->Form->control('municipio', ['class' => 'municipio']); ?>
        </div>

        <div class="col-lg-4">
            <?= $this->Form->input(
                'estado',
                [
                    'empty' => true,
                    'type' => 'select',
                    'options' => $this->Address->getStatesBrazil(),
                    'class' => 'estado'
                ]
            ); ?>

        </div>

        <div class="col-lg-4">
            <?= $this->Form->control('pais', ['class' => 'pais']); ?>
        </div>
    </fieldset>
    <div class="col-lg-12">

        <?= $this->Form->button(
            __(
                '{0} Salvar',
                $this->Html->tag('i', '', ['class' => 'fa fa-save'])
            ),
            [
                'id' => 'user_submit',
                'escape' => false
            ]
        ) ?>
        <?= $this->Form->end() ?>
    </div>
</div>

<?php if (Configure::read('debug') == true) : ?>
    <?= $this->Html->script('scripts/usuarios/edit'); ?>
<?php else : ?> 
    <?= $this->Html->script('scripts/usuarios/edit.min'); ?>
<?php endif; ?>

<?= $this->fetch('script'); ?>

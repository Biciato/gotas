
<?php

/**
 * @author   Gustavo Souza Gonçalves
 * @file     src/Template/clientes/dados_cliente_atendimento_usuario.ctp
 * @date     11/01/2018
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);


if ($user_logged['tipo_perfil'] <= (int)Configure::read('profileTypes')['WorkerProfileType']) {
    $this->Breadcrumbs->add('Pontuações do Usuário', ['controller' => 'pontuacoes_comprovantes', 'action' => 'historico_pontuacoes', $usuarios_id]);
} else {
    $this->Breadcrumbs->add('Meu Histórico de Pontuações', ['controller' => 'pontuacoes_comprovantes', 'action' => 'historico_pontuacoes']);
}

$this->Breadcrumbs->add('Detalhes do Ponto de Atendimento', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);


$controller_voltar = null;
$action_voltar = null;

if ($user_logged['tipo_perfil'] == Configure::read('profileTypes')['AdminDeveloperProfileType']) {
    $controller_voltar = 'redes';
    $action_voltar = 'ver_detalhes';
    $id_voltar = $cliente->rede_has_cliente->redes_id;
} else {
    $controller_voltar = 'pages';
    $action_voltar = 'display';
}

?>

<?= $this->element(
    '../Clientes/left_menu',
    [
        'view' => true
    ]
) ?>
<div class="clientes view col-lg-9 col-md-10">
    <legend>
        <?= h(__("{0} - {1}", $cliente->rede_has_cliente->rede->nome_rede, $cliente->nome_fantasia)) ?>
    </legend>

    <h4>Dados Cadastrais</h4>
    <table class="table table-striped table-condensed table-responsive">
        <tr>
            <th>Nome da Rede</th>
            <td>
                <?= h($cliente->rede_has_cliente->rede->nome_rede) ?>
            </td>
            <th>Nome Fantasia</th>
            <td>
                <?= h($cliente->nome_fantasia) ?>
            </td>
        </tr>
        <tr>
            <th>Razao Social</th>
            <td>
                <?= h($cliente->razao_social) ?>
            </td>
            <th>CNPJ</th>
            <td>
                <?= h($this->NumberFormat->formatNumberToCNPJ($cliente->cnpj)) ?>
            </td>
        </tr>
    </table>

    <h4>Endereço:</h4>
    
    <table class="table table-striped table-condensed table-responsive">
        <tr>
            <th>Endereco</th>
            <td>
                <?= h($cliente->endereco) ?>
            </td>
            <th>Número</th>
            <td>
                <?= $cliente->endereco_numero ?>
            </td>
            <th>Complemento</th>
            <td>
                <?= h($cliente->endereco_complemento) ?>
            </td>
            <th>Bairro</th>
            <td>
                <?= h($cliente->bairro) ?>
            </td>
        </tr>
        
        <tr>
            <th>Municipio</th>
            <td>
                <?= h($cliente->municipio) ?>
            </td>
            <th>Estado</th>
            <td>
                <?= h($cliente->estado) ?>
            </td>
            <th>País</th>
            <td>
                <?= h($cliente->pais) ?>
            </td>
            <th>CEP</th>
            <td>
                <?= h($this->Address->formatCEP($cliente->cep)) ?>
            </td>
        </tr>

    </table>

    <h4>Contato:</h4>
    <table class="table table-striped table-condensed table-responsive">
        <tr>
            <th>Telefone Fixo</th>
            <td>
                <?= h($this->Phone->formatPhone($cliente->tel_fixo)) ?>
            </td>
            <th>Telefone Fax</th>
            <td>
                <?= h($this->Phone->formatPhone($cliente->tel_fax)) ?>
            </td>
            <th>Telefone Celular</th>
            <td>
                <?= h($this->Phone->formatPhone($cliente->tel_celular)) ?>
            </td>
        </tr>
        
    </table>
    
</div>

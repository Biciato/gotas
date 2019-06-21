<table class="table table-striped table-hover">
    <tr>
        <th scope="row"><?= __('Código') ?></th>
        <td><?= $this->Number->format($tiposBrindesRede->id) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Nome') ?></th>
        <td><?= h($tiposBrindesRede->nome) ?></td>
    </tr>

    <tr>
        <th scope="row"><?= __('Cadastrado para Rede:') ?></th>
        <td><?= h($tiposBrindesRede["rede"]["nome_rede"]) ?></td>
    </tr>

    <tr>
        <th scope="row"><?= __('Equipamento RTI') ?></th>
        <td><?= $this->Boolean->convertEquipamentoRTIBooleanToString($tiposBrindesRede->equipamento_rti); ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Habilitado') ?></th>
        <td><?= $this->Boolean->convertEnabledToString($tiposBrindesRede->habilitado) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Atribuir Automatico') ?></th>
        <td><?= $this->Boolean->convertBooleanToString($tiposBrindesRede->atribuir_automatico) ?></td>
    </tr>
    <?php if ($tiposBrindesRede["equipamento_rti"]): ?>
        <tr>
            <th scope="row"><?= __('Código Primário de Equipamento RTI') ?></th>
            <td><?= h($tiposBrindesRede->tipo_principal_codigo_brinde_default) ?></td>
        </tr>
        <tr>
        <th scope="row"><?= __('Código Secundário de Equipamento RTI') ?></th>
            <td><?= h($tiposBrindesRede->tipo_secundario_codigo_brinde_default) ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <th scope="row"><?= __('Data de Criação') ?></th>
        <td><?= h(!empty($tiposBrindesRede->audit_insert) ? $tiposBrindesRede->audit_insert->format('d/m/Y H:i:s') : null) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Data da Última Alteração') ?></th>
        <td><?= h(!empty($tiposBrindesRede->audit_update) ? $tiposBrindesRede->audit_update->format('d/m/Y H:i:s') : null) ?></td>
    </tr>
</table>
<!-- <div class="related">
    <h4><?= __('Related Clientes') ?></h4>
    <?php if (!empty($tiposBrindesRede->clientes)) : ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th scope="col"><?= __('Id') ?></th>
            <th scope="col"><?= __('Matriz') ?></th>
            <th scope="col"><?= __('Ativado') ?></th>
            <th scope="col"><?= __('Tipo Unidade') ?></th>
            <th scope="col"><?= __('Codigo Rti Shower') ?></th>
            <th scope="col"><?= __('Nome Fantasia') ?></th>
            <th scope="col"><?= __('Razao Social') ?></th>
            <th scope="col"><?= __('Cnpj') ?></th>
            <th scope="col"><?= __('Endereco') ?></th>
            <th scope="col"><?= __('Endereco Numero') ?></th>
            <th scope="col"><?= __('Endereco Complemento') ?></th>
            <th scope="col"><?= __('Bairro') ?></th>
            <th scope="col"><?= __('Municipio') ?></th>
            <th scope="col"><?= __('Estado') ?></th>
            <th scope="col"><?= __('Pais') ?></th>
            <th scope="col"><?= __('Cep') ?></th>
            <th scope="col"><?= __('Latitude') ?></th>
            <th scope="col"><?= __('Longitude') ?></th>
            <th scope="col"><?= __('Tel Fixo') ?></th>
            <th scope="col"><?= __('Tel Fax') ?></th>
            <th scope="col"><?= __('Tel Celular') ?></th>
            <th scope="col"><?= __('Audit Insert') ?></th>
            <th scope="col"><?= __('Audit Update') ?></th>
            <th scope="col" class="actions"><?= __('Actions') ?></th>
        </tr>
        <?php foreach ($tiposBrindesRede->clientes as $clientes) : ?>
        <tr>
            <td><?= h($clientes->id) ?></td>
            <td><?= h($clientes->matriz) ?></td>
            <td><?= h($clientes->ativado) ?></td>
            <td><?= h($clientes->tipo_unidade) ?></td>
            <td><?= h($clientes->codigo_equipamento_rti) ?></td>
            <td><?= h($clientes->nome_fantasia) ?></td>
            <td><?= h($clientes->razao_social) ?></td>
            <td><?= h($clientes->cnpj) ?></td>
            <td><?= h($clientes->endereco) ?></td>
            <td><?= h($clientes->endereco_numero) ?></td>
            <td><?= h($clientes->endereco_complemento) ?></td>
            <td><?= h($clientes->bairro) ?></td>
            <td><?= h($clientes->municipio) ?></td>
            <td><?= h($clientes->estado) ?></td>
            <td><?= h($clientes->pais) ?></td>
            <td><?= h($clientes->cep) ?></td>
            <td><?= h($clientes->latitude) ?></td>
            <td><?= h($clientes->longitude) ?></td>
            <td><?= h($clientes->tel_fixo) ?></td>
            <td><?= h($clientes->tel_fax) ?></td>
            <td><?= h($clientes->tel_celular) ?></td>
            <td><?= h($clientes->audit_insert) ?></td>
            <td><?= h($clientes->audit_update) ?></td>
            <td class="actions">
                <?= $this->Html->link(__('View'), ['controller' => 'Clientes', 'action' => 'view', $clientes->id]) ?>
                <?= $this->Html->link(__('Edit'), ['controller' => 'Clientes', 'action' => 'edit', $clientes->id]) ?>
                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Clientes', 'action' => 'delete', $clientes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $clientes->id)]) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div> -->

<table class="table table-striped table-hover">
    <tr>
        <th scope="row"><?= __('Código') ?></th>
        <td><?= $this->Number->format($tiposBrindesCliente->id) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Nome') ?></th>
        <td><?= h($tiposBrindesCliente["tipos_brindes_rede"]["nome"]) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Equipamento RTI') ?></th>
        <td><?= $this->Boolean->convertBooleanToString($tiposBrindesCliente->equipamento_rti); ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Habilitado') ?></th>
        <td><?= $this->Boolean->convertEnabledToString($tiposBrindesCliente->habilitado) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Atribuir Automatico') ?></th>
        <td><?= $this->Boolean->convertBooleanToString($tiposBrindesCliente->atribuir_automatico) ?></td>
    </tr>
    <?php if ($tiposBrindesCliente["tipos_brindes_rede"]["equipamento_rti"]): ?>
        <tr>
            <th scope="row"><?= __('Código Primário de Equipamento RTI') ?></th>
            <td><?= h($tiposBrindesCliente["tipo_principal_codigo_brinde"]) ?></td>
        </tr>
        <tr>
        <th scope="row"><?= __('Código Secundário de Equipamento RTI') ?></th>
            <td><?= h($tiposBrindesCliente["tipo_secundario_codigo_brinde"]) ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <th scope="row"><?= __('Data de Criação') ?></th>
        <td><?= h(!empty($tiposBrindesCliente->audit_insert) ? $tiposBrindesCliente->audit_insert->format('d/m/Y H:i:s') : null) ?></td>
    </tr>
    <tr>
        <th scope="row"><?= __('Data da Última Alteração') ?></th>
        <td><?= h(!empty($tiposBrindesCliente->audit_update) ? $tiposBrindesCliente->audit_update->format('d/m/Y H:i:s') : null) ?></td>
    </tr>
</table>


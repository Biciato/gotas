<?php

/**
 * tabela_info_usuarios.ctp 
 * 
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2017-08-10
 */

?>

<div class="form-group row">

<div class="col-lg-4">
    <label for="nome">Nome:</label>
    <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $usuario["nome"]?>" readonly />
</div>
<div class="col-lg-4">
    <label for="tipo_perfil">Tipo de Perfil:</label>
    <input type="text" class="form-control" id="tipo_perfil" nome="tipo_perfil" value="<?php echo $this->UserUtil->getProfileType($usuario["tipo_perfil"])?>" readonly />
</div>
<div class="col-lg-4">
    <label for="data_nasc">Data Nascimento:</label>
    <input type="text" class="form-control" id="data_nasc" nome="data_nasc" value="<?php echo $this->DateUtil->dateToFormat($usuario["data_nasc"], "d/m/Y")?>" readonly />
</div>

<?php if (strlen($usuario->cpf) > 0) : ?> 
<div class="col-lg-4">
    <label for="cpf">CPF:</label>
    <input type="text" class="form-control" id="cpf" nome="cpf" value="<?php echo $this->NumberFormat->formatNumberToCPF($usuario["cpf"])?>" readonly />
</div>
// @todo gustavosg continuar ajuste 
<tr>

    <th scope="row"><?= __('Cpf') ?></th>
    <td><?= h($this->NumberFormat->formatNumberToCPF($usuario->cpf)) ?></td>
</tr>
<?php elseif (strlen($usuario->doc_estrangeiro) > 0) : ?> 
<tr>
    <th scope="row"><?= __('Documento Estrangeiro') ?></th>
    <td><?= h($usuario->doc_estrangeiro) ?></td>
</tr>
<?php endif; ?> 

</div>
    <table class="table table-striped table-hover table-condensed table-responsive">

         <tr>
            <th scope="row"><?= __('Data de Nascimento') ?></th>
            <td><?= h(date('d/m/Y', strtotime($usuario->data_nasc))) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Email') ?></th>
            <td><?= h($usuario->email) ?></td>
        </tr>
     
        <tr>
            <th scope="row"><?= __('Telefone') ?></th>
            <td><?= h($this->Phone->formatPhone($usuario->telefone)) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Endereco') ?></th>
            <td><?= h(__($usuario->endereco)) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Endereco Complemento') ?></th>
            <td><?= h($usuario->endereco_complemento) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Número') ?></th>
            <td><?= $this->Number->format($usuario->endereco_numero) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Bairro') ?></th>
            <td><?= h($usuario->bairro) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Municipio') ?></th>
            <td><?= h($usuario->municipio) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Estado') ?></th>
            <td><?= h($usuario->estado) ?></td>
        </tr>
        
        <tr>
            <th scope="row"><?= __('CEP') ?></th>
            <td><?= h($this->Address->formatCep($usuario->cep)) ?></td>
        </tr>
      
        <tr>
            <th scope="row"><?= __('Sexo') ?></th>
            <td><?= $this->UserUtil->getGenderType($usuario->sexo) ?></td>
        </tr>

        <tr>
            <th scope="row"><?= __('Portador de Nec. Especiais?') ?></th>
            <td><?= $this->Boolean->convertBooleanToString($usuario->necessidades_especiais) ?></td>
        </tr>
        <tr>
            <th>Data de Criação</th>
            <td><?= h($usuario->audit_insert->format('d/m/Y H:i:s')) ?></tr>
            
        </tr>
        <tr>
            <th>Data de alteração</th>
            <td><?= h(isset($usuario->audit_update) ? $usuario->audit_update->format('d/m/Y H:i:s') : null) ?></tr>
            
        </tr>
    </table>
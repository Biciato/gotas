<?php

/**
 * tabela_info_usuarios.ctp 
 * 
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @since 2017-08-10
 */

use Cake\Core\Configure;

?>

<fieldset>
<div class="form-group row">

    <div class="col-lg-4">
        <label for="nome">
            Nome:
        </label>
        <input type="text" 
            class="form-control" 
            id="nome"
            name="nome" 
            value="<?php echo $usuario["nome"] ?>" 
            readonly />
    </div>
    <div class="col-lg-4">
        <label for="email">
            Email:
        </label>
        <input type="text" 
            class="form-control" 
            id="email" 
            name="email" 
            value="<?php echo $usuario["email"] ?>" 
            readonly />
    </div>
    <div class="col-lg-4">
        <label for="tipo_perfil">
            Tipo de Perfil:
            </label>
        <input type="text" 
            class="form-control" 
            id="tipo_perfil" 
            name="tipo_perfil" 
            value="<?php echo $this->UserUtil->getProfileType($usuario["tipo_perfil"]) ?>" 
            readonly />
    </div>
    
</div>

<?php if ($usuario["tipo_perfil"] == Configure::read("profileTypes")["UserProfileType"]) : ?> 
<div class="form-group row">
    <div class="col-lg-6">
        <label for="cpf">
            CPF:
        </label>
        <input type="text" 
            class="form-control" 
            id="cpf" 
            name="cpf" 
            value="<?php echo $this->NumberFormat->formatNumberToCPF($usuario["cpf"]) ?>" 
            readonly />
    </div>
    <div class="col-lg-6">
        <label for="doc_estrangeiro">
            Documento Estrangeiro:
        </label>
        <input type="text" 
            class="form-control" 
            id="doc_estrangeiro" 
            name="doc_estrangeiro" 
            value="<?php echo $usuario["doc_estrangeiro"] ?>" 
            readonly />
    </div>

</div>
<?php endif; ?> 

<div class="form-group row">
    <div class="col-lg-2">
        <label for="data_nasc">
            Data Nascimento:
        </label>
        <input type="text" 
            class="form-control" 
            id="data_nasc"
            name="data_nasc" 
            value="<?php echo $this->DateUtil->dateToFormat($usuario["data_nasc"], "d/m/Y") ?>" 
            readonly />
    </div>
 
    <div class="col-lg-2">
        <label for="sexo">
            Sexo:
            </label>
        <input type="text" 
            class="form-control" 
            id="sexo"
            name="sexo" 
            value="<?php echo $this->UserUtil->getGenderType($usuario["sexo"]) ?>" 
            readonly />
    </div>
    <div class="col-lg-4">
        <label for="necessidades_especiais">Port. Necessidades Especiais:</label>
        <input type="text" 
            class="form-control" 
            id="necessidades_especiais" 
            name="necessidades_especiais" 
            value="<?php echo $this->Boolean->convertBooleanToString($usuario["necessidades_especiais"]) ?>" 
            readonly />
    </div>
    <div class="col-lg-2">
        <label for="audit_insert">
            Data de Criação:
        </label>
        <input type="text" 
            class="form-control" 
            id="audit_insert" 
            name="audit_insert" 
            value="<?php echo $this->DateUtil->dateToFormat($usuario["audit_insert"], "d/m/Y") ?>" 
            readonly />
    </div>
    <div class="col-lg-2">
        <label for="audit_update">
            Última Alteração:
        </label>
        <input type="text" 
            class="form-control" 
            id="audit_update" 
            name="audit_update" 
            value="<?php echo $this->DateUtil->dateToFormat($usuario["audit_update"], "d/m/Y") ?>" 
            readonly />
    </div>
</div>

<?php if ($usuario["tipo_perfil"] == Configure::read("profileTypes")["UserProfileType"]) :?> 

<div class="form-group row">
    <div class="col-lg-6">
        <label for="endereco">
            Endereço:
        </label>
        <input type="text" 
            class="form-control" 
            id="endereco" 
            name="endereco" 
            value="<?= $usuario["endereco"] ?>" 
            readonly />
    </div>
    <div class="col-lg-1">
        <label for="endereco_numero">
            Número:
        </label>
        <input type="text" 
            class="form-control" 
            id="endereco_complemento" 
            name="endereco_numero" 
            value="<?= $this->Number->format($usuario["endereco_numero"]) ?>" 
            readonly />
    </div>
    <div class="col-lg-2">
        <label for="endereco_complemento">
            Complemento:
        </label>
        <input type="text" 
            class="form-control" 
            id="endereco_complemento" 
            name="endereco_complemento" 
            value="<?= $usuario["endereco_complemento"] ?>" 
            readonly />
    </div>
    <div class="col-lg-3">
        <label for="bairro">
            Bairro:
        </label>
        <input type="text" 
            class="form-control" 
            id="bairro" 
            name="bairro" 
            value="<?= $usuario["bairro"] ?>" 
            readonly />
        </tr>
    </div>
</div>

<div class="form-group row">

    <div class="col-lg-3">
        <label for="bairro">
            Bairro:
        </label>
        <input type="text" 
            class="form-control" 
            id="bairro" 
            name="bairro"
            value="<?= $usuario["bairro"] ?>" 
            readonly />
        </tr>
    </div>
    <div class="col-lg-4">
        <label for="municipio">
            Município:
        </label>
        <input type="text" 
            class="form-control" 
            id="municipio" 
            name="municipio"
            value="<?= $usuario["municipio"] ?>" 
            readonly />
    </div>
    <div class="col-lg-1">
        <label for="estado">
            Estado:
        </label>
        <input type="text" 
            class="form-control" 
            id="estado" 
            name="estado"
            value="<?= $usuario["estado"] ?>" 
            readonly />
    </div>
    <div class="col-lg-2">
        <label for="cep">
            CEP:
        </label>
        <input type="text" 
            class="form-control" 
            id="cep" 
            name="cep"
            value="<?= $this->Address->formatCep($usuario["cep"]) ?>" 
            readonly />
    </div>

     <div class="col-lg-2">
        <label for="telefone">
            Telefone:
        </label>
        <input type="text" 
            class="form-control" 
            id="telefone" 
            name="telefone"
            value="<?= $this->Phone->formatPhone($usuario["telefone"]) ?>" 
            readonly />
    </div>

</div>

<?php elseif($usuario["tipo_perfil"] <= Configure::read("profileTypes")["ManagerProfileType"]): ?> 

    <div class="form-group row">
        <div class="col-lg-2">
            <label for="telefone">
                Telefone:
            </label>
            <input type="text" 
                class="form-control" 
                id="telefone" 
                name="telefone"
                value="<?= $this->Phone->formatPhone($usuario["telefone"]) ?>" 
                readonly />
        </div>
    </div>
<?php endif; ?> 
</fieldset>

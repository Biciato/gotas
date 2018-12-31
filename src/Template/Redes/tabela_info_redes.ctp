<?php


/**
 * @description Ver detalhes de Usuário
 * @author      Gustavo Souza Gonçalves
 * @file        src/Template/Redes/tabela_info_redes.ctp
 * @date        22/11/2017
 *
 */

?>

<div class="form-group row">
    <div class="col-lg-4">
        <label for="nome_rede">Nome da Rede</label>
        <input name="nome_rede"
            id="nome_rede"
            class="form-control"
            value="<?php echo $rede['nome_rede'] ?>"
            disabled
        />
    </div>

    <div class="col-lg-4">
        <label for="quantidade_pontuacoes_usuarios_dia">Máx. Aquisição Gotas Usuários (Dia)</label>
        <input name="quantidade_pontuacoes_usuarios_dia"
            id="quantidade_pontuacoes_usuarios_dia"
            class="form-control"
            value="<?php echo $rede['quantidade_pontuacoes_usuarios_dia'] ?>"
            disabled
        />
    </div>

    <div class="col-lg-4">
        <label for="custo_referencia_gotas">Custo Referência Gotas</label>
        <input name="custo_referencia_gotas"
            id="custo_referencia_gotas"
            class="form-control"
            value="<?php echo $rede['custo_referencia_gotas'] ?>"
            disabled
        />
    </div>

    <div class="col-lg-4">
        <label for="media_assiduidade_clientes">Media Assiduidade Clientes (Mês)</label>
        <input name="media_assiduidade_clientes"
            id="media_assiduidade_clientes"
            class="form-control"
            value="<?php echo $rede['media_assiduidade_clientes'] ?>"
            title='Utilizado para fins de relatório'
            disabled
        />
    </div>

    <div class="col-lg-2">
        <label for="ativado">Rede Ativada</label>
        <input name="ativado"
            id="ativado"
            class="form-control"
            value="<?php echo $this->Boolean->convertBooleanToString($rede['ativado']) ?>"
            title='Utilizado para fins de relatório'
            disabled
        />
    </div>

    <div class="col-lg-3">
        <label for="audit_insert">Data Cadastro</label>
        <input name="audit_insert"
            id="audit_insert"
            class="form-control"
            value="<?php echo $this->DateUtil->dateToFormat($rede['audit_insert'], "d/m/Y H:i:s") ?>"
            disabled
        />
    </div>

    <div class="col-lg-3">
        <label for="audit_update">Última Atualização</label>
        <input name="audit_update"
            id="audit_update"
            class="form-control"
            value="<?php echo $this->DateUtil->dateToFormat($rede['audit_update'], "d/m/Y H:i:s") ?>"
            disabled
        />
    </div>
</div>

<?php if (isset($imagem) && strlen($imagem) > 0) : ?>
    <div class="form-group row">
        <label for="imagem">Imagem Atual</label>
        <img src="<?php echo $imagem ?>" class="imagem-rede" id="imagem" name="imagem"/>
    </div>
<?php endif; ?>



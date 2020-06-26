<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Adicionar Usuário</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#/usuarios/index">Usuários</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Adicionar Usuário</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <input type="hidden" name="id"/>
                    <input type="hidden" name="tipo_perfil" id="tipo_perfil" value="6"/>

                    <div class="form-group row">

                        <div class="col-lg-4">
                            <div class="form-group select required">
                                <label for="tipo_perfil_select">Tipo de Perfil*</label>
                                <select name="tipo_perfil_select" id="tipo_perfil_select" required="required" class="form-control">
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group select required">
                                <label for="redes_select">Rede</label>
                                <select name="redes_select" id="redes_select" class="form-control" disabled>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group select required">
                                <label for="unidades_select">Unidade</label>
                                <select name="unidades_select" id="unidades_select" class="form-control" disabled>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="tipoDocumento">
                                <label class="form-check-label" for="tipoDocumento" style="margin-left: 0.5em">
                                    Selecione se o usuário for estrangeiro
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <span id="cpf_validation" class="text-danger validation-message"></span>
                        </div>
                        <div class="form-group col-lg-6">
                            <div id="cpf_box">
                                <label for="cpf">
                                    CPF*
                                </label>
                                <input name="cpf" type="text" id="cpf" class="form-control" required="required" placeholder="CPF..." />
                            </div>
                            <div id="doc_estrangeiro_box" style="display: none">
                                <label for="doc_estrangeiro">Documento de Identificação Estrangeira*</label>
                                <input type="text" name="doc_estrangeiro" id="doc_estrangeiro" class="form-control" placeholder="Documento Estrangeiro.." />
                            </div>

                        </div>

                        <div class="form-group col-lg-6">
                            <label for="email">E-mail</label>
                            <input type="text" name="email" id="email" class="form-control" placeholder="E-mail..." />
                            <span id="email_validation" class="text-danger validation-message">
                        </div>
                    </div>

                    <input type="hidden" name="doc_invalido" id="doc_invalido"/>
                    <div class="form-group row">

                        <div class="col-lg-5">
                            <label for="nome">Nome*</label>
                            <input type="text" name="nome" required="required" placeholder="Nome..." id="nome" class="form-control">
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group select required">
                                <label for="sexo">Sexo*</label>
                                <select name="sexo" placeholder="Sexo*..." required="required" id="sexo" class="form-control">
                                    <option value=""></option>
                                    <option value="2">Não informar</option>
                                    <option value="1">Masculino</option>
                                    <option value="0">Feminino</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group text">
                                <label for="data_nasc">Data de Nascimento</label>
                                <input type="text" name="data_nasc" class="datepicker-input form-control" div="form-inline" id="data_nasc" format="d/m/Y" value="29/05/2020"/>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-3">

                            <div class="form-group select required"><label for="necessidades-especiais">Portador de Nec. Especiais?*</label><select name="necessidades_especiais" required="required" placeholder="Necessidades Especiais..." id="necessidades-especiais" class="form-control"><option value=""></option><option value="1">Sim</option><option value="0">Não</option></select></div>                </div>

                        <div class="col-lg-3">
                            <label for="telefone">Telefone*</label>
                            <input type="telefone" name="telefone" id="telefone" placeholder="Telefone..." class="form-control" value="" required="required">
                        </div>

                        <div class="col-lg-3">
                            <label for="senha">Senha*</label>
                            <input type="password" name="senha" required="true" minLength="6" placeholder="Senha..." id="senha" class="form-control" />
                        </div>

                        <div class="col-lg-3">
                            <label for="confirm_senha">Confirmar Senha*</label>
                            <input type="password" name="confirm_senha" required="true" placeholder="Confirmar Senha..." minLength="6" id="confirm_senha" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-2">
                            <label for="cep">CEP</label>
                            <input type="text" name="cep" placeholder="CEP..." id="cep" class="form-control cep" value="">

                        </div>

                        <div class="col-lg-3">
                            <label for="endereco">Endereço</label>
                            <input type="text" name="endereco" id="endereco" class="form-control endereco" placeholder="Endereço..." value="">

                        </div>

                        <div class="col-lg-2">
                            <label for="endereco_numero">Número</label>
                            <input type="text" name="endereco_numero" id="endereco_numero" class="form-control numero" placeholder="Número..." value="" />
                        </div>
                        <div class="col-lg-2">
                            <label for="endereco_complemento">Complemento</label>
                            <input type="text" name="endereco_complemento" id="endereco_complemento" class="form-control complemento" placeholder="Complemento..." value="" />
                        </div>

                        <div class="col-lg-3">
                            <label for="bairro">Bairro</label>
                            <input type="text" name="bairro" id="bairro" class="form-control bairro" placeholder="Bairro..." value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-4">
                            <label for="municipio">Municipio</label>
                            <input type="text" name="municipio" id="municipio" class="form-control municipio" placeholder="Municipio..." value="" />
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group select"><label for="estado">Estado</label><select name="estado" class="estado form-control" id="estado"><option value=""></option><option value="AC">Acre</option><option value="AL">Alagoas</option><option value="AP">Amapá</option><option value="AM">Amazonas</option><option value="BA">Bahia</option><option value="CE">Ceará</option><option value="DF">Distrito Federal</option><option value="ES">Espírito Santo</option><option value="GO">Goiás</option><option value="MA">Maranhão</option><option value="MT">Mato Grosso</option><option value="MS">Mato Grosso do Sul</option><option value="MG">Minas Gerais</option><option value="PA">Pará</option><option value="PB">Paraíba</option><option value="PR">Paraná</option><option value="PE">Pernambuco</option><option value="PI">Piauí</option><option value="RJ">Rio de Janeiro</option><option value="RN">Rio Grande do Norte</option><option value="RS">Rio Grande do Sul</option><option value="RO">Rondônia</option><option value="RR">Roraima</option><option value="SC">Santa Catarina</option><option value="SP">São Paulo</option><option value="SE">Sergipe</option><option value="TO">Tocantins</option><option value="--">Outro</option></select></div>
                        </div>

                        <div class="col-lg-4">
                            <label for="pais">País</label>
                            <input type="text" name="pais" id="pais" class="form-control pais" placeholder="País..." value="" />
                        </div>
                    </div>
                    <div class="action-buttons">
                        <a href="#/usuarios/index" class="btn btn-danger">
                            <em class="fas fa-arrow-left"></em> Voltar
                        </a>
                        <button id="btn-save" class="btn btn-primary" style="float: right">
                            <em class="fas fa-save"></em> Confirmar
                        </button>
                        <span style="clear: right; display: block;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        usuariosAdd.init();
    });
</script>

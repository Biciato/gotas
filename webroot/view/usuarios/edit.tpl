<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-4">
        <h2>Editar Usuário</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#/usuarios/index">Usuários</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Editar Usuário</strong>
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
                        <div class="col-lg-12">
                            <span id="cpf_validation" class="text-danger validation-message"></span>
                        </div>
                        <div class="form-group col-lg-3">
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

                        <div class="col-lg-3">
                            <div class="form-group select required">
                                <label for="sexo">Sexo*</label>
                                <select name="sexo" required="required" id="sexo" class="form-control">
                                    <option value=""></option>
                                    <option value="2">Não informar</option>
                                    <option value="1">Masculino</option>
                                    <option value="0">Feminino</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="doc_invalido" id="doc_invalido"/>
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <label for="nome">Nome*</label>
                            <input type="text" name="nome" required="required" placeholder="Nome..." id="nome" class="form-control">
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group select required">
                                <label for="necessidades-especiais">Portador de Nec. Especiais?*</label>
                                <select name="necessidades_especiais" required="required" id="necessidades_especiais" class="form-control">
                                    <option value=""></option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <label for="telefone">Telefone*</label>
                            <input type="telefone" name="telefone" id="telefone" placeholder="Telefone..." class="form-control" value="" required="required">
                        </div>

                        <div class="col-lg-3">
                            <div class="form-group text">
                                <label for="data_nasc">Data de Nascimento</label>
                                <input type="text" name="data_nasc" class="form-control"/>
                            </div>
                        </div>

                        <input type="hidden" name="senha" required="true" minLength="6" id="senha" class="form-control" />
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
                            <div class="form-group select">
                                <label for="estado">Estado</label>
                                <select name="estado" class="estado form-control" id="estado">
                                    <option value=""></option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                    <option value="--">Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="pais">País</label>
                            <input type="text" name="pais" id="pais" class="form-control pais" placeholder="País..." value="" />
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button id="btn-save" class="btn btn-primary" style="float: right">
                            <em class="fas fa-save"></em> Salvar
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
        usuariosEdit.init();
    });
</script>

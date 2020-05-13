<div class="form-group row border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2>Novo Estabelecimento</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#/">Início</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#/redes/index">Redes</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#/redes/view/<%= redesId %>">Informações da Rede</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Novo Estabelecimento</strong>
            </li>
        </ol>
    </div>
</div>

<div class="content">
    <div class="row clientes-view-form">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Novo Estabelecimento</h5>
                </div>
                <div class="ibox-content">
                    <form id="form">
                        <fieldset>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#general" data-toggle="tab">Dados Gerais</a>
                                </li>
                                <li>
                                    <a href="#address" data-toggle="tab">Endereço</a>
                                </li>
                                <li>
                                    <a href="#contact" data-toggle="tab">Contato</a>
                                </li>
                                <li>
                                    <a href="#tax-software" data-toggle="tab">Software Fiscal</a>
                                </li>
                                <li>
                                    <a href="#time-board" data-toggle="tab">Quadro de Horários</a>
                                </li>
                            </ul>

                            <div class="tab-content clearfix">

                                <!-- Dados Gerais -->
                                <div class="tab-pane active" id="general">
                                    <div class="ibox-content">
                                        <div class="form-group row">
                                            <label for="codigo_equipamento_rti" class="col-lg-2">
                                                Código Equipamento RTI*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="codigo_equipamento_rti"
                                                    id="codigo-equipamento-rti" class="form-control"
                                                    placeholder="Código Equipamento RTI..."
                                                    title="Código Equipamento RTI" value="" />
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="tipo_unidade" class="col-lg-2">
                                                Tipo Unidade*
                                            </label>
                                            <div class="col-lg-10">
                                                <select name="tipo_unidade" id="tipo-unidade"
                                                    title="Tipo de Estabelecimento (Posto / Loja)" class="form-control">
                                                    <option value=""></option>
                                                    <option value="1">Posto</option>
                                                    <option value="0">Loja</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="nome_fantasia" class="col-lg-2">Nome Fantasia</label>
                                            <div class="col-lg-10">
                                                <input type="text" name="nome_fantasia" id="nome-fantasia"
                                                    class="form-control" placeholder="Nome Fantasia..."
                                                    title="Nome Fantasia" value="" />
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="razao_social" class="col-lg-2">
                                                Razão Social*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="razao_social" id="razao-social"
                                                    class="form-control" placeholder="Razão Social..."
                                                    title="Razão Social" value="" />
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="cnpj" class="col-lg-2">
                                                CNPJ*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="cnpj" id="cnpj" class="form-control"
                                                    placeholder="CNPJ..." title="CNPJ" value="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Endereço -->
                                <div class="tab-pane" id="address">
                                    <div class="ibox-content">
                                        <div class="form-group row">
                                            <label for="endereco" class="col-lg-2">
                                                Endereço*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Endereço" id="endereco"
                                                    placeholder="Endereço" name="endereco" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="endereco_numero" class="col-lg-2">
                                                Número
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Número"
                                                    id="endereco_numero" placeholder="Número" name="endereco_numero"
                                                    value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="endereco_complemento" class="col-lg-2">
                                                Complemento
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Complemento"
                                                    placeholder="Complemento" id="endereco_complemento"
                                                    name="endereco_complemento" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="cep" class="col-lg-2">
                                                CEP*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="CEP" id="cep"
                                                    placeholder="CEP" name="cep" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="bairro" class="col-lg-2">
                                                Bairro
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Bairro" id="bairro"
                                                    name="bairro" placeholder="Bairro" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="municipio" class="col-lg-2">
                                                Município
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Município" id="municipio"
                                                    placeholder="Município" name="municipio" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="estado" class="col-lg-2">
                                                Estado*
                                            </label>
                                            <div class="col-lg-10">
                                                <select id="estado" name="estado" class="form-control" title="Estado"
                                                    placeholder="Estado">
                                                    <option></option>
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
                                                </select>
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="pais" class="col-lg-2">
                                                País
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="País" id="pais"
                                                    name="pais" placeholder="País" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="latitude" class="col-lg-2">Latitude</label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Latitude" id="latitude"
                                                    placeholder="Latitude" name="latitude" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="longitude" class="col-lg-2">Longitude</label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Longitude" id="longitude"
                                                    placeholder="Longitude" name="longitude" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contato -->
                                <div class="tab-pane" id="contact">
                                    <div class="ibox-content">
                                        <div class="form-group row">
                                            <label for="tel-fixo" class="col-lg-2">
                                                Telefone Fixo
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Telefone Fixo"
                                                    placeholder="Telefone Fixo" id="tel-fixo" name="tel_fixo" value="">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="tel-fax" class="col-lg-2">
                                                Telefone Fax
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Telefone Fax"
                                                    placeholder="Telefone Fax" id="tel-fax" name="tel_fax" value="">
                                            </div>

                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="tel_celular" class="col-lg-2">
                                                Telefone Celular*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" title="Telefone Celular"
                                                    placeholder="Telefone Celular" id="tel-celular" name="tel_celular"
                                                    value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Software Fiscal -->
                                <div class="tab-pane" id="tax-software">
                                    <div class="ibox-content">
                                        <div class="form-group row">
                                            <label for="impressao_sw_linha_continua" class="col-lg-2">
                                                Impressão Continua de SW Fiscal?
                                            </label>
                                            <div class="col-lg-10">
                                                <select name="impressao_sw_linha_continua"
                                                    id="impressao_sw_linha_continua"
                                                    title="Sim => Sistema Fiscal imprime CF de uma vez / Não => Sistema Fiscal imprime CF Linha a Linha"
                                                    class="form-control">
                                                    <option value="1">Sim</option>
                                                    <option value="0">Não</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="delimitador_nota_impressao" class="col-lg-2">
                                                Delimitador de Impressão da Nota Fiscal
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="delimitador_nota_impressao"
                                                    id="delimitador-nota-impressao" class="form-control" value=""
                                                    placeholder="Delimitador de Impressão da Nota Fiscal">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="delimitador_nota_produtos_inicial" class="col-lg-2">
                                                Delimitador Inicial de Produtos da Nota Fiscal
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="delimitador_nota_produtos_inicial"
                                                    id="delimitador-nota-produtos-inicial" class="form-control" value=""
                                                    placeholder="Delimitador Inicial de Produtos da Nota Fiscal">
                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                        <div class="form-group row">
                                            <label for="delimitador_nota_produtos_final" class="col-lg-2">
                                                Delimitador Final de Produtos da Nota Fiscal
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="delimitador_nota_produtos_final"
                                                    id="delimitador-nota-produtos-final" class="form-control" value=""
                                                    placeholder="Delimitador Final de Produtos da Nota Fiscal">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quadros de Horários -->
                                <div class="tab-pane" id="time-board">
                                    <div class="ibox-content">
                                        <div class="form-group row">
                                            <label for="qte_turnos" class="col-lg-2">
                                                Quantidade de Turnos
                                            </label>
                                            <div class="col-lg-10">
                                                <select name="qte_turnos" id="qte-turnos" class="form-control">
                                                    <option value="2" selected>2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="6">6</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="turno" class="col-lg-2">
                                                Primeiro Turno do Dia*
                                            </label>
                                            <div class="col-lg-10">
                                                <input type="text" name="turno" id="turno" class="form-control" />
                                            </div>
                                        </div>
                                        <div id="quadro-horarios">

                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <a href="#/redes/view/<%=redesId %>" class="btn btn-default" tooltip="Cancelar"
                                    id="btn-cancel">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                                <button type="submit" id="btn-save" class="btn btn-primary">
                                    <em class="fas fa-save"></em> Salvar
                                </button>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/webroot/css/styles/clientes/view.css">

<script>
    $(function () {
        let dataStorage = JSON.parse(localStorage.getItem("data"));

        clientesAdd.init(dataStorage.id);
    })
        .ajaxStart(callLoaderAnimation)
        .ajaxStop(closeLoaderAnimation)
        .ajaxError(closeLoaderAnimation);
</script>

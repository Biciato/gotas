<div class="col-lg-12">

    <div class="loading" if-loading>
        <!--
            TODO:
            Isto deverá ser portado para o template principal depois da mudança para AngularJS
        -->
        <img src="/webroot/img/icons/loading.gif" alt="">
    </div>

    <div ng-model="inputData" ng-init="init()">

    <legend>Relatório de Usuários Assíduos:</legend>

    <div class="row">
        <div class="col-lg-4">
            <label>Posto de Atendimento:</label>
            <ui-select ng-model="inputData.clientesSelectedItem" theme="bootstrap" title="Posto de Atendimento" >
                <ui-select-match placeholder="Posto de Atendimento..." allow-clear="true">
                    {{$select.selected.razao_social}} / {{$select.selected.nome_fantasia}}
                </ui-select-match>
                <ui-select-choices repeat="cliente in clientesList | filter: {nome_fantasia: $select.search}">
                    <span>{{cliente.nome_fantasia}}</span>
                </ui-select-choices>
            </ui-select>
        </div>

        <!-- Nome: -->
        <div class="col-lg-4">
            <label>Nome: </label>
            <input type="text" ng-model="inputData.nome" ng-maxlength="50" ng-trim="true" class="form-control">
        </div>

        <!-- CPF: -->
        <div class="col-lg-4">
            <label>CPF: </label>
            <input type="text" ng-model="inputData.cpf" ui-mask="999.999.999-99" numbers-only ng-trim="true" class="form-control">
        </div>
    </div>
    <div class="row">

        <!-- Veículo: -->
        <div class="col-lg-2">
            <label>Veículo: </label>
            <input type="text" ui-mask="AAA9999" ng-model="inputData.placa" ng-change="inputData.placa=inputData.placa.toUpperCase()" ng-trim="true" class="form-control">
        </div>

        <!-- Doc. Estrangeiro -->
        <div class="col-lg-2">
            <label for="input-id">Doc. Estrangeiro</label>
            <input type="text" ng-model="inputData.documentoEstrangeiro" ng-trim="true" class="form-control">
        </div>

        <!-- Status de Cadastro: -->
        <div class="col-lg-2">
            <label>Status de Cadastro: </label>
            <ui-select ng-model="inputData.statusSelectedItem" theme="bootstrap" title="Status" >
                <ui-select-match placeholder="Status..." allow-clear="true">
                    {{$select.selected.nome}}
                </ui-select-match>
                <ui-select-choices repeat="status in inputData.statusList | filter: {nome: $select.search}">
                    <span>{{status.nome}}</span>
                </ui-select-choices>
            </ui-select>
        </div>

        <!-- Usuários Assíduos: -->
        <div class="col-lg-2">
            <label>Usuários Assíduos: </label>
            <ui-select ng-model="inputData.assiduidadeSelectedItem" theme="bootstrap" title="Status" >
                <ui-select-match placeholder="Assiduidade..." allow-clear="true">
                    {{$select.selected.nome}}
                </ui-select-match>
                <ui-select-choices repeat="assiduidade in inputData.assiduidadeList | filter: {nome: $select.search}">
                    <span>{{assiduidade.nome}}</span>
                </ui-select-choices>
            </ui-select>
        </div>
        <!-- Data Inicial -->
        <div class="col-lg-2">
            <label>Data Inicial:</label>
            <p class="input-group">
                <input type="text" model-view-value="true" ui-mask="99/99/9999" class="form-control" uib-datepicker-popup="{{format}}" ng-model="inputData.dataInicial" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="false" close-text="Fechar" current-text="Hoje" clear-text="Limpar" alt-input-formats="altInputFormats" />
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" ng-click="open1()"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
            </p>
        </div>

        <!-- Data Final-->
        <div class="col-lg-2">
            <label>Data Final:</label>
            <p class="input-group">
                <input type="text" model-view-value="true" ui-mask="99/99/9999" class="form-control" uib-datepicker-popup="{{format}}" ng-model="inputData.dataFinal" is-open="popup2.opened" datepicker-options="dateOptions" ng-required="false" close-text="Fechar" current-text="Hoje" clear-text="Limpar" alt-input-formats="altInputFormats" />
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default" ng-click="open2()"><i class="glyphicon glyphicon-calendar"></i></button>
                </span>
            </p>
        </div>

    </div>
    <div class="form-group row">
        <div class="col-lg-5 pull-right group-btn-area vertical-align">
            <button class="col-lg-4 btn btn-danger text-center" escape="#" ng-click="limparDados()">
                <i class="fa fa-trash">
                </i>
                Limpar
            </button>

            <button class="col-lg-4 btn btn-primary text-center" escape="#" ng-click="pesquisarUsuarios(inputData)">
                <i class="fa fa-search">
                </i>
                Pesquisar
            </button>

            <button class="col-lg-4 btn btn-success" ng-click="gerarExcel(inputData)">
                <span class="fa fa-file-excel-o">
                </span>
                Gerar Excel
            </button>
        </div>
    </div>
</div>

    <!-- Tabela -->
    <div class="row">
        <div class="col-lg-12">

        <table class="table table-condensed table-responsive table-striped table-hover">
            <thead >
                <th ng-repeat="cabecalho in cabecalhos">{{cabecalho}}</th>
            </thead>
            <tbody>
                <tr ng-repeat="usuario in dadosUsuarios | orderBy: usuario.nome | startFrom:(paginaAtual - 1 ) * tamanhoDaPagina | limitTo:tamanhoDaPagina">
                    <td width="10%">{{usuario.nome}}</td>
                    <td width="8%">{{usuario.cpf | cpf}}</td>
                    <td width="8%">{{usuario.docEstrangeiro}}</td>
                    <td width="6%">{{usuario.mediaAssiduidade}}</td>
                    <td width="10%">{{usuario.statusAssiduidade | regularIrregular}}</td>
                    <td width="10%">{{usuario.gotasAdquiridas}}</td>
                    <td width="10%">{{usuario.gotasUtilizadas}}</td>
                    <td width="10%">{{usuario.gotasExpiradas}}</td>
                    <td width="10%">{{usuario.saldoAtual}}</td>
                    <td width="10%">{{usuario.totalMoedaCompraBrindes | currency}}</td>
                    <!-- <td width="15%">{{usuario.dataVinculo}}</td> -->
                    <td width="12%">
                        <button ng-click="detalhesUsuario(usuario)" title="Detalhes Usuário" style="width: 25px">
                            <i class="fas fa-user"></i>
                        </button>
                        <button ng-click="detalhesAssiduidadeUsuario(usuario)" title="Detalhes Assiduidade" style="width: 25px">
                            <i class="fas fa-info"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="text-center">
        <ul uib-pagination boundary-links="true" total-items="dadosUsuarios.length" ng-model="paginaAtual"
        items-per-page="tamanhoDaPagina" class="pagination-sm"
        max-size="5" previous-text="<< Ant." next-text="Próx. >>" first-text="Primeira" last-text="Última"></ul>
        </div>
        <div ng-show="dadosUsuarios.length == 0">
            <span class="alert alert-warning">Não há registros à serem exibidos!</span>
        </div>
        </div>
    </div>
</div>

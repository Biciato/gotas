<div class="col-lg-12">

    <div class="loading" if-loading>
        <!--
            TODO:
            Isto deverá ser portado para o template principal depois da mudança para AngularJS
        -->
        <img src="/webroot/img/icons/loading.gif" alt="">
    </div>

    <div ng-model="inputData" ng-init="init()">

    <legend>Relatório de Frequência Média de Usuários:</legend>

        <div class="row">

            <!-- <div ng-if="usuarioLogado.tipoPerfil >= APP_CONFIG.PROFILE_TYPES.ADMIN_DEVEL">
                oLA
            </div> -->

            <div class="col-lg-6">
                <label>Posto de Atendimento:</label>
                <ui-select ng-model="inputData.clientesSelectedItem" theme="bootstrap" title="Posto de Atendimento"  maxlength="20">
                    <ui-select-match placeholder="Posto de Atendimento..." allow-clear="true" maxlength="20">
                    {{$select.selected.nome_fantasia}} / {{$select.selected.razao_social}}
                    </ui-select-match>
                    <ui-select-choices repeat="cliente in clientesList | filter: {nome_fantasia: $select.search}">
                        <span maxlength="20">{{cliente.nome_fantasia}}</span>
                    </ui-select-choices>
                </ui-select>
            </div>
            <!-- Nome: -->
            <div class="col-lg-6">
                <label>Usuário(s): </label>
                <input type="text" ng-model="inputData.nome" ng-maxlength="50" ng-trim="true" class="form-control">
            </div>
            <!-- Status: -->
            <div class="col-lg-3">
                <label>Status Conta: </label>
                <ui-select ng-model="inputData.usuarioContaAtivadaSelectedItem" theme="bootstrap" title="Status" >
                    <ui-select-match placeholder="Status..." allow-clear="true">
                        {{$select.selected.nome}}
                    </ui-select-match>
                    <ui-select-choices repeat="status in inputData.usuarioContaAtivadaList | filter: {nome: $select.search}">
                        <span>{{status.nome}}</span>
                    </ui-select-choices>
                </ui-select>
            </div>
            <!-- Tipo de Frequência: -->
            <div class="col-lg-3">
                <label>Tipo de Frequência </label>
                <ui-select ng-model="inputData.tipoFrequenciaSelectedItem" theme="bootstrap" title="Status" >
                    <ui-select-match placeholder="Status..." allow-clear="true">
                        {{$select.selected}}
                    </ui-select-match>
                    <ui-select-choices repeat="status in inputData.tipoFrequenciaList | filter: $select.search">
                        <span>{{status}}</span>
                    </ui-select-choices>
                </ui-select>
            </div>
            <!-- Data Inicial -->
            <div class="col-lg-3">
                <label>Data Inicial:</label>
                <p class="input-group">
                    <input type="text" model-view-value="true" ui-mask="99/99/9999" class="form-control" uib-datepicker-popup="{{format}}" ng-model="inputData.dataInicial" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="false" close-text="Fechar" current-text="Hoje" clear-text="Limpar" alt-input-formats="altInputFormats" />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="open1()"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </p>
            </div>

            <!-- Data Final-->
            <div class="col-lg-3">
                <label>Data Final:</label>
                <p class="input-group">
                    <input type="text" model-view-value="true" ui-mask="99/99/9999" class="form-control" uib-datepicker-popup="{{format}}" ng-model="inputData.dataFinal" is-open="popup2.opened" datepicker-options="dateOptions" ng-required="false" close-text="Fechar" current-text="Hoje" clear-text="Limpar" alt-input-formats="altInputFormats" />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="open2()"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </p>
            </div>
            <!-- CPF: -->
            <!-- <div class="col-lg-2">
                <label>CPF: </label>
                <input type="text" ng-model="inputData.cpf" ui-mask="999.999.999-99" numbers-only ng-trim="true" class="form-control">
            </div> -->

            <!-- Veículo: -->
            <!-- <div class="col-lg-2">
                <label>Veículo: </label>
                <input type="text" ui-mask="AAA9999" ng-model="inputData.placa" ng-change="inputData.placa=inputData.placa.toUpperCase()" ng-trim="true" class="form-control">
            </div> -->

            <!-- Doc. Estrangeiro -->
            <!-- <div class="col-lg-3">
                <label for="input-id">Doc. Estrangeiro</label>
                <input type="text" ng-model="inputData.documentoEstrangeiro" ng-trim="true" class="form-control">
            </div> -->



        </div>
        <div class="form-group row">
            <div class="col-lg-5 pull-right group-btn-area vertical-align">
                <button class="col-lg-4 btn btn-danger text-center" escape="#" ng-click="limparDados()">
                    <i class="fas fa-trash">
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

        <table class="table table-condensed table-responsive table-striped table-hover" >
            <thead >
                <th ng-repeat="cabecalho in cabecalhos">{{cabecalho}}</th>
            </thead>
            <tbody>
                <tr ng-repeat="usuario in dadosUsuarios | orderBy: usuario.nome | startFrom:(paginaAtual - 1 ) * tamanhoDaPagina | limitTo:tamanhoDaPagina">
                    <td width="20%">{{usuario.nome}}</td>
                    <td width="10%">{{usuario.cpf | cpf}}</td>
                    <td width="10%">{{usuario.mediaFrequencia}}</td>
                    <td width="15%">
                        <div ng-click="detalhesUsuario(usuario)" ng-tooltip="Detalhes" class="btn btn-default btn-xs">
                        <i class="fas fa-user"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <tr ng-if="dadosUsuarios.length == 0" col-span="4">
            <span class="alert alert-warning">Não há registros à serem exibidos!</span>
        </tr>

        <div class="text-center">
        <ul uib-pagination boundary-links="true" total-items="dadosUsuarios.length" ng-model="paginaAtual"
        items-per-page="tamanhoDaPagina" class="pagination-sm"
        max-size="5" previous-text="<< Ant." next-text="Próx. >>" first-text="Primeira" last-text="Última"></ul>
        </div>

        </div>
    </div>
</div>

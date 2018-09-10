<div class="col-lg-12">


    <div ng-model="inputData" ng-init="init()">

    <legend>Relatório de Usuários Fidelizados:</legend>

        <div class="row">
            <div class="col-lg-12">
                <label>Posto de Atendimento:</label>
                <ui-select ng-model="inputData.clientesSelectedItem" theme="bootstrap" title="Posto de Atendimento">
                    <ui-select-match placeholder="Posto de Atendimento...">
                        {{$select.selected.razao_social}} / {{$select.selected.nome_fantasia}}
                    </ui-select-match>
                    <ui-select-choices repeat="cliente in clientesList | filter: {nome_fantasia: $select.search}">
                        <span>{{cliente.nome_fantasia}}</span>
                    </ui-select-choices>
                </ui-select>
            </div>
        </div>

        <div class="row">
            <!-- Nome: -->
            <div class="col-lg-4">
                <label>Nome: </label>
                <input type="text" ng-model="inputData.nome" ng-maxlength="50" ng-trim="true" class="form-control">
            </div>

            <!-- CPF: -->
            <div class="col-lg-2">
                <label>CPF: </label>
                <input type="text" ng-model="inputData.cpf" ui-mask="999.999.999-99" numbers-only ng-trim="true" class="form-control">
            </div>

            <!-- Veículo: -->
            <div class="col-lg-2">
                <label>Veículo: </label>
                <input type="text" ui-mask="AAA9999" ng-model="inputData.veiculo" ng-change="inputData.veiculo=inputData.veiculo.toUpperCase()" ng-trim="true" class="form-control">
            </div>

            <!-- Documento Estrangeiro -->
            <div class="col-lg-4">
                <label for="input-id">Documento Estrangeiro</label>
                <input type="text" ng-model="inputData.documentoEstrangeiro" ng-trim="true" class="form-control">
            </div>

        </div>
        <div class="row">
            <!-- Status: -->
            <div class="col-lg-3">
                <label>Status: </label>
                <select ng-model="inputData.statusSelectedItem" ng-options="y for (x,y) in inputData.statusList" class="form-control"></select>
            </div>
            <!-- Data Inicial -->
            <div class="col-lg-2">
                <label>Data Inicial:</label>
                <p class="input-group">
                    <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="inputData.dataInicial" is-open="popup1.opened" datepicker-options="dateOptions" ng-required="false" close-text="Fechar" current-text="Hoje" clear-text="Limpar" alt-input-formats="altInputFormats" />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="open1()"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </p>
            </div>

            <!-- Data Final-->
            <div class="col-lg-2">
                <label>Data Final:</label>
                <p class="input-group">
                    <input type="text" class="form-control" uib-datepicker-popup="{{format}}" ng-model="inputData.dataFinal" is-open="popup2.opened" datepicker-options="dateOptions" ng-required="false" close-text="Fechar" current-text="Hoje" clear-text="Limpar" alt-input-formats="altInputFormats" />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-default" ng-click="open2()"><i class="glyphicon glyphicon-calendar"></i></button>
                    </span>
                </p>
            </div>

            <div class="col-lg-3 pull-right">
                <button class="col-lg-6 btn btn-primary" ng-click="pesquisar(inputData)">
                    <span class="fa fa-search">
                    </span>
                    <div>Pesquisar</div>
                </button>

                <button class="col-lg-6 btn btn-default" ng-click="gerarExcel(inputData)">
                    <span class="fa fa-file-excel-o">
                    </span>
                    <div>Gerar Excel</div>
                </button>
            </div>
        </div>




    </div>

{{ inputData }}

</div>

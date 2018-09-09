<div class="col-lg-12">


    <div ng-model="inputData" ng-init="init()">

    <legend>Relatório de Usuários Ativos:</legend>

        <!-- Nome: -->
        <div class="col-lg-3">
            <label>Nome: </label>
            <input type="text" ng-model="inputData.nome" ng-maxlength="50" ng-trim="true" class="form-control">
        </div>

        <!-- Status: -->
        <div class="col-lg-3">
            <label>Status: </label>
            <select ng-model="inputData.statusSelectedItem" ng-options="y for (x,y) in inputData.statusList" class="form-control"></select>
            <!-- <input type="list" ng-model="inputData.nome" class="form-control "> -->
        </div>

    </div>

{{ inputData }}

</div>

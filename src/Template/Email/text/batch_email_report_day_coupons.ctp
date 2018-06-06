<style>
    .left {
        float: left;
        width: 360px;
        height: 683px;
		padding: 20px;
    }

    .left img {
        width: 360px;
        height: 683px;
    }

    .right {
		padding: 20px;
	}

    .table {
        border: 1px solid black;
    }

    .table thead {
        background-color: #c4c6c6;
        border: 1px dashed black;
    }

    .table thead th {
        border: 1px solid black;
    }

    .table tbody {
        background-color: #F7F7F7;
    }

    .table tbody td {
        border: 1px solid black;
    }

</style>

<p>Olá
    <?php echo $admin_name; ?>
</p>

<p>Você está recebendo o relatório diário dos Cupons Fiscais processados, aos quais foram inseridos manualmente.</p>

<p>Para fazer a consulta via site da SEFAZ, acesse o link:

    <a href='<?= $link_sefaz?>'>Site Sefaz</a>, com a chave do cupom fiscal. </p>


<?php foreach ($pontuacoes_comprovantes as $key => $pontuacao) : ?>

    <h4>
        <?= __("Atendimento ao Cliente {0}", $pontuacao['usuario']['nome'] );?>
    </h4>

    <?= $this->Html->tag('span', __("Link para realizar auditoria:")) ?>
	<a href="<?= $pontuacao['appAddress'].'pontuacoes/detalhesCupom/'. $pontuacao['id']?>">Auditoria</a>
            
    <h5>Dados do atendimento</h5>
    <table class="table table-striped table-hover align-center">
        <tr>
            <th>
                <?= __('Cliente') ?>
            </th>
            <td>
                <?= h($pontuacao['usuario']['nome']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Funcionário do atendimento') ?>
            </th>
            <td>
                <?= h($pontuacao['funcionario']['nome']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Total de Gotas') ?>
            </th>
            <td>
                <?= h($pontuacao['soma_pontuacoes']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Chave da NFE') ?>
            </th>
            <td>
                <?= h($pontuacao['chave_nfe']) ?>
            </td>
        </tr>
        <tr>
            <th>
                <?= __('Data Impressão ') ?>
            </th>
            <td>
                <?= $pontuacao['data']->format('d/m/Y H:i:s') ?>
            </td>

        </tr>
    </table>

    <div class="container">
        <div class="left">

            <legend>Imagem da Captura</legend>
            <?= $this->Html->image($pontuacao['image_data'], ['alt' => 'Comprovante', 'class' => 'image-receipt', 'width' => '360', 'height' => '683', 'style' => 'display: block;']) ?>
        </div>
        <div class="right">

            <legend>Dados da Captura</legend>

            <?= $this->element('../Pontuacoes/tabela_descritivo_pontuacoes', ['pontos' => $pontuacao['pontuacoes']]) ?>
        </div>
    </div>


<?php endforeach; ?>

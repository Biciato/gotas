<?php


$brindes_aguardando_autorizacao = isset($brindes_aguardando_autorizacao) ? $brindes_aguardando_autorizacao : null;


?>

<h1><?= $this->fetch('title') ?></h1>
<?= $this->fetch('content') ?>


<body class="home">

    <div class="container">
       <div class="painel-avisos">

       		<?php if (sizeof($brindes_aguardando_autorizacao->toArray()) > 0): ?>
	       		<div class="painel-avisos-brindes-autorizacao">
	       			<legend>Brindes com valores divergentes do padrão</legend>


	       			<?= $this->Html->link(__("Sua rede possui {0} brinde(s) aguardando aprovação de alteração de preço!", sizeof($brindes_aguardando_autorizacao)), array('controller' => 'clientesHasBrindesHabilitadosPreco', 'action' => 'brindesAguardandoAprovacao')); ?>
	       		</div>
       		<?php endif;?>
       </div>
    </div>

</div>
</body> 


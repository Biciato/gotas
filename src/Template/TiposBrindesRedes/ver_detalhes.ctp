<?php

/**
 * ver_detalhes.ctp
 *
 * View para genero_brindes/ver_detlahes
 *
 * Variáveis:
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\GeneroBrinde $generoBrinde
 *
 * @category View
 * @package App\Template\GeneroBrindes
 * @author Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>
 * @date 30/05/2018
 * @copyright 2018 Gustavo Souza Gonçalves
 * @license  http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    1.0
 * @link       http://pear.php.net/package/PackageName
 * @since      File available since Release 1.0.0
 *
 */

use Cake\Core\Configure;
use Cake\Routing\Router;

// Navegação
$this->Breadcrumbs->add('Início', ['controller' => 'pages', 'action' => 'display']);
$this->Breadcrumbs->add('Gênero de Brindes', ['controller' => 'genero_brindes', 'action' => 'index']);
$this->Breadcrumbs->add('Detalhes do Gênero de Brindes', [], ['class' => 'active']);

echo $this->Breadcrumbs->render(
    ['class' => 'breadcrumb']
);

?>
<?= $this->Element("../GeneroBrindes/left_menu", ["mode" => "view"]) ?>
<div class="genero-brindes view col-lg-9 col-md-10 columns content">
    <legend><?= h($generoBrinde->nome) ?></legend>
    <?= $this->Element("../GeneroBrindes/tabela_info_genero_brinde", ["generoBrinde" => $generoBrinde]) ?>
</div>

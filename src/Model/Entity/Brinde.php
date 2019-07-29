<?php

namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use App\Custom\RTI\DebugUtil;
use stdClass;

/**
 * Brinde Entity
 *
 * @property int $id
 * @property int $clientes_id
 * @property int $categorias_brindes_id
 * @property string $nome
 * @property int $ilimitado
 * @property string $tipo_venda
 * @property float $preco_padrao
 * @property decimal $valor_moeda_venda_padrao
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Cliente $cliente
 */
class Brinde extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    protected $_virtual = array("nome_img_completo", "nome_brinde_detalhado", "categoria_brinde");

    protected function _getCategoriaBrinde()
    {
        if (empty($this->_properties["categoria_brinde"]) ) {
            $obj = new stdClass();
            $obj->nome = "Sem Categoria";
            return $obj;
        }

        return $this->_properties["categoria_brinde"];
    }

    protected function _getNomeImgCompleto()
    {
        if (!empty($this->_properties["nome_img"]) && strlen($this->_properties["nome_img"]) > 0) {
            return __("{0}{1}{2}", Configure::read("appAddress"), Configure::read("imageGiftPath"), $this->_properties["nome_img"]);
        }

        return null;
    }

    protected function _getNomeBrindeDetalhado()
    {
        $codigoPrimario = 0;
        $nome = "";

        if (!empty($this->_properties["codigo_primario"])) {
            $codigoPrimario = $this->_properties["codigo_primario"];
        }

        if (!empty($this->_properties["nome"])) {
            $nome = $this->_properties["nome"];
        }

        if (empty($nome)) {
            return "";
        }

        if (empty($codigoPrimario)) {
            return $nome;
        }

        if (in_array($codigoPrimario, array(2, 4))) {
            $nomeCompleto = sprintf("%s (%s)", $nome, "PNE");
            return $nomeCompleto;
        }

        return $nome;
    }
};

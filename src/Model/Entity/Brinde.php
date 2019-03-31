<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Brinde Entity
 *
 * @property int $id
 * @property int $clientes_id
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

    protected $_virtual = array("nome_img_completo");

    protected function _getNomeImgCompleto()
    {
        if (!empty($this->_properties["nome_img"]) && strlen($this->_properties["nome_img"]) > 0) {
            return __("{0}{1}{2}", Configure::read("appAddress"), Configure::read("imageGiftPath"), $this->_properties["nome_img"]);
        }

        return null;
    }
}
;

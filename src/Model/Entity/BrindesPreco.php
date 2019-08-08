<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\Number;

/**
 * BrindesPreco Entity
 *
 * @property int $id
 * @property int $brindes_habilitados_id
 * @property float $preco
 * @property \Cake\I18n\FrozenTime $data_preco
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\BrindesHabilitado $brindes_habilitado
 */
class BrindesPreco extends Entity
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

    /**
    * ------------------------------------------------------------------------------------------
    * Propriedades Virtuais
    * ------------------------------------------------------------------------------------------
    */

    protected $_virtual = array("valor_moeda_venda_formatado");

    protected function _getValorMoedaVendaFormatado()
    {
        if (!empty($this->_properties["valor_moeda_venda"])) {
            return Number::currency($this->_properties["valor_moeda_venda"]);
        }
        
        return null;
    }
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use App\Custom\RTI\DateTimeUtil;
use Cake\Log\Log;

/**
 * Cupom Entity
 *
 * @property int $id
 * @property int $brindes_id
 * @property int $clientes_id
 * @property int $usuarios_id
 * @property int $codigo_principal
 * @property int $codigo_secundario
 * @property float $valor_pago_gotas
 * @property float $valor_pago_reais
 * @property string $tipo_venda
 * @property int $tipo_banho
 * @property int $senha
 * @property string $cupom_emitido
 * @property \Cake\I18n\FrozenTime $data
 * @property bool $resgatado
 * @property bool $usado
 * @property int $quantidade
 * @property bool $estornado
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Brinde $brinde
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Usuario $usuario
 */
class Cupon extends Entity
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

    protected $_virtual = array("data_validade");

    protected function _getDataValidade()
    {
        $data = !empty($this->_properties["data"]) ? $this->_properties["data"] : null ;

        if (empty($data)) {
            return null;
        }

        $dataValidade = date("Y-m-d H:i:s", strtotime($data .  " +1 day "));

        return $dataValidade;
    }
}

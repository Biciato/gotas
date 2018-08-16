<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TipoBrindesRede Entity
 *
 * @property int $id
 * @property string $nome
 * @property bool $equipamento_rti
 * @property bool $habilitado
 * @property bool $atribuir_automatico
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Cliente[] $clientes
 */
class TiposBrindesRede extends Entity
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

    protected function _getNomeNecessidadesEspeciais()
    {
        return __("{0} {1}", $this->_properties["nome"], $this->_properties["brinde_necessidades_especiais"] == 1 ? "(PNE)" : null);
    }
}

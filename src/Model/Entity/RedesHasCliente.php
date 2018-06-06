<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RedesHasCliente Entity
 *
 * @property int $id
 * @property int $redes_id
 * @property int $clientes_id
 * @property bool $ativado
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Rede $rede
 * @property \App\Model\Entity\Cliente $cliente
 */
class RedesHasCliente extends Entity
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
}

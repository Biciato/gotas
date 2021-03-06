<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GotasHasHistoricoValores Entity
 *
 * @property int $id
 * @property int $clientes_id
 * @property int $gotas_id
 * @property float $preco
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Gota $gota
 */
class GotasHasHistoricoValores extends Entity
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
        'clientes_id' => true,
        'gotas_id' => true,
        'preco' => true,
        'audit_insert' => true,
        'audit_update' => true,
        'cliente' => true,
        'gota' => true
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GeneroBrindesCliente Entity
 *
 * @property int $id
 * @property int $genero_brindes_id
 * @property int $clientes_id
 * @property bool $tipo_principal_codigo_brinde
 * @property int $tipo_secundario_codigo_brinde
 * @property bool $habilitado
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\GeneroBrinde $genero_brinde
 * @property \App\Model\Entity\Cliente $cliente
 */
class GeneroBrindesCliente extends Entity
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

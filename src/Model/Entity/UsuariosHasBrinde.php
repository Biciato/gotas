<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsuariosHasBrinde Entity
 *
 * @property int $id
 * @property int $usuarios_id
 * @property int $brindes_habilitados_id
 * @property float $preco
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\ClientesHasBrindesHabilitado $clientes_has_brindes_habilitado
 */
class UsuariosHasBrinde extends Entity
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

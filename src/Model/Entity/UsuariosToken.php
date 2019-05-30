<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UsuariosToken Entity
 *
 * @property int $id
 * @property int $usuarios_id
 * @property string $tipo
 * @property string $token
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Usuario $usuario
 */
class UsuariosToken extends Entity
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
        'usuarios_id' => true,
        'tipo' => true,
        'token' => true,
        'audit_insert' => true,
        'audit_update' => true,
        'usuario' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'token'
    ];
}

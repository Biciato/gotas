<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * BrindesEstoque Entity
 *
 * @property int $id
 * @property int $brindes_id
 * @property int $usuarios_id
 * @property int $quantidade
 * @property int $tipo_operacao
 * @property \Cake\I18n\FrozenTime $data
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Brinde $brinde
 * @property \App\Model\Entity\Usuario $usuario
 */
class BrindesEstoque extends Entity
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

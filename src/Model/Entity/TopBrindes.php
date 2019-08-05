<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TopBrindes Entity
 *
 * @property int $id
 * @property int $redes_id
 * @property int $clientes_id
 * @property int $brindes_id
 * @property int $posicao
 * @property string $tipo
 * @property \Cake\I18n\FrozenTime $data
 * @property int $audit_user_insert_id
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Rede $rede
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Brinde $brinde
 * @property \App\Model\Entity\Usuario $usuario
 */
class TopBrindes extends Entity
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
        'redes_id' => true,
        'clientes_id' => true,
        'brindes_id' => true,
        'posicao' => true,
        'tipo' => true,
        'habilitado' => true,
        'data' => true,
        'audit_user_insert_id' => true,
        'audit_insert' => true,
        'audit_update' => true,
        'rede' => true,
        'cliente' => true,
        'brinde' => true,
        'usuario' => true
    ];
}

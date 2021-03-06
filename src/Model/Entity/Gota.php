<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Gota Entity
 *
 * @property int $id
 * @property int $clientes_id
 * @property string $nome_parametro
 * @property int $multiplicador_gota
 * @property bool $habilitado
 * @property bool $tipo_cadastro
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Cliente $cliente
 */
class Gota extends Entity
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

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CuponsTransacoes Entity
 *
 * @property int $id
 * @property int $redes_id
 * @property int $clientes_id
 * @property int $cupons_id
 * @property int $brindes_id
 * @property int $clientes_has_quadro_horario_id
 * @property int $funcionarios_id
 * @property string $tipo_operacao
 * @property \Cake\I18n\FrozenTime $data
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Rede $rede
 * @property \App\Model\Entity\Cliente $cliente
 * @property \App\Model\Entity\Cupon $cupon
 * @property \App\Model\Entity\Brinde $brinde
 * @property \App\Model\Entity\ClientesHasQuadroHorario $clientes_has_quadro_horario
 * @property \App\Model\Entity\Usuario $usuario
 */
class CuponsTransacoes extends Entity
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
        'cupons_id' => true,
        'brindes_id' => true,
        'clientes_has_quadro_horario_id' => true,
        'funcionarios_id' => true,
        'tipo_operacao' => true,
        'data' => true,
        'audit_insert' => true,
        'audit_update' => true,
        'rede' => true,
        'cliente' => true,
        'cupon' => true,
        'brinde' => true,
        'clientes_has_quadro_horario' => true,
        'usuario' => true
    ];
}

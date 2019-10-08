<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Pontuaco Entity
 *
 * @property int $id
 * @property int $clientes_id *
 * @property int $usuarios_id
 * @property int $brindes_id
 * @property int $funcionarios_id
 * @property int $gotas_id
 * @property float $quantidade_multiplicador
 * @property double $quantidade_gotas
 * @property double $valor_gota_sefaz
 * @property float $valor_moeda_venda
 * @property int $pontuacoes_comprovante_id
 * @property \Cake\I18n\FrozenTime $data
 * @property int $expirado
 * @property int $utilizado
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\Usuario $funcionario
 * @property \App\Model\Entity\Brinde $brinde
 * @property \App\Model\Entity\Gota $gota
 */
class Pontuacao extends Entity
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

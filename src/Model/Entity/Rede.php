<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;

/**
 * Rede Entity
 *
 * @property int $id
 * @property string $nome_rede
 * @property string nome_img
 * @property bool $ativado
 * @property int $tempo_expiracao_gotas_usuarios
 * @property int $quantidade_pontuacoes_usuarios_dia
 * @property int $quantidade_consumo_usuarios_dia
 * @property int $qte_mesmo_brinde_resgate_dia
 * @property int $qte_gotas_minima_bonificacao
 * @property int $qte_gotas_bonificacao
 * @property string $propaganda_img
 * @property string $propaganda_link
 * @property float $custo_referencia_gotas
 * @property int $media_assiduidade_clientes
 * @property bool $app_personalizado
 * @property bool $msg_distancia_compra_brinde
 * @property bool $pontuacao_extra_produto_generico
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 */
class Rede extends Entity
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

    /**
     * ------------------------------------------------------------------------------------------
     * Propriedades Virtuais
     * ------------------------------------------------------------------------------------------
     */

    protected $_virtual = array("nome_img_completo", "propaganda_img_completo");

    protected function _getNomeImgCompleto()
    {
        if (!empty($this->_properties["nome_img"]) && (strlen($this->_properties["nome_img"]) > 0)) {
            return sprintf("%s%s%s%s", __SERVER__, PATH_WEBROOT, PATH_IMAGES_REDES, $this->_properties["nome_img"]);
        }

        return null;
    }

    protected function _getPropagandaImgCompleto()
    {
        if (!empty($this->_properties["propaganda_img"]) && strlen($this->_properties["propaganda_img"]) > 0) {
            return sprintf("%s%s%s%s", __SERVER__, PATH_WEBROOT, PATH_IMAGES_REDES, $this->_properties["propaganda_img"]);
        }

        return null;
    }
}

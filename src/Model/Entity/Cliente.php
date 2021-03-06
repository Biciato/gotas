<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Core\Configure;

/**
 * Cliente Entity
 *
 * @property int $id
 * @property int $matriz
 * @property int $tipo_unidade
 * @property string $nome_fantasia
 * @property string $razao_social
 * @property string $cnpj
 * @property string $endereco
 * @property int $endereco_numero
 * @property string $endereco_complemento
 * @property string $bairro
 * @property string $municipio
 * @property string $estado
 * @property string $cep
 * @property string $tel_fixo
 * @property string $tel_fax
 * @property string $tel_celular
 * @property string $propaganda_link
 * @property string $propaganda_img
 * @property int $impressao_sw_linha_continua
 * @property string $delimitador_nota_produtos_inicial
 * @property string $delimitador_nota_produtos_final
 * @property string $delimitador_nota_impressao
 * @property string $delimitador_qr_code
 * @property \Cake\I18n\FrozenTime $audit_insert
 * @property \Cake\I18n\FrozenTime $audit_update
 *
 * @property \App\Model\Entity\Matriz $matriz
 */
class Cliente extends Entity
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

    protected $_virtual = [
        "propaganda_img_completo",
        "nome_fantasia_municipio_estado"
    ];


    protected function _getPropagandaImgCompleto()
    {
        if (isset($this->_properties["propaganda_img"]) && strlen($this->_properties["propaganda_img"]) > 0) {
            return sprintf("%s%s%s%s", __SERVER__, PATH_WEBROOT, PATH_IMAGES_CLIENTES, $this->_properties["propaganda_img"]);
        }

        return "";
        // return $this->_properties["propaganda_img"];
    }

    protected function _getNomeFantasiaMunicipioEstado()
    {
        $nomeFantasia = !empty($this->_properties["nome_fantasia"]) ? $this->_properties["nome_fantasia"] : null;
        $municipio = !empty($this->_properties["municipio"]) ? $this->_properties["municipio"] : null;
        $estado = !empty($this->_properties["estado"]) ? $this->_properties["estado"] : null;

        $retorno = $nomeFantasia;

        if (!empty($municipio)) {
            $retorno .= " / " . $municipio;
        }

        if (!empty($estado)) {
            $retorno .= " / " . $estado;
        }

        return $retorno;
    }
}

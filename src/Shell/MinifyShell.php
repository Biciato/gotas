<?php

/**
 * Arquivo para Classe para execução em terminal (shell)
 *
 * @category Class
 * @package  App\Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/
 */

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Event\Event;
use MatthiasMullie\Minify;

/**
 * Classe para execução em terminal (shell)
 *
 * @category ClasseDeExecucaoBackground
 * @package  Shell
 * @author   "Gustavo Souza Gonçalves <gustavosouzagoncalves@outlook.com>"
 * @date     05/08/2017
 * @license  http://www.apache.org/licenses/LICENSE-2.0.txt Apache License 2.0
 * @link     https://www.rtibrindes.com.br/Shell/ClasseDeExecucaoBackground
 */
class MinifyShell extends ExtendedShell
{
    // lista dos arquivos em js e seu respectivo destino
    protected $js_files = [
        [
            'brindes' => [
                'brindes_filtro_outros_ajax',
                'brindes_filtro_shower_ajax',
                'brindes_form',
                'brindes_filtro_pesquisa_comum',
                'filtro_brindes_relatorio',
                'impressao_rapida'
            ]
        ],
        [
            'clientes' => [
                'clientes_form',
                'filtro_clientes'
            ]
        ],
        [
            'clientes_has_brindes_habilitados_preco' => [
                'filtro_relatorio_historico_preco_brindes_redes_detalhado',
                'preco_brinde_form',
            ]
        ],
        [
            'clientes_has_brindes_habilitados_estoque' => [
                'filtro_relatorio_estoque_brindes_detalhado',
            ]
        ],
        [
            'cupons' => [
                'historico_brindes',
                'impressao_cupom_layout',
                'imprime_brinde_comum',
                'imprime_brinde_shower',
                'imprimir_brinde_comum',
                'reimpressao_shower_modal',
                'resgate_cupom_canhoto_confirmacao',
                'resgate_cupom_form'
            ]
        ],
        [
            'gotas' => [
                'atribuir_gotas_form',
                'filtro_gotas_relatorio',
                'gotas_config_input_form',
                'gotas_input_form_com_ocr',
                'gotas_input_form_sem_ocr',
                'gotas_minha_rede'
            ]
        ],
        [
            'pages' => [
                'buscar',
                'dashboard_funcionario',
                'home',
                'modal_confirm_purchase_with_message'
            ]
        ],
        [
            'pontuacoes' => [
                'alterar_cliente_pontuacao',
                'editar_pontuacao',
                'filtro_cupons'
            ]
        ],
        [
            'pontuacoes_comprovantes' => [
                'pesquisar_cliente_final_pontuacoes'
            ]
        ],
        [
            'redes' => [
                'filtro_redes_relatorio',
                'filtro_relatorio_usuarios_redes',
            ]
        ],
        array(
            "tipos_brindes_redes" => array(
                "form_tipos_brindes_redes"
            )
        ),
        array(
            "tipos_brindes_clientes" => array(
                "form_tipos_brindes_clientes"
            )
        ),
        [
            'transportadoras' => [
                'filtro_relatorio_transportadoras_usuarios_redes',
                'transportadoras_form'
            ]
        ],
        [
            'usuarios' => [
                'add',
                'edit',
                'filtro_relatorio_usuarios_cadastrados',
                'filtro_usuarios_ajax',
                'filtro_usuarios_venda_avulsa_ajax',
                'login',
                'pesquisar_cliente_alterar_dados',
                'reativar_conta',
                'senha_modal'
            ]
        ],
        [
            'usuarios_has_brindes' => [
                'filtro_relatorio_brindes_usuarios_redes',
                'pesquisar_cliente_final_brindes'
            ]
        ],
        [
            'util' => [
                'pdf417_helper'
            ]
        ],
        [
            'veiculos' => [
                'filtro_relatorio_veiculos_usuarios_redes',
                'form_cadastrar_veiculo',
                'general'
            ]
        ]

    ];

    protected $css_files = [
        [
            'common' =>
                [
                'loader',
            ]
        ],
        [
            'brindes' => [
                'brinde_desativado',
                'brindes_filtro_shower_ajax',
                'impressao_rapida'
            ]
        ],
        [
            'clientesHasBrindesHabilitados' =>
                [
                'clientesHasBrindesHabilitados'
            ]
        ],
        [
            'cupons' => [
                'escolher_brinde',
                'impressao_comum_layout_canhoto',
                'impressao_cupom_layout',
                'impressao_shower_layout',
                'imprime_brinde_comum',
                'imprime_brinde_shower',
                'resgate_cupom_canhoto_confirmacao',
                'resgate_cupom_canhoto_impressao',
                'resgate_cupom_form'
            ]
        ],
        [
            'gotas' => [
                'atribuir_gotas_form',
                'gotas_input_form_com_ocr',
                'gotas_input_form_sem_ocr'
            ]
        ],
        [
            'pages' => [
                'buscar',
                'dashboard_funcionario',
                'escolher_unidade_rede',
                'modal_buscar_results'
            ]
        ],
        [
            'pontuacoes' => [
                'detalhes_cupom',
            ]
        ],
        [
            'transportadoras' => [
                'add'
            ]
        ],
        [
            'usuarios' => [
                'filtro_usuarios_ajax',
                'filtro_usuarios_venda_avulsa_ajax',
            ]
        ],

    ];

    protected $js_base_dir = WWW_ROOT . '/js/scripts/';
    protected $js_extension_debug = '.js';
    protected $js_extension_minify = '.min.js';

    protected $css_base_dir = WWW_ROOT . '/css/styles/';
    protected $css_extension_debug = '.css';
    protected $css_extension_minify = '.min.css';


    /**
     * Método de inicialização
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Gera relatório diário e envia para cada administrador de cada loja/matriz
     *
     * @return void
     */
    public function main()
    {
        try {
            $this->out("Iniciando minify de arquivos css...");

            foreach ($this->css_files as $key => $array_item) {
                $name_array = key($array_item) . '/';

                foreach ($array_item as $key => $item) {
                    foreach ($item as $key => $value) {
                        $origin = $this->css_base_dir . $name_array . $value . $this->css_extension_debug;
                        $minifier = new Minify\CSS($origin);

                        $destination = $this->css_base_dir . $name_array . $value . $this->css_extension_minify;

                        // $minifier->minify($destination);
                        $minified = $minifier->minify();

                        file_put_contents($destination, $minified);
                        $this->out('Arquivo de build: ' . $origin);

                        $this->out('Arquivo reduzido: ' . $destination);
                    }
                }
            }

            $this->out('Finalizado minify de arquivos css...');

            $this->out("Iniciando minify de arquivos js...");

            foreach ($this->js_files as $key => $array_item) {
                $name_array = key($array_item) . '/';

                foreach ($array_item as $key => $item) {
                    foreach ($item as $key => $value) {
                        $origin = $this->js_base_dir . $name_array . $value . $this->js_extension_debug;
                        $minifier = new Minify\JS($origin);

                        $destination = $this->js_base_dir . $name_array . $value . $this->js_extension_minify;

                        $minifier->minify($destination);
                        $this->out('Arquivo de build: ' . $origin);

                        $this->out('Arquivo reduzido: ' . $destination);
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
}

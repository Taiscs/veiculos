<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CarroModel;
use App\Models\ModeloModel;
use App\Models\LocacaoModel;
use App\Models\ManutencaoModel;

class Carros extends BaseController
{
    /**
     * Exibe a listagem principal de carros
     * URL: http://localhost/veiculos/public/carros
     */
    public function index()
    {
        $carroModel  = new CarroModel();
        $modeloModel = new ModeloModel();

        $data['carros']  = $carroModel->getCarrosComModelo();
        $data['modelos'] = $modeloModel->findAll();

        return view('carros/index', $data);
    }

    /**
     * Retorna os detalhes do veículo via JSON para requisições AJAX (Modal de Histórico)
     * URL: http://localhost/veiculos/public/carros/detalhes/{id}
     */
    public function detalhes($id = null)
    {
        // Garante cabeçalho JSON
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'mensagem' => 'ID do veículo não informado.'
            ]);
        }

        try {
            $carroModel      = new CarroModel();
            $aluguelModel    = new LocacaoModel();
            $manutencaoModel = new ManutencaoModel();

            // Busca os dados cadastrais do carro
            $carroData = $carroModel->getCarrosComModelo($id);

            if (!$carroData) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'mensagem' => 'Veículo não encontrado.'
                ]);
            }

            // Trata se o método getCarrosComModelo($id) retornar uma lista [0 => ...] em vez de elemento único
            $carro = isset($carroData[0]) ? $carroData[0] : $carroData;

            // Busca histórico de locações (trata retorno nulo)
            $locacoes = [];
            try {
                $locacoes = $aluguelModel->where('carro_id', $id)
                                         ->orderBy('id', 'DESC') // Ordenação segura por ID caso data_inicio varie
                                         ->findAll(5) ?? [];
            } catch (\Throwable $eLoc) {
                log_message('error', 'Erro ao buscar locações: ' . $eLoc->getMessage());
            }

            // Busca histórico de manutenções (trata retorno nulo)
            $manutencoes = [];
            try {
                $manutencoes = $manutencaoModel->where('carro_id', $id)
                                               ->orderBy('id', 'DESC') // Ordenação segura por ID
                                               ->findAll(5) ?? [];
            } catch (\Throwable $eMan) {
                log_message('error', 'Erro ao buscar manutenções: ' . $eMan->getMessage());
            }

            return $this->response->setJSON([
                'status'      => 'success',
                'carro'       => $carro,
                'locacoes'    => $locacoes,
                'manutencoes' => $manutencoes
            ]);

        } catch (\Throwable $e) {
            // Em caso de erro de SQL ou banco, registra no log e envia detalhamento na resposta JSON
            log_message('error', 'Erro no método detalhes: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'status'   => 'error',
                'mensagem' => 'Erro interno no servidor: ' . $e->getMessage()
            ]);
        }
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ManutencaoModel;
use App\Models\CarroModel;

class Manutencoes extends BaseController
{
    protected $manutencaoModel;
    protected $carroModel;

    public function __construct()
    {
        $this->manutencaoModel = new ManutencaoModel();
        $this->carroModel      = new CarroModel();
    }

    public function index()
    {
        $data = [
            'titulo'      => 'Ordens de Serviço / Manutenção',
            'manutencoes' => $this->manutencaoModel->getManutencoesComCarro(),
            'carros'      => $this->carroModel->getCarrosComModelo(),
        ];

        return view('manutencoes/index', $data);
    }

    public function salvar()
    {
        $id      = $this->request->getPost('id');
        $carroId = $this->request->getPost('carro_id');
        $status  = $this->request->getPost('status') ?? 'aberta';

        $dados = [
            'carro_id'            => $carroId,
            'mecanica_oficina'    => $this->request->getPost('mecanica_oficina'),
            'descricao_problema'  => $this->request->getPost('descricao_problema'),
            'servico_realizado'   => $this->request->getPost('servico_realizado'),
            'data_entrada'        => $this->request->getPost('data_entrada'),
            'data_saida_prevista' => $this->request->getPost('data_saida_prevista'),
            'valor_total'         => $this->request->getPost('valor_total') ?? 0.00,
            'status'              => $status,
        ];

        if ($id) {
            $this->manutencaoModel->update($id, $dados);
            $msg = 'Ordem de serviço atualizada!';
        } else {
            $this->manutencaoModel->insert($dados);
            // Atualiza status do veículo para manutenção
            $this->carroModel->update($carroId, ['status' => 'manutencao']);
            $msg = 'Ordem de serviço aberta com sucesso!';
        }

        return redirect()->to(base_url('manutencoes'))->with('sucesso', $msg);
    }

    public function finalizar($id = null)
    {
        $os = $this->manutencaoModel->find($id);

        if ($os) {
            // Finaliza a O.S.
            $this->manutencaoModel->update($id, [
                'status'          => 'concluida',
                'data_saida_real' => date('Y-m-d')
            ]);

            // Libera o carro para "disponivel"
            $this->carroModel->update($os['carro_id'], ['status' => 'disponivel']);

            return redirect()->to(base_url('manutencoes'))->with('sucesso', 'O.S. finalizada e veículo liberado!');
        }

        return redirect()->to(base_url('manutencoes'))->with('erro', 'Ordem de serviço não encontrada.');
    }
}
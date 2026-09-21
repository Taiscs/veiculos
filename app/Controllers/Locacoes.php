<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LocacaoModel;
use App\Models\CarroModel;
use App\Models\UsuarioModel;

class Locacoes extends BaseController
{
    protected $locacaoModel;
    protected $carroModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->locacaoModel = new LocacaoModel();
        $this->carroModel   = new CarroModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo'    => 'Gestão de Locações',
            'locacoes'  => $this->locacaoModel->getLocacoesComDetalhes(),
            'clientes'  => $this->usuarioModel->where('tipo', 'cliente')->findAll(),
            'vendedores'=> $this->usuarioModel->where('tipo', 'vendedor')->findAll(),
            // Traz apenas carros disponíveis para novas locações
            'carros'    => $this->carroModel->getCarrosComModelo(),
        ];

        return view('locacoes/index', $data);
    }

    public function salvar()
    {
        $id          = $this->request->getPost('id');
        $carroId     = $this->request->getPost('carro_id');
        $dataInicio  = $this->request->getPost('data_inicio');
        $dataFim     = $this->request->getPost('data_fim_prevista');
        $valorDiaria = $this->request->getPost('valor_diaria');

        // Cálculo de dias
        $d1 = new \DateTime($dataInicio);
        $d2 = new \DateTime($dataFim);
        $dias = $d1->diff($d2)->days;
        $dias = $dias == 0 ? 1 : $dias;

        $valorTotal = $dias * $valorDiaria;

        $dados = [
            'cliente_id'        => $this->request->getPost('cliente_id'),
            'vendedor_id'       => $this->request->getPost('vendedor_id'),
            'carro_id'          => $carroId,
            'data_inicio'       => $dataInicio,
            'data_fim_prevista' => $dataFim,
            'valor_diaria'      => $valorDiaria,
            'valor_total'       => $valorTotal,
            'status'            => $this->request->getPost('status') ?? 'ativa',
            'observacoes'       => $this->request->getPost('observacoes'),
        ];

        if ($id) {
            $this->locacaoModel->update($id, $dados);
            $msg = 'Locação atualizada com sucesso!';
        } else {
            $this->locacaoModel->insert($dados);
            // Atualiza status do carro para "manutencao" ou reservado se necessário
            $this->carroModel->update($carroId, ['status' => 'manutencao']);
            $msg = 'Locação realizada com sucesso!';
        }

        return redirect()->to(base_url('locacoes'))->with('sucesso', $msg);
    }

    public function finalizar($id = null)
    {
        $locacao = $this->locacaoModel->find($id);

        if ($locacao) {
            // Finaliza locação
            $this->locacaoModel->update($id, [
                'status'         => 'concluida',
                'data_devolucao' => date('Y-m-d')
            ]);

            // Libera o carro para disponível novamente
            $this->carroModel->update($locacao['carro_id'], ['status' => 'disponivel']);

            return redirect()->to(base_url('locacoes'))->with('sucesso', 'Locação finalizada e veículo liberado!');
        }

        return redirect()->to(base_url('locacoes'))->with('erro', 'Locação não encontrada.');
    }
}
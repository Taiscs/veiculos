<?php

namespace App\Controllers;

use App\Models\ModeloModel;

class Modelos extends BaseController
{
    protected $modeloModel;

    public function __construct()
    {
        $this->modeloModel = new ModeloModel();
    }

    public function index()
    {
        $data['modelos'] = $this->modeloModel->orderBy('marca', 'ASC')->findAll();
        return view('modelos/index', $data);
    }

    public function salvar()
    {
        $id = $this->request->getPost('id');

        $dados = [
            'marca' => $this->request->getPost('marca'),
            'nome'  => $this->request->getPost('nome'),
        ];

        if (!empty($id)) {
            $this->modeloModel->update($id, $dados);
            session()->setFlashdata('success', 'Modelo atualizado com sucesso!');
        } else {
            $this->modeloModel->insert($dados);
            session()->setFlashdata('success', 'Modelo cadastrado com sucesso!');
        }

        return redirect()->to('/modelos');
    }

    public function excluir($id)
    {
        $db = \Config\Database::connect();
        
        // Verifica se existem carros associados a esse modelo antes de excluir
        $carrosVinculados = $db->table('carros')->where('modelo_id', $id)->countAllResults();

        if ($carrosVinculados > 0) {
            session()->setFlashdata('error', 'Não é possível excluir! Existem carros vinculados a este modelo.');
            return redirect()->to('/modelos');
        }

        $this->modeloModel->delete($id);
        session()->setFlashdata('success', 'Modelo excluído com sucesso!');
        return redirect()->to('/modelos');
    }
}
<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Pessoas extends BaseController
{
    protected $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    // --- CLIENTES (CONDUTORES) ---
    public function clientes()
    {
        $data['clientes'] = $this->usuarioModel->getPorTipo('condutor');
        return view('pessoas/clientes', $data);
    }

    public function salvarCliente()
    {
        $id = $this->request->getPost('id');
        $senha = $this->request->getPost('senha');

        $dados = [
            'nome'  => $this->request->getPost('nome'),
            'email' => $this->request->getPost('email'),
            'cpf'   => $this->request->getPost('cpf'),
            'cnh'   => $this->request->getPost('cnh'),
            'tipo'  => 'condutor',
        ];

        if (!empty($senha)) {
            $dados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        if (!empty($id)) {
            $this->usuarioModel->update($id, $dados);
            session()->setFlashdata('success', 'Cliente atualizado com sucesso!');
        } else {
            if (empty($senha)) {
                $dados['senha'] = password_hash('123456', PASSWORD_DEFAULT); // Senha padrão se vazia
            }
            $this->usuarioModel->insert($dados);
            session()->setFlashdata('success', 'Cliente cadastrado com sucesso!');
        }

        return redirect()->to('/clientes');
    }

    // --- VENDEDORES (FUNCIONÁRIOS) ---
    public function vendedores()
    {
        $db = \Config\Database::connect();
        $data['vendedores'] = $this->usuarioModel->getPorTipo('funcionario');
        $data['perfis']     = $db->table('perfis')->get()->getResultArray();

        return view('pessoas/vendedores', $data);
    }

    public function salvarVendedor()
    {
        $id = $this->request->getPost('id');
        $senha = $this->request->getPost('senha');

        $dados = [
            'nome'      => $this->request->getPost('nome'),
            'email'     => $this->request->getPost('email'),
            'cpf'       => $this->request->getPost('cpf'),
            'perfil_id' => $this->request->getPost('perfil_id'),
            'tipo'      => 'funcionario',
        ];

        if (!empty($senha)) {
            $dados['senha'] = password_hash($senha, PASSWORD_DEFAULT);
        }

        if (!empty($id)) {
            $this->usuarioModel->update($id, $dados);
            session()->setFlashdata('success', 'Vendedor atualizado com sucesso!');
        } else {
            if (empty($senha)) {
                $dados['senha'] = password_hash('123456', PASSWORD_DEFAULT);
            }
            $this->usuarioModel->insert($dados);
            session()->setFlashdata('success', 'Vendedor cadastrado com sucesso!');
        }

        return redirect()->to('/vendedores');
    }

    public function excluir($id, $destino)
    {
        $this->usuarioModel->delete($id);
        session()->setFlashdata('success', 'Registro excluído com sucesso!');
        return redirect()->to('/' . $destino);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        // Se já estiver logado, redireciona para a tela do BI / Dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('bi'));
        }

        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $db      = \Config\Database::connect();

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        if (empty($email) || empty($senha)) {
            $session->setFlashdata('error', 'Preencha todos os campos.');
            return redirect()->back()->withInput();
        }

        // Busca o usuário na tabela 'usuarios'
        $usuario = $db->table('usuarios')
                      ->where('email', $email)
                      ->get()
                      ->getRowArray();

        if ($usuario) {
            // Verifica a hash da senha (ou texto puro caso seja legado)
            $senhaValida = password_verify($senha, $usuario['senha']) || ($senha === $usuario['senha']);

            if ($senhaValida) {

                // Busca as chaves das telas permitidas vinculadas ao perfil do usuário
                $permissoesQuery = [];
                if (!empty($usuario['perfil_id'])) {
                    $permissoesQuery = $db->table('perfil_permissoes pp')
                                         ->select('t.chave')
                                         ->join('telas t', 't.id = pp.tela_id')
                                         ->where('pp.perfil_id', $usuario['perfil_id'])
                                         ->get()
                                         ->getResultArray();
                }

                // Extrai apenas a coluna 'chave' para montar o array (ex: ['cad_carro', 'relatorio_powerbi', ...])
                $permissoes = array_column($permissoesQuery, 'chave');

                // Monta os dados da sessão do usuário
                $sessionData = [
                    'id'         => $usuario['id'],
                    'nome'       => $usuario['nome'],
                    'email'      => $usuario['email'],
                    'tipo'       => $usuario['tipo'] ?? 'usuario',
                    'perfil_id'  => $usuario['perfil_id'] ?? null,
                    'permissoes' => $permissoes,
                    'isLoggedIn' => true,
                ];

                $session->set($sessionData);

                // Redireciona diretamente para o BI
                return redirect()->to(site_url('bi'));
            }
        }

        // Caso o usuário não seja encontrado ou a senha seja inválida
        $session->setFlashdata('error', 'E-mail ou senha incorretos.');
        return redirect()->back()->withInput();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}

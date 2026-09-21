<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        // Se já estiver logado, vai para o BI
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/bi');
        }

        return view('auth/login');
    }

    public function login()
    {
        $session = session();

        $email = trim((string) $this->request->getPost('email'));
        $senha = (string) $this->request->getPost('senha');

        if ($email === '' || $senha === '') {
            $session->setFlashdata('error', 'Preencha todos os campos.');

            return redirect()->back()->withInput();
        }

        $db = \Config\Database::connect();

        // Busca o usuário
        $usuario = $db->table('usuarios')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        // Verifica usuário e senha
        if ($usuario && password_verify($senha, $usuario['senha'])) {

            /*
             * Busca as permissões do perfil do usuário.
             *
             * perfil_permissoes:
             * perfil_id -> tela_id
             *
             * telas:
             * id -> chave
             */
            $permissoes = $db->table('perfil_permissoes pp')
                ->select('t.chave')
                ->join('telas t', 't.id = pp.tela_id')
                ->where('pp.perfil_id', $usuario['perfil_id'])
                ->get()
                ->getResultArray();

            $listaPermissoes = array_column($permissoes, 'chave');

            // Cria a sessão no formato esperado pelo PermissionFilter
            $session->set([
                'usuario_id' => $usuario['id'],
                'nome'       => $usuario['nome'],
                'email'      => $usuario['email'],
                'tipo'       => $usuario['tipo'],
                'perfil_id'  => $usuario['perfil_id'],

                'isLoggedIn' => true,
                'permissoes' => $listaPermissoes,
            ]);

            // Login concluído -> BI
            return redirect()->to('/bi');
        }

        $session->setFlashdata(
            'error',
            'E-mail ou senha incorretos.'
        );

        return redirect()->to('/login')->withInput();
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}

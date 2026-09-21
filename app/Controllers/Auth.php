<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    public function index()
    {
        // Se já estiver logado, redireciona diretamente para o BI
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/bi');
        }

        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        $usuario = $model->where('email', $email)->first();

        if ($usuario) {
            if (password_verify($senha, $usuario['senha'])) {
                // Guarda os dados na sessão
                    $sessionData = [
                        'id'          => $usuario['id'],
                        'nome'        => $usuario['nome'],
                        'email'       => $usuario['email'],
                        'tipo'        => $usuario['tipo'] ?? 'usuario',
                        'perfil_id'   => $usuario['perfil_id'] ?? null,
                        'permissoes'  => $usuario['permissoes'] ?? [], // Retorna array vazio se não existir a chave
                        'isLoggedIn'  => true,
                    ];

                $session->set($sessionData);

                // Redireciona para a rota do BI logo após o login de sucesso
                return redirect()->to('/bi');
            }
        }

        $session->setFlashdata('error', 'E-mail ou senha incorretos.');
        return redirect()->to('/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
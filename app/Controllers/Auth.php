<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        // Se já estiver logado, manda para o sistema
        if (session()->get('logado')) {
            return redirect()->to('/');
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

        // Busca o usuário pelo e-mail
        $usuario = $db->table('usuarios')
            ->where('email', $email)
            ->get()
            ->getRowArray();

        // Verifica se o usuário existe e se a senha confere
        if ($usuario && password_verify($senha, $usuario['senha'])) {

            $session->set([
                'usuario_id' => $usuario['id'],
                'nome'       => $usuario['nome'],
                'email'      => $usuario['email'],
                'tipo'       => $usuario['tipo'],
                'perfil_id'  => $usuario['perfil_id'],
                'logado'     => true,
            ]);

            return redirect()->to('/');
        }

        $session->setFlashdata('error', 'E-mail ou senha incorretos.');

        return redirect()->back()->withInput();
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}

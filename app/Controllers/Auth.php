<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function index()
    {
        // Se já estiver logado, redireciona para o BI
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('bi'));
        }

        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $db = \Config\Database::connect();

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        if (empty($email) || empty($senha)) {
            $session->setFlashdata(
                'error',
                'Preencha todos os campos.'
            );

            return redirect()->back()->withInput();
        }

        // Busca o usuário na tabela usuarios
        $usuario = $db->table('usuarios')
                      ->where('email', $email)
                      ->get()
                      ->getRowArray();

        /*
         * TESTE TEMPORÁRIO
         * Vamos descobrir se:
         * 1. O usuário foi encontrado
         * 2. A hash possui 60 caracteres
         * 3. password_verify() reconhece a senha
         */
        if (!$usuario) {
            die('TESTE: USUARIO NAO ENCONTRADO');
        }

        echo 'TESTE: USUARIO ENCONTRADO<br>';

        echo 'Email banco: '
            . htmlspecialchars($usuario['email'])
            . '<br>';

        echo 'Tamanho hash: '
            . strlen($usuario['senha'])
            . '<br>';

        echo 'password_verify: '
            . (
                password_verify(
                    $senha,
                    $usuario['senha']
                )
                    ? 'SIM'
                    : 'NAO'
            );

        die();

        /*
         * Daqui para baixo permanece o login normal.
         * Durante este teste ele não será executado
         * por causa do die() acima.
         */
        if ($usuario) {

            $senhaValida =
                password_verify(
                    $senha,
                    $usuario['senha']
                )
                || ($senha === $usuario['senha']);

            if ($senhaValida) {

                // Busca as telas permitidas para o perfil
                $permissoesQuery = [];

                if (!empty($usuario['perfil_id'])) {

                    $permissoesQuery =
                        $db->table('perfil_permissoes pp')
                           ->select('t.chave')
                           ->join(
                               'telas t',
                               't.id = pp.tela_id'
                           )
                           ->where(
                               'pp.perfil_id',
                               $usuario['perfil_id']
                           )
                           ->get()
                           ->getResultArray();
                }

                // Extrai somente as chaves das telas
                $permissoes = array_column(
                    $permissoesQuery,
                    'chave'
                );

                // Dados da sessão
                $sessionData = [
                    'id'         => $usuario['id'],
                    'nome'       => $usuario['nome'],
                    'email'      => $usuario['email'],
                    'tipo'       => $usuario['tipo']
                                    ?? 'usuario',
                    'perfil_id'  => $usuario['perfil_id']
                                    ?? null,
                    'permissoes' => $permissoes,
                    'isLoggedIn' => true,
                ];

                $session->set($sessionData);

                return redirect()->to(site_url('bi'));
            }
        }

        $session->setFlashdata(
            'error',
            'E-mail ou senha incorretos.'
        );

        return redirect()->back()->withInput();
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }
}

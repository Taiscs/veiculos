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
        /*
         * TESTE TEMPORÁRIO
         *
         * Gera uma nova hash usando o próprio PHP
         * que está rodando na Render.
         *
         * Senha temporária: 12345678
         */

        $novaHash = password_hash(
            '12345678',
            PASSWORD_DEFAULT
        );

        echo 'Nova hash:<br><br>';

        echo htmlspecialchars($novaHash);

        echo '<br><br>';

        echo 'Tamanho da hash: '
            . strlen($novaHash);

        echo '<br><br>';

        echo 'Teste imediato: ';

        echo password_verify(
            '12345678',
            $novaHash
        )
            ? 'SIM'
            : 'NAO';

        die();
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }
}

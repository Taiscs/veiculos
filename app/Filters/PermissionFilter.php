<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Verifica se o usuário está logado
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Por favor, faça login para acessar.');
        }

        // 2. Se a rota exigir uma permissão específica
        if (!empty($arguments[0])) {
            $permissaoRequerida = $arguments[0];
            $permissoesUsuario  = $session->get('permissoes') ?? [];

            // Se for do tipo 'admin', possui acesso irrestrito
            if ($session->get('tipo') === 'admin') {
                return;
            }

            // Verifica se a chave de permissão existe na sessão do usuário
            if (!in_array($permissaoRequerida, $permissoesUsuario)) {
                return redirect()->to('/dashboard')->with('error', 'Você não tem permissão para acessar esta tela.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nada a executar após a requisição
    }
}
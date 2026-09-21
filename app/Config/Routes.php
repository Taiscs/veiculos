<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Home;
use App\Controllers\Auth;
use App\Controllers\Carros;     // Adicionado (faltava esta importação!)
use App\Controllers\Modelos;
use App\Controllers\Bi;
use App\Controllers\Pessoas;
use App\Controllers\Locacoes;
use App\Controllers\Manutencoes;

/** @var RouteCollection $routes */
$routes->get('/', [Home::class, 'index']);

// Rotas de Autenticação (Públicas)
$routes->get('login', [Auth::class, 'index']);
$routes->post('login', [Auth::class, 'login']);
$routes->get('logout', [Auth::class, 'logout']);

// Exemplo de Rotas Protegidas por Permissão
$routes->group('', ['filter' => 'permission'], function($routes) {
    // Dashboard
    $routes->get('dashboard', [Home::class, 'index']);

    // Rotas de Modelos
    $routes->get('modelos', [Modelos::class, 'index'], ['filter' => 'permission:cad_modelo']);
    $routes->post('modelos/salvar', [Modelos::class, 'salvar'], ['filter' => 'permission:cad_modelo']);
    $routes->get('modelos/excluir/(:num)', [Modelos::class, 'excluir'], ['filter' => 'permission:cad_modelo']);

    // BI / Relatórios
    $routes->get('bi', [Bi::class, 'index'], ['filter' => 'permission:relatorio_financeiro']);
    $routes->get('relatorios/bi', [Bi::class, 'index'], ['filter' => 'permission:relatorio_financeiro']);

    // Clientes
    $routes->get('clientes', [Pessoas::class, 'clientes'], ['filter' => 'permission:cad_cliente']);
    $routes->get('pessoas/clientes', [Pessoas::class, 'clientes'], ['filter' => 'permission:cad_cliente']);
    $routes->post('clientes/salvar', [Pessoas::class, 'salvarCliente'], ['filter' => 'permission:cad_cliente']);

    // Vendedores
    $routes->get('vendedores', [Pessoas::class, 'vendedores'], ['filter' => 'permission:cad_vendedor']);
    $routes->get('pessoas/vendedores', [Pessoas::class, 'vendedores'], ['filter' => 'permission:cad_vendedor']);
    $routes->post('vendedores/salvar', [Pessoas::class, 'salvarVendedor'], ['filter' => 'permission:cad_vendedor']);

    // Exclusão genérica de pessoas
    $routes->get('pessoas/excluir/(:num)/(:segment)', [Pessoas::class, 'excluir']);

    // Frota de Carros (Incluindo a rota da Modal Executiva)
    $routes->get('carros', [Carros::class, 'index'], ['filter' => 'permission:cad_carro']);
    $routes->post('carros/salvar', [Carros::class, 'salvar'], ['filter' => 'permission:cad_carro']);
    $routes->get('carros/excluir/(:num)', [Carros::class, 'excluir'], ['filter' => 'permission:cad_carro']);
    $routes->get('carros/detalhes/(:num)', [Carros::class, 'detalhes']);

    // Rotas de Locações
    $routes->get('locacoes', [Locacoes::class, 'index'], ['filter' => 'permission:cad_locacao']);
    $routes->post('locacoes/salvar', [Locacoes::class, 'salvar'], ['filter' => 'permission:cad_locacao']);
    $routes->get('locacoes/finalizar/(:num)', [Locacoes::class, 'finalizar'], ['filter' => 'permission:cad_locacao']);

    // Rotas de Manutenção (O.S.)
    $routes->get('manutencoes', [Manutencoes::class, 'index'], ['filter' => 'permission:cad_os']);
    $routes->post('manutencoes/salvar', [Manutencoes::class, 'salvar'], ['filter' => 'permission:cad_os']);
    $routes->get('manutencoes/finalizar/(:num)', [Manutencoes::class, 'finalizar'], ['filter' => 'permission:cad_os']);
});
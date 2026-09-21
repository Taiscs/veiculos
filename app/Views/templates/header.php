<?php
$session = session();
$permissoes = $session->get('permissoes') ?? [];
$isAdmin = $session->get('tipo') === 'admin';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand font-weight-bold" href="<?= base_url('bi') ?>">🚗 Locadora</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Dashboard / BI (Visível para quem tem acesso a relatorio_financeiro ou admin) -->
                <?php if ($isAdmin || in_array('relatorio_financeiro', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('bi') ?>">📊 Dashboard / BI</a>
                    </li>
                <?php endif; ?>

                <!-- Cadastro de Modelos -->
                <?php if ($isAdmin || in_array('cad_modelo', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('modelos') ?>">🏷️ Modelos</a>
                    </li>
                <?php endif; ?>

                <!-- Cadastro de Carros -->
                <?php if ($isAdmin || in_array('cad_carro', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('carros') ?>">🚘 Frota de Carros</a>
                    </li>
                <?php endif; ?>

                <!-- Clientes / Condutores -->
                <?php if ($isAdmin || in_array('cad_cliente', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('clientes') ?>">👤 Clientes</a>
                    </li>
                <?php endif; ?>

                <!-- Vendedores / Funcionários -->
                <?php if ($isAdmin || in_array('cad_vendedor', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('vendedores') ?>">👔 Vendedores</a>
                    </li>
                <?php endif; ?>

                <!-- Aluguéis -->
                <?php if ($isAdmin || in_array('locacoes', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('locacoes') ?>">🔑 Locações</a>
                    </li>
                <?php endif; ?>

                <!-- Manutenção / Ordem de Serviço -->
                <?php if ($isAdmin || in_array('manutencoes', $permissoes)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('manutencoes') ?>">🛠️ Manutenção (O.S)</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Usuário Logado e Botão Sair -->
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Olá, <strong><?= esc($session->get('nome')) ?></strong></span>
                <a class="btn btn-outline-light btn-sm" href="<?= base_url('logout') ?>">Sair</a>
            </div>
        </div>
    </div>
</nav>
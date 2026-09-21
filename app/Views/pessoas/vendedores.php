<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Vendedores - Gestão de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?= view('templates/header') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Cadastro de Vendedores / Funcionários</h2>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">Voltar ao Dashboard</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulário de Vendedor -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">Novo Vendedor</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('vendedores/salvar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" id="vendedor_id">

                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" id="nome" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">E-mail (Login do Sistema)</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">CPF</label>
                            <input type="text" name="cpf" id="cpf" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Perfil de Acesso (Telas Permitidas)</label>
                            <select name="perfil_id" id="perfil_id" class="form-select" required>
                                <option value="">Selecione um Perfil...</option>
                                <?php foreach ($perfis as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= esc($p['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Senha de Acesso</label>
                            <input type="password" name="senha" id="senha" class="form-control" placeholder="Deixe em branco p/ padrão (123456)">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Salvar Vendedor</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Vendedores -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Vendedores Cadastrados</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>CPF</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vendedores)): ?>
                                <?php foreach ($vendedores as $v): ?>
                                    <tr>
                                        <td><strong><?= esc($v['nome']) ?></strong></td>
                                        <td><?= esc($v['email']) ?></td>
                                        <td><?= esc($v['cpf']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('pessoas/excluir/' . $v['id'] . '/vendedores') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Excluir este vendedor?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-3">Nenhum vendedor cadastrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
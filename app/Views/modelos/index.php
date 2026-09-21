<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Modelos - Gestão de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?= view('templates/header') ?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Gestão de Modelos</h2>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">Voltar ao Dashboard</a>
    </div>

    <!-- Mensagens de Feedback -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulário de Cadastro / Edição -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0" id="formTitle">Novo Modelo</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('modelos/salvar') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" id="modelo_id">

                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" name="marca" id="marca" class="form-control" placeholder="Ex: Chevrolet, Fiat, Toyota" required>
                        </div>

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Modelo</label>
                            <input type="text" name="nome" id="nome" class="form-control" placeholder="Ex: Onix, Uno, Corolla" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Salvar Modelo</button>
                        <button type="button" class="btn btn-outline-secondary w-100 mt-2 d-none" id="btnCancelar" onclick="limparFormulario()">Cancelar Edição</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabela de Modelos Cadastrados -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Modelos Cadastrados</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($modelos)): ?>
                                <?php foreach ($modelos as $m): ?>
                                    <tr>
                                        <td><?= $m['id'] ?></td>
                                        <td><?= esc($m['marca']) ?></td>
                                        <td><?= esc($m['nome']) ?></td>
                                        <td class="text-center">
                                            <button class="btn btn-warning btn-sm" onclick="editarModelo(<?= $m['id'] ?>, '<?= esc($m['marca']) ?>', '<?= esc($m['nome']) ?>')">Editar</button>
                                            <a href="<?= base_url('modelos/excluir/' . $m['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir?')">Excluir</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-3">Nenhum modelo cadastrado ainda.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editarModelo(id, marca, nome) {
    document.getElementById('modelo_id').value = id;
    document.getElementById('marca').value = marca;
    document.getElementById('nome').value = nome;
    document.getElementById('formTitle').innerText = 'Editar Modelo #' + id;
    document.getElementById('btnCancelar').classList.remove('d-none');
}

function limparFormulario() {
    document.getElementById('modelo_id').value = '';
    document.getElementById('marca').value = '';
    document.getElementById('nome').value = '';
    document.getElementById('formTitle').innerText = 'Novo Modelo';
    document.getElementById('btnCancelar').classList.add('d-none');
}
</script>

</body>
</html>
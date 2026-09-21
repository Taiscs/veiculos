<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Locações - Gestão de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?= view('templates/header') ?>

<div class="container-fluid px-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Gestão de Locações</h2>
        <div>
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalLocacao" onclick="limparFormulario()">
                Nova Locação
            </button>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">Voltar ao Dashboard</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('sucesso') ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erro') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Locações Registradas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Cliente</th>
                            <th>Veículo</th>
                            <th>Vendedor</th>
                            <th>Período</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($locacoes)): ?>
                            <?php foreach ($locacoes as $l): ?>
                                <tr>
                                    <td><strong>#<?= $l['id'] ?></strong></td>
                                    <td><?= esc($l['cliente_nome']) ?></td>
                                    <td><strong><?= esc($l['placa']) ?></strong> (<?= esc($l['marca']) ?> <?= esc($l['modelo_nome']) ?>)</td>
                                    <td><?= esc($l['vendedor_nome']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($l['data_inicio'])) ?> até <?= date('d/m/Y', strtotime($l['data_fim_prevista'])) ?></td>
                                    <td>R$ <?= number_format($l['valor_total'], 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($l['status'] == 'ativa'): ?>
                                            <span class="badge bg-primary">Ativa</span>
                                        <?php elseif ($l['status'] == 'concluida'): ?>
                                            <span class="badge bg-success">Concluída</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cancelada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($l['status'] == 'ativa'): ?>
                                            <a href="<?= base_url('locacoes/finalizar/' . $l['id']) ?>" class="btn btn-success btn-sm me-1" onclick="return confirm('Confirmar a devolução do veículo?')">Devolver</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted p-3">Nenhuma locação registrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cadastrar Nova Locação -->
<div class="modal fade" id="modalLocacao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('locacoes/salvar') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="locacao_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalLocacaoLabel">Nova Locação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Cliente *</label>
                            <select name="cliente_id" id="cliente_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= esc($c['nome']) ?> (CPF: <?= esc($c['cpf']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Vendedor / Atendente *</label>
                            <select name="vendedor_id" id="vendedor_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($vendedores as $v): ?>
                                    <option value="<?= $v['id'] ?>"><?= esc($v['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Veículo *</label>
                            <select name="carro_id" id="carro_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($carros as $car): ?>
                                    <option value="<?= $car['id'] ?>"><?= esc($car['placa']) ?> - <?= esc($car['marca']) ?> <?= esc($car['modelo_nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Valor da Diária (R$) *</label>
                            <input type="number" step="0.01" name="valor_diaria" id="valor_diaria" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data Início *</label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Data Prevista de Devolução *</label>
                            <input type="date" name="data_fim_prevista" id="data_fim_prevista" class="form-control" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" id="observacoes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Locação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function limparFormulario() {
    document.getElementById('locacao_id').value = '';
    document.getElementById('cliente_id').value = '';
    document.getElementById('vendedor_id').value = '';
    document.getElementById('carro_id').value = '';
    document.getElementById('valor_diaria').value = '';
    document.getElementById('observacoes').value = '';
}
</script>
</body>
</html>
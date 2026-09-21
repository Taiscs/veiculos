<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Manutenção (O.S.) - Gestão de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?= view('templates/header') ?>

<div class="container-fluid px-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Ordens de Serviço (O.S.) / Manutenção</h2>
        <div>
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalOS" onclick="limparFormulario()">
                Nova O.S.
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
            <h5 class="card-title mb-0">Histórico de Manutenções</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>O.S. #</th>
                            <th>Veículo</th>
                            <th>Oficina / Mecânica</th>
                            <th>Entrada</th>
                            <th>Previsão Saída</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($manutencoes)): ?>
                            <?php foreach ($manutencoes as $os): ?>
                                <tr>
                                    <td><strong>#<?= $os['id'] ?></strong></td>
                                    <td><strong><?= esc($os['placa']) ?></strong> (<?= esc($os['marca']) ?> <?= esc($os['modelo_nome']) ?>)</td>
                                    <td><?= esc($os['mecanica_oficina'] ?? 'Interno') ?></td>
                                    <td><?= date('d/m/Y', strtotime($os['data_entrada'])) ?></td>
                                    <td><?= $os['data_saida_prevista'] ? date('d/m/Y', strtotime($os['data_saida_prevista'])) : '-' ?></td>
                                    <td>R$ <?= number_format($os['valor_total'], 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($os['status'] == 'aberta'): ?>
                                            <span class="badge bg-warning text-dark">Aberta</span>
                                        <?php elseif ($os['status'] == 'em_andamento'): ?>
                                            <span class="badge bg-info text-dark">Em Andamento</span>
                                        <?php elseif ($os['status'] == 'concluida'): ?>
                                            <span class="badge bg-success">Concluída</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Cancelada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-warning btn-sm me-1" onclick='editarOS(<?= json_encode($os) ?>)'>Editar</button>
                                        <?php if ($os['status'] != 'concluida' && $os['status'] != 'cancelada'): ?>
                                            <a href="<?= base_url('manutencoes/finalizar/' . $os['id']) ?>" class="btn btn-success btn-sm" onclick="return confirm('Concluir O.S. e liberar veículo?')">Finalizar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted p-3">Nenhuma manutenção registrada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cadastrar / Editar O.S. -->
<div class="modal fade" id="modalOS" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('manutencoes/salvar') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="os_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalOSLabel">Nova Ordem de Serviço</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
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
                            <label class="form-label">Oficina / Mecânica</label>
                            <input type="text" name="mecanica_oficina" id="mecanica_oficina" class="form-control" placeholder="Ex: Oficina AutoFix">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Data Entrada *</label>
                            <input type="date" name="data_entrada" id="data_entrada" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Previsão de Saída</label>
                            <input type="date" name="data_saida_prevista" id="data_saida_prevista" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Custo Estimado/Total (R$)</label>
                            <input type="number" step="0.01" name="valor_total" id="valor_total" class="form-control" value="0.00">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Descrição do Problema *</label>
                            <textarea name="descricao_problema" id="descricao_problema" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Serviço Realizado / Peças</label>
                            <textarea name="servico_realizado" id="servico_realizado" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status da O.S.</label>
                            <select name="status" id="status" class="form-select">
                                <option value="aberta">Aberta</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="concluida">Concluída</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar O.S.</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function limparFormulario() {
    document.getElementById('os_id').value = '';
    document.getElementById('carro_id').value = '';
    document.getElementById('mecanica_oficina').value = '';
    document.getElementById('descricao_problema').value = '';
    document.getElementById('servico_realizado').value = '';
    document.getElementById('data_saida_prevista').value = '';
    document.getElementById('valor_total').value = '0.00';
    document.getElementById('status').value = 'aberta';
    document.getElementById('modalOSLabel').innerText = 'Nova Ordem de Serviço';
}

function editarOS(os) {
    document.getElementById('os_id').value = os.id;
    document.getElementById('carro_id').value = os.carro_id;
    document.getElementById('mecanica_oficina').value = os.mecanica_oficina;
    document.getElementById('descricao_problema').value = os.descricao_problema;
    document.getElementById('servico_realizado').value = os.servico_realizado;
    document.getElementById('data_entrada').value = os.data_entrada;
    document.getElementById('data_saida_prevista').value = os.data_saida_prevista;
    document.getElementById('valor_total').value = os.valor_total;
    document.getElementById('status').value = os.status;
    document.getElementById('modalOSLabel').innerText = 'Editar O.S. #' + os.id;

    var modal = new bootstrap.Modal(document.getElementById('modalOS'));
    modal.show();
}
</script>
</body>
</html>
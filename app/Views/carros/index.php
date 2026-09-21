<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frota de Veículos - Gestão de Veículos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
   

<?= view('templates/header') ?>

<div class="container-fluid px-4 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Gestão da Frota de Veículos</h2>
        <div>
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalCarro" onclick="limparFormulario()">
                <i class="bi bi-plus-lg"></i> Novo Veículo
            </button>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">Voltar ao Dashboard</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('sucesso')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('sucesso') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('erro')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('erro') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Listagem da Frota</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Placa</th>
                            <th>Modelo / Marca</th>
                            <th>Ano (Fab/Mod)</th>
                            <th>Cor</th>
                            <th>KM / Status Revisão</th>
                            <th>Preço</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($carros)): ?>
                            <?php foreach ($carros as$carro): ?>
                                <?php 
                                    $kmAtual = (int)($carro['km'] ?? 0);
                                    $restoRevisao =$kmAtual % 10000;
                                    
                                    $badgeRevisao = '';

                                    if ($kmAtual > 0) {
                                        if ($restoRevisao >= 9000) {$badgeRevisao = '<span class="badge bg-warning text-dark ms-1" title="Próximo da revisão de ' . (ceil($kmAtual / 10000) * 10000) . ' km"><i class="bi bi-exclamation-triangle-fill"></i> Revisão Próxima</span>';
                                        } elseif ($restoRevisao <= 500 && $kmAtual >= 10000) {$badgeRevisao = '<span class="badge bg-danger ms-1" title="Revisão necessária!"><i class="bi bi-wrench-statue"></i> Revisão Vencida/Urgente</span>';
                                        }
                                    }

                                    $statusCarro =$carro['status'] ?? 'desconhecido';
                                ?>
                                <tr>
                                    <td><strong>#<?= $carro['id'] ?></strong></td>
                                    <td><strong><?= esc($carro['placa']) ?></strong></td>
                                    <td><?= esc($carro['marca'] ?? '') ?> <?= esc($carro['modelo_nome'] ?? '') ?></td>
                                    <td>
                                        <?= esc($carro['ano_fabricacao'] ?? $carro['ano'] ?? 'N/D') ?>
                                        <?= isset($carro['ano_modelo']) ? '/' . esc($carro['ano_modelo']) : '' ?>
                                    </td>
                                    <td><?= esc($carro['cor']) ?></td>
                                    <td>
                                        <?= number_format($kmAtual, 0, ',', '.') ?> km
                                        <?= $badgeRevisao ?>
                                    </td>
                                    <td>R$ <?= number_format($carro['preco_venda'] ?? $carro['valor_compra'] ?? 0, 2, ',', '.') ?></td>
                                    <td>
                                        <?php if ($statusCarro === 'disponivel'): ?>
                                            <span class="badge bg-success">Disponível</span>
                                        <?php elseif ($statusCarro === 'vendido' || $statusCarro === 'alugado'): ?>
                                            <span class="badge bg-primary"><?= ucfirst(esc($statusCarro)) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Manutenção</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm text-white me-1 btn-detalhes" data-id="<?= $carro['id'] ?>" title="Ver Histórico Executivo">
                                            <i class="bi bi-eye-fill"></i> Histórico
                                        </button>
                                        <button class="btn btn-warning btn-sm me-1" onclick='editarCarro(<?= json_encode($carro, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </button>
                                        <a href="<?= base_url('carros/excluir/' . $carro['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente excluir este veículo?')">
                                            <i class="bi bi-trash"></i> Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted p-3">Nenhum veículo cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cadastrar / Editar -->
<div class="modal fade" id="modalCarro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= base_url('carros/salvar') ?>" method="POST" id="formCarro">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="carro_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCarroLabel">Cadastrar Veículo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Modelo *</label>
                            <select name="modelo_id" id="modelo_id" class="form-select" required>
                                <option value="">Selecione...</option>
                                <?php if (!empty($modelos)): ?>
                                    <?php foreach ($modelos as$m): ?>
                                        <option value="<?= $m['id'] ?>"><?= esc($m['marca']) ?> - <?= esc($m['nome']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Placa *</label>
                            <input type="text" name="placa" id="placa" class="form-control text-uppercase" maxlength="10" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cor *</label>
                            <input type="text" name="cor" id="cor" class="form-control" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Ano Fab. *</label>
                            <input type="number" name="ano_fabricacao" id="ano_fabricacao" class="form-control" min="1900" max="2099" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ano Mod. *</label>
                            <input type="number" name="ano_modelo" id="ano_modelo" class="form-control" min="1900" max="2099" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">KM</label>
                            <input type="number" name="km" id="km" class="form-control" value="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Preço (R$) *</label>
                            <input type="number" step="0.01" name="preco_venda" id="preco_venda" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chassi</label>
                            <input type="text" name="chassi" id="chassi" class="form-control text-uppercase" maxlength="17">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="disponivel">Disponível</option>
                                <option value="manutencao">Manutenção</option>
                                <option value="vendido">Vendido</option>
                                <option value="alugado">Alugado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Veículo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Histórico Executivo -->
<div class="modal fade" id="modalDetalhesCarro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    Histórico do Veículo - <span id="modalPlaca" class="badge bg-light text-dark"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3 bg-light p-3 rounded">
                    <div class="col-md-4"><strong>Modelo/Marca:</strong> <span id="modalModelo">-</span></div>
                    <div class="col-md-4"><strong>Quilometragem:</strong> <span id="modalKm">-</span></div>
                    <div class="col-md-4"><strong>Status Atual:</strong> <span id="modalStatus">-</span></div>
                </div>

                <!-- Abas de Navegação -->
                <ul class="nav nav-tabs" id="historicoTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-manutencao" data-bs-toggle="tab" data-bs-target="#content-manutencao" type="button" role="tab">
                            <i class="bi bi-wrench"></i> Manutenções
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-locacao" data-bs-toggle="tab" data-bs-target="#content-locacao" type="button" role="tab">
                            <i class="bi bi-key-fill"></i> Locações
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3" id="historicoTabsContent">
                    <!-- Tab Manutenção -->
                    <div class="tab-pane fade show active" id="content-manutencao" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Data Entrada</th>
                                        <th>Descrição / Serviço</th>
                                        <th>Oficina</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="listaManutencoes">
                                    <tr><td colspan="5" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Locação -->
                    <div class="tab-pane fade" id="content-locacao" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th>Valor Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="listaLocacoes">
                                    <tr><td colspan="3" class="text-center text-muted">Carregando...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function limparFormulario() {
    document.getElementById('formCarro').reset();
    document.getElementById('carro_id').value = '';
    document.getElementById('modalCarroLabel').textContent = 'Cadastrar Veículo';
}

function editarCarro(carro) {
    document.getElementById('carro_id').value = carro.id || '';
    document.getElementById('modelo_id').value = carro.modelo_id || '';
    document.getElementById('placa').value = carro.placa || '';
    document.getElementById('cor').value = carro.cor || '';
    document.getElementById('ano_fabricacao').value = carro.ano_fabricacao || carro.ano || '';
    document.getElementById('ano_modelo').value = carro.ano_modelo || '';
    document.getElementById('km').value = carro.km || 0;
    document.getElementById('preco_venda').value = carro.preco_venda || carro.valor_compra || '';
    document.getElementById('chassi').value = carro.chassi || '';
    document.getElementById('status').value = carro.status || 'disponivel';

    document.getElementById('modalCarroLabel').textContent = 'Editar Veículo';
    const modal = new bootstrap.Modal(document.getElementById('modalCarro'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('modalDetalhesCarro');
    const modalInstance = new bootstrap.Modal(modalElement);

    document.querySelectorAll('.btn-detalhes').forEach(button => {
        button.addEventListener('click', function () {
            const carroId = this.getAttribute('data-id');

            fetch(`<?= base_url('carros/detalhes') ?>/${carroId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const carro = data.carro;

                        document.getElementById('modalPlaca').textContent = carro.placa;
                        document.getElementById('modalModelo').textContent = `${carro.marca || ''} ${carro.modelo_nome || ''}`;
                        document.getElementById('modalKm').textContent = `${parseInt(carro.km || 0).toLocaleString('pt-BR')} km`;
                        
                        let statusBadge = '<span class="badge bg-secondary">Desconhecido</span>';
                        if (carro.status === 'disponivel') statusBadge = '<span class="badge bg-success">Disponível</span>';
                        else if (carro.status === 'alugado' || carro.status === 'vendido') statusBadge = `<span class="badge bg-primary">${carro.status}</span>`;
                        else if (carro.status === 'manutencao') statusBadge = '<span class="badge bg-warning text-dark">Em Manutenção</span>';
                        document.getElementById('modalStatus').innerHTML = statusBadge;

                        // Histórico de Manutenções
                        const tbodyManutencao = document.getElementById('listaManutencoes');
                        tbodyManutencao.innerHTML = '';
                        if (data.manutencoes && data.manutencoes.length > 0) {
                            data.manutencoes.forEach(m => {
                                let dataEntrada = '-';
                                if (m.data_entrada) {
                                    const partes = m.data_entrada.split(' ')[0].split('-');
                                    dataEntrada = `${partes[2]}/${partes[1]}/${partes[0]}`;
                                }

                                tbodyManutencao.innerHTML += `
                                    <tr>
                                        <td>${dataEntrada}</td>
                                        <td><strong>${m.descricao_problema || m.descricao || '-'}</strong><br><small class="text-muted">${m.servico_realizado || ''}</small></td>
                                        <td>${m.mecanica_oficina || '-'}</td>
                                        <td>R$ ${parseFloat(m.valor_total || m.valor || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                                        <td><span class="badge ${m.status === 'concluida' ? 'bg-success' : 'bg-warning text-dark'}">${m.status || 'Concluído'}</span></td>
                                    </tr>`;
                            });
                        } else {
                            tbodyManutencao.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Nenhuma manutenção registrada para este veículo.</td></tr>';
                        }

                        // Histórico de Locações
                        const tbodyLocacao = document.getElementById('listaLocacoes');
                        tbodyLocacao.innerHTML = '';
                        if (data.locacoes && data.locacoes.length > 0) {
                            data.locacoes.forEach(l => {
                                const formatarData = (dt) => {
                                    if (!dt) return '-';
                                    const partes = dt.split(' ')[0].split('-');
                                    return `${partes[2]}/${partes[1]}/${partes[0]}`;
                                };

                                tbodyLocacao.innerHTML += `
                                    <tr>
                                        <td>${formatarData(l.data_inicio)} até ${formatarData(l.data_fim)}</td>
                                        <td>R$ ${parseFloat(l.valor_total || 0).toLocaleString('pt-BR', {minimumFractionDigits: 2})}</td>
                                        <td><span class="badge ${l.status === 'concluido' || l.status === 'concluida' ? 'bg-secondary' : 'bg-primary'}">${l.status}</span></td>
                                    </tr>`;
                            });
                        } else {
                            tbodyLocacao.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Nenhuma locação encontrada.</td></tr>';
                        }

                        modalInstance.show();
                    } else {
                        alert(data.mensagem || 'Erro ao carregar dados do veículo.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar os detalhes do veículo:', error);
                    alert('Falha na requisição ao buscar dados do histórico.');
                });
        });
    });
});
</script>
</body>
</html>
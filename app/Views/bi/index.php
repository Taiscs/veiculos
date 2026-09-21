<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>BI & Dashboard - Gestão de Frota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<?= view('templates/header') ?>

<div class="container-fluid px-4 mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard & Business Intelligence (BI)</h2>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary btn-sm">Voltar ao Dashboard</a>
    </div>

    <!-- CARDS DE METRICAS CONSOLIDADAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.8rem; opacity: 0.85;">Total da Frota</h6>
                    <h3 class="mb-0 fw-bold"><?= $totais_gerais['total_veiculos'] ?> <small class="fs-6 fw-normal">Veículos</small></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.8rem; opacity: 0.85;">Total de Locações</h6>
                        <h3 class="mb-0 fw-bold"><?= $totais_gerais['total_qtd_locacao'] ?> <small class="fs-6 fw-normal">Locações</small></h3>
                    </div>
                    <div class="text-end">
                        <span class="d-block text-uppercase fw-bold" style="font-size: 0.75rem; opacity: 0.85;">Receita</span>
                        <span class="fs-5 fw-bold">R$ <?= number_format($totais_gerais['total_valor_locacao'], 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase fw-bold mb-1" style="font-size: 0.8rem; opacity: 0.85;">Total Manutenções</h6>
                        <h3 class="mb-0 fw-bold"><?= $totais_gerais['total_qtd_manutencao'] ?> <small class="fs-6 fw-normal">O.S.</small></h3>
                    </div>
                    <div class="text-end">
                        <span class="d-block text-uppercase fw-bold" style="font-size: 0.75rem; opacity: 0.85;">Custo Total</span>
                        <span class="fs-5 fw-bold">R$ <?= number_format($totais_gerais['total_valor_manutencao'], 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CARDS DE DESTAQUE: MAIOR RECEITA X MAIOR CUSTO -->
    <div class="row g-3 mb-4">
        <!-- Maior Receita -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <span class="badge bg-success mb-2">🏆 Maior Receita de Aluguel</span>
                    <?php if (!empty($maior_receita_aluguel)): ?>
                        <h4 class="fw-bold mb-1"><?= esc($maior_receita_aluguel['placa']) ?> - <?= esc($maior_receita_aluguel['marca']) ?> <?= esc($maior_receita_aluguel['modelo']) ?></h4>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted">Total de Aluguéis: <strong><?= $maior_receita_aluguel['qtd_locacoes'] ?></strong></span>
                            <span class="fs-4 fw-bold text-success">R$ <?= number_format($maior_receita_aluguel['total_receita'], 2, ',', '.') ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhuma locação concluída ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Maior Custo -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body">
                    <span class="badge bg-danger mb-2">⚠️ Maior Custo de Manutenção</span>
                    <?php if (!empty($maior_custo_manutencao)): ?>
                        <h4 class="fw-bold mb-1"><?= esc($maior_custo_manutencao['placa']) ?> - <?= esc($maior_custo_manutencao['marca']) ?> <?= esc($maior_custo_manutencao['modelo']) ?></h4>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="text-muted">Ordens de Serviço: <strong><?= $maior_custo_manutencao['qtd_os'] ?></strong></span>
                            <span class="fs-4 fw-bold text-danger">R$ <?= number_format($maior_custo_manutencao['total_gasto'], 2, ',', '.') ?></span>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhuma manutenção concluída ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- GRAFICO MÊS A MÊS -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Evolução Financeira Mês a Mês (Receita x Despesa x Lucro)</h5>
        </div>
        <div class="card-body">
            <canvas id="graficoLucroMensal" style="max-height: 320px;"></canvas>
        </div>
    </div>

    <!-- TABELA DETALHADA -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0">Análise Financeira por Veículo</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Veículo / Placa</th>
                            <th>Status</th>
                            <th>Valor Compra</th>
                            <th class="text-center">Qtd. Locações</th>
                            <th>Receita (R$)</th>
                            <th class="text-center">Qtd. O.S.</th>
                            <th>Custo (R$)</th>
                            <th>Lucro Líquido (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dados_bi)): ?>
                            <?php foreach ($dados_bi as $item): ?>
                                <?php $saldo = $item['ganho_aluguel'] - $item['gasto_manutencao']; ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($item['placa']) ?></strong><br>
                                        <small class="text-muted"><?= esc($item['marca']) ?> <?= esc($item['modelo']) ?></small>
                                    </td>
                                    <td>
                                        <?php if ($item['status'] == 'disponivel'): ?>
                                            <span class="badge bg-success">Disponível</span>
                                        <?php elseif ($item['status'] == 'manutencao'): ?>
                                            <span class="badge bg-warning text-dark">Manutenção</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= ucfirst($item['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>R$ <?= number_format($item['valor_veiculo'], 2, ',', '.') ?></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= $item['qtd_locacao'] ?></span></td>
                                    <td class="text-success fw-bold">R$ <?= number_format($item['ganho_aluguel'], 2, ',', '.') ?></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= $item['qtd_manutencao'] ?></span></td>
                                    <td class="text-danger fw-bold">R$ <?= number_format($item['gasto_manutencao'], 2, ',', '.') ?></td>
                                    <td class="<?= $saldo >= 0 ? 'text-success' : 'text-danger' ?> fw-bold">
                                        R$ <?= number_format($saldo, 2, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" class="text-center text-muted p-3">Nenhum registro encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT DO CHART.JS -->
<script>
const dadosMensais = <?= json_encode($grafico_mensal) ?>;

const labels = dadosMensais.map(item => item.ano_mes);
const receitas = dadosMensais.map(item => parseFloat(item.receita));
const despesas = dadosMensais.map(item => parseFloat(item.despesa));
const lucros = dadosMensais.map(item => parseFloat(item.lucro));

const ctx = document.getElementById('graficoLucroMensal').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Receita (R$)',
                data: receitas,
                backgroundColor: 'rgba(25, 135, 84, 0.7)',
                borderColor: '#198754',
                borderWidth: 1
            },
            {
                label: 'Despesa (R$)',
                data: despesas,
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: '#dc3545',
                borderWidth: 1
            },
            {
                label: 'Lucro Líquido (R$)',
                data: lucros,
                type: 'line',
                borderColor: '#0d6efd',
                backgroundColor: '#0d6efd',
                borderWidth: 3,
                fill: false,
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
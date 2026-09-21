<?php

namespace App\Controllers;

class Bi extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Resumo do Pátio
        $data['totais_patio'] = [
            'disponiveis' => $db->table('carros')->where('status', 'disponivel')->countAllResults(),
            'alugados'    => $db->table('carros')->where('status', 'alugado')->countAllResults(),
            'manutencao'  => $db->table('carros')->where('status', 'manutencao')->countAllResults(),
            'total'       => $db->table('carros')->countAllResults(),
        ];

        // 2. Totais Gerais de BI
        $queryTotais = $db->query("
            SELECT 
                COUNT(c.id) AS total_veiculos,
                COALESCE(SUM(c.valor_compra), 0) AS valor_total_frota,
                (SELECT COUNT(id) FROM manutençoes) AS total_qtd_manutencao,
                (SELECT COALESCE(SUM(valor_total), 0) FROM manutençoes WHERE status = 'concluida') AS total_valor_manutencao,
                (SELECT COUNT(id) FROM locacoes) AS total_qtd_locacao,
                (SELECT COALESCE(SUM(valor_total), 0) FROM locacoes WHERE status = 'concluida') AS total_valor_locacao
            FROM carros c
        ");
        $data['totais_gerais'] = $queryTotais->getRowArray();

        // 3. Veículo com MAIOR CUSTO de Manutenção
        $data['maior_custo_manutencao'] = $db->query("
            SELECT 
                c.placa, m.nome AS modelo, m.marca,
                SUM(maint.valor_total) AS total_gasto,
                COUNT(maint.id) AS qtd_os
            FROM manutençoes maint
            JOIN carros c ON c.id = maint.carro_id
            JOIN modelos m ON m.id = c.modelo_id
            WHERE maint.status = 'concluida'
            GROUP BY c.id
            ORDER BY total_gasto DESC
            LIMIT 1
        ")->getRowArray();

        // 4. Veículo com MAIOR RECEITA de Aluguel
        $data['maior_receita_aluguel'] = $db->query("
            SELECT 
                c.placa, m.nome AS modelo, m.marca,
                SUM(loc.valor_total) AS total_receita,
                COUNT(loc.id) AS qtd_locacoes
            FROM locacoes loc
            JOIN carros c ON c.id = loc.carro_id
            JOIN modelos m ON m.id = c.modelo_id
            WHERE loc.status = 'concluida'
            GROUP BY c.id
            ORDER BY total_receita DESC
            LIMIT 1
        ")->getRowArray();

        // 5. Dados Financeiros Mês a Mês (Últimos 12 meses)
        $queryMensal = $db->query("
            SELECT 
                meses.ano_mes,
                COALESCE(rec.receita, 0) AS receita,
                COALESCE(desp.despesa, 0) AS despesa,
                (COALESCE(rec.receita, 0) - COALESCE(desp.despesa, 0)) AS lucro
            FROM (
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS ano_mes FROM locacoes
                UNION
                SELECT DATE_FORMAT(created_at, '%Y-%m') AS ano_mes FROM manutençoes
            ) AS meses
            LEFT JOIN (
                SELECT DATE_FORMAT(data_inicio, '%Y-%m') AS ano_mes, SUM(valor_total) AS receita
                FROM locacoes WHERE status = 'concluida' GROUP BY ano_mes
            ) rec ON rec.ano_mes = meses.ano_mes
            LEFT JOIN (
                SELECT DATE_FORMAT(data_entrada, '%Y-%m') AS ano_mes, SUM(valor_total) AS despesa
                FROM manutençoes WHERE status = 'concluida' GROUP BY ano_mes
            ) desp ON desp.ano_mes = meses.ano_mes
            WHERE meses.ano_mes IS NOT NULL
            ORDER BY meses.ano_mes ASC
            LIMIT 12
        ");
        $data['grafico_mensal'] = $queryMensal->getResultArray();

        // 6. Consulta Detalhada por Veículo
        $queryFinanceiro = $db->query("
            SELECT 
                c.id, c.placa, c.status, m.nome AS modelo, m.marca, c.valor_compra AS valor_veiculo,
                COALESCE((SELECT COUNT(id) FROM manutençoes WHERE carro_id = c.id), 0) AS qtd_manutencao,
                COALESCE((SELECT SUM(valor_total) FROM manutençoes WHERE carro_id = c.id AND status = 'concluida'), 0) AS gasto_manutencao,
                COALESCE((SELECT COUNT(id) FROM locacoes WHERE carro_id = c.id), 0) AS qtd_locacao,
                COALESCE((SELECT SUM(valor_total) FROM locacoes WHERE carro_id = c.id AND status = 'concluida'), 0) AS ganho_aluguel
            FROM carros c
            JOIN modelos m ON m.id = c.modelo_id
            ORDER BY c.id DESC
        ");
        $data['dados_bi'] = $queryFinanceiro->getResultArray();

        return view('bi/index', $data);
    }
}
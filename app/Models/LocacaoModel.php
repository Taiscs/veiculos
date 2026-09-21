<?php

namespace App\Models;

use CodeIgniter\Model;

class LocacaoModel extends Model
{
    protected $table            = 'locacoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'cliente_id',
        'vendedor_id',
        'carro_id',
        'data_inicio',
        'data_fim_prevista',
        'data_devolucao',
        'valor_diaria',
        'valor_total',
        'status',
        'observacoes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLocacoesComDetalhes($id = null)
    {
        $builder = $this->select('locacoes.*, 
                                 cli.nome as cliente_nome, cli.cpf as cliente_cpf,
                                 vend.nome as vendedor_nome,
                                 carros.placa, modelos.nome as modelo_nome, modelos.marca')
                        ->join('usuarios as cli', 'cli.id = locacoes.cliente_id')
                        ->join('usuarios as vend', 'vend.id = locacoes.vendedor_id')
                        ->join('carros', 'carros.id = locacoes.carro_id')
                        ->join('modelos', 'modelos.id = carros.modelo_id')
                        ->orderBy('locacoes.id', 'DESC');

        if ($id !== null) {
            return $builder->where('locacoes.id', $id)->first();
        }

        return $builder->findAll();
    }
}
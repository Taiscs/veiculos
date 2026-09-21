<?php

namespace App\Models;

use CodeIgniter\Model;

class ManutencaoModel extends Model
{
    protected $table            = 'manutençoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'carro_id',
        'descricao_problema',
        'servico_realizado',
        'mecanica_oficina',
        'data_entrada',
        'data_saida_prevista',
        'data_saida_real',
        'valor_total',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getManutencoesComCarro($id = null)
    {
        $builder = $this->select('manutençoes.*, carros.placa, modelos.nome as modelo_nome, modelos.marca')
                        ->join('carros', 'carros.id = manutençoes.carro_id')
                        ->join('modelos', 'modelos.id = carros.modelo_id')
                        ->orderBy('manutençoes.id', 'DESC');

        if ($id !== null) {
            return $builder->where('manutençoes.id', $id)->first();
        }

        return $builder->findAll();
    }
}
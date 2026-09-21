<?php

namespace App\Models;

use CodeIgniter\Model;

class CarroModel extends Model
{
    protected $table            = 'carros';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    // Ajustado para refletir os campos reais do banco: id, modelo_id, placa, cor, ano, valor_compra, status
    protected $allowedFields    = [
        'modelo_id',
        'placa',
        'cor',
        'ano',
        'valor_compra',
        'status'
    ];

    // Desativado pois a tabela 'carros' não possui as colunas created_at / updated_at
    protected $useTimestamps = false;

    /**
     * Retorna os carros trazendo os dados do modelo/marca junto
     * e cria alias para compatibilidade com as Views que usam ano_fabricacao/preco_venda
     */
    public function getCarrosComModelo($id = null)
    {
        $builder = $this->select('
            carros.*, 
            carros.ano AS ano_fabricacao, 
            carros.ano AS ano_modelo, 
            carros.valor_compra AS preco_venda, 
            COALESCE(carros.km, 0) AS km, 
            modelos.nome AS modelo_nome, 
            modelos.marca
        ')->join('modelos', 'modelos.id = carros.modelo_id', 'left');

        if ($id !== null) {
            return $builder->where('carros.id', $id)->first();
        }

        return $builder->findAll();
    }
}
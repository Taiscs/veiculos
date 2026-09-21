<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLocacoesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cliente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'vendedor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'carro_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'data_inicio' => [
                'type' => 'DATE',
            ],
            'data_fim_prevista' => [
                'type' => 'DATE',
            ],
            'data_devolucao' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'valor_diaria' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'valor_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['ativa', 'concluida', 'cancelada'],
                'default'    => 'ativa',
            ],
            'observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('cliente_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('vendedor_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('carro_id', 'carros', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('locacoes');
    }

    public function down()
    {
        $this->forge->dropTable('locacoes');
    }
}
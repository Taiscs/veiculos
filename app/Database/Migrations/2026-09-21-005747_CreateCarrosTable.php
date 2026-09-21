<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCarrosTable extends Migration
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
            'modelo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'placa' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'chassi' => [
                'type'       => 'VARCHAR',
                'constraint' => 17,
                'null'       => true,
            ],
            'cor' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'ano_fabricacao' => [
                'type'       => 'YEAR',
            ],
            'ano_modelo' => [
                'type'       => 'YEAR',
            ],
            'km' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'preco_venda' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['disponivel', 'vendido', 'manutencao'],
                'default'    => 'disponivel',
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
        $this->forge->addForeignKey('modelo_id', 'modelos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('carros');
    }

    public function down()
    {
        $this->forge->dropTable('carros');
    }
}
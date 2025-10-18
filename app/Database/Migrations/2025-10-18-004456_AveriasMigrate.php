<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AveriasMigrate extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'cliente' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => false
            ],
            'problema' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false
            ],
            'fechahora' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['P', 'S'],
                'default' => 'P'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('averias');
    }

    public function down()
    {
        $this->forge->dropTable('averias');
    }
}
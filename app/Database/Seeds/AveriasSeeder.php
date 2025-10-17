<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AveriasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'cliente' => 'Carlos Ramos',
                'problema' => 'Pantalla no enciende',
                'fechahora' => date('Y-m-d H:i:s'),
                'status' => 'pendiente'
            ],
            [
                'cliente' => 'Lucía Torres',
                'problema' => 'Error en el sistema de sonido',
                'fechahora' => date('Y-m-d H:i:s'),
                'status' => 'pendiente'
            ],
            [
                'cliente' => 'Jorge Medina',
                'problema' => 'Actualización fallida',
                'fechahora' => date('Y-m-d H:i:s'),
                'status' => 'solucionado'
            ],
        ];

        $this->db->table('averias')->insertBatch($data);
    }
}

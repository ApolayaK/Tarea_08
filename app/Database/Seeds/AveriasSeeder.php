<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AveriasSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                "cliente" => "Roxana Cárdenas",
                "problema" => "Internet lento",
                "fechahora" => "2025-10-17 09:00:00",
                "status" => "P"
            ],
            [
                "cliente" => "Cristina Ávalos",
                "problema" => "No tiene internet",
                "fechahora" => "2025-10-17 09:15:00",
                "status" => "P"
            ],
            [
                "cliente" => "Tania Mendoza",
                "problema" => "El WIFI no funciona",
                "fechahora" => "2025-10-17 10:00:00",
                "status" => "P"
            ]
        ];

        $this->db->table('averias')->insertBatch($data);
    }
}
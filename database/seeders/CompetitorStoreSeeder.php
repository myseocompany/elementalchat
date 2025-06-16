<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetitorStore;

class CompetitorStoreSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/competitor_stores.json');
        $data = json_decode(file_get_contents($path), true);
        foreach ($data as $item) {
            if (is_numeric($item['Latitud']) && is_numeric($item['Longitud'])) {
                CompetitorStore::create([
                    'name' => $item['Tienda'],
                    'address' => $item['Dirección'],
                    'latitude' => $item['Latitud'],
                    'longitude' => $item['Longitud'],
                ]);
            }
        }
    }
}

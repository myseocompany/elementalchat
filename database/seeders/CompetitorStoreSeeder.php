<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetitorStore;
use App\Models\Franchise;

class CompetitorStoreSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/competitor_stores.json');
        $data = json_decode(file_get_contents($path), true);

        $franchiseMap = [
            'Falabella' => 'Falabella',
            'Farmatodo' => 'Farmatodo',
            'Linda Piel' => 'Linda Piel',
            'Linea Estetica' => 'Linea Estetica',
            'Medipiel' => 'Medipiel',
            'Naturell' => 'Naturell',
            'Para tu piel' => 'Para tu piel',
            'Profamiliar' => 'Profamiliar',
            'Skin Life' => 'SkinLife',
            'SkinLife' => 'SkinLife',
            'Cruz Verde' => 'Cruz Verde',
            'Kuma' => 'Kuma',
            'One Center' => 'One Center',
        ];

        foreach ($data as $item) {
            if (is_numeric($item['Latitud']) && is_numeric($item['Longitud'])) {
                $franchiseName = collect($franchiseMap)
                    ->filter(fn($val, $key) => str_contains(strtolower($item['Tienda']), strtolower($key)))
                    ->first();

                $franchiseId = Franchise::where('name', $franchiseName)->value('id');

                CompetitorStore::create([
                    'name' => $item['Tienda'],
                    'address' => $item['Dirección'],
                    'latitude' => $item['Latitud'],
                    'longitude' => $item['Longitud'],
                    'franchise_id' => $franchiseId,
                    'opened_year' => $item['Año apertura'] ?? null,
                    'created_at' => '2022-06-01 12:00:00',
                    'updated_at' => '2022-06-01 12:00:00',
                ]);
            }
        }
    }
}

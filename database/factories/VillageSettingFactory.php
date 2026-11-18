<?php

namespace Database\Factories;

use App\Models\VillageSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VillageSetting>
 */
class VillageSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'village_name' => 'Desa Pakel',
            'district_name' => 'Watulimo',
            'regency_name' => 'Trenggalek',
            'province_name' => 'Jawa Timur',
            'description' => fake()->paragraph(),
            'address' => null,
            'phone' => null,
            'email' => null,
            'whatsapp' => null,
            'office_hours' => null,
            'maps_url' => null,
            'map_embed_url' => null,
        ];
    }
}

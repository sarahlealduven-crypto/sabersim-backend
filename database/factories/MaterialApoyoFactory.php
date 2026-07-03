<?php

namespace Database\Factories;

use App\Models\Materia;
use App\Models\MaterialApoyo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaterialApoyo>
 */
class MaterialApoyoFactory extends Factory
{
    protected $model = MaterialApoyo::class;

    public function definition(): array
    {
        $titulo = fake()->sentence(4);
        $sourceUrl = 'https://www.youtube.com/watch?v=jjO21znJdiE';

        return [
            'materia_id' => Materia::factory(),
            'titulo' => $titulo,
            'slug' => Str::slug($titulo).'-'.fake()->unique()->randomNumber(5),
            'descripcion' => fake()->sentence(),
            'tipo' => MaterialApoyo::TIPO_YOUTUBE,
            'source_url' => $sourceUrl,
            'embed_url' => MaterialApoyo::embedUrlFor(MaterialApoyo::TIPO_YOUTUBE, $sourceUrl),
            'thumbnail_url' => 'https://img.youtube.com/vi/jjO21znJdiE/hqdefault.jpg',
            'duracion' => fake()->numberBetween(12, 55).' min',
            'orden_visualizacion' => fake()->numberBetween(1, 20),
            'activo' => true,
        ];
    }

    public function googleDrive(): static
    {
        $sourceUrl = 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz123456/view';

        return $this->state(fn (array $attributes): array => [
            'tipo' => MaterialApoyo::TIPO_GOOGLE_DRIVE,
            'source_url' => $sourceUrl,
            'embed_url' => MaterialApoyo::embedUrlFor(MaterialApoyo::TIPO_GOOGLE_DRIVE, $sourceUrl),
            'thumbnail_url' => null,
            'duracion' => null,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes): array => [
            'activo' => false,
        ]);
    }
}

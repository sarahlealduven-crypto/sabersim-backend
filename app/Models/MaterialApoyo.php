<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MaterialApoyo extends Model
{
    use HasFactory;

    public const TIPO_YOUTUBE = 'youtube';

    public const TIPO_GOOGLE_DRIVE = 'google_drive';

    protected $table = 'materiales_apoyo';

    protected $fillable = [
        'materia_id',
        'titulo',
        'slug',
        'descripcion',
        'tipo',
        'source_url',
        'embed_url',
        'thumbnail_url',
        'duracion',
        'orden_visualizacion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'orden_visualizacion' => 'integer',
        ];
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function booted(): void
    {
        static::saving(function (MaterialApoyo $material): void {
            if (! $material->slug) {
                $material->slug = Str::slug($material->titulo);
            }

            $material->embed_url = self::embedUrlFor($material->tipo, $material->source_url);
        });
    }

    public static function embedUrlFor(string $tipo, string $url): string
    {
        return match ($tipo) {
            self::TIPO_YOUTUBE => self::youtubeEmbedUrl($url),
            self::TIPO_GOOGLE_DRIVE => self::googleDriveEmbedUrl($url),
            default => throw new InvalidArgumentException('Tipo de material no soportado.'),
        };
    }

    private static function youtubeEmbedUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = trim($parts['path'] ?? '', '/');

        if ($host === 'youtu.be') {
            return 'https://www.youtube.com/embed/'.self::assertYouTubeId(strtok($path, '/'));
        }

        if (str_contains($host, 'youtube.com')) {
            if (str_starts_with($path, 'embed/')) {
                return 'https://www.youtube.com/embed/'.self::assertYouTubeId(Str::after($path, 'embed/'));
            }

            if (str_starts_with($path, 'shorts/')) {
                return 'https://www.youtube.com/embed/'.self::assertYouTubeId(Str::after($path, 'shorts/'));
            }

            parse_str($parts['query'] ?? '', $query);
            if (isset($query['v'])) {
                return 'https://www.youtube.com/embed/'.self::assertYouTubeId((string) $query['v']);
            }
        }

        throw new InvalidArgumentException('URL de YouTube no válida.');
    }

    private static function googleDriveEmbedUrl(string $url): string
    {
        $parts = parse_url($url);
        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (! str_contains($host, 'google.com')) {
            throw new InvalidArgumentException('URL de Google Drive no válida.');
        }

        if (preg_match('#/(?:file|document|presentation|spreadsheets)/d/([^/]+)#', $path, $matches) === 1) {
            $resource = trim(explode('/d/', $path)[0], '/');

            if ($resource === 'file') {
                return "https://drive.google.com/file/d/{$matches[1]}/preview";
            }

            return "https://docs.google.com/{$resource}/d/{$matches[1]}/preview";
        }

        parse_str($parts['query'] ?? '', $query);
        if (isset($query['id'])) {
            return 'https://drive.google.com/file/d/'.$query['id'].'/preview';
        }

        throw new InvalidArgumentException('URL de Google Drive no válida.');
    }

    private static function assertYouTubeId(string|false $id): string
    {
        $id = trim((string) $id);

        if (preg_match('/^[A-Za-z0-9_-]{11}$/', $id) !== 1) {
            throw new InvalidArgumentException('URL de YouTube no válida.');
        }

        return $id;
    }
}

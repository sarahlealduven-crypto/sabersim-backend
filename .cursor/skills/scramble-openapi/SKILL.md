---
name: scramble-openapi
description: Generate OpenAPI documentation with dedoc/scramble for Laravel APIs. Use when creating API endpoints, documenting requests/responses, adding authentication docs, customizing OpenAPI output, or when the user mentions Swagger, OpenAPI, or API documentation.
---

# Scramble - Generador de Documentación OpenAPI para Laravel

Scramble genera automáticamente documentación OpenAPI 3.1.0 desde tu código Laravel sin requerir anotaciones PHPDoc en la mayoría de los casos.

## Referencia Rápida

| Característica | Cómo Funciona |
|----------------|---------------|
| Solicitudes | Inferidas desde reglas de validación y parámetros de ruta |
| Respuestas | Inferidas desde tipos de retorno y Recursos JSON |
| Autenticación | Configurada mediante transformadores de documento |
| Interfaz | Stoplight Elements en `/docs/api` |

## URLs de Documentación

- **Ver documentación**: `/docs/api`
- **OpenAPI JSON**: `/docs/api.json`
- **Exportar**: `php artisan scramble:export`

## Documentar Solicitudes

### Parámetros de Ruta

Documentados automáticamente desde las definiciones de ruta. Mejora con PHPDoc o atributos:

```php
use Dedoc\Scramble\Attributes\PathParameter;

class ExamenController extends Controller
{
    #[PathParameter('examen', description: 'El ID del examen', type: 'int', example: 1)]
    public function show(Examen $examen): ExamenResource
    {
        return new ExamenResource($examen);
    }
}
```

### Cuerpo de Solicitud (vía Form Requests)

Scramble lee las reglas de validación de las clases Form Request:

```php
class IniciarExamenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /**
             * El tipo de examen.
             * @example "completo"
             */
            'tipo_examen' => ['required', 'string', Rule::enum(TipoExamen::class)],

            /** @query */
            'per_page' => ['integer', 'min:1', 'max:100'],

            /** @ignoreParam */
            'internal_flag' => ['boolean'],
        ];
    }
}
```

### Parámetros de Consulta (Manual)

```php
use Dedoc\Scramble\Attributes\QueryParameter;

#[QueryParameter('per_page', description: 'Elementos por página', type: 'int', default: 15, example: 20)]
#[QueryParameter('sort', description: 'Campo de ordenamiento', type: 'string', example: 'created_at')]
public function index(Request $request): AnonymousResourceCollection
{
    // ...
}
```

## Documentar Respuestas

### Recursos JSON

Scramble analiza el método `toArray()`. Agrega descripciones con PHPDoc:

```php
class ExamenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            /**
             * Estado actual del examen.
             * @example "en_progreso"
             */
            'estado' => $this->estado,
            /** @format date-time */
            'fecha_inicio' => $this->fecha_inicio->toIso8601String(),
            'secciones' => SeccionExamenResource::collection($this->whenLoaded('seccionesExamen')),
        ];
    }
}
```

### Paginación

Detectado automáticamente al usar métodos de paginación:

```php
public function index(): AnonymousResourceCollection
{
    return ExamenResource::collection(Examen::paginate());
}
```

### Respuestas de Error

Documentadas automáticamente desde:
- Llamadas `validate()` → respuesta 422
- Llamadas `authorize()` → respuesta 403
- Binding de modelos → respuesta 404
- Helpers `abort()` → código de estado correspondiente

## Referencia de Atributos

| Atributo | Propósito |
|----------|-----------|
| `#[Group('nombre')]` | Agrupa endpoints en la documentación |
| `#[QueryParameter(...)]` | Documenta parámetros de consulta |
| `#[PathParameter(...)]` | Documenta parámetros de ruta |
| `#[BodyParameter(...)]` | Documenta campos del cuerpo |
| `#[HeaderParameter(...)]` | Documenta encabezados de solicitud |
| `#[Header(...)]` | Documenta encabezados de respuesta |
| `#[Response(...)]` | Documenta respuestas |
| `#[Endpoint(...)]` | Establece ID de operación, título, método |
| `#[SchemaName('Nombre')]` | Nombre de esquema personalizado |
| `#[ExcludeRouteFromDocs]` | Oculta endpoint de la documentación |

## Anotaciones PHPDoc

| Anotación | Propósito |
|-----------|-----------|
| `@deprecated` | Marca como obsoleto |
| `@var tipo` | Sobrescribe el tipo inferido |
| `@example valor` | Agrega un ejemplo |
| `@format formato` | Establece formato (date-time, uuid, email) |
| `@default valor` | Establece valor por defecto |
| `@query` | Marca como parámetro de consulta (no-GET) |
| `@ignoreParam` | Excluye de la documentación |
| `@unauthenticated` | Excluye de requisitos de autenticación |
| `@response Tipo` | Sobrescribe tipo de respuesta |
| `@operationId id` | ID de operación personalizado |

## Configuración de Autenticación

Configura en `AppServiceProvider::boot()`:

```php
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;

public function boot(): void
{
    Scramble::configure()
        ->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });
}
```

### Security Schemes

```php
// Bearer token
SecurityScheme::http('bearer');

// Bearer JWT
SecurityScheme::http('bearer', 'JWT');

// API Key in query
SecurityScheme::apiKey('query', 'api_token');

// API Key in header
SecurityScheme::apiKey('header', 'X-API-Key');

// Basic auth
SecurityScheme::http('basic');
```

## Personalización

### Transformadores de Documento

Modifica todo el documento OpenAPI:

```php
Scramble::configure()
    ->withDocumentTransformers(function (OpenApi $openApi) {
        $openApi->info->title = 'API de SaberSim';
        $openApi->info->description = 'API para simulacros de exámenes';
    });
```

### Transformadores de Operación

Modifica operaciones individuales:

```php
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;

Scramble::configure()
    ->withOperationTransformers(function (Operation $operation) {
        $operation->addParameters([
            new Parameter('X-Request-ID', 'header'),
        ]);
    });
```

## Configuración

Opciones clave en `config/scramble.php`:

```php
return [
    'api_path' => 'api',              // Prefijo de rutas a documentar
    'api_domain' => null,             // Dominio de la API (null = dominio de la app)
    'export_path' => 'api.json',      // Ruta del archivo de exportación

    'info' => [
        'version' => env('API_VERSION', '0.0.1'),
        'description' => '',
    ],

    'ui' => [
        'title' => null,              // Título de la página de documentación
        'theme' => 'light',           // light, dark, system
        'hide_try_it' => false,       // Ocultar característica "Try It"
        'layout' => 'responsive',     // sidebar, responsive, stacked
    ],
];
```

## Reglas de Validación Soportadas

`required`, `string`, `bool`, `int`, `array`, `in`, `Rule::in`, `nullable`, `email`, `uuid`, `exists`, `min`, `max`, `Enum`, `file`, `image`, `date`, `date_format`, `size`, `between`, `regex`

## Comandos

```bash
# Exportar especificación OpenAPI
php artisan scramble:export

# Exportar a ruta específica
php artisan scramble:export --path=public/api.json

# Exportar versión específica de la API
php artisan scramble:export --api=v2
```

## Recursos Adicionales

- Para detalles de documentación de enums, consulta [enums.md](enums.md)
- Para personalización avanzada, consulta [customization.md](customization.md)
- Para referencia completa de atributos, consulta [attributes.md](attributes.md)

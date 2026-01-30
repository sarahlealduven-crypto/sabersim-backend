# Documentar Enums con Scramble

Scramble documenta automáticamente los enums respaldados por PHP en formato OpenAPI.

## Documentación Básica de Enums

```php
enum EstadoExamen: string
{
    case EnProgreso = 'en_progreso';
    case Completado = 'completado';
    case Abandonado = 'abandonado';
}
```

Genera:

```json
{
    "title": "EstadoExamen",
    "type": "string",
    "enum": ["en_progreso", "completado", "abandonado"]
}
```

## Agregar Descripciones de Casos

Agrega PHPDoc a los casos de enum para descripciones:

```php
enum EstadoExamen: string
{
    /**
     * El examen está siendo tomado actualmente.
     */
    case EnProgreso = 'en_progreso';

    /**
     * El examen ha finalizado y sido calificado.
     */
    case Completado = 'completado';

    /**
     * El examen fue abandonado antes de completarse.
     */
    case Abandonado = 'abandonado';
}
```

## Configuración de Estrategia de Descripción

En `config/scramble.php`:

```php
return [
    /**
     * Cómo almacenar las descripciones de casos de enum.
     *
     * Opciones:
     * - 'description': Como tabla markdown en la descripción del esquema
     * - 'extension': En extensión x-enumDescriptions (compatible con Redocly)
     * - false: Ignorar descripciones de casos
     */
    'enum_cases_description_strategy' => 'description',

    /**
     * Cómo almacenar los nombres de casos de enum.
     *
     * Opciones:
     * - 'names': En extensión x-enumNames
     * - 'varnames': En extensión x-enum-varnames
     * - false: No almacenar nombres de casos
     */
    'enum_cases_names_strategy' => false,
];
```

### Estrategia de Descripción: 'description' (por defecto)

Crea una tabla markdown en la descripción del esquema:

```json
{
    "title": "EstadoExamen",
    "type": "string",
    "enum": ["en_progreso", "completado", "abandonado"],
    "description": "|---|---|\\n|`en_progreso`|El examen está siendo tomado actualmente.|\\n|`completado`|El examen ha finalizado y sido calificado.|\\n|`abandonado`|El examen fue abandonado antes de completarse.|"
}
```

### Estrategia de Descripción: 'extension'

Usa la extensión `x-enumDescriptions` (compatible con Redocly):

```json
{
    "title": "EstadoExamen",
    "type": "string",
    "enum": ["en_progreso", "completado", "abandonado"],
    "x-enumDescriptions": {
        "en_progreso": "El examen está siendo tomado actualmente.",
        "completado": "El examen ha finalizado y sido calificado.",
        "abandonado": "El examen fue abandonado antes de completarse."
    }
}
```

## Usar Enums en Validación

```php
use Illuminate\Validation\Rule;

class IniciarExamenRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            /**
             * Tipo de examen a crear.
             * @example "completo"
             */
            'tipo_examen' => ['required', Rule::enum(TipoExamen::class)],
        ];
    }
}
```

## Usar Enums en Recursos

```php
class ExamenResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            /**
             * Estado actual del examen.
             */
            'estado' => $this->estado, // enum EstadoExamen
            'tipo' => $this->tipo_examen, // enum TipoExamen
        ];
    }
}
```

## Enums Respaldados por Enteros

```php
enum NivelDificultad: int
{
    /**
     * Preguntas fáciles para principiantes.
     */
    case Facil = 1;

    /**
     * Preguntas de dificultad media.
     */
    case Medio = 2;

    /**
     * Preguntas difíciles para usuarios avanzados.
     */
    case Dificil = 3;
}
```

Genera:

```json
{
    "title": "NivelDificultad",
    "type": "integer",
    "enum": [1, 2, 3]
}
```

## Convención de Nomenclatura de Enums

Siguiendo las convenciones de Laravel/PHP, los nombres de casos de enum deben estar en TitleCase:

```php
// Bueno
enum TipoExamen: string
{
    case Completo = 'completo';
    case PorMateria = 'por_materia';
    case Practica = 'practica';
}

// Evitar
enum TipoExamen: string
{
    case COMPLETO = 'completo';    // ALL_CAPS
    case por_materia = 'por_materia'; // snake_case
}
```

## Obtener Enum desde Solicitud

```php
// En FormRequest
public function toEnum(string $key, string $enumClass): mixed
{
    $value = $this->validated($key);
    return $enumClass::tryFrom($value);
}

// En Controlador
public function store(IniciarExamenRequest $request): ExamenResource
{
    $tipoExamen = $request->toEnum('tipo_examen', TipoExamen::class);
    // ...
}
```

# Referencia de Atributos de Scramble

Referencia completa para todos los atributos PHP disponibles en dedoc/scramble.

## Atributos de Parámetro

Todos los atributos de parámetro comparten estas propiedades comunes:

| Propiedad | Tipo | Descripción |
|-----------|------|-------------|
| `$name` | string | Nombre del parámetro (requerido) |
| `$description` | string | Descripción del parámetro |
| `$required` | bool | ¿Es el parámetro requerido? |
| `$deprecated` | bool | ¿Está el parámetro obsoleto? |
| `$type` | string | Tipo del parámetro |
| `$format` | string | Formato (uuid, email, date-time) |
| `$default` | mixed | Valor por defecto |
| `$example` | mixed | Un solo ejemplo |
| `$examples` | array | Múltiples ejemplos |
| `$infer` | bool | Mezclar con información inferida (por defecto: true) |

### QueryParameter

Documenta parámetros de cadena de consulta.

```php
use Dedoc\Scramble\Attributes\QueryParameter;

class ExamenController extends Controller
{
    #[QueryParameter('per_page', description: 'Elementos por página', type: 'int', default: 15)]
    #[QueryParameter('status', description: 'Filtrar por estado', type: 'string', example: 'en_progreso')]
    #[QueryParameter('materia_id', description: 'Filtrar por materia', type: 'int', required: false)]
    public function index(): AnonymousResourceCollection
    {
        // ...
    }
}
```

### PathParameter

Documenta parámetros de ruta URL.

```php
use Dedoc\Scramble\Attributes\PathParameter;

#[PathParameter('examen', description: 'ID del examen', type: 'int', example: 42)]
public function show(Examen $examen): ExamenResource
{
    // ...
}

// Para rutas UUID
#[PathParameter('uuid', description: 'UUID del recurso', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440000')]
public function showByUuid(string $uuid): ExamenResource
{
    // ...
}
```

### BodyParameter

Documenta campos del cuerpo de solicitud.

```php
use Dedoc\Scramble\Attributes\BodyParameter;

#[BodyParameter('titulo', description: 'Título del examen', type: 'string', required: true)]
#[BodyParameter('descripcion', description: 'Descripción del examen', type: 'string')]
#[BodyParameter('preguntas', description: 'Lista de preguntas', type: 'array')]
public function store(Request $request): ExamenResource
{
    // ...
}
```

### HeaderParameter

Documenta encabezados de solicitud.

```php
use Dedoc\Scramble\Attributes\HeaderParameter;

#[HeaderParameter('X-Tenant-ID', description: 'Identificador del tenant', type: 'string', required: true)]
#[HeaderParameter('Accept-Language', description: 'Idioma preferido', type: 'string', default: 'es')]
public function index(): AnonymousResourceCollection
{
    // ...
}
```

### CookieParameter

Documenta parámetros de cookie.

```php
use Dedoc\Scramble\Attributes\CookieParameter;

#[CookieParameter('session_id', description: 'Identificador de sesión', type: 'string')]
public function checkSession(): JsonResponse
{
    // ...
}
```

## Atributos de Respuesta

### Header

Documenta encabezados de respuesta.

```php
use Dedoc\Scramble\Attributes\Header;

#[Header('X-RateLimit-Limit', 'Máximo de solicitudes por minuto', type: 'int')]
#[Header('X-RateLimit-Remaining', 'Solicitudes restantes', type: 'int')]
#[Header('X-Request-ID', 'ID de seguimiento de solicitud', type: 'string', format: 'uuid')]
public function index(): AnonymousResourceCollection
{
    // ...
}
```

### Response

Documenta respuestas manualmente.

```php
use Dedoc\Scramble\Attributes\Response;

// Agrega descripción a respuesta 200
#[Response('Lista de exámenes disponibles')]
public function index(): AnonymousResourceCollection
{
    // ...
}

// Documenta código de estado específico
#[Response(201, 'Examen creado exitosamente', type: 'array{id: int, message: string}')]
public function store(Request $request): JsonResponse
{
    // ...
}

// Múltiples respuestas
#[Response(200, 'Éxito')]
#[Response(404, 'Examen no encontrado', type: 'array{message: string}')]
#[Response(422, 'Validación fallida')]
public function show(Examen $examen): ExamenResource
{
    // ...
}
```

## Atributos de Metadatos

### Group

Agrupa y ordena endpoints.

```php
use Dedoc\Scramble\Attributes\Group;

// Agrupación simple
#[Group('Exámenes')]
class ExamenController extends Controller
{
    // Todos los endpoints agrupados bajo "Exámenes"
}

// Con peso para ordenamiento (menor = primero)
#[Group('Autenticación', weight: 0)]
class LoginController extends Controller {}

#[Group('Exámenes', weight: 1)]
class ExamenController extends Controller {}

#[Group('Estadísticas', weight: 2)]
class EstadisticaController extends Controller {}
```

### Endpoint

Establece metadatos de operación.

```php
use Dedoc\Scramble\Attributes\Endpoint;

#[Endpoint(
    operationId: 'getExam',
    title: 'Obtener detalles del examen',
    description: 'Recupera un solo examen con todas sus secciones y preguntas'
)]
public function show(Examen $examen): ExamenResource
{
    // ...
}

// Sobrescribir método HTTP (útil para rutas PUT|PATCH)
#[Endpoint(method: 'PATCH')]
public function update(Request $request, Examen $examen): ExamenResource
{
    // ...
}
```

### SchemaName

Nombres de esquema personalizados para recursos y modelos.

```php
use Dedoc\Scramble\Attributes\SchemaName;

#[SchemaName('User')]
class UserResource extends JsonResource
{
    // Aparecerá como "User" en lugar de "UserResource" en la documentación
}

// Para clases con el mismo nombre en diferentes espacios de nombres
#[SchemaName('AdminUser')]
class UserResource extends JsonResource {} // En espacio de nombres Admin

#[SchemaName('PublicUser')]
class UserResource extends JsonResource {} // En espacio de nombres Api
```

### ExcludeRouteFromDocs

Oculta endpoints específicos.

```php
use Dedoc\Scramble\Attributes\ExcludeRouteFromDocs;

class ExamenController extends Controller
{
    #[ExcludeRouteFromDocs]
    public function internalEndpoint(): JsonResponse
    {
        // Esto no aparecerá en la documentación de la API
    }
}
```

### ExcludeAllRoutesFromDocs

Oculta todos los endpoints de un controlador.

```php
use Dedoc\Scramble\Attributes\ExcludeAllRoutesFromDocs;

#[ExcludeAllRoutesFromDocs]
class InternalController extends Controller
{
    // Ninguno de estos endpoints aparecerá en la documentación
    public function healthCheck(): JsonResponse {}
    public function metrics(): JsonResponse {}
}
```

## Objeto de Ejemplo

Para escenarios de ejemplo complejos:

```php
use Dedoc\Scramble\Attributes\QueryParameter;
use Dedoc\Scramble\Attributes\Example;

#[QueryParameter(
    'status',
    description: 'Filtrar por estado del examen',
    examples: [
        'active' => new Example('en_progreso', summary: 'Exámenes activos', description: 'Devuelve exámenes actualmente en progreso'),
        'completed' => new Example('completado', summary: 'Exámenes completados', description: 'Devuelve exámenes finalizados'),
        'abandoned' => new Example('abandonado', summary: 'Exámenes abandonados'),
    ]
)]
public function index(): AnonymousResourceCollection
{
    // ...
}
```

## Combinando Múltiples Atributos

```php
use Dedoc\Scramble\Attributes\{Group, Endpoint, QueryParameter, Response, Header};

#[Group('API de Exámenes', weight: 1)]
class ExamenController extends Controller
{
    #[Endpoint(operationId: 'listarExams', title: 'Listar todos los exámenes')]
    #[QueryParameter('per_page', type: 'int', default: 15)]
    #[QueryParameter('status', type: 'string')]
    #[Header('X-Total-Count', 'Número total de exámenes', type: 'int')]
    #[Response('Lista paginada de exámenes')]
    public function index(): AnonymousResourceCollection
    {
        // ...
    }
}
```

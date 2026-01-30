# Guía de Personalización de Scramble

Opciones avanzadas de personalización para la generación de documentos OpenAPI.

## Transformadores de Documento

Modifica todo el documento OpenAPI después de la generación.

### Uso Básico

```php
// app/Providers/AppServiceProvider.php
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;

public function boot(): void
{
    Scramble::configure()
        ->withDocumentTransformers(function (OpenApi $openApi) {
            // Modificar documento aquí
            $openApi->info->title = 'API de SaberSim';
            $openApi->info->description = 'API para simulacros de exámenes';
            $openApi->info->version = config('app.api_version', '1.0.0');
        });
}
```

### Transformador Basado en Clase

```php
// app/OpenApi/Transformers/AddApiInfo.php
namespace App\OpenApi\Transformers;

use Dedoc\Scramble\Contracts\DocumentTransformer;
use Dedoc\Scramble\Support\Generator\OpenApi;

class AddApiInfo implements DocumentTransformer
{
    public function transform(OpenApi $openApi): void
    {
        $openApi->info->title = config('app.name') . ' API';
        $openApi->info->description = 'API RESTful para gestión de exámenes';
        $openApi->info->termsOfService = 'https://example.com/terms';
        $openApi->info->contact = [
            'name' => 'Soporte de API',
            'email' => 'api@example.com',
        ];
        $openApi->info->license = [
            'name' => 'MIT',
            'url' => 'https://opensource.org/licenses/MIT',
        ];
    }
}

// Registrar en AppServiceProvider
Scramble::configure()
    ->withDocumentTransformers(AddApiInfo::class);
```

### Múltiples Transformadores con Orden

```php
use Dedoc\Scramble\Configuration\DocumentTransformers;

Scramble::configure()
    ->withDocumentTransformers(function (DocumentTransformers $transformers) {
        $transformers
            ->prepend(AddSecuritySchemes::class)  // Ejecutar primero
            ->append(AddApiInfo::class)            // Ejecutar después de los predeterminados
            ->append(function (OpenApi $openApi) {
                // Transformador en línea
            });
    });
```

## Transformadores de Operación

Modifica operaciones de API individuales.

### Agregar Encabezados Comunes

```php
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;

Scramble::configure()
    ->withOperationTransformers(function (Operation $operation) {
        $operation->addParameters([
            (new Parameter('X-Request-ID', 'header'))
                ->description('Identificador único de solicitud')
                ->setSchema(['type' => 'string', 'format' => 'uuid']),
        ]);
    });
```

### Transformador de Operación Basado en Clase

```php
// app/OpenApi/Transformers/AddRateLimitHeaders.php
namespace App\OpenApi\Transformers;

use Dedoc\Scramble\Contracts\OperationTransformer;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;

class AddRateLimitHeaders implements OperationTransformer
{
    public function transform(Operation $operation, RouteInfo $routeInfo): void
    {
        // Agregar a todas las operaciones
        $operation->addParameters([
            (new Parameter('X-RateLimit-Limit', 'header'))
                ->description('Límite de solicitudes por minuto'),
            (new Parameter('X-RateLimit-Remaining', 'header'))
                ->description('Solicitudes restantes'),
        ]);
    }
}
```

### Transformación de Operación Condicional

```php
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\RouteInfo;

Scramble::configure()
    ->withOperationTransformers(function (Operation $operation, RouteInfo $routeInfo) {
        // Solo para rutas específicas
        if (str_contains($routeInfo->route->uri(), 'admin')) {
            $operation->addParameters([
                (new Parameter('X-Admin-Token', 'header'))
                    ->required(true)
                    ->description('Token de autenticación de administrador'),
            ]);
        }
    });
```

## Configuración de Autenticación

### Token Bearer (Sanctum)

```php
use Dedoc\Scramble\Support\Generator\SecurityScheme;

Scramble::configure()
    ->withDocumentTransformers(function (OpenApi $openApi) {
        $openApi->secure(
            SecurityScheme::http('bearer', 'JWT')
        );
    });
```

### Múltiples Esquemas de Autenticación

```php
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\SecurityRequirement;

Scramble::configure()
    ->withDocumentTransformers(function (OpenApi $openApi) {
        // Definir esquemas
        $openApi->components->securitySchemes['bearer'] = SecurityScheme::http('bearer');
        $openApi->components->securitySchemes['apiKey'] = SecurityScheme::apiKey('header', 'X-API-Key');

        // Requerir ambos
        $openApi->security[] = new SecurityRequirement([
            'bearer' => [],
            'apiKey' => [],
        ]);
    });
```

### Configuración OAuth2

```php
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\Support\Generator\SecuritySchemes\OAuthFlow;

Scramble::configure()
    ->withDocumentTransformers(function (OpenApi $openApi) {
        $openApi->secure(
            SecurityScheme::oauth2()
                ->flow('authorizationCode', function (OAuthFlow $flow) {
                    $flow
                        ->authorizationUrl(config('app.url') . '/oauth/authorize')
                        ->tokenUrl(config('app.url') . '/oauth/token')
                        ->refreshUrl(config('app.url') . '/oauth/token/refresh')
                        ->addScope('read', 'Acceso de lectura')
                        ->addScope('write', 'Acceso de escritura')
                        ->addScope('admin', 'Acceso de administrador');
                })
        );
    });
```

## Resolvedor de Rutas Personalizado

Controla qué rutas se documentan:

```php
use Illuminate\Routing\Route;

Scramble::configure()
    ->routes(function (Route $route) {
        // Solo documentar rutas que comienzan con 'api/v1'
        return str_starts_with($route->uri(), 'api/v1');
    });
```

## Preferir PATCH sobre PUT

```php
Scramble::configure()
    ->preferPatchMethod();
```

## URLs de Servidor Personalizadas

En `config/scramble.php`:

```php
'servers' => [
    'Local' => 'api',
    'Staging' => 'https://staging.example.com/api',
    'Production' => 'https://api.example.com',
],
```

## Múltiples Versiones de API

```php
// app/Providers/AppServiceProvider.php
use Dedoc\Scramble\Scramble;

public function boot(): void
{
    // API por defecto (v1)
    Scramble::configure('default')
        ->routes(fn ($route) => str_starts_with($route->uri(), 'api/v1'));

    // API v2
    Scramble::configure('v2')
        ->routes(fn ($route) => str_starts_with($route->uri(), 'api/v2'))
        ->withDocumentTransformers(function (OpenApi $openApi) {
            $openApi->info->version = '2.0.0';
        });
}
```

Acceder a documentación:
- V1: `/docs/api`
- V2: `/docs/v2`

## Configuración de Middleware

```php
// config/scramble.php
'middleware' => [
    'web',
    RestrictedDocsAccess::class,
    // Agregar middleware personalizado
    \App\Http\Middleware\CheckDocsAccess::class,
],
```

## Personalización de UI

```php
// config/scramble.php
'ui' => [
    'title' => 'Documentación de API SaberSim',
    'theme' => 'dark',           // light, dark, system
    'hide_try_it' => false,      // Ocultar característica "Try It"
    'hide_schemas' => false,     // Ocultar esquemas en la barra lateral
    'logo' => '/images/logo.png',
    'try_it_credentials_policy' => 'include', // omit, include, same-origin
    'layout' => 'responsive',    // sidebar, responsive, stacked
],
```

## Exportar Especificación

```bash
# Exportación por defecto
php artisan scramble:export

# Ruta personalizada
php artisan scramble:export --path=public/openapi.json

# Versión específica de API
php artisan scramble:export --api=v2 --path=public/api-v2.json
```

## Ruta de Documentación Personalizada

```php
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

Route::get('/api-docs', function () {
    return view('scramble::docs', [
        'spec' => file_get_contents(base_path('api.json')),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
})->middleware([RestrictedDocsAccess::class]);
```

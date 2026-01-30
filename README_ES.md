# SaberSim Backend

API backend para una plataforma de simulacros y exámenes en línea. Este proyecto permite a los usuarios registrarse, iniciar exámenes de práctica, responder preguntas y realizar seguimiento de sus estadísticas de desempeño.

## Características

### Autenticación
- Registro de usuarios
- Inicio de sesión con tokens de API (Laravel Sanctum)
- Cierre de sesión
- Restablecimiento de contraseña

### Gestión de Exámenes
- Creación de exámenes con diferentes tipos (simulacro, práctica)
- Estados de examen: En progreso, Completado, Abandonado
- Sistema de secciones para organizar preguntas
- Cálculo automático de puntajes y tiempo
- Envío de respuestas pregunta por pregunta o en masa
- Finalización o abandono de exámenes

### Materias y Preguntas
- Organización por materias/subjects
- Temas dentro de cada materia
- Preguntas de opción múltiple
- Niveles de dificultad configurable
- Iconos personalizados por materia

### Estadísticas de Usuario
- Seguimiento de exámenes completados por materia
- Cálculo de puntaje promedio
- Registro del mejor puntaje alcanzado
- Tiempo total invertido
- Fecha del último examen
- Total de preguntas respondidas y correctas

### Documentación de API
- Documentación automática OpenAPI/Swagger usando Scramble
- Interfaz interactiva disponible en `/docs`

## Stack Tecnológico

- **PHP**: 8.4.6
- **Framework**: Laravel 12
- **Autenticación**: Laravel Sanctum v4
- **Testing**: Pest v4
- **Documentación API**: Dedoc Scramble
- **Estilos**: Tailwind CSS v4

## Instalación

### Requisitos Previos
- PHP >= 8.2
- Composer
- Node.js y NPM
- Base de datos (MySQL, PostgreSQL, o SQLite)

### Pasos de Instalación

1. Clonar el repositorio
```bash
git clone <repositorio-url>
cd sabersim-backend
```

2. Instalar dependencias
```bash
composer install
npm install
```

3. Configurar el archivo de entorno
```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar la base de datos en el archivo `.env`

5. Ejecutar las migraciones
```bash
php artisan migrate
```

6. (Opcional) Ejecutar los seeders
```bash
php artisan db:seed
```

7. Construir los assets del frontend
```bash
npm run build
```

## Comandos Disponibles

### Desarrollo
```bash
# Iniciar servidor de desarrollo con Vite y cola de trabajos
composer run dev

# Solo servidor de Laravel
php artisan serve

# Solo Vite para desarrollo del frontend
npm run dev
```

### Testing
```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests en modo compacto
php artisan test --compact

# Filtrar tests específicos
php artisan test --filter=nombre_del_test
```

### Calidad de Código
```bash
# Formatear código con Laravel Pint
vendor/bin/pint

# Formatear solo archivos modificados
vendor/bin/pint --dirty
```

## Estructura del Proyecto

```
app/
├── Enums/              # Enumeraciones (EstadoExamen, TipoExamen, etc.)
├── Http/
│   ├── Controllers/    # Controladores de la API
│   ├── Requests/       # Form Request para validación
│   └── Resources/      # API Resources para respuestas
├── Models/             # Modelos Eloquent
└── Services/           # Lógica de negocio
```

## Modelos de Datos

### Materia
- Materias/subjects disponibles para practicar
- Contiene preguntas organizadas por temas
- Configuración de tiempo límite y cantidad de preguntas

### Topico
- Temas dentro de una materia
- Agrupación lógica de preguntas

### Pregunta
- Preguntas de opción múltiple
- Asociadas a un tema y materia
- Con opciones de respuesta

### Examen
- Exámenes creados por usuarios
- Relacionado con una materia específica
- Estados: En progreso, Completado, Abandonado
- Tipos: Simulacro, Práctica

### RespuestaUsuario
- Respuestas proporcionadas por el usuario
- Asociadas a una pregunta y examen

### EstadisticaUsuario
- Estadísticas de desempeño por usuario y materia
- Puntajes promedios, mejores puntajes, tiempo invertido

## Endpoints de la API

### Autenticación
- `POST /api/auth/register` - Registrar nuevo usuario
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión (requiere autenticación)
- `POST /api/auth/forgot-password` - Solicitar restablecimiento de contraseña
- `POST /api/auth/reset-password` - Restablecer contraseña

### Materias (Público)
- `GET /api/v1/materias` - Listar todas las materias
- `GET /api/v1/materias/{materia}` - Obtener detalles de una materia

### Exámenes (Requiere autenticación)
- `GET /api/v1/examenes` - Listar exámenes del usuario
- `POST /api/v1/examenes` - Crear nuevo examen
- `GET /api/v1/examenes/{examen}` - Obtener detalles de un examen
- `POST /api/v1/examenes/{examen}/respuesta` - Enviar respuesta a una pregunta
- `POST /api/v1/examenes/{examen}/finalizar` - Finalizar examen
- `POST /api/v1/examenes/{examen}/abandonar` - Abandonar examen

### Estadísticas (Requiere autenticación)
- `GET /api/v1/estadisticas` - Estadísticas generales del usuario
- `GET /api/v1/estadisticas/{materia}` - Estadísticas por materia

## Documentación

Una vez que el servidor está corriendo, puedes acceder a la documentación interactiva de la API en:
- `http://localhost:8000/docs` (o el puerto configurado)

## Licencia

Este proyecto está bajo la licencia MIT.

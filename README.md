# laravel-dev

Breve descripción del proyecto.

## Tecnologías
- PHP 8.3
- Laravel 12
- Docker
- PostgreSQL
- API REST
- Nginx
- Sanctum

## Requisitos
- Docker
- Docker Compose

## Instalación y uso
Estos comandos se probaron con la herramienta git bash
1. Clonar el repositorio:
https://github.com/rudyca16-ai/laravel-dev
2. cd proyecto
3. cp .env.example .env
4. docker-compose up -d --build // comando que construye y ejecuta el docker que están configurados en el archivo docker-compose.yml
5. docker ps // comando que muestra los dockers que se están ejecutando, el contenedor se llama laravel-dev,
en la columna NAMES se ven los 3 contenedores dentro de esta, utilizaremos el laravel_bd y laravel_app para ejecutar scripts.
Estos están configurados en los archivos siguientes para que el docker funcione:
- laravel-dev -> docker -> nginx -> default.conf // aquí está la configuración del servidor web, necesario para manejar 
las solicitudes de servicios
- laravel-dev -> docker-compose.yml // el archivo de configuración de Docker Compose. Sirve para definir varios 
contenedores y cómo deben funcionar juntos.
- laravel-dev -> Dockerfile // define como construír la imagen de un contenedor
6. docker exec -it laravel_app bash // comando que genera un bash interactivo en donde se puede ejecutar comandos con composer,
es el contenedor del proyecto laravel:
root@666ef84bb5e8:/var/www#
7. composer -v
8. composer install
9. php artisan migrate // comando para migrar las tablas
10. php artisan db:seed // comando para ejecutar seeders
11. php artisan route:list // comando para ver las rutas de los apis
12. exit // comando Para salir del comando ejecutado en el punto 6.
13. docker exec -it laravel_db bash // comando que genera un bash interactivo en donde se puede ejecutar comandos de postgresql, 
es el contenedor de la base de datos sql.
root@af1cd934a569:/#
14. psql -U laravel -d laravel_desafio // comando para general un bash en donde ejecutar scripts de sql
15. laravel_desafio=# \dt // para ver las tablas existentes
16. laravel_desafio-# \d courses // para ver el diseño de una tabla específica, en este caso courses
17. se puede ejecutar cualquier script de sql, por ejemplo para ver los datos de la tabla students:
laravel_desafio=# select * from students;
18. laravel_desafio=# \q // comando para salir del comando utilizado en el punto 13.
19. root@af1cd934a569:/# exit // comando para salir del comando utilizado en el punto 12. Se sale del contenedor de laravel_db

## Dump SQL
Crear el archivo dump sql con el siguiente comando:
1. docker exec -t laravel_db pg_dump -U laravel -d laravel_desafio -F p > dump.sql
// El archivo se guarda en la raíz del proyecto con el nombre de dump.sql

## Testing
1. Los archivos se encuentran en laravel-dev -> test -> Feature
2. php artisan test --filter=CourseTest // comando para ejecutar el test de Curso
3. php artisan test --filter=EnrollmentTest // comando para ejecutar el test de Enrollment
4. php artisan test --filter=StudentTest // comando para ejecutar el test de Student


## APIs
1. Las rutas se encuentran en laravel-dev -> routes -> api.php

| Método | Endpoint           | Descripción             |
| ------ | ------------------ | ----------------------- |
| POST   | /api/auth/register | Registrar nuevo usuario |
| POST   | /api/auth/login    | Login y obtener token   |

- El servicio /api/auth/register devuelve un Bearer Token para usar en los endpoints protegidos.
- Utiliza la tabla users para guardar a los usuarios

Students

| Método | Endpoint           | Descripción                  |
| ------ | ------------------ | ---------------------------- |
| GET    | /api/students      | Listar todos los estudiantes |
| POST   | /api/students      | Crear un estudiante          |
| GET    | /api/students/{id} | Obtener estudiante por ID    |
| PUT    | /api/students/{id} | Actualizar estudiante        |
| DELETE | /api/students/{id} | Eliminar estudiante          |

- Utiliza la tabla students para guardar a los estudiantes

Courses

| Método | Endpoint          | Descripción             |
| ------ |-------------------|-------------------------|
| GET    | /api/courses      | Listar todos los cursos |
| POST   | /api/courses      | Crear un curso          |
| GET    | /api/courses/{id} | Obtener curso por ID    |
| PUT    | /api/courses/{id} | Actualizar curso        |
| DELETE | /api/courses/{id} | Eliminar curso          |

- Utiliza la tabla courses para guardar los cursos

Enrollments

| Método | Endpoint              | Descripción                    |
| ------ |-----------------------|--------------------------------|
| GET    | /api/enrollments      | Listar todos las inscripciones |
| POST   | /api/enrollments      | Crear una inscripcion          |
| DELETE | /api/enrollments/{id} | Eliminar inscripcion           |

- Utiliza la tabla enrollments para guardar las inscripciones







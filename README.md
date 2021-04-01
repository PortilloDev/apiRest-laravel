Configuración carpetas de test

1 - Crear fichero test
    php artisan make:test Http/Controllers/Api/PostControllerTest
2 - Crear ficheros originales
    - Modelo con el factory y la migración
        php artisan make:model Post -fm
    - Crear el controlador enlazandolo con el modelo 
        php artisan make:controller Api/PostController --api --model=Post
    - Crear la base de datos de prueba
        - En la carpeta database se crea el archivo database.sqlite
        - En la carpeta config/database.php se modifica la conexión a sqlite
            propiedad database, dejandola así =>   'database' => database_path('database.sqlite'),

## Установка

1. Скачивание плагина
```
composer require masterdmx/laravel-relinking
```

2. Подключение провайдера в config app.php
```php
'providers' => [
    MasterDmx\LaravelRelinking\RelinkingServiceProvider::class,
]
```

3. Публикация конфига
```
php artisan vendor:publish --provider="MasterDmx\LaravelRelinking\RelinkingServiceProvider" --tag="config"
```

4. Публикация миграций
```
php artisan vendor:publish --provider="MasterDmx\LaravelRelinking\RelinkingServiceProvider" --tag="migrations"
```

4. Миграция
```
php artisan migrate
```

## Контексты

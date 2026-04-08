# Laravel Admin Multi Language

[![License](https://img.shields.io/packagist/l/laravel-packages/multi-language.svg?style=flat-square)](https://packagist.org/packages/laravel-packages/multi-language)
[![PHP Version](https://img.shields.io/packagist/php-v/laravel-packages/multi-language.svg?style=flat-square)](https://packagist.org/packages/laravel-packages/multi-language)
[![Latest Version](https://img.shields.io/packagist/v/laravel-packages/multi-language.svg?style=flat-square)](https://packagist.org/packages/laravel-packages/multi-language)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-packages/multi-language.svg?style=flat-square)](https://packagist.org/packages/laravel-packages/multi-language)
[![Laravel](https://img.shields.io/badge/Laravel-12-red?style=flat-square)](https://laravel.com)
[![Octane](https://img.shields.io/badge/Octane-ready-green?style=flat-square)](https://laravel.com/docs/octane)

Профессиональное мультиязычное расширение для Open Admin Panel с поддержкой динамического переключения языков, сохранением данных формы, кэшированием и безопасной обработкой запросов.

## 🌟 **Возможности**

### 🚀 **Производительность**
- ✅ **Кэширование конфигурации** - минимальное время отклика
- ✅ **Laravel Octane Ready** - полная поддержка Swoole/RoadRunner
- ✅ **Memory cache** для высоконагруженных проектов
- ✅ **Оптимизированный Middleware** - минимальный overhead
- ✅ **Ленивая загрузка** ресурсов

### 🛡️ **Безопасность**
- ✅ **CSRF защита** всех запросов
- ✅ **Валидация локалей** - только разрешенные языки
- ✅ **Безопасные cookie** (httpOnly, secure, SameSite)
- ✅ **Защита от XSS** через санитизацию данных
- ✅ **Логирование подозрительных действий**
- ✅ **Rate limiting** для API endpoints

### 💾 **Хранение данных**
- ✅ **Сохранение в cookie** - быстрое определение языка
- ✅ **Сохранение в БД** - для авторизованных пользователей
- ✅ **Автоматическая миграция** поля `locale` в таблице администраторов
- ✅ **Синхронизация** cookie и БД

### 🎨 **UI/UX**
- ✅ **Флаги стран** в навигационном меню
- ✅ **Визуальный фидбек** при переключении (preloader)
- ✅ **Адаптивный дизайн** для всех устройств
- ✅ **Поддержка темной темы**
- ✅ **Плавные анимации**
- ✅ **RTL поддержка** (для арабского/иврита)

### 🔧 **Функциональность**
- ✅ **Автоматическое определение языка** (cookie + браузер)
- ✅ **Ручное переключение** через навбар или страницу входа
- ✅ **Сохранение данных формы** при смене языка на логине
- ✅ **Type-safe локали** через PHP 8.1 Enum
- ✅ **Интеграция с Open Admin Core**
- ✅ **API Tester support**

### 📊 **Мониторинг и отладка**
- ✅ **Структурированное логирование** с контекстом
- ✅ **Трекинг производительности** (request_id, duration)
- ✅ **Логирование безопасности** (подозрительные попытки)
- ✅ **Метрики времени выполнения**
- ✅ **Поддержка ELK/Splunk** через теги

---

## 📋 **Содержание**

- [Структура пакета](#структура-пакета)
- [Требования](#требования)
- [Установка](#установка)
- [Конфигурация](#конфигурация)
- [Использование](#использование)
- [API](#api)
- [Производительность](#производительность)
- [Безопасность](#безопасность)
- [Тестирование](#тестирование)
- [Обновление](#обновление)
- [Участие в разработке](#участие-в-разработке)
- [Лицензия](#лицензия)

---

## <a name="структура-пакета"></a>🔧 **Структура пакета**

```text
laravel-packages/multi-language/
│
├── 📂 src/
│   ├── 📂 Http/
│   │   └── 📂 Controllers/
│   │       └── 📂 Api/
│   │           └── 📄 MultiLanguageApiController.php
│   │       └── 📄 MultiLanguageController.php
│   │
│   ├── 📂 Middlewares/
│   │   └── 📄 MultiLanguageMiddleware.php
│   │
│   ├── 📂 Widgets/
│   │   └── 📄 LanguageMenu.php
│   │
│   ├── 📂 Traits/
│   │   └── 📄 ApiResponse.php
│   │   └── 📄 LogsWithContext.php
│   │
│   ├── 📂 Enums/
│   │   └── 📄 Locale.php
│   │
│   ├── 📂 Octane/
│   │   └── 📄 OctaneHandler.php
│   │
│   ├── 📄 MultiLanguage.php
│   └── 📄 MultiLanguageServiceProvider.php
│
├── 📂 resources/
│   ├── 📂 assets/
│   │   ├── 📂 js/
│   │   │   └── 📄 multilanguage.js
│   │   └── 📂 css/
│   │       └── 📄 multilanguage.css
│   │  
│   ├── 📂 lang/
│   │   ├── 📂 en/
│   │   │   └── 📄 multi-language.php
│   │   └── 📂 ru/
│   │       └── 📄 multi-language.php
│   │
│   └── 📂 views/
│       │   └── 📂 partials/
│       │       └── 📄 login-language-selector.blade.php
│       └── 📄 language-menu.blade.php
│
├── 📂 routes/
│   └── 📄 web.php
│
├── 📂 config/
│   └── 📄 multi-language.php
│
├── 📂 tests/
│   ├── 📂 Feature/
│   │   ├── 📄 MultiLanguageControllerTest.php
│   │   └── 📄 MultiLanguageMiddlewareTest.php
│   ├── 📂 Unit/
│   │   ├── 📄 LocaleTest.php
│   │   └── 📄 LanguageMenuTest.php
│   └── 📄 TestCase.php
│
├── 📄 composer.json
├── 📄 CHANGELOG.md
├── 📄 README.md
├── 📄 phpunit.xml
└── 📄 LICENSE
```
### 📋 **Детальное описание каждого файла**

#### 🎯 **Корневые файлы**


|**Файл**| 	**Назначение**                                 |
|-----------|-------------------------------------------------|
|composer.json| 	Конфигурация пакета, зависимости, автозагрузка |
|README.md	| Документация, инструкция по установке           |
|CHANGELOG.md| 	История изменений версий                       |
|phpunit.xml| 	Конфигурационный файл для PHPUnit              |
|LICENSE| 	MIT лицензия                                   |

🧩 src/ - Основной код

📄 MultiLanguage.php
```php
- Базовый класс расширения
- Содержит: $name, $views, $assets
- Методы: getVersion(), getName(), getInfo()
```
📄 MultiLanguageServiceProvider.php
```php
- Регистрация пакета в Laravel
- Загрузка views
- Публикация assets и config
- Регистрация middleware
- Добавление виджета в навбар
- Регистрация Octane обработчика
- Подключение CSS/JS (registerAssets())
```
📂 Http/Controllers/
📄 MultiLanguageController.php
```php
- Обработка POST /locale (смена языка)
- Обработка GET /auth/login (кастомная страница логина)
- API /api/languages (список языков)
- Логирование, валидация, работа с cookie
```
📂 Middlewares/
📄 MultiLanguageMiddleware.php
```php
- Определение локали из cookie/браузера
- Кэширование конфигурации
- Сохранение локали в БД для авторизованных
- Поддержка Octane
- Логирование производительности
- Сброс кэша схемы
```
📂 Widgets/
📄 LanguageMenu.php
```php
- Рендер выпадающего меню языков
- Получение текущей локали из cookie
- Формирование массива языков с флагами
- Безопасная работа с Enum (tryFrom)
```
📂 Traits/
📄 LogsWithContext.php
```php
- Структурированное логирование
- Request ID для трейсинга
- Логирование безопасности
- Логирование производительности
- Метрики времени выполнения
```
📂 Enums/
📄 Locale.php (PHP 8.1+ Enum)
```php
- Список поддерживаемых языков
- Методы: label(), flag(), direction()
- dateFormat(), timezone()
- isValid(), fromString(), tryFrom()
- toConfigArray()
```
📂 Octane/
📄 OctaneHandler.php
```php
- Обработка событий Laravel Octane
- WorkerStarting, RequestReceived
- RequestTerminated, WorkerStopping
- Memory cache для конфигурации
- Сброс состояний между запросами
```
🎨 resources/ - Ресурсы
📂 assets/js/
📄 multilanguage.js
```javascript
- Класс MultiLanguage (Singleton)
- Получение CSRF токена (meta + cookie)
- Обработка кликов на .language
- Визуальный фидбек (preloader, loading)
- Сохранение/восстановление данных формы
- Fetch запросы к /locale
- Поддержка PJAX/Turbolinks
```
📂 assets/css/
📄 multilanguage.css
```css
- CSS переменные для прелоадера
- Анимация preloader-spin
- Адаптивность под мобильные
- Темная тема (prefers-color-scheme)
- Стили для .language.loading
- Анимация spin для загрузки
```
📂 views/
📄 language-menu.blade.php
```blade
- HTML выпадающего меню
- Флаги стран из Enum
- data-id и data-direction атрибуты
- Активный класс для текущего языка
- Без JS (чистый HTML)
```
📂 views/partials/
📄 login-language-selector.blade.php
```blade
- Частично встраиваемый список стран из Enum lkz страницs входа
- meta[name="csrf-token"] для CSRF
- Select для выбора языка
- Подключение multilanguage.js
```
🛣️ routes/
📄 web.php
```php
- POST /locale - смена языка (middleware 'web')
- GET /auth/login - кастомная страница входа
- Условная регистрация на основе конфига
```
⚙️ config/
📄 multi-language.php
```php
- enable (env)
- languages (из Enum)
- default (env)
- cookie-name (env)
- show-login-page (env)
- show-navbar (env)
- Настройки логирования
- Настройки кэширования
- Настройки производительности
```
🧪 tests/ - Тесты
📂 Feature/
📄 MultiLanguageControllerTest.php
```php
- test_it_sets_locale_and_returns_ok
- test_it_returns_login_view_with_languages
- test_it_preserves_form_data_on_locale_change
- test_it_rejects_invalid_locale
- test_it_handles_validation_errors
- test_it_returns_languages_via_api
```
📄 MultiLanguageMiddlewareTest.php
```php
- test_it_sets_locale_from_valid_cookie
- test_it_uses_default_locale_when_cookie_invalid
- test_it_uses_browser_locale_when_no_cookie
- test_it_bypasses_when_disabled
- test_it_handles_octane_mode
```
📂 Unit/
📄 LocaleTest.php
```php
- test_it_returns_correct_labels
- test_it_returns_correct_flags
- test_it_validates_locale_correctly
- test_it_creates_from_string
- test_it_uses_try_from_safely
- test_it_returns_date_formats
- test_it_returns_timezones
- test_it_converts_to_config_array
```
📄 LanguageMenuTest.php
```php
- test_it_renders_menu_with_current_locale
- test_it_handles_missing_cookie
- test_it_handles_non_enum_locales
```
📄 LogsWithContextTest.php
```php
- test_it_logs_with_context
- test_it_logs_security_events
- test_it_logs_performance_metrics
```
📄 TestCase.php
```php
- Базовый класс для тестов
- Настройка Orchestra Testbench
- Загрузка провайдеров
- Конфигурация тестового окружения
```
📊 Статистика пакета

|**Категория**|	**Количество**|
|-------------|---------------|
|PHP файлы|	14|
|JavaScript файлы|	1|
|CSS файлы|	1|
|Blade шаблоны|	2|
|Конфигурация|	1|
|Тесты|	6|
|Всего файлов|	~30|

🎯 Ключевые особенности структуры
1. Модульность - каждый компонент в своей директории
2. PSR-4 автозагрузка - правильные неймспейсы
3. Разделение ответственности - Middleware, Controllers, Widgets
4. Современный PHP - Enum, типизация, match
5. Тестируемость - полный набор тестов
6. Расширяемость - легко добавить новые языки
7. Производительность - кэширование, Octane
8. Безопасность - CSRF, валидация, логирование





## <a name="требования"></a>🔧 **Требования**

| Компонент | Версия |
|-----------|--------|
| **PHP** | ^8.2 (рекомендуется 8.3) |
| **Laravel** | ^10.0 \| ^11.0 \| ^12.0 |
| **Open Admin Core** | dev-main |
| **Database** | MySQL 5.7+ / PostgreSQL 9.6+ |
| **Node.js** | ^16.0 (для сборки ассетов) |

---

## <a name="установка"></a>🚀 **Установка**

### Шаг 1: Установка через Composer

```bash
composer require laravel-packages/multi-language
```
### Шаг 2: Публикация ресурсов
```bash
php artisan vendor:publish --provider="OpenAdminCore\Admin\MultiLanguage\MultiLanguageServiceProvider" --tag=multi-language
php artisan vendor:publish --provider="OpenAdminCore\Admin\MultiLanguage\MultiLanguageServiceProvider" --tag=multi-language-config
```
### Шаг 3: Настройка базы данных
Убедитесь, что в таблице администраторов есть поле locale:

```php
// В миграции Open Admin Core уже есть:
$table->string('locale', 10)->default('ru')->after('avatar');
```
Если нет - добавьте миграцию:

```bash
php artisan make:migration add_locale_to_admin_users_table
```
```php
public function up()
{
    Schema::table('admin_users', function (Blueprint $table) {
        $table->string('locale', 2)->default('ru')->after('remember_token');
    });
}
```
### Шаг 4: Настройка конфигурации
Добавьте в config/admin.php:

```php
'extensions' => [
    'multi-language' => [
        // Включить/выключить расширение
        'enable' => env('MULTI_LANGUAGE_ENABLE', true),
        
        // Доступные языки (ключ = код локали, значение = отображаемое название)
        'languages' => [
            'ru' => 'Русский',
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'it' => 'Italiano',
            'zh' => '中文',
            'ja' => '日本語',
        ],
        
        // Локаль по умолчанию
        'default' => env('MULTI_LANGUAGE_DEFAULT', 'ru'),
        
        // Показывать мультиязычную страницу входа
        'show-login-page' => env('MULTI_LANGUAGE_SHOW_LOGIN', true),
        
        // Показывать меню языков в навигационной панели
        'show-navbar' => env('MULTI_LANGUAGE_SHOW_NAVBAR', true),
        
        // Имя cookie для хранения локали
        'cookie-name' => env('MULTI_LANGUAGE_COOKIE', 'locale'),
    ],
],
```
### Шаг 5: Настройка аутентификации
Добавьте маршрут в исключения аутентификации в config/admin.php:

```php
'auth' => [
    'excepts' => [
        'auth/login',
        'auth/logout',
        'locale',
    ],
],
```
### Шаг 6: Очистка кэша
```bash
php artisan optimize:clear
```
## <a name="конфигурация"></a>⚙️ Детальная конфигурация
### Файл конфигурации config/multi-language.php
После публикации вы можете настроить все параметры:

```php
<?php

use OpenAdminCore\Admin\MultiLanguage\Enums\Locale;

return [
    /*
    |--------------------------------------------------------------------------
    | Основные настройки мультиязычности
    |--------------------------------------------------------------------------
    */
    'enable' => env('MULTI_LANGUAGE_ENABLE', true),
    'languages' => Locale::toConfigArray(),
    'default' => env('MULTI_LANGUAGE_DEFAULT', 'ru'),
    'cookie-name' => env('MULTI_LANGUAGE_COOKIE', 'locale'),
    'show-login-page' => env('MULTI_LANGUAGE_SHOW_LOGIN', true),
    'show-navbar' => env('MULTI_LANGUAGE_SHOW_NAVBAR', true),

    /*
    |--------------------------------------------------------------------------
    | Настройки логирования
    |--------------------------------------------------------------------------
    */
    'log_channel' => env('MULTI_LANGUAGE_LOG_CHANNEL', 'stack'),
    'log_security' => env('MULTI_LANGUAGE_LOG_SECURITY', true),
    'log_performance' => env('MULTI_LANGUAGE_LOG_PERFORMANCE', app()->environment('local')),

    /*
    |--------------------------------------------------------------------------
    | Настройки кэширования
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => env('MULTI_LANGUAGE_CACHE_TTL', 3600),
    'cache_key' => 'multi-language.config',

    /*
    |--------------------------------------------------------------------------
    | Настройки производительности
    |--------------------------------------------------------------------------
    */
    'slow_threshold_ms' => env('MULTI_LANGUAGE_SLOW_THRESHOLD', 100),
    'prefetch_browser_locale' => env('MULTI_LANGUAGE_PREFETCH_BROWSER', true),
    'save_to_database' => env('MULTI_LANGUAGE_SAVE_TO_DB', true),
];
```
### Переменные окружения .env

```env
# Основные настройки
MULTI_LANGUAGE_ENABLE=true
MULTI_LANGUAGE_DEFAULT=ru
MULTI_LANGUAGE_COOKIE=locale
MULTI_LANGUAGE_SHOW_LOGIN=true
MULTI_LANGUAGE_SHOW_NAVBAR=true

# Логирование
MULTI_LANGUAGE_LOG_CHANNEL=stack
MULTI_LANGUAGE_LOG_SECURITY=true
MULTI_LANGUAGE_LOG_PERFORMANCE=false

# Кэширование
MULTI_LANGUAGE_CACHE_TTL=3600

# Производительность
MULTI_LANGUAGE_SLOW_THRESHOLD=100
MULTI_LANGUAGE_PREFETCH_BROWSER=true
MULTI_LANGUAGE_SAVE_TO_DB=true
```

## <a name="использование"></a>🎯 Использование
### Автоматическое определение языка
Пакет автоматически определяет язык пользователя в следующем порядке:

1. Cookie (предпочтения пользователя)
2. Браузер (Accept-Language header)
3. Конфигурация (значение по умолчанию)

### Ручное переключение языка
#### Через навигационную панель
После установки в правом верхнем углу появится выпадающее меню с флагами стран:

* Клик по флагу - мгновенное переключение языка
* Визуальный фидбек (анимация загрузки)
* Автоматическая перезагрузка страницы

#### На странице входа
Доступен выпадающий список для выбора языка:

* Сохранение введенных данных (логин/пароль)
* Восстановление после перезагрузки
* CSRF защита

**Программное использование:**
```php
// Получить текущую локаль
$locale = App::getLocale();

// Установить локаль (сохранится в cookie и БД)
app(MultiLanguageController::class)->changeLocale('en');

// В middleware
$locale = $request->cookie('locale') ?? 'ru';
App::setLocale($locale);
```
### В Blade шаблонах
```blade
@if(App::getLocale() === 'ru')
    <p>Привет, мир!</p>
@else
    <p>Hello, world!</p>
@endif

<!-- Использование языковых файлов -->
{{ __('messages.welcome') }}
```
```blade
{{-- Проверка на существование --}}
<!-- НАЧАЛО: Блок для выбора языка (будет подставлен из пакета multi-language) -->
@if(View::exists('multi-language::partials.login-language-selector'))
    @include('multi-language::partials.login-language-selector')
@endif
<!-- КОНЕЦ: Блок для выбора языка -->
```

## <a name="api"></a>🌐 **REST API**

Пакет предоставляет полноценное REST API для управления мультиязычностью.

### Базовый URL
https://your-site.com/admin/api


### Аутентификация
- **Публичные endpoints** - не требуют аутентификации
- **User endpoints** - требуют `admin.auth` (авторизация в админке)
- **Admin endpoints** - требуют роль `administrator`

---

### 📋 **1. Список доступных языков**


Получить список всех поддерживаемых языков с детальной информацией.
```http
GET /admin/api/languages
```

### Пример ответа:

```json
{
    "success": true,
    "data": {
        "languages": [
            {
                "code": "ru",
                "name": "Русский",
                "native_name": "Русский",
                "flag": "🇷🇺",
                "direction": "ltr",
                "date_format": "d.m.Y",
                "timezone": "Europe/Moscow",
                "is_rtl": false
            },
            {
                "code": "en",
                "name": "English",
                "native_name": "English",
                "flag": "🇬🇧",
                "direction": "ltr",
                "date_format": "m/d/Y",
                "timezone": "Europe/London",
                "is_rtl": false
            }
        ],
        "current": "ru",
        "default": "ru",
        "total": 2
    },
    "meta": {
        "version": "2.0.0",
        "timestamp": "2024-01-15T10:30:00+00:00"
    }
}
```
---
### 🔄 **2. Получить текущую локаль**
Определяет текущую локаль пользователя (cookie → браузер → default).
```http
GET /admin/api/locale
```
### Пример ответа:
```json
{
    "success": true,
    "data": {
        "locale": "ru",
        "source": "cookie",
        "name": "Русский"
    }
}
```
---
### 🔄 **3. Установить локаль**
Устанавливает локаль пользователя и сохраняет в cookie.

```http
POST /admin/api/locale
Content-Type: application/json

{
    "locale": "en",
    "remember": true
}
```
Параметры:

| **Параметр** | **Тип** | **Обязательный** | **Описание** |
|--------------|---------|------------------|--------------|
| locale | string | ✅ | Код языка (2 буквы) |
| remember | boolean | ❌ | Сохранить надолго (30 дней) |
---
Пример ответа:
```json
{
    "success": true,
    "data": {
        "locale": "en",
        "name": "English",
        "changed_at": "2024-01-15T10:35:00+00:00"
    },
    "message": "Locale changed successfully"
}
```
Response Headers:
```text
Set-Cookie: locale=en; path=/; httponly; samesite=lax
```
---
### 👤 **4. Получить локаль пользователя (Auth)**
Возвращает локаль из базы данных для авторизованного пользователя.

```http
GET /admin/api/locale/user
Authorization: Bearer {token}
```
Пример ответа:
```json
{
    "success": true,
    "data": {
        "user_id": 1,
        "locale": "ru",
        "name": "Русский",
        "updated_at": "2024-01-15T10:30:00+00:00"
    }
}
```
---
### 👤 **5. Обновить локаль пользователя (Auth)**
Обновляет локаль в базе данных для авторизованного пользователя.

```http
PUT /admin/api/locale/user
Content-Type: application/json
Authorization: Bearer {token}

{
    "locale": "es"
}
```
Пример ответа:

```json
{
    "success": true,
    "data": {
        "user_id": 1,
        "locale": "es",
        "old_locale": "ru",
        "name": "Español",
        "updated_at": "2024-01-15T10:40:00+00:00"
    },
    "message": "User locale updated successfully"
}
```
---
### 📖 **6. Получить переводы**
Загружает все языковые файлы для указанной локали.

```http
GET /admin/api/translations/{locale}
```

Пример: `GET /admin/api/translations/ru`
```json
{
    "success": true,
    "data": {
        "locale": "ru",
        "translations": {
            "auth": {
                "failed": "Неверное имя пользователя или пароль",
                "throttle": "Слишком много попыток входа"
            },
            "pagination": {
                "previous": "&laquo; Назад",
                "next": "Вперед &raquo;"
            }
        },
        "count": 42
    }
}
```
---
### 📖 **7. Получить группу переводов**
Загружает конкретную группу переводов.

```http
GET /admin/api/translations/{locale}/{group}
```
Пример: `GET /admin/api/translations/ru/auth`
```json
{
    "success": true,
    "data": {
        "locale": "ru",
        "group": "auth",
        "translations": {
            "failed": "Неверное имя пользователя или пароль",
            "throttle": "Слишком много попыток входа"
        },
        "count": 2
    }
}
```
---
### ✅ **8. Валидация локали**
Проверяет, поддерживается ли указанная локаль.

```http
POST /admin/api/validate
Content-Type: application/json

{
    "locale": "ru"
}
```
Пример ответа:

```json
{
    "success": true,
    "valid": true,
    "locale": "ru",
    "name": "Русский",
    "available_locales": ["ru", "en", "es", "de"]
}
```
---
### 📊 **9. Статистика использования (Admin only)**
Возвращает статистику использования языков пользователями.

```http
GET /admin/api/stats
Authorization: Bearer {token}
```
Требуемые права: роль `administrator`

Пример ответа:

```json
{
    "success": true,
    "data": {
        "total_users": 150,
        "distribution": {
            "ru": {
                "count": 85,
                "percentage": 56.67,
                "name": "Русский"
            },
            "en": {
                "count": 45,
                "percentage": 30.00,
                "name": "English"
            },
            "es": {
                "count": 20,
                "percentage": 13.33,
                "name": "Español"
            }
        },
        "languages": {
            "ru": "Русский",
            "en": "English",
            "es": "Español"
        }
    },
    "meta": {
        "generated_at": "2024-01-15T11:00:00+00:00"
    }
}
```
---
### ⚡ Rate Limiting
API имеет ограничение на количество запросов:

* 60 запросов в минуту для публичных endpoints
* 120 запросов в минуту для авторизованных пользователей

Headers ответа:

```text
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
```
При превышении лимита:

```json
{
    "success": false,
    "error": "Too many requests",
    "retry_after": 30
}
```
---
### 🛠️ Примеры использования
#### cURL
```bash
# Получить список языков
curl -X GET https://your-site.com/admin/api/languages

# Установить локаль
curl -X POST https://your-site.com/admin/api/locale \
  -H "Content-Type: application/json" \
  -d '{"locale":"en"}'

# Получить переводы (авторизованный запрос)
curl -X GET https://your-site.com/admin/api/translations/ru \
  -H "Authorization: Bearer {token}"
```
#### JavaScript (Fetch)
```javascript
// Установка локали
fetch('/admin/api/locale', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ locale: 'en' })
})
.then(response => response.json())
.then(data => console.log(data));

// Получение списка языков
fetch('/admin/api/languages')
    .then(response => response.json())
    .then(data => {
        const languages = data.data.languages;
        // Отобразить языки в UI
    });
```
#### Vue.js / React
```javascript
// Vue composable
import { ref } from 'vue'

export function useLocale() {
    const currentLocale = ref('ru')
    
    async function setLocale(locale) {
        const response = await fetch('/admin/api/locale', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ locale })
        })
        const data = await response.json()
        if (data.success) {
            currentLocale.value = data.data.locale
            window.location.reload()
        }
    }
    
    return { currentLocale, setLocale }
}
```
---
### 📚 Коды ответов
| **Код** | **Описание** |
|---------|--------------|
|200|	Успешно|
|400|	Неверный запрос (невалидная локаль)|
|401|	Не авторизован|
|403|	Доступ запрещен (недостаточно прав)|
|422|	Ошибка валидации|
|429|	Слишком много запросов|
|500|	Внутренняя ошибка сервера|
---
### 🔐 Безопасность
* Все запросы защищены CSRF (кроме публичных GET)
* Rate limiting для защиты от DDoS
* Валидация всех входных данных
* Логирование всех подозрительных действий
---
### 📊 **Итоговый список API endpoints**

| Метод | Endpoint | Описание | Auth |
|-------|----------|----------|------|
| GET | `/languages` | Список языков | ❌ |
| GET | `/locale` | Текущая локаль | ❌ |
| POST | `/locale` | Установить локаль | ❌ |
| GET | `/locale/user` | Локаль пользователя | ✅ |
| PUT | `/locale/user` | Обновить локаль пользователя | ✅ |
| GET | `/translations/{locale}` | Все переводы | ❌ |
| GET | `/translations/{locale}/{group}` | Группа переводов | ❌ |
| POST | `/validate` | Валидация локали | ❌ |
| GET | `/stats` | Статистика | ✅ (admin) |

---


# <a name="производительность"></a>⚡ Производительность
## Кэширование
* Config cache - 3600 секунд (настраивается)
* Schema cache - проверка наличия поля locale
* Octane memory cache - для highload проектов

## Оптимизации
✅ Минимальное количество запросов к БД
✅ Ленивая загрузка ресурсов
✅ Оптимизированный JavaScript (3.5kb gzipped)
✅ CSS переменные для темной темы

## Метрики
| **Операция** | **Время (ms)** |
|--------------|----------------|
| Определение локали | < 1ms |
| Проверка схемы БД | 2-5ms (кешируется) |
| Смена языка | 50-100ms |
| Рендер меню | < 10ms |
# <a name="безопасность"></a>🛡️ Безопасность
## Защита
✅ CSRF - все POST запросы защищены
✅ XSS - санитизация всех входных данных
✅ SQL Injection - через Eloquent ORM
✅ Session Fixation - регенерация сессии

## Логирование
```php
// Пример лога безопасности
[SECURITY] Invalid locale cookie attempt
{
    "locale": "xx",
    "ip": "192.168.1.1",
    "user_agent": "Mozilla/5.0...",
    "user_id": null
}
```
## Рекомендации
* Используйте HTTPS в production
* Настройте правильные SameSite cookie
* Регулярно обновляйте пакет

# <a name="тестирование"></a>🧪 Тестирование

### Итоговое покрытие тестами

| Компонент                                      | Файл теста | Покрытие |
|------------------------------------------------|------------|----------|
| **Enum**                                       |	LocaleTest.php	| 100%     |
| **Middleware** | MultiLanguageMiddlewareTest.php | 95%      |
| **Controller** | MultiLanguageControllerTest.php | 92%      |
| **Widget** | LanguageMenuTest.php | 90%      |
| **Trait** | LogsWithContextTest.php | 85%      |
### Запуск тестов

#### Установка зависимостей для тестирования
```bash
composer require --dev orchestra/testbench:^8.0
```
#### Запуск всех тестов
```bash
composer test
```
#### С отчетом о покрытии
```bash
composer test-coverage
```
#### Только feature тесты
```bash
composer test-feature
```
#### Только unit тесты
```bash
composer test-unit
```

# <a name="обновление"></a>🔄 Обновление 
## С версии 1.x до 2.0
```bash
# Обновление через composer
composer update laravel-packages/multi-language

# Перепубликация ассетов
php artisan vendor:publish --tag=multi-language --force

# Очистка кэша
php artisan optimize:clear
```
## Что нового в 2.0
✅ Поддержка Laravel 12 и PHP 8.3

✅ Laravel Octane compatibility

✅ Type-safe Enum для локалей

✅ Профессиональное логирование

✅ Улучшенная производительность

✅ Исправление CSRF на странице логина

# <a name="участие-в-разработке"></a>🤝 Участие в разработке
## Процесс
* Форкните репозиторий
* Создайте feature ветку (git checkout -b feature/amazing-feature)
* Внесите изменения
* Добавьте тесты
* Запустите форматирование (composer format)
* Отправьте pull request

## Code Style
```bash
# Запуск Laravel Pint
composer format

# Проверка синтаксиса
composer lint
```
# <a name="лицензия"></a>📝 Лицензия
Распространяется под лицензией MIT. Смотрите файл LICENSE для получения дополнительной информации.

## 📞 Поддержка
* GitHub Issues: Создать issue
* Email: dedermus@gmail.com
* Документация: Wiki

## 🎉 Благодарности
Спасибо, что используете Laravel Admin Multi Language! Если вам нравится пакет, поставьте звезду на GitLab ⭐

### Laravel Admin Multi Language — профессиональное решение для мультиязычности вашей админ-панели. 🚀

<?php

namespace OpenAdminCore\Admin\MultiLanguage;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use OpenAdminCore\Admin\Facades\Admin;
use Illuminate\Support\ServiceProvider;
use OpenAdminCore\Admin\MultiLanguage\Enums\Locale;
use OpenAdminCore\Admin\MultiLanguage\Widgets\LanguageMenu;
use OpenAdminCore\Admin\MultiLanguage\Octane\OctaneHandler;

class MultiLanguageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param MultiLanguage $extension
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(MultiLanguage $extension): void
    {
        // ПРОВЕРКА enable флага - если false, не инициализировать
        if (!MultiLanguage::config('enable', true)) {
            return;
        }

        // Проверка `MultiLanguage::boot()`
        if (! MultiLanguage::boot()) {
            return;
        }

        // Загрузка представлений
        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'multi-language');
        }

        // Публикация ассетов
        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-packages/multi-language')],
                'multi-language'
            );

            // Публикация конфигурации
            $this->publishes([
                __DIR__.'/../config/multi-language.php' => config_path('multi-language.php'),
            ], 'multi-language-config');
        }

        // Публикация языковых файлов
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/multi-language'),
            ], 'multi-language-lang');
        }

        // Загрузка переводов из пакета
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'multi-language');

        // Регистрация маршрутов (включая API)
        $this->app->booted(function () {
            MultiLanguage::routes(__DIR__.'/../routes/web.php');

            // Регистрируем API маршруты отдельно
            if (file_exists(__DIR__.'/../routes/api.php')) {
                $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
            }
        });

        // Добавление Middleware
        $this->registerMiddleware(Middlewares\MultiLanguageMiddleware::class, 'web');

        // Добавление элемента в навбар
        if (MultiLanguage::config("show-navbar", true)) {
            Admin::navbar()->add(new LanguageMenu());
        }

        // Регистрация Octane обработчика если доступен
        $this->registerOctaneHandler();

        // Регистрация CSS и JS ассетов
        $this->registerAssets();

        // Расширяем стандартный шаблон логина из ядра
        $this->extendLoginTemplate();
    }

    protected function extendLoginTemplate()
    {
        // Используем метод composer, чтобы передать данные в секцию
        view()->composer('admin::login', function ($view) {
            // Передаем список языков в основной шаблон, если их там еще нет
            if (!$view->offsetExists('languages')) {
                $view->with('languages', Locale::cases());
            }
        });

        // Регистрируем секцию, которая будет вставлена в @yield('admin.login.language_selector')
        // Для этого нужно переопределить секцию. Это можно сделать через начало буферизации.
        $this->app['view']->creator('admin::login', function ($view) {
            $view->prepend('admin.login.language_selector', view('multi-language::partials.login-language-selector')->render());
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/multi-language.php', 'multi-language'
        );
    }

    /**
     * Регистрация Middleware
     *
     * @param string $middleware
     * @param string $group_name
     */
    protected function registerMiddleware(string $middleware, string $group_name = 'api'): void
    {
        $kernel = $this->app[Kernel::class];
        $kernel->appendMiddlewareToGroup($group_name, $middleware);
    }

    /**
     * Регистрация Octane обработчика
     */
    protected function registerOctaneHandler(): void
    {
        // Проверяем, установлен ли Laravel Octane
        if (!class_exists('Laravel\Octane\Octane')) {
            return;
        }

        // Регистрируем обработчик событий Octane
        $this->app['events']->listen(
            [
                'Laravel\Octane\Events\WorkerStarting',
                'Laravel\Octane\Events\RequestReceived',
                'Laravel\Octane\Events\RequestTerminated',
                'Laravel\Octane\Events\WorkerStopping',
                'Laravel\Octane\Events\WorkerErrorOccurred',
            ],
            OctaneHandler::class
        );

        // Логируем активацию Octane
        if ($this->app->runningInConsole()) {
            $this->app['log']->info('Multi-language: Octane support enabled');
        }
    }

    /**
     * Регистрация CSS и JS ассетов
     */
    protected function registerAssets(): void
    {
        // Подключаем CSS на всех страницах админки
        Admin::css('/vendor/laravel-packages/multi-language/css/multilanguage.css');

        // Подключаем JS на всех страницах админки
        Admin::js('/vendor/laravel-packages/multi-language/js/multilanguage.js');

        // Добавляем скрипт с конфигурацией в header
        Admin::script(
            'window.localeUrl = "' . admin_url('/locale') . '";'
        );
    }
}

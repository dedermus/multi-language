<?php

namespace OpenAdminCore\Admin\MultiLanguage;

use Illuminate\Contracts\Container\BindingResolutionException;
use OpenAdminCore\Admin\Facades\Admin;
use Illuminate\Support\ServiceProvider;
use OpenAdminCore\Admin\MultiLanguage\Widgets\LanguageMenu;
use Illuminate\Contracts\Http\Kernel as HttpKernel;

class MultiLanguageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * - Метод `boot` принимает параметр `$extension` типа `MultiLanguage`.
     *
     * @param MultiLanguage $extension
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(MultiLanguage $extension): void
    {
        // Проверка `MultiLanguage::boot()`
        // - Метод `boot` класса `MultiLanguage` вызывается для инициализации. Если он возвращает `false`, выполнение метода `boot` прекращается.
        if (! MultiLanguage::boot()) {
            return;
        }

        // Загрузка представлений
        // - Если метод `views()` возвращает путь к представлениям, они загружаются с помощью `loadViewsFrom`.
        if ($views = $extension->views()) {
            $this->loadViewsFrom($views, 'multi-language');
        }

        // Публикация ассетов
        // - Ассеты публикуются только в консольном режиме.
        if ($this->app->runningInConsole() && $assets = $extension->assets()) {
            $this->publishes(
                [$assets => public_path('vendor/laravel-packages/multi-language')],
                'multi-language'
            );
        }

        // Регистрация маршрутов
        // - Маршруты регистрируются после загрузки приложения с помощью `app->booted`.
        $this->app->booted(function () {
            MultiLanguage::routes(__DIR__.'/../routes/web.php');
        });

        // Добавление Middleware
        // - Middleware добавляется в стек и в группу `web`, что позволяет обрабатывать запросы с учетом мультиязычности.
        # $this->app->make(HttpKernel::class)->prependMiddleware(Middlewares\MultiLanguageMiddleware::class);
        $this->app['router']->pushMiddlewareToGroup('web', Middlewares\MultiLanguageMiddleware::class);

        // Добавление элемента в навбар
        // - Если в конфигурации указано `show-navbar`, в навбар добавляется элемент `LanguageMenu`.
        if (MultiLanguage::config("show-navbar", true)) {
            Admin::navbar()->add(new LanguageMenu());
        }
    }
}

<?php

namespace OpenAdminCore\Admin\MultiLanguage\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguageServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Загружаем миграции
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Публикуем конфигурацию
        $this->artisan('vendor:publish', [
            '--provider' => MultiLanguageServiceProvider::class,
            '--tag' => 'multi-language-config'
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            MultiLanguageServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Настройка базы данных для тестов
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Настройка мультиязычности
        $app['config']->set('multi-language.enable', true);
        $app['config']->set('multi-language.languages', [
            'ru' => 'Русский',
            'en' => 'English',
            'es' => 'Español',
            'de' => 'Deutsch',
        ]);
        $app['config']->set('multi-language.default', 'ru');
        $app['config']->set('multi-language.cookie-name', 'locale');
        $app['config']->set('multi-language.show-login-page', true);
        $app['config']->set('multi-language.show-navbar', true);

        // Настройка админки
        $app['config']->set('admin.auth.excepts', ['auth/login', 'auth/logout', 'locale']);
    }
}

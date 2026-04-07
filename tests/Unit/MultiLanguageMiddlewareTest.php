<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\Middlewares\MultiLanguageMiddleware;
use Tests\TestCase;

class MultiLanguageMiddlewareTest extends TestCase
{
    /** @test */
    public function it_sets_locale_based_on_cookie()
    {
        // Установим конфигурацию языков
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.cookie-name', 'locale');

        // Установим куки
        Cookie::queue('locale', 'fr');

        // Создадим запрос
        $request = Request::create('/');

        // Применим middleware
        $middleware = new MultiLanguageMiddleware();
        $middleware->handle($request, function ($req) {
            return response('next');
        });

        // Проверим, что локаль установлена
        $this->assertEquals('fr', App::getLocale());
    }
}

<?php

namespace OpenAdminCore\Admin\MultiLanguage\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\Middlewares\MultiLanguageMiddleware;
use OpenAdminCore\Admin\MultiLanguage\Tests\TestCase;
use OpenAdminCore\Admin\MultiLanguage\Octane\OctaneHandler;
use Mockery;

class MultiLanguageMiddlewareTest extends TestCase
{
    /** @test */
    public function it_sets_locale_from_valid_cookie()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.cookie-name', 'locale');

        Cookie::queue('locale', 'fr');

        $request = Request::create('/');
        $middleware = new MultiLanguageMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        });

        $this->assertEquals('fr', App::getLocale());
    }

    /** @test */
    public function it_uses_default_locale_when_cookie_invalid()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.default', 'en');
        Config::set('multi-language.cookie-name', 'locale');

        Cookie::queue('locale', 'invalid');

        $request = Request::create('/');
        $middleware = new MultiLanguageMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        });

        $this->assertEquals('en', App::getLocale());
    }

    /** @test */
    public function it_uses_browser_locale_when_no_cookie()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.default', 'en');
        Config::set('multi-language.cookie-name', 'locale');

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ]);

        $middleware = new MultiLanguageMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        });

        $this->assertEquals('fr', App::getLocale());
    }

    /** @test */
    public function it_bypasses_when_disabled()
    {
        Config::set('multi-language.enable', false);

        $request = Request::create('/');
        $middleware = new MultiLanguageMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        });

        $this->assertEquals('next', $response->content());
    }

    /** @test */
    public function it_handles_octane_mode()
    {
        Config::set('multi-language.languages', ['en' => 'English']);

        // Мокаем OctaneHandler
        OctaneHandler::shouldReceive('isActive')
            ->once()
            ->andReturn(true);

        OctaneHandler::shouldReceive('get')
            ->with('config')
            ->once()
            ->andReturn(['languages' => ['en' => 'English'], 'default' => 'en']);

        $request = Request::create('/');
        $middleware = new MultiLanguageMiddleware();

        $response = $middleware->handle($request, function ($req) {
            return response('next');
        });

        $this->assertEquals('en', App::getLocale());
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Tests\TestCase;

class MultiLanguageControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_sets_locale_and_returns_ok()
    {
        // Установим конфигурацию языков
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.cookie-name', 'locale');

        // Выполним POST запрос для установки локали
        $response = $this->post('/locale', ['locale' => 'fr']);

        // Проверим, что ответ 'ok' и куки установлены
        $response->assertStatus(200);
        $response->assertSee('ok');
        $this->assertEquals('fr', Cookie::get('locale'));
    }

    /** @test */
    public function it_returns_login_view_with_languages()
    {
        // Установим конфигурацию языков
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.cookie-name', 'locale');
        Config::set('multi-language.default', 'en');

        // Выполним GET запрос для получения страницы входа
        $response = $this->get('auth/login');

        // Проверим, что возвращается корректное представление
        $response->assertStatus(200);
        $response->assertViewIs('multi-language::login');
        $response->assertViewHas('languages');
        $response->assertViewHas('current', 'en');
    }
}

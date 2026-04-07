<?php

namespace OpenAdminCore\Admin\MultiLanguage\Tests\Feature;

use Illuminate\Support\Facades\Config;
use OpenAdminCore\Admin\MultiLanguage\Tests\TestCase;

class MultiLanguageControllerTest extends TestCase
{
    /** @test */
    public function it_sets_locale_and_returns_ok()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.cookie-name', 'locale');

        $response = $this->post('/admin/locale', ['locale' => 'fr']);

        $response->assertStatus(200);
        $response->assertSee('ok');

        // Проверяем cookie в ответе
        $response->assertCookie('locale', 'fr');
    }

    /** @test */
    public function it_returns_login_view_with_languages()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);
        Config::set('multi-language.default', 'en');

        $response = $this->get('/admin/auth/login');

        $response->assertStatus(200);
        $response->assertViewIs('multi-language::login');
        $response->assertViewHas('languages');
        $response->assertViewHas('current', 'en');
    }

    /** @test */
    public function it_preserves_form_data_on_locale_change()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);

        $response = $this->post('/admin/locale', [
            'locale' => 'fr',
            'username' => 'testuser',
            'password' => 'testpass',
            'remember' => '1'
        ]);

        $response->assertStatus(200);
        $response->assertCookie('locale', 'fr');

        // Проверяем, что данные сохранились в сессии
        $this->assertNotNull(session('form_data'));
    }

    /** @test */
    public function it_rejects_invalid_locale()
    {
        Config::set('multi-language.languages', ['en' => 'English', 'fr' => 'French']);

        $response = $this->post('/admin/locale', ['locale' => 'invalid']);
        $response->assertStatus(400);
        $response->assertJson(['error' => 'Invalid locale']);
    }

    /** @test */
    public function it_handles_validation_errors()
    {
        $response = $this->post('/admin/locale', ['locale' => '']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['error', 'errors']);
    }

    /** @test */
    public function it_returns_languages_via_api()
    {
        Config::set('multi-language.languages', ['ru' => 'Русский', 'en' => 'English']);

        $response = $this->get('/admin/api/languages');

        $response->assertStatus(200);
        $response->assertJson([
            'languages' => ['ru' => 'Русский', 'en' => 'English'],
            'current' => 'ru',
            'default' => 'ru'
        ]);
    }
}

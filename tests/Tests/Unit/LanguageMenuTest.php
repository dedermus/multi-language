<?php

namespace OpenAdminCore\Admin\MultiLanguage\Tests\Unit;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\Widgets\LanguageMenu;
use OpenAdminCore\Admin\MultiLanguage\Tests\TestCase;

class LanguageMenuTest extends TestCase
{
    /** @test */
    public function it_renders_menu_with_current_locale()
    {
        Config::set('multi-language.languages', [
            'ru' => 'Русский',
            'en' => 'English',
        ]);
        Config::set('multi-language.default', 'ru');
        Config::set('multi-language.cookie-name', 'locale');

        Cookie::queue('locale', 'en');

        $menu = new LanguageMenu();
        $html = $menu->render();

        $this->assertStringContainsString('🇬🇧', $html);
        $this->assertStringContainsString('EN', $html);
        $this->assertStringContainsString('data-id="ru"', $html);
        $this->assertStringContainsString('data-id="en"', $html);
    }

    /** @test */
    public function it_handles_missing_cookie()
    {
        Config::set('multi-language.languages', [
            'ru' => 'Русский',
            'en' => 'English',
        ]);
        Config::set('multi-language.default', 'ru');

        $menu = new LanguageMenu();
        $html = $menu->render();

        $this->assertStringContainsString('🇷🇺', $html);
        $this->assertStringContainsString('RU', $html);
    }

    /** @test */
    public function it_handles_non_enum_locales()
    {
        Config::set('multi-language.languages', [
            'ua' => 'Українська',
        ]);
        Config::set('multi-language.default', 'ua');

        $menu = new LanguageMenu();
        $html = $menu->render();

        $this->assertStringContainsString('Українська', $html);
        $this->assertStringContainsString('UA', $html);
    }
}

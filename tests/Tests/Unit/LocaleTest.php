<?php

namespace OpenAdminCore\Admin\MultiLanguage\Tests\Unit;

use OpenAdminCore\Admin\MultiLanguage\Enums\Locale;
use OpenAdminCore\Admin\MultiLanguage\Tests\TestCase;
use InvalidArgumentException;

class LocaleTest extends TestCase
{
    /** @test */
    public function it_returns_correct_labels()
    {
        $this->assertEquals('Русский', Locale::RU->label());
        $this->assertEquals('English', Locale::EN->label());
        $this->assertEquals('Español', Locale::ES->label());
        $this->assertEquals('Deutsch', Locale::DE->label());
    }

    /** @test */
    public function it_returns_correct_flags()
    {
        $this->assertEquals('🇷🇺', Locale::RU->flag());
        $this->assertEquals('🇬🇧', Locale::EN->flag());
        $this->assertEquals('🇪🇸', Locale::ES->flag());
        $this->assertEquals('🇩🇪', Locale::DE->flag());
    }

    /** @test */
    public function it_validates_locale_correctly()
    {
        $this->assertTrue(Locale::isValid('ru'));
        $this->assertTrue(Locale::isValid('en'));
        $this->assertFalse(Locale::isValid('xx'));
        $this->assertFalse(Locale::isValid(''));
    }

    /** @test */
    public function it_creates_from_string()
    {
        $locale = Locale::fromString('ru');
        $this->assertEquals(Locale::RU, $locale);

        $this->expectException(InvalidArgumentException::class);
        Locale::fromString('xx');
    }

    /** @test */
    public function it_uses_try_from_safely()
    {
        $this->assertEquals(Locale::RU, Locale::tryFrom('ru'));
        $this->assertNull(Locale::tryFrom('xx'));
    }

    /** @test */
    public function it_returns_date_formats()
    {
        $this->assertEquals('d.m.Y', Locale::RU->dateFormat());
        $this->assertEquals('m/d/Y', Locale::EN->dateFormat());
        $this->assertEquals('Y-m-d', Locale::ZH->dateFormat());
    }

    /** @test */
    public function it_returns_timezones()
    {
        $this->assertEquals('Europe/Moscow', Locale::RU->timezone());
        $this->assertEquals('Europe/London', Locale::EN->timezone());
        $this->assertEquals('Asia/Shanghai', Locale::ZH->timezone());
    }

    /** @test */
    public function it_converts_to_config_array()
    {
        $config = Locale::toConfigArray();
        $this->assertIsArray($config);
        $this->assertArrayHasKey('ru', $config);
        $this->assertEquals('Русский', $config['ru']);
    }
}

<?php

namespace OpenAdminCore\Admin\MultiLanguage;

use OpenAdminCore\Admin\Extension;

class MultiLanguage extends Extension
{
    public $name = 'multi-language';

    public $views = __DIR__.'/../resources/views';

    public $assets = __DIR__.'/../resources/assets';

    /**
     * @var string|null Версия пакета (кешируется)
     */
    private static ?string $version = null;

    /**
     * Получить версию пакета из composer.json
     */
    public static function getVersion(): string
    {
        if (self::$version !== null) {
            return self::$version;
        }

        try {
            $composerPath = __DIR__.'/../composer.json';

            if (file_exists($composerPath)) {
                $composer = json_decode(file_get_contents($composerPath), true);
                self::$version = $composer['version'] ?? '1.0.0';
            } else {
                self::$version = '1.0.0';
            }
        } catch (\Exception $e) {
            self::$version = '1.0.0';
        }

        return self::$version;
    }

    /**
     * Получить название пакета
     */
    public static function getName(): string
    {
        return (new self())->name;
    }

    /**
     * Получить информацию о пакете
     */
    public static function getInfo(): array
    {
        return [
            'name' => self::getName(),
            'version' => self::getVersion(),
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ];
    }
}

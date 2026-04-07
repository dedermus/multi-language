<?php

namespace OpenAdminCore\Admin\MultiLanguage\Enums;

use InvalidArgumentException;

enum Locale: string
{
    case RU = 'ru';
    case EN = 'en';
    case ES = 'es';
    case DE = 'de';
    case FR = 'fr';
    case IT = 'it';
    case ZH = 'zh';
    case JA = 'ja';

    /**
     * Получить название языка
     */
    public function label(): string
    {
        return match($this) {
            self::RU => 'Русский',
            self::EN => 'English',
            self::ES => 'Español',
            self::DE => 'Deutsch',
            self::FR => 'Français',
            self::IT => 'Italiano',
            self::ZH => '中文',
            self::JA => '日本語',
        };
    }

    /**
     * Получить флаг страны (emoji)
     */
    public function flag(): string
    {
        return match($this) {
            self::RU => '🇷🇺',
            self::EN => '🇬🇧',
            self::ES => '🇪🇸',
            self::DE => '🇩🇪',
            self::FR => '🇫🇷',
            self::IT => '🇮🇹',
            self::ZH => '🇨🇳',
            self::JA => '🇯🇵',
        };
    }

    /**
     * Получить направление текста
     */
    public function direction(): string
    {
        return match($this) {
            default => 'ltr', // Все поддерживаемые языки - ltr
        };
    }

    /**
     * Получить формат даты
     */
    public function dateFormat(): string
    {
        return match($this) {
            self::RU => 'd.m.Y',
            self::EN => 'm/d/Y',
            self::ES => 'd/m/Y',
            self::DE => 'd.m.Y',
            self::FR => 'd/m/Y',
            self::IT => 'd/m/Y',
            self::ZH => 'Y-m-d',
            self::JA => 'Y年m月d日',
        };
    }

    /**
     * Получить часовой пояс по умолчанию
     */
    public function timezone(): string
    {
        return match($this) {
            self::RU => 'Europe/Moscow',
            self::EN => 'Europe/London',
            self::ES => 'Europe/Madrid',
            self::DE => 'Europe/Berlin',
            self::FR => 'Europe/Paris',
            self::IT => 'Europe/Rome',
            self::ZH => 'Asia/Shanghai',
            self::JA => 'Asia/Tokyo',
        };
    }

    /**
     * Проверить, поддерживается ли локаль
     */
    public static function isValid(string $locale): bool
    {
        return in_array($locale, array_column(self::cases(), 'value'));
    }

    /**
     * Создать из строки с валидацией
     */
    public static function fromString(string $locale): self
    {
        $locale = strtolower(substr($locale, 0, 2));

        foreach (self::cases() as $case) {
            if ($case->value === $locale) {
                return $case;
            }
        }

        throw new InvalidArgumentException("Unsupported locale: {$locale}");
    }

    /**
     * Получить все локали для конфига
     */
    public static function toConfigArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }
}

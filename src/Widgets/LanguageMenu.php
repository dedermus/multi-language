<?php

namespace OpenAdminCore\Admin\MultiLanguage\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use OpenAdminCore\Admin\MultiLanguage\Enums\Locale;

class LanguageMenu implements Renderable
{
    public function render(): string
    {
        $config = MultiLanguage::config();

        $current = $config['default'] ?? 'en';
        $cookieName = $config['cookie-name'] ?? 'locale';

        // Получаем текущую локаль из cookie
        if (Cookie::has($cookieName)) {
            $cookieValue = Cookie::get($cookieName);
            if (array_key_exists($cookieValue, $config['languages'] ?? [])) {
                $current = $cookieValue;
            }
        }

        // Формируем массив языков
        $languages = [];
        foreach (($config['languages'] ?? []) as $code => $name) {
            $locale = Locale::tryFrom($code);
            $languages[$code] = [
                'name' => $locale?->label() ?? $name,
                'flag' => $locale?->flag(),
                'direction' => $locale?->direction() ?? 'ltr',
            ];
        }

        // Получаем объект Locale для текущего языка
        $currentLocale = Locale::tryFrom($current);

        return view("multi-language::language-menu", [
            'languages' => $languages,
            'current' => $current,
            'currentLocale' => $currentLocale
        ])->render();
    }
}

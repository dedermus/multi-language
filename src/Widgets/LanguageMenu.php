<?php

namespace OpenAdminCore\Admin\MultiLanguage\Widgets;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Cookie;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;

/**
 * Класс LanguageMenu
 *
 * Этот класс отвечает за рендеринг меню выбора языка
 * для функции мультиязычности OpenAdmin.
 */
class LanguageMenu implements Renderable
{
    /**
     * Рендеринг меню языков.
     *
     * Этот метод получает текущий язык из куки или использует
     * язык по умолчанию, если куки не установлены. Затем он рендерит
     * представление меню языков с доступными языками и текущим языком.
     *
     * @return string Рендеренный HTML меню языков.
     */
    public function render(): string
    {
        // Получение языка по умолчанию из конфигурации
        $current = MultiLanguage::config('default');

        // Получение имени куки из конфигурации
        $cookieName = MultiLanguage::config('cookie-name', 'locale');

        // Проверка, установлена ли куки языка, и обновление текущего языка
        if (Cookie::has($cookieName)) {
            $current = Cookie::get($cookieName);
        }

        // Получение доступных языков из конфигурации
        $languages = MultiLanguage::config("languages");

        // Рендеринг представления меню языков с доступными языками и текущим языком
        return view("multi-language::language-menu", compact('languages', 'current'))->render();
    }
}

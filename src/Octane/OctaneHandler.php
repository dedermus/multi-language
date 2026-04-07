<?php

namespace OpenAdminCore\Admin\MultiLanguage\Octane;

use Laravel\Octane\Contracts\OperationTerminated;
use Laravel\Octane\Events\RequestReceived;
use Laravel\Octane\Events\RequestTerminated;
use Laravel\Octane\Events\TaskReceived;
use Laravel\Octane\Events\TickReceived;
use Laravel\Octane\Events\WorkerErrorOccurred;
use Laravel\Octane\Events\WorkerStarting;
use Laravel\Octane\Events\WorkerStopping;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;
use OpenAdminCore\Admin\MultiLanguage\Middlewares\MultiLanguageMiddleware;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OctaneHandler
{
    /**
     * Флаг, что Octane активен
     */
    protected static bool $octaneActive = false;

    /**
     * Кэш в памяти для Octane
     */
    protected static array $memoryCache = [];

    /**
     * Обработчик событий Octane
     */
    public function __invoke($event): void
    {
        match(true) {
            $event instanceof WorkerStarting => $this->onWorkerStarting($event),
            $event instanceof RequestReceived => $this->onRequestReceived($event),
            $event instanceof RequestTerminated => $this->onRequestTerminated($event),
            $event instanceof WorkerStopping => $this->onWorkerStopping($event),
            $event instanceof WorkerErrorOccurred => $this->onWorkerError($event),
            default => null
        };
    }

    /**
     * Воркер запускается
     */
    protected function onWorkerStarting(WorkerStarting $event): void
    {
        self::$octaneActive = true;

        // Предзагружаем конфигурацию в память
        self::$memoryCache['config'] = MultiLanguage::config();

        Log::channel('multi-language')->info('Octane worker starting', [
            'worker_id' => $event->workerId ?? 'unknown'
        ]);
    }

    /**
     * Получен запрос
     */
    protected function onRequestReceived(RequestReceived $event): void
    {
        // Сбрасываем персистентные состояния для запроса
        $this->resetRequestState();
    }

    /**
     * Запрос завершен
     */
    protected function onRequestTerminated(RequestTerminated $event): void
    {
        // Очищаем временные данные
        $this->cleanupRequestData();
    }

    /**
     * Воркер останавливается
     */
    protected function onWorkerStopping(WorkerStopping $event): void
    {
        Log::channel('multi-language')->info('Octane worker stopping', [
            'worker_id' => $event->workerId ?? 'unknown'
        ]);

        // Сохраняем важные данные перед остановкой
        $this->persistMemoryCache();
    }

    /**
     * Ошибка воркера
     */
    protected function onWorkerError(WorkerErrorOccurred $event): void
    {
        Log::channel('multi-language')->error('Octane worker error', [
            'error' => $event->error->getMessage(),
            'worker_id' => $event->workerId ?? 'unknown'
        ]);
    }

    /**
     * Получить значение из memory cache
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$memoryCache[$key] ?? $default;
    }

    /**
     * Установить значение в memory cache
     */
    public static function set(string $key, mixed $value): void
    {
        self::$memoryCache[$key] = $value;
    }

    /**
     * Очистить memory cache
     */
    public static function clear(): void
    {
        self::$memoryCache = [];
    }

    /**
     * Проверить, активен ли Octane
     */
    public static function isActive(): bool
    {
        return self::$octaneActive;
    }

    /**
     * Сброс состояния запроса
     */
    protected function resetRequestState(): void
    {
        // Сбрасываем статические переменные, которые могут быть персистентными
        MultiLanguageMiddleware::resetSchemaCache();
    }

    /**
     * Очистка данных запроса
     */
    protected function cleanupRequestData(): void
    {
        // Очищаем временные данные запроса
    }

    /**
     * Сохранить memory cache в постоянное хранилище
     */
    protected function persistMemoryCache(): void
    {
        try {
            Cache::put('multi-language.octane-cache', self::$memoryCache, 3600);
        } catch (\Exception $e) {
            Log::warning('Failed to persist Octane cache', [
                'error' => $e->getMessage()
            ]);
        }
    }
}

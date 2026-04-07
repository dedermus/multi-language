<?php

namespace OpenAdminCore\Admin\MultiLanguage\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenAdminCore\Admin\MultiLanguage\MultiLanguage;

trait LogsWithContext
{
    /**
     * @var string Идентификатор запроса для трейсинга
     */
    protected string $requestId;

    /**
     * Инициализация контекста логирования
     */
    protected function initLogContext(): void
    {
        $this->requestId = request()->header('X-Request-ID')
            ?? Str::uuid()->toString();

        // Добавляем глобальный контекст для всех логов
        Log::withContext([
            'component' => 'multi-language',
            'version' => MultiLanguage::getVersion(), // Используем метод вместо константы
            'environment' => app()->environment(),
            'request_id' => $this->requestId,
            'session_id' => session()->getId() ?? 'cli',
            'ip' => request()->ip() ?? 'cli',
            'url' => request()->fullUrl() ?? 'cli'
        ]);
    }

    /**
     * Логирование с контекстом
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        Log::channel($this->getLogChannel())
            ->withContext($this->getBaseContext())
            ->log($level, $message, array_merge($context, [
                'memory' => memory_get_usage(true),
                'time' => microtime(true) - LARAVEL_START
            ]));
    }

    /**
     * Логирование безопасности
     */
    protected function logSecurity(string $message, array $context = []): void
    {
        $this->log('warning', "[SECURITY] {$message}", array_merge($context, [
            'user_id' => auth()->id() ?? 'guest',
            'user_role' => auth()->user()?->roles?->first()?->name ?? 'none',
            'timestamp' => now()->toIso8601String()
        ]));
    }

    /**
     * Логирование производительности
     */
    protected function logPerformance(string $operation, float $duration, array $context = []): void
    {
        $threshold = config('multi-language.slow_threshold_ms', 100) / 1000; // convert to seconds

        $this->log('debug', "[PERF] {$operation} took " . round($duration * 1000, 2) . "ms", array_merge($context, [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'slow_threshold_ms' => $threshold * 1000
        ]));

        // Алерт на медленные операции
        if ($duration > $threshold) {
            $this->log('warning', "[SLOW] {$operation} exceeded threshold", [
                'duration_ms' => round($duration * 1000, 2),
                'threshold_ms' => $threshold * 1000
            ]);
        }
    }

    /**
     * Получить канал логирования
     */
    protected function getLogChannel(): string
    {
        return config('multi-language.log_channel', config('logging.default'));
    }

    /**
     * Базовый контекст для всех логов
     */
    private function getBaseContext(): array
    {
        return [
            'request_id' => $this->requestId ?? 'cli',
            'memory_peak' => memory_get_peak_usage(true)
        ];
    }
}

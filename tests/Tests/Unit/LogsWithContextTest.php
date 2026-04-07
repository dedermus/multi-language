<?php

namespace OpenAdminCore\Admin\MultiLanguage\Tests\Unit;

use Illuminate\Support\Facades\Log;
use OpenAdminCore\Admin\MultiLanguage\Traits\LogsWithContext;
use OpenAdminCore\Admin\MultiLanguage\Tests\TestCase;

class LogsWithContextTest extends TestCase
{
    use LogsWithContext;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initLogContext();
    }

    /** @test */
    public function it_logs_with_context()
    {
        Log::shouldReceive('channel')
            ->with('stack')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('withContext')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('log')
            ->with('info', 'Test message', \Mockery::type('array'))
            ->once();

        $this->log('info', 'Test message');
    }

    /** @test */
    public function it_logs_security_events()
    {
        Log::shouldReceive('channel')
            ->with('stack')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('withContext')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('log')
            ->with('warning', '[SECURITY] Test security', \Mockery::type('array'))
            ->once();

        $this->logSecurity('Test security');
    }

    /** @test */
    public function it_logs_performance_metrics()
    {
        Log::shouldReceive('channel')
            ->with('stack')
            ->times(2)
            ->andReturnSelf();

        Log::shouldReceive('withContext')
            ->times(2)
            ->andReturnSelf();

        Log::shouldReceive('log')
            ->with('debug', '[PERF] test took 50ms', \Mockery::type('array'))
            ->once();

        $this->logPerformance('test', 0.05);
    }
}

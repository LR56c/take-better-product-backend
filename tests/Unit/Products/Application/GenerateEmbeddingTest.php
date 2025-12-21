<?php

namespace Tests\Unit\Products\Application;

use Illuminate\Support\Facades\Log;
use Mockery;
use Src\Products\Application\GenerateEmbedding;
use Src\Products\Domain\ProductAiRepository;
use Tests\TestCase;

class GenerateEmbeddingTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_generates_embedding_successfully()
    {
        // Arrange
        $aiRepository = Mockery::mock(ProductAiRepository::class);
        $this->app->instance(ProductAiRepository::class, $aiRepository);

        $useCase = $this->app->make(GenerateEmbedding::class);

        $text = 'Product Title Description';
        $expectedVector = [0.1, 0.2, 0.3];

        $aiRepository->shouldReceive('generateEmbedding')
            ->once()
            ->with($text)
            ->andReturn($expectedVector);

        Log::shouldReceive('info')->twice(); // One for start, one for success

        // Act
        $vector = $useCase->execute($text);

        // Assert
        $this->assertEquals($expectedVector, $vector);
    }

    public function test_it_logs_warning_on_failure()
    {
        // Arrange
        $aiRepository = Mockery::mock(ProductAiRepository::class);
        $this->app->instance(ProductAiRepository::class, $aiRepository);

        $useCase = $this->app->make(GenerateEmbedding::class);

        $text = 'Product Title Description';

        $aiRepository->shouldReceive('generateEmbedding')
            ->once()
            ->with($text)
            ->andReturn(null);

        Log::shouldReceive('info')->once(); // Start log
        Log::shouldReceive('warning')->once(); // Failure log

        // Act
        $vector = $useCase->execute($text);

        // Assert
        $this->assertNull($vector);
    }
}

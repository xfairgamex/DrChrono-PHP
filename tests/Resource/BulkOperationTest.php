<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Client\Config;
use DrChrono\Client\HttpClient;
use DrChrono\Exception\ApiException;
use DrChrono\Exception\BulkTimeoutException;
use DrChrono\Resource\BulkOperation;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BulkOperationTest extends TestCase
{
    private HttpClient|MockObject $httpClient;
    private Config|MockObject $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('getBulkPollInterval')->willReturn(0); // no sleep in tests
        $this->config->method('getBulkPollMaxAttempts')->willReturn(3);

        $this->httpClient = $this->createMock(HttpClient::class);
        $this->httpClient->method('getConfig')->willReturn($this->config);
    }

    public function testSubmitStoresUuid(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/appointments_list_create', ['page_size' => 1000, 'page' => 1])
            ->willReturn([
                'status' => 'In progress',
                'uuid' => 'test-uuid-123',
            ]);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        $operation->submit(['page_size' => 1000, 'page' => 1]);

        $this->assertEquals('test-uuid-123', $operation->getUuid());
        $this->assertEquals('In progress', $operation->getStatus());
        $this->assertFalse($operation->isComplete());
    }

    public function testSubmitThrowsWhenNoUuidReturned(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'Error']);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Bulk operation POST did not return a UUID');
        $operation->submit();
    }

    public function testPollReturnsResultsWhenComplete(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointments_list_list', ['uuid' => 'test-uuid-123'])
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'test-uuid-123',
                'pagination' => [
                    'count' => 50,
                    'pages' => 1,
                    'page_size' => 1000,
                    'page' => 1,
                ],
                'results' => [
                    ['id' => 1, 'patient' => 'John'],
                    ['id' => 2, 'patient' => 'Jane'],
                ],
            ]);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        // Set UUID directly via submit mock
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'In progress', 'uuid' => 'test-uuid-123']);
        $operation->submit();

        $operation->poll(0, 5);

        $this->assertTrue($operation->isComplete());
        $this->assertCount(2, $operation->getResults());
        $this->assertEquals(50, $operation->getTotalCount());
        $this->assertEquals(1, $operation->getTotalPages());
        $this->assertEquals(1, $operation->getCurrentPage());
        $this->assertEquals(1000, $operation->getPageSize());
    }

    public function testPollHandlesInProgressThenComplete(): void
    {
        $matcher = $this->exactly(2);
        $this->httpClient
            ->expects($matcher)
            ->method('get')
            ->with('/api/patients_list_list', ['uuid' => 'uuid-456'])
            ->willReturnCallback(function () use ($matcher) {
                if ($matcher->numberOfInvocations() === 1) {
                    return ['status' => 'In progress', 'uuid' => 'uuid-456'];
                }
                return [
                    'status' => 'Complete',
                    'uuid' => 'uuid-456',
                    'pagination' => ['count' => 10, 'pages' => 1, 'page_size' => 1000, 'page' => 1],
                    'results' => [['id' => 1]],
                ];
            });

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/patients_list_create',
            '/api/patients_list_list'
        );

        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'In progress', 'uuid' => 'uuid-456']);
        $operation->submit();

        $operation->poll(0, 5);

        $this->assertTrue($operation->isComplete());
        $this->assertCount(1, $operation->getResults());
    }

    public function testPollThrowsOnTimeout(): void
    {
        $this->httpClient
            ->method('get')
            ->willReturn(['status' => 'In progress', 'uuid' => 'uuid-timeout']);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'In progress', 'uuid' => 'uuid-timeout']);
        $operation->submit();

        $this->expectException(BulkTimeoutException::class);
        $this->expectExceptionMessage('Bulk operation timed out after 2 polling attempts');
        $operation->poll(0, 2);
    }

    public function testPollThrowsOnExpiredUuid(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn(['detail' => 'Invalid uuid specified']);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'In progress', 'uuid' => 'expired-uuid']);
        $operation->submit();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Bulk operation UUID has expired');
        $operation->poll(0, 5);
    }

    public function testPollThrowsWithoutSubmit(): void
    {
        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('No UUID available. Call submit() first.');
        $operation->poll();
    }

    public function testSubmitAndPollUsesConfigDefaults(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/transactions_list_create', ['since' => '2024-01-01'])
            ->willReturn(['status' => 'In progress', 'uuid' => 'uuid-config']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/transactions_list_list', ['uuid' => 'uuid-config'])
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'uuid-config',
                'pagination' => ['count' => 5, 'pages' => 1, 'page_size' => 1000, 'page' => 1],
                'results' => [['id' => 1]],
            ]);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/transactions_list_create',
            '/api/transactions_list_list'
        );

        $result = $operation->submitAndPoll(['since' => '2024-01-01']);

        $this->assertTrue($result->isComplete());
        $this->assertEquals(5, $result->getTotalCount());
    }

    public function testBulkTimeoutExceptionPreservesUuid(): void
    {
        $exception = new BulkTimeoutException('my-uuid', '/api/test_list_list', 10);

        $this->assertEquals('my-uuid', $exception->getUuid());
        $this->assertEquals('/api/test_list_list', $exception->getBulkListPath());
        $this->assertStringContainsString('my-uuid', $exception->getMessage());
        $this->assertStringContainsString('10', $exception->getMessage());
    }

    public function testPaginationAccessors(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'In progress', 'uuid' => 'uuid-pag']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'uuid-pag',
                'pagination' => ['count' => 4428, 'pages' => 5, 'page_size' => 1000, 'page' => 1],
                'results' => array_fill(0, 1000, ['id' => 1]),
            ]);

        $operation = new BulkOperation(
            $this->httpClient,
            '/api/appointments_list_create',
            '/api/appointments_list_list'
        );

        $operation->submitAndPoll();

        $this->assertEquals(4428, $operation->getTotalCount());
        $this->assertEquals(5, $operation->getTotalPages());
        $this->assertEquals(1000, $operation->getPageSize());
        $this->assertEquals(1, $operation->getCurrentPage());
        $this->assertEquals([
            'count' => 4428,
            'pages' => 5,
            'page_size' => 1000,
            'page' => 1,
        ], $operation->getPagination());
    }
}

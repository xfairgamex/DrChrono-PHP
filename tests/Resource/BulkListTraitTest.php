<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Client\Config;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\AppointmentsResource;
use DrChrono\Resource\BulkOperation;
use DrChrono\Resource\ClinicalNoteFieldValuesResource;
use DrChrono\Resource\PatientsResource;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class BulkListTraitTest extends TestCase
{
    private HttpClient|MockObject $httpClient;
    private Config|MockObject $config;

    protected function setUp(): void
    {
        $this->config = $this->createMock(Config::class);
        $this->config->method('getBulkPollInterval')->willReturn(0);
        $this->config->method('getBulkPollMaxAttempts')->willReturn(3);
        $this->config->method('useLaravelCollections')->willReturn(false);

        $this->httpClient = $this->createMock(HttpClient::class);
        $this->httpClient->method('getConfig')->willReturn($this->config);
    }

    public function testBulkListOnAppointments(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/appointments_list_create', [
                'since' => '2024-01-01',
                'verbose' => true,
                'page_size' => 1000,
                'page' => 1,
            ])
            ->willReturn(['status' => 'In progress', 'uuid' => 'appt-uuid']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointments_list_list', ['uuid' => 'appt-uuid'])
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'appt-uuid',
                'pagination' => ['count' => 2, 'pages' => 1, 'page_size' => 1000, 'page' => 1],
                'results' => [['id' => 1], ['id' => 2]],
            ]);

        $resource = new AppointmentsResource($this->httpClient);
        $operation = $resource->bulkList(['since' => '2024-01-01', 'verbose' => true]);

        $this->assertInstanceOf(BulkOperation::class, $operation);
        $this->assertTrue($operation->isComplete());
        $this->assertCount(2, $operation->getResults());
    }

    public function testBulkListOnPatients(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/patients_list_create', $this->anything())
            ->willReturn(['status' => 'In progress', 'uuid' => 'pat-uuid']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patients_list_list', ['uuid' => 'pat-uuid'])
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'pat-uuid',
                'pagination' => ['count' => 1, 'pages' => 1, 'page_size' => 1000, 'page' => 1],
                'results' => [['id' => 100]],
            ]);

        $resource = new PatientsResource($this->httpClient);
        $operation = $resource->bulkList(['since' => '2024-06-01']);

        $this->assertCount(1, $operation->getResults());
        $this->assertEquals(100, $operation->getResults()[0]['id']);
    }

    public function testBulkListOnClinicalNoteFieldValues(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/clinical_note_field_values_list_create', $this->anything())
            ->willReturn(['status' => 'In progress', 'uuid' => 'cnfv-uuid']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values_list_list', ['uuid' => 'cnfv-uuid'])
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'cnfv-uuid',
                'pagination' => ['count' => 3, 'pages' => 1, 'page_size' => 1000, 'page' => 1],
                'results' => [['id' => 1], ['id' => 2], ['id' => 3]],
            ]);

        $resource = new ClinicalNoteFieldValuesResource($this->httpClient);
        $operation = $resource->bulkList();

        $this->assertCount(3, $operation->getResults());
    }

    public function testBulkListCustomPageSize(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/appointments_list_create', [
                'page_size' => 500,
                'page' => 1,
            ])
            ->willReturn(['status' => 'In progress', 'uuid' => 'custom-uuid']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'custom-uuid',
                'pagination' => ['count' => 10, 'pages' => 1, 'page_size' => 500, 'page' => 1],
                'results' => [['id' => 1]],
            ]);

        $resource = new AppointmentsResource($this->httpClient);
        $operation = $resource->bulkList([], 500);

        $this->assertEquals(500, $operation->getPageSize());
    }

    public function testBulkIterateAllAcrossMultiplePages(): void
    {
        // Page 1
        $postMatcher = $this->exactly(2);
        $this->httpClient
            ->expects($postMatcher)
            ->method('postWithQuery')
            ->willReturnCallback(function (string $path, array $query) use ($postMatcher) {
                $page = $query['page'];
                return ['status' => 'In progress', 'uuid' => "uuid-page-{$page}"];
            });

        $getMatcher = $this->exactly(2);
        $this->httpClient
            ->expects($getMatcher)
            ->method('get')
            ->willReturnCallback(function (string $path, array $query) use ($getMatcher) {
                if ($getMatcher->numberOfInvocations() === 1) {
                    return [
                        'status' => 'Complete',
                        'uuid' => 'uuid-page-1',
                        'pagination' => ['count' => 3, 'pages' => 2, 'page_size' => 2, 'page' => 1],
                        'results' => [['id' => 1], ['id' => 2]],
                    ];
                }
                return [
                    'status' => 'Complete',
                    'uuid' => 'uuid-page-2',
                    'pagination' => ['count' => 3, 'pages' => 2, 'page_size' => 2, 'page' => 2],
                    'results' => [['id' => 3]],
                ];
            });

        $resource = new AppointmentsResource($this->httpClient);
        $allItems = [];

        foreach ($resource->bulkIterateAll([], 2) as $item) {
            $allItems[] = $item;
        }

        $this->assertCount(3, $allItems);
        $this->assertEquals(1, $allItems[0]['id']);
        $this->assertEquals(2, $allItems[1]['id']);
        $this->assertEquals(3, $allItems[2]['id']);
    }

    public function testBulkAllReturnsArray(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->willReturn(['status' => 'In progress', 'uuid' => 'all-uuid']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'all-uuid',
                'pagination' => ['count' => 2, 'pages' => 1, 'page_size' => 1000, 'page' => 1],
                'results' => [['id' => 10], ['id' => 20]],
            ]);

        $resource = new PatientsResource($this->httpClient);
        $all = $resource->bulkAll();

        $this->assertIsArray($all);
        $this->assertCount(2, $all);
        $this->assertEquals(10, $all[0]['id']);
    }

    public function testBulkListSpecificPage(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('postWithQuery')
            ->with('/api/appointments_list_create', [
                'page_size' => 1000,
                'page' => 3,
            ])
            ->willReturn(['status' => 'In progress', 'uuid' => 'page3-uuid']);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->willReturn([
                'status' => 'Complete',
                'uuid' => 'page3-uuid',
                'pagination' => ['count' => 2500, 'pages' => 3, 'page_size' => 1000, 'page' => 3],
                'results' => [['id' => 2001]],
            ]);

        $resource = new AppointmentsResource($this->httpClient);
        $operation = $resource->bulkList([], 1000, 3);

        $this->assertEquals(3, $operation->getCurrentPage());
        $this->assertEquals(2500, $operation->getTotalCount());
    }
}

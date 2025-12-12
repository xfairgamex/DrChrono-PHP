<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\AmendmentsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AmendmentsResourceTest extends TestCase
{
    private AmendmentsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new AmendmentsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'description' => 'Correct medication dosage'],
                ['id' => 2, 'patient' => 457, 'description' => 'Update diagnosis'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/amendments', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testListByPatient(): void
    {
        $patientId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'description' => 'Correct medication'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/amendments', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'description' => 'Correct medication'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/amendments', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $amendmentId = 1;
        $expectedResponse = ['id' => 1, 'description' => 'Correct medication', 'status' => 'pending'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/amendments/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($amendmentId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateAmendment(): void
    {
        $data = [
            'patient' => 456,
            'description' => 'Correct medication dosage',
            'reason' => 'Data entry error',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/amendments', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createAmendment($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateAmendment(): void
    {
        $amendmentId = 1;
        $data = ['status' => 'approved'];
        $expectedResponse = ['id' => 1, 'status' => 'approved'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/amendments/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateAmendment($amendmentId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteAmendment(): void
    {
        $amendmentId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/amendments/1')
            ->willReturn([]);

        $this->resource->deleteAmendment($amendmentId);
    }

    public function testApprove(): void
    {
        $amendmentId = 1;
        $approverNotes = 'Reviewed and approved';
        $expectedResponse = ['id' => 1, 'status' => 'approved', 'approver_notes' => $approverNotes];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/amendments/1', [
                'status' => 'approved',
                'approver_notes' => $approverNotes,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->approve($amendmentId, $approverNotes);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testApproveWithoutNotes(): void
    {
        $amendmentId = 1;
        $expectedResponse = ['id' => 1, 'status' => 'approved'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/amendments/1', ['status' => 'approved'])
            ->willReturn($expectedResponse);

        $result = $this->resource->approve($amendmentId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeny(): void
    {
        $amendmentId = 1;
        $denialReason = 'Insufficient information';
        $expectedResponse = [
            'id' => 1,
            'status' => 'denied',
            'denial_reason' => $denialReason,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/amendments/1', [
                'status' => 'denied',
                'denial_reason' => $denialReason,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->deny($amendmentId, $denialReason);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetPending(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'status' => 'pending'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/amendments', ['status' => 'pending'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getPending();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetHistoryForNote(): void
    {
        $clinicalNoteId = 100;
        $mockData = [
            ['id' => 1, 'clinical_note' => 100, 'description' => 'First amendment'],
            ['id' => 2, 'clinical_note' => 100, 'description' => 'Second amendment'],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/amendments', ['clinical_note' => $clinicalNoteId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getHistoryForNote($clinicalNoteId);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(2, $result[1]['id']);
    }
}

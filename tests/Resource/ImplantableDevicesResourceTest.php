<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\ImplantableDevicesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ImplantableDevicesResourceTest extends TestCase
{
    private ImplantableDevicesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new ImplantableDevicesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'device_type' => 'Pacemaker', 'status' => 'active'],
                ['id' => 2, 'patient' => 457, 'device_type' => 'Hip Prosthesis', 'status' => 'active'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', [])
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
                ['id' => 1, 'patient' => 456, 'device_type' => 'Pacemaker'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'device_type' => 'Pacemaker'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $deviceId = 1;
        $expectedResponse = ['id' => 1, 'device_type' => 'Pacemaker', 'status' => 'active'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($deviceId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateDevice(): void
    {
        $data = [
            'patient' => 456,
            'device_type' => 'Pacemaker',
            'implant_date' => '2025-11-23',
            'device_identifier' => '(01)12345678901234',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/implantable_devices', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createDevice($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateDevice(): void
    {
        $deviceId = 1;
        $data = ['notes' => 'Device functioning properly'];
        $expectedResponse = ['id' => 1, 'notes' => 'Device functioning properly'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/implantable_devices/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateDevice($deviceId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteDevice(): void
    {
        $deviceId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/implantable_devices/1')
            ->willReturn([]);

        $this->resource->deleteDevice($deviceId);
    }

    public function testGetActiveForPatient(): void
    {
        $patientId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'status' => 'active'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', ['patient' => $patientId, 'status' => 'active'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getActiveForPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testMarkRemoved(): void
    {
        $deviceId = 1;
        $removalDate = '2025-11-25';
        $removalReason = 'Device malfunction';
        $expectedResponse = [
            'id' => 1,
            'status' => 'removed',
            'removal_date' => $removalDate,
            'removal_reason' => $removalReason,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/implantable_devices/1', [
                'status' => 'removed',
                'removal_date' => $removalDate,
                'removal_reason' => $removalReason,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->markRemoved($deviceId, $removalDate, $removalReason);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testMarkRemovedWithoutReason(): void
    {
        $deviceId = 1;
        $removalDate = '2025-11-25';
        $expectedResponse = [
            'id' => 1,
            'status' => 'removed',
            'removal_date' => $removalDate,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/implantable_devices/1', [
                'status' => 'removed',
                'removal_date' => $removalDate,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->markRemoved($deviceId, $removalDate);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetByType(): void
    {
        $deviceType = 'Pacemaker';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'device_type' => 'Pacemaker'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', ['device_type' => $deviceType])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByType($deviceType);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetByManufacturer(): void
    {
        $manufacturer = 'Medtronic';
        $mockData = [
            ['id' => 1, 'manufacturer' => 'Medtronic', 'device_type' => 'Pacemaker'],
            ['id' => 2, 'manufacturer' => 'Boston Scientific', 'device_type' => 'Stent'],
            ['id' => 3, 'manufacturer' => 'Medtronic', 'device_type' => 'Defibrillator'],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByManufacturer($manufacturer);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testFindByUdi(): void
    {
        $udi = '(01)12345678901234';
        $mockData = [
            ['id' => 1, 'device_identifier' => '(01)00000000000000'],
            ['id' => 2, 'device_identifier' => '(01)12345678901234'],
            ['id' => 3, 'device_identifier' => '(01)99999999999999'],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->findByUdi($udi);

        $this->assertNotNull($result);
        $this->assertEquals(2, $result['id']);
        $this->assertEquals($udi, $result['device_identifier']);
    }

    public function testFindByUdiNotFound(): void
    {
        $udi = '(01)12345678901234';
        $mockData = [
            ['id' => 1, 'device_identifier' => '(01)00000000000000'],
            ['id' => 3, 'device_identifier' => '(01)99999999999999'],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/implantable_devices', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->findByUdi($udi);

        $this->assertNull($result);
    }
}

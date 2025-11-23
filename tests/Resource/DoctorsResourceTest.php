<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\DoctorsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DoctorsResourceTest extends TestCase
{
    private DoctorsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new DoctorsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                [
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'specialty' => 'Cardiology',
                    'is_account_suspended' => false,
                ],
                [
                    'id' => 2,
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'specialty' => 'Pediatrics',
                    'is_account_suspended' => false,
                ],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $expectedResponse = [
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Smith',
            'specialty' => 'Cardiology',
            'npi_number' => '1234567890',
            'email' => 'jsmith@example.com',
            'is_account_suspended' => false,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get(1);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('John', $result['first_name']);
    }

    public function testListActive(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Smith', 'is_account_suspended' => false],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Doe', 'is_account_suspended' => true],
                ['id' => 3, 'first_name' => 'Bob', 'last_name' => 'Jones', 'is_account_suspended' => false],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->listActive();

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testListSuspended(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Smith', 'is_account_suspended' => false],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Doe', 'is_account_suspended' => true],
                ['id' => 3, 'first_name' => 'Bob', 'last_name' => 'Jones', 'is_account_suspended' => false],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->listSuspended();

        $this->assertCount(1, $result);
        $this->assertEquals(2, $result[0]['id']);
        $this->assertTrue($result[0]['is_account_suspended']);
    }

    public function testListBySpecialty(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Smith', 'specialty' => 'Cardiology'],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Doe', 'specialty' => 'Pediatrics'],
                ['id' => 3, 'first_name' => 'Bob', 'last_name' => 'Jones', 'specialty' => 'Cardiology'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->listBySpecialty('Cardiology');

        $this->assertCount(2, $result);
        $this->assertEquals('Cardiology', $result[0]['specialty']);
        $this->assertEquals('Cardiology', $result[1]['specialty']);
    }

    public function testSearch(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Smith'],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Doe'],
                ['id' => 3, 'first_name' => 'Johnny', 'last_name' => 'Walker'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->search('john');

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testSearchCaseInsensitive(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Smith'],
                ['id' => 2, 'first_name' => 'jane', 'last_name' => 'doe'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->search('JOHN');

        $this->assertCount(1, $result);
        $this->assertEquals('John', $result[0]['first_name']);
    }

    public function testListByPracticeGroup(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'first_name' => 'John', 'last_name' => 'Smith', 'practice_group' => 100],
                ['id' => 2, 'first_name' => 'Jane', 'last_name' => 'Doe', 'practice_group' => 200],
                ['id' => 3, 'first_name' => 'Bob', 'last_name' => 'Jones', 'practice_group' => 100],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/doctors', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPracticeGroup(100);

        $this->assertCount(2, $result);
        $this->assertEquals(100, $result[0]['practice_group']);
        $this->assertEquals(100, $result[1]['practice_group']);
    }

    public function testGetFullName(): void
    {
        $doctor = [
            'first_name' => 'John',
            'last_name' => 'Smith',
            'suffix' => 'MD',
        ];

        $fullName = $this->resource->getFullName($doctor);

        $this->assertEquals('John Smith, MD', $fullName);
    }

    public function testGetFullNameWithoutSuffix(): void
    {
        $doctor = [
            'first_name' => 'John',
            'last_name' => 'Smith',
        ];

        $fullName = $this->resource->getFullName($doctor);

        $this->assertEquals('John Smith', $fullName);
    }

    public function testIsActiveTrue(): void
    {
        $doctor = ['is_account_suspended' => false];

        $this->assertTrue($this->resource->isActive($doctor));
    }

    public function testIsActiveFalse(): void
    {
        $doctor = ['is_account_suspended' => true];

        $this->assertFalse($this->resource->isActive($doctor));
    }

    public function testIsActiveDefault(): void
    {
        $doctor = [];

        $this->assertTrue($this->resource->isActive($doctor));
    }
}

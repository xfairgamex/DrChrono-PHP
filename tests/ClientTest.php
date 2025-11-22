<?php

declare(strict_types=1);

namespace DrChrono\Tests;

use DrChrono\DrChronoClient;
use DrChrono\Client\Config;
use DrChrono\Resource\PatientsResource;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testWithAccessToken(): void
    {
        $client = DrChronoClient::withAccessToken('test_token');

        $this->assertInstanceOf(DrChronoClient::class, $client);
        $this->assertEquals('test_token', $client->getConfig()->getAccessToken());
    }

    public function testWithOAuth(): void
    {
        $client = DrChronoClient::withOAuth(
            'client_id',
            'client_secret',
            'http://localhost/callback'
        );

        $this->assertInstanceOf(DrChronoClient::class, $client);
        $this->assertEquals('client_id', $client->getConfig()->getClientId());
        $this->assertEquals('client_secret', $client->getConfig()->getClientSecret());
        $this->assertEquals('http://localhost/callback', $client->getConfig()->getRedirectUri());
    }

    public function testResourceAccess(): void
    {
        $client = DrChronoClient::withAccessToken('test_token');

        $this->assertInstanceOf(PatientsResource::class, $client->patients);
    }

    public function testConfigurationOptions(): void
    {
        $client = new DrChronoClient([
            'access_token' => 'test_token',
            'timeout' => 60,
            'max_retries' => 5,
            'debug' => true,
        ]);

        $config = $client->getConfig();

        $this->assertEquals('test_token', $config->getAccessToken());
        $this->assertEquals(60, $config->getTimeout());
        $this->assertEquals(5, $config->getMaxRetries());
        $this->assertTrue($config->isDebug());
    }

    public function testConfigObject(): void
    {
        $config = new Config([
            'access_token' => 'test_token',
            'client_id' => 'test_client_id',
        ]);

        $client = new DrChronoClient($config);

        $this->assertEquals('test_token', $client->getConfig()->getAccessToken());
        $this->assertEquals('test_client_id', $client->getConfig()->getClientId());
    }
}

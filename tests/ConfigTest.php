<?php

declare(strict_types=1);

namespace DrChrono\Tests;

use DrChrono\Client\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $config = new Config();

        $this->assertEquals('https://app.drchrono.com', $config->getBaseUri());
        $this->assertEquals(30, $config->getTimeout());
        $this->assertEquals(10, $config->getConnectTimeout());
        $this->assertEquals(3, $config->getMaxRetries());
        $this->assertFalse($config->isDebug());
    }

    public function testSetters(): void
    {
        $config = new Config();

        $config->setAccessToken('token123')
            ->setClientId('client123')
            ->setClientSecret('secret123')
            ->setRedirectUri('http://localhost/callback')
            ->setTimeout(60)
            ->setDebug(true);

        $this->assertEquals('token123', $config->getAccessToken());
        $this->assertEquals('client123', $config->getClientId());
        $this->assertEquals('secret123', $config->getClientSecret());
        $this->assertEquals('http://localhost/callback', $config->getRedirectUri());
        $this->assertEquals(60, $config->getTimeout());
        $this->assertTrue($config->isDebug());
    }

    public function testConstructorArray(): void
    {
        $config = new Config([
            'access_token' => 'token123',
            'client_id' => 'client123',
            'timeout' => 45,
            'debug' => true,
        ]);

        $this->assertEquals('token123', $config->getAccessToken());
        $this->assertEquals('client123', $config->getClientId());
        $this->assertEquals(45, $config->getTimeout());
        $this->assertTrue($config->isDebug());
    }

    public function testTokenExpiration(): void
    {
        $config = new Config();

        // Token not set
        $this->assertFalse($config->isTokenExpired());

        // Token expires in future
        $config->setTokenExpiresAt(time() + 3600);
        $this->assertFalse($config->isTokenExpired());

        // Token expired
        $config->setTokenExpiresAt(time() - 3600);
        $this->assertTrue($config->isTokenExpired());

        // Token expires soon (within 5 min buffer)
        $config->setTokenExpiresAt(time() + 200);
        $this->assertTrue($config->isTokenExpired());
    }

    public function testHasCredentials(): void
    {
        $config = new Config();
        $this->assertFalse($config->hasCredentials());

        $config->setClientId('client123');
        $this->assertFalse($config->hasCredentials());

        $config->setClientSecret('secret123');
        $this->assertTrue($config->hasCredentials());
    }

    public function testHasAccessToken(): void
    {
        $config = new Config();
        $this->assertFalse($config->hasAccessToken());

        $config->setAccessToken('token123');
        $this->assertTrue($config->hasAccessToken());
    }

    public function testToArray(): void
    {
        $config = new Config([
            'access_token' => 'token123',
            'client_id' => 'client123',
            'timeout' => 60,
        ]);

        $array = $config->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('token123', $array['access_token']);
        $this->assertEquals('client123', $array['client_id']);
        $this->assertEquals(60, $array['timeout']);
    }

    public function testBaseUriNormalization(): void
    {
        $config = new Config();
        $config->setBaseUri('https://app.drchrono.com/');

        // Should remove trailing slash
        $this->assertEquals('https://app.drchrono.com', $config->getBaseUri());
    }
}

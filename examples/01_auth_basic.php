<?php

/**
 * Example 1: Basic Authentication with Access Token
 *
 * This example shows how to authenticate with an existing access token
 * and fetch the current authenticated user.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Exception\AuthenticationException;

// Create client with access token
$client = DrChronoClient::withAccessToken('your_access_token_here');

try {
    // Get current authenticated user
    $user = $client->getCurrentUser();

    echo "Authenticated as: {$user['first_name']} {$user['last_name']}\n";
    echo "Email: {$user['email']}\n";
    echo "Username: {$user['username']}\n";

    // Get user's offices
    $offices = $client->offices->listAll();
    echo "\nOffices: {$offices->count()}\n";

    foreach ($offices as $office) {
        echo "  - {$office['name']} ({$office['city']}, {$office['state']})\n";
    }

} catch (AuthenticationException $e) {
    echo "Authentication failed: {$e->getMessage()}\n";
    echo "Please check your access token.\n";
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

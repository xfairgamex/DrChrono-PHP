<?php

/**
 * Example 2: OAuth2 Authorization Flow
 *
 * This example demonstrates the complete OAuth2 flow:
 * 1. Generate authorization URL
 * 2. User authorizes (manual step)
 * 3. Exchange authorization code for tokens
 * 4. Use access token to make API calls
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Exception\AuthenticationException;

// OAuth2 credentials (replace with your own)
$clientId = 'your_client_id';
$clientSecret = 'your_client_secret';
$redirectUri = 'http://localhost:8000/callback';

// Create client with OAuth credentials
$client = DrChronoClient::withOAuth($clientId, $clientSecret, $redirectUri);

// Step 1: Generate authorization URL
$authUrl = $client->auth()->getAuthorizationUrl(
    scopes: ['patients:read', 'appointments:read', 'appointments:write'],
    state: bin2hex(random_bytes(16)) // CSRF protection
);

echo "Step 1: Authorization URL\n";
echo "Visit this URL to authorize:\n";
echo $authUrl . "\n\n";

// Step 2: After user authorizes, they'll be redirected to your callback URL
// The callback will include a 'code' parameter
// For this example, we'll simulate having received the code
echo "Step 2: After authorization, you'll receive a code\n";
echo "Enter the authorization code: ";
$authCode = trim(fgets(STDIN));

try {
    // Step 3: Exchange code for tokens
    echo "\nStep 3: Exchanging code for tokens...\n";
    $tokens = $client->auth()->exchangeAuthorizationCode($authCode);

    echo "Access token received: " . substr($tokens['access_token'], 0, 20) . "...\n";
    echo "Token expires in: {$tokens['expires_in']} seconds\n";
    echo "Refresh token: " . (isset($tokens['refresh_token']) ? 'Yes' : 'No') . "\n";

    // Step 4: Use the access token to make API calls
    echo "\nStep 4: Fetching user information...\n";
    $user = $client->getCurrentUser();
    echo "Authenticated as: {$user['first_name']} {$user['last_name']}\n";

    // Save tokens for later use
    echo "\nSave these tokens securely:\n";
    echo "Access Token: {$tokens['access_token']}\n";
    echo "Refresh Token: {$tokens['refresh_token']}\n";

} catch (AuthenticationException $e) {
    echo "OAuth flow failed: {$e->getMessage()}\n";
    print_r($e->getErrorDetails());
}

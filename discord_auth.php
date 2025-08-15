<?php
session_start();

// Load configuration
$config = require_once 'config.php';

// Discord OAuth Configuration
$client_id = $config['discord']['client_id'];
$client_secret = $config['discord']['client_secret'];
$redirect_uri = $config['discord']['redirect_uri'];
$scope = $config['discord']['scope'];

// Generate state parameter for security
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

// Discord OAuth URL
$discord_oauth_url = 'https://discord.com/api/oauth2/authorize?' . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => $scope,
    'state' => $state
]);

// Redirect to Discord OAuth
header('Location: ' . $discord_oauth_url);
exit();
?>

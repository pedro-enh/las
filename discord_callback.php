<?php
session_start();

// Load configuration
$config = require_once 'config.php';

// Discord OAuth Configuration
$client_id = $config['discord']['client_id'];
$client_secret = $config['discord']['client_secret'];
$redirect_uri = $config['discord']['redirect_uri'];

// Check if we have the authorization code and state
if (!isset($_GET['code']) || !isset($_GET['state'])) {
    header('Location: index.php?error=auth_failed');
    exit();
}

// Verify state parameter
if ($_GET['state'] !== $_SESSION['oauth_state']) {
    header('Location: index.php?error=invalid_state');
    exit();
}

$code = $_GET['code'];

// Exchange authorization code for access token
$token_url = 'https://discord.com/api/oauth2/token';
$token_data = [
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirect_uri
];

$token_options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($token_data)
    ]
];

$token_context = stream_context_create($token_options);
$token_response = file_get_contents($token_url, false, $token_context);

if ($token_response === FALSE) {
    header('Location: index.php?error=token_failed');
    exit();
}

$token_data = json_decode($token_response, true);

if (!isset($token_data['access_token'])) {
    header('Location: index.php?error=no_token');
    exit();
}

$access_token = $token_data['access_token'];

// Get user information from Discord
$user_url = 'https://discord.com/api/users/@me';
$user_options = [
    'http' => [
        'header' => "Authorization: Bearer " . $access_token . "\r\n",
        'method' => 'GET'
    ]
];

$user_context = stream_context_create($user_options);
$user_response = file_get_contents($user_url, false, $user_context);

if ($user_response === FALSE) {
    header('Location: index.php?error=user_failed');
    exit();
}

$user_data = json_decode($user_response, true);

if (!isset($user_data['id'])) {
    header('Location: index.php?error=no_user_data');
    exit();
}

// Store user information in session
$_SESSION['discord_user'] = [
    'id' => $user_data['id'],
    'username' => $user_data['username'],
    'discriminator' => $user_data['discriminator'] ?? '0000',
    'avatar' => $user_data['avatar'],
    'global_name' => $user_data['global_name'] ?? $user_data['username']
];

// Clean up OAuth state
unset($_SESSION['oauth_state']);

// Redirect back to main page
header('Location: index.php#whitelist');
exit();
?>

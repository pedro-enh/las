<?php
session_start();

// Load configuration
$config = require_once 'config.php';

// Check if user is logged in with Discord and has admin role
if (!isset($_SESSION['discord_user']) || !hasAdminRole($_SESSION['discord_user']['id'], $config)) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

function hasAdminRole($user_id, $config) {
    $bot_token = $config['bot']['token'];
    $guild_id = $config['bot']['guild_id'];
    $admin_role_id = $config['bot']['admin_role_id'];
    
    // If bot token is not configured, fall back to the old admin list
    if ($bot_token === 'YOUR_BOT_TOKEN_HERE' || empty($bot_token)) {
        error_log("Bot token not configured, falling back to admin list");
        return in_array($user_id, $config['admins']);
    }
    
    // Get user's roles from Discord API
    $url = "https://discord.com/api/v10/guilds/{$guild_id}/members/{$user_id}";
    
    $options = [
        'http' => [
            'header' => "Authorization: Bot {$bot_token}\r\n",
            'method' => 'GET',
            'ignore_errors' => true
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        error_log("Failed to get user roles for user: {$user_id}");
        // Fall back to the old admin list if API call fails
        return in_array($user_id, $config['admins']);
    }
    
    $member_data = json_decode($response, true);
    
    if (!isset($member_data['roles']) || !is_array($member_data['roles'])) {
        error_log("Invalid member data for user: {$user_id}");
        // Fall back to the old admin list if data is invalid
        return in_array($user_id, $config['admins']);
    }
    
    // Check if user has the admin role
    $has_admin_role = in_array($admin_role_id, $member_data['roles']);
    
    error_log("User {$user_id} admin role check: " . ($has_admin_role ? 'HAS_ROLE' : 'NO_ROLE'));
    
    return $has_admin_role;
}

// Get status parameter
$status = $_GET['status'] ?? '';

if (!in_array($status, ['accept', 'reject'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit();
}

// Set content type to JSON
header('Content-Type: application/json');

// Get applications by status
$applications = getAllApplicationsByStatus($status);

// Return applications as JSON
echo json_encode(array_values($applications));

function getAllApplicationsByStatus($status) {
    $applications_file = 'data/applications.json';
    if (!file_exists($applications_file)) {
        return [];
    }
    
    $data = json_decode(file_get_contents($applications_file), true);
    if (!$data) return [];
    
    $today = date('Y-m-d');
    return array_filter($data, function($app) use ($status, $today) {
        return $app['status'] === $status && 
               isset($app['admin_decision']['timestamp']) &&
               strpos($app['admin_decision']['timestamp'], $today) === 0;
    });
}
?>

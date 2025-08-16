<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['discord_user'])) {
    header('Location: discord_auth.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forum.php');
    exit();
}

$user = $_SESSION['discord_user'];

// Load configuration
$config = require_once 'config.php';

// Discord webhook URL
$webhook_url = $config['webhook']['url'];

// Validate and sanitize form data
$thread_title = trim($_POST['thread_title'] ?? '');
$player_name = trim($_POST['player_name'] ?? '');
$suspect_name = trim($_POST['suspect_name'] ?? '');
$violation_date = trim($_POST['violation_date'] ?? '');
$violation_time = trim($_POST['violation_time'] ?? '');
$content = trim($_POST['content'] ?? '');
$proofs = trim($_POST['proofs'] ?? '');
$watch_thread = isset($_POST['watch_thread']);
$discord_notifications = isset($_POST['discord_notifications']);

// Validation
$errors = [];

if (empty($thread_title)) {
    $errors[] = 'Thread title is required';
}

if (strlen($thread_title) > 200) {
    $errors[] = 'Thread title must be less than 200 characters';
}

if (empty($player_name)) {
    $errors[] = 'Your name in game is required';
}

// Validate player name format (should be realistic)
if (!empty($player_name) && !preg_match('/^[A-Za-z]+_[A-Za-z]+$/', $player_name)) {
    $errors[] = 'Player name must be in format: Firstname_Lastname (e.g., John_Smith)';
}

if (empty($suspect_name)) {
    $errors[] = 'Suspect name is required';
}

// Validate suspect name format (should be realistic)
if (!empty($suspect_name) && !preg_match('/^[A-Za-z]+_[A-Za-z]+$/', $suspect_name)) {
    $errors[] = 'Suspect name must be in format: Firstname_Lastname (e.g., Jane_Doe)';
}

if (empty($violation_date)) {
    $errors[] = 'Date of violation is required';
}

if (empty($violation_time)) {
    $errors[] = 'Time of violation is required';
}

if (empty($content)) {
    $errors[] = 'Detailed description is required';
}

// Content length validation removed - allow any length

// Validate proofs URL if provided
if (!empty($proofs) && !filter_var($proofs, FILTER_VALIDATE_URL)) {
    $errors[] = 'Proofs must be a valid URL';
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['forum_form_errors'] = $errors;
    $_SESSION['forum_form_data'] = $_POST;
    header('Location: post_thread.php');
    exit();
}

// Create data directory if it doesn't exist
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Generate unique ID for this post
$post_id = uniqid('post_', true);

// Prepare forum post data
$post_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'pending',
    'title' => $thread_title,
    'content' => $content,
    'player_name' => $player_name,
    'suspect_name' => $suspect_name,
    'violation_date' => $violation_date,
    'violation_time' => $violation_time,
    'proofs' => $proofs,
    'watch_thread' => $watch_thread,
    'discord_notifications' => $discord_notifications,
    'discord_user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'global_name' => $user['global_name'] ?? null,
        'avatar' => $user['avatar'] ?? null
    ],
    'comments' => []
];

// Load existing forum posts
$posts_file = 'data/forum_posts.json';
$posts = [];
if (file_exists($posts_file)) {
    $existing_data = json_decode(file_get_contents($posts_file), true);
    if ($existing_data) {
        $posts = $existing_data;
    }
}

// Add new post
$posts[$post_id] = $post_data;

// Save updated posts
file_put_contents($posts_file, json_encode($posts, JSON_PRETTY_PRINT));

// Prepare Discord embed for webhook
$embed = [
    'title' => '📋 New Forum Post - Las Vegas Role Play',
    'description' => 'A new forum post has been submitted for review',
    'color' => 0x2f00ff,
    'timestamp' => date('c'),
    'fields' => [
        [
            'name' => '📝 Thread Information',
            'value' => "**Title:** {$thread_title}\n**Status:** Pending Review",
            'inline' => false
        ],
        [
            'name' => '👤 Discord User',
            'value' => "**Name:** " . ($user['global_name'] ?? $user['username']) . "\n**Username:** {$user['username']}\n**Discord ID:** {$user['id']}",
            'inline' => true
        ],
        [
            'name' => '🎮 Game Information',
            'value' => "**Player Name:** {$player_name}\n**Suspect Name:** {$suspect_name}",
            'inline' => true
        ],
        [
            'name' => '📅 Incident Details',
            'value' => "**Date:** {$violation_date}\n**Time:** {$violation_time}",
            'inline' => false
        ],
        [
            'name' => '📖 Description',
            'value' => strlen($content) > 1024 ? substr($content, 0, 1021) . '...' : $content,
            'inline' => false
        ]
    ],
    'footer' => [
        'text' => 'Las Vegas Role Play - Forum System',
        'icon_url' => 'https://cdn.discordapp.com/attachments/123456789/123456789/server_icon.png'
    ],
    'thumbnail' => [
        'url' => $user['avatar'] ? "https://cdn.discordapp.com/avatars/{$user['id']}/{$user['avatar']}.png" : 'https://cdn.discordapp.com/embed/avatars/0.png'
    ]
];

// Add proofs field if provided
if (!empty($proofs)) {
    $embed['fields'][] = [
        'name' => '🔗 Evidence',
        'value' => "[View Proofs]({$proofs})",
        'inline' => false
    ];
}

// Prepare webhook payload
$webhook_data = [
    'content' => '🔔 **New Forum Post** - Requires Admin Review!\n@here',
    'embeds' => [$embed]
];

// Send to Discord webhook
$webhook_options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($webhook_data)
    ]
];

$webhook_context = stream_context_create($webhook_options);
$webhook_response = file_get_contents($webhook_url, false, $webhook_context);

// Check if webhook was successful
if ($webhook_response === FALSE) {
    // Log error but don't fail the submission
    error_log("Forum webhook failed for post ID: {$post_id}");
}

// Store post info in session for confirmation
$_SESSION['forum_post_submitted'] = [
    'post_id' => $post_id,
    'title' => $thread_title,
    'timestamp' => time(),
    'player_name' => $player_name,
    'suspect_name' => $suspect_name
];

// Clear form data
unset($_SESSION['forum_form_data']);
unset($_SESSION['forum_form_errors']);

// Redirect to forum success page
header('Location: forum_success.php');
exit();
?>

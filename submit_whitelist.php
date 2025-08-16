<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['discord_user'])) {
    header('Location: index.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['discord_user'];

// Load configuration
$config = require_once 'config.php';

// Discord webhook URL
$webhook_url = $config['webhook']['url'];

// Validate and sanitize form data
$real_name = trim($_POST['real_name'] ?? '');
$real_age = intval($_POST['real_age'] ?? 0);
$nationality = trim($_POST['nationality'] ?? '');
$character_name = trim($_POST['character_name'] ?? '');
$character_age = intval($_POST['character_age'] ?? 0);
$character_type = trim($_POST['character_type'] ?? '');
$rp_experience = trim($_POST['rp_experience'] ?? '');
$character_story = trim($_POST['character_story'] ?? '');
$terms = isset($_POST['terms']);
$truthful = isset($_POST['truthful']);

// Validation
$errors = [];

if (empty($real_name)) {
    $errors[] = 'Real name is required';
}

if ($real_age < 13 || $real_age > 100) {
    $errors[] = 'Real age must be between 13 and 100 years';
}

if (empty($nationality)) {
    $errors[] = 'Nationality is required';
}

if (empty($character_name)) {
    $errors[] = 'Character name is required';
}

// Validate character name format (should be realistic)
if (!empty($character_name) && !preg_match('/^[A-Za-z]+_[A-Za-z]+$/', $character_name)) {
    $errors[] = 'Character name must be in format: Firstname_Lastname (e.g., John_Smith)';
}

if ($character_age < 18 || $character_age > 80) {
    $errors[] = 'Character age must be between 18 and 80 years';
}

if (empty($character_type)) {
    $errors[] = 'Character type is required';
}

if (empty($rp_experience)) {
    $errors[] = 'Roleplay experience level is required';
}

if (empty($character_story)) {
    $errors[] = 'Character backstory is required';
}

// Check if story has at least 250 characters
if (strlen($character_story) < 250) {
    $errors[] = 'Character backstory must be at least 250 characters long';
}


if (!$terms) {
    $errors[] = 'You must agree to the server rules';
}

if (!$truthful) {
    $errors[] = 'You must certify that all information is true and accurate';
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php#whitelist');
    exit();
}

// Prepare Discord embed
$embed = [
    'title' => '🎮 New Whitelist Application - Las Vegas Role Play',
    'description' => 'A new whitelist application has been submitted for review',
    'color' => 0x2f00ff, // Updated to match the new primary color
    'timestamp' => date('c'),
    'fields' => [
        [
            'name' => '👤 Discord Information',
            'value' => "**Name:** " . ($user['global_name'] ?? $user['username']) . "\n**Username:** {$user['username']}\n**Discord ID:** {$user['id']}",
            'inline' => false
        ],
        [
            'name' => '🆔 Personal Information',
            'value' => "**Real Name:** {$real_name}\n**Real Age:** {$real_age} years\n**Nationality:** {$nationality}",
            'inline' => true
        ],
        [
            'name' => '🎭 Character Information',
            'value' => "**Character Name:** {$character_name}\n**Character Age:** {$character_age} years\n**Character Type:** {$character_type}",
            'inline' => true
        ],
        [
            'name' => '🎯 Roleplay Experience',
            'value' => $rp_experience,
            'inline' => false
        ],
        [
            'name' => '📖 Character Backstory',
            'value' => strlen($character_story) > 1024 ? substr($character_story, 0, 1021) . '...' : $character_story,
            'inline' => false
        ],
        [
            'name' => '📊 Application Stats',
            'value' => "**Story Length:** " . strlen($character_story) . " characters\n**Submission Time:** " . date('Y-m-d H:i:s T'),
            'inline' => false
        ]
    ],
    'footer' => [
        'text' => 'Las Vegas Role Play - Whitelist System',
        'icon_url' => 'https://cdn.discordapp.com/attachments/123456789/123456789/server_icon.png'
    ],
    'thumbnail' => [
        'url' => $user['avatar'] ? "https://cdn.discordapp.com/avatars/{$user['id']}/{$user['avatar']}.png" : 'https://cdn.discordapp.com/embed/avatars/0.png'
    ]
];

// Prepare webhook payload
$webhook_data = [
    'content' => '🔔 **New Whitelist Application** - Requires Admin Review!\n@here',
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
    $_SESSION['form_errors'] = ['An error occurred while submitting your application. Please try again.'];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php#whitelist');
    exit();
}

// Optional: Log the application to a file for backup
$log_entry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'discord_user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'global_name' => $user['global_name'] ?? null
    ],
    'application_data' => [
        'real_name' => $real_name,
        'real_age' => $real_age,
        'nationality' => $nationality,
        'character_name' => $character_name,
        'character_age' => $character_age,
        'character_type' => $character_type,
        'rp_experience' => $rp_experience,
        'character_story_length' => strlen($character_story)
    ]
];

// Create data directory if it doesn't exist
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Save application to JSON file for admin panel
$application_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'status' => 'pending',
    'discord_user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'global_name' => $user['global_name'] ?? null,
        'avatar' => $user['avatar'] ?? null
    ],
    'application_data' => [
        'real_name' => $real_name,
        'real_age' => $real_age,
        'nationality' => $nationality,
        'character_name' => $character_name,
        'character_age' => $character_age,
        'character_type' => $character_type,
        'rp_experience' => $rp_experience,
        'character_story' => $character_story
    ]
];

// Load existing applications
$applications_file = 'data/applications.json';
$applications = [];
if (file_exists($applications_file)) {
    $existing_data = json_decode(file_get_contents($applications_file), true);
    if ($existing_data) {
        $applications = $existing_data;
    }
}

// Generate unique ID for this application
$application_id = uniqid('app_', true);
$applications[$application_id] = $application_data;

// Save updated applications
file_put_contents($applications_file, json_encode($applications, JSON_PRETTY_PRINT));

// Create logs directory if it doesn't exist
if (!file_exists('logs')) {
    mkdir('logs', 0755, true);
}

// Log to file (optional - uncomment if you want to keep local logs)
// file_put_contents('logs/whitelist_applications.log', json_encode($log_entry) . "\n", FILE_APPEND | LOCK_EX);

// Store application in session for confirmation page
$_SESSION['application_submitted'] = [
    'timestamp' => time(),
    'character_name' => $character_name,
    'discord_id' => $user['id'],
    'real_name' => $real_name,
    'nationality' => $nationality,
    'character_type' => $character_type,
    'rp_experience' => $rp_experience
];

// Clear form data
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Redirect to success page
header('Location: success.php');
exit();
?>

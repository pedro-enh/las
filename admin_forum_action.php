<?php
session_start();
require_once 'config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['discord_user'])) {
    header('Location: discord_auth.php');
    exit();
}

$user = $_SESSION['discord_user'];

// Check if user is admin
$is_admin = false;
if (isset($config['admin_ids'])) {
    $is_admin = in_array($user['id'], $config['admin_ids']);
}

if (!$is_admin) {
    header('Location: forum.php');
    exit();
}

// Get action and post ID from URL
$action = $_GET['action'] ?? '';
$post_id = $_GET['id'] ?? '';

if (empty($action) || empty($post_id) || !in_array($action, ['approve', 'reject'])) {
    header('Location: forum.php');
    exit();
}

// Load forum posts
$posts_file = 'data/forum_posts.json';
$posts = [];
if (file_exists($posts_file)) {
    $existing_data = json_decode(file_get_contents($posts_file), true);
    if ($existing_data) {
        $posts = $existing_data;
    }
}

// Check if post exists
if (!isset($posts[$post_id])) {
    header('Location: forum.php');
    exit();
}

$post = $posts[$post_id];

// Check if post is pending (only pending posts can be approved/rejected)
if ($post['status'] !== 'pending') {
    header('Location: view_post.php?id=' . urlencode($post_id));
    exit();
}

// Update post status
$posts[$post_id]['status'] = $action === 'approve' ? 'approved' : 'rejected';
$posts[$post_id]['reviewed_by'] = [
    'admin_id' => $user['id'],
    'admin_name' => $user['global_name'] ?? $user['username'],
    'reviewed_at' => date('Y-m-d H:i:s')
];

// Save updated posts
file_put_contents($posts_file, json_encode($posts, JSON_PRETTY_PRINT));

// Send Discord notification to the post author (if they opted for notifications)
if ($post['discord_notifications'] && isset($config['bot'])) {
    $bot_token = $config['bot']['token'];
    $author_discord_id = $post['discord_user']['id'];
    
    // Prepare notification message
    $status_text = $action === 'approve' ? 'approved' : 'rejected';
    $status_emoji = $action === 'approve' ? '✅' : '❌';
    $status_color = $action === 'approve' ? 0x28a745 : 0xdc3545;
    
    $embed = [
        'title' => "{$status_emoji} Forum Post {$status_text}",
        'description' => "Your forum post has been {$status_text} by an administrator.",
        'color' => $status_color,
        'timestamp' => date('c'),
        'fields' => [
            [
                'name' => '📝 Post Title',
                'value' => $post['title'],
                'inline' => false
            ],
            [
                'name' => '👤 Reviewed By',
                'value' => $user['global_name'] ?? $user['username'],
                'inline' => true
            ],
            [
                'name' => '📅 Review Date',
                'value' => date('M j, Y \a\t g:i A'),
                'inline' => true
            ]
        ],
        'footer' => [
            'text' => 'Las Vegas Role Play - Forum System'
        ]
    ];
    
    if ($action === 'approve') {
        $embed['fields'][] = [
            'name' => '💬 What\'s Next?',
            'value' => 'Your post is now live and other players can comment on it. You can view it on our forum.',
            'inline' => false
        ];
    } else {
        $embed['fields'][] = [
            'name' => '📋 What\'s Next?',
            'value' => 'Your post did not meet our community guidelines. Please review our rules and feel free to submit a new post.',
            'inline' => false
        ];
    }
    
    // Send DM to user
    $dm_data = [
        'embeds' => [$embed]
    ];
    
    // Create DM channel first
    $create_dm_url = "https://discord.com/api/v10/users/@me/channels";
    $create_dm_data = [
        'recipient_id' => $author_discord_id
    ];
    
    $create_dm_options = [
        'http' => [
            'header' => [
                "Content-Type: application/json",
                "Authorization: Bot {$bot_token}"
            ],
            'method' => 'POST',
            'content' => json_encode($create_dm_data)
        ]
    ];
    
    $create_dm_context = stream_context_create($create_dm_options);
    $create_dm_response = file_get_contents($create_dm_url, false, $create_dm_context);
    
    if ($create_dm_response !== FALSE) {
        $dm_channel = json_decode($create_dm_response, true);
        
        if (isset($dm_channel['id'])) {
            // Send message to DM channel
            $send_message_url = "https://discord.com/api/v10/channels/{$dm_channel['id']}/messages";
            
            $send_message_options = [
                'http' => [
                    'header' => [
                        "Content-Type: application/json",
                        "Authorization: Bot {$bot_token}"
                    ],
                    'method' => 'POST',
                    'content' => json_encode($dm_data)
                ]
            ];
            
            $send_message_context = stream_context_create($send_message_options);
            $send_message_response = file_get_contents($send_message_url, false, $send_message_context);
            
            // Log the result for debugging
            if ($send_message_response === FALSE) {
                error_log("Failed to send forum notification DM to user {$author_discord_id} for post {$post_id}");
            } else {
                error_log("Successfully sent forum notification DM to user {$author_discord_id} for post {$post_id}");
            }
        }
    }
}

// Send webhook notification to admin channel
if (isset($config['webhook']['url'])) {
    $webhook_url = $config['webhook']['url'];
    
    $status_text = $action === 'approve' ? 'approved' : 'rejected';
    $status_emoji = $action === 'approve' ? '✅' : '❌';
    $status_color = $action === 'approve' ? 0x28a745 : 0xdc3545;
    
    $webhook_embed = [
        'title' => "{$status_emoji} Forum Post {$status_text}",
        'description' => "A forum post has been {$status_text} by an administrator.",
        'color' => $status_color,
        'timestamp' => date('c'),
        'fields' => [
            [
                'name' => '📝 Post Information',
                'value' => "**Title:** {$post['title']}\n**Author:** {$post['discord_user']['global_name'] ?? $post['discord_user']['username']}\n**Player:** {$post['player_name']}\n**Suspect:** {$post['suspect_name']}",
                'inline' => false
            ],
            [
                'name' => '👤 Admin Action',
                'value' => "**Reviewed By:** {$user['global_name'] ?? $user['username']}\n**Action:** " . ucfirst($status_text) . "\n**Date:** " . date('M j, Y \a\t g:i A'),
                'inline' => false
            ]
        ],
        'footer' => [
            'text' => 'Las Vegas Role Play - Forum System'
        ]
    ];
    
    $webhook_data = [
        'embeds' => [$webhook_embed]
    ];
    
    $webhook_options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($webhook_data)
        ]
    ];
    
    $webhook_context = stream_context_create($webhook_options);
    $webhook_response = file_get_contents($webhook_url, false, $webhook_context);
    
    if ($webhook_response === FALSE) {
        error_log("Failed to send forum action webhook for post {$post_id}");
    }
}

// Set success message
$action_text = $action === 'approve' ? 'approved' : 'rejected';
$_SESSION['admin_success'] = "Post has been {$action_text} successfully!";

// Redirect back to the post or admin panel
if (isset($_GET['redirect']) && $_GET['redirect'] === 'admin') {
    header('Location: admin.php#forum');
} else {
    header('Location: view_post.php?id=' . urlencode($post_id));
}
exit();
?>

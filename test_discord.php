<?php
// Test Discord Bot Configuration
// This script helps debug Discord bot issues

// Load configuration
$config = require_once 'config.php';

echo "<h2>Discord Bot Configuration Test</h2>";

// Check bot token
$bot_token = $config['bot']['token'];
echo "<p><strong>Bot Token Status:</strong> ";
if ($bot_token === 'YOUR_BOT_TOKEN_HERE' || empty($bot_token)) {
    echo "<span style='color: red;'>❌ NOT CONFIGURED</span></p>";
    echo "<p>Please configure your bot token in the .env file:</p>";
    echo "<pre>DISCORD_BOT_TOKEN=your_actual_bot_token_here</pre>";
} else {
    echo "<span style='color: green;'>✅ CONFIGURED</span></p>";
    echo "<p>Token: " . substr($bot_token, 0, 20) . "...</p>";
}

// Check guild ID
$guild_id = $config['bot']['guild_id'];
echo "<p><strong>Guild ID Status:</strong> ";
if ($guild_id === 'YOUR_GUILD_ID_HERE' || empty($guild_id)) {
    echo "<span style='color: red;'>❌ NOT CONFIGURED</span></p>";
} else {
    echo "<span style='color: green;'>✅ CONFIGURED</span> ($guild_id)</p>";
}

// Check channel ID
$channel_id = $config['bot']['channel_id'];
echo "<p><strong>Channel ID Status:</strong> ";
if ($channel_id === 'YOUR_CHANNEL_ID_HERE' || empty($channel_id)) {
    echo "<span style='color: orange;'>⚠️ NOT CONFIGURED (Optional)</span></p>";
} else {
    echo "<span style='color: green;'>✅ CONFIGURED</span> ($channel_id)</p>";
}

// Test bot token if configured
if ($bot_token !== 'YOUR_BOT_TOKEN_HERE' && !empty($bot_token)) {
    echo "<h3>Testing Bot Token...</h3>";
    
    // Test bot user endpoint
    $options = [
        'http' => [
            'header' => "Authorization: Bot {$bot_token}\r\n",
            'method' => 'GET',
            'ignore_errors' => true
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents('https://discord.com/api/v10/users/@me', false, $context);
    
    if ($response !== FALSE) {
        $bot_user = json_decode($response, true);
        if (isset($bot_user['username'])) {
            echo "<p><span style='color: green;'>✅ Bot Token Valid</span></p>";
            echo "<p>Bot Username: <strong>{$bot_user['username']}#{$bot_user['discriminator']}</strong></p>";
            echo "<p>Bot ID: <strong>{$bot_user['id']}</strong></p>";
        } else {
            echo "<p><span style='color: red;'>❌ Bot Token Invalid</span></p>";
            echo "<p>Response: " . htmlspecialchars($response) . "</p>";
        }
    } else {
        echo "<p><span style='color: red;'>❌ Failed to connect to Discord API</span></p>";
        if (isset($http_response_header)) {
            echo "<p>HTTP Headers: " . implode(', ', $http_response_header) . "</p>";
        }
    }
}

// Test DM functionality with a test user ID
if (isset($_GET['test_user_id']) && !empty($_GET['test_user_id'])) {
    $test_user_id = $_GET['test_user_id'];
    echo "<h3>Testing DM to User ID: $test_user_id</h3>";
    
    if ($bot_token !== 'YOUR_BOT_TOKEN_HERE' && !empty($bot_token)) {
        // Create DM channel
        $dm_channel_data = ['recipient_id' => $test_user_id];
        $dm_options = [
            'http' => [
                'header' => "Content-Type: application/json\r\nAuthorization: Bot {$bot_token}\r\n",
                'method' => 'POST',
                'content' => json_encode($dm_channel_data),
                'ignore_errors' => true
            ]
        ];
        
        $dm_context = stream_context_create($dm_options);
        $dm_response = file_get_contents('https://discord.com/api/v10/users/@me/channels', false, $dm_context);
        
        if ($dm_response !== FALSE) {
            $dm_channel = json_decode($dm_response, true);
            if (isset($dm_channel['id'])) {
                echo "<p><span style='color: green;'>✅ DM Channel Created</span></p>";
                echo "<p>Channel ID: {$dm_channel['id']}</p>";
                
                // Send test message
                $test_message = [
                    'content' => '🧪 **Test Message from Las Vegas RP Admin Panel**',
                    'embeds' => [[
                        'title' => '✅ Discord Bot Test',
                        'description' => 'This is a test message to verify the Discord bot is working correctly.',
                        'color' => 0x2f00ff,
                        'footer' => ['text' => 'Las Vegas Role Play - Bot Test'],
                        'timestamp' => date('c')
                    ]]
                ];
                
                $message_options = [
                    'http' => [
                        'header' => "Content-Type: application/json\r\nAuthorization: Bot {$bot_token}\r\n",
                        'method' => 'POST',
                        'content' => json_encode($test_message),
                        'ignore_errors' => true
                    ]
                ];
                
                $message_context = stream_context_create($message_options);
                $message_response = file_get_contents("https://discord.com/api/v10/channels/{$dm_channel['id']}/messages", false, $message_context);
                
                if ($message_response !== FALSE) {
                    echo "<p><span style='color: green;'>✅ Test DM Sent Successfully!</span></p>";
                } else {
                    echo "<p><span style='color: red;'>❌ Failed to send test DM</span></p>";
                    if (isset($http_response_header)) {
                        echo "<p>HTTP Headers: " . implode(', ', $http_response_header) . "</p>";
                    }
                }
            } else {
                echo "<p><span style='color: red;'>❌ Failed to create DM channel</span></p>";
                echo "<p>Response: " . htmlspecialchars($dm_response) . "</p>";
            }
        } else {
            echo "<p><span style='color: red;'>❌ Failed to create DM channel</span></p>";
        }
    } else {
        echo "<p><span style='color: red;'>❌ Bot token not configured</span></p>";
    }
}

echo "<hr>";
echo "<h3>How to Fix Issues:</h3>";
echo "<ol>";
echo "<li><strong>Configure Bot Token:</strong> Add your Discord bot token to the .env file</li>";
echo "<li><strong>Bot Permissions:</strong> Ensure your bot has 'Send Messages' and 'Send Messages in DMs' permissions</li>";
echo "<li><strong>Bot in Server:</strong> Make sure the bot is added to your Discord server</li>";
echo "<li><strong>User Settings:</strong> The user must allow DMs from server members</li>";
echo "</ol>";

echo "<h3>Test DM Functionality:</h3>";
echo "<form method='GET'>";
echo "<input type='text' name='test_user_id' placeholder='Enter Discord User ID' style='padding: 5px; margin: 5px;'>";
echo "<button type='submit' style='padding: 5px 10px; margin: 5px;'>Send Test DM</button>";
echo "</form>";
echo "<p><small>Enter a Discord User ID to test sending a DM. You can use your own Discord ID for testing.</small></p>";
?>

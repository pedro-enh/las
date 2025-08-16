<?php
// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_ENV)) {
            $_ENV[$name] = $value;
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Discord OAuth Configuration
// You need to create a Discord application at https://discord.com/developers/applications
// and get your Client ID and Client Secret

return [
    'discord' => [
       // Replace these with your actual Discord application credentials
        'client_id' => '1406065032655143058',
        'client_secret' => '4wRnQN55Uc05RGeyt4AP9v0dP2dicR08',
        
        // Update this with your actual domain
        'redirect_uri' => 'https://las-vegas.zeabur.app/discord_callback.php',
        
        // OAuth scopes
        'scope' => 'identify'
    ],
    
    'webhook' => [
        // Discord webhook URL for receiving whitelist applications
        'url' => 'https://discord.com/api/webhooks/1406037465403359385/z-DQoCdC8_MZyshj58DZZnXbLz8SHVtgLtJ_EhrbvDRltKW2H_n5g8KA-tkef0IQmtFr'
    ],
    
    'bot' => [
        // Discord bot token for sending DMs and mentions (loaded from .env file)
        'token' => $_ENV['DISCORD_BOT_TOKEN'] ?? 'YOUR_BOT_TOKEN_HERE',
        'guild_id' => $_ENV['DISCORD_GUILD_ID'] ?? 'YOUR_GUILD_ID_HERE',
        'channel_id' => $_ENV['DISCORD_CHANNEL_ID'] ?? null // Optional channel for mentions
    ],
    
    'admins' => [
        // Admin Discord IDs
        '675332512414695441',
        '767757877850800149',
        '1335359052166856706',
        '1324090429716697222',
        '1398005902321127536'
    ],
    
    'server' => [
        'name' => 'Las Vegas SAMP Server',
        'description' => 'سيرفر لاس فيغاس للرول بلاي المغربي'
    ]
];
?>

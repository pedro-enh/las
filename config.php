<?php
// Discord OAuth Configuration
// You need to create a Discord application at https://discord.com/developers/applications
// and get your Client ID and Client Secret

return [
    'discord' => [
        // Replace these with your actual Discord application credentials
        'client_id' => 'YOUR_DISCORD_CLIENT_ID',
        'client_secret' => 'YOUR_DISCORD_CLIENT_SECRET',
        
        // Update this with your actual domain
        'redirect_uri' => 'http://localhost/siteweb/discord_callback.php',
        
        // OAuth scopes
        'scope' => 'identify'
    ],
    
    'webhook' => [
        // Discord webhook URL for receiving whitelist applications
        'url' => 'https://discord.com/api/webhooks/1406037465403359385/z-DQoCdC8_MZyshj58DZZnXbLz8SHVtgLtJ_EhrbvDRltKW2H_n5g8KA-tkef0IQmtFr'
    ],
    
    'server' => [
        'name' => 'Las Vegas SAMP Server',
        'description' => 'سيرفر لاس فيغاس للرول بلاي المغربي'
    ]
];
?>

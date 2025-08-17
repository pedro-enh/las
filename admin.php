<?php
session_start();

// Load configuration
$config = require_once 'config.php';

// Check if user is logged in with Discord
if (!isset($_SESSION['discord_user'])) {
    // Redirect to Discord OAuth for admin login
    header('Location: discord_auth.php?admin=1');
    exit();
}

$user = $_SESSION['discord_user'];

// Check if user is admin
if (!in_array($user['id'], $config['admins'])) {
    // Not an admin, show access denied
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied - Las Vegas RP Admin</title>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
        <div class="container d-flex align-items-center justify-content-center min-vh-100">
            <div class="text-center">
                <div class="error-card">
                    <i class="fas fa-shield-alt error-icon"></i>
                    <h1 class="error-title">Access Denied</h1>
                    <p class="error-message">You don't have permission to access the admin panel.</p>
                    <a href="discord_auth.php?admin=1" class="btn btn-primary me-2">
                        <i class="fab fa-discord me-2"></i>
                        Login with Different Account
                    </a>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
        <style>
        .error-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 20px;
            padding: 3rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-title {
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #e0e0e0;
            margin-bottom: 2rem;
        }
        </style>
    </body>
    </html>
    <?php
    exit();
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $application_id = $_POST['application_id'] ?? '';
    $admin_message = trim($_POST['admin_message'] ?? '');
    
    if ($action === 'accept' || $action === 'reject') {
        handleApplicationDecision($application_id, $action, $admin_message, $user, $config);
    }
}

// Get all pending applications
$applications = getPendingApplications();

function getPendingApplications() {
    $applications_file = 'data/applications.json';
    if (!file_exists($applications_file)) {
        return [];
    }
    
    $data = json_decode(file_get_contents($applications_file), true);
    return $data ? array_filter($data, function($app) {
        return $app['status'] === 'pending';
    }) : [];
}

function handleApplicationDecision($application_id, $decision, $admin_message, $admin_user, $config) {
    $applications_file = 'data/applications.json';
    
    if (!file_exists($applications_file)) {
        return false;
    }
    
    $applications = json_decode(file_get_contents($applications_file), true);
    
    if (!isset($applications[$application_id])) {
        return false;
    }
    
    $application = &$applications[$application_id];
    $application['status'] = $decision;
    $application['admin_decision'] = [
        'admin_id' => $admin_user['id'],
        'admin_name' => $admin_user['global_name'] ?? $admin_user['username'],
        'decision' => $decision,
        'message' => $admin_message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Save updated applications
    file_put_contents($applications_file, json_encode($applications, JSON_PRETTY_PRINT));
    
    // Send Discord notification
    sendDiscordNotification($application, $decision, $admin_message, $config);
    
    return true;
}

function giveUserRole($user_id, $guild_id, $role_id, $bot_token) {
    // Add role to user using Discord API
    $url = "https://discord.com/api/v10/guilds/{$guild_id}/members/{$user_id}/roles/{$role_id}";
    
    $options = [
        'http' => [
            'header' => "Authorization: Bot {$bot_token}\r\nContent-Length: 0\r\n",
            'method' => 'PUT',
            'ignore_errors' => true
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    // Log the response for debugging
    error_log("Role assignment response for user {$user_id}: " . ($response !== FALSE ? 'SUCCESS' : 'FAILED'));
    if (isset($http_response_header)) {
        error_log("Role assignment HTTP headers: " . implode(', ', $http_response_header));
    }
    
    return $response !== FALSE;
}

function sendDiscordNotification($application, $decision, $admin_message, $config) {
    $bot_token = $config['bot']['token'];
    $user_id = $application['discord_user']['id'];
    $guild_id = $config['bot']['guild_id'];
    $whitelist_role_id = $config['bot']['whitelist_role_id'];
    
    // Debug: Log the bot token status
    error_log("Bot token status: " . ($bot_token === 'YOUR_BOT_TOKEN_HERE' ? 'NOT_CONFIGURED' : 'CONFIGURED'));
    error_log("User ID: " . $user_id);
    
    if ($bot_token === 'YOUR_BOT_TOKEN_HERE' || empty($bot_token)) {
        // Bot token not configured, skip Discord notification
        error_log("Discord notification skipped: Bot token not configured");
        return false;
    }
    
    // If application is accepted, give the user the whitelist role
    if ($decision === 'accept') {
        giveUserRole($user_id, $guild_id, $whitelist_role_id, $bot_token);
    }
    
    $status_emoji = $decision === 'accept' ? '✅' : '❌';
    $status_text = $decision === 'accept' ? 'ACCEPTED' : 'REJECTED';
    $status_color = $decision === 'accept' ? 0x28a745 : 0xdc3545;
    
    $embed = [
        'title' => "{$status_emoji} Whitelist Application {$status_text}",
        'description' => "Your whitelist application for **Las Vegas Role Play** has been **{$status_text}**.",
        'color' => $status_color,
        'fields' => [
            [
                'name' => '👤 Character Name',
                'value' => $application['application_data']['character_name'],
                'inline' => true
            ],
            [
                'name' => '📅 Decision Date',
                'value' => date('Y-m-d H:i:s'),
                'inline' => true
            ]
        ],
        'footer' => [
            'text' => 'Las Vegas Role Play - Admin Team'
        ],
        'timestamp' => date('c')
    ];
    
    if (!empty($admin_message)) {
        $embed['fields'][] = [
            'name' => '💬 Admin Message',
            'value' => $admin_message,
            'inline' => false
        ];
    }
    
    if ($decision === 'accept') {
        $embed['fields'][] = [
            'name' => '🔊 Next Steps',
            'value' => "Come to Whitelist Vocal <#1389417508494377160>",
            'inline' => false
        ];
    }
    
    // Send DM to user
    $dm_data = [
        'content' => "<@{$user_id}>",
        'embeds' => [$embed]
    ];
    
    // Create DM channel first
    $dm_channel_data = ['recipient_id' => $user_id];
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
    
    // Debug: Log DM channel creation response
    error_log("DM Channel Response: " . ($dm_response ?: 'FALSE'));
    if (isset($http_response_header)) {
        error_log("HTTP Response Headers: " . implode(', ', $http_response_header));
    }
    
    if ($dm_response !== FALSE) {
        $dm_channel = json_decode($dm_response, true);
        error_log("DM Channel Data: " . json_encode($dm_channel));
        
        if (isset($dm_channel['id'])) {
            // Send message to DM channel
            $message_options = [
                'http' => [
                    'header' => "Content-Type: application/json\r\nAuthorization: Bot {$bot_token}\r\n",
                    'method' => 'POST',
                    'content' => json_encode($dm_data),
                    'ignore_errors' => true
                ]
            ];
            
            $message_context = stream_context_create($message_options);
            $message_response = file_get_contents("https://discord.com/api/v10/channels/{$dm_channel['id']}/messages", false, $message_context);
            
            // Debug: Log message sending response
            error_log("Message Send Response: " . ($message_response ?: 'FALSE'));
            if (isset($http_response_header)) {
                error_log("Message HTTP Response Headers: " . implode(', ', $http_response_header));
            }
            
            if ($message_response !== FALSE) {
                error_log("Discord DM sent successfully to user: " . $user_id);
                return true;
            } else {
                error_log("Failed to send Discord DM to user: " . $user_id);
                return false;
            }
        } else {
            error_log("Failed to get DM channel ID. Response: " . json_encode($dm_channel));
            return false;
        }
    } else {
        error_log("Failed to create DM channel for user: " . $user_id);
        return false;
    }
    
    // Send mention in guild channel (if configured)
    if (isset($config['bot']['channel_id']) && $config['bot']['channel_id'] && $config['bot']['channel_id'] !== 'YOUR_CHANNEL_ID_HERE') {
        $guild_message = [
            'content' => "📋 **Whitelist Update** - <@{$user_id}> application has been **{$status_text}** by admin.",
            'embeds' => [$embed]
        ];
        
        // Send message to configured channel
        $channel_options = [
            'http' => [
                'header' => "Content-Type: application/json\r\nAuthorization: Bot {$bot_token}\r\n",
                'method' => 'POST',
                'content' => json_encode($guild_message)
            ]
        ];
        
        $channel_context = stream_context_create($channel_options);
        file_get_contents("https://discord.com/api/v10/channels/{$config['bot']['channel_id']}/messages", false, $channel_context);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Las Vegas RP</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://cdn.discordapp.com/icons/1303802817479446568/a_bc0ae6879a0973672910e44a2c9ac412.gif?size=512" alt="Las Vegas RP" class="server-logo me-2">
                <span class="brand-text">Las Vegas RP - Admin</span>
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <?php if (isset($user['avatar']) && $user['avatar']): ?>
                            <img src="https://cdn.discordapp.com/avatars/<?php echo $user['id']; ?>/<?php echo $user['avatar']; ?>.png?size=32" 
                                 alt="Avatar" class="rounded-circle me-2" width="24" height="24">
                        <?php endif; ?>
                        <?php echo htmlspecialchars($user['global_name'] ?? $user['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="index.php"><i class="fas fa-home me-2"></i>Main Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Admin Panel Content -->
    <div class="admin-panel">
        <div class="container">
            <div class="admin-header text-center mb-5">
                <h1 class="admin-title">
                    <i class="fas fa-shield-alt me-3"></i>
                    Admin Panel
                </h1>
                <p class="admin-subtitle">Manage whitelist applications for Las Vegas Role Play</p>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo count($applications); ?></h3>
                            <p>Pending Applications</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card clickable-card" onclick="showAcceptedApplications()">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo count(getAllApplicationsByStatus('accept')); ?></h3>
                            <p>Accepted Today</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card clickable-card" onclick="showRejectedApplications()">
                        <div class="stat-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo count(getAllApplicationsByStatus('reject')); ?></h3>
                            <p>Rejected Today</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications List -->
            <div class="applications-section">
                <h2 class="section-title">
                    <i class="fas fa-list me-2"></i>
                    Pending Applications
                </h2>

                <?php if (empty($applications)): ?>
                    <div class="no-applications">
                        <i class="fas fa-inbox"></i>
                        <h3>No Pending Applications</h3>
                        <p>All applications have been processed.</p>
                    </div>
                <?php else: ?>
                    <div class="applications-grid">
                        <?php foreach ($applications as $id => $app): ?>
                            <div class="application-card" id="app-<?php echo $id; ?>">
                                <div class="application-header">
                                    <div class="user-info">
                                        <?php if (isset($app['discord_user']['avatar']) && $app['discord_user']['avatar']): ?>
                                            <img src="https://cdn.discordapp.com/avatars/<?php echo $app['discord_user']['id']; ?>/<?php echo $app['discord_user']['avatar']; ?>.png?size=64" 
                                                 alt="Avatar" class="user-avatar">
                                        <?php else: ?>
                                            <div class="user-avatar-placeholder">
                                                <i class="fab fa-discord"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="user-details">
                                            <h4><?php echo htmlspecialchars($app['discord_user']['global_name'] ?? $app['discord_user']['username']); ?></h4>
                                            <p class="discord-id">ID: <?php echo htmlspecialchars($app['discord_user']['id']); ?></p>
                                            <p class="submission-time">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('Y-m-d H:i:s', strtotime($app['timestamp'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="application-status">
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock me-1"></i>
                                            Pending
                                        </span>
                                    </div>
                                </div>

                                <div class="application-details">
                                    <div class="detail-section">
                                        <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <strong>Real Name:</strong> <?php echo htmlspecialchars($app['application_data']['real_name']); ?>
                                            </div>
                                            <div class="detail-item">
                                                <strong>Age:</strong> <?php echo htmlspecialchars($app['application_data']['real_age']); ?> years
                                            </div>
                                            <div class="detail-item">
                                                <strong>Nationality:</strong> <?php echo htmlspecialchars($app['application_data']['nationality']); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="detail-section">
                                        <h5><i class="fas fa-mask me-2"></i>Character Information</h5>
                                        <div class="detail-grid">
                                            <div class="detail-item">
                                                <strong>Character Name:</strong> <?php echo htmlspecialchars($app['application_data']['character_name']); ?>
                                            </div>
                                            <div class="detail-item">
                                                <strong>Character Age:</strong> <?php echo htmlspecialchars($app['application_data']['character_age']); ?> years
                                            </div>
                                            <div class="detail-item">
                                                <strong>Character Type:</strong> <?php echo htmlspecialchars($app['application_data']['character_type']); ?>
                                            </div>
                                            <div class="detail-item">
                                                <strong>RP Experience:</strong> <?php echo htmlspecialchars($app['application_data']['rp_experience']); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="detail-section">
                                        <h5><i class="fas fa-book me-2"></i>Character Backstory</h5>
                                        <div class="backstory-content">
                                            <?php echo nl2br(htmlspecialchars($app['application_data']['character_story'])); ?>
                                        </div>
                                        <div class="backstory-stats">
                                            <span class="stat">
                                                <i class="fas fa-font me-1"></i>
                                                <?php echo strlen($app['application_data']['character_story']); ?> characters
                                            </span>
                                            <span class="stat">
                                                <i class="fas fa-list-ol me-1"></i>
                                                <?php echo count(array_filter(explode("\n", $app['application_data']['character_story']), function($line) { return trim($line) !== ''; })); ?> lines
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="application-actions">
                                    <form method="POST" class="action-form">
                                        <input type="hidden" name="application_id" value="<?php echo $id; ?>">
                                        
                                        <div class="admin-message-section">
                                            <label for="admin_message_<?php echo $id; ?>">Admin Message (Optional):</label>
                                            <textarea name="admin_message" id="admin_message_<?php echo $id; ?>" 
                                                      class="form-control admin-message-input" rows="3" 
                                                      placeholder="Add a message for the applicant (optional)..."></textarea>
                                        </div>
                                        
                                        <div class="action-buttons">
                                            <button type="submit" name="action" value="accept" class="btn btn-success action-btn">
                                                <i class="fas fa-check me-2"></i>
                                                Accept
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger action-btn">
                                                <i class="fas fa-times me-2"></i>
                                                Reject
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for Accepted/Rejected Applications -->
    <div class="modal fade" id="applicationsModal" tabindex="-1" aria-labelledby="applicationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="applicationsModalLabel">Applications</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalApplicationsList">
                        <!-- Applications will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add confirmation for actions
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const action = this.value;
                const actionText = action === 'accept' ? 'accept' : 'reject';
                
                if (!confirm(`Are you sure you want to ${actionText} this application?`)) {
                    e.preventDefault();
                }
            });
        });

        // Function to show accepted applications
        function showAcceptedApplications() {
            loadApplicationsModal('accept', 'Accepted Today');
        }

        // Function to show rejected applications
        function showRejectedApplications() {
            loadApplicationsModal('reject', 'Rejected Today');
        }

        // Function to load applications in modal
        function loadApplicationsModal(status, title) {
            document.getElementById('applicationsModalLabel').textContent = title;
            
            // Show loading
            document.getElementById('modalApplicationsList').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-white">Loading applications...</p>
                </div>
            `;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('applicationsModal'));
            modal.show();
            
            // Fetch applications data
            fetch(`admin_applications_data.php?status=${status}`)
                .then(response => response.json())
                .then(data => {
                    displayApplicationsInModal(data, status);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalApplicationsList').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading applications. Please try again.
                        </div>
                    `;
                });
        }

        // Function to display applications in modal
        function displayApplicationsInModal(applications, status) {
            const container = document.getElementById('modalApplicationsList');
            
            if (applications.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-white">
                        <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                        <h4>No ${status === 'accept' ? 'Accepted' : 'Rejected'} Applications Today</h4>
                        <p class="text-muted">No applications have been ${status === 'accept' ? 'accepted' : 'rejected'} today.</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            applications.forEach(app => {
                const statusClass = status === 'accept' ? 'success' : 'danger';
                const statusIcon = status === 'accept' ? 'check-circle' : 'times-circle';
                const statusText = status === 'accept' ? 'ACCEPTED' : 'REJECTED';
                
                html += `
                    <div class="application-card mb-4">
                        <div class="application-header">
                            <div class="user-info">
                                ${app.discord_user.avatar ? 
                                    `<img src="https://cdn.discordapp.com/avatars/${app.discord_user.id}/${app.discord_user.avatar}.png?size=64" alt="Avatar" class="user-avatar">` :
                                    `<div class="user-avatar-placeholder"><i class="fab fa-discord"></i></div>`
                                }
                                <div class="user-details">
                                    <h4>${app.discord_user.global_name || app.discord_user.username}</h4>
                                    <p class="discord-id">ID: ${app.discord_user.id}</p>
                                    <p class="submission-time">
                                        <i class="fas fa-clock me-1"></i>
                                        Submitted: ${new Date(app.timestamp).toLocaleString()}
                                    </p>
                                    <p class="decision-time">
                                        <i class="fas fa-${statusIcon} me-1"></i>
                                        ${statusText}: ${new Date(app.admin_decision.timestamp).toLocaleString()}
                                    </p>
                                </div>
                            </div>
                            <div class="application-status">
                                <span class="status-badge status-${statusClass}">
                                    <i class="fas fa-${statusIcon} me-1"></i>
                                    ${statusText}
                                </span>
                            </div>
                        </div>
                        
                        <div class="application-details">
                            <div class="detail-section">
                                <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <strong>Real Name:</strong> ${app.application_data.real_name}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Age:</strong> ${app.application_data.real_age} years
                                    </div>
                                    <div class="detail-item">
                                        <strong>Nationality:</strong> ${app.application_data.nationality}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h5><i class="fas fa-mask me-2"></i>Character Information</h5>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <strong>Character Name:</strong> ${app.application_data.character_name}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Character Age:</strong> ${app.application_data.character_age} years
                                    </div>
                                    <div class="detail-item">
                                        <strong>Character Type:</strong> ${app.application_data.character_type}
                                    </div>
                                    <div class="detail-item">
                                        <strong>RP Experience:</strong> ${app.application_data.rp_experience}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="detail-section">
                                <h5><i class="fas fa-user-shield me-2"></i>Admin Decision</h5>
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <strong>Admin:</strong> ${app.admin_decision.admin_name}
                                    </div>
                                    <div class="detail-item">
                                        <strong>Decision Time:</strong> ${new Date(app.admin_decision.timestamp).toLocaleString()}
                                    </div>
                                    ${app.admin_decision.message ? 
                                        `<div class="detail-item full-width">
                                            <strong>Admin Message:</strong><br>
                                            <div class="admin-message">${app.admin_decision.message}</div>
                                        </div>` : ''
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
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

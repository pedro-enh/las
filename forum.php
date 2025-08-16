<?php
session_start();
require_once 'config.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['discord_user']);
$user = $is_logged_in ? $_SESSION['discord_user'] : null;

// Load forum posts
$posts_file = 'data/forum_posts.json';
$posts = [];
if (file_exists($posts_file)) {
    $existing_data = json_decode(file_get_contents($posts_file), true);
    if ($existing_data) {
        $posts = $existing_data;
    }
}

// Sort posts by timestamp (newest first)
uasort($posts, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - Las Vegas Role Play</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .forum-container {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(0, 47, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .forum-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .forum-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(47, 0, 255, 0.3);
        }

        .post-thread-btn {
            background: linear-gradient(45deg, var(--primary-color), #4a00ff);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .post-thread-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(47, 0, 255, 0.3);
            color: white;
        }

        .forum-tabs {
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(47, 0, 255, 0.2);
            color: #e0e0e0;
            margin-right: 0.5rem;
            border-radius: 10px 10px 0 0;
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .forum-post {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .forum-post:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(47, 0, 255, 0.2);
        }

        .post-header {
            display: flex;
            align-items: center;
            justify-content: between;
            margin-bottom: 1rem;
        }

        .post-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            margin-right: 1rem;
        }

        .post-info h5 {
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
        }

        .post-meta {
            color: #aaa;
            font-size: 0.9rem;
        }

        .post-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .status-approved {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .status-rejected {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .post-content {
            color: #e0e0e0;
            margin-bottom: 1rem;
        }

        .post-details {
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
        }

        .post-details .row {
            margin: 0;
        }

        .post-details .col-md-6 {
            padding: 0.25rem 0.5rem;
        }

        .post-details strong {
            color: var(--primary-color);
        }

        .post-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(47, 0, 255, 0.2);
        }

        .view-post-btn {
            background: rgba(47, 0, 255, 0.2);
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .view-post-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        .no-posts {
            text-align: center;
            color: #aaa;
            padding: 3rem;
            font-size: 1.1rem;
        }

        .login-prompt {
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-prompt h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .discord-login-btn {
            background: #5865F2;
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .discord-login-btn:hover {
            background: #4752C4;
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forum-container">
            <div class="forum-header">
                <h1 class="forum-title">
                    <i class="fas fa-comments me-3"></i>
                    Las Vegas Role Play Forum
                </h1>
                <p class="text-muted">Community discussions and reports</p>
                
                <?php if ($is_logged_in): ?>
                    <a href="post_thread.php" class="post-thread-btn">
                        <i class="fas fa-plus me-2"></i>
                        Post Thread
                    </a>
                <?php else: ?>
                    <div class="login-prompt">
                        <h4>Login Required</h4>
                        <p class="text-muted mb-3">You need to login with Discord to create forum posts</p>
                        <a href="discord_auth.php?forum=1" class="discord-login-btn">
                            <i class="fab fa-discord me-2"></i>
                            Login with Discord
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Forum Tabs -->
            <ul class="nav nav-tabs forum-tabs" id="forumTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab">
                        <i class="fas fa-list me-2"></i>All Posts
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                        <i class="fas fa-clock me-2"></i>Pending Review
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved" type="button" role="tab">
                        <i class="fas fa-check me-2"></i>Approved
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab">
                        <i class="fas fa-times me-2"></i>Rejected
                    </button>
                </li>
            </ul>

            <!-- Forum Content -->
            <div class="tab-content" id="forumTabContent">
                <!-- All Posts -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <?php if (empty($posts)): ?>
                        <div class="no-posts">
                            <i class="fas fa-comments fa-3x mb-3" style="color: #666;"></i>
                            <p>No forum posts yet. Be the first to create one!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post_id => $post): ?>
                            <?php include 'forum_post_card.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pending Posts -->
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    <?php 
                    $pending_posts = array_filter($posts, function($post) {
                        return $post['status'] === 'pending';
                    });
                    ?>
                    <?php if (empty($pending_posts)): ?>
                        <div class="no-posts">
                            <i class="fas fa-clock fa-3x mb-3" style="color: #ffc107;"></i>
                            <p>No pending posts</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pending_posts as $post_id => $post): ?>
                            <?php include 'forum_post_card.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Approved Posts -->
                <div class="tab-pane fade" id="approved" role="tabpanel">
                    <?php 
                    $approved_posts = array_filter($posts, function($post) {
                        return $post['status'] === 'approved';
                    });
                    ?>
                    <?php if (empty($approved_posts)): ?>
                        <div class="no-posts">
                            <i class="fas fa-check fa-3x mb-3" style="color: #28a745;"></i>
                            <p>No approved posts</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($approved_posts as $post_id => $post): ?>
                            <?php include 'forum_post_card.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Rejected Posts -->
                <div class="tab-pane fade" id="rejected" role="tabpanel">
                    <?php 
                    $rejected_posts = array_filter($posts, function($post) {
                        return $post['status'] === 'rejected';
                    });
                    ?>
                    <?php if (empty($rejected_posts)): ?>
                        <div class="no-posts">
                            <i class="fas fa-times fa-3x mb-3" style="color: #dc3545;"></i>
                            <p>No rejected posts</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($rejected_posts as $post_id => $post): ?>
                            <?php include 'forum_post_card.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="fas fa-home me-2"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

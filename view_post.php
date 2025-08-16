<?php
session_start();
require_once 'config.php';

// Get post ID from URL
$post_id = $_GET['id'] ?? '';

if (empty($post_id)) {
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

// Check if user is logged in
$is_logged_in = isset($_SESSION['discord_user']);
$user = $is_logged_in ? $_SESSION['discord_user'] : null;

// Check if user is admin
$is_admin = false;
if ($is_logged_in && isset($config['admins'])) {
    $is_admin = in_array($user['id'], $config['admins']);
}

// Get form data from session if available (for error recovery)
$form_data = $_SESSION['comment_form_data'] ?? [];
$form_errors = $_SESSION['comment_form_errors'] ?? [];

// Clear form data from session
unset($_SESSION['comment_form_data']);
unset($_SESSION['comment_form_errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Las Vegas Role Play Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .post-container {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(0, 47, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .post-header {
            border-bottom: 1px solid rgba(47, 0, 255, 0.2);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .post-title {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(47, 0, 255, 0.3);
        }

        .post-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .author-info {
            display: flex;
            align-items: center;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            margin-right: 1rem;
        }

        .author-avatar-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            margin-right: 1rem;
        }

        .post-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
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
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .post-details {
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .post-details h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(47, 0, 255, 0.1);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .detail-value {
            color: #e0e0e0;
        }

        .admin-actions {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .admin-actions h5 {
            color: #dc3545;
            margin-bottom: 1rem;
        }

        .admin-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-approve {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }

        .btn-approve:hover {
            background: #28a745;
            color: white;
        }

        .btn-reject {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .btn-reject:hover {
            background: #dc3545;
            color: white;
        }

        .comments-section {
            margin-top: 3rem;
        }

        .comments-header {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(47, 0, 255, 0.2);
        }

        .comment {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .admin-command-comment {
            background: rgba(220, 53, 69, 0.1);
            border: 2px solid rgba(220, 53, 69, 0.3);
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.2);
        }

        .admin-command-comment .comment-header {
            border-bottom: 1px solid rgba(220, 53, 69, 0.2);
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .admin-command-comment .admin-command-icon {
            color: #dc3545;
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            margin-right: 1rem;
        }

        .comment-avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: white;
            margin-right: 1rem;
        }

        .comment-form {
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .form-control {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(47, 0, 255, 0.2);
            color: #e0e0e0;
            border-radius: 10px;
        }

        .form-control:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(47, 0, 255, 0.25);
            color: #e0e0e0;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .submit-btn {
            background: linear-gradient(45deg, var(--primary-color), #4a00ff);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(47, 0, 255, 0.3);
        }

        .back-btn {
            background: rgba(108, 117, 125, 0.2);
            border: 1px solid #6c757d;
            color: #6c757d;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }

        .locked-message {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-top: 2rem;
        }

        .locked-message h6 {
            color: #ffc107;
            margin-bottom: 0.5rem;
        }

        .locked-message p {
            color: #e0e0e0;
            margin: 0;
        }

        .alert {
            border-radius: 15px;
            border: none;
            margin-bottom: 2rem;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="post-container">

            <!-- Post Header -->
            <div class="post-header">
                <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                
                <div class="post-meta">
                    <div class="author-info">
                        <?php if (isset($post['discord_user']['avatar']) && $post['discord_user']['avatar']): ?>
                            <img src="https://cdn.discordapp.com/avatars/<?php echo $post['discord_user']['id']; ?>/<?php echo $post['discord_user']['avatar']; ?>.png?size=64" 
                                 alt="Avatar" class="author-avatar">
                        <?php else: ?>
                            <div class="author-avatar-placeholder">
                                <i class="fab fa-discord"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <div style="color: var(--primary-color); font-weight: 600;">
                                <?php echo htmlspecialchars($post['discord_user']['global_name'] ?? $post['discord_user']['username']); ?>
                            </div>
                            <div class="text-muted">
                                Posted on <?php echo date('M j, Y \a\t g:i A', strtotime($post['timestamp'])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($post['status'] === 'pending' && $is_admin): ?>
                        <div class="dropdown">
                            <button class="btn post-status status-pending dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-clock me-1"></i>PENDING REVIEW
                            </button>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="statusDropdown">
                                <li>
                                    <a class="dropdown-item text-success" href="admin_forum_action.php?action=approve&id=<?php echo $post_id; ?>" 
                                       onclick="return confirm('Are you sure you want to approve this post?')">
                                        <i class="fas fa-check me-2"></i>Approve
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="admin_forum_action.php?action=reject&id=<?php echo $post_id; ?>" 
                                       onclick="return confirm('Are you sure you want to reject this post?')">
                                        <i class="fas fa-times me-2"></i>Reject
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="post-status status-<?php echo $post['status']; ?>">
                            <?php 
                            switch($post['status']) {
                                case 'pending':
                                    echo '<i class="fas fa-clock me-1"></i>Pending Review';
                                    break;
                                case 'approved':
                                    echo '<i class="fas fa-check me-1"></i>Approved';
                                    break;
                                case 'rejected':
                                    echo '<i class="fas fa-times me-1"></i>Rejected';
                                    break;
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Admin Actions -->
            <?php if ($is_admin && $post['status'] === 'pending'): ?>
                <div class="admin-actions">
                    <h5><i class="fas fa-shield-alt me-2"></i>Admin Actions</h5>
                    <p class="text-muted mb-3">Review this post and take appropriate action</p>
                    
                    <a href="admin_forum_action.php?action=approve&id=<?php echo $post_id; ?>" 
                       class="admin-btn btn-approve" 
                       onclick="return confirm('Are you sure you want to approve this post?')">
                        <i class="fas fa-check me-1"></i>Approve Post
                    </a>
                    
                    <a href="admin_forum_action.php?action=reject&id=<?php echo $post_id; ?>" 
                       class="admin-btn btn-reject" 
                       onclick="return confirm('Are you sure you want to reject this post?')">
                        <i class="fas fa-times me-1"></i>Reject Post
                    </a>
                </div>
            <?php endif; ?>

            <!-- Post Details -->
            <div class="post-details">
                <h5><i class="fas fa-info-circle me-2"></i>Incident Details</h5>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Player Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($post['player_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Suspect Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($post['suspect_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date of Violation:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($post['violation_date']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Time of Violation:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($post['violation_time']); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($post['proofs'])): ?>
                    <div class="mt-3">
                        <strong class="detail-label">Evidence:</strong> 
                        <a href="<?php echo htmlspecialchars($post['proofs']); ?>" target="_blank" class="text-primary">
                            <i class="fas fa-external-link-alt me-1"></i>View Proofs
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Post Content -->
            <div class="post-content">
                <h5 style="color: var(--primary-color); margin-bottom: 1rem;">
                    <i class="fas fa-file-alt me-2"></i>Description
                </h5>
                <p style="color: #e0e0e0; line-height: 1.6; white-space: pre-wrap;"><?php echo htmlspecialchars($post['content']); ?></p>
            </div>

            <!-- Comments Section -->
            <div class="comments-section">
                <div class="comments-header">
                    <i class="fas fa-comments me-2"></i>
                    Comments (<?php echo count($post['comments']); ?>)
                </div>

                <!-- Display Comments -->
                <?php if (!empty($post['comments'])): ?>
                    <?php foreach ($post['comments'] as $comment): ?>
                        <div class="comment <?php echo isset($comment['is_admin_command']) && $comment['is_admin_command'] ? 'admin-command-comment' : ''; ?>">
                            <div class="comment-header">
                                <?php if (isset($comment['discord_user']['avatar']) && $comment['discord_user']['avatar']): ?>
                                    <img src="https://cdn.discordapp.com/avatars/<?php echo $comment['discord_user']['id']; ?>/<?php echo $comment['discord_user']['avatar']; ?>.png?size=64" 
                                         alt="Avatar" class="comment-avatar">
                                <?php else: ?>
                                    <div class="comment-avatar-placeholder">
                                        <i class="fab fa-discord"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div>
                                    <div style="color: var(--primary-color); font-weight: 600;">
                                        <?php echo htmlspecialchars($comment['discord_user']['global_name'] ?? $comment['discord_user']['username']); ?>
                                        <?php 
                                        // Check if commenter is admin
                                        $is_commenter_admin = false;
                                        if (isset($config['admins']) && isset($comment['discord_user']['id'])) {
                                            $is_commenter_admin = in_array($comment['discord_user']['id'], $config['admins']);
                                        }
                                        
                                        if ($is_commenter_admin): ?>
                                            <span class="badge bg-danger ms-2" style="font-size: 0.7rem;">ADMIN</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.9rem;">
                                        <?php echo date('M j, Y \a\t g:i A', strtotime($comment['timestamp'])); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="color: #e0e0e0; line-height: 1.5;">
                                <?php if (isset($comment['is_admin_command']) && $comment['is_admin_command']): ?>
                                    <i class="fas fa-gavel admin-command-icon"></i>
                                <?php endif; ?>
                                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-comment-slash fa-2x mb-2"></i>
                        <p>No comments yet. Be the first to comment!</p>
                    </div>
                <?php endif; ?>

                <!-- Comment Form -->
                <?php if ($post['status'] === 'pending' && $is_logged_in): ?>
                    <!-- Success Messages -->
                    <?php if (isset($_SESSION['comment_success'])): ?>
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['comment_success']); ?></h6>
                        </div>
                        <?php unset($_SESSION['comment_success']); ?>
                    <?php endif; ?>

                    <!-- Error Messages -->
                    <?php if (!empty($form_errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Please correct the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach ($form_errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="comment-form">
                        <h6 style="color: var(--primary-color); margin-bottom: 1rem;">
                            <i class="fas fa-plus-circle me-2"></i>Add Comment
                        </h6>
                        
                        <form method="POST" action="submit_comment.php">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
                            
                            <div class="mb-3">
                                <textarea class="form-control" name="comment_content" rows="4" 
                                          placeholder="Write your comment here..." required><?php echo htmlspecialchars($form_data['comment_content'] ?? ''); ?></textarea>
                                <small class="form-text text-muted">Minimum 10 characters required</small>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane me-2"></i>
                                Post Comment
                            </button>
                        </form>
                    </div>
                <?php elseif ($post['status'] !== 'pending'): ?>
                    <div class="locked-message">
                        <h6><i class="fas fa-lock me-2"></i>Comments Locked</h6>
                        <p>Comments have been locked because this post has been <?php echo $post['status']; ?> by an administrator.</p>
                    </div>
                <?php elseif (!$is_logged_in): ?>
                    <div class="locked-message">
                        <h6><i class="fas fa-sign-in-alt me-2"></i>Login Required</h6>
                        <p>You need to <a href="discord_auth.php" class="text-primary">login with Discord</a> to post comments.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="forum.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Forum
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

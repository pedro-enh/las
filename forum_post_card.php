<div class="forum-post">
    <div class="post-header">
        <div class="d-flex align-items-center flex-grow-1">
            <?php if (isset($post['discord_user']['avatar']) && $post['discord_user']['avatar']): ?>
                <img src="https://cdn.discordapp.com/avatars/<?php echo $post['discord_user']['id']; ?>/<?php echo $post['discord_user']['avatar']; ?>.png?size=64" 
                     alt="Avatar" class="post-avatar">
            <?php else: ?>
                <div class="post-avatar d-flex align-items-center justify-content-center" style="background: var(--primary-color); color: white; font-size: 1.2rem;">
                    <i class="fab fa-discord"></i>
                </div>
            <?php endif; ?>
            
            <div class="post-info">
                <h5><?php echo htmlspecialchars($post['title']); ?></h5>
                <div class="post-meta">
                    By <strong><?php echo htmlspecialchars($post['discord_user']['global_name'] ?? $post['discord_user']['username']); ?></strong>
                    • <?php echo date('M j, Y \a\t g:i A', strtotime($post['timestamp'])); ?>
                    • <span class="post-status status-<?php echo $post['status']; ?>">
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
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="post-content">
        <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 200))); ?><?php echo strlen($post['content']) > 200 ? '...' : ''; ?></p>
    </div>

    <div class="post-details">
        <div class="row">
            <div class="col-md-6">
                <strong>Your Name in Game:</strong> <?php echo htmlspecialchars($post['player_name']); ?>
            </div>
            <div class="col-md-6">
                <strong>Suspect Name:</strong> <?php echo htmlspecialchars($post['suspect_name']); ?>
            </div>
            <div class="col-md-6">
                <strong>Date of Violation:</strong> <?php echo htmlspecialchars($post['violation_date']); ?>
            </div>
            <div class="col-md-6">
                <strong>Time of Violation:</strong> <?php echo htmlspecialchars($post['violation_time']); ?>
            </div>
        </div>
        
        <?php if (!empty($post['proofs'])): ?>
            <div class="mt-2">
                <strong>Proofs:</strong> 
                <a href="<?php echo htmlspecialchars($post['proofs']); ?>" target="_blank" class="text-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View Evidence
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="post-actions">
        <div>
            <i class="fas fa-comments me-1"></i>
            <span class="text-muted"><?php echo isset($post['comments']) ? count($post['comments']) : 0; ?> comments</span>
        </div>
        
        <div class="d-flex gap-2">
            <?php 
            // Check if user is admin and post is pending
            $is_admin = false;
            if (isset($_SESSION['discord_user']) && isset($config['admins'])) {
                $is_admin = in_array($_SESSION['discord_user']['id'], $config['admins']);
            }
            
            if ($is_admin && $post['status'] === 'pending'): ?>
                <a href="admin_forum_action.php?action=approve&id=<?php echo $post_id; ?>" 
                   class="btn btn-sm btn-success" 
                   onclick="return confirm('Are you sure you want to approve this post?')"
                   title="Approve Post">
                    <i class="fas fa-check"></i>
                </a>
                <a href="admin_forum_action.php?action=reject&id=<?php echo $post_id; ?>" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('Are you sure you want to reject this post?')"
                   title="Reject Post">
                    <i class="fas fa-times"></i>
                </a>
            <?php endif; ?>
            
            <a href="view_post.php?id=<?php echo $post_id; ?>" class="view-post-btn">
                <i class="fas fa-eye me-1"></i>View Post
            </a>
        </div>
    </div>
</div>

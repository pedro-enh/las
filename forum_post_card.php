<div class="forum-post-row" onclick="window.location.href='view_post.php?id=<?php echo $post_id; ?>'" style="cursor: pointer;">
    <div class="post-status-section">
        <?php 
        $statusClass = '';
        $statusIcon = '';
        $statusText = '';
        
        switch($post['status']) {
            case 'pending':
                $statusClass = 'status-pending-review';
                $statusIcon = 'fas fa-clock';
                $statusText = 'Pending review';
                break;
            case 'approved':
                $statusClass = 'status-approved';
                $statusIcon = 'fas fa-check';
                $statusText = 'Approved';
                break;
            case 'rejected':
                $statusClass = 'status-rejected';
                $statusIcon = 'fas fa-times';
                $statusText = 'Rejected';
                break;
            default:
                $statusClass = 'status-consideration';
                $statusIcon = 'fas fa-eye';
                $statusText = 'On consideration';
                break;
        }
        ?>
        <span class="status-badge <?php echo $statusClass; ?>">
            <i class="<?php echo $statusIcon; ?>"></i>
            <?php echo $statusText; ?>
        </span>
        
        <?php if (strtotime($post['timestamp']) > strtotime('-24 hours')): ?>
            <span class="new-badge">NEW</span>
        <?php endif; ?>
    </div>
    
    <div class="post-main-content">
        <div class="post-title-section">
            <h5 class="post-title">
                <a href="view_post.php?id=<?php echo $post_id; ?>" class="post-link">
                    <?php echo htmlspecialchars($post['title']); ?>
                </a>
            </h5>
            <div class="post-meta-info">
                <span class="post-author"><?php echo htmlspecialchars($post['discord_user']['global_name'] ?? $post['discord_user']['username']); ?></span>
                <span class="post-separator">•</span>
                <span class="post-time"><?php echo date('j M Y \a\t G:i', strtotime($post['timestamp'])); ?></span>
                <span class="post-separator">•</span>
                <span class="post-category"><?php echo htmlspecialchars($post['category'] ?? 'General'); ?></span>
            </div>
        </div>
    </div>
    
    <div class="post-stats">
        <div class="stat-item-small">
            <i class="fas fa-comments"></i>
            <span><?php echo isset($post['comments']) ? count($post['comments']) : 0; ?></span>
        </div>
    </div>
    
    <div class="post-last-activity">
        <div class="activity-time"><?php echo date('j M Y', strtotime($post['timestamp'])); ?></div>
        <div class="activity-user">
            <span><?php echo htmlspecialchars($post['discord_user']['global_name'] ?? $post['discord_user']['username']); ?></span>
            <?php if (isset($post['discord_user']['avatar']) && $post['discord_user']['avatar']): ?>
                <img src="https://cdn.discordapp.com/avatars/<?php echo $post['discord_user']['id']; ?>/<?php echo $post['discord_user']['avatar']; ?>.png?size=32" 
                     alt="Avatar" class="user-avatar-small">
            <?php else: ?>
                <div class="user-avatar-small user-avatar-placeholder">
                    <?php echo strtoupper(substr($post['discord_user']['username'], 0, 1)); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

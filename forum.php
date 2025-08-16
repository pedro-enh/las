<?php
session_start();
require_once 'config.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['discord_user']);
$user = $is_logged_in ? $_SESSION['discord_user'] : null;

// Check if user is admin
$is_admin = false;
if ($is_logged_in && isset($config['admins'])) {
    $is_admin = in_array($user['id'], $config['admins']);
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
                <span class="brand-text">Las Vegas RP</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#home"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about"><i class="fas fa-info-circle me-1"></i> About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#server-status"><i class="fas fa-server me-1"></i> Server Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="forum.php"><i class="fas fa-comments me-1"></i> Forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#whitelist"><i class="fas fa-user-plus me-1"></i> Whitelist</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Forum Section -->
    <section class="py-5" style="padding-top: 120px; min-height: 100vh; background: linear-gradient(to bottom, var(--dark-bg), var(--darker-bg));">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold text-white mb-4">Community Forum</h2>
                <p class="lead text-muted">Share your experiences, report issues, and connect with the Las Vegas RP community</p>
            </div>
            
            <!-- Post Thread Button -->
            <div class="text-center mb-4">
                <?php if ($is_logged_in): ?>
                    <a href="post_thread.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Post Thread
                    </a>
                <?php else: ?>
                    <a href="discord_auth.php?forum=1" class="btn btn-primary">
                        <i class="fab fa-discord me-2"></i>
                        Login with Discord
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="forum-container" style="background: rgba(0, 0, 0, 0.8); border: 1px solid rgba(0, 47, 255, 0.2); border-radius: 20px; padding: 2rem; backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);">

            <!-- Search Bar -->
            <div class="search-container mb-4">
                <div class="input-group">
                    <span class="input-group-text" style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(47, 0, 255, 0.2); color: var(--primary-color);">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="forumSearch" placeholder="Search by player name or forum title..." 
                           style="background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(47, 0, 255, 0.2); color: #e0e0e0;">
                    <button class="btn btn-outline-secondary" type="button" id="clearSearch" style="border-color: rgba(47, 0, 255, 0.2); color: #aaa;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <small class="text-muted mt-2 d-block">Search in titles, player names, or suspect names</small>
            </div>

            <!-- Forum Tabs -->
            <ul class="nav nav-tabs forum-tabs mb-4" id="forumTabs" role="tablist" style="border-bottom: 1px solid rgba(47, 0, 255, 0.2);">
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

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="footer-title">Las Vegas Role Play</h5>
                    <p class="footer-description">The most immersive GTA San Andreas Multiplayer roleplay experience. Join our community and start your story in Las Vegas.</p>
                    <div class="footer-social">
                        <a href="https://discord.gg/qGMCBgQFMt" target="_blank"><i class="fab fa-discord"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title">Server</h6>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="index.php#about">About</a></li>
                        <li><a href="index.php#server-status">Server Status</a></li>
                        <li><a href="index.php#whitelist">Whitelist</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title">Community</h6>
                    <ul class="footer-links">
                        <li><a href="https://discord.gg/qGMCBgQFMt" target="_blank">Discord</a></li>
                        <li><a href="forum.php">Forums</a></li>
                        <li><a href="#">Rules</a></li>
                        <li><a href="https://discord.gg/qGMCBgQFMt">Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="footer-title">Server Information</h6>
                    <p class="text-muted mb-2"><i class="fas fa-server me-2"></i> IP: 94.23.168.153:1285</p>
                    <p class="text-muted mb-2"><i class="fas fa-users me-2"></i> 500+ Active Players</p>
                    <p class="text-muted mb-2"><i class="fas fa-clock me-2"></i> 24/7 Online</p>
                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i> Las Vegas, USA</p>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p>&copy; 2024 Las Vegas Role Play. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('forumSearch');
            const clearButton = document.getElementById('clearSearch');
            
            // Search functionality
            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const allPosts = document.querySelectorAll('.forum-post-row');
                let visibleCount = 0;
                
                allPosts.forEach(post => {
                    const title = post.querySelector('.post-title')?.textContent.toLowerCase() || '';
                    const playerName = post.querySelector('.post-meta')?.textContent.toLowerCase() || '';
                    const content = post.textContent.toLowerCase();
                    
                    const matches = title.includes(searchTerm) || 
                                  playerName.includes(searchTerm) || 
                                  content.includes(searchTerm);
                    
                    if (searchTerm === '' || matches) {
                        post.style.display = '';
                        visibleCount++;
                    } else {
                        post.style.display = 'none';
                    }
                });
                
                // Show/hide no results message
                updateNoResultsMessage(searchTerm, visibleCount);
            }
            
            function updateNoResultsMessage(searchTerm, visibleCount) {
                // Remove existing no results message
                const existingMessage = document.querySelector('.no-search-results');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add no results message if needed
                if (searchTerm !== '' && visibleCount === 0) {
                    const activeTab = document.querySelector('.tab-pane.active');
                    if (activeTab) {
                        const noResultsDiv = document.createElement('div');
                        noResultsDiv.className = 'no-search-results text-center py-5';
                        noResultsDiv.innerHTML = `
                            <i class="fas fa-search fa-3x mb-3" style="color: #666;"></i>
                            <h5 class="text-muted">No results found</h5>
                            <p class="text-muted">No posts match your search for "${searchTerm}"</p>
                        `;
                        activeTab.appendChild(noResultsDiv);
                    }
                }
            }
            
            // Clear search
            function clearSearch() {
                searchInput.value = '';
                performSearch();
                searchInput.focus();
            }
            
            // Event listeners
            searchInput.addEventListener('input', performSearch);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Escape') {
                    clearSearch();
                }
            });
            
            clearButton.addEventListener('click', clearSearch);
            
            // Re-run search when switching tabs
            document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function() {
                    setTimeout(performSearch, 100); // Small delay to ensure tab content is loaded
                });
            });
            
            // Focus search input with Ctrl+F
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
            
            // Add search input styling on focus
            searchInput.addEventListener('focus', function() {
                this.style.borderColor = 'var(--primary-color)';
                this.style.boxShadow = '0 0 0 0.2rem rgba(47, 0, 255, 0.25)';
            });
            
            searchInput.addEventListener('blur', function() {
                this.style.borderColor = 'rgba(47, 0, 255, 0.2)';
                this.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>

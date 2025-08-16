<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['discord_user'])) {
    header('Location: discord_auth.php');
    exit();
}

$user = $_SESSION['discord_user'];

// Get form data from session if available (for error recovery)
$form_data = $_SESSION['forum_form_data'] ?? [];
$form_errors = $_SESSION['forum_form_errors'] ?? [];

// Clear form data from session
unset($_SESSION['forum_form_data']);
unset($_SESSION['forum_form_errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Thread - Las Vegas Role Play Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .post-form-container {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(0, 47, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(47, 0, 255, 0.3);
        }

        .user-info {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(0, 47, 255, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            margin-right: 1rem;
        }

        .user-avatar-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-right: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control, .form-select {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(47, 0, 255, 0.2);
            color: #e0e0e0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(0, 0, 0, 0.5);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(47, 0, 255, 0.25);
            color: #e0e0e0;
        }

        .form-control::placeholder {
            color: #aaa;
        }

        .form-text {
            color: #aaa;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .character-counter {
            text-align: right;
            font-size: 0.9rem;
            color: #aaa;
            margin-top: 0.5rem;
        }

        .form-check {
            padding: 1rem;
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .form-check-input {
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(47, 0, 255, 0.2);
            margin-right: 0.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(47, 0, 255, 0.25);
        }

        .form-check-label {
            color: #e0e0e0;
            font-weight: 500;
        }

        .submit-btn {
            background: linear-gradient(45deg, var(--primary-color), #4a00ff);
            border: none;
            color: white;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 50px;
            min-width: 250px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(47, 0, 255, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-text {
            display: inline;
        }

        .submit-btn:disabled .btn-text {
            display: none;
        }

        .submit-btn:disabled .loading {
            display: inline-block !important;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="post-form-container">
            <div class="form-header">
                <h1 class="form-title">
                    <i class="fas fa-plus-circle me-3"></i>
                    Post New Thread
                </h1>
                <p class="text-muted">Create a new forum post or report</p>
            </div>

            <!-- User Info -->
            <div class="user-info">
                <?php if (isset($user['avatar']) && $user['avatar']): ?>
                    <img src="https://cdn.discordapp.com/avatars/<?php echo $user['id']; ?>/<?php echo $user['avatar']; ?>.png?size=64" 
                         alt="Avatar" class="user-avatar">
                <?php else: ?>
                    <div class="user-avatar-placeholder">
                        <i class="fab fa-discord"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h5 class="mb-1" style="color: var(--primary-color);">
                        <?php echo htmlspecialchars($user['global_name'] ?? $user['username']); ?>
                    </h5>
                    <p class="mb-0 text-muted">Discord ID: <?php echo htmlspecialchars($user['id']); ?></p>
                </div>
            </div>

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

            <!-- Forum Post Form -->
            <form id="forumPostForm" method="POST" action="submit_forum_post.php">
                <div class="form-group">
                    <label for="thread_title">Thread Title *</label>
                    <input type="text" class="form-control" id="thread_title" name="thread_title" 
                           value="<?php echo htmlspecialchars($form_data['thread_title'] ?? ''); ?>" 
                           placeholder="Enter a descriptive title for your post" required>
                    <small class="form-text">Choose a clear and descriptive title</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="player_name">Your Name in Game *</label>
                            <input type="text" class="form-control" id="player_name" name="player_name" 
                                   value="<?php echo htmlspecialchars($form_data['player_name'] ?? ''); ?>" 
                                   placeholder="Example: John_Smith" required>
                            <small class="form-text">Your character name in the server</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="suspect_name">Suspect Name *</label>
                            <input type="text" class="form-control" id="suspect_name" name="suspect_name" 
                                   value="<?php echo htmlspecialchars($form_data['suspect_name'] ?? ''); ?>" 
                                   placeholder="Example: Jane_Doe" required>
                            <small class="form-text">Name of the person you're reporting</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="violation_date">Date of Violation *</label>
                            <input type="date" class="form-control" id="violation_date" name="violation_date" 
                                   value="<?php echo htmlspecialchars($form_data['violation_date'] ?? ''); ?>" required>
                            <small class="form-text">When did the incident occur?</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="violation_time">Time of Violation *</label>
                            <input type="time" class="form-control" id="violation_time" name="violation_time" 
                                   value="<?php echo htmlspecialchars($form_data['violation_time'] ?? ''); ?>" required>
                            <small class="form-text">Approximate time of the incident</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="content">Detailed Description *</label>
                    <textarea class="form-control" id="content" name="content" rows="8" required 
                              placeholder="Provide a detailed description of what happened..."><?php echo htmlspecialchars($form_data['content'] ?? ''); ?></textarea>
                    <small class="form-text">Describe the incident in detail</small>
                    <div id="content-counter" class="character-counter">0 characters</div>
                </div>

                <div class="form-group">
                    <label for="proofs">Proofs/Evidence</label>
                    <input type="url" class="form-control" id="proofs" name="proofs" 
                           value="<?php echo htmlspecialchars($form_data['proofs'] ?? ''); ?>" 
                           placeholder="https://example.com/evidence-link">
                    <small class="form-text">Link to screenshots, videos, or other evidence (optional)</small>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="watch_thread" name="watch_thread" 
                               <?php echo isset($form_data['watch_thread']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="watch_thread">
                            <i class="fas fa-eye me-2"></i>Watch this thread
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="discord_notifications" name="discord_notifications" 
                               <?php echo isset($form_data['discord_notifications']) ? 'checked' : 'checked'; ?>>
                        <label class="form-check-label" for="discord_notifications">
                            <i class="fab fa-discord me-2"></i>Receive Discord notifications
                        </label>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">
                            <i class="fas fa-paper-plane me-2"></i>
                            Post Thread
                        </span>
                        <span class="loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-2"></i>
                            Posting...
                        </span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="forum.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Forum
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counter for content textarea
            const contentTextarea = document.getElementById('content');
            const contentCounter = document.getElementById('content-counter');
            
            if (contentTextarea && contentCounter) {
                function updateCounter() {
                    const count = contentTextarea.value.length;
                    contentCounter.textContent = count + ' characters';
                    
                    // Simple character counter without color coding
                    contentCounter.style.color = '#aaa';
                }
                
                contentTextarea.addEventListener('input', updateCounter);
                updateCounter(); // Initial count
            }
            
            // Form submission handling
            const form = document.getElementById('forumPostForm');
            const submitBtn = document.getElementById('submitBtn');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function(e) {
                    submitBtn.disabled = true;
                    
                    // Basic validation
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.style.borderColor = '#dc3545';
                        } else {
                            field.style.borderColor = '';
                        }
                    });
                    
                    // Content validation removed - allow any length
                    
                    if (!isValid) {
                        e.preventDefault();
                        submitBtn.disabled = false;
                        return false;
                    }
                });
            }
            
            // Add floating animation to form elements
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                group.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                
                setTimeout(() => {
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>

<?php
session_start();

// Check if user has submitted a forum post
if (!isset($_SESSION['forum_post_submitted'])) {
    header('Location: forum.php');
    exit();
}

$post_info = $_SESSION['forum_post_submitted'];

// Clear the session data
unset($_SESSION['forum_post_submitted']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Submitted - Las Vegas Role Play Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .success-container {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid rgba(0, 47, 255, 0.2);
            border-radius: 20px;
            padding: 3rem;
            margin: 3rem auto;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
            max-width: 600px;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .success-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 0 10px rgba(47, 0, 255, 0.3);
        }

        .success-message {
            color: #e0e0e0;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .post-details {
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }

        .post-details h5 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            text-align: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(47, 0, 255, 0.1);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .detail-value {
            color: #e0e0e0;
        }

        .status-badge {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .action-buttons {
            margin-top: 2rem;
        }

        .btn-primary-custom {
            background: linear-gradient(45deg, var(--primary-color), #4a00ff);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(47, 0, 255, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: rgba(108, 117, 125, 0.2);
            border: 1px solid #6c757d;
            color: #6c757d;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }

        .info-box {
            background: rgba(23, 162, 184, 0.1);
            border: 1px solid rgba(23, 162, 184, 0.2);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
        }

        .info-box h6 {
            color: #17a2b8;
            margin-bottom: 1rem;
        }

        .info-box p {
            color: #e0e0e0;
            margin: 0;
            line-height: 1.5;
        }

        .countdown {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="success-title">Post Submitted Successfully!</h1>
            
            <p class="success-message">
                Your forum post has been submitted and is now pending review by our administration team. 
                You will receive a Discord notification once your post has been reviewed.
            </p>

            <div class="post-details">
                <h5><i class="fas fa-info-circle me-2"></i>Submission Details</h5>
                
                <div class="detail-row">
                    <span class="detail-label">Post ID:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($post_info['post_id']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Title:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($post_info['title']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Player Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($post_info['player_name']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Suspect Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($post_info['suspect_name']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Submitted:</span>
                    <span class="detail-value"><?php echo date('M j, Y \a\t g:i A', $post_info['timestamp']); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="status-badge">
                        <i class="fas fa-clock me-1"></i>Pending Review
                    </span>
                </div>
            </div>

            <div class="info-box">
                <h6><i class="fas fa-lightbulb me-2"></i>What happens next?</h6>
                <p>
                    • Your post will be reviewed by our administration team<br>
                    • You'll receive a Discord notification with the decision<br>
                    • If approved, other players can comment on your post<br>
                    • If rejected, you'll receive feedback on the reason
                </p>
            </div>

            <div class="action-buttons">
                <a href="forum.php" class="btn-primary-custom">
                    <i class="fas fa-comments me-2"></i>
                    View All Posts
                </a>
                
                <a href="post_thread.php" class="btn-secondary-custom">
                    <i class="fas fa-plus me-2"></i>
                    Create Another Post
                </a>
                
                <a href="index.php" class="btn-secondary-custom">
                    <i class="fas fa-home me-2"></i>
                    Back to Home
                </a>
            </div>

            <div class="countdown" id="countdown">
                Redirecting to forum in <span id="timer">10</span> seconds...
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto redirect countdown
        let timeLeft = 10;
        const timerElement = document.getElementById('timer');
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(() => {
            timeLeft--;
            timerElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                window.location.href = 'forum.php';
            }
        }, 1000);
        
        // Stop countdown if user interacts with the page
        document.addEventListener('click', () => {
            clearInterval(countdown);
            countdownElement.style.display = 'none';
        });
        
        document.addEventListener('keydown', () => {
            clearInterval(countdown);
            countdownElement.style.display = 'none';
        });
        
        // Add entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.success-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            container.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            
            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>

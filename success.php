<?php
session_start();

// Check if user is logged in and has submitted an application
if (!isset($_SESSION['discord_user']) || !isset($_SESSION['application_submitted'])) {
    header('Location: index.php');
    exit();
}

$user = $_SESSION['discord_user'];
$application = $_SESSION['application_submitted'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted Successfully - Las Vegas Role Play</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 class="success-title">Application Submitted Successfully!</h1>
            
            <div class="success-message">
                <p class="welcome-text">Thank you <strong><?php echo htmlspecialchars($user['global_name'] ?? $user['username']); ?></strong> for submitting your whitelist application to Las Vegas Role Play.</p>
                
                <div class="application-details">
                    <h3><i class="fas fa-info-circle me-2"></i>Application Details:</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Character Name:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($application['character_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Discord ID:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($application['discord_id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Submission Date:</span>
                            <span class="detail-value"><?php echo date('F j, Y \a\t g:i A', $application['timestamp']); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="next-steps">
                    <h3><i class="fas fa-list-ol me-2"></i>What Happens Next:</h3>
                    <div class="steps-container">
                        <div class="step-item">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Application Review</h4>
                                <p>Our administration team will carefully review your application</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Discord Response</h4>
                                <p>You'll receive a response on Discord within 24-48 hours</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Server Access</h4>
                                <p>If approved, you'll receive server connection details</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Start Playing</h4>
                                <p>Read the server rules and begin your roleplay journey</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="important-note">
                    <div class="note-header">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h4>Important Notice</h4>
                    </div>
                    <p>Please ensure your Discord privacy settings allow messages from server members so we can contact you with the application result.</p>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home me-2"></i>
                    Back to Home
                </a>
                <a href="https://discord.gg/qGMCBgQFMt" target="_blank" class="btn btn-discord">
                    <i class="fab fa-discord me-2"></i>
                    Join Discord
                </a>
                <a href="logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to bottom, var(--darker-bg), var(--dark-bg));
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(
                circle at 50% 50%,
                rgba(47, 0, 255, 0.1) 0%,
                transparent 70%
            );
            pointer-events: none;
        }

        .success-card {
            background: rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(47, 0, 255, 0.2);
            border-radius: 20px;
            padding: 3rem;
            max-width: 800px;
            width: 100%;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 1.5rem;
            animation: successPulse 2s infinite;
        }

        @keyframes successPulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .success-title {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 2rem;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(47, 0, 255, 0.3);
        }

        .success-message {
            text-align: left;
            margin-bottom: 2rem;
        }

        .welcome-text {
            font-size: 1.2rem;
            color: #e0e0e0;
            margin-bottom: 2rem;
            text-align: center;
            line-height: 1.6;
        }

        .application-details {
            background: rgba(47, 0, 255, 0.1);
            border: 1px solid rgba(47, 0, 255, 0.2);
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
        }

        .application-details h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .details-grid {
            display: grid;
            gap: 1rem;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            border: 1px solid rgba(47, 0, 255, 0.1);
        }

        .detail-label {
            color: #aaa;
            font-weight: 500;
        }

        .detail-value {
            color: #fff;
            font-weight: 600;
            font-family: monospace;
        }

        .next-steps {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.2);
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
        }

        .next-steps h3 {
            color: #28a745;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .steps-container {
            display: grid;
            gap: 1rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            border: 1px solid rgba(40, 167, 69, 0.1);
            transition: all 0.3s ease;
        }

        .step-item:hover {
            border-color: #28a745;
            transform: translateX(5px);
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .step-content h4 {
            color: #28a745;
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }

        .step-content p {
            color: #ccc;
            margin: 0;
            font-size: 0.9rem;
        }

        .important-note {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.2);
            padding: 1.5rem;
            border-radius: 15px;
            margin: 2rem 0;
        }

        .note-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .note-header i {
            color: #ffc107;
            font-size: 1.2rem;
        }

        .note-header h4 {
            color: #ffc107;
            margin: 0;
            font-size: 1.1rem;
        }

        .important-note p {
            color: #fff3cd;
            margin: 0;
            line-height: 1.6;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 150px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-discord {
            background: #5865f2;
            border: 2px solid #5865f2;
            color: white;
        }

        .btn-discord:hover {
            background: #4752c4;
            border-color: #4752c4;
            color: white;
            box-shadow: 0 8px 25px rgba(88, 101, 242, 0.4);
        }

        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 2rem;
                margin: 1rem;
            }

            .success-title {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
            }

            .step-item {
                flex-direction: column;
                text-align: center;
            }

            .detail-item {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.success-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
            
            // Animate step items
            const stepItems = document.querySelectorAll('.step-item');
            stepItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
                item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                
                setTimeout(() => {
                    item.style.opacity = '1';
                    item.style.transform = 'translateX(0)';
                }, 500 + (index * 100));
            });
        });
    </script>
</body>
</html>

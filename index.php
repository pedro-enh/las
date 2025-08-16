<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Las Vegas Role Play - GTA SAMP Server</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="https://cdn.discordapp.com/icons/1303802817479446568/a_bc0ae6879a0973672910e44a2c9ac412.gif?size=512" alt="Las Vegas RP" class="server-logo me-2">
                <span class="brand-text">Las Vegas RP</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about"><i class="fas fa-info-circle me-1"></i> About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#server-status"><i class="fas fa-server me-1"></i> Server Status</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="forum.php"><i class="fas fa-comments me-1"></i> Forum</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#whitelist"><i class="fas fa-user-plus me-1"></i> Whitelist</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-slide active" style="background-image: url('https://cdn.discordapp.com/banners/1303802817479446568/a_058bbe8abb3969dc636f61c7e1a2d207.webp?size=480&animated=true');">
        </div>
        
        <div class="container hero-content d-flex align-items-center justify-content-center min-vh-100">
            <div class="text-center">
                <h1 class="display-1 fw-bold hero-title mb-4">Las Vegas Role Play</h1>
                <p class="lead hero-subtitle mb-5">Experience the most immersive GTA SAMP roleplay server</p>
                
                <div class="server-status mb-4">
                    <i class="fas fa-circle text-success"></i>
                    <span class="text-white">Server Online</span>
                    <span class="text-muted ms-3">IP: 94.23.168.153:1285</span>
                </div>
                
                <div class="hero-buttons">
                    <div class="dropdown d-inline-block me-3">
                        <button class="btn hero-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download"></i> Download Launcher
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="https://www.sa-mp.mp/"><i class="fab fa-windows"></i> Windows</a></li>
                            <li><a class="dropdown-item" href="https://play.google.com/store/apps/details?id=ru.unisamp_mobile.launcher&hl=fr"><i class="fab fa-android"></i> Android</a></li>
                        </ul>
                    </div>
                    <a href="https://discord.gg/qGMCBgQFMt" target="_blank" class="btn hero-btn">
                        <i class="fab fa-discord"></i> Join Discord
                    </a>
                    <div class="mt-3">
                        <small class="text-muted">Join our community of 500+ active players</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5">
        <div class="container">
            <div class="about-header text-center mb-5">
                <h2 class="display-4 fw-bold text-white mb-4">Welcome to Las Vegas</h2>
                <p class="lead">Experience the most immersive GTA San Andreas Multiplayer roleplay server, where your story begins in the neon-lit streets of Las Vegas.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="about-card text-center h-100">
                        <div class="about-icon">
                            <i class="fas fa-theater-masks"></i>
                        </div>
                        <h3 class="about-title">Realistic Roleplay</h3>
                        <p class="about-text">Immerse yourself in authentic roleplay scenarios with strict IC/OOC rules and professional administration.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-card text-center h-100">
                        <div class="about-icon">
                            <i class="fas fa-city"></i>
                        </div>
                        <h3 class="about-title">Dynamic City</h3>
                        <p class="about-text">Explore a living, breathing Las Vegas with custom interiors, businesses, and endless opportunities.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-card text-center h-100">
                        <div class="about-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="about-title">Active Community</h3>
                        <p class="about-text">Join hundreds of players in our vibrant and welcoming international community.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="about-card text-center h-100">
                        <div class="about-icon">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <h3 class="about-title">Fair Administration</h3>
                        <p class="about-text">Professional staff team ensuring fair play and enjoyable experience for everyone.</p>
                    </div>
                </div>
            </div>
            
            <div class="about-stats mt-5">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Active Players</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Server Uptime</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Custom Jobs</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Server Status Section -->
    <section id="server-status" class="server-status-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold text-white mb-4">Server Information</h2>
                <p class="lead text-muted">Real-time server statistics and connection details</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="server-monitor-card">
                        <div class="server-monitor-header">
                            <div class="server-name">
                                <i class="fas fa-server text-success"></i>
                                Las Vegas Role Play
                            </div>
                            <div class="server-ip" onclick="copyIP()">
                                <span class="copy-ip">94.23.168.153:1285</span>
                                <i class="fas fa-copy ms-2"></i>
                            </div>
                        </div>
                        <div class="server-monitor-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="status-card">
                                        <div class="status-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="status-info">
                                            <div class="status-label">Players Online</div>
                                            <div class="status-value">127/200</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="status-card">
                                        <div class="status-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="status-info">
                                            <div class="status-label">Uptime</div>
                                            <div class="status-value">99.9%</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="status-card">
                                        <div class="status-icon">
                                            <i class="fas fa-gamepad"></i>
                                        </div>
                                        <div class="status-info">
                                            <div class="status-label">Game Mode</div>
                                            <div class="status-value">Las Vegas RP</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="status-card">
                                        <div class="status-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="status-info">
                                            <div class="status-label">Location</div>
                                            <div class="status-value">Morocco</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button class="join-btn" onclick="connectToServer()">
                                    <i class="fas fa-play"></i> Connect to Server
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Whitelist Section -->
    <section id="whitelist" class="whitelist-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold text-white mb-4">Join Our Server</h2>
                <p class="lead text-muted">To join Las Vegas Role Play, you need to login with Discord and submit a whitelist application</p>
            </div>
            
            <?php
            // Display messages
            if (isset($_SESSION['logout_message'])) {
                echo '<div class="alert alert-success text-center">' . $_SESSION['logout_message'] . '</div>';
                unset($_SESSION['logout_message']);
            }
            
            if (isset($_SESSION['form_errors'])) {
                echo '<div class="alert alert-danger">';
                echo '<strong>Please correct the following errors:</strong><ul class="mb-0 mt-2">';
                foreach ($_SESSION['form_errors'] as $error) {
                    echo '<li>' . htmlspecialchars($error) . '</li>';
                }
                echo '</ul></div>';
                unset($_SESSION['form_errors']);
            }
            
            // Display error messages from URL parameters
            if (isset($_GET['error'])) {
                $error_messages = [
                    'auth_failed' => 'Authentication failed. Please try again.',
                    'invalid_state' => 'Security verification error. Please try again.',
                    'token_failed' => 'Failed to get access token. Please try again.',
                    'no_token' => 'No access token received. Please try again.',
                    'user_failed' => 'Failed to get user information. Please try again.',
                    'no_user_data' => 'No user data received. Please try again.'
                ];
                
                $error_key = $_GET['error'];
                if (isset($error_messages[$error_key])) {
                    echo '<div class="alert alert-danger text-center">' . $error_messages[$error_key] . '</div>';
                }
            }
            
            if (!isset($_SESSION['discord_user'])) {
                echo '<div class="text-center">
                        <div class="discord-login-card mx-auto" style="max-width: 400px;">
                            <h4 class="text-white mb-4">Discord Authentication Required</h4>
                            <p class="text-muted mb-4">Please login with your Discord account to continue with the whitelist application.</p>
                            <a href="discord_auth.php" class="btn hero-btn w-100">
                                <i class="fab fa-discord me-2"></i>
                                Login with Discord
                            </a>
                        </div>
                      </div>';
            } else {
                include 'whitelist_form.php';
            }
            ?>
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
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#server-status">Server Status</a></li>
                        <li><a href="#whitelist">Whitelist</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-title">Community</h6>
                    <ul class="footer-links">
                        <li><a href="https://discord.gg/qGMCBgQFMt" target="_blank">Discord</a></li>
                        <li><a href="https://las-vegas.zeabur.app/forum.php">Forums</a></li>
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
</body>
</html>


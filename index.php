<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Las Vegas SAMP Server - سيرفر لاس فيغاس</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="banner">
            <img src="https://cdn.discordapp.com/banners/1303802817479446568/a_058bbe8abb3969dc636f61c7e1a2d207.webp?size=480&animated=true" alt="Las Vegas Banner" class="banner-img">
            <div class="banner-overlay">
                <h1 class="server-title">سيرفر لاس فيغاس</h1>
                <p class="server-subtitle">Las Vegas SAMP Server</p>
            </div>
        </div>
    </header>

    <nav class="navbar">
        <div class="container">
            <ul class="nav-links">
                <li><a href="#home">الرئيسية</a></li>
                <li><a href="#about">حول السيرفر</a></li>
                <li><a href="#rules">القوانين</a></li>
                <li><a href="#whitelist">طلب الانضمام</a></li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <section id="home" class="hero-section">
            <div class="container">
                <h2>مرحباً بك في سيرفر لاس فيغاس</h2>
                <p class="hero-description">
                    انضم إلى أفضل سيرفر رول بلاي مغربي في لعبة San Andreas Multiplayer
                </p>
                <a href="#whitelist" class="cta-button">ابدأ رحلتك الآن</a>
            </div>
        </section>

        <section id="about" class="about-section">
            <div class="container">
                <h2>حول سيرفر لاس فيغاس</h2>
                <div class="about-grid">
                    <div class="about-card">
                        <h3>🎭 رول بلاي حقيقي</h3>
                        <p>نوفر تجربة رول بلاي واقعية ومتطورة مع قوانين صارمة لضمان أفضل تجربة لعب</p>
                    </div>
                    <div class="about-card">
                        <h3>🏙️ مدينة متكاملة</h3>
                        <p>استكشف مدينة لاس فيغاس بكل تفاصيلها مع وظائف متنوعة وأنشطة لا محدودة</p>
                    </div>
                    <div class="about-card">
                        <h3>👥 مجتمع مغربي</h3>
                        <p>انضم إلى مجتمع مغربي نشط ومتفاعل مع إدارة محترفة ومتفهمة</p>
                    </div>
                    <div class="about-card">
                        <h3>⚖️ نظام عدالة</h3>
                        <p>نظام قضائي متطور مع محاكم حقيقية ونظام شرطة واقعي</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="rules" class="rules-section">
            <div class="container">
                <h2>قوانين السيرفر</h2>
                <div class="rules-list">
                    <div class="rule-item">
                        <h4>1. احترام اللاعبين</h4>
                        <p>يجب احترام جميع اللاعبين والإدارة في جميع الأوقات</p>
                    </div>
                    <div class="rule-item">
                        <h4>2. الرول بلاي الواقعي</h4>
                        <p>يجب اللعب بطريقة واقعية ومنطقية دون كسر الشخصية</p>
                    </div>
                    <div class="rule-item">
                        <h4>3. عدم استخدام الهاكات</h4>
                        <p>ممنوع استخدام أي برامج غش أو تلاعب في اللعبة</p>
                    </div>
                    <div class="rule-item">
                        <h4>4. اللغة المناسبة</h4>
                        <p>استخدام لغة مهذبة ومناسبة في جميع المحادثات</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="whitelist" class="whitelist-section">
            <div class="container">
                <h2>طلب الانضمام للسيرفر</h2>
                <p class="whitelist-description">
                    للانضمام إلى سيرفر لاس فيغاس، يجب عليك تسجيل الدخول بحساب Discord وملء طلب الانضمام
                </p>
                
                <?php
                // Display messages
                if (isset($_SESSION['logout_message'])) {
                    echo '<div class="message success">' . $_SESSION['logout_message'] . '</div>';
                    unset($_SESSION['logout_message']);
                }
                
                if (isset($_SESSION['form_errors'])) {
                    echo '<div class="message error">';
                    echo '<strong>يرجى تصحيح الأخطاء التالية:</strong><ul>';
                    foreach ($_SESSION['form_errors'] as $error) {
                        echo '<li>' . htmlspecialchars($error) . '</li>';
                    }
                    echo '</ul></div>';
                    unset($_SESSION['form_errors']);
                }
                
                // Display error messages from URL parameters
                if (isset($_GET['error'])) {
                    $error_messages = [
                        'auth_failed' => 'فشل في تسجيل الدخول. يرجى المحاولة مرة أخرى.',
                        'invalid_state' => 'خطأ في التحقق من الأمان. يرجى المحاولة مرة أخرى.',
                        'token_failed' => 'فشل في الحصول على رمز الوصول. يرجى المحاولة مرة أخرى.',
                        'no_token' => 'لم يتم الحصول على رمز الوصول. يرجى المحاولة مرة أخرى.',
                        'user_failed' => 'فشل في الحصول على معلومات المستخدم. يرجى المحاولة مرة أخرى.',
                        'no_user_data' => 'لم يتم الحصول على معلومات المستخدم. يرجى المحاولة مرة أخرى.'
                    ];
                    
                    $error_key = $_GET['error'];
                    if (isset($error_messages[$error_key])) {
                        echo '<div class="message error">' . $error_messages[$error_key] . '</div>';
                    }
                }
                
                if (!isset($_SESSION['discord_user'])) {
                    echo '<div class="discord-login">
                            <a href="discord_auth.php" class="discord-btn">
                                <img src="https://assets-global.website-files.com/6257adef93867e50d84d30e2/636e0a6a49cf127bf92de1e2_icon_clyde_blurple_RGB.png" alt="Discord" class="discord-icon">
                                تسجيل الدخول بـ Discord
                            </a>
                          </div>';
                } else {
                    include 'whitelist_form.php';
                }
                ?>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 سيرفر لاس فيغاس - جميع الحقوق محفوظة</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>

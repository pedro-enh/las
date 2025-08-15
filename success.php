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
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إرسال الطلب بنجاح - سيرفر لاس فيغاس</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                ✅
            </div>
            
            <h1>تم إرسال طلبك بنجاح!</h1>
            
            <div class="success-message">
                <p>شكراً لك <strong><?php echo htmlspecialchars($user['global_name']); ?></strong> على تقديم طلب الانضمام إلى سيرفر لاس فيغاس.</p>
                
                <div class="application-details">
                    <h3>تفاصيل الطلب:</h3>
                    <ul>
                        <li><strong>اسم الشخصية:</strong> <?php echo htmlspecialchars($application['character_name']); ?></li>
                        <li><strong>Discord ID:</strong> <?php echo htmlspecialchars($application['discord_id']); ?></li>
                        <li><strong>تاريخ الإرسال:</strong> <?php echo date('Y-m-d H:i:s', $application['timestamp']); ?></li>
                    </ul>
                </div>
                
                <div class="next-steps">
                    <h3>الخطوات التالية:</h3>
                    <ol>
                        <li>سيتم مراجعة طلبك من قبل فريق الإدارة</li>
                        <li>ستتلقى رد على Discord خلال 24-48 ساعة</li>
                        <li>في حالة القبول، ستحصل على تفاصيل الاتصال بالسيرفر</li>
                        <li>تأكد من قراءة قوانين السيرفر قبل البدء</li>
                    </ol>
                </div>
                
                <div class="important-note">
                    <h4>⚠️ ملاحظة مهمة:</h4>
                    <p>يرجى التأكد من أن إعدادات الخصوصية في Discord تسمح باستقبال الرسائل من أعضاء السيرفر حتى نتمكن من التواصل معك.</p>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="index.php" class="btn btn-primary">العودة للرئيسية</a>
                <a href="logout.php" class="btn btn-secondary">تسجيل الخروج</a>
            </div>
        </div>
    </div>

    <style>
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
        }

        .success-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: center;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .success-card h1 {
            color: #28a745;
            font-size: 2.5rem;
            margin-bottom: 30px;
            font-weight: 700;
        }

        .success-message {
            text-align: right;
            margin-bottom: 30px;
        }

        .success-message p {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .application-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .application-details h3 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .application-details ul {
            list-style: none;
            padding: 0;
        }

        .application-details li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .application-details li:last-child {
            border-bottom: none;
        }

        .next-steps {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .next-steps h3 {
            color: #1565c0;
            margin-bottom: 15px;
        }

        .next-steps ol {
            text-align: right;
            padding-right: 20px;
        }

        .next-steps li {
            padding: 5px 0;
            line-height: 1.5;
        }

        .important-note {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .important-note h4 {
            color: #856404;
            margin-bottom: 10px;
        }

        .important-note p {
            color: #856404;
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102,126,234,0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 20px;
                margin: 10px;
            }

            .success-card h1 {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</body>
</html>

<?php
session_start();

// Clear all session data
session_unset();
session_destroy();

// Start a new session for the logout message
session_start();
$_SESSION['logout_message'] = 'تم تسجيل الخروج بنجاح';

// Redirect to main page
header('Location: index.php');
exit();
?>

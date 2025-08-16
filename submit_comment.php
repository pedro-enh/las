<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['discord_user'])) {
    header('Location: discord_auth.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forum.php');
    exit();
}

$user = $_SESSION['discord_user'];

// Validate and sanitize form data
$post_id = trim($_POST['post_id'] ?? '');
$comment_content = trim($_POST['comment_content'] ?? '');

// Validation
$errors = [];

if (empty($post_id)) {
    $errors[] = 'Invalid post ID';
}

if (empty($comment_content)) {
    $errors[] = 'Comment content is required';
}

// Check if comment has at least 10 characters
if (strlen($comment_content) < 10) {
    $errors[] = 'Comment must be at least 10 characters long';
}

// Check if comment is not too long
if (strlen($comment_content) > 1000) {
    $errors[] = 'Comment must be less than 1000 characters';
}

// If there are errors, redirect back with error message
if (!empty($errors)) {
    $_SESSION['comment_form_errors'] = $errors;
    $_SESSION['comment_form_data'] = $_POST;
    header('Location: view_post.php?id=' . urlencode($post_id));
    exit();
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

// Check if post exists
if (!isset($posts[$post_id])) {
    $_SESSION['comment_form_errors'] = ['Post not found'];
    header('Location: forum.php');
    exit();
}

// Check if post is pending (only pending posts can receive comments)
if ($posts[$post_id]['status'] !== 'pending') {
    $_SESSION['comment_form_errors'] = ['Comments are only allowed on pending posts'];
    header('Location: view_post.php?id=' . urlencode($post_id));
    exit();
}

// Prepare comment data
$comment_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'content' => $comment_content,
    'discord_user' => [
        'id' => $user['id'],
        'username' => $user['username'],
        'global_name' => $user['global_name'] ?? null,
        'avatar' => $user['avatar'] ?? null
    ]
];

// Add comment to post
if (!isset($posts[$post_id]['comments'])) {
    $posts[$post_id]['comments'] = [];
}

$posts[$post_id]['comments'][] = $comment_data;

// Save updated posts
file_put_contents($posts_file, json_encode($posts, JSON_PRETTY_PRINT));

// Clear form data
unset($_SESSION['comment_form_data']);
unset($_SESSION['comment_form_errors']);

// Set success message
$_SESSION['comment_success'] = 'Comment posted successfully!';

// Redirect back to post
header('Location: view_post.php?id=' . urlencode($post_id) . '#comments');
exit();
?>

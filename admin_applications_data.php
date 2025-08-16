<?php
session_start();

// Load configuration
$config = require_once 'config.php';

// Check if user is logged in with Discord and is admin
if (!isset($_SESSION['discord_user']) || !in_array($_SESSION['discord_user']['id'], $config['admins'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

// Get status parameter
$status = $_GET['status'] ?? '';

if (!in_array($status, ['accept', 'reject'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit();
}

// Set content type to JSON
header('Content-Type: application/json');

// Get applications by status
$applications = getAllApplicationsByStatus($status);

// Return applications as JSON
echo json_encode(array_values($applications));

function getAllApplicationsByStatus($status) {
    $applications_file = 'data/applications.json';
    if (!file_exists($applications_file)) {
        return [];
    }
    
    $data = json_decode(file_get_contents($applications_file), true);
    if (!$data) return [];
    
    $today = date('Y-m-d');
    return array_filter($data, function($app) use ($status, $today) {
        return $app['status'] === $status && 
               isset($app['admin_decision']['timestamp']) &&
               strpos($app['admin_decision']['timestamp'], $today) === 0;
    });
}
?>

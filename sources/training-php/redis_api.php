<?php
header('Content-Type: application/json');

require_once __DIR__ . '/configs/redis.php';

try {
    $redis = createRedisClient();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $key = isset($input['key']) ? (string)$input['key'] : null;
        $value = isset($input['value']) ? (string)$input['value'] : null;
        $delete = isset($input['delete']) ? (bool)$input['delete'] : false;
        
        if ($key === null) {
            http_response_code(400);
            echo json_encode(['error' => 'key is required']);
            exit;
        }
        
        if ($delete) {
            $redis->del($key);
            echo json_encode(['ok' => true, 'action' => 'deleted']);
        } else {
            if ($value === null) {
                http_response_code(400);
                echo json_encode(['error' => 'value is required for set operation']);
                exit;
            }
            $redis->set($key, $value);
            echo json_encode(['ok' => true, 'action' => 'set']);
        }
        exit;
    }

    if ($method === 'GET') {
        // Handle clear all action
        if (isset($_GET['action']) && $_GET['action'] === 'clear_all') {
            $keys = $redis->keys('*');
            if (!empty($keys)) {
                $redis->del($keys);
                echo json_encode(['ok' => true, 'deleted_count' => count($keys)]);
            } else {
                echo json_encode(['ok' => true, 'deleted_count' => 0]);
            }
            exit;
        }
        
        $key = isset($_GET['key']) ? (string)$_GET['key'] : null;
        if ($key === null) {
            http_response_code(400);
            echo json_encode(['error' => 'key is required']);
            exit;
        }
        $value = $redis->get($key);
        echo json_encode(['key' => $key, 'value' => $value]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Redis error', 'message' => $e->getMessage()]);
}




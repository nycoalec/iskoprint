<?php
require_once '../config/database.php';
require_once '../auth.php';

header('Content-Type: application/json');

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$currentUser = $auth->getCurrentUser();
$userId = $currentUser['user_id'];

$database = new Database();
$conn = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Get all orders for the current user
            $stmt = $conn->prepare("
                SELECT order_id, service_type, subject, amount, status, created_at, paid_at, paid_via
                FROM user_orders 
                WHERE user_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format orders to match frontend expectations
            $formattedOrders = array_map(function($order) {
                // Format dates to ISO string format
                $createdAt = $order['created_at'] ? date('c', strtotime($order['created_at'])) : null;
                $paidAt = $order['paid_at'] ? date('c', strtotime($order['paid_at'])) : null;
                
                return [
                    'id' => $order['order_id'],
                    'serviceType' => $order['service_type'],
                    'subject' => $order['subject'],
                    'amount' => floatval($order['amount']),
                    'status' => $order['status'],
                    'createdAt' => $createdAt,
                    'paidAt' => $paidAt,
                    'paidVia' => $order['paid_via']
                ];
            }, $orders);
            
            echo json_encode(['success' => true, 'orders' => $formattedOrders]);
            break;
            
        case 'POST':
            // Create a new order
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['serviceType']) || !isset($data['subject']) || !isset($data['amount'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }
            
            $serviceType = $data['serviceType'];
            $subject = $data['subject'];
            $amount = floatval($data['amount']);
            $orderId = 'ORD-' . time() . '-' . rand(1000, 9999);
            
            $stmt = $conn->prepare("
                INSERT INTO user_orders (user_id, order_id, service_type, subject, amount, status, created_at)
                VALUES (?, ?, ?, ?, ?, 'Unpaid', NOW())
            ");
            
            $result = $stmt->execute([$userId, $orderId, $serviceType, $subject, $amount]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Order created successfully',
                    'order' => [
                        'id' => $orderId,
                        'serviceType' => $serviceType,
                        'subject' => $subject,
                        'amount' => $amount,
                        'status' => 'Unpaid',
                        'createdAt' => date('Y-m-d\TH:i:s\Z')
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create order']);
            }
            break;
            
        case 'PUT':
            // Mark orders as paid
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['orderIds']) || !is_array($data['orderIds'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Missing order IDs']);
                exit;
            }
            
            $orderIds = $data['orderIds'];
            $paidVia = $data['paidVia'] ?? 'Manual';
            $transactionId = $data['transactionId'] ?? null;
            
            // Create placeholders for IN clause
            $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
            
            // Update orders to paid status
            $stmt = $conn->prepare("
                UPDATE user_orders 
                SET status = 'Paid', 
                    paid_at = NOW(), 
                    paid_via = ?
                WHERE user_id = ? 
                AND order_id IN ($placeholders)
                AND status = 'Unpaid'
            ");
            
            $params = array_merge([$paidVia, $userId], $orderIds);
            $result = $stmt->execute($params);
            
            if ($result) {
                $affectedRows = $stmt->rowCount();
                
                // Record payment in payments table if transaction ID provided
                if ($transactionId && $affectedRows > 0) {
                    // Get total amount of paid orders
                    $stmt2 = $conn->prepare("
                        SELECT SUM(amount) as total 
                        FROM user_orders 
                        WHERE user_id = ? 
                        AND order_id IN ($placeholders)
                    ");
                    $stmt2->execute(array_merge([$userId], $orderIds));
                    $total = $stmt2->fetch(PDO::FETCH_ASSOC)['total'];
                    
                    // Insert payment record
                    $stmt3 = $conn->prepare("
                        INSERT INTO payments (order_id, transaction_id, payment_method, amount, currency, status, created_at)
                        VALUES (?, ?, ?, ?, 'PHP', 'completed', NOW())
                    ");
                    // Use first order ID as reference
                    $stmt3->execute([$orderIds[0], $transactionId, $paidVia, $total]);
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => "Marked $affectedRows order(s) as paid",
                    'affectedRows' => $affectedRows
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update orders']);
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>


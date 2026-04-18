<?php

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Notification.php';

class NotificationController extends Controller {
    private $notificationModel;

    public function __construct() {
        // Ensure session is started for API calls
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->notificationModel = new Notification();
    }

    /**
     * Display full notification page
     */
    public function index() {
        // Check if user is logged in
        if (!isset($_SESSION['admin_id'])) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/login');
            exit;
        }

        $recipientType = 'admin';
        $recipientId = $_SESSION['admin_id'];

        // Get filter parameters
        $filters = [];
        if (isset($_GET['is_read']) && $_GET['is_read'] !== '') {
            $filters['is_read'] = $_GET['is_read'] === '1' ? 1 : 0;
        }
        if (isset($_GET['type']) && $_GET['type'] !== '') {
            $filters['type'] = $_GET['type'];
        }
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $filters['search'] = $_GET['search'];
        }

        // Pagination
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = 20;

        // Get notifications
        $result = $this->notificationModel->getAll($recipientType, $recipientId, $filters, $page, $perPage);
        
        // Get unread count
        $unreadCount = $this->notificationModel->getUnreadCount($recipientType, $recipientId);

        $this->view('notifications/index', [
            'title' => 'Notifications',
            'notifications' => $result['notifications'],
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'total_pages' => $result['total_pages'],
            'unread_count' => $unreadCount,
            'filters' => $filters
        ]);
    }

    /**
     * AJAX endpoint: Get unread notification count
     * Returns JSON
     */
    public function getUnreadCount() {
        // Start output buffering to catch any PHP errors
        ob_start();
        
        // Check if user is logged in
        if (!isset($_SESSION['admin_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        try {
            $recipientType = 'admin';
            $recipientId = $_SESSION['admin_id'];

            $count = $this->notificationModel->getUnreadCount($recipientType, $recipientId);

            $this->jsonResponse([
                'success' => true,
                'count' => $count
            ]);
        } catch (Throwable $e) {
            error_log('NotificationController error: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint: Get recent notifications for dropdown
     * Returns JSON
     */
    public function getRecent() {
        // Start output buffering to catch any PHP errors
        ob_start();
        
        // Check if user is logged in
        if (!isset($_SESSION['admin_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        try {
            $recipientType = 'admin';
            $recipientId = $_SESSION['admin_id'];
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

            $notifications = $this->notificationModel->getRecent($recipientType, $recipientId, $limit);

            // Add relative time and icon to each notification
            foreach ($notifications as &$notification) {
                $notification['relative_time'] = Notification::getRelativeTime($notification['created_at']);
                $notification['icon'] = Notification::getTypeIcon($notification['type']);
                $notification['color'] = Notification::getTypeColor($notification['type']);
            }

            $this->jsonResponse([
                'success' => true,
                'notifications' => $notifications
            ]);
        } catch (Throwable $e) {
            error_log('NotificationController error: ' . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint: Mark single notification as read
     * Returns JSON
     */
    public function markAsRead($id) {
        // Check if user is logged in
        if (!isset($_SESSION['admin_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $recipientId = $_SESSION['admin_id'];
        
        $success = $this->notificationModel->markAsRead($id, $recipientId);

        if ($success) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * AJAX endpoint: Mark all notifications as read
     * Returns JSON
     */
    public function markAllAsRead() {
        // Check if user is logged in
        if (!isset($_SESSION['admin_id'])) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $recipientType = 'admin';
        $recipientId = $_SESSION['admin_id'];

        $success = $this->notificationModel->markAllAsRead($recipientType, $recipientId);

        if ($success) {
            $this->jsonResponse([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } else {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Helper method to send JSON response
     */
    protected function jsonResponse($data, $statusCode = 200) {
        // Clean any previous output (including PHP errors)
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

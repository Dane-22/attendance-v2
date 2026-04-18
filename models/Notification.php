<?php

require_once __DIR__ . '/../core/Model.php';

class Notification extends Model {
    protected $table = 'notifications';

    /**
     * Create a new notification
     * @param array $data Notification data
     * @return bool Success status
     */
    public function create($data) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  (recipient_type, recipient_id, type, title, message, link, is_read, created_at) 
                  VALUES (:recipient_type, :recipient_id, :type, :title, :message, :link, :is_read, NOW())';
        
        $stmt = $this->db->prepare($query);
        
        $recipientType = $data['recipient_type'] ?? 'admin';
        $recipientId = $data['recipient_id'];
        $type = $data['type'];
        $title = $data['title'];
        $message = $data['message'];
        $link = $data['link'] ?? null;
        $isRead = $data['is_read'] ?? false;
        
        $stmt->bindParam(':recipient_type', $recipientType);
        $stmt->bindParam(':recipient_id', $recipientId, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':link', $link);
        $stmt->bindParam(':is_read', $isRead, PDO::PARAM_BOOL);
        
        return $stmt->execute();
    }

    /**
     * Get unread notification count for a recipient
     * @param string $recipientType 'admin' or 'employee'
     * @param int $recipientId Recipient ID
     * @return int Count of unread notifications
     */
    public function getUnreadCount($recipientType, $recipientId) {
        $query = 'SELECT COUNT(*) FROM ' . $this->table . ' 
                  WHERE recipient_type = :recipient_type 
                  AND recipient_id = :recipient_id 
                  AND is_read = FALSE';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':recipient_type', $recipientType);
        $stmt->bindParam(':recipient_id', $recipientId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get recent notifications for dropdown
     * @param string $recipientType 'admin' or 'employee'
     * @param int $recipientId Recipient ID
     * @param int $limit Number of notifications to return
     * @return array Notifications
     */
    public function getRecent($recipientType, $recipientId, $limit = 10) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE recipient_type = :recipient_type 
                  AND recipient_id = :recipient_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':recipient_type', $recipientType);
        $stmt->bindParam(':recipient_id', $recipientId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Get all notifications with optional filters
     * @param string $recipientType 'admin' or 'employee'
     * @param int $recipientId Recipient ID
     * @param array $filters Optional filters (is_read, type, search)
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array Notifications with pagination info
     */
    public function getAll($recipientType, $recipientId, $filters = [], $page = 1, $perPage = 20) {
        $where = ['recipient_type = :recipient_type', 'recipient_id = :recipient_id'];
        $params = [
            ':recipient_type' => $recipientType,
            ':recipient_id' => $recipientId
        ];
        
        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            $where[] = 'is_read = :is_read';
            $params[':is_read'] = $filters['is_read'];
        }
        
        if (isset($filters['type']) && $filters['type'] !== '') {
            $where[] = 'type = :type';
            $params[':type'] = $filters['type'];
        }
        
        if (isset($filters['search']) && $filters['search'] !== '') {
            $where[] = '(title LIKE :search OR message LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Get total count
        $countQuery = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE ' . $whereClause;
        $countStmt = $this->db->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetchColumn();
        
        // Get notifications
        $offset = ($page - 1) * $perPage;
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE ' . $whereClause . ' 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset';
        
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $notifications = $stmt->fetchAll();
        
        return [
            'notifications' => $notifications,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Mark a single notification as read
     * @param int $id Notification ID
     * @param int $recipientId Recipient ID (for security)
     * @return bool Success status
     */
    public function markAsRead($id, $recipientId) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET is_read = TRUE, read_at = NOW() 
                  WHERE id = :id AND recipient_id = :recipient_id';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':recipient_id', $recipientId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Mark all notifications as read for a recipient
     * @param string $recipientType 'admin' or 'employee'
     * @param int $recipientId Recipient ID
     * @return bool Success status
     */
    public function markAllAsRead($recipientType, $recipientId) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET is_read = TRUE, read_at = NOW() 
                  WHERE recipient_type = :recipient_type 
                  AND recipient_id = :recipient_id 
                  AND is_read = FALSE';
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':recipient_type', $recipientType);
        $stmt->bindParam(':recipient_id', $recipientId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Get relative time string
     * @param string $datetime MySQL datetime
     * @return string Relative time (e.g., "2 minutes ago")
     */
    public static function getRelativeTime($datetime) {
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $time);
        }
    }

    /**
     * Get icon class based on notification type
     * @param string $type Notification type
     * @return string Font Awesome icon class
     */
    public static function getTypeIcon($type) {
        $icons = [
            'attendance' => 'fa-clock',
            'payroll' => 'fa-dollar-sign',
            'system' => 'fa-cog',
            'leave' => 'fa-calendar-times',
            'default' => 'fa-bell'
        ];
        
        return $icons[$type] ?? $icons['default'];
    }

    /**
     * Get color class based on notification type
     * @param string $type Notification type
     * @return string CSS color class
     */
    public static function getTypeColor($type) {
        $colors = [
            'attendance' => 'text-blue-500',
            'payroll' => 'text-green-500',
            'system' => 'text-gray-500',
            'leave' => 'text-orange-500',
            'default' => 'text-gray-400'
        ];
        
        return $colors[$type] ?? $colors['default'];
    }
}

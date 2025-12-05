<?php
/**
 * Lesson Model
 * Handles all lesson-related database operations
 */
namespace Models;
// models/Lesson.php

use PDO;

class Lesson {
    private $conn;
    private $table = 'lessons';

    public function __construct() {
        // Đảm bảo bạn đã có file Database.php chuẩn
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả bài học của một khóa (Sắp xếp theo thứ tự học)
     */
    public function getByCourse($courseId) {
        $query = "SELECT l.*, 
                  (SELECT COUNT(*) FROM materials m WHERE m.lesson_id = l.id) as material_count
                  FROM {$this->table} l
                  WHERE l.course_id = :course_id
                  ORDER BY l.`order` ASC"; // Chú ý dấu huyền ở chữ order

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết 1 bài học (Kèm thông tin khóa học để check quyền)
     */
    public function getById($id) {
        $query = "SELECT l.*, c.title as course_title, c.instructor_id
                  FROM {$this->table} l
                  LEFT JOIN courses c ON l.course_id = c.id
                  WHERE l.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Tạo bài học mới (Tự động tính số thứ tự tiếp theo)
     */
    public function create($data) {
        // 1. Tự động tính Order: Lấy order lớn nhất hiện tại + 1
        $orderQuery = "SELECT COALESCE(MAX(`order`), 0) + 1 FROM {$this->table} WHERE course_id = :course_id";
        $orderStmt = $this->conn->prepare($orderQuery);
        $orderStmt->bindParam(':course_id', $data['course_id'], PDO::PARAM_INT);
        $orderStmt->execute();

        // Nếu người dùng nhập order thì lấy, không thì lấy số tự động tính
        $nextOrder = $data['order'] ?? $orderStmt->fetchColumn();

        // 2. Insert
        $query = "INSERT INTO {$this->table} (course_id, title, content, video_url, `order`) 
                  VALUES (:course_id, :title, :content, :video_url, :order)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $data['course_id'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':content', $data['content']);
        $stmt->bindParam(':video_url', $data['video_url']);
        $stmt->bindParam(':order', $nextOrder, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Cập nhật bài học
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['title', 'content', 'video_url', 'order'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                // Xử lý riêng chữ 'order' vì nó là từ khóa SQL
                $fieldName = $field === 'order' ? '`order`' : $field;
                $fields[] = "{$fieldName} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Xóa bài học
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đếm số bài học trong khóa
     */
    public function countByCourse($courseId) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sắp xếp lại thứ tự bài học (Dùng Transaction để an toàn)
     * $lessonOrders = [id_bai_hoc => so_thu_tu_moi, ...]
     */
    public function reorder($courseId, $lessonOrders) {
        try {
            $this->conn->beginTransaction(); // Bắt đầu giao dịch

            foreach ($lessonOrders as $lessonId => $order) {
                $query = "UPDATE {$this->table} SET `order` = :order WHERE id = :id AND course_id = :course_id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':order', $order, PDO::PARAM_INT);
                $stmt->bindParam(':id', $lessonId, PDO::PARAM_INT);
                $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
                $stmt->execute();
            }

            $this->conn->commit(); // Lưu thay đổi
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack(); // Hoàn tác nếu lỗi
            return false;
        }
    }

    /**
     * Lấy bài học TIẾP THEO (Cho nút 'Next Lesson')
     */
    public function getNextLesson($courseId, $currentOrder) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE course_id = :course_id AND `order` > :current_order
                  ORDER BY `order` ASC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':current_order', $currentOrder, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lấy bài học TRƯỚC ĐÓ (Cho nút 'Previous Lesson')
     */
    public function getPreviousLesson($courseId, $currentOrder) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE course_id = :course_id AND `order` < :current_order
                  ORDER BY `order` DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':current_order', $currentOrder, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
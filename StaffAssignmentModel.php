<?php
require_once 'includes/conn.php';

class StaffAssignmentModel
{
    private $table_name = "staffassignments";
    private $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'root', '', 'hospitaldb');
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    public function getAllStaffAssignments() {
        $query = "SELECT * FROM " . $this->table_name;
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getStaffAssignmentById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM staffassignments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staffassignment = $result->fetch_assoc();
        $stmt->close();
        return $staffassignment;
    }

   // Add this simple conflict check method
public function hasScheduleConflict($staff_id, $date_time, $shift_length, $current_id = null) {
    $start_time = $date_time;
    $end_time = date('Y-m-d H:i:s', strtotime($date_time . ' + ' . $shift_length . ' hours'));
    
    $sql = "SELECT COUNT(*) as count 
            FROM staffassignments 
            WHERE staff_id = ? 
            AND (
                (date_time < ? AND DATE_ADD(date_time, INTERVAL shift_length HOUR) > ?)
                OR (date_time BETWEEN ? AND ?)
            )";
    
    if ($current_id) {
        $sql .= " AND id != ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssi", $staff_id, $end_time, $start_time, $start_time, $end_time, $current_id);
    } else {
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issss", $staff_id, $end_time, $start_time, $start_time, $end_time);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

// Modify createStaffAssignment to use the conflict check
public function createStaffAssignment($staff_id, $assignment_id, $date_time, $shift_length) {
    if ($this->hasScheduleConflict($staff_id, $date_time, $shift_length)) {
        return "Error: This staff member already has an assignment during this time period.";
    }

    $query = "INSERT INTO " . $this->table_name . " (staff_id, assignment_id, date_time, shift_length) VALUES (?, ?, ?, ?)";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("iisi", $staff_id, $assignment_id, $date_time, $shift_length);
    return $stmt->execute();
}

// Modify updateStaffAssignment to use the conflict check
public function updateStaffAssignment($id, $staff_id, $assignment_id, $date_time, $shift_length) {
    if ($this->hasScheduleConflict($staff_id, $date_time, $shift_length, $id)) {
        return "Error: This staff member already has an assignment during this time period.";
    }

    $query = "UPDATE " . $this->table_name . " SET staff_id = ?, assignment_id = ?, date_time = ?, shift_length = ? WHERE id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("iisii", $staff_id, $assignment_id, $date_time, $shift_length, $id);
    return $stmt->execute();
}

    public function deleteStaffAssignment($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

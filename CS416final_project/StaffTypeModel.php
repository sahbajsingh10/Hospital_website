<?php
require_once 'includes/conn.php';

class StaffTypeModel
{
    private $table_name = "stafftypes";
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllStaffTypes()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaffTypeById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createStaffType($staff_type, $take_appointment)
    {
        $query = "INSERT INTO " . $this->table_name . " (staff_type, take_appointment) VALUES (:staff_type, :take_appointment)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":staff_type", $staff_type);
        $stmt->bindParam(":take_appointment", $take_appointment);
        return $stmt->execute();
    }

    public function getStaffTypeByName($staff_type) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE staff_type = :staff_type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_type', $staff_type);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isStaffTypeReferenced($staff_type) {
        $query = "SELECT COUNT(*) FROM staffprofiles WHERE staff_profile_type = :staff_type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':staff_type', $staff_type);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function updateStaffType($id, $staff_type, $take_appointment)
{
    try {
        // First update stafftypes
        $query = "UPDATE " . $this->table_name . " 
                 SET staff_type = :staff_type, 
                     take_appointment = :take_appointment 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":staff_type", $staff_type);
        $stmt->bindParam(":take_appointment", $take_appointment);
        $stmt->bindParam(":id", $id);
        
        $result1 = $stmt->execute();
        
        // Then update staffprofiles
        $query = "UPDATE staffprofiles 
                 SET staff_profile_type = :new_type 
                 WHERE staff_profile_type = (
                     SELECT staff_type FROM " . $this->table_name . " 
                     WHERE id = :id
                 )";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":new_type", $staff_type);
        $stmt->bindParam(":id", $id);
        $result2 = $stmt->execute();

        error_log("Update results - stafftypes: " . ($result1 ? 'true' : 'false') . 
                 ", staffprofiles: " . ($result2 ? 'true' : 'false'));
                 
        return ($result1 && $result2);

    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        return false;
    }
}

    public function deleteStaffType($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
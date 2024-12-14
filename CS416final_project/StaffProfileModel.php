<?php
require_once 'includes/conn.php';

class StaffProfileModel
{
    private $table_name = "staffprofiles";
    private $conn;

    public function __construct()
    {
        global $conn;  // Use the global connection variable from conn.php
        $this->conn = $conn;
    }

    public function getAllStaffProfiles()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStaffProfileById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Specify the data type
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStaffTypeByUserId($user_id)
    {
        $query = "SELECT staff_profile_type FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createStaffProfile($user_id, $first_name, $last_name, $staff_profile_type)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, first_name, last_name, staff_profile_type) VALUES (:user_id, :first_name, :last_name, :staff_profile_type)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":staff_profile_type", $staff_profile_type);
        return $stmt->execute();
    }

    public function updateStaffProfile($staffprofile_id, $user_id, $first_name, $last_name, $staff_profile_type) {
        $sql = "UPDATE staffprofiles SET user_id = :user_id, first_name = :first_name, last_name = :last_name, staff_profile_type = :staff_profile_type WHERE id = :staffprofile_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':staff_profile_type', $staff_profile_type);
        $stmt->bindParam(':staffprofile_id', $staffprofile_id);
        return $stmt->execute();
    }
    
    public function updateStaffProfileTypeByUserID($user_id, $staff_profile_type) {
        $sql = "UPDATE staffprofiles SET staff_profile_type = :staff_profile_type WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':staff_profile_type', $staff_profile_type);
        return $stmt->execute();
    }

    public function deleteStaffProfile($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Specify the data type
        return $stmt->execute();
    }
}

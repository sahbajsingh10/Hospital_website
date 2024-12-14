<?php
require_once 'includes/conn.php';

class PatientProfileModel
{
    private $table_name = "patientprofiles";
    private $conn;

    public function __construct()
    {
        global $conn;  // Use the global connection variable from conn.php
        $this->conn = $conn;
    }

    public function getAllPatientProfiles()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientProfileById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Specify the data type
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPatientProfile($user_id, $first_name, $last_name)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, first_name, last_name) VALUES (:user_id, :first_name, :last_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        return $stmt->execute();
    }

    public function updatePatientProfile($id, $user_id, $first_name, $last_name)
    {
        $query = "UPDATE " . $this->table_name . " SET user_id = :user_id, first_name = :first_name, last_name = :last_name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Specify the data type
        return $stmt->execute();
    }

    public function deletePatientProfile($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Specify the data type
        return $stmt->execute();
    }
}

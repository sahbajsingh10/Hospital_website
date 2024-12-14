<?php
require_once 'includes/conn.php';

class AppointmentTypeModel
{
    private $table_name = "appointmenttypes";
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function getAllAppointmentTypes()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentTypeById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createAppointmentType($appointment_name)
    {
        $query = "INSERT INTO " . $this->table_name . " (appointment_name) VALUES (:appointment_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":appointment_name", $appointment_name);
        return $stmt->execute();
    }

    public function updateAppointmentType($id, $appointment_name)
    {
        try {
            $this->conn->beginTransaction();

            // Update the appointment type
            $query = "UPDATE " . $this->table_name . " 
                     SET appointment_name = :appointment_name 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":appointment_name", $appointment_name);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error updating appointment type: " . $e->getMessage());
            return false;
        }
    }

    public function deleteAppointmentType($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getAppointmentTypeByName($name) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE appointment_name = :name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error checking appointment type name: " . $e->getMessage());
            return false;
        }
    }

    public function isAppointmentTypeReferenced($name) {
        try {
            $query = "SELECT COUNT(*) FROM appointments WHERE appointment_type = :name";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->execute();
            
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error checking appointment type references: " . $e->getMessage());
            return false;
        }
    }
}
<?php
require_once 'includes/conn.php';

class AssignmentTypeModel
{
    private $table_name = "assignmenttypes";
    private $conn;

    public function __construct()
    {
        global $conn;  // Use the global connection variable from conn.php
        $this->conn = $conn;
    }

    public function getAllAssignmentTypes()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignmentTypeById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createAssignmentType($assignment_name)
    {
        $query = "INSERT INTO " . $this->table_name . " (assignment_name) VALUES (:assignment_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":assignment_name", $assignment_name);
        return $stmt->execute();
    }

    public function updateAssignmentType($id, $assignment_name)
    {
        // Check if the new name already exists for a different ID
        $query = "SELECT COUNT(*) FROM " . $this->table_name . 
                " WHERE assignment_name = :assignment_name AND id != :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":assignment_name", $assignment_name);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("An assignment type with this name already exists.");
        }

        $query = "UPDATE " . $this->table_name . " SET assignment_name = :assignment_name WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":assignment_name", $assignment_name);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isAssignmentTypeInUse($id)
    {
        $query = "SELECT COUNT(*) FROM staffassignments WHERE assignment_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function deleteAssignmentType($id)
    {
        // First check if the assignment type is in use
        if ($this->isAssignmentTypeInUse($id)) {
            throw new Exception("Cannot delete this assignment type as it is currently in use.");
        }
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function assignmentTypeExists($assignment_name)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE assignment_name = :assignment_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":assignment_name", $assignment_name);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}
<?php
require_once 'includes/conn.php';
class AppointmentModel
{
    private $db;

    public function __construct()
    {
        // Database connection
        $this->db = new mysqli('localhost', 'root', '', 'hospitaldb');
        
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function getAllAppointments()
    {
        $query = "SELECT * FROM appointments";
        $result = $this->db->query($query);
        $appointments = [];

        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        return $appointments;
    }

    public function getAppointmentById($id)
    {
        $query = "SELECT * FROM appointments WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createAppointment($patient_id, $staff_id, $appointment_type, $date_time, $appointment_status)
    {
        $query = "INSERT INTO appointments (patient_id, staff_id, appointment_type, date_time, appointment_status)
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("issss", $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status);
        
        if ($stmt->execute()) {
            return "Appointment created successfully.";
        } else {
            throw new Exception("Error creating appointment.");
        }
    }

    public function updateAppointment($id, $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status)
    {
        $query = "UPDATE appointments SET patient_id = ?, staff_id = ?, appointment_type = ?, date_time = ?, appointment_status = ?
                  WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("iisssi", $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status, $id);
        
        if ($stmt->execute()) {
            return "Appointment updated successfully.";
        } else {
            throw new Exception("Error updating appointment.");
        }
    }

    public function updateStatus($id, $status)
    {
        $query = "UPDATE appointments SET appointment_status = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            return "Appointment status updated.";
        } else {
            throw new Exception("Error updating status.");
        }
    }

    public function deleteAppointment($id)
    {
        $query = "DELETE FROM appointments WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return "Appointment deleted successfully.";
        } else {
            throw new Exception("Error deleting appointment.");
        }
    }
}
?>
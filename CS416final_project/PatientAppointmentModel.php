<?php
class PatientAppointmentModel
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

    // Method to get appointments for a specific user
    public function getUserAppointments($userId)
    {
        $query = "SELECT * FROM appointments WHERE patient_id = ? OR staff_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $appointments = [];
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        return $appointments;
    }

    public function getDoctorName($staffId)
    {
        $query = "SELECT first_name, last_name FROM staffprofiles WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $staffId);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        return $doctor ? $doctor['first_name'] . ' ' . $doctor['last_name'] : 'Unknown';
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
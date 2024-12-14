<?php
require_once 'AppointmentModel.php';

class AppointmentController
{
    private $model;

    public function __construct()
    {
        // Make sure the model is properly instantiated
        $this->model = new AppointmentModel();
        
        // Set the default time zone (adjust this based on your server's time zone)
        date_default_timezone_set('America/New_York');  // Adjust this to your server's time zone
    }

    public function canTakeAppointment($staff_id) {
        $conn = new mysqli('localhost', 'root', '', 'hospitaldb');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
    
        $sql = "SELECT st.take_appointment 
                FROM staffprofiles sp 
                JOIN stafftypes st ON sp.staff_profile_type = st.staff_type 
                WHERE sp.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $conn->close();
        return $row['take_appointment'] === 'yes';
    }
    

    public function listAppointments()
    {
        // Get all appointments from the model
        $appointments = $this->model->getAllAppointments();
        
        // Get the current date and time
        $currentDateTime = new DateTime();  // This will use the server's current time in the default time zone
        
        // Loop through each appointment and check if the status should be updated
        foreach ($appointments as &$appointment) {
            if ($appointment['appointment_status'] == 'scheduled') {
                $appointmentDateTime = new DateTime($appointment['date_time']);
                
                if ($appointmentDateTime < $currentDateTime) {
                    // If the appointment time has passed, update the status to 'completed'
                    $appointment['appointment_status'] = 'completed';
                    $this->model->updateStatus($appointment['id'], 'completed');
                }
            }
        }

        return $appointments;
    }

    public function viewAppointment($id)
    {
        return $this->model->getAppointmentById($id);
    }

    public function addAppointment($patient_id, $staff_id, $appointment_type, $date_time, $appointment_status)
    {
        try {
            // Ensure date_time is in the correct format before storing
            $date_time = $this->formatDateTime($date_time);
            return $this->model->createAppointment($patient_id, $staff_id, $appointment_type, $date_time, $appointment_status);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function updateAppointment($id, $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status)
    {
        try {
            // Ensure date_time is in the correct format before updating
            $date_time = $this->formatDateTime($date_time);
            return $this->model->updateAppointment($id, $patient_id, $staff_id, $appointment_type, $date_time, $appointment_status);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteAppointment($id)
    {
        try {
            $this->model->deleteAppointment($id);
            return "Appointment deleted successfully.";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    // Helper method to format date_time to MySQL DATETIME format
    private function formatDateTime($date_time)
    {
        $dateTimeObj = new DateTime($date_time);
        return $dateTimeObj->format('Y-m-d H:i:s');
    }

    // New method to update the appointment status
    public function updateAppointmentStatus($id, $status)
    {
        try {
            $this->model->updateStatus($id, $status);
            return "Appointment status updated successfully.";
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }
}
?>
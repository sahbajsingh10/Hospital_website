<?php
require_once 'PatientAppointmentModel.php';

class PatientAppointmentController
{
    private $model;

    public function __construct()
    {
        $this->model = new PatientAppointmentModel();
        date_default_timezone_set('America/New_York');
    }

    public function cancelAppointment($id)
    {
        try {
            return $this->model->updateStatus($id, 'canceled');
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function listAppointments($userId)
    {
        $appointments = $this->model->getUserAppointments($userId);
        
        // Get the current date and time
        $currentDateTime = new DateTime();
        
        foreach ($appointments as &$appointment) {
            if ($appointment['appointment_status'] == 'scheduled') {
                $appointmentDateTime = new DateTime($appointment['date_time']);
                if ($appointmentDateTime < $currentDateTime) {
                    $appointment['appointment_status'] = 'completed';
                    $this->model->updateStatus($appointment['id'], 'completed');
                }
            }

            // Fetch the doctor's name
            $appointment['doctor_name'] = $this->getDoctorName($appointment['staff_id']);
        }

        return $appointments;
    }

    public function getDoctorName($staffId)
    {
        return $this->model->getDoctorName($staffId);
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
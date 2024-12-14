<?php
require_once 'StaffAssignmentModel.php';

class StaffAssignmentController {
    private $model;

    public function __construct() {
        $this->model = new StaffAssignmentModel();
    }

    public function listStaffAssignments() {
        return $this->model->getAllStaffAssignments();
    }

    public function viewStaffAssignment($id) {
        return $this->model->getStaffAssignmentById($id);
    }

    public function addStaffAssignment($staff_id, $assignment_id, $date_time, $shift_length) {
        return $this->model->createStaffAssignment($staff_id, $assignment_id, $date_time, $shift_length);
    }

    public function updateStaffAssignment($id, $staff_id, $assignment_id, $date_time, $shift_length) {
        return $this->model->updateStaffAssignment($id, $staff_id, $assignment_id, $date_time, $shift_length);
    }

    public function deleteStaffAssignment($id) {
        return $this->model->deleteStaffAssignment($id);
    }
}
?>

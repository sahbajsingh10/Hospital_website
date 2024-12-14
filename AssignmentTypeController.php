<?php
require_once 'AssignmentTypeModel.php';

class AssignmentTypeController
{
    private $model;

    public function __construct()
    {
        $this->model = new AssignmentTypeModel();
    }

    public function listAssignmentTypes()
    {
        return $this->model->getAllAssignmentTypes();
    }

    public function viewAssignmentType($id)
    {
        return $this->model->getAssignmentTypeById($id);
    }

    public function addAssignmentType($assignment_name)
    {
        return $this->model->createAssignmentType($assignment_name);
    }

    public function updateAssignmentType($id, $assignment_name)
    {
        try {
            return $this->model->updateAssignmentType($id, $assignment_name);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteAssignmentType($id)
    {
        try {
            return $this->model->deleteAssignmentType($id);
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function assignmentTypeExists($assignment_name)
    {
        return $this->model->assignmentTypeExists($assignment_name);
    }
}
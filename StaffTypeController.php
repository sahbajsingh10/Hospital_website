<?php
require_once 'StaffTypeModel.php';

class StaffTypeController
{
    private $model;

    public function __construct()
    {
        $this->model = new StaffTypeModel();
    }

    public function listStaffTypes()
    {
        return $this->model->getAllStaffTypes();
    }

    public function isStaffTypeReferenced($staff_type) {
        return $this->model->isStaffTypeReferenced($staff_type);
    }

    public function viewStaffType($id)
    {
        return $this->model->getStaffTypeById($id);
    }

    public function addStaffType($staff_type, $take_appointment)
    {
        return $this->model->createStaffType($staff_type, $take_appointment);
    }

    public function updateStaffType($id, $staff_type, $take_appointment)
{
    return $this->model->updateStaffType($id, $staff_type, $take_appointment);
}

    public function getStaffTypeByName($staff_type) {
        return $this->model->getStaffTypeByName($staff_type);
    }

    public function deleteStaffType($id)
    {
        return $this->model->deleteStaffType($id);
    }
}
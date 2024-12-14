<?php
require_once 'AppointmentTypeModel.php';

class AppointmentTypeController
{
    private $model;

    public function __construct()
    {
        $this->model = new AppointmentTypeModel();
    }

    public function listAppointmentTypes()
    {
        return $this->model->getAllAppointmentTypes();
    }

    public function viewAppointmentType($id)
    {
        return $this->model->getAppointmentTypeById($id);
    }

    public function addAppointmentType($appointment_name)
    {
        return $this->model->createAppointmentType($appointment_name);
    }

    public function updateAppointmentType($id, $appointment_name)
    {
        return $this->model->updateAppointmentType($id, $appointment_name);
    }

    public function deleteAppointmentType($id)
    {
        return $this->model->deleteAppointmentType($id);
    }

    public function getAppointmentTypeByName($name) {
        return $this->model->getAppointmentTypeByName($name);
    }

    public function isAppointmentTypeReferenced($name) {
        return $this->model->isAppointmentTypeReferenced($name);
    }
}
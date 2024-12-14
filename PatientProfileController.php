<?php
require_once 'PatientProfileModel.php';

class PatientProfileController
{
    private $model;

    public function __construct()
    {
        $this->model = new PatientProfileModel();
    }

    public function listPatientProfiles()
    {
        return $this->model->getAllPatientProfiles();
    }

    public function viewPatientProfile($id)
    {
        return $this->model->getPatientProfileById($id);
    }

    public function addPatientProfile($user_id, $first_name, $last_name)
    {
        return $this->model->createPatientProfile($user_id, $first_name, $last_name);
    }

    public function updatePatientProfile($id, $user_id, $first_name, $last_name)
    {
        return $this->model->updatePatientProfile($id, $user_id, $first_name, $last_name);
    }

    public function deletePatientProfile($id)
    {
        return $this->model->deletePatientProfile($id);
    }
}

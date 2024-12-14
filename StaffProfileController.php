<?php
require_once 'StaffProfileModel.php';

class StaffProfileController
{
    private $model;

    public function __construct()
    {
        $this->model = new StaffProfileModel();
    }

    public function listStaffProfiles()
    {
        return $this->model->getAllStaffProfiles();
    }

    public function viewStaffProfile($id)
    {
        return $this->model->getStaffProfileById($id);
    }

    public function viewStaffTypeForUser($user_id)
    {
        return $this->model->getStaffTypeByUserId($user_id);
    }

    public function addStaffProfile($user_id, $first_name, $last_name, $staff_profile_type)
    {
        return $this->model->createStaffProfile($user_id, $first_name, $last_name, $staff_profile_type);
    }

    public function updateStaffProfile($staffprofile_id, $user_id, $first_name, $last_name, $staff_profile_type) {
        $model = new StaffProfileModel();
        return $model->updateStaffProfile($staffprofile_id, $user_id, $first_name, $last_name, $staff_profile_type);
    }

    public function updateStaffProfileTypeByUserID($user_id, $staff_profile_type) {
        return $this->model->updateStaffProfileTypeByUserID($user_id, $staff_profile_type);
    }
    
    public function deleteStaffProfile($id)
    {
        return $this->model->deleteStaffProfile($id);
    }
}

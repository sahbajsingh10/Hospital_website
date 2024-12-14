<?php
require_once 'UserModel.php';

class UserController {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    public function listUsers() {
        return $this->model->getAllUsers();
    }

    public function viewUser($id) {
        return $this->model->getUserById($id);
    }

    public function addUser($email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image) {
        // Hash the password before passing it to the model
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);  // Hash the password
    
        // Pass the hashed password to the model
        return $this->model->createUser($email, $first_name, $last_name, $hashed_password, $user_type, $phone, $user_image);
    }

    public function updateUser($id, $email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image) {
        return $this->model->updateUser($id, $email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image);
    }
    
    public function deleteUser($id) {
        return $this->model->deleteUser($id);
    }

    public function verifyUser($email, $entered_password) {
        // This method should retrieve the hashed password from the database using the model
        $user = $this->model->getUserByEmail($email);
        if ($user && password_verify($entered_password, $user['user_password'])) {
            return $user;
        }
        return false;
    }
}
?>
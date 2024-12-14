<?php
require_once 'includes/conn.php';

class UserModel
{
    private $table_name = "users";
    private $conn;

    public function __construct()
    {
        global $conn; // Use the global connection variable from conn.php
        $this->conn = $conn;
    }

    public function createUser($email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image)
    {
        try {
            // Prepare SQL query to insert the user data into the database
            $query = "INSERT INTO " . $this->table_name . " (email, first_name, last_name, user_password, user_type, phone, user_image) 
                      VALUES (:email, :first_name, :last_name, :user_password, :user_type, :phone, :user_image)";
            $stmt = $this->conn->prepare($query);
    
            // Bind the parameters to the prepared statement
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':user_password', $user_password);
            $stmt->bindParam(':user_type', $user_type);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':user_image', $user_image, PDO::PARAM_LOB); 
    
            // Execute the query to insert the user data
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function loginUser($email, $entered_password)
    {
        // Fetch user by email
        $user = $this->getUserByEmail($email);

        if ($user) {
            // Log the entered and stored passwords for debugging
            error_log("Entered password: " . $entered_password);
            error_log("Stored password (hashed): " . $user['user_password']);
            
            // Use password_verify to compare the entered password with the hashed password
            if (password_verify($entered_password, $user['user_password'])) {
                // Password is correct, proceed with login
                return $user;
            } else {
                // Invalid password
                error_log("Invalid password");
                return false;
            }
        } else {
            // No user found with that email
            error_log("No user found with email: " . $email);
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            // Query to fetch the user by email
            $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);  // Fetch the user (hashed password should be here)
        } catch (PDOException $e) {
            error_log("Error fetching user by email: " . $e->getMessage());
            return false;
        }
    }

    public function getAllUsers()
    {
        try {
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching users: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Specify the data type
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user by ID: " . $e->getMessage());
            return false;
        }
    }

    public function updateUser($id, $email, $first_name, $last_name, $user_password, $user_type, $phone, $user_image) {
        try {
            // We don't need to check for $current_password here; just use $user_password directly
            $hashed_password = $user_password; // The password has already been hashed (or not) in editUser.php
    
            // SQL query to update the user's data
            $query = "UPDATE " . $this->table_name . " 
                      SET email = :email, first_name = :first_name, last_name = :last_name, 
                          user_password = :user_password, user_type = :user_type, phone = :phone, user_image = :user_image 
                      WHERE id = :id";
    
            $stmt = $this->conn->prepare($query);
    
            // Bind parameters
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":user_password", $hashed_password);
            $stmt->bindParam(":user_type", $user_type);
            $stmt->bindParam(":phone", $phone);
            $stmt->bindParam(":user_image", $user_image);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Bind the user ID
    
            // Execute the query
            if ($stmt->execute()) {
                return true; // Successfully updated
            } else {
                return false; // Failed to execute update
            }
    
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage()); // Log any errors
            return false;
        }
    }
    
    

    public function deleteUser($id)
    {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Specify the data type
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}
?>
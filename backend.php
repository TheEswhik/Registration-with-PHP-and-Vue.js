<?php

/**
 * User Registration
 *
 * This PHP script handles user registration, validates input data, and inserts new users
 * into the database after ensuring the uniqueness of the username and email.
 *
 * PHP version 7.0 or higher
 *
 * @category EswRegister
 * @package  User Registration
 * @author   José Caruajulca
 */

// Start user session
session_name('esw_session');
session_start();

// Include database configuration file
require_once('app/config/db.php');

/**
 * EswRegister Class
 *
 * Provides methods for user registration.
 */
class EswRegister
{
    /**
     * Register a new user
     *
     * @param string $username   Username
     * @param string $name       User's first name
     * @param string $last_name  User's last name
     * @param string $email      User's email address
     * @param string $password   User's password
     *
     * @return void
     */
    public static function registerUser($username, $name, $last_name, $email, $password)
    {
        try {
            $connection = Database::connect();

            if (!$connection) {
                throw new Exception("Database connection error.");
            }

            // Check if username or email already exists
            $existingUserQuery = "SELECT * FROM users WHERE username = :username OR email = :email";
            $existingUserStatement = $connection->prepare($existingUserQuery);
            $existingUserStatement->bindParam(':username', $username, PDO::PARAM_STR);
            $existingUserStatement->bindParam(':email', $email, PDO::PARAM_STR);
            $existingUserStatement->execute();

            if ($existingUserStatement->rowCount() > 0) {
                echo json_encode(['error_message' => 'Username or email already in use.']);
            } else {
                // Hash the password before storing in the database
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user into the database
                $query = "INSERT INTO users (username, name, last_name, email, password) VALUES (:username, :name, :last_name, :email, :password)";
                $statement = $connection->prepare($query);
                $statement->bindParam(':username', $username, PDO::PARAM_STR);
                $statement->bindParam(':name', $name, PDO::PARAM_STR);
                $statement->bindParam(':last_name', $last_name, PDO::PARAM_STR);
                $statement->bindParam(':email', $email, PDO::PARAM_STR);
                $statement->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                $statement->execute();

                echo json_encode(['success' => true, 'success_message' => 'Successful registration, now you can log in.']);
            }
        } catch (PDOException $e) {
            // Log any database query errors
            error_log("Database query error: " . $e->getMessage(), 0);
            echo json_encode(['error_message' => 'Error attempting to register user. Please try again later.']);
        } catch (Exception $e) {
            // Handle other exceptions
            echo json_encode(['error_message' => $e->getMessage()]);
        } finally {
            // Close the database connection
            if ($connection) {
                $connection = null;
            }
        }
    }
}

// Process the registration form if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Filter and get form data
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        // Check if required fields are complete
        if (empty($username) || empty($name) || empty($last_name) || empty($email) || empty($password)) {
            throw new Exception("Please complete all fields.");
        }

        // Check CSRF token validity
        if (!isset($_POST['csrf_token']) || !hash_equals($_POST['csrf_token'], $_SESSION['csrf_token'])) {
            throw new Exception("Invalid CSRF token.");
        }

        // Check password length and complexity
        if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
            throw new Exception("Password must be at least 8 characters long, including letters and numbers.");
        }

        // Call the method to register the user
        EswRegister::registerUser($username, $name, $last_name, $email, $password);
    } catch (Exception $e) {
        // Handle exceptions
        echo json_encode(['error_message' => $e->getMessage()]);
    }
}

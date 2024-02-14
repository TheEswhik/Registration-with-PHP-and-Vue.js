<?php

/**
 * User Session Configuration and CSRF Token Generation
 *
 * This PHP script handles user session configuration, ensuring secure cookie parameters,
 * and generating a CSRF token for protection against CSRF attacks during user registration.
 *
 * PHP version 7.0 or higher
 *
 * @category Configuration
 * @package  Sessions and CSRF Security
 * @author   José Caruajulca
 */

// Set the session name
session_name('esw_session');

// Start or resume the session
session_start();

// Check if there is no CSRF token in the session
if (!isset($_SESSION['csrf_token'])) {
    // Generate a new CSRF token using random bytes
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <div id="app" data-csrf-token="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">User register</h2>
            </div>
            <div class="card-body">
                <form @submit.prevent="formRegister">
                    <input type="hidden" v-model="csrfToken" name="csrf_token">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" v-model="formData.username" required>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" v-model="formData.name" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last name</label>
                        <input type="text" class="form-control" v-model="formData.lastName" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" v-model="formData.email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" v-model="formData.password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">To register</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.6/dist/vue.global.prod.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="app/vue/auth-register.js"></script>
</body>

</html>
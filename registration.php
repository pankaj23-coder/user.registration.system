<?php
// Registration System - Beginner Version

// Start session (for future use if needed)
session_start();

// Set all variables to empty at start
$name = $email = $password = $confirm_password = "";
$name_error = $email_error = $password_error = $confirm_password_error = "";
$success_message = $error_message = "";

// Name of our JSON file to store users
$users_file = "users.json";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get data from form
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Flag to check if everything is valid
    $is_valid = true;
    
    // ----------------------------
    // VALIDATION SECTION
    // ----------------------------
    
    // Check name
    if (empty($name)) {
        $name_error = "Please enter your name";
        $is_valid = false;
    } elseif (strlen($name) < 2) {
        $name_error = "Name must be at least 2 characters";
        $is_valid = false;
    }
    
    // Check email
    if (empty($email)) {
        $email_error = "Please enter your email";
        $is_valid = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Please enter a valid email address";
        $is_valid = false;
    }
    
    // Check password
    if (empty($password)) {
        $password_error = "Please enter a password";
        $is_valid = false;
    } elseif (strlen($password) < 8) {
        $password_error = "Password must be at least 8 characters";
        $is_valid = false;
    }
    
    // Check confirm password
    if (empty($confirm_password)) {
        $confirm_password_error = "Please confirm your password";
        $is_valid = false;
    } elseif ($password !== $confirm_password) {
        $confirm_password_error = "Passwords do not match";
        $is_valid = false;
    }
    
    // If everything is valid, process the registration
    if ($is_valid) {
        try {
            // Create users.json if it doesn't exist
            if (!file_exists($users_file)) {
                file_put_contents($users_file, json_encode([]));
            }
            
            // Read existing users from JSON file
            $users_data = file_get_contents($users_file);
            $users = json_decode($users_data, true);
            
            // Check if email already exists
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    $error_message = "This email is already registered. Please use a different email.";
                    throw new Exception($error_message);
                }
            }
            
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Create new user array
            $new_user = [
                'id' => time() . rand(100, 999), // Simple ID generation
                'name' => $name,
                'email' => $email,
                'password' => $hashed_password,
                'registration_date' => date('Y-m-d H:i:s')
            ];
            
            // Add new user to users array
            $users[] = $new_user;
            
            // Save updated array back to JSON file
            $result = file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
            
            if ($result === false) {
                throw new Exception("Sorry, there was an error saving your data. Please try again.");
            }
            
            // Clear form fields after successful registration
            $name = $email = $password = $confirm_password = "";
            
            // Show success message
            $success_message = "Registration successful! Welcome, " . htmlspecialchars($name) . "!";
            
        } catch (Exception $e) {
            // Show error message if something goes wrong
            $error_message = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration System</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 30px;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4a90e2;
        }
        
        .error {
            color: #ff0000;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #d6e9c6;
        }
        
        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ebccd1;
        }
        
        .btn {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn:hover {
            background-color: #3a80d2;
        }
        
        .instructions {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;+
            .
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #555;
        }
        
        .instructions h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #4a90e2;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Account</h1>
        <p class="subtitle">Please fill in all fields to register</p>
        
        <div class="instructions">
            <h3>Testing Instructions:</h3>
            <p>1. Try submitting with empty fields</p>
            <p>2. Test with invalid email</p>
            <p>3. Try different passwords in the two password fields</p>
            <p>4. Use valid data to register successfully</p>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" 
                       value="<?php echo htmlspecialchars($name); ?>" 
                       placeholder="Enter your full name">
                <?php if (!empty($name_error)): ?>
                    <div class="error"><?php echo $name_error; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($email); ?>" 
                       placeholder="Enter your email">
                <?php if (!empty($email_error)): ?>
                    <div class="error"><?php echo $email_error; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" 
                       value="<?php echo htmlspecialchars($password); ?>" 
                       placeholder="Create a password (min 8 characters)">
                <?php if (!empty($password_error)): ?>
                    <div class="error"><?php echo $password_error; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       value="<?php echo htmlspecialchars($confirm_password); ?>" 
                       placeholder="Confirm your password">
                <?php if (!empty($confirm_password_error)): ?>
                    <div class="error"><?php echo $confirm_password_error; ?></div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <div class="login-link">
            <p>Already have an account? <a href="#">Sign in here</a></p>
        </div>
    </div>
    
    <script>
        // Simple client-side validation
        document.querySelector('form').addEventListener('submit', function(e) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                alert('Passwords do not match. Please check and try again.');
                e.preventDefault();
                document.getElementById('confirm_password').focus();
            }
            
            if (password.length < 8) {
                alert('Password must be at least 8 characters long.');
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>
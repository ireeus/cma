<?php
// Placeholder for user registration logic (to be implemented)
function registerUser($username, $password, $email) {
    include('config.php');
    // Validate the input (add more validation as needed)
    if (empty($username) || empty($password) || empty($email)) {
        return false;
    }

    // Hash the password (use a secure hashing algorithm like password_hash)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Create a new user file
    $filePath = $dir."users/" . $username . ".php";
    
    // Check if the user already exists
    if (file_exists($filePath)) {
        return false; // User already exists
    }

    // Save user information to the file
    $userContent = "<?php: 
/*: 
Username: $username\nPassword: $hashedPassword\nType: $accountLevel\nEmail: $email\n
*/: ";
    if (file_put_contents($filePath, $userContent) !== false) {
        return true; // Registration successful
    }

    return false; // Registration failed
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Register the user
    if (registerUser($username, $password, $email)) {
        // Redirect to login page after successful registration
        header('Location: login.php');
        exit();
    } else {
        $error_message = 'Registration failed. Please try again.';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
        <link rel="icon" type="image/png" href="favicon.png">

    <title>Register</title>
<style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        #login-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            text-align: center;
        }

        ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        li {
            margin-bottom: 10px;
        }

        .error {
            color: #ff0000;
            text-align: center;
            margin: 10px 0;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        #sessionSelect {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #newSessionInput {
            display: none;
        }

        button {
            background-color: #3498db;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #007bb5;
        }
    </style>
</head>
<body>
    <div id="login-container">
        <h2>Register</h2>
        <p id="error-message" class="error"></p>
        <form method="post" action="" onsubmit="return validateForm()">
            <div>
                <p>
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username" required><br>
                </p>
                <p>
                    <label for="password">Password:</label><br>
                    <input type="password" id="password" name="password" required><br>
                </p>
                <p>
                    <label for="confirm-password">Confirm Password:</label><br>
                    <input type="password" id="confirm-password" name="confirm-password" required>
                </p>
                                <p>
                    <label for="confirm-password">Email:</label><br>
                    <input type="email" id="email" name="email" required>
                </p>
                <p>
                    <button type="submit">Register</button>
                </p>
                
                <a href="terms.php"> Terms </a>
            </div>
        </form>
    </div>

    <script>
        function avalidateForm() {
            // Reset error message
            document.getElementById("error-message").innerHTML = "";

            // Retrieve form inputs
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm-password").value;

            // Validate username length
            if (username.length < 6) {
                document.getElementById("error-message").innerHTML = "Username must be at least 6 characters long.";
                return false;
            }

            // Validate password length
            if (password.length < 8) {
                document.getElementById("error-message").innerHTML = "Password must be at least 8 characters long and must contain at least one number and one capital letter.";
                return false;
            }

            // Validate password complexity (at least one number and one capital letter)
            if (!/\d/.test(password) || !/[A-Z]/.test(password)) {
                document.getElementById("error-message").innerHTML = "Password must be at least 8 characters long and must contain at least one number and one capital letter.";
                return false;
            }

            // Validate password and confirm password match
            if (password !== confirmPassword) {
                document.getElementById("error-message").innerHTML = "Passwords do not match.";
                return false;
            }

            // Proceed with form submission if all validations pass
            return true;
        }
    </script>
</body>
</html>

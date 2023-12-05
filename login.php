<?php
ini_set('display_errors', 1);
session_start();
$error = '';

if ($conn = new mysqli("localhost", "test", "test", "tracker")) {
    // echo "Connected successfully";
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        $error = 'Username or password is empty';
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $hashed_pass = [sha1('$password')];
        $date = $time = date('Y-m-d H:i:s');

        // Using prepared statements to prevent SQL Injection
        $stmt = $conn->prepare("SELECT password_hash FROM registration WHERE username = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo "Hashed Password from DB: " . $row['password_hash'] . "<br>"; // Debug
            echo "Input Password: " . $password . "<br>"; // Debug
            // Verify the password
            if ($password === $row['password_hash']) {
                // Password is correct
                $_SESSION['email'] = $email; // Note: $email is not defined in this script

                // Insert login record
                $stmt = $conn->prepare("INSERT INTO login (time, date, username) VALUES (?, ?, ?)");
                if ($stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }

                $stmt->bind_param("sss", $time, $date, $username);
                if ($stmt->execute()) {
                    // Redirect to search page
                    header("Location:  http://127.0.0.1:5000");
                    exit();
                } else {
                    echo "Failed to insert login record";
                }
            } else {
                function function_alert($message) 
                {
                    echo "<script>alert('$message');</script>";
                    return false;
                }
                if (function_alert("username or password is incorrect"))
                {
                    return false;
                }
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}

if (!empty($error)) {
    echo $error;
}
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Signup Form</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(pics/background.gif);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        input[type="username"], input[type="password"]{
            width: calc(100% - 20px);
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        .btn:hover {
            opacity: 0.8;
        }
        .form-footer {
            margin-top: 10px;
            text-align: center;
        }
        </style>
    </head>

    <div class="form-container">
        <h2>Login</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <input type="username" name="username" required id="username" placeholder="Enter your username"><br>
            <input type="password" name="password" required id="password" placeholder="Enter your password"><br>
            <a href="#">Forgot password?</a><br>
            <button type="submit" class="btn" name="login">Login</button><br>
            Don't have an account? <a href="registration.php">SignUp</a>
        </form>
    </div>

</body>

<script>

  function reg()
  {
     window.location.href = 'regitration.php';
     return false;
  }

</script>
</html>

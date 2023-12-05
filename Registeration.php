<?php
// session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize the registration array
$registration = array();

// Create connection
if ($conn = new mysqli("localhost", "test", "test", "tracker")){
  echo "connect success     ";
}

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Register functionality
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password'];

      // Automatically save the current date and time
      $date = $time = date('Y-m-d H:i:s');

      // Insert data into the database
      if ($sql = "INSERT INTO registration (email, username, password_hash, date, time) VALUES ('$email', '$username', SHA('$password'), '$date', '$time')")
        echo " inserted";

      if ($conn->query($sql) === TRUE) {
          echo "Registered successfully!";
          header("location: http://127.0.0.1:5000");
      } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
      }
    }
}

// Close the database connection
$conn->close();
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
    input[type="email"], input[type="password"], input[type="username"]{
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
<body>

<div class="form-container">
  <h2>Registerartion</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input name="email" type="email" placeholder="Enter your email" required>
        <input name="username" type="username" placeholder="Enter your username" required>
        <input type="password" placeholder="Enter your password" required>
        <a href="#">Forgot password?</a>
        <button type="submit" class="btn" name="register">register</button>
        <div class="form-footer">
        Have an exiting account? <a onclick="login()" href="#">SignIn</a>
        </div>
  </form>
</div>

</body>

<script>

  function login()
  {
     window.location.href = 'login.php';
     return false;
  }

</script>
</html>

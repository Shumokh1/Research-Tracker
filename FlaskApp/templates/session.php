<?php
    $conn = mysqli_connect("localhost", "test", "test", "tracker");
    
    session_start();
    if (isset($_SESSION['email']))//login_username
    {
        $user_check = $_SESSION['email'];
    }
    else {
        $user_check = "";
    }

    $query = "SELECT email from registration where username = '$user_check'";
    $ses_sql = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($ses_sql);
    if (isset($_SESSION['email']))
    {
        $login_session = $row['email'];
    }
    else {
        $login_session = "";
    }
?>
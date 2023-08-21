<?php
error_reporting(E_ALL ^ E_NOTICE);
if(!isset($_SESSION)) 
    { 
        session_start(); 
    }
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <title> Profile Page </title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'/>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css'>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'>
</head>

<body>
    <div class='container text-center'>
        <h1> Profile Page</h1>
    </div>
    <center>
        <?php
        require 'master.php';
        require_once 'config.php';
        $email = $_SESSION['email'];
        $password = $_SESSION['password'];
        $db = connect_to_mysql();
        if ($db->connect_error) {
            die('Connection failed: ' . $db->connect_error);
        }
        $sql = "SELECT * FROM `User` WHERE `email` = ? AND `password` = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            //make all one echo? is that better for performance?
            //Style it?
            echo "<strong>Email:</strong> " . $row["email"]."<br>";
            echo "<strong>Name:</strong> " . $row["firstName"]." " .$row["lastName"]."<br>";
            echo "<strong>Password:</strong> " .$row["password"]."<br>";
            echo "<strong>User Type:</strong> " .$row["userType"]."<br>";
            echo "<strong>Enrolled Courses:</strong> " .$row["enrolledCourses"]."<br>";
        }
		//maybe add waitlisted classes?
        ?>
		
    </center>
</body>
</html>

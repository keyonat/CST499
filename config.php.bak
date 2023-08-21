<?php
error_reporting(E_ALL ^ E_NOTICE);
function connect_to_mysql() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'registration_project_vid';

    // Create connection
    $conn = mysqli_connect($host, $user, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}
?>
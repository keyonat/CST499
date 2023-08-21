<?php
    error_reporting(E_ALL ^ E_NOTICE);
    require_once('config.php');
    if(!isset($_SESSION)) 
{ 
    session_start(); 
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Course Registration Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="index.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<div class='container text-center'>
    <h1> Add Courses To Catalog (Admin Only) </h1>
</div>
    <?php include 'master.php';?>
    <div class="container">
        <form class="padding-top" method="post">
            <div class="form-group">
                <label for="inputCourseName">Course Name:</label>
                <input type="text" class="form-control" id="inputCourseName" placeholder="Enter Course Name" name="courseName">
            </div>
            <div class="form-group">
                <label for="inputSemester">Semester:</label>
                <input type="text" class="form-control" id="inputSemester" placeholder="Enter Semester" name="semester">
            </div>
            <div class="form-group">
                <label for="inputMaxEnrollment">Max Enrollment:</label>
                <input type="number" class="form-control" id="inputMaxEnrollment" placeholder="Enter Max Enrollment" name="maxEnrollment">
            </div>
            <div class="form-group">
                <label for="selectCourseToRemove">Select Course To Remove:</label>
                <select class="form-control" id="selectCourseToRemove" name="courseToRemove">
                    <option value="">Select Course</option>
                    <?php
                    $conn = connect_to_mysql();
                    $result = mysqli_query($conn, "SELECT * FROM Course");
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<option value="'.$row['courseID'].'">'.$row['courseName'].' - '.$row['semester'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="addCourse">Add Course</button>
            <button type="submit" class="btn btn-danger" name="removeCourse">Remove Course</button>
			<?php 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["removeCourse"])) {
            $courseId = test_input($_POST["courseToRemove"]);
			$sql = "DELETE FROM `Course` WHERE `courseID` = '$courseId'";
            $conn = connect_to_mysql();
            if(mysqli_query($conn, $sql)) {
                echo "<br><br><div class='alert alert-success'>Record deleted successfully.</div>";
				header("Location: courseRegistration.php"); // To avoid allowing refresh to repeat the same operation.
            } else {
                echo "<br><br><div class='alert alert-danger'>Error: " . $sql . "<br>" . mysqli_error($conn) . "</div>";
            }
        } elseif (isset($_POST["addCourse"])) {
            $courseName = test_input($_POST["courseName"]);
            $semester = test_input($_POST["semester"]);
            $maxEnrollment = test_input($_POST["maxEnrollment"]);
            $enrolledStudents = "";
            $numEnrolled = 0;
            $waitingList = 0;

            $sql = "INSERT INTO `Course` (courseName, semester, enrolledStudents, maxEnrollment, numEnrolled, waitingList) 
            VALUES ('$courseName', '$semester', '$enrolledStudents', '$maxEnrollment', '$numEnrolled', '$waitingList')";
            $conn = connect_to_mysql();
            if(mysqli_query($conn, $sql)) {
                echo "<br><br><div class='alert alert-success'>Record inserted successfully.</div>";
				header("Location: courseRegistration.php"); // To avoid allowing refresh to repeat the same operation.

            } else {
                echo "<br><br><div class='alert alert-danger'>Error: " . $sql . "<br>" . mysqli_error($conn) . "</div>";
            }
        }
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
?>

			

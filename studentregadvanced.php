<?php
require_once('config.php');
if(!isset($_SESSION)) 
{ 
    session_start(); 
}

// get the list of courses from the database
$db = connect_to_mysql();
$courses = $db->query('SELECT * FROM Course');

// process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if(isset($_POST['register'])) {
    // get the selected course ID
    $selected_course_id = $_POST['courseID'];

    // get the user's ID from the session
    $user_id = $_SESSION['userID'];

// check if the user is already enrolled in the selected course
	// check if the user is already enrolled in the selected course
	$stmt = $db->prepare('SELECT FIND_IN_SET(?, enrolledStudents) as isEnrolled FROM Course WHERE CourseID = ?');
	$stmt->bind_param('ii', $user_id, $selected_course_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$course = $result->fetch_assoc();
	$isEnrolled = $course['isEnrolled'];
	if ($isEnrolled) {
	  // student is already enrolled in the course, prevent registration
	  echo "You are already enrolled in this course.";
	} else {
	  // add the user to the course's enrolled students list
	  $stmt = $db->prepare("UPDATE Course SET enrolledStudents = CONCAT(enrolledStudents, ',', ?), numEnrolled = numEnrolled + 1 WHERE CourseID = ? AND numEnrolled < maxEnrollment AND waitingList = 0");
	  $stmt->bind_param('si', $user_id, $selected_course_id);
	  $stmt->execute();
	  // check if the course is full
	  $stmt = $db->prepare("SELECT numEnrolled, maxEnrollment FROM Course WHERE CourseID = ?");
	  $stmt->bind_param('i', $selected_course_id);
	  $stmt->execute();
	  $result = $stmt->get_result();
	  $course = $result->fetch_assoc();
	  $numEnrolled = $course['numEnrolled'];
	  $maxEnrollment = $course['maxEnrollment'];

	 if ($numEnrolled >= $maxEnrollment) {
  // add the student to the waitlist
  $stmt = $db->prepare("INSERT INTO Waitlist (CourseID, UserID) VALUES (?, ?)");
  $stmt->bind_param('ii', $selected_course_id, $user_id);
  $stmt->execute();
  echo "This course is full. You have been added to the waitlist.";
  $stmt = $db->prepare("UPDATE Course SET waitingList = waitingList + 1 WHERE CourseID = ?");
  $stmt->bind_param('i', $selected_course_id);
  $stmt->execute();

  // add the course name to the user's enrolled courses list
  $stmt = $db->prepare('SELECT courseName FROM Course WHERE CourseID = ?');
  $stmt->bind_param('i', $selected_course_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $course = $result->fetch_assoc();
  $courseName = $course['courseName'];
  $stmt = $db->prepare('SELECT enrolledCourses FROM User WHERE userID = ?');
  $stmt->bind_param('i', $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $enrolledCourses = $user['enrolledCourses'];
  if (empty($enrolledCourses)) {
    $enrolledCourses = $courseName;
  } else {
    $enrolledCourses .= ',' . $courseName;
  }
  $stmt = $db->prepare('UPDATE User SET enrolledCourses = ? WHERE userID = ?');
  $stmt->bind_param('si', $enrolledCourses, $user_id);
  $stmt->execute();
} else {
  // get the course name based on the selected course id
  $stmt = $db->prepare('SELECT courseName FROM Course WHERE CourseID = ?');
  $stmt->bind_param('i', $selected_course_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $course = $result->fetch_assoc();
  $courseName = $course['courseName'];

  // add the course name to the user's enrolled courses list
  $stmt = $db->prepare('SELECT enrolledCourses FROM User WHERE userID = ?');
  $stmt->bind_param('i', $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();
  $enrolledCourses = $user['enrolledCourses'];
  if (empty($enrolledCourses)) {
    $enrolledCourses = $courseName;
  } else {
    $enrolledCourses .= ',' . $courseName;
  }
  $stmt = $db->prepare('UPDATE User SET enrolledCourses = ? WHERE userID = ?');
  $stmt->bind_param('si', $enrolledCourses, $user_id);
  $stmt->execute();

  // redirect the user to the course registration confirmation page
  header("Location: profile.php");
  exit;
}
}
	}
    else if(isset($_POST['remove'])) {
    // get the selected course name
    $selected_course_name = $_POST['courseName'];
	// get the selected course ID based on the course name
	$stmt = $db->prepare('SELECT CourseID FROM Course WHERE courseName = ?');
	$stmt->bind_param('s', $selected_course_name);
	$stmt->execute();
	$result = $stmt->get_result();
	$course = $result->fetch_assoc();
	$selected_course_id = $course['CourseID'];

    // get the user's ID from the session
    $user_id = $_SESSION['userID'];

    // remove the course from the user's enrolled courses list
    $stmt = $db->prepare('SELECT enrolledCourses FROM User WHERE userID = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $enrolledCourses = $user['enrolledCourses'];
    $enrolledCourses_array = explode(',', $enrolledCourses);
    $new_enrolledCourses_array = array();
    foreach ($enrolledCourses_array as $course) {
      if ($course != $selected_course_name) {
        array_push($new_enrolledCourses_array, $course);
      }
    }
    $new_enrolledCourses = implode(',', $new_enrolledCourses_array);
    $stmt = $db->prepare('UPDATE User SET enrolledCourses = ? WHERE userID = ?');
    $stmt->bind_param('si', $new_enrolledCourses, $user_id);
    $stmt->execute();

    // update the course's enrolled students list and number of enrolled students
	$stmt = $db->prepare("UPDATE Course SET enrolledStudents = TRIM(BOTH ',' FROM REPLACE(CONCAT(',', enrolledStudents, ','), CONCAT(',', ? ,','), ',')), numEnrolled = CASE WHEN waitingList > 0 THEN numEnrolled ELSE GREATEST(numEnrolled - 1, 0) END WHERE CourseID = ?");
	$stmt->bind_param('ii', $user_id, $selected_course_id);
	$stmt->execute();

   // check if the course has any students on the waitlist
	$stmt = $db->prepare("SELECT WaitlistID, UserID FROM Waitlist WHERE CourseID = ? ORDER BY WaitlistID ASC");
	$stmt->bind_param('i', $selected_course_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if ($result->num_rows > 0) {
	  // move the first student on the waitlist into the course
	  $row = $result->fetch_assoc();
	  $waitlist_id = $row['WaitlistID'];
	  $user_id = $row['UserID'];
	  $stmt = $db->prepare("UPDATE Course SET enrolledStudents = CONCAT(enrolledStudents, ', ', ?), numEnrolled = numEnrolled + 1, waitingList = waitingList - 1 WHERE CourseID = ?");
	  $stmt->bind_param('ii', $user_id, $selected_course_id);
	  $stmt->execute();
	  // remove the student from the waitlist
	  $stmt = $db->prepare("DELETE FROM Waitlist WHERE WaitlistID = ?");
	  $stmt->bind_param('i', $waitlist_id);
	  $stmt->execute();
	  // notify the student who was moved from the waitlist to the course
	  $stmt = $db->prepare("SELECT email FROM User WHERE userID = ?");
	  $stmt->bind_param('i', $user_id);
	  $stmt->execute();
	  $result = $stmt->get_result();
	  $row = $result->fetch_assoc();
	  $email = $row['email'];
	  $subject = "You have been moved from the waitlist to the course!";
	  $message = "Dear student,\n\nYou have been moved from the waitlist to the course \"$selected_course_name\".\n\nSincerely,\nThe Course Registration Team";
	  mail($email, $subject, $message);
	}
    // redirect the user to the profile page
    header("Location: profile.php");
	exit;
		}
}
?>
<div class='container text-center'>
  <h1>Course Registration</h1>
  <?php require 'master.php'; ?>
  <form method='post'>
    <div class='form-group'>
      <label for='course-select'>Select a course to register:</label>
      <select class='form-control' id='course-select' name='courseID'>
        <?php while ($course = $courses->fetch_assoc()) { ?>
          <option value='<?php echo $course['courseID']; ?>'><?php echo $course['courseName']; ?></option>
        <?php } ?>
      </select>
    </div>
    <button type='submit' class='btn btn-primary' name='register'>Register</button>
  </form>
  <form method='post'>
    <div class='form-group'>
      <label for='course-select-remove'>Select a course to remove:</label>
      <select class='form-control' id='course-select-remove' name='courseName'>
        <?php
          // get the list of enrolled courses for the user
          $stmt = $db->prepare('SELECT enrolledCourses FROM User WHERE email = ?');
          $stmt->bind_param('s', $_SESSION['email']);
          $stmt->execute();
          $result = $stmt->get_result();
          $user = $result->fetch_assoc();
          $enrolledCourses = $user['enrolledCourses'];
          
          // check if the user has any enrolled courses
          if(!empty($enrolledCourses)) {
            $courses_array = explode(",", $enrolledCourses);
            foreach($courses_array as $course) {
              // get the course ID based on the course name
              $stmt = $db->prepare('SELECT CourseID FROM Course WHERE courseName = ?');
              $stmt->bind_param('s', $course);
              $stmt->execute();
              $result = $stmt->get_result();
              $course_id = $result->fetch_assoc()['CourseID'];

              echo "<option value='" . $course . "' data-course-id='" . $course_id . "'>" . $course . "</option>";
            }
          }
        ?>
      </select>
    </div>
    <input type='hidden' name='courseID' id='courseID'>
    <button type='submit' class='btn btn-danger' name='remove'>Remove Course</button>
  </form>
  <script>
  $(document).ready(function() {
    $('#course-select-remove').change(function() {
      var selectedCourseID = $(this).find(':selected').data('course-id');
      $('#courseID').val(selectedCourseID);
    });
  });
</script>

</div>
<?php require 'footer.php'; ?>
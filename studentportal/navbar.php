<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
$fname = '';
$mname = '';
$lname = '';
$student_name = 'Student';
$profile_photo = 'images/default_profile.png';

if (!empty($enrollment_no)) {
    $conn = new mysqli("localhost", "root", "12345678", "newton_db");
    if (!$conn->connect_error) {
        $stmt = $conn->prepare("SELECT profile, fname, mname, lname FROM student WHERE enrollment_no = ?");
        $stmt->bind_param("s", $enrollment_no);
        $stmt->execute();
        $stmt->bind_result($profile_path, $fname, $mname, $lname);

        if ($stmt->fetch()) {
            $full_name = trim($fname . ' ' . $mname . ' ' . $lname);
            $student_name = $full_name ?: 'Student';
            if (!empty($profile_path)) {
                $encoded_path = implode("/", array_map("rawurlencode", explode("/", $profile_path)));
                $profile_photo = "http://localhost/project_backend/" . $encoded_path;
            }
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Student Portal</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    .sidebar {
      width: 220px;
      background: #093c56;
      height: 100vh;
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      padding-top: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      border-right: 2px solid black;
    }

    .sidebar .back-button {
      width: 100%;
      padding: 14px 20px;
      background: #072f45;
      color: white;
      text-decoration: none;
      text-align: left;
      font-weight: bold;
      border-bottom: 1px solid #000;
    }

    .sidebar .back-button:hover {
      background: #0f4a6e;
    }

    .sidebar .profile-img {
      margin: 20px 0 10px;
      text-align: center;
    }

    .sidebar .profile-img img {
      width: 90px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
      border: 2px solid white;
      display: block;
      margin: 0 auto;
    }

    .sidebar .student-name {
      margin-top: 8px;
      font-size: 15px;
      font-weight: 500;
      color: #e0e0e0;
      text-align: center;
      padding: 0 10px;
    }

    .sidebar a {
      display: block;
      width: 100%;
      padding: 12px 20px;
      color: white;
      text-decoration: none;
      transition: background 0.3s ease;
      font-size: 15px;
      text-align: left;
    }

    .sidebar a:hover {
      background: #0f4a6e;
    }

    .topbar {
      margin-left: 220px;
      background: #f4f4f4;
      height: 60px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 999;
      border-bottom: 2px solid black;
    }

    .topbar .title {
      font-size: 18px;
      font-weight: bold;
      color: #093c56;
      padding-left: 400px;
    }

    .topbar img {
      height: 40px;
      width: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
      border: 2px solid #093c56;
    }

    .logout {
      color: rgb(9, 60, 86);
      text-decoration: none;
      font-weight: bold;
    }

    .logout:hover {
      color: rgb(60, 121, 152);
    }
  </style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div class="title">STUDENT PORTAL</div>
  <div>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
  <a href="javascript:history.back()" class="back-button">⬅ Back</a>

  <div class="profile-img">
    <img src="<?= htmlspecialchars($profile_photo) ?>" alt="Profile Image">
    <div class="student-name"><?= htmlspecialchars($student_name) ?></div>
  </div>

  <a href="dashboard.php">Dashboard</a>
  <a href="student_profile.php">Student Profile</a>
  <a href="fees1.php">Fees</a>
  <a href="exam.php">Marks</a>
  <a href="attendance.php">Attendance</a>
  <a href="leave.php">Leaves</a>
  <a href="documents.php">Documents</a>
</div>

</body>
</html>

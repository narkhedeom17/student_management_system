<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['teacher_loggedin']) || !isset($_SESSION['staff_no'])) {
    header("Location: index.php");
    exit();
}

$student_found = false;
$student_data = null;
$enrollment_no = '';
$student_name = '';
$profile_photo = '';
$show_form = true;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['enrollment_no'])) {
    $enrollment_no = $_POST['enrollment_no'];
    $show_form = false;

    $conn = new mysqli("localhost", "root", "12345678", "newton_db");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $sql = "
        SELECT s.*, c.city_name, a.area_name 
        FROM student s
        LEFT JOIN tbl_city c ON s.city_id = c.city_id
        LEFT JOIN tbl_area a ON s.area_id = a.area_id
        WHERE s.enrollment_no = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $enrollment_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student_data = $result->fetch_assoc();
        $student_found = true;
        $student_name = $student_data['fname'] . ' ' . $student_data['mname'] . ' ' . $student_data['lname'];

        // Photo handling
        if (!empty($student_data['profile'])) {
            $encoded_path = implode("/", array_map("rawurlencode", explode("/", $student_data['profile'])));
            $profile_photo = "http://localhost/project_backend/" . $encoded_path;
        } else {
            $profile_photo = "images/default-profile.png";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Student</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
    }

    .main-content {
      margin-left: 220px;
      padding: 30px;
    }

    .search-form {
      max-width: 500px;
      margin: 30px auto;
      background: white;
      padding: 20px 30px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .search-form h2 {
      text-align: center;
      color: #093c56;
      margin-bottom: 20px;
    }

    .search-form input[type="text"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .search-form button {
      width: 100%;
      padding: 12px;
      background-color: #093c56;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .search-form button:hover {
      background-color: #0f4a6e;
    }

    .top-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    .top-buttons .nav-btn {
      background-color: #093c56;
      color: white;
      padding: 10px 18px;
      text-decoration: none;
      border-radius: 6px;
      font-size: 14px;
      margin-left: 10px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .top-buttons .nav-btn:hover {
      background-color: #0f4a6e;
    }

    .container {
      display: flex;
      max-width: 1200px;
      background: white;
      margin: 20px auto;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    .photo-section {
      flex: 1;
      background: #f9f9f9;
      text-align: center;
      padding: 20px;
      border-right: 1px solid #ddd;
    }

    .photo-section img {
      width: 100px;
      height: 120px;
      border-radius: 10px;
      object-fit: cover;
      border: 2px solid #aaa;
      margin-bottom: 12px;
    }

    .photo-info {
      font-size: 14px;
      color: #333;
    }

    .info-section {
      flex: 3;
      padding: 25px;
    }

    .name-header {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #093c56;
      text-align: center;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px 20px;
      font-size: 14px;
    }

    .field {
      margin-bottom: 5px;
    }

    .label {
      font-weight: bold;
      color: #444;
    }

    .value {
      color: #555;
    }

    .full {
      grid-column: span 3;
    }

    .no-result {
      text-align: center;
      font-size: 18px;
      color: red;
      margin-top: 20px;
    }

    @media (max-width: 768px) {
      .main-content { margin-left: 0; }
      .container { flex-direction: column; }
      .grid { grid-template-columns: 1fr; }
      .top-buttons { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

<div class="main-content">

  <?php if ($show_form): ?>
    <!-- SEARCH FORM -->
    <form method="POST" class="search-form">
      <h2>Search Student by Enrollment No</h2>
      <input type="text" name="enrollment_no" required placeholder="Enter Enrollment Number">
      <button type="submit">Search</button>
    </form>
  <?php elseif ($student_found): ?>
    <!-- TOP BUTTONS -->
    <div class="top-buttons">
      <div>
        <a class="nav-btn" href="exam.php?enrollment_no=<?= urlencode($enrollment_no) ?>"><i class="fa fa-pen"></i> Exam</a>
        <a class="nav-btn" href="leave.php?enrollment_no=<?= urlencode($enrollment_no) ?>"><i class="fa fa-plane"></i> Leave</a>
        <a class="nav-btn" href="student_attendance.php?enrollment_no=<?= urlencode($enrollment_no) ?>"><i class="fa fa-plane"></i> Attendance</a>
      </div>
      <form method="POST">
        <button type="submit" name="reset" class="nav-btn"><i class="fa fa-search"></i> Search Another Student</button>
      </form>
    </div>

    <!-- PROFILE DISPLAY -->
    <div class="container">
      <div class="photo-section">
        <img src="<?= $profile_photo ?>" alt="Profile Photo">
        <div class="photo-info">
          <div><strong>Enrollment No:</strong> <?= htmlspecialchars($student_data['enrollment_no']) ?></div>
          <div><strong>Status:</strong> Running</div>
          <div><strong>Enrollment Date:</strong> <?= htmlspecialchars($student_data['enrollment_date']) ?></div>
        </div>
      </div>

      <div class="info-section">
        <div class="name-header"><?= htmlspecialchars($student_name) ?></div>
        <hr><br>
        <div class="grid">
          <div class="field"><div class="label">Age</div><div class="value"><?= htmlspecialchars($student_data['age']) ?></div></div>
          <div class="field"><div class="label">DOB</div><div class="value"><?= htmlspecialchars($student_data['dob']) ?></div></div>
          <div class="field"><div class="label">Gender</div><div class="value"><?= htmlspecialchars($student_data['gender']) ?></div></div>
          <div class="field"><div class="label">Blood Group</div><div class="value"><?= htmlspecialchars($student_data['blood_group']) ?></div></div>
          <div class="field"><div class="label">Phone</div><div class="value"><?= htmlspecialchars($student_data['phone']) ?></div></div>
          <div class="field"><div class="label">Alt Phone</div><div class="value"><?= htmlspecialchars($student_data['alt_phone']) ?></div></div>
          <div class="field"><div class="label">Email</div><div class="value"><?= htmlspecialchars($student_data['email']) ?></div></div>
          <div class="field"><div class="label">Religion</div><div class="value"><?= htmlspecialchars($student_data['religion']) ?></div></div>
          <div class="field"><div class="label">Category</div><div class="value"><?= htmlspecialchars($student_data['category']) ?></div></div>
          <div class="field"><div class="label">Disability</div><div class="value"><?= htmlspecialchars($student_data['disability']) ?></div></div>
          <div class="field full"><div class="label">Address 1</div><div class="value"><?= htmlspecialchars($student_data['address1']) ?></div></div>
          <div class="field full"><div class="label">Address 2</div><div class="value"><?= htmlspecialchars($student_data['address2']) ?></div></div>
          <div class="field"><div class="label">State</div><div class="value"><?= htmlspecialchars($student_data['state']) ?></div></div>
          <div class="field"><div class="label">City</div><div class="value"><?= htmlspecialchars($student_data['city_name']) ?></div></div>
          <div class="field"><div class="label">Area</div><div class="value"><?= htmlspecialchars($student_data['area_name']) ?></div></div>
          <div class="field"><div class="label">Pincode</div><div class="value"><?= htmlspecialchars($student_data['pincode']) ?></div></div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="no-result">Student not found. Please check the enrollment number.</div>
    <form method="POST" style="text-align:center; margin-top: 20px;">
      <button type="submit" name="reset" class="nav-btn"><i class="fa fa-search"></i> Try Again</button>
    </form>
  <?php endif; ?>

</div>
</body>
</html>

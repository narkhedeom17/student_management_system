<?php
session_start();

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
if (!$enrollment_no) {
  echo "<h2>Access denied. Please login.</h2>";
  exit;
}

$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$stmt = $conn->prepare("SELECT * FROM student WHERE enrollment_no = ?");
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  echo "<h2>Student not found!</h2>";
  exit;
}
$data = $result->fetch_assoc();

$city_name = '';
$area_name = '';

$city_stmt = $conn->prepare("SELECT city_name FROM tbl_city WHERE city_id = ?");
$city_stmt->bind_param("i", $data['city_id']);
$city_stmt->execute();
$city_res = $city_stmt->get_result();
if ($city = $city_res->fetch_assoc()) $city_name = $city['city_name'];

$area_stmt = $conn->prepare("SELECT area_name FROM tbl_area WHERE area_id = ?");
$area_stmt->bind_param("i", $data['area_id']);
$area_stmt->execute();
$area_res = $area_stmt->get_result();
if ($area = $area_res->fetch_assoc()) $area_name = $area['area_name'];

$student_name = $data['fname'] . ' ' . $data['mname'] . ' ' . $data['lname'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0f2f5;
    }

    .content-container {
      margin-left: 220px;
      padding: 20px;
    }

    .profile-box {
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      max-width: 1000px;
      margin: auto;
    }

    .name-header {
      font-size: 24px;
      text-align: center;
      font-weight: bold;
      color: #00334e;
      margin-bottom: 20px;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px 24px;
      font-size: 14px;
    }

    .field .label {
      font-weight: bold;
      color: #444;
    }

    .field .value {
      color: #555;
    }

    .full {
      grid-column: span 3;
    }

    @media (max-width: 768px) {
      .content-container {
        margin-left: 0;
        padding: 10px;
      }

      .grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content-container">
  <div class="profile-box">
    <div class="name-header"><?= htmlspecialchars($student_name) ?></div>
    <hr><br>
    <div class="grid">
      <div class="field"><div class="label">Enrollment No</div><div class="value"><?= $data['enrollment_no'] ?></div></div>
      <div class="field"><div class="label">Enrollment Date</div><div class="value"><?= $data['enrollment_date'] ?></div></div>
      <div class="field"><div class="label">Age</div><div class="value"><?= $data['age'] ?></div></div>

      <div class="field"><div class="label">DOB</div><div class="value"><?= $data['dob'] ?></div></div>
      <div class="field"><div class="label">Gender</div><div class="value"><?= $data['gender'] ?></div></div>
      <div class="field"><div class="label">Blood Group</div><div class="value"><?= $data['blood_group'] ?></div></div>

      <div class="field"><div class="label">Phone</div><div class="value"><?= $data['phone'] ?></div></div>
      <div class="field"><div class="label">Alt Phone</div><div class="value"><?= $data['alt_phone'] ?></div></div>
      <div class="field"><div class="label">Email</div><div class="value"><?= $data['email'] ?></div></div>

      <div class="field"><div class="label">Religion</div><div class="value"><?= $data['religion'] ?></div></div>
      <div class="field"><div class="label">Category</div><div class="value"><?= $data['category'] ?></div></div>
      <div class="field"><div class="label">Disability</div><div class="value"><?= $data['disability'] ?></div></div>

      <div class="field full"><div class="label">Address Line 1</div><div class="value"><?= $data['address1'] ?></div></div>
      <div class="field full"><div class="label">Address Line 2</div><div class="value"><?= $data['address2'] ?></div></div>

      <div class="field"><div class="label">State</div><div class="value"><?= $data['state'] ?></div></div>
      <div class="field"><div class="label">City</div><div class="value"><?= $city_name ?></div></div>
      <div class="field"><div class="label">Area</div><div class="value"><?= $area_name ?></div></div>
      <div class="field"><div class="label">Pincode</div><div class="value"><?= $data['pincode'] ?></div></div>
    </div>
  </div>
</div>

</body>
</html>

<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$enrollment_no = $_GET['enrollment_no'] ?? '';

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

if ($result->num_rows === 0) {
  echo "<h2>Student not found!</h2>";
  exit;
}

$data = $result->fetch_assoc();
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
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f2f2f2;
    }
    .container {
      display: flex;
      max-width: 1200px;
      background: white;
      margin: 10px auto;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      flex-direction: row;
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
    .photo-info div {
      margin: 4px 0;
    }
    .status-dot {
      height: 10px;
      width: 10px;
      background-color: green;
      border-radius: 100%;
      display: inline-block;
      margin-right: 5px;
    }
    .status-text {
      font-weight: bold;
      color: #000;
    }
    .info-section {
      flex: 3;
      padding: 20px;
    }
    .name-header {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 10px;
      color: #333;
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
    .profile-nav {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      padding: 12px;
      gap: 12px;
      margin-bottom: 12px;
      background: #fff;
      border-bottom: 1px solid rgba(0, 0, 0, 0.08);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .nav-btn {
      font-size: 13px;
      color: #333;
      text-decoration: none;
      padding: 6px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: rgba(255, 255, 255, 0.7);
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease-in-out;
      backdrop-filter: blur(3px);
    }
    .nav-btn:hover {
      background-color: rgba(255, 255, 255, 0.9);
      border-color: #888;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<!-- ✅ Navigation Buttons Section Below Navbar -->
<nav class="profile-nav">
  <a class="nav-btn" href="documents.php?enrollment_no=<?= urlencode($enrollment_no) ?>">
    <i class="fa fa-file-lines"></i> Documents
  </a>
  <a class="nav-btn" href="fees1.php?enrollment_no=<?= urlencode($enrollment_no) ?>">
    <i class="fa fa-money-check-alt"></i> Fees
  </a>
</nav>

<!-- ✅ Profile Content -->
<div class="container">
  <div class="photo-section">
    <img src="<?= htmlspecialchars($data['profile']) ?>" alt="Profile Photo">
    <div class="photo-info">
      <div><strong>Enrollment No:</strong> <?= htmlspecialchars($data['enrollment_no']) ?></div>
      <div class="status-text"><span class="status-dot"></span>Status: Running</div>
      <div><strong>Enrollment Date:</strong> <?= htmlspecialchars($data['enrollment_date']) ?></div>
    </div>
  </div>

  <div class="info-section">
    <div class="name-header"><?= htmlspecialchars($student_name) ?></div><hr><br>
    <div class="grid">
      <div class="field"><div class="label">Age</div><div class="value"><?= htmlspecialchars($data['age']) ?></div></div>
      <div class="field"><div class="label">Date of Birth</div><div class="value"><?= htmlspecialchars($data['dob']) ?></div></div>
      <div class="field"><div class="label">Gender</div><div class="value"><?= htmlspecialchars($data['gender']) ?></div></div>

      <div class="field"><div class="label">Blood Group</div><div class="value"><?= htmlspecialchars($data['blood_group']) ?></div></div>
      <div class="field"><div class="label">Phone</div><div class="value"><?= htmlspecialchars($data['phone']) ?></div></div>
      <div class="field"><div class="label">Alternate Phone</div><div class="value"><?= htmlspecialchars($data['alt_phone']) ?></div></div>

      <div class="field"><div class="label">Email</div><div class="value"><?= htmlspecialchars($data['email']) ?></div></div>
      <div class="field"><div class="label">Religion</div><div class="value"><?= htmlspecialchars($data['religion']) ?></div></div>
      <div class="field"><div class="label">Category</div><div class="value"><?= htmlspecialchars($data['category']) ?></div></div>

      <div class="field"><div class="label">Disability</div><div class="value"><?= htmlspecialchars($data['disability']) ?></div></div>
      <div class="field full"><div class="label">Address Line 1</div><div class="value"><?= htmlspecialchars($data['address1']) ?></div></div>
      <div class="field full"><div class="label">Address Line 2</div><div class="value"><?= htmlspecialchars($data['address2']) ?></div></div>

      <div class="field"><div class="label">State</div><div class="value"><?= htmlspecialchars($data['state']) ?></div></div>
      <div class="field"><div class="label">City</div><div class="value"><?= htmlspecialchars($data['city_name']) ?></div></div>
      <div class="field"><div class="label">Area</div><div class="value"><?= htmlspecialchars($data['area_name']) ?></div></div>
      <div class="field"><div class="label">Pincode</div><div class="value"><?= htmlspecialchars($data['pincode']) ?></div></div>
    </div>
  </div>
</div>

</body>
</html>

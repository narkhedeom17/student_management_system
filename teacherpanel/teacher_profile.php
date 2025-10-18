<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['teacher_loggedin']) || !isset($_SESSION['staff_no'])) {
    header("Location: index.php");
    exit();
}

$staff_no = $_SESSION['staff_no'];
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_no = ?");
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "Staff not found.";
    exit;
}
$row = $result->fetch_assoc();
$profile_path = $row['profile_photo'];
$profile_photo = "images/default-profile.png";
if (!empty($profile_path)) {
    $encoded_path = implode("/", array_map("rawurlencode", explode("/", $profile_path)));
    $profile_photo = "http://localhost/project_backend/" . $encoded_path;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Teacher Profile</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f2f2f2;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-content {
      margin-left: 220px;
      padding: 30px;
      background-color: #f9f9f9;
      min-height: 100vh;
    }

    .profile-container {
      max-width: 1050px;
      margin: auto;
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .profile-header {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 20px;
      margin-bottom: 30px;
    }

    .profile-header img {
      width: 120px;
      height: 140px;
      object-fit: cover;
      border-radius: 12px;
      border: 3px solid #093c56;
      margin-right: 25px;
    }

    .profile-header h2 {
      margin: 0;
      font-size: 26px;
      color: #093c56;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 25px;
      font-size: 15px;
    }

    .field {
      margin-bottom: 10px;
    }

    .label {
      font-weight: 600;
      color: #333;
    }

    .value {
      color: #666;
      margin-top: 3px;
      display: block;
    }

    @media (max-width: 900px) {
      .grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 600px) {
      .main-content {
        margin-left: 0;
      }

      .grid {
        grid-template-columns: 1fr;
      }

      .profile-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .profile-header img {
        margin-bottom: 15px;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="profile-container">
    <div class="profile-header">
      
      <div>
        <h2><?= htmlspecialchars($row['name']) ?></h2>
        <p style="margin: 5px 0; color: #777;">Staff No: <?= htmlspecialchars($row['staff_no']) ?> | Status: <span style="color: green;">Active</span></p>
      </div>
    </div>

    <div class="grid">
      <div class="field">
        <div class="label">Gender</div>
        <span class="value"><?= htmlspecialchars($row['gender']) ?></span>
      </div>
      <div class="field">
        <div class="label">Date of Birth</div>
        <span class="value"><?= htmlspecialchars($row['dob']) ?></span>
      </div>
      <div class="field">
        <div class="label">Contact</div>
        <span class="value"><?= htmlspecialchars($row['contact']) ?></span>
      </div>
      <div class="field">
        <div class="label">Email</div>
        <span class="value"><?= htmlspecialchars($row['email']) ?></span>
      </div>
      <div class="field">
        <div class="label">Address</div>
        <span class="value"><?= htmlspecialchars($row['address']) ?></span>
      </div>
      <div class="field">
        <div class="label">Qualification</div>
        <span class="value"><?= htmlspecialchars($row['qualification']) ?></span>
      </div>
      <div class="field">
        <div class="label">Designation</div>
        <span class="value"><?= htmlspecialchars($row['designation']) ?></span>
      </div>
      <div class="field">
        <div class="label">Staff Type</div>
        <span class="value"><?= htmlspecialchars($row['staff_type']) ?></span>
      </div>
      <div class="field">
        <div class="label">Aadhaar</div>
        <span class="value"><?= htmlspecialchars($row['aadhaar']) ?></span>
      </div>
      <div class="field">
        <div class="label">Bank Name</div>
        <span class="value"><?= htmlspecialchars($row['bank_name']) ?></span>
      </div>
      <div class="field">
        <div class="label">Account Number</div>
        <span class="value"><?= htmlspecialchars($row['bank_account']) ?></span>
      </div>
      <div class="field">
        <div class="label">Joining Date</div>
        <span class="value"><?= htmlspecialchars($row['date_of_joining']) ?></span>
      </div>
    </div>
  </div>
</div>

</body>
</html>

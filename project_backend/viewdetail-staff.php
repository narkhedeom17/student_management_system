<?php
include 'navbar.php';

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$staff_no = $_GET['staff_no'] ?? '';
if (!$staff_no) {
  echo "Invalid staff number.";
  exit;
}

$sql = "SELECT * FROM staff WHERE staff_no = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Staff not found.";
  exit;
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Details - <?php echo htmlspecialchars($staff_no); ?></title>
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
      max-width: 1000px;
      background: white;
      margin: 40px auto;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
      overflow: hidden;
    }

    .photo-section {
      width: 300px;
      background: #f9f9f9;
      text-align: center;
      padding: 30px 20px;
      border-right: 1px solid #ddd;
    }

    .photo-section img {
      width: 120px;
      height: 140px;
      object-fit: cover;
      border-radius: 8px;
      border: 2px solid #888;
      background-color: #fff;
      margin-bottom: 12px;
    }

    .photo-info {
      font-size: 14px;
      color: #333;
    }

    .status-dot {
      height: 10px;
      width: 10px;
      background-color: green;
      border-radius: 50%;
      display: inline-block;
      margin-right: 5px;
    }

    .info-section {
      flex: 1;
      padding: 30px;
    }

    .name-header {
      font-size: 24px;
      font-weight: bold;
      color: #003366;
      margin-bottom: 25px;
      text-align: center;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      font-size: 15px;
    }

    .field {
      margin-bottom: 10px;
    }

    .label {
      font-weight: 600;
      color: #444;
    }

    .value {
      color: #666;
    }

    .full {
      grid-column: span 3;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        margin: 20px;
      }

      .photo-section, .info-section {
        width: 100%;
        padding: 20px;
        border-right: none;
      }

      .grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="photo-section">
    <?php if ($row['profile_photo'] && file_exists($row['profile_photo'])): ?>
      <img src="<?php echo $row['profile_photo']; ?>" alt="Profile Photo">
    <?php else: ?>
      <img src="default-profile.png" alt="No Photo">
    <?php endif; ?>
    <div class="photo-info">
      <div><span class="status-dot"></span>Active</div>
      <div>Staff No: <?php echo htmlspecialchars($row['staff_no']); ?></div>
    </div>
  </div>

  <div class="info-section">
    <div class="name-header"><?php echo htmlspecialchars($row['name']); ?></div>

    <div class="grid">
      <div class="field"><span class="label">Gender:</span> <span class="value"><?php echo htmlspecialchars($row['gender']); ?></span></div>
      <div class="field"><span class="label">DOB:</span> <span class="value"><?php echo htmlspecialchars($row['dob']); ?></span></div>
      <div class="field"><span class="label">Contact:</span> <span class="value"><?php echo htmlspecialchars($row['contact']); ?></span></div>

      <div class="field"><span class="label">Email:</span> <span class="value"><?php echo htmlspecialchars($row['email']); ?></span></div>
      <div class="field"><span class="label">Address:</span> <span class="value"><?php echo htmlspecialchars($row['address']); ?></span></div>
      <div class="field"><span class="label">Qualification:</span> <span class="value"><?php echo htmlspecialchars($row['qualification']); ?></span></div>

      <div class="field"><span class="label">Designation:</span> <span class="value"><?php echo htmlspecialchars($row['designation']); ?></span></div>
      <div class="field"><span class="label">Staff Type:</span> <span class="value"><?php echo htmlspecialchars($row['staff_type']); ?></span></div>
      <div class="field"><span class="label">Aadhaar:</span> <span class="value"><?php echo htmlspecialchars($row['aadhaar']); ?></span></div>

      <div class="field"><span class="label">Bank Name:</span> <span class="value"><?php echo htmlspecialchars($row['bank_name']); ?></span></div>
      <div class="field"><span class="label">Account No.:</span> <span class="value"><?php echo htmlspecialchars($row['bank_account']); ?></span></div>
      <div class="field"><span class="label">Joining Date:</span> <span class="value"><?php echo htmlspecialchars($row['date_of_joining']); ?></span></div>
    </div>
  </div>
</div>

</body>
</html>

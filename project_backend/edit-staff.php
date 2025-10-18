<?php
include 'navbar.php';

$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get staff_no from URL
$staff_no = $_GET['staff_no'] ?? '';
if (!$staff_no) {
  echo "No staff number provided.";
  exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $gender = $_POST['gender'];
  $dob = $_POST['dob'];
  $contact = $_POST['contact'];
  $email = $_POST['email'];
  $address = $_POST['address'];
  $qualification = $_POST['qualification'];
  $designation = $_POST['designation'];
  $staff_type = $_POST['staff_type'];
  $aadhaar = $_POST['aadhaar'];
  $bank_name = $_POST['bank_name'];
  $bank_account = $_POST['bank_account'];
  $date_of_joining = $_POST['date_of_joining'];

  // Handle photo upload
  $photo_path = $_POST['existing_photo']; // default
  if (!empty($_FILES['profile_photo']['name'])) {
    $upload_dir = "uploads/";
    $filename = "staff_" . time() . "_" . basename($_FILES['profile_photo']['name']);
    $target_file = $upload_dir . $filename;
    move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file);
    $photo_path = $target_file;
  }

  $stmt = $conn->prepare("UPDATE staff SET 
    name=?, gender=?, dob=?, contact=?, email=?, address=?, 
    qualification=?, designation=?, staff_type=?, aadhaar=?, 
    bank_name=?, bank_account=?, date_of_joining=?, profile_photo=?
    WHERE staff_no=?");

  $stmt->bind_param("sssssssssssssss", 
    $name, $gender, $dob, $contact, $email, $address,
    $qualification, $designation, $staff_type, $aadhaar,
    $bank_name, $bank_account, $date_of_joining, $photo_path, $staff_no
  );

  if ($stmt->execute()) {
    echo "<script>alert('Staff updated successfully.'); window.location.href='staff.php';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }
}

// Fetch staff by staff_no
$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_no=?");
$stmt->bind_param("s", $staff_no);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
if (!$data) {
  echo "Staff not found.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Staff - Newton School</title>
  <style>
    body {
      background-color: #f2f4f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .content-wrapper {
      max-width: 960px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #003366;
      margin-bottom: 20px;
    }
    form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: space-between;
    }
    .form-group {
      flex: 1 1 45%;
      display: flex;
      flex-direction: column;
    }
    .form-group.full-width {
      flex: 1 1 100%;
    }
    label {
      font-weight: 600;
      margin-bottom: 5px;
    }
    input, select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 14px;
    }
    .btn-submit {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
      margin: 0 auto;
      display: block;
    }
    .btn-submit:hover {
      background-color: #0056b3;
    }
    .photo-preview img {
      width: 100px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #999;
      margin-top: 6px;
    }
    @media (max-width: 768px) {
      .form-group {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>
  <div class="content-wrapper">
    <h2>Edit Staff</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($data['name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Gender</label>
        <select name="gender" required>
          <option value="">Select</option>
          <option <?= $data['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
          <option <?= $data['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
          <option <?= $data['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
      </div>

      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob" value="<?= htmlspecialchars($data['dob']) ?>" required>
      </div>

      <div class="form-group">
        <label>Contact</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($data['contact']) ?>" required>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required>
      </div>

      <div class="form-group">
        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($data['address']) ?>" required>
      </div>

      <div class="form-group">
        <label>Qualification</label>
        <input type="text" name="qualification" value="<?= htmlspecialchars($data['qualification']) ?>" required>
      </div>

      <div class="form-group">
        <label>Designation</label>
        <input type="text" name="designation" value="<?= htmlspecialchars($data['designation']) ?>" required>
      </div>

      <div class="form-group">
        <label>Staff Type</label>
        <select name="staff_type" required>
          <option value="">Select</option>
          <option <?= $data['staff_type'] === 'Technical' ? 'selected' : '' ?>>Technical</option>
          <option <?= $data['staff_type'] === 'Non-Technical' ? 'selected' : '' ?>>Non-Technical</option>
        </select>
      </div>

      <div class="form-group">
        <label>Aadhaar Number</label>
        <input type="text" name="aadhaar" maxlength="12" value="<?= htmlspecialchars($data['aadhaar']) ?>" required>
      </div>

      <div class="form-group">
        <label>Bank Name</label>
        <input type="text" name="bank_name" value="<?= htmlspecialchars($data['bank_name']) ?>" required>
      </div>

      <div class="form-group">
        <label>Bank Account Number</label>
        <input type="text" name="bank_account" value="<?= htmlspecialchars($data['bank_account']) ?>" required>
      </div>

      <div class="form-group">
        <label>Date of Joining</label>
        <input type="date" name="date_of_joining" value="<?= htmlspecialchars($data['date_of_joining']) ?>" required>
      </div>

      <div class="form-group">
        <label>Change Profile Photo</label>
        <input type="file" name="profile_photo" accept="image/*">
        <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($data['profile_photo']) ?>">
        <?php if (!empty($data['profile_photo'])): ?>
          <div class="photo-preview">
            <img src="<?= htmlspecialchars($data['profile_photo']) ?>" alt="Current Photo">
          </div>
        <?php endif; ?>
      </div>

      <div class="form-group full-width" style="text-align: center;">
        <button type="submit" class="btn-submit">Update</button>
      </div>
    </form>
  </div>
</body>
</html>

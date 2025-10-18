<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $result = $conn->query("SELECT staff_no FROM staff ORDER BY staff_no DESC LIMIT 1");
  if ($result && $row = $result->fetch_assoc()) {
    $lastNo = intval(substr($row['staff_no'], 2));
    $newNo = $lastNo + 1;
  } else {
    $newNo = 1;
  }

  if ($newNo > 9999) die("Maximum staff number limit (ST9999) reached.");
  $staff_no = 'ST' . str_pad($newNo, 4, '0', STR_PAD_LEFT);

  // Get form data
  $name           = $_POST['name'];
  $gender         = $_POST['gender'];
  $dob            = $_POST['dob'];
  $contact        = $_POST['contact'];
  $email          = $_POST['email'];
  $address        = $_POST['address'];
  $qualification  = $_POST['qualification'];
  $designation    = $_POST['designation'];
  $staff_type     = $_POST['staff_type'];
  $aadhaar        = $_POST['aadhaar'];
  $bank_name      = $_POST['bank_name'];
  $bank_account   = $_POST['bank_account'];
  $date_of_joining= $_POST['date_of_joining'];

  // Handle profile photo upload
  $profile_photo = '';
  if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir);

    $fileName = $staff_no . '_' . basename($_FILES['profile_photo']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetPath)) {
      $profile_photo = $targetPath;
    }
  }

  // Insert into database
  $sql = "INSERT INTO staff (
    staff_no, name, gender, dob, contact, email, address, 
    qualification, designation, staff_type, aadhaar, 
    bank_name, bank_account, date_of_joining, profile_photo
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  if (!$stmt) die("Prepare failed: " . $conn->error);

  $stmt->bind_param("sssssssssssssss", 
    $staff_no, $name, $gender, $dob, $contact, $email, $address,
    $qualification, $designation, $staff_type, $aadhaar,
    $bank_name, $bank_account, $date_of_joining, $profile_photo
  );

  if ($stmt->execute()) {
    echo "<script>alert('Staff details submitted successfully! Staff No: $staff_no'); window.location.href='staff_form.php';</script>";
  } else {
    echo "Error: " . $stmt->error;
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Staff Admission Form</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background-color: #f2f4f6; }
    .navbar { width: 100%; background-color: #003366; color: white; padding: 15px 20px; font-size: 18px; }
    .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 30px; }
    h2 { text-align: center; color: #003366; margin-bottom: 30px; }
    form { display: flex; flex-wrap: wrap; gap: 20px; }
    .form-group { flex: 1 1 calc(33.33% - 20px); display: flex; flex-direction: column; }
    label { font-weight: 600; margin-bottom: 5px; color: #444; }
    input, select { padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
    .full-width { flex: 1 1 100%; }
    button { padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 6px; font-size: 16px; cursor: pointer; width: 100%; }
    button:hover { background-color: #0056b3; }
    @media (max-width: 768px) { .form-group { flex: 1 1 100%; } }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>
<div class="container">
  <h2>Staff Admission Form</h2>
  <form method="POST" enctype="multipart/form-data">

    <div class="form-group"><label for="name">Full Name</label><input type="text" id="name" name="name" required></div>
    <div class="form-group"><label for="gender">Gender</label>
      <select id="gender" name="gender" required>
        <option value="">Select</option><option>Male</option><option>Female</option><option>Other</option>
      </select>
    </div>
    <div class="form-group"><label for="dob">Date of Birth</label><input type="date" id="dob" name="dob" required></div>
    <div class="form-group"><label for="contact">Contact Number</label><input type="text" id="contact" name="contact" required></div>
    <div class="form-group"><label for="email">Email ID</label><input type="email" id="email" name="email" required></div>
    <div class="form-group"><label for="address">Address</label><input type="text" id="address" name="address" required></div>
    <div class="form-group"><label for="qualification">Qualification</label><input type="text" id="qualification" name="qualification" required></div>
    <div class="form-group"><label for="designation">Designation</label><input type="text" id="designation" name="designation" required></div>
    <div class="form-group"><label for="staff_type">Staff Type</label>
      <select id="staff_type" name="staff_type" required>
        <option value="">Select</option><option>Technical</option><option>Non-Technical</option>
      </select>
    </div>
    <div class="form-group"><label for="aadhaar">Aadhaar Number</label><input type="text" id="aadhaar" name="aadhaar" maxlength="12" required></div>
    <div class="form-group"><label for="bank_name">Bank Name</label><input type="text" id="bank_name" name="bank_name" required></div>
    <div class="form-group"><label for="bank_account">Bank Account Number</label><input type="text" id="bank_account" name="bank_account" required></div>
    <div class="form-group"><label for="date_of_joining">Date of Joining</label><input type="date" id="date_of_joining" name="date_of_joining" required></div>
    
    <div class="form-group">
      <label for="profile_photo">Profile Photo</label>
      <input type="file" id="profile_photo" name="profile_photo" accept="image/*" required>
    </div>

    <div class="form-group full-width"><button type="submit">Submit</button></div>
  </form>
</div>

</body>
</html>

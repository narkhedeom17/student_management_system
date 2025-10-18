<?php
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch cities
$cities = $conn->query("SELECT * FROM tbl_city ORDER BY city_name ASC");

// Auto-generate enrollment number
$result = $conn->query("SELECT MAX(enrollment_no) AS max_enroll FROM student");
$next_enroll_no = '00000027';
if ($result && $row = $result->fetch_assoc()) {
  $max = $row['max_enroll'];
  if ($max) {
    $next = intval($max) + 1;
    $next_enroll_no = str_pad($next, 8, '0', STR_PAD_LEFT);
  }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $enrollment_no = $_POST['enrollment_no'];
  $fname = $_POST['fname'];
  $mname = $_POST['mname'];
  $lname = $_POST['lname'];
  $age = $_POST['age'];
  $dob = $_POST['dob'];
  $enrollment_date = $_POST['enrollment_date'];
  $blood_group = $_POST['blood_group'];
  $gender = $_POST['gender'];
  $phone = $_POST['phone'];
  $alt_phone = $_POST['alt_phone'];
  $email = $_POST['email'];
  $religion = $_POST['religion'];
  $category = $_POST['category'];
  $disability = $_POST['disability'];
  $address1 = $_POST['address1'];
  $address2 = $_POST['address2'];
  $state = $_POST['state'];
  $city_id = $_POST['city_id'];
  $area_id = $_POST['area_id'];
  $pincode = $_POST['pincode'];

  $profile_path = null;
  if (isset($_FILES['profile']) && $_FILES['profile']['error'] === 0) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir);
    $profile_path = $targetDir . time() . "_" . basename($_FILES["profile"]["name"]);
    move_uploaded_file($_FILES["profile"]["tmp_name"], $profile_path);
  }

  $sql = "INSERT INTO student (
    enrollment_no, profile, fname, mname, lname, age, dob, enrollment_date,
    blood_group, gender, phone, alt_phone, email, religion, category, disability,
    address1, address2, state, city_id, area_id, pincode
  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  if (!$stmt) die("Prepare failed: " . $conn->error);

  $stmt->bind_param(
    "sssssisssssssssssssiss",
    $enrollment_no, $profile_path, $fname, $mname, $lname, $age, $dob, $enrollment_date,
    $blood_group, $gender, $phone, $alt_phone, $email, $religion, $category,
    $disability, $address1, $address2, $state, $city_id, $area_id, $pincode
  );

  if ($stmt->execute()) {
    echo "<script>alert('Student added successfully!'); window.location.href='admission.php';</script>";
    exit;
  } else {
    echo "Error: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Student</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      padding: 0;
      margin: 0;
    }

    .navbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      width: 100%;
      background: #000;
      color: white;
      padding: 15px 30px;
      font-size: 20px;
      font-weight: bold;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .container {
      max-width: 1200px;
      background: #fff;
      margin: 30px auto;
      padding: 30px;
      border-radius: 10px;
    }

    .row-group {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }

    label {
      font-weight: bold;
      margin-top: 12px;
      display: block;
    }

    input, select, textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .full {
      grid-column: span 3;
    }

    .buttons {
      text-align: center;
      margin-top: 20px;
    }

    button {
      padding: 10px 20px;
      background: black;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background: #333;
    }
  </style>
</head>
<body>
  <?php include 'navbar.php'; ?>

  <div class="container">
    <h2>Add New Student</h2>
    <form method="POST" enctype="multipart/form-data">
      <div class="row-group">
        <div><label>Enrollment No</label><input name="enrollment_no" value="<?= $next_enroll_no ?>" readonly></div>
        <div><label>First Name</label><input name="fname" required></div>
        <div><label>Middle Name</label><input name="mname"></div>
        <div><label>Last Name</label><input name="lname" required></div>
        <div><label>Age</label><input type="number" name="age"></div>
        <div><label>Date of Birth</label><input type="date" name="dob"></div>
        <div><label>Enrollment Date</label><input type="date" name="enrollment_date" required></div>

        <div>
          <label>Blood Group</label>
          <select name="blood_group" required>
            <option value="">Select</option>
            <option value="A+">A+</option><option value="A-">A−</option>
            <option value="B+">B+</option><option value="B-">B−</option>
            <option value="AB+">AB+</option><option value="AB-">AB−</option>
            <option value="O+">O+</option><option value="O-">O−</option>
          </select>
        </div>

        <div>
          <label>Gender</label>
          <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male</option><option value="Female">Female</option><option value="Other">Other</option>
          </select>
        </div>

        <div><label>Phone</label><input name="phone"></div>
        <div><label>Alt Phone</label><input name="alt_phone"></div>
        <div><label>Email</label><input type="email" name="email"></div>

        <div>
          <label>Religion</label>
          <select name="religion" required>
            <option value="">Select Religion</option>
            <option value="Hindu">Hindu</option><option value="Muslim">Muslim</option>
            <option value="Christian">Christian</option><option value="Sikh">Sikh</option>
            <option value="Buddhist">Buddhist</option><option value="Jain">Jain</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div>
          <label>Category</label>
          <select name="category" required>
            <option value="">Select Category</option>
            <option value="General">General</option><option value="SC">SC</option>
            <option value="ST">ST</option><option value="OBC">OBC</option>
            <option value="EWS">EWS</option><option value="Other">Other</option>
          </select>
        </div>

        <div>
          <label>Disability</label>
          <select name="disability" required>
            <option value="">Select</option>
            <option value="No">No</option><option value="Yes">Yes</option>
          </select>
        </div>
      </div>

      <div class="row-group full">
        <div class="full"><label>Address Line 1</label><textarea name="address1"></textarea></div>
        <div class="full"><label>Address Line 2</label><textarea name="address2"></textarea></div>

        <div>
          <label>State</label>
          <select name="state" required>
            <option value="">Select State</option>
            <option value="Gujarat">Gujarat</option>
          </select>
        </div>

        <div>
          <label>City</label>
          <select name="city_id" id="city_select" required>
            <option value="">Select City</option>
            <?php while($city = $cities->fetch_assoc()): ?>
              <option value="<?= $city['city_id'] ?>"><?= $city['city_name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div>
          <label>Area</label>
          <select name="area_id" id="area_select" required>
            <option value="">Select Area</option>
          </select>
        </div>

        <div><label>Pincode</label><input type="number" name="pincode"></div>
      </div>

      <div class="full"><label>Profile Photo</label><input type="file" name="profile" accept="image/*"></div>

      <div class="buttons">
        <button type="submit">Add Student</button>
      </div>
    </form>
  </div>

  <script>
    $('#city_select').on('change', function() {
      const city_id = $(this).val();
      $.post('get_areas.php', { city_id }, function(data) {
        $('#area_select').html(data);
      });
    });
  </script>
</body>
</html>

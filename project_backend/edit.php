<?php
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Update logic
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

  $sql = "UPDATE student SET 
    fname=?, mname=?, lname=?, age=?, dob=?, enrollment_date=?,
    blood_group=?, gender=?, phone=?, alt_phone=?, email=?, religion=?, 
    category=?, disability=?, address1=?, address2=?, state=?, city_id=?, area_id=?, pincode=?";

  if ($profile_path) $sql .= ", profile=?";
  $sql .= " WHERE enrollment_no=?";

  $stmt = $conn->prepare($sql);
  if (!$stmt) die("Prepare failed: " . $conn->error);

  if ($profile_path) {
    $stmt->bind_param(
      "sssisssssssssssssiiiss",
      $fname, $mname, $lname, $age, $dob, $enrollment_date,
      $blood_group, $gender, $phone, $alt_phone, $email, $religion,
      $category, $disability, $address1, $address2, $state, $city_id, $area_id, $pincode,
      $profile_path, $enrollment_no
    );
  } else {
    $stmt->bind_param(
      "sssisssssssssssssiiis",
      $fname, $mname, $lname, $age, $dob, $enrollment_date,
      $blood_group, $gender, $phone, $alt_phone, $email, $religion,
      $category, $disability, $address1, $address2, $state, $city_id, $area_id, $pincode,
      $enrollment_no
    );
  }

  if ($stmt->execute()) {
    echo "<script>alert('Student updated successfully!'); window.location.href='admission.php';</script>";
    exit;
  } else {
    echo "Error: " . $stmt->error;
  }
}

// Fetch student data
$enrollment_no = $_GET['enrollment_no'] ?? '';
if (!$enrollment_no) die("Enrollment number is missing.");

$cities = $conn->query("SELECT * FROM tbl_city ORDER BY city_name ASC");

$stmt = $conn->prepare("SELECT * FROM student WHERE enrollment_no = ?");
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) die("Student not found.");
$row = $res->fetch_assoc();

$areas = $conn->query("SELECT * FROM tbl_area WHERE city_id = '{$row['city_id']}'");
$full_name = trim($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Student - <?= htmlspecialchars($full_name) ?></title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    body { margin: 0; font-family: 'Segoe UI'; background: #f4f4f4; }

    .navbar-wrapper {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 999;
    }

    .container {
      max-width: 1200px;
      background: #fff;
      margin: 100px auto 30px;
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

    .full { grid-column: span 3; }
    .buttons { text-align: center; margin-top: 20px; }
    button {
      padding: 10px 20px;
      background: black;
      color: white;
      border: none;
      border-radius: 6px;
    }

    @media (max-width: 768px) {
      .row-group { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

<div class="navbar-wrapper">
  <?php include 'navbar.php'; ?>
</div>

<div class="container">
  <h2>Edit Student: <?= htmlspecialchars($full_name) ?></h2>

  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="enrollment_no" value="<?= $enrollment_no ?>">

    <div class="row-group">
      <div><label>First Name</label><input name="fname" value="<?= $row['fname'] ?>" required></div>
      <div><label>Middle Name</label><input name="mname" value="<?= $row['mname'] ?>"></div>
      <div><label>Last Name</label><input name="lname" value="<?= $row['lname'] ?>" required></div>

      <div><label>Age</label><input type="number" name="age" value="<?= $row['age'] ?>"></div>
      <div><label>Date of Birth</label><input type="date" name="dob" value="<?= $row['dob'] ?>"></div>
      <div><label>Enrollment Date</label><input type="date" name="enrollment_date" value="<?= $row['enrollment_date'] ?>" required></div>

      <div>
        <label>Blood Group</label>
        <select name="blood_group">
          <option value="">Select</option>
          <?php
          $blood_groups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
          foreach ($blood_groups as $group) {
            $selected = ($row['blood_group'] == $group) ? 'selected' : '';
            echo "<option value=\"$group\" $selected>$group</option>";
          }
          ?>
        </select>
      </div>

      <div>
        <label>Gender</label>
        <select name="gender">
          <option value="">Select Gender</option>
          <?php
          $genders = ['Male', 'Female', 'Other'];
          foreach ($genders as $g) {
            $selected = ($row['gender'] == $g) ? 'selected' : '';
            echo "<option value=\"$g\" $selected>$g</option>";
          }
          ?>
        </select>
      </div>

      <div><label>Phone</label><input name="phone" value="<?= $row['phone'] ?>"></div>

      <div><label>Alt Phone</label><input name="alt_phone" value="<?= $row['alt_phone'] ?>"></div>
      <div><label>Email</label><input type="email" name="email" value="<?= $row['email'] ?>"></div>

      <div>
        <label>Religion</label>
        <select name="religion">
          <option value="">Select Religion</option>
          <?php
          $religions = ['Hindu', 'Muslim', 'Christian', 'Sikh', 'Buddhist', 'Jain', 'Other'];
          $currentReligion = strtolower(trim($row['religion']));
          foreach ($religions as $r) {
            $selected = (strtolower($r) === $currentReligion) ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($r) . "\" $selected>" . htmlspecialchars($r) . "</option>";
          }
          ?>
        </select>
      </div>

      <div>
        <label>Category</label>
        <select name="category">
          <option value="">Select Category</option>
          <?php
          $categories = ['General', 'OBC', 'SC', 'ST', 'EWS', 'Other'];
          foreach ($categories as $cat) {
            $selected = ($row['category'] == $cat) ? 'selected' : '';
            echo "<option value=\"$cat\" $selected>$cat</option>";
          }
          ?>
        </select>
      </div>

      <div>
        <label>Disability</label>
        <select name="disability">
          <option value="">Select</option>
          <?php
          $disabilities = ['No', 'Yes', 'Other'];
          foreach ($disabilities as $d) {
            $selected = ($row['disability'] == $d) ? 'selected' : '';
            echo "<option value=\"$d\" $selected>$d</option>";
          }
          ?>
        </select>
      </div>
    </div>

    <div class="row-group full">
      <div class="full"><label>Address Line 1</label><textarea name="address1"><?= $row['address1'] ?></textarea></div>
      <div class="full"><label>Address Line 2</label><textarea name="address2"><?= $row['address2'] ?></textarea></div>

      <div>
        <label>State</label>
        <select name="state" required>
          <option value="Gujarat" <?= (strcasecmp(trim($row['state']), 'Gujarat') === 0) ? 'selected' : '' ?>>Gujarat</option>
        </select>
      </div>

      <div>
        <label>City</label>
        <select name="city_id" id="city_select" required>
          <option value="">Select City</option>
          <?php while($city = $cities->fetch_assoc()): ?>
            <option value="<?= $city['city_id'] ?>" <?= $city['city_id'] == $row['city_id'] ? 'selected' : '' ?>>
              <?= $city['city_name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div>
        <label>Area</label>
        <select name="area_id" id="area_select" required>
          <option value="">Select Area</option>
          <?php while($area = $areas->fetch_assoc()): ?>
            <option value="<?= $area['area_id'] ?>" <?= $area['area_id'] == $row['area_id'] ? 'selected' : '' ?>>
              <?= $area['area_name'] ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div><label>Pincode</label><input type="number" name="pincode" value="<?= $row['pincode'] ?>"></div>
    </div>

    <div class="full"><label>Update Profile Photo (Optional)</label><input type="file" name="profile" accept="image/*"></div>

    <div class="buttons">
      <button type="submit">Update Student</button>
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

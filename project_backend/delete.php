<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$enrollment_no = $_GET['enrollment_no'] ?? '';
if (!$enrollment_no) die("Enrollment number is missing.");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Deactivate the student (soft delete)
  $deactivate_sql = "UPDATE student SET status = 'Inactive' WHERE enrollment_no = ?";
  $stmt = $conn->prepare($deactivate_sql);
  $stmt->bind_param("s", $enrollment_no);

  if ($stmt->execute()) {
    echo "<script>alert('Student Deleted successfully!'); window.location.href='admission.php';</script>";
    exit;
  } else {
    echo "Error Delete student: " . $stmt->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Delete Student</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * { box-sizing: border-box; }
    html, body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      height: 100%;
    }
    .navbar {
      background-color: #3e3e3e;
      color: white;
      padding: 14px 20px;
      font-size: 18px;
      font-weight: bold;
    }
    .page {
      min-height: calc(100vh - 58px);
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    .form-container {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      max-width: 500px;
      width: 100%;
      text-align: center;
    }
    h2 {
      font-size: 22px;
      color: #333;
      margin-bottom: 20px;
    }
    p {
      font-size: 15px;
      color: #555;
    }
    form {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }
    button {
      padding: 8px 16px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .confirm {
      background-color: #c0392b;
      color: white;
    }
    .confirm:hover {
      background-color: #922b21;
    }
    .cancel {
      background-color: #2c3e50;
      color: white;
      text-decoration: none;
      display: inline-block;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: bold;
    }
    .cancel:hover {
      background-color: #1a252f;
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page">
  <div class="form-container">
    <h2>Are you sure?</h2>
    <p>Do you really want to delete student with enrollment no: <strong><?= htmlspecialchars($enrollment_no) ?></strong>?</p>

    <form method="POST">
      <button type="submit" class="confirm">Yes, delete</button>
      <a href="admission.php" class="cancel">Cancel</a>
    </form>
  </div>
</div>

</body>
</html>

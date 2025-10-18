<?php
$servername = "localhost";
$username = "root";
$password = "12345678";
$dbname = "newton_db";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get staff_no from URL
$staff_no = $_GET['staff_no'] ?? '';
if (!$staff_no) {
  echo "No staff number provided.";
  exit;
}

// Step: Set status to 'Inactive' instead of deleting
$deactivate_sql = "UPDATE staff SET status = 'Inactive' WHERE staff_no = ?";
$deactivate_stmt = $conn->prepare($deactivate_sql);
$deactivate_stmt->bind_param("s", $staff_no);

if ($deactivate_stmt->execute()) {
  echo "<script>alert('Staff deleted successfully.'); window.location.href='staff.php';</script>";
} else {
  echo "Error deleting staff: " . $deactivate_stmt->error;
}
?>

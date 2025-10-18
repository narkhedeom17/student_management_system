<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
$student_name = $_SESSION['student_name'] ?? 'Student';

$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch leave records
$stmt = $conn->prepare("SELECT * FROM student_leaves WHERE enrollment_no = ? ORDER BY from_date DESC");
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$leave_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Records</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f4f4;
    }

    .content {
      margin-left: 220px;
      padding: 30px;
    }

    h2 {
      text-align: center;
      color: #093c56;
      font-size: 24px;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
      font-size: 15px;
    }

    th, td {
      padding: 10px 12px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background-color: #093c56;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .no-data {
      text-align: center;
      padding: 20px;
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
      margin: 0 auto;
      width: 60%;
      border-radius: 6px;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="content">
  <h2>My Leave Records</h2>

  <?php if ($leave_result && $leave_result->num_rows > 0): ?>
    <table>
      <tr>
        <th>From Date</th>
        <th>To Date</th>
        <th>Days</th>
        <th>Reason</th>
        <th>Applied On</th>
      </tr>
      <?php while ($row = $leave_result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['from_date']) ?></td>
          <td><?= htmlspecialchars($row['to_date']) ?></td>
          <td><?= htmlspecialchars($row['days']) ?></td>
          <td><?= htmlspecialchars($row['reason']) ?></td>
          <td><?= date('d-M-Y H:i', strtotime($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <div class="no-data">No leave records found.</div>
  <?php endif; ?>
</div>

</body>
</html>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
$student_name = $_SESSION['student_name'] ?? 'Student';

$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch exam marks
$stmt = $conn->prepare("SELECT * FROM exam_marks WHERE enrollment_no = ? ORDER BY exam_date DESC");
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$marks_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Exam Marks</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
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

    .no-records {
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
  <h2>My Exam Marks</h2>

  <?php if ($marks_result && $marks_result->num_rows > 0): ?>
    <table>
      <tr>
        <th>Exam</th>
        <th>Exam Date</th>
        <th>Subject</th>
        <th>Total</th>
        <th>Scored</th>
        <th>Percentage</th>
      </tr>
      <?php while ($row = $marks_result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['exam_name']) ?></td>
          <td><?= date('d-M-Y', strtotime($row['exam_date'])) ?></td>
          <td><?= htmlspecialchars($row['subject']) ?></td>
          <td><?= htmlspecialchars($row['total_marks']) ?></td>
          <td><?= htmlspecialchars($row['marks_obtained']) ?></td>
          <td><?= htmlspecialchars($row['percentage']) ?>%</td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <div class="no-records">No exam records found.</div>
  <?php endif; ?>
</div>

</body>
</html>

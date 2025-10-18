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

$students = $conn->query("SELECT enrollment_no, fname, mname, lname FROM student ORDER BY fname ASC");

$selected_enrollment = $_GET['enrollment_no'] ?? ($_POST['enrollment_no'] ?? '');
$readonly = isset($_GET['enrollment_no']);
$student_name = '';

// Get student name if selected
if ($selected_enrollment) {
    $stmt = $conn->prepare("SELECT fname, mname, lname FROM student WHERE enrollment_no = ?");
    $stmt->bind_param("s", $selected_enrollment);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $student_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['lname']);
    }
    $stmt->close();
}

// Insert leave
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_leave'])) {
    $enrollment_no = $_POST['enrollment_no'] ?? '';
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    if ($enrollment_no && $from_date && $to_date && $reason) {
        $from = new DateTime($from_date);
        $to = new DateTime($to_date);
        $interval = $from->diff($to)->days + 1;

        if ($interval > 0) {
            $stmt = $conn->prepare("INSERT INTO student_leaves (enrollment_no, from_date, to_date, reason, days) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $enrollment_no, $from_date, $to_date, $reason, $interval);
            $stmt->execute();

            // Redirect back to the same page with student context
            header("Location: leave.php?enrollment_no=" . urlencode($enrollment_no));
            exit();
        } else {
            echo "<script>alert('To date must be equal to or after From date.');</script>";
        }
    } else {
        echo "<script>alert('Please fill all fields.');</script>";
    }
}

// Fetch leave records
$leave_result = null;
if ($selected_enrollment) {
    $stmt = $conn->prepare("SELECT * FROM student_leaves WHERE enrollment_no = ? ORDER BY from_date DESC");
    $stmt->bind_param("s", $selected_enrollment);
    $stmt->execute();
    $leave_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Entry</title>
  <style>
    body {
      background: #f4f7fc;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
    }

    .main-content {
      margin-left: 220px;
      padding: 30px;
    }

    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #093c56;
      margin-bottom: 25px;
    }

    label {
      font-weight: bold;
      display: block;
      margin: 12px 0 5px;
    }

    select, input[type="date"], textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    textarea {
      resize: vertical;
    }

    button {
      background: #093c56;
      color: white;
      padding: 10px 15px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    button:hover {
      background: #0f4a6e;
    }

    .readonly-info {
      font-weight: bold;
      color: #444;
      padding: 8px 0;
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
    }

    th {
      background: #093c56;
      color: white;
    }

    tr:nth-child(even) {
      background: #f2f2f2;
    }

    h3 {
      margin-top: 40px;
      text-align: center;
      color: #333;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 15px;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="container">
    <h2>Leave Entry Form</h2>

    <form method="POST">
      <?php if (!$readonly): ?>
        <label for="enrollment_no">Select Student</label>
        <select name="enrollment_no" required>
          <option value="">-- Select --</option>
          <?php
          mysqli_data_seek($students, 0);
          while ($row = $students->fetch_assoc()):
            $full_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['lname']);
            $selected = ($row['enrollment_no'] == $selected_enrollment) ? 'selected' : '';
          ?>
            <option value="<?= $row['enrollment_no'] ?>" <?= $selected ?>>
              <?= htmlspecialchars($full_name) ?>
            </option>
          <?php endwhile; ?>
        </select>
      <?php else: ?>
        <input type="hidden" name="enrollment_no" value="<?= htmlspecialchars($selected_enrollment) ?>">
        <p class="readonly-info">Student: <?= htmlspecialchars($student_name) ?></p>
      <?php endif; ?>

      <label for="from_date">From Date</label>
      <input type="date" name="from_date" required>

      <label for="to_date">To Date</label>
      <input type="date" name="to_date" required>

      <label for="reason">Reason</label>
      <textarea name="reason" rows="4" required placeholder="Sick, Family Function, etc."></textarea>

      <button type="submit" name="submit_leave">Submit Leave</button>
    </form>

    <?php if ($leave_result && $leave_result->num_rows > 0): ?>
      <h3>Leave History for <?= htmlspecialchars($student_name) ?></h3>
      <table>
        <tr>
          <th>From</th>
          <th>To</th>
          <th>Days</th>
          <th>Reason</th>
          <th>Created At</th>
        </tr>
        <?php while ($row = $leave_result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['from_date'] ?></td>
            <td><?= $row['to_date'] ?></td>
            <td><?= $row['days'] ?></td>
            <td><?= htmlspecialchars($row['reason']) ?></td>
            <td><?= date('d-M-Y H:i', strtotime($row['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php elseif ($selected_enrollment): ?>
      <p style="text-align:center; color:#999;">No leave history found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

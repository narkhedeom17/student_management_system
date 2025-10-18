<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['teacher_loggedin']) || !isset($_SESSION['staff_no'])) {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$subjects = ['Math', 'Science', 'English', 'History', 'Geography', 'Marathi', 'Hindi', 'Computer', 'EVS', 'Physics', 'Chemistry'];

$selected_enrollment = $_GET['enrollment_no'] ?? ($_POST['enrollment_no'] ?? '');
$student_name = '';

// Fetch student name
if ($selected_enrollment) {
    $stmt_name = $conn->prepare("SELECT fname, mname, lname FROM student WHERE enrollment_no = ?");
    $stmt_name->bind_param("s", $selected_enrollment);
    $stmt_name->execute();
    $res = $stmt_name->get_result();
    if ($row = $res->fetch_assoc()) {
        $student_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['lname']);
    }
    $stmt_name->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_marks'])) {
    $exam_name = $_POST['exam_name'] ?? '';
    $exam_date = $_POST['exam_date'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $total_marks = intval($_POST['total_marks'] ?? 0);
    $marks_obtained = intval($_POST['marks_obtained'] ?? 0);
    $percentage = $total_marks > 0 ? round(($marks_obtained / $total_marks) * 100, 2) : 0;

    if ($selected_enrollment && $exam_name && $exam_date && $subject && $total_marks > 0 && $marks_obtained >= 0) {
        $stmt = $conn->prepare("INSERT INTO exam_marks (enrollment_no, exam_name, exam_date, subject, total_marks, marks_obtained, percentage) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiii", $selected_enrollment, $exam_name, $exam_date, $subject, $total_marks, $marks_obtained, $percentage);
        $stmt->execute();

        // Redirect back to same student
        header("Location: exam.php?enrollment_no=" . urlencode($selected_enrollment));
        exit();
    } else {
        echo "<script>alert('Please fill all fields correctly.');</script>";
    }
}

// Fetch exam records
$marks_result = null;
if ($selected_enrollment) {
    $stmt = $conn->prepare("SELECT * FROM exam_marks WHERE enrollment_no = ? ORDER BY exam_date DESC");
    $stmt->bind_param("s", $selected_enrollment);
    $stmt->execute();
    $marks_result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Exam Marks Entry</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f7f9fc;
      margin: 0;
      padding: 0;
    }

    .main-content {
      margin-left: 220px;
      padding: 30px;
    }

    .container {
      max-width: 700px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #093c56;
    }

    label {
      font-weight: bold;
      display: block;
      margin: 10px 0 5px;
    }

    select, input[type="text"], input[type="number"], input[type="date"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
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
      color: #444;
      text-align: center;
    }

    .student-info {
      font-weight: bold;
      color: #333;
      margin-bottom: 10px;
      text-align: center;
    }

    @media (max-width: 768px) {
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

<div class="main-content">
  <div class="container">
    <h2>Exam Marks Entry</h2>

    <?php if ($selected_enrollment): ?>
      <div class="student-info">Student: <?= htmlspecialchars($student_name) ?> (<?= htmlspecialchars($selected_enrollment) ?>)</div>
    <?php endif; ?>

    <form method="POST">
      <label for="exam_name">Exam Name</label>
      <input type="text" name="exam_name" required placeholder="e.g. Unit Test, Final Exam">

      <label for="exam_date">Exam Date</label>
      <input type="date" name="exam_date" required>

      <label for="subject">Subject</label>
      <select name="subject" required>
        <option value="">-- Select Subject --</option>
        <?php foreach ($subjects as $sub): ?>
          <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="total_marks">Total Marks</label>
      <input type="number" name="total_marks" required min="1">

      <label for="marks_obtained">Marks Obtained</label>
      <input type="number" name="marks_obtained" required min="0">

      <button type="submit" name="submit_marks">Submit Marks</button>
    </form>

    <?php if ($marks_result && $marks_result->num_rows > 0): ?>
      <h3>Exam Records for <?= htmlspecialchars($student_name) ?></h3>
      <table>
        <tr>
          <th>Exam</th>
          <th>Date</th>
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
            <td><?= $row['total_marks'] ?></td>
            <td><?= $row['marks_obtained'] ?></td>
            <td><?= $row['percentage'] ?>%</td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php else: ?>
      <p style="text-align:center; color: #888;">No exam records found for this student.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

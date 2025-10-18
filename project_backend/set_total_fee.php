<?php
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['form_submit'])) {
    $enrollment_no = $_POST['enrollment_no'];
    $total_fee = $_POST['total_fee'];
    $reason = trim($_POST['reason'] ?? '');

    $check = $conn->prepare("SELECT * FROM student_fee_master WHERE enrollment_no = ?");
    $check->bind_param("s", $enrollment_no);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        if (empty($reason)) {
            $message = "Please provide a reason to update the fee.";
        } else {
            $stmt = $conn->prepare("UPDATE student_fee_master SET total_fee = ?, reason = ? WHERE enrollment_no = ?");
            $stmt->bind_param("dss", $total_fee, $reason, $enrollment_no);
            $stmt->execute();
            $message = "Fee updated for $enrollment_no.";
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO student_fee_master (enrollment_no, total_fee) VALUES (?, ?)");
        $stmt->bind_param("sd", $enrollment_no, $total_fee);
        $stmt->execute();
        $message = "Fee set for $enrollment_no.";
    }
}

$students = $conn->query("
    SELECT s.enrollment_no, s.fname, s.mname, s.lname, f.total_fee, f.reason
    FROM student s
    LEFT JOIN student_fee_master f ON s.enrollment_no = f.enrollment_no
");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Set/Edit Student Fees</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f4f4f4; }
    .navbar {
      width: 100%;
      background-color: #00334e;
      color: white;
      padding: 14px 30px;
      font-size: 18px;
      font-weight: bold;
    }

    .wrapper {
      display: flex;
      height: calc(100vh - 60px);
    }

    .left {
      width: 70%;
      padding: 30px;
      background: #ffffff;
      border-right: 2px solid #ddd;
      overflow-y: auto;
    }

    .right {
      width: 30%;
      padding: 30px;
      background: #ffffff;
      overflow-y: auto;
      position: relative;
    }

    h2 {
      text-align: center;
      color: #00334e;
      margin-bottom: 10px;
    }

    .message {
      margin: 10px auto;
      padding: 10px;
      background-color: #e2e3e5;
      border-left: 4px solid #0d6efd;
      color: #333;
    }

    .search-box {
      text-align: center;
      margin-bottom: 20px;
    }

    .search-box input {
      padding: 8px;
      width: 90%;
      max-width: 300px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      font-size: 14px;
    }

    th {
      background: #0d6efd;
      color: white;
    }

    button.select-btn {
      background: #0077b6;
      color: white;
      padding: 5px 10px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    form {
      max-width: 100%;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }

    input[type="text"], input[type="number"], textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    input[type="submit"] {
      margin-top: 20px;
      padding: 10px 20px;
      background: #00334e;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    input[type="submit"]:hover {
      background: #001d33;
    }

    .form-box {
      display: none;
    }

    .disclaimer {
      padding: 20px;
      text-align: center;
      font-style: italic;
      color: #666;
      border: 2px dashed #ccc;
      border-radius: 10px;
      margin-top: 40px;
      background: #f9f9f9;
    }

    @media (max-width: 768px) {
      .wrapper { flex-direction: column; height: auto; }
      .left, .right { width: 100%; border: none; }
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>


<div class="wrapper">
  <!-- Left Panel -->
  <div class="left">
    <h2>Set or Edit Student Total Fee</h2>
    <div class="search-box">
      <input type="text" id="studentSearch" placeholder="Search student name...">
    </div>
    <table id="studentTable">
      <thead>
        <tr>
          <th>Enrollment</th>
          <th>Name</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $students->fetch_assoc()): ?>
          <tr>
            <td><?= $row['enrollment_no'] ?></td>
            <td><?= trim($row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']) ?></td>
            <td>
              <button class="select-btn"
                data-enroll="<?= $row['enrollment_no'] ?>"
                data-fee="<?= $row['total_fee'] ?>"
                data-reason="<?= htmlspecialchars($row['reason']) ?>">
                Select
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Right Panel -->
  <div class="right">
    <h2>Fee Form</h2>

    <?php if (!empty($message)): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <!-- Default disclaimer -->
    <div class="disclaimer" id="disclaimer">
      👈 Click on the <strong>Select</strong> button to add or edit the student's total fee.
    </div>

    <!-- Form box -->
    <div class="form-box" id="formBox">
      <form method="POST">
        <input type="hidden" name="form_submit" value="1">
        <label>Enrollment No</label>
        <input type="text" name="enrollment_no" id="enrollInput" readonly required>

        <label>Total Fee</label>
        <input type="number" step="0.01" name="total_fee" id="feeInput" required>

        <div id="reasonSection" style="display: none;">
          <label>Reason for Update</label>
          <textarea name="reason" id="reasonInput" rows="3" placeholder="Reason required for fee update"></textarea>
        </div>

        <input type="submit" value="Save Fee">
      </form>
    </div>
  </div>
</div>

<script>
  const buttons = document.querySelectorAll('.select-btn');
  const enrollInput = document.getElementById('enrollInput');
  const feeInput = document.getElementById('feeInput');
  const reasonInput = document.getElementById('reasonInput');
  const reasonSection = document.getElementById('reasonSection');
  const formBox = document.getElementById('formBox');
  const disclaimer = document.getElementById('disclaimer');

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      const fee = btn.dataset.fee;
      enrollInput.value = btn.dataset.enroll;
      feeInput.value = fee || '';
      reasonInput.value = btn.dataset.reason || '';

      // Show/hide reason section
      if (fee && parseFloat(fee) > 0) {
        reasonSection.style.display = 'block';
      } else {
        reasonSection.style.display = 'none';
      }

      // Show form, hide disclaimer
      formBox.style.display = 'block';
      disclaimer.style.display = 'none';
    });
  });

  document.getElementById('studentSearch').addEventListener('input', function () {
    const val = this.value.toLowerCase();
    document.querySelectorAll('#studentTable tbody tr').forEach(row => {
      const name = row.cells[1].innerText.toLowerCase();
      row.style.display = name.includes(val) ? '' : 'none';
    });
  });
</script>

</body>
</html>

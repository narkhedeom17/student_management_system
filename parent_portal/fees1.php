<?php
session_start();

$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$enrollment_no = $_SESSION['enrollment_no'] ?? '';
if (empty($enrollment_no)) {
    echo "Enrollment number not found. Please login.";
    exit;
}

// Fetch fee summary
$total_fee = $paid_fee = $pending_fee = 0;

$totalResult = $conn->query("SELECT total_fee FROM student_fee_master WHERE enrollment_no = '$enrollment_no'");
$total_fee = ($totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total_fee'] : 0;

$paidResult = $conn->query("SELECT SUM(paid_fee) AS paid FROM student_fee_payments WHERE enrollment_no = '$enrollment_no'");
$paid_fee = ($paidResult->num_rows > 0) ? $paidResult->fetch_assoc()['paid'] : 0;

$pending_fee = $total_fee - $paid_fee;

// Fetch payment history
$payments = $conn->query("SELECT * FROM student_fee_payments WHERE enrollment_no = '$enrollment_no' ORDER BY payment_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Student Fee Overview</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
    .container {
      margin-left: 240px;
      padding: 30px;
    }
    .card-container {
      display: flex;
      gap: 20px;
      margin: 20px 0;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      flex: 1;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      text-align: center;
    }
    .card h3 {
      margin-bottom: 10px;
      color: #333;
    }
    .card p {
      font-size: 20px;
      font-weight: bold;
      color: #0d6efd;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-top: 30px;
    }
    th, td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #0d6efd;
      color: white;
    }
    .section-heading {
      margin-top: 40px;
      margin-bottom: 10px;
      color: #222;
      font-size: 18px;
    }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
  <h2>Fee Overview</h2>

  <!-- Fee Summary Cards -->
  <div class="card-container">
    <div class="card">
      <h3>Total Fee</h3>
      <p>₹<?= number_format($total_fee, 2) ?></p>
    </div>
    <div class="card">
      <h3>Paid Fee</h3>
      <p>₹<?= number_format($paid_fee, 2) ?></p>
    </div>
    <div class="card">
      <h3>Pending Fee</h3>
      <p>₹<?= number_format(max(0, $pending_fee), 2) ?></p>
    </div>
  </div>

  <!-- Payment History -->
  <h3 class="section-heading">Payment History</h3>
  <?php if ($payments && $payments->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Sr. No.</th>
          <th>Date</th>
          <th>Amount</th>
          <th>Mode</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php $sr = 1; while ($payment = $payments->fetch_assoc()): ?>
          <tr>
            <td><?= $sr++ ?></td>
            <td><?= htmlspecialchars($payment['payment_date']) ?></td>
            <td>₹<?= number_format($payment['paid_fee'], 2) ?></td>
            <td><?= htmlspecialchars($payment['mode_of_payment']) ?></td>
            <td><?= htmlspecialchars($payment['remarks']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No payment history found.</p>
  <?php endif; ?>
</div>

</body>
</html>

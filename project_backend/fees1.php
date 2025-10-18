<?php
$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get enrollment number
$enrollment_no = $_GET['enrollment_no'] ?? '';
if (empty($enrollment_no)) {
    echo "Enrollment number not provided.";
    exit;
}

// Fetch student name
$student_name = '';
$stmt = $conn->prepare("SELECT fname, mname, lname FROM student WHERE enrollment_no = ?");
$stmt->bind_param("s", $enrollment_no);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $student_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['lname']);
}
$stmt->close();

// Handle payment form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['paid_fee'])) {
    $paid_fee = $_POST['paid_fee'];
    $payment_date = $_POST['payment_date'];
    $mode = $_POST['mode_of_payment'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("INSERT INTO student_fee_payments (enrollment_no, paid_fee, payment_date, mode_of_payment, remarks) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $enrollment_no, $paid_fee, $payment_date, $mode, $remarks);
    $stmt->execute();

    header("Location: fees1.php?enrollment_no=$enrollment_no&success=1");
    exit;
}

// Fetch fee details
$total_fee = $paid_fee = $pending_fee = 0;

$totalResult = $conn->query("SELECT total_fee FROM student_fee_master WHERE enrollment_no = '$enrollment_no'");
$total_fee = ($totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total_fee'] : 0;

$paidResult = $conn->query("SELECT SUM(paid_fee) AS paid FROM student_fee_payments WHERE enrollment_no = '$enrollment_no'");
$paid_fee = ($paidResult->num_rows > 0) ? $paidResult->fetch_assoc()['paid'] : 0;

$pending_fee = $total_fee - $paid_fee;

$payments = $conn->query("SELECT * FROM student_fee_payments WHERE enrollment_no = '$enrollment_no' ORDER BY payment_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Fee Payment</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
        .container { padding: 30px; max-width: 900px; margin: 0 auto; }
        .card-container { display: flex; gap: 20px; margin: 20px 0; }
        .card { background: white; padding: 20px; border-radius: 10px; flex: 1; box-shadow: 0 2px 6px rgba(0,0,0,0.1); text-align: center; }
        .card h3 { margin-bottom: 10px; color: #333; }
        .card p { font-size: 20px; font-weight: bold; color: #0d6efd; }
        form { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        input, select { padding: 10px; margin: 10px 0; width: 100%; border-radius: 6px; border: 1px solid #ccc; }
        input[type="submit"] { background: black; color: white; border: none; cursor: pointer; }
        input[type="submit"]:hover { background: #333; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: rgb(0, 96, 155); color: white; }
        .message { margin-bottom: 20px; padding: 10px; background: #d1e7dd; border-left: 4px solid #0f5132; color: #0f5132; }
        .section-heading { margin-top: 40px; margin-bottom: 10px; color: #222; }
        .student-name { font-size: 22px; font-weight: bold; color: #00334e; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <div class="student-name">Fee Details for: <?= htmlspecialchars($student_name) ?></div>

    <?php if (isset($_GET['success'])): ?>
        <div class="message">Payment recorded successfully.</div>
    <?php endif; ?>

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

    <!-- Payment Form -->
    <?php if ($pending_fee > 0): ?>
        <h3 class="section-heading">Add New Payment</h3>
        <form method="post">
            <input type="number" name="paid_fee" step="0.01" max="<?= $pending_fee ?>" placeholder="Paid Fee Amount" required>

            <label>Select Payment Date:</label>
            <input type="date" name="payment_date" required>

            <select name="mode_of_payment" required>
                <option value="">-- Select Mode of Payment --</option>
                <option>Cash</option>
                <option>Cheque</option>
                <option>UPI</option>
                <option>Online</option>
                <option>Bank Transfer</option>
            </select>

            <input type="text" name="remarks" placeholder="Remarks (optional)">
            <input type="submit" value="Add Payment">
        </form>
    <?php else: ?>
        <p style="color:green; font-weight: bold;">All fees have been paid. No further payments allowed.</p>
    <?php endif; ?>

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

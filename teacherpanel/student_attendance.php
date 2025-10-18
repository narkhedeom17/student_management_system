<?php
session_start();
include 'navbar.php';

$conn = new mysqli("localhost", "root", "12345678", "newton_db");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Determine who is logged in and what student to show
$enrollment_no = '';

if (isset($_SESSION['loggedin']) && isset($_SESSION['enrollment_no'])) {
    // Student login
    $enrollment_no = $_SESSION['enrollment_no'];
} elseif (isset($_SESSION['teacher_loggedin']) && isset($_GET['enrollment_no'])) {
    // Teacher viewing student
    $enrollment_no = $_GET['enrollment_no'];
} else {
    // Not authorized
    header("Location: index.php");
    exit();
}

// Month & Year selection
$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');
$month = intval($month);
$year = intval($year);

$first_day = date('Y-m-01', strtotime("$year-$month-01"));
$last_day = date('Y-m-t', strtotime($first_day));
$days_in_month = date('t', strtotime($first_day));

// ✅ Auto-fill Present (P) by default if teacher is viewing
if (isset($_SESSION['teacher_loggedin'])) {
    $today = date('Y-m-d');
    for ($day = 1; $day <= $days_in_month; $day++) {
        $date = date('Y-m-d', strtotime("$year-$month-$day"));
        $dow = date('w', strtotime($date)); // 0 = Sun, 6 = Sat
        if ($dow == 0 || $dow == 6 || $date > $today) continue; // Skip weekends & future

        // Check if already marked
        $check = $conn->prepare("SELECT 1 FROM student_attendance WHERE enrollment_no = ? AND attendance_date = ?");
        $check->bind_param("ss", $enrollment_no, $date);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            // Insert default Present
            $insert = $conn->prepare("INSERT INTO student_attendance (enrollment_no, attendance_date, status) VALUES (?, ?, 'P')");
            $insert->bind_param("ss", $enrollment_no, $date);
            $insert->execute();
        }
        $check->close();
    }
}

// Handle AJAX save
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['date'], $_POST['status'])) {
    $att_date = $_POST['date'];
    $status = $_POST['status'];
    if (in_array($status, ['P', 'A', 'H'])) {
        $stmt = $conn->prepare("INSERT INTO student_attendance (enrollment_no, attendance_date, status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status)");
        $stmt->bind_param("sss", $enrollment_no, $att_date, $status);
        $stmt->execute();
    }
    exit();
}

// Fetch attendance records
$records = [];
$stmt = $conn->prepare("SELECT attendance_date, status FROM student_attendance WHERE enrollment_no = ? AND attendance_date BETWEEN ? AND ?");
$stmt->bind_param("sss", $enrollment_no, $first_day, $last_day);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $records[$row['attendance_date']] = $row['status'];
}

// Count summary
$totalP = count(array_filter($records, fn($v) => $v == 'P'));
$totalA = count(array_filter($records, fn($v) => $v == 'A'));
$totalH = count(array_filter($records, fn($v) => $v == 'H'));
$totalMarked = $totalP + $totalA;
$attendancePercent = $totalMarked > 0 ? round(($totalP / $totalMarked) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Attendance</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f4f4;
        }

        .main-content {
            margin-left: 220px;
            padding: 30px;
        }

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            background: white;
            border: 1px solid #ccc;
            border-radius: 10px;
            overflow: hidden;
        }

        .calendar div {
            padding: 14px;
            border: 1px solid #eee;
            text-align: center;
            cursor: pointer;
        }

        .header {
            background: #093c56;
            color: white;
            font-weight: bold;
        }

        .holiday { background: #f0f0f0; color: #999; }
        .present { background: #d4edda; }
        .absent { background: #f8d7da; }
        .default { background: #fff; }

        .summary {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        .nav-month {
            text-align: center;
            margin: 20px 0;
        }

        .nav-month a {
            margin: 0 20px;
            font-size: 18px;
            text-decoration: none;
            color: #093c56;
        }

        .status-legend {
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }

        .status-legend span {
            display: inline-block;
            margin-right: 20px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="nav-month">
        <?php
        $prevMonth = $month - 1; $prevYear = $year;
        $nextMonth = $month + 1; $nextYear = $year;
        if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
        if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }
        $base = basename($_SERVER['PHP_SELF']);
        $queryString = isset($_GET['enrollment_no']) ? "&enrollment_no=" . urlencode($_GET['enrollment_no']) : '';
        ?>
        <a href="<?= $base ?>?month=<?= $prevMonth ?>&year=<?= $prevYear . $queryString ?>">&laquo; Prev</a>
        <strong><?= date('F Y', strtotime("$year-$month-01")) ?></strong>
        <a href="<?= $base ?>?month=<?= $nextMonth ?>&year=<?= $nextYear . $queryString ?>">Next &raquo;</a>
    </div>

    <div class="calendar">
        <?php
        $weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        foreach ($weekdays as $wd) echo "<div class='header'>$wd</div>";

        $startDayOfWeek = date('w', strtotime($first_day));
        for ($i = 0; $i < $startDayOfWeek; $i++) echo "<div class='holiday'></div>";

        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = date('Y-m-d', strtotime("$year-$month-$day"));
            $dow = date('w', strtotime($date));
            $status = $records[$date] ?? '';
            $class = $status == 'P' ? 'present' : ($status == 'A' ? 'absent' : ($status == 'H' || $dow == 0 || $dow == 6 ? 'holiday' : 'default'));
            echo "<div class='$class' data-date='$date'>" . $day . "</div>";
        }
        ?>
    </div>

    <div class="summary">
        <h3>Monthly Summary</h3>
        <p>✅ Present: <?= $totalP ?> | ❌ Absent: <?= $totalA ?> | 🏖️ Holiday: <?= $totalH ?></p>
        <p><strong>Attendance %: <?= $attendancePercent ?>%</strong></p>
        <div class="status-legend">
            <span style="background:#d4edda;padding:4px 10px;border-radius:4px;">P = Present</span>
            <span style="background:#f8d7da;padding:4px 10px;border-radius:4px;">A = Absent</span>
            <span style="background:#f0f0f0;padding:4px 10px;border-radius:4px;">H = Holiday</span>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.calendar div[data-date]').forEach(cell => {
    cell.addEventListener('click', () => {
        const date = cell.getAttribute('data-date');
        const status = prompt("Enter status for " + date + " (P = Present, A = Absent, H = Holiday):");
        if (!status) return;
        const upper = status.trim().toUpperCase();
        if (!['P', 'A', 'H'].includes(upper)) return alert("Invalid entry.");
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'date=' + date + '&status=' + upper
        }).then(() => location.reload());
    });
});
</script>

</body>
</html>
